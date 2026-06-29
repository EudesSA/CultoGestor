<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('provai_vede', function (Blueprint $table) {
            $table->foreignId('culto_id')->nullable()->after('id')
                ->constrained('cultos')->nullOnDelete();
        });

        Schema::table('informativos', function (Blueprint $table) {
            $table->foreignId('culto_id')->nullable()->after('id')
                ->constrained('cultos')->nullOnDelete();
        });

        Schema::table('anuncios', function (Blueprint $table) {
            $table->foreignId('culto_id')->nullable()->after('id')
                ->constrained('cultos')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('provai_vede', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Culto::class);
            $table->dropColumn('culto_id');
        });

        Schema::table('informativos', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Culto::class);
            $table->dropColumn('culto_id');
        });

        Schema::table('anuncios', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Culto::class);
            $table->dropColumn('culto_id');
        });
    }
};
