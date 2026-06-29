<?php

namespace App\Jobs;

use App\Models\ProvaiVede;
use App\Models\ProvaiVedePlaylist;
use App\Services\YoutubeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Scraper do Provai e Vede (Especificação Mestra, Módulo 4 — melhoria).
 *
 * Lê os feeds RSS configurados (playlists mensais do canal no YouTube),
 * importa apenas vídeos:
 *   - com data de publicação >= hoje (agendamentos/futuros);
 *   - que NÃO sejam versão em Libras.
 * Cria registros `pendente_aprovacao` (inativos) para o admin revisar.
 * Deduplica por ID do YouTube. Agendado semanalmente no scheduler.
 */
class MonitorarProvaiVedeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @return int Quantidade de novos vídeos importados.
     */
    public function handle(YoutubeService $youtube): int
    {
        $criados = 0;

        foreach ($this->feeds() as $feed) {
            $criados += $this->processarFeed($feed, $youtube);
        }

        Log::info('Scraper Provai e Vede concluído', ['feeds' => count($this->feeds()), 'novos' => $criados]);

        return $criados;
    }

    /**
     * Feeds a processar: primeiro as playlists cadastradas no sistema
     * (Configurações → Playlists Provai e Vede); se não houver nenhuma,
     * cai no fallback do .env (config('cultogestor.provai_vede_url')).
     */
    private function feeds(): array
    {
        $playlists = ProvaiVedePlaylist::feedsAtivos();

        if (! empty($playlists)) {
            return $playlists;
        }

        $raw = (string) config('cultogestor.provai_vede_url');

        return collect(preg_split('/[\n,]+/', $raw))
            ->map(fn ($u) => trim($u))
            ->filter()
            ->values()
            ->all();
    }

    private function processarFeed(string $url, YoutubeService $youtube): int
    {
        try {
            $resposta = Http::timeout(20)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (CultoGestor)'])
                ->get($url);
        } catch (\Throwable $e) {
            Log::warning('Scraper Provai e Vede: falha ao buscar feed', ['url' => $url, 'erro' => $e->getMessage()]);
            return 0;
        }

        if (! $resposta->ok()) {
            Log::warning('Scraper Provai e Vede: resposta não OK', ['url' => $url, 'status' => $resposta->status()]);
            return 0;
        }

        $itens = $this->parsearFeed($resposta->body());
        $hoje  = Carbon::today();
        $criados = 0;

        foreach ($itens as $item) {
            // Filtro 1: apenas data >= hoje (agendamentos/futuros).
            if ($item['data'] && $item['data']->lt($hoje)) {
                continue;
            }

            // Filtro 2: ignora versões em Libras.
            if (str_contains(mb_strtolower($item['titulo']), 'libras')) {
                continue;
            }

            // Deduplica por ID já cadastrado.
            if (ProvaiVede::where('link_youtube', 'like', "%{$item['id']}%")->exists()) {
                continue;
            }

            $link = "https://www.youtube.com/watch?v={$item['id']}";
            $meta = $youtube->buscarMetadados($link) ?? [];

            ProvaiVede::create([
                'titulo'            => $item['titulo'] ?: ($meta['titulo'] ?? "Vídeo {$item['id']}"),
                'link_youtube'      => $link,
                'youtube_canal'     => $meta['canal'] ?? null,
                'youtube_thumbnail' => $meta['thumbnail'] ?? "https://img.youtube.com/vi/{$item['id']}/hqdefault.jpg",
                'duracao_segundos'  => $meta['duracao_segundos'] ?? null,
                'data_exibicao'     => $item['data']?->toDateString(),
                'categoria'         => 'outro',
                'status_importacao' => 'pendente_aprovacao',
                'ativo'             => false,
            ]);

            $criados++;
        }

        return $criados;
    }

    /**
     * Extrai {id, titulo, data} de um feed Atom do YouTube (RSS de canal/playlist).
     * Faz fallback para extração simples de IDs caso não seja um feed válido.
     *
     * @return array<int, array{id:string, titulo:string, data:?Carbon}>
     */
    private function parsearFeed(string $body): array
    {
        $xml = @simplexml_load_string($body);

        if ($xml !== false && isset($xml->entry)) {
            $itens = [];
            foreach ($xml->entry as $entry) {
                $yt = $entry->children('http://www.youtube.com/xml/schemas/2015');
                $id = (string) ($yt->videoId ?? '');
                if (! $id) {
                    continue;
                }

                $titulo    = trim((string) ($entry->title ?? ''));
                $publicado = (string) ($entry->published ?? '');
                $publicado = $publicado ? Carbon::parse($publicado) : null;

                $itens[] = [
                    'id'     => $id,
                    'titulo' => $titulo,
                    // A data relevante é a de EXIBIÇÃO (no título, ex.: "(04/Jul)"),
                    // não a de publicação. Cai na de publicação se não houver no título.
                    'data'   => $this->dataDoItem($titulo, $publicado),
                ];
            }

            return $itens;
        }

        // Fallback: página HTML comum (sem data/título por vídeo).
        preg_match_all(
            '/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([A-Za-z0-9_-]{11})/',
            $body,
            $m
        );

        return collect($m[1] ?? [])
            ->unique()
            ->map(fn ($id) => ['id' => $id, 'titulo' => '', 'data' => null])
            ->values()
            ->all();
    }

    /**
     * Extrai a data de exibição do título (ex.: "… (04/Jul)" ou "(04/Jul/2026)").
     * O ano vem do parêntese; senão de um ano no título (ex.: "Provai e Vede 2026").
     * Sem data no título, usa a data de publicação.
     */
    private function dataDoItem(string $titulo, ?Carbon $publicado): ?Carbon
    {
        if (preg_match('/\((\d{1,2})\s*\/\s*([A-Za-zçÇ]{3,})(?:\s*\/\s*(\d{2,4}))?\)/u', $titulo, $m)) {
            $dia = (int) $m[1];
            $mes = $this->mesParaNumero($m[2]);

            if ($mes) {
                $ano = isset($m[3]) && $m[3] !== ''
                    ? (int) (strlen($m[3]) === 2 ? '20' . $m[3] : $m[3])
                    : $this->anoDoTitulo($titulo);

                try {
                    return Carbon::create($ano, $mes, $dia)->startOfDay();
                } catch (\Throwable) {
                    // data inválida → ignora e usa publicação
                }
            }
        }

        return $publicado;
    }

    private function mesParaNumero(string $abrev): ?int
    {
        $mapa = [
            'jan' => 1, 'fev' => 2, 'mar' => 3, 'abr' => 4, 'mai' => 5, 'jun' => 6,
            'jul' => 7, 'ago' => 8, 'set' => 9, 'out' => 10, 'nov' => 11, 'dez' => 12,
        ];

        return $mapa[mb_strtolower(mb_substr($abrev, 0, 3))] ?? null;
    }

    private function anoDoTitulo(string $titulo): int
    {
        if (preg_match('/\b(20\d{2})\b/', $titulo, $m)) {
            return (int) $m[1];
        }

        return (int) now()->year;
    }
}
