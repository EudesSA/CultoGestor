<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Habilidades: quais funções de culto cada usuário exerce (Sonoplasta,
 * Projeção, Transmissão, etc.). Usado para filtrar o select de escala —
 * ao escolher uma função, só aparecem os membros que a exercem.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('funcao_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('funcao_id')->constrained('funcoes')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'funcao_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('funcao_user');
    }
};
