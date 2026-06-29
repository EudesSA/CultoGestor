<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('musicas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cantor_id')->constrained('cantores')->cascadeOnDelete();
            // culto_id opcional: o cantor pré-cadastra músicas no seu repertório;
            // a associação ao culto acontece quando ele é escalado.
            $table->foreignId('culto_id')->nullable()->constrained('cultos')->nullOnDelete();
            $table->string('nome');
            $table->string('artista')->nullable();
            $table->string('tom')->nullable();
            $table->unsignedInteger('duracao_segundos')->nullable();
            $table->string('link_youtube')->nullable();
            $table->enum('status', ['pendente', 'enviado', 'revisado', 'aprovado'])->default('pendente');
            $table->text('observacoes_diretor')->nullable();
            $table->string('token_acesso')->unique()->nullable();
            $table->timestamp('enviado_em')->nullable();
            $table->timestamp('aprovado_em')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('musicas');
    }
};
