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
        Schema::create('musica_arquivos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('musica_id')->constrained('musicas')->cascadeOnDelete();
            $table->enum('tipo', ['mp3', 'playback', 'letra', 'cifra', 'partitura', 'outro']);
            $table->string('nome_original');
            $table->string('path_local')->nullable();
            $table->string('path_drive')->nullable();
            $table->string('drive_file_id')->nullable();
            $table->unsignedBigInteger('tamanho_bytes')->nullable();
            $table->string('mime_type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('musica_arquivos');
    }
};
