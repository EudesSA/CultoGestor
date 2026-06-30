<?php

/**
 * CultoGestor — Seeder de demonstração (base de apresentação).
 *
 * Popula o sistema com dados realistas para demonstrar o produto: equipe técnica
 * com habilidades, cantores, cultos passados e futuros, escalas (confirmadas e
 * pendentes), músicas em vários status, hinos, Provai e Vede, informativos e
 * anúncios. Idempotente — pode rodar mais de uma vez sem duplicar registros.
 *
 * Uso:  php artisan db:seed --class=DemoSeeder
 *
 * @author  Eudes S. Aguiar — ProezaTech — www.proezatech.com
 * @link     https://www.proezatech.com
 */

namespace Database\Seeders;

use App\Models\Anuncio;
use App\Models\Cantor;
use App\Models\Culto;
use App\Models\CultoTipo;
use App\Models\Escala;
use App\Models\Funcao;
use App\Models\Hino;
use App\Models\Informativo;
use App\Models\Musica;
use App\Models\ProvaiVede;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    /** Senha única para todos os usuários de demonstração. */
    private const SENHA = 'password';

    public function run(): void
    {
        // Garante que roles, tipos de culto e funções existam (DatabaseSeeder).
        if (CultoTipo::count() === 0 || Funcao::count() === 0) {
            $this->call(DatabaseSeeder::class);
        }

        $usuarios  = $this->criarEquipe();
        $cantores  = $this->criarCantores($usuarios);
        $cultos    = $this->criarCultos();
        $this->criarEscalas($cultos, $usuarios);
        $this->criarMusicas($cantores, $cultos);
        $this->criarHinos();
        $this->criarBibliotecas($cultos);
        $this->criarAnuncios();

        $this->command?->info('✓ Base de demonstração criada. Login: admin@cultogestor.com / ' . self::SENHA);
    }

    /**
     * Equipe: pastor, diretor, sonoplastas e cantores — cada um com as
     * habilidades (funções de culto) que exerce, para alimentar as escalas.
     */
    private function criarEquipe(): array
    {
        $membros = [
            // nome, email, role, [habilidades]
            ['Pr. Daniel Moreira', 'pastor@cultogestor.com',   'Pastor',            []],
            ['Marina Alves',       'marina@cultogestor.com',   'Diretor de Música', ['Cantor Especial', 'Músico']],
            ['Carlos Mendes',      'carlos@cultogestor.com',   'Sonoplasta',        ['Sonoplasta']],
            ['Rafael Souza',       'rafael@cultogestor.com',   'Sonoplasta',        ['Projeção', 'Transmissão']],
            ['Beatriz Lima',       'beatriz@cultogestor.com',  'Sonoplasta',        ['Fotógrafo', 'Recepção']],
            ['Ana Beatriz Rocha',  'ana@cultogestor.com',      'Cantor',            ['Cantor Especial']],
            ['João Pedro Santos',  'joao@cultogestor.com',     'Cantor',            ['Cantor Especial', 'Músico']],
            ['Letícia Gomes',      'leticia@cultogestor.com',  'Cantor',            ['Cantor Especial']],
            ['Tiago Ferreira',     'tiago@cultogestor.com',    'Cantor',            ['Cantor Especial', 'Músico']],
        ];

        $funcoes  = Funcao::pluck('id', 'nome');
        $usuarios = [];

        foreach ($membros as [$nome, $email, $role, $habilidades]) {
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name'           => $nome,
                    'password'       => Hash::make(self::SENHA),
                    'phone_whatsapp' => '+55 11 9' . random_int(1000, 9999) . '-' . random_int(1000, 9999),
                ]
            );

            $user->syncRoles([$role]);

            $idsHabilidades = collect($habilidades)
                ->map(fn ($h) => $funcoes[$h] ?? null)
                ->filter()
                ->all();

            if ($idsHabilidades) {
                $user->funcoes()->syncWithoutDetaching($idsHabilidades);
            }

            $usuarios[$email] = $user;
        }

        return $usuarios;
    }

    /** Cria o registro de Cantor (perfil + portal tokenizado) para cada usuário Cantor. */
    private function criarCantores(array $usuarios): array
    {
        $perfis = [
            'ana@cultogestor.com'     => ['voz' => 'soprano',   'obs' => 'Disponível aos sábados pela manhã.'],
            'joao@cultogestor.com'    => ['voz' => 'tenor',     'obs' => 'Toca violão e canta.'],
            'leticia@cultogestor.com' => ['voz' => 'contralto', 'obs' => 'Prefere louvores contemplativos.'],
            'tiago@cultogestor.com'   => ['voz' => 'baritono',  'obs' => 'Repertório de adoração e quartetos.'],
        ];

        $cantores = [];

        foreach ($perfis as $email => $dados) {
            $user = $usuarios[$email];

            $cantores[$email] = Cantor::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'nome'        => $user->name,
                    'email'       => $user->email,
                    'telefone'    => $user->phone_whatsapp,
                    'voz'         => $dados['voz'],
                    'observacoes' => $dados['obs'],
                    'ativo'       => true,
                ]
            );
        }

        return $cantores;
    }

    /**
     * Cultos: 3 passados (realizados), 1 desta semana e 4 futuros (agendados),
     * distribuídos entre os tipos cadastrados.
     */
    private function criarCultos(): array
    {
        $tipos = CultoTipo::pluck('id', 'nome');
        $hoje  = Carbon::today();

        $plano = [
            // tipo, data, hora_inicio, hora_fim, tema, status, local
            ['Escola Sabatina', $hoje->copy()->subDays(24), '09:00', '10:15', 'A fé que transforma',           'realizado', 'Templo Central'],
            ['Culto Divino',    $hoje->copy()->subDays(17), '10:30', '12:00', 'O bom pastor',                  'realizado', 'Templo Central'],
            ['Culto Jovem',     $hoje->copy()->subDays(10), '19:30', '21:00', 'Geração que não se conforma',   'realizado', 'Salão Social'],
            ['Culto Divino',    $hoje->copy()->subDays(3),  '10:30', '12:00', 'Gratidão em todo tempo',        'realizado', 'Templo Central'],
            ['Semana de Oração', $hoje->copy()->addDays(2), '19:30', '20:30', 'Quarta da fé',                  'agendado',  'Templo Central'],
            ['Culto Divino',    $hoje->copy()->addDays(4),  '10:30', '12:00', 'A graça suficiente',            'agendado',  'Templo Central'],
            ['Culto Jovem',     $hoje->copy()->addDays(11), '19:30', '21:00', 'Propósito e identidade',        'agendado',  'Salão Social'],
            ['Vigília',         $hoje->copy()->addDays(18), '22:00', '00:30', 'Vigília de renovo espiritual',  'agendado',  'Templo Central'],
        ];

        $cultos = [];

        foreach ($plano as $i => [$tipoNome, $data, $ini, $fim, $tema, $status, $local]) {
            // O tipo pode não existir se a lista do DatabaseSeeder mudar; cai no primeiro.
            $tipoId = $tipos[$tipoNome] ?? $tipos->first();

            $cultos[] = Culto::firstOrCreate(
                [
                    'culto_tipo_id' => $tipoId,
                    'data'          => $data->toDateString(),
                ],
                [
                    'hora_inicio' => $ini,
                    'hora_fim'    => $fim,
                    'tema'        => $tema,
                    'status'      => $status,
                    'local'       => $local,
                    'observacoes' => null,
                ]
            );
        }

        return $cultos;
    }

    /**
     * Escalas: para cada culto, escala os membros conforme suas habilidades.
     * Cultos passados ficam confirmados; futuros misturam confirmado e pendente.
     */
    private function criarEscalas(array $cultos, array $usuarios): void
    {
        $funcoes = Funcao::pluck('id', 'nome');

        // função => e-mail do membro que a exerce
        $atribuicoes = [
            'Sonoplasta'   => 'carlos@cultogestor.com',
            'Projeção'     => 'rafael@cultogestor.com',
            'Transmissão'  => 'rafael@cultogestor.com',
            'Fotógrafo'    => 'beatriz@cultogestor.com',
            'Recepção'     => 'beatriz@cultogestor.com',
        ];

        foreach ($cultos as $culto) {
            $passado = Carbon::parse($culto->data)->isPast();

            foreach ($atribuicoes as $funcaoNome => $email) {
                $funcaoId = $funcoes[$funcaoNome] ?? null;
                $user     = $usuarios[$email] ?? null;
                if (! $funcaoId || ! $user) {
                    continue;
                }

                $confirmado = $passado || random_int(0, 1) === 1;

                Escala::firstOrCreate(
                    [
                        'culto_id'  => $culto->id,
                        'funcao_id' => $funcaoId,
                        'user_id'   => $user->id,
                    ],
                    [
                        'status'        => $confirmado ? 'confirmado' : 'pendente',
                        'confirmado_em' => $confirmado ? Carbon::parse($culto->data)->subDays(2) : null,
                    ]
                );
            }
        }
    }

    /**
     * Músicas: cada cantor envia algumas, em status variados. Algumas já
     * vinculadas a um culto, outras soltas no repertório.
     */
    private function criarMusicas(array $cantores, array $cultos): void
    {
        $repertorio = [
            // cantor (email), nome, artista, tom, dur(s), videoId, status, cultoIndex|null
            ['ana@cultogestor.com',     'Tua Graça Me Basta',       'Toque no Altar',     'G',  295, 'a1bC2dEf3Gh', 'aprovado',  3],
            ['ana@cultogestor.com',     'Deus de Promessas',        'Davi Sacer',         'D',  312, 'b2cD3eFg4Hi', 'enviado',   5],
            ['joao@cultogestor.com',    'Eu Navegarei',             'Comunidade Católica','E',  268, 'c3dE4fGh5Ij', 'aprovado',  3],
            ['joao@cultogestor.com',    'Lugar Secreto',            'Gabriela Rocha',     'A',  330, 'd4eF5gHi6Jk', 'revisado',  6],
            ['leticia@cultogestor.com', 'Ousado Amor',              'Isaías Saad',        'B',  410, 'e5fG6hIj7Kl', 'pendente',  null],
            ['leticia@cultogestor.com', 'Em Teus Braços',           'Laura Souza',        'C',  287, 'f6gH7iJk8Lm', 'enviado',   6],
            ['tiago@cultogestor.com',   'Teu Santo Nome',           'Gabriela Rocha',     'D',  354, 'g7hI8jKl9Mn', 'aprovado',  3],
            ['tiago@cultogestor.com',   'Casa do Pai',              'Aline Barros',       'G',  300, 'h8iJ9kLm0No', 'pendente',  null],
        ];

        foreach ($repertorio as [$email, $nome, $artista, $tom, $dur, $videoId, $status, $cultoIdx]) {
            $cantor = $cantores[$email] ?? null;
            if (! $cantor) {
                continue;
            }

            $cultoId  = $cultoIdx !== null && isset($cultos[$cultoIdx]) ? $cultos[$cultoIdx]->id : null;
            $enviado  = in_array($status, ['enviado', 'revisado', 'aprovado'], true);
            $aprovado = $status === 'aprovado';

            Musica::firstOrCreate(
                [
                    'cantor_id' => $cantor->id,
                    'nome'      => $nome,
                ],
                [
                    'culto_id'          => $cultoId,
                    'artista'           => $artista,
                    'tom'               => $tom,
                    'duracao_segundos'  => $dur,
                    'link_youtube'      => "https://www.youtube.com/watch?v={$videoId}",
                    'youtube_titulo'    => $nome,
                    'youtube_canal'     => $artista,
                    'youtube_thumbnail' => "https://img.youtube.com/vi/{$videoId}/hqdefault.jpg",
                    'status'            => $status,
                    'enviado_em'        => $enviado ? now()->subDays(random_int(3, 12)) : null,
                    'aprovado_em'       => $aprovado ? now()->subDays(random_int(1, 3)) : null,
                ]
            );
        }
    }

    /** Hinos do Hinário Adventista para o módulo de hinos / liturgia. */
    private function criarHinos(): void
    {
        $hinos = [
            [13,  'Cantai ao Senhor',           'C', 'C, D'],
            [56,  'Grandioso És Tu',            'Bb', 'A, Bb'],
            [120, 'Saudosa Lembrança',          'G', null],
            [233, 'Vencendo Vem Jesus',         'D', 'C, D'],
            [577, 'Castelo Forte',              'C', null],
            [481, 'Mais Perto Quero Estar',     'F', 'E, F'],
        ];

        foreach ($hinos as [$numero, $titulo, $tom, $alt]) {
            Hino::firstOrCreate(
                ['numero' => $numero, 'hinario' => 'Hinário Adventista'],
                [
                    'titulo'            => $titulo,
                    'tom'               => $tom,
                    'tons_alternativos' => $alt,
                ]
            );
        }
    }

    /** Provai e Vede + Informativos (bibliotecas de vídeo). */
    private function criarBibliotecas(array $cultos): void
    {
        $provai = [
            ['Vida saudável: o poder da água',   'saude',     'TZ1aB2cD3eF', 138],
            ['Mordomia cristã na prática',       'financas',  'UZ2bC3dE4fG', 152],
            ['Família e comunhão no lar',        'familia',   'VZ3cD4eF5gH', 176],
        ];

        foreach ($provai as $i => [$titulo, $categoria, $videoId, $dur]) {
            ProvaiVede::firstOrCreate(
                ['titulo' => $titulo],
                [
                    'tema'              => $titulo,
                    'categoria'         => $categoria,
                    'data_exibicao'     => Carbon::today()->addDays($i * 7),
                    'duracao_segundos'  => $dur,
                    'link_youtube'      => "https://www.youtube.com/watch?v={$videoId}",
                    'youtube_canal'     => 'Novo Tempo',
                    'youtube_thumbnail' => "https://img.youtube.com/vi/{$videoId}/hqdefault.jpg",
                    'ativo'             => true,
                ]
            );
        }

        $informativos = [
            ['Informativo Mundial das Missões', 'missoes', 'WZ4dE5fG6hI', 240],
            ['Notícias da Associação',          'outro',   'XZ5eF6gH7iJ', 180],
        ];

        foreach ($informativos as $i => [$titulo, $categoria, $videoId, $dur]) {
            Informativo::firstOrCreate(
                ['titulo' => $titulo],
                [
                    'tema'              => $titulo,
                    'categoria'         => $categoria,
                    'data_exibicao'     => Carbon::today()->addDays($i * 7),
                    'duracao_segundos'  => $dur,
                    'link_youtube'      => "https://www.youtube.com/watch?v={$videoId}",
                    'youtube_canal'     => 'IASD',
                    'youtube_thumbnail' => "https://img.youtube.com/vi/{$videoId}/hqdefault.jpg",
                    'ativo'             => true,
                ]
            );
        }
    }

    /** Anúncios para o slot de avisos do culto. */
    private function criarAnuncios(): void
    {
        $hoje = Carbon::today();

        $anuncios = [
            ['Congresso Regional da Igreja', 'anuncio', 'congresso', false, $hoje->copy()->subDays(2), $hoje->copy()->addDays(12), 1],
            ['Campanha do Agasalho',         'aviso',   'mutirao',   false, $hoje->copy(),             $hoje->copy()->addDays(20), 2],
            ['Horários de Culto',            'aviso',   'geral',     true,  null,                      null,                       3],
        ];

        foreach ($anuncios as [$titulo, $tipo, $categoria, $sempre, $inicio, $fim, $ordem]) {
            Anuncio::firstOrCreate(
                ['titulo' => $titulo],
                [
                    'tipo'              => $tipo,
                    'categoria'         => $categoria,
                    'descricao'         => $titulo,
                    'sempre_disponivel' => $sempre,
                    'data_inicio'       => $inicio?->toDateString(),
                    'data_fim'          => $fim?->toDateString(),
                    'ativo'             => true,
                    'ordem'             => $ordem,
                ]
            );
        }
    }
}
