<?php

namespace Database\Seeders;

use App\Models\CultoTipo;
use App\Models\Funcao;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Roles
        $roles = ['Administrador', 'Diretor de Música', 'Sonoplasta', 'Cantor', 'Pastor'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // Admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@cultogestor.com'],
            [
                'name'     => 'Administrador',
                'password' => bcrypt('password'),
            ]
        );
        $admin->assignRole('Administrador');

        // Tipos de culto
        $tipos = [
            ['nome' => 'Culto Divino',      'cor' => '#3B82F6', 'icone' => 'heroicon-o-building-library', 'ordem' => 1],
            ['nome' => 'Escola Sabatina',   'cor' => '#10B981', 'icone' => 'heroicon-o-book-open',         'ordem' => 2],
            ['nome' => 'Culto Jovem',       'cor' => '#F59E0B', 'icone' => 'heroicon-o-sparkles',          'ordem' => 3],
            ['nome' => 'JA',                'cor' => '#8B5CF6', 'icone' => 'heroicon-o-fire',              'ordem' => 4],
            ['nome' => 'Vigília',           'cor' => '#6366F1', 'icone' => 'heroicon-o-moon',              'ordem' => 5],
            ['nome' => 'Semana de Oração',  'cor' => '#EC4899', 'icone' => 'heroicon-o-hand-raised',       'ordem' => 6],
            ['nome' => 'Evangelismo',       'cor' => '#EF4444', 'icone' => 'heroicon-o-megaphone',         'ordem' => 7],
        ];

        foreach ($tipos as $tipo) {
            CultoTipo::firstOrCreate(['nome' => $tipo['nome']], $tipo);
        }

        // Funções na escala
        $funcoes = [
            ['nome' => 'Sonoplasta',         'icone' => 'heroicon-o-speaker-wave',      'cor' => '#3B82F6', 'ordem' => 1],
            ['nome' => 'Projeção',           'icone' => 'heroicon-o-computer-desktop',  'cor' => '#10B981', 'ordem' => 2],
            ['nome' => 'Transmissão',        'icone' => 'heroicon-o-video-camera',      'cor' => '#8B5CF6', 'ordem' => 3],
            ['nome' => 'Fotógrafo',          'icone' => 'heroicon-o-camera',            'cor' => '#F59E0B', 'ordem' => 4],
            ['nome' => 'Músico',             'icone' => 'heroicon-o-musical-note',      'cor' => '#EC4899', 'ordem' => 5],
            ['nome' => 'Cantor Especial',    'icone' => 'heroicon-o-microphone',        'cor' => '#EF4444', 'ordem' => 6],
            ['nome' => 'Recepção',           'icone' => 'heroicon-o-hand-thumb-up',     'cor' => '#6B7280', 'ordem' => 7],
        ];

        foreach ($funcoes as $funcao) {
            Funcao::firstOrCreate(['nome' => $funcao['nome']], $funcao);
        }

        // Mapeia roles → permissões do Shield.
        // IMPORTANTE: as permissões são criadas por `php artisan shield:generate`.
        // Ordem de setup: migrate → shield:generate --all → db:seed.
        // O seeder é idempotente e só aplica permissões que já existem.
        $this->call(ShieldRolePermissionSeeder::class);
    }
}
