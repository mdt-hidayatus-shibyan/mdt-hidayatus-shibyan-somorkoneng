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
        Schema::create('jadwal_ujians', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ujian_id')->constrained('ujians')->onDelete('cascade');
            $table->foreignId('mata_pelajaran_id')->nullable()->constrained('mata_pelajarans')->onDelete('set null');
            $table->string('nama_mata_pelajaran_custom')->nullable();
            // Detail Waktu Pelaksanaan
            $table->date('tanggal_ujian'); // Contoh: 2023-12-05
            $table->time('waktu_mulai');   // Contoh: 07:30:00
            $table->time('waktu_selesai'); // Contoh: 09:00:00
            $table->foreignId('level_id')->nullable()->constrained('levels')->onDelete('set null');
            // Relasi ke Pengawas Ujian Guru/User)
            $table->foreignId('ustadz_id')->nullable()->constrained('ustadzs')->onDelete('set null');

            $table->timestamps();
        });

        Schema::create('nilai_ujians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ujian_id')->constrained('ujians')->onDelete('cascade');
            $table->foreignId('jadwal_ujian_id')->constrained('jadwal_ujians')->onDelete('cascade');
            $table->foreignId('murid_id')->constrained('murids')->onDelete('cascade');
            $table->foreignId('ruangan_id')->constrained('ruangans')->onDelete('cascade');
            $table->integer('nilai')->nullable();
            $table->foreignId('diinput_oleh')->constrained('users')->onDelete('cascade');
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });

        // 3. TABEL DISPENSASI / IZIN ADMINISTRASI KEUANGAN DARI ORANG TUA
        Schema::create('dispensasi_ujians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ujian_id')->constrained('ujians')->onDelete('cascade');
            $table->foreignId('murid_id')->constrained('murids')->onDelete('cascade');
            $table->string('alasan_izin')->nullable(); // Alasan kurang mampu, yatim luar kriteria, dll
            $table->foreignId('diizinkan_oleh')->constrained('users')->onDelete('cascade'); // ID Admin
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispensasi_ujians');
        Schema::dropIfExists('nilai_ujians');
        Schema::dropIfExists('jadwal_ujians');
    }
};
