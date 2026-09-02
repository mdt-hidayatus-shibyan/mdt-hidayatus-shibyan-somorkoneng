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
        Schema::create('pelanggaran_murids', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->foreignId('tahun_pelajaran_id')->constrained('tahun_pelajarans')->cascadeOnDelete();
            $table->foreignId('semester_id')
                ->nullable()
                ->constrained('semesters')
                ->cascadeOnDelete();
            $table->foreignId('ruangan_id')->constrained('ruangans')->cascadeOnDelete();
            // Siapa yang melanggar
            $table->foreignId('murid_id')->constrained('murids')->cascadeOnDelete();
            // Pelanggaran apa yang dilakukan
            $table->foreignId('referensi_pelanggaran_id')->constrained('referensi_pelanggarans')->cascadeOnDelete();
            // Catatan spesifik
            $table->string('keterangan')->nullable();

            // Jejak rekam admin/guru
            $table->foreignId('diinput_oleh_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelanggaran_murids');
    }
};
