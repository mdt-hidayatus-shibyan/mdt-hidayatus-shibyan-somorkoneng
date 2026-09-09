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
        Schema::create('tabungan_komplains', function (Blueprint $table) {
            $table->id();
            $table->string('kode_komplain', 50)->unique();
            $table->foreignId('transaksi_tabungan_id')->constrained('transaksi_tabungans')->cascadeOnDelete();
            $table->foreignId('tabungan_id')->constrained('tabungans')->cascadeOnDelete();
            $table->foreignId('murid_id')->constrained('murids')->cascadeOnDelete();
            $table->foreignId('wali_id')->nullable()->constrained('users')->nullOnDelete();

            $table->decimal('nominal_tercatat', 14, 2);
            $table->decimal('nominal_klaim', 14, 2);
            $table->decimal('selisih', 14, 2); // nominal_klaim - nominal_tercatat

            $table->text('alasan');
            $table->string('foto_bukti', 255)->nullable();
            $table->enum('status', ['Menunggu_Verifikasi', 'Disetujui', 'Ditolak', 'Dibatalkan'])->default('Menunggu_Verifikasi');

            $table->foreignId('diverifikasi_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('diverifikasi_pada')->nullable();
            $table->text('catatan_verifikasi')->nullable();

            $table->timestamps();

            $table->index(['tabungan_id', 'status']);
            $table->index(['murid_id', 'status']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tabungan_komplains');
    }
};
