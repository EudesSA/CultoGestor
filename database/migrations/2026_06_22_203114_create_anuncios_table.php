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
        Schema::create('anuncios', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->enum('tipo', ['anuncio', 'aviso'])->default('anuncio');
            $table->enum('categoria', ['campal', 'congresso', 'ja', 'desbravadores', 'aventureiros', 'mutirao', 'santa_ceia', 'batismo', 'geral'])->default('geral');
            $table->text('descricao')->nullable();
            $table->date('data_inicio')->nullable();
            $table->date('data_fim')->nullable();
            $table->boolean('ativo')->default(true);
            $table->boolean('sempre_disponivel')->default(false);
            $table->integer('ordem')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anuncios');
    }
};
