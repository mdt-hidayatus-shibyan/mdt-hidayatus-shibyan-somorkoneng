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
        Schema::table('nilai_ujians', function (Blueprint $table) {
            $table->unique(['ujian_id', 'jadwal_ujian_id', 'murid_id'], 'nilai_ujian_santri_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nilai_ujians', function (Blueprint $table) {
            $table->dropUnique('nilai_ujian_santri_unique');
        });
    }
};
