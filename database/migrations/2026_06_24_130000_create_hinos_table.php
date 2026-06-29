<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hinos', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('numero')->index();
            $table->string('titulo');
            $table->string('hinario')->default('Hinário Adventista');
            $table->string('tom')->nullable();                 // tom padrão/sugerido
            $table->string('tons_alternativos')->nullable();   // ex.: "C, D, E"
            $table->string('link_youtube')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();

            $table->unique(['numero', 'hinario']);
        });

        // Log (append-only) das execuções, para sugerir o "tom mais tocado".
        Schema::create('hino_execucoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hino_id')->constrained('hinos')->cascadeOnDelete();
            $table->foreignId('culto_id')->nullable()->constrained('cultos')->nullOnDelete();
            $table->string('tom')->nullable();
            $table->date('executado_em')->nullable();
            $table->timestamps();

            $table->index(['hino_id', 'tom']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hino_execucoes');
        Schema::dropIfExists('hinos');
    }
};
