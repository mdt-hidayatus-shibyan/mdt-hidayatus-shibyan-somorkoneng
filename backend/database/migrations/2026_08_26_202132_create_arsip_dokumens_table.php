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
        Schema::create('arsip_dokumens', function (Blueprint $table) {
            // Menggunakan UUID sebagai Primary Key
            $table->uuid('id')->primary();

            // Jenis dokumen (contoh: 'rapor', 'kwitansi', 'ijazah', 'sk_kelulusan')
            $table->string('tipe_dokumen')->index();

            // Relasi Polymorphic
            $table->string('referensi_tipe');
            $table->unsignedBigInteger('referensi_id');
            $table->index(['referensi_tipe', 'referensi_id']);

            // Lokasi penyimpanan file PDF (Wajib jika menggunakan teknik Pembekuan File)
            $table->string('file_path')->nullable();

            // Snapshot data krusial dalam format JSON (nama pejabat, TTD, nominal, dll)
            $table->json('snapshot_data');

            // Mencatat ID user/admin yang mencetak/mengesahkan dokumen tersebut
            $table->unsignedBigInteger('dicetak_oleh')->nullable();
            $table->foreign('dicetak_oleh')
                ->references('id')->on('users')
                ->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arsip_dokumens');
    }
};
