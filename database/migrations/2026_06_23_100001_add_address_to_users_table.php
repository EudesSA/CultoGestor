<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->date('data_nascimento')->nullable()->after('avatar');
            $table->enum('genero', ['masculino', 'feminino', 'outro'])->nullable()->after('data_nascimento');
            $table->string('endereco')->nullable()->after('genero');
            $table->string('numero', 20)->nullable()->after('endereco');
            $table->string('complemento')->nullable()->after('numero');
            $table->string('bairro')->nullable()->after('complemento');
            $table->string('cidade')->nullable()->after('bairro');
            $table->string('estado', 2)->nullable()->after('cidade');
            $table->string('cep', 9)->nullable()->after('estado');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'data_nascimento', 'genero',
                'endereco', 'numero', 'complemento',
                'bairro', 'cidade', 'estado', 'cep',
            ]);
        });
    }
};
