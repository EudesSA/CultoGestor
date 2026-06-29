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
        Schema::create('cultos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('culto_tipo_id')->constrained('culto_tipos')->cascadeOnDelete();
            $table->date('data');
            $table->time('hora_inicio');
            $table->time('hora_fim')->nullable();
            $table->string('tema')->nullable();
            $table->text('observacoes')->nullable();
            $table->string('local')->nullable();
            $table->enum('status', ['agendado', 'realizado', 'cancelado'])->default('agendado');
            $table->string('google_meet_link')->nullable();
            $table->timestamps();

            $table->index(['data', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cultos');
    }
};
