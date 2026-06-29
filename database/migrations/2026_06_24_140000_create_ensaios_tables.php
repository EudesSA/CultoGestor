<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ensaios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('culto_id')->nullable()->constrained('cultos')->nullOnDelete();
            $table->dateTime('data_hora');
            $table->string('local')->nullable();
            $table->text('observacoes')->nullable();
            $table->enum('status', ['agendado', 'realizado', 'cancelado'])->default('agendado');
            $table->timestamps();

            $table->index(['data_hora', 'status']);
        });

        Schema::create('ensaio_participantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ensaio_id')->constrained('ensaios')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['pendente', 'confirmado', 'recusado'])->default('pendente');
            $table->timestamp('confirmado_em')->nullable();
            $table->string('token_confirmacao')->unique()->nullable();
            $table->string('observacao')->nullable();
            $table->timestamps();

            $table->unique(['ensaio_id', 'user_id']);
        });

        Schema::create('ensaio_musicas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ensaio_id')->constrained('ensaios')->cascadeOnDelete();
            $table->foreignId('musica_id')->nullable()->constrained('musicas')->nullOnDelete();
            $table->string('nome');
            $table->string('tom')->nullable();
            $table->unsignedInteger('ordem')->default(0);
            $table->string('observacao')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ensaio_musicas');
        Schema::dropIfExists('ensaio_participantes');
        Schema::dropIfExists('ensaios');
    }
};
