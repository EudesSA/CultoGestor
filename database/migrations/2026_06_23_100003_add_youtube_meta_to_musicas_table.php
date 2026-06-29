<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('musicas', function (Blueprint $table) {
            $table->string('youtube_titulo')->nullable()->after('link_youtube');
            $table->string('youtube_canal')->nullable()->after('youtube_titulo');
            $table->string('youtube_thumbnail')->nullable()->after('youtube_canal');
        });
    }

    public function down(): void
    {
        Schema::table('musicas', function (Blueprint $table) {
            $table->dropColumn(['youtube_titulo', 'youtube_canal', 'youtube_thumbnail']);
        });
    }
};
