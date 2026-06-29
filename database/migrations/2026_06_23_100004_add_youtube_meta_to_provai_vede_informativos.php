<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('provai_vede', function (Blueprint $table) {
            $table->string('youtube_canal')->nullable()->after('thumbnail_path');
            $table->string('youtube_thumbnail')->nullable()->after('youtube_canal');
        });

        Schema::table('informativos', function (Blueprint $table) {
            $table->string('youtube_canal')->nullable()->after('thumbnail_path');
            $table->string('youtube_thumbnail')->nullable()->after('youtube_canal');
        });
    }

    public function down(): void
    {
        Schema::table('provai_vede', function (Blueprint $table) {
            $table->dropColumn(['youtube_canal', 'youtube_thumbnail']);
        });

        Schema::table('informativos', function (Blueprint $table) {
            $table->dropColumn(['youtube_canal', 'youtube_thumbnail']);
        });
    }
};
