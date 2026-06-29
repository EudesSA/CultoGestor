<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cantores', function (Blueprint $table) {
            $table->string('token_portal')->unique()->nullable()->after('ativo');
        });

        // Gera token para cantores já existentes
        DB::table('cantores')->whereNull('token_portal')->orderBy('id')->each(function ($cantor) {
            DB::table('cantores')->where('id', $cantor->id)->update([
                'token_portal' => Str::uuid()->toString(),
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('cantores', function (Blueprint $table) {
            $table->dropColumn('token_portal');
        });
    }
};
