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
        Schema::create('pengaturan_akademiks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tahun_pelajaran_id')->constrained('tahun_pelajarans')->cascadeOnDelete();

            $table->decimal('bobot_imda', 5, 2)->default(60.00);
            $table->decimal('bobot_akhlaq', 5, 2)->default(40.00);

            $table->decimal('bobot_presensi', 5, 2)->default(24.00);
            $table->decimal('bobot_pelanggaran', 5, 2)->default(16.00);

            $table->decimal('poin_alpha', 5, 2)->default(1.00);
            $table->decimal('poin_izin', 5, 2)->default(0.16);

            $table->decimal('poin_hadir', 5, 2)->default(0.00);
            $table->decimal('poin_dispen', 5, 2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaturan_akademiks');
    }
};
