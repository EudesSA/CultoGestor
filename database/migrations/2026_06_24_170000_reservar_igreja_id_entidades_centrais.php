<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Reserva a coluna `igreja_id` (nullable) nas entidades centrais, conforme
 * o CLAUDE.md: prepara o terreno para o Multi-Igreja (Fase 9) sem construir
 * a UI de tenancy agora. Deixar a coluna pronta evita uma migration dolorosa
 * depois, quando as tabelas já tiverem muitos dados.
 */
return new class extends Migration
{
    private array $tabelas = ['cultos', 'escalas', 'musicas', 'anuncios', 'provai_vede', 'informativos'];

    public function up(): void
    {
        foreach ($this->tabelas as $tabela) {
            if (! Schema::hasColumn($tabela, 'igreja_id')) {
                Schema::table($tabela, function (Blueprint $table) {
                    $table->foreignId('igreja_id')->nullable()->after('id')
                        ->constrained('igrejas')->nullOnDelete();
                });
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tabelas as $tabela) {
            if (Schema::hasColumn($tabela, 'igreja_id')) {
                Schema::table($tabela, function (Blueprint $table) {
                    $table->dropConstrainedForeignId('igreja_id');
                });
            }
        }
    }
};
