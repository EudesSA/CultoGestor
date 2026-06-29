<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Mapeia as roles do CultoGestor para as permissões geradas pelo Shield.
 *
 * Referência: Especificação Mestra, seção 7 (Estrutura de Permissões).
 * - Administrador  → super_admin (bypass via gate; não precisa de permissões explícitas)
 * - Diretor        → agenda cultos/cantores, aprova músicas (CRUD operacional)
 * - Sonoplasta     → consulta agenda e baixa arquivos (somente leitura)
 * - Pastor         → visualização geral (somente leitura)
 * - Cantor         → apenas as próprias músicas (leitura geral + gestão de Musica;
 *                    o recorte "só as próprias" é feito por query no MusicaResource)
 *
 * Idempotente: só sincroniza permissões que realmente existem no banco.
 */
class ShieldRolePermissionSeeder extends Seeder
{
    /** Todas as ações de CRUD geradas pelo Shield para um recurso. */
    private const ACOES_CRUD = [
        'ViewAny', 'View', 'Create', 'Update', 'Delete', 'DeleteAny',
        'Restore', 'RestoreAny', 'ForceDelete', 'ForceDeleteAny',
        'Replicate', 'Reorder',
    ];

    private const ACOES_LEITURA = ['ViewAny', 'View'];

    /** Recursos operacionais do domínio (sem Role/Igreja/User admin). */
    private const RECURSOS_DOMINIO = [
        'Culto', 'CultoTipo', 'Escala', 'Funcao', 'Cantor',
        'Musica', 'ProvaiVede', 'Informativo', 'Anuncio', 'Hino', 'Ensaio', 'AvaliacaoCulto',
        'ProvaiVedePlaylist', 'Igreja',
    ];

    /** Permissões de páginas customizadas concedidas aos papéis operacionais. */
    private const PAGINAS = [
        'View:Relatorios',
    ];

    private const WIDGETS = [
        'View:CalendarioMensalWidget',
        'View:ProximoCultoWidget',
        'View:StatsWidget',
        'View:AnunciosAtivosWidget',
        'View:HistoricoRecenteWidget',
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // --- Diretor de Música: CRUD nos recursos de domínio + ver usuários ---
        $this->sync('Diretor de Música', [
            ...$this->perms(self::RECURSOS_DOMINIO, self::ACOES_CRUD),
            ...$this->perms(['User'], self::ACOES_LEITURA),
            ...self::WIDGETS,
            ...self::PAGINAS,
        ]);

        // --- Sonoplasta: leitura geral + gestão de Cultos ---
        // Pode agendar/editar cultos (criação de culto liberada junto com Diretor
        // e Administrador); nos demais recursos permanece somente leitura.
        $this->sync('Sonoplasta', [
            ...$this->perms(self::RECURSOS_DOMINIO, self::ACOES_LEITURA),
            ...$this->perms(['Culto'], self::ACOES_CRUD),
            ...self::WIDGETS,
            ...self::PAGINAS,
        ]);

        // --- Pastor: visualização geral ---
        $this->sync('Pastor', [
            ...$this->perms(self::RECURSOS_DOMINIO, self::ACOES_LEITURA),
            ...self::WIDGETS,
            ...self::PAGINAS,
        ]);

        // --- Cantor: apenas as próprias músicas (+ ver onde está escalado) ---
        // Gerencia totalmente as próprias músicas (criar, editar e excluir);
        // o recorte "só as próprias" é garantido por query no MusicaResource.
        $this->sync('Cantor', [
            'ViewAny:Musica', 'View:Musica', 'Create:Musica', 'Update:Musica',
            'Delete:Musica', 'DeleteAny:Musica',
            'ViewAny:Culto', 'View:Culto',
            'ViewAny:Escala', 'View:Escala',
            'ViewAny:Cantor', 'View:Cantor',
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /** Gera nomes "Acao:Recurso" para cada combinação. */
    private function perms(array $recursos, array $acoes): array
    {
        $nomes = [];
        foreach ($recursos as $recurso) {
            foreach ($acoes as $acao) {
                $nomes[] = "{$acao}:{$recurso}";
            }
        }
        return $nomes;
    }

    /** Sincroniza a role com as permissões desejadas que existem no banco. */
    private function sync(string $roleName, array $desejadas): void
    {
        $role = Role::where('name', $roleName)->where('guard_name', 'web')->first();
        if (! $role) {
            $this->command?->warn("Role '{$roleName}' não encontrada — pulando.");
            return;
        }

        $existentes = Permission::whereIn('name', $desejadas)
            ->where('guard_name', 'web')
            ->pluck('name')
            ->all();

        $role->syncPermissions($existentes);
        $this->command?->info("Role '{$roleName}': " . count($existentes) . ' permissões aplicadas.');
    }
}
