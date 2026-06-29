<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('provai_vede', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('tema')->nullable();
            $table->enum('categoria', ['saude', 'familia', 'alimentacao', 'espiritualidade', 'educacao', 'relacionamentos', 'financas', 'outro'])->default('saude');
            $table->date('data_exibicao')->nullable();
            $table->unsignedInteger('duracao_segundos')->nullable();
            $table->string('link_youtube')->nullable();
            $table->string('thumbnail_path')->nullable();
            $table->string('trimestre')->nullable();
            $table->enum('status_importacao', ['pendente_aprovacao', 'ativo', 'inativo'])->default('ativo');
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->fullText(['titulo', 'tema']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provai_vede');
    }
};
