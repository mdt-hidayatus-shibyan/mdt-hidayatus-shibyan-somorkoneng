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
        Schema::create('pengumumans', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('konten');

            // Kategori Pengumuman
            $table->enum('tipe', ['Informasi', 'Penting', 'Kegiatan', 'Libur'])->default('Informasi');

            // Siapa yang bisa melihat pengumuman ini?
            $table->enum('target_audience', ['Semua', 'Wali Murid', 'Ustadz'])->default('Semua');

            // Status publikasi
            $table->enum('status', ['Draft', 'Terbit', 'Arsip'])->default('Terbit');

            // Masa berlaku pengumuman (Opsional)
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();

            // User/Admin yang membuat pengumuman
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengumumans');
    }
};
