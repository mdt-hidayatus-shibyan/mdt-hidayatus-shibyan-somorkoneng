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
        Schema::table('pengaturan_potongan_tabungans', function (Blueprint $table) {
            $table->dropUnique('pengaturan_potongan_tabungans_jenis_nasabah_unique');
            $table->foreignId('periode_tabungan_id')->nullable()->after('id')->constrained('periode_tabungans')->cascadeOnDelete();
            $table->unique(['periode_tabungan_id', 'jenis_nasabah'], 'potongan_periode_nasabah_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengaturan_potongan_tabungans', function (Blueprint $table) {
            $table->dropUnique('potongan_periode_nasabah_unique');
            $table->dropForeign(['periode_tabungan_id']);
            $table->dropColumn('periode_tabungan_id');
            $table->unique('jenis_nasabah');
        });
    }
};
