<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('funcoes', function (Blueprint $table) {
            $table->string('role_vinculado')->nullable()->after('nome')
                ->comment('Papel Spatie que filtra os usuários disponíveis para esta função');
        });

        // Mapeia funções técnicas → role Sonoplasta; musicais → role Cantor
        DB::table('funcoes')
            ->whereIn('nome', ['Sonoplasta', 'Projeção', 'Transmissão'])
            ->update(['role_vinculado' => 'Sonoplasta']);

        DB::table('funcoes')
            ->whereIn('nome', ['Músico', 'Cantor Especial'])
            ->update(['role_vinculado' => 'Cantor']);
    }

    public function down(): void
    {
        Schema::table('funcoes', function (Blueprint $table) {
            $table->dropColumn('role_vinculado');
        });
    }
};
