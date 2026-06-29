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
        Schema::create('culto_liturgias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('culto_id')->constrained('cultos')->cascadeOnDelete();
            $table->integer('ordem');
            $table->enum('tipo', ['musica', 'video', 'anuncio', 'informativo', 'hino', 'item_livre', 'oracao', 'sermao', 'ofertorio']);
            $table->string('titulo');
            $table->time('horario_previsto')->nullable();
            $table->unsignedInteger('duracao_minutos')->nullable();
            $table->text('observacao')->nullable();
            $table->nullableMorphs('referencia');
            $table->boolean('concluido')->default(false);
            $table->timestamp('iniciado_em')->nullable();
            $table->timestamp('concluido_em')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('culto_liturgias');
    }
};
