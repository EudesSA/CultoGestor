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
        Schema::create('google_calendar_eventos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('culto_id')->constrained('cultos')->cascadeOnDelete();
            $table->string('google_event_id')->unique();
            $table->string('calendar_id');
            $table->string('html_link')->nullable();
            $table->timestamp('sincronizado_em')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('google_calendar_eventos');
    }
};
