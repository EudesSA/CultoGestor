<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('anuncios', function (Blueprint $table) {
            $table->string('link_youtube')->nullable()->after('descricao');
            $table->string('youtube_canal')->nullable()->after('link_youtube');
            $table->string('youtube_thumbnail')->nullable()->after('youtube_canal');
            $table->unsignedInteger('duracao_segundos')->nullable()->after('youtube_thumbnail');
        });
    }

    public function down(): void
    {
        Schema::table('anuncios', function (Blueprint $table) {
            $table->dropColumn(['link_youtube', 'youtube_canal', 'youtube_thumbnail', 'duracao_segundos']);
        });
    }
};
