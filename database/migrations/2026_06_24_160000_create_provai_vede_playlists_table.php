<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('provai_vede_playlists', function (Blueprint $table) {
            $table->id();
            $table->string('nome');             // ex.: "Provai e Vede - Julho 2026"
            $table->string('playlist_id');      // ex.: PLxxxxxxxx
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->unique('playlist_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provai_vede_playlists');
    }
};
