<?php

namespace App\Jobs;

use App\Models\Anuncio;
use App\Models\Culto;
use App\Models\Informativo;
use App\Models\LouvorjaExportacao;
use App\Models\Musica;
use App\Models\ProvaiVede;
use DOMDocument;
use DOMElement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GerarLiturgiaJaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly int $cultoId,
        public readonly int $geradoPor,
    ) {}

    public function handle(): void
    {
        static::gerar($this->cultoId, $this->geradoPor);
    }

    public static function gerar(int $cultoId, int $geradoPor): LouvorjaExportacao
    {
        $culto = Culto::with([
            'tipo',
            'escalas.funcao',
            'escalas.user',
            'liturgias' => fn ($q) => $q->orderBy('ordem'),
            'liturgias.referencia',
        ])->findOrFail($cultoId);

        // Carrega relacionamentos específicos por tipo (polimórfico não suporta eager aninhado direto)
        $culto->liturgias->each(function ($liturgia) {
            if ($liturgia->referencia instanceof Musica) {
                $liturgia->referencia->load('arquivos', 'cantor');
            } elseif ($liturgia->referencia instanceof Anuncio) {
                $liturgia->referencia->load('midias');
            }
        });

        $basePath = rtrim(config('cultogestor.louvorja_base_path', 'C:\CultoGestor'), '\\/');

        $xml     = static::buildXml($culto, $basePath);
        $slug    = Str::slug(($culto->tipo?->nome ?? 'culto') . '-' . $culto->data?->format('d-m-Y'));
        $arquivo = "louvorja/{$slug}.ja";

        Storage::disk('local')->put($arquivo, $xml);

        return LouvorjaExportacao::create([
            'culto_id'     => $cultoId,
            'gerado_por'   => $geradoPor,
            'path_arquivo' => $arquivo,
            'total_itens'  => $culto->liturgias->count(),
            'gerado_em'    => now(),
        ]);
    }

    // -------------------------------------------------------------------------

    private static function buildXml(Culto $culto, string $basePath): string
    {
        $dom               = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        $root = $dom->createElement('Programacao');
        $dom->appendChild($root);

        $cultoEl = $dom->createElement('Culto');
        $root->appendChild($cultoEl);

        // Cabeçalho do culto
        static::el($dom, $cultoEl, 'Nome', $culto->tipo?->nome ?? 'Culto');
        static::el($dom, $cultoEl, 'Data', $culto->data?->format('d/m/Y') ?? '');
        static::el($dom, $cultoEl, 'HoraInicio', $culto->hora_inicio ? substr($culto->hora_inicio, 0, 5) : '');
        static::el($dom, $cultoEl, 'HoraFim', $culto->hora_fim ? substr($culto->hora_fim, 0, 5) : '');
        static::el($dom, $cultoEl, 'Tema', $culto->tema ?? '');
        static::el($dom, $cultoEl, 'Local', $culto->local ?? '');
        static::el($dom, $cultoEl, 'Observacoes', $culto->observacoes ?? '');

        // Equipe escalada
        if ($culto->escalas->isNotEmpty()) {
            $equipeEl = $dom->createElement('Equipe');
            $cultoEl->appendChild($equipeEl);

            foreach ($culto->escalas as $escala) {
                $membroEl = $dom->createElement('Membro');
                $membroEl->setAttribute('funcao', $escala->funcao?->nome ?? '');
                $membroEl->setAttribute('status', $escala->status ?? 'pendente');
                $membroEl->appendChild($dom->createTextNode($escala->user?->name ?? ''));
                $equipeEl->appendChild($membroEl);
            }
        }

        // Liturgia
        $liturgiaEl = $dom->createElement('Liturgia');
        $cultoEl->appendChild($liturgiaEl);

        foreach ($culto->liturgias as $item) {
            $itemEl = $dom->createElement('Item');
            $itemEl->setAttribute('ordem', (string) $item->ordem);
            $liturgiaEl->appendChild($itemEl);

            static::el($dom, $itemEl, 'Tipo', $item->tipo);
            static::el($dom, $itemEl, 'Titulo', $item->titulo ?? '');
            static::el($dom, $itemEl, 'HorarioPrevisto', $item->horario_previsto ? substr($item->horario_previsto, 0, 5) : '');
            static::el($dom, $itemEl, 'Duracao', (string) ($item->duracao_minutos ?? ''));
            static::el($dom, $itemEl, 'Observacao', $item->observacao ?? '');

            // Detalhes por tipo de referência
            $ref = $item->referencia;

            if ($ref instanceof Musica) {
                static::appendMusicaNodes($dom, $itemEl, $ref, $basePath);
            } elseif ($ref instanceof ProvaiVede) {
                static::appendVideoNodes($dom, $itemEl, $ref);
            } elseif ($ref instanceof Informativo) {
                static::appendInformativoNodes($dom, $itemEl, $ref);
            } elseif ($ref instanceof Anuncio) {
                static::appendAnuncioNodes($dom, $itemEl, $ref, $basePath);
            }
        }

        return $dom->saveXML();
    }

    private static function appendMusicaNodes(DOMDocument $dom, DOMElement $itemEl, Musica $musica, string $basePath): void
    {
        static::el($dom, $itemEl, 'Artista', $musica->artista ?? '');
        static::el($dom, $itemEl, 'Tom', $musica->tom ?? '');
        static::el($dom, $itemEl, 'Cantor', $musica->cantor?->nome ?? '');
        static::el($dom, $itemEl, 'LinkYoutube', $musica->link_youtube ?? '');

        $arquivos = $musica->arquivos->filter(fn ($a) => $a->path_local);
        if ($arquivos->isEmpty()) {
            return;
        }

        $arquivosEl = $dom->createElement('Arquivos');
        $itemEl->appendChild($arquivosEl);

        foreach ($arquivos as $arquivo) {
            $arqEl = $dom->createElement('Arquivo');
            $arquivosEl->appendChild($arqEl);

            static::el($dom, $arqEl, 'Tipo', $arquivo->tipo);
            static::el($dom, $arqEl, 'NomeOriginal', $arquivo->nome_original ?? '');
            static::el($dom, $arqEl, 'Caminho', $basePath . '\\' . str_replace('/', '\\', $arquivo->path_local));
        }
    }

    private static function appendVideoNodes(DOMDocument $dom, DOMElement $itemEl, ProvaiVede $pv): void
    {
        static::el($dom, $itemEl, 'Tema', $pv->tema ?? '');
        static::el($dom, $itemEl, 'Categoria', $pv->categoria ?? '');
        static::el($dom, $itemEl, 'Canal', $pv->youtube_canal ?? '');
        static::el($dom, $itemEl, 'LinkYoutube', $pv->link_youtube ?? '');
        static::el($dom, $itemEl, 'DuracaoSegundos', (string) ($pv->duracao_segundos ?? ''));
        static::el($dom, $itemEl, 'DuracaoFormatada', $pv->duracao_formatada ?? '');
        static::el($dom, $itemEl, 'Trimestre', $pv->trimestre ?? '');
    }

    private static function appendInformativoNodes(DOMDocument $dom, DOMElement $itemEl, Informativo $info): void
    {
        static::el($dom, $itemEl, 'Tema', $info->tema ?? '');
        static::el($dom, $itemEl, 'Categoria', $info->categoria ?? '');
        static::el($dom, $itemEl, 'Canal', $info->youtube_canal ?? '');
        static::el($dom, $itemEl, 'LinkYoutube', $info->link_youtube ?? '');
        static::el($dom, $itemEl, 'DuracaoSegundos', (string) ($info->duracao_segundos ?? ''));
    }

    private static function appendAnuncioNodes(DOMDocument $dom, DOMElement $itemEl, Anuncio $anuncio, string $basePath): void
    {
        static::el($dom, $itemEl, 'Categoria', $anuncio->categoria ?? '');
        static::el($dom, $itemEl, 'Descricao', $anuncio->descricao ?? '');
        static::el($dom, $itemEl, 'DataInicio', $anuncio->data_inicio?->format('d/m/Y') ?? '');
        static::el($dom, $itemEl, 'DataFim', $anuncio->data_fim?->format('d/m/Y') ?? '');

        $midias = $anuncio->midias->filter(fn ($m) => $m->path_local);
        if ($midias->isEmpty()) {
            return;
        }

        $arquivosEl = $dom->createElement('Arquivos');
        $itemEl->appendChild($arquivosEl);

        foreach ($midias as $midia) {
            $arqEl = $dom->createElement('Arquivo');
            $arquivosEl->appendChild($arqEl);

            static::el($dom, $arqEl, 'Tipo', $midia->tipo);
            static::el($dom, $arqEl, 'NomeOriginal', $midia->nome_original ?? '');
            static::el($dom, $arqEl, 'Caminho', $basePath . '\\' . str_replace('/', '\\', $midia->path_local));
        }
    }

    private static function el(DOMDocument $dom, DOMElement $parent, string $tag, string $value): void
    {
        $el = $dom->createElement($tag);
        $el->appendChild($dom->createTextNode($value));
        $parent->appendChild($el);
    }
}
