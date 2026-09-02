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
        Schema::create('riwayat_kenaikans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_pelajaran_id')->constrained('tahun_pelajarans')->onDelete('cascade');
            $table->foreignId('ruangan_asal_id')->constrained('ruangans')->onDelete('cascade');
            $table->foreignId('murid_id')->constrained('murids')->onDelete('cascade');
            $table->decimal('nilai_akumulasi', 5, 2)->nullable();
            $table->enum('status_keputusan', ['Naik Kelas', 'Tinggal Kelas', 'Lulus']);
            $table->text('catatan_wali_kelas')->nullable();
            $table->foreignId('diputuskan_oleh')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['tahun_pelajaran_id', 'murid_id'], 'unik_kenaikan_pertahun');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_kenaikans');
    }
};
