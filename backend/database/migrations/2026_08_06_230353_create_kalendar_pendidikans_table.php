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
        Schema::create('kategori_kegiatans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kategori');
            $table->string('kode_warna');
            $table->timestamps();
        });

        Schema::create('kalendar_pendidikans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_pelajaran_id')->constrained('tahun_pelajarans')->cascadeOnDelete();

            // Relasi ke tabel kategori yang baru saja kita buat
            $table->foreignId('kategori_kegiatan_id')->constrained('kategori_kegiatans')->cascadeOnDelete();

            $table->string('nama_kegiatan'); // Contoh: Ujian Lisan Kitab
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kalendar_pendidikans');
        Schema::dropIfExists('kategori_kegiatans');
    }
};
