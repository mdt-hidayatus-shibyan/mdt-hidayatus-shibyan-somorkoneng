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
        Schema::create('laporan_kendalas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('ustadz_id')->nullable()->constrained('ustadzs')->nullOnDelete();
            $table->string('kategori')->default('Laporan Kendala'); // Laporan Kendala, Masalah Teknis, Rekomendasi Fitur, Pertanyaan
            $table->string('judul');
            $table->text('deskripsi');
            $table->string('tipe_perangkat')->nullable();
            $table->string('versi_aplikasi')->nullable();
            $table->enum('status', ['Menunggu', 'Diproses', 'Selesai', 'Ditolak'])->default('Menunggu');
            $table->text('respon_admin')->nullable();
            $table->foreignId('responded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_kendalas');
    }
};
