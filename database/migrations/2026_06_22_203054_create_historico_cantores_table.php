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
        Schema::create('historico_cantores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cantor_id')->constrained('cantores')->cascadeOnDelete();
            $table->foreignId('culto_id')->constrained('cultos')->cascadeOnDelete();
            $table->foreignId('musica_id')->nullable()->constrained('musicas')->nullOnDelete();
            $table->string('nome_musica');
            $table->string('artista')->nullable();
            $table->string('tom')->nullable();
            $table->date('data_culto');
            $table->string('tipo_culto');
            $table->text('observacao')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historico_cantores');
    }
};
