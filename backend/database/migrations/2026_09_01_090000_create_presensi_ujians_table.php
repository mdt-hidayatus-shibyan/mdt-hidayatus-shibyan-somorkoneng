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
        // 1. Tabel Presensi Santri dalam Ujian
        Schema::create('presensi_ujians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ujian_id')->constrained('ujians')->cascadeOnDelete();
            $table->foreignId('jadwal_ujian_id')->constrained('jadwal_ujians')->cascadeOnDelete();
            $table->foreignId('ruangan_id')->constrained('ruangans')->cascadeOnDelete();
            $table->foreignId('murid_id')->constrained('murids')->cascadeOnDelete();
            $table->enum('status', ['Hadir', 'Sakit', 'Izin', 'Alpha', 'Dispensasi'])->default('Hadir');
            $table->string('catatan', 255)->nullable();
            $table->foreignId('diinput_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Cegah duplikasi presensi santri pada jadwal dan ruangan yang sama
            $table->unique(['jadwal_ujian_id', 'ruangan_id', 'murid_id'], 'presensi_ujian_santri_unique');
        });

        // 2. Tabel Presensi Pengawas Ujian & Berita Acara per Sesi
        Schema::create('presensi_pengawas_ujians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_ujian_id')->constrained('jadwal_ujians')->cascadeOnDelete();
            $table->foreignId('ruangan_id')->constrained('ruangans')->cascadeOnDelete();
            $table->foreignId('ustadz_id')->nullable()->constrained('ustadzs')->nullOnDelete(); // Pengawas terjadwal
            $table->foreignId('ustadz_pengganti_id')->nullable()->constrained('ustadzs')->nullOnDelete(); // Badal / Pengawas pengganti
            $table->enum('status', ['Hadir', 'Izin', 'Sakit', 'Alpha', 'Digantikan'])->default('Hadir');
            $table->text('catatan_berita_acara')->nullable(); // Catatan kejadian selama ujian berlangsung
            $table->foreignId('diinput_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['jadwal_ujian_id', 'ruangan_id'], 'presensi_pengawas_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensi_pengawas_ujians');
        Schema::dropIfExists('presensi_ujians');
    }
};
