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
        Schema::create('escalas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('culto_id')->constrained()->cascadeOnDelete();
            $table->foreignId('funcao_id')->constrained('funcoes')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('token_confirmacao')->unique()->nullable();
            $table->enum('status', ['pendente', 'confirmado', 'recusado'])->default('pendente');
            $table->timestamp('confirmado_em')->nullable();
            $table->text('observacao')->nullable();
            $table->timestamps();

            $table->unique(['culto_id', 'funcao_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('escalas');
    }
};
