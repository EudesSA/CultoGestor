<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('avaliacoes_culto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('culto_id')->constrained('cultos')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('nota_geral');                 // 1-5
            $table->unsignedTinyInteger('nota_som')->nullable();
            $table->unsignedTinyInteger('nota_projecao')->nullable();
            $table->unsignedTinyInteger('nota_transmissao')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();

            $table->unique(['culto_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('avaliacoes_culto');
    }
};
