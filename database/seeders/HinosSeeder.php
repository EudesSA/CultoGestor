<?php

/**
 * CultoGestor — Seeder do Banco de Hinos (Novo Hinário Adventista).
 *
 * Lê database/data/hinos.json (numero, titulo, video_id — vídeos oficiais
 * da Casa Publicadora Brasileira) e faz upsert por numero+hinario.
 * Idempotente: pode rodar quantas vezes for preciso sem duplicar.
 *
 * @author Eudes S. Aguiar — ProezaTech — www.proezatech.com
 */

namespace Database\Seeders;

use App\Models\Hino;
use Illuminate\Database\Seeder;

class HinosSeeder extends Seeder
{
    public const HINARIO = 'Hinário Adventista';

    public function run(): void
    {
        $arquivo = database_path('data/hinos.json');

        if (! file_exists($arquivo)) {
            $this->command?->warn("HinosSeeder: {$arquivo} não encontrado — nada a fazer.");

            return;
        }

        $hinos = json_decode(file_get_contents($arquivo), true) ?: [];

        foreach ($hinos as $hino) {
            $dados = [
                'titulo'       => $hino['titulo'],
                'link_youtube' => "https://www.youtube.com/watch?v={$hino['video_id']}",
            ];

            // Só sobrescreve observações quando o JSON traz alguma (preserva anotações manuais).
            if (! empty($hino['observacoes'])) {
                $dados['observacoes'] = $hino['observacoes'];
            }

            Hino::updateOrCreate(
                ['numero' => $hino['numero'], 'hinario' => self::HINARIO],
                $dados,
            );
        }

        $this->command?->info('HinosSeeder: '.count($hinos).' hinos cadastrados/atualizados.');
    }
}
