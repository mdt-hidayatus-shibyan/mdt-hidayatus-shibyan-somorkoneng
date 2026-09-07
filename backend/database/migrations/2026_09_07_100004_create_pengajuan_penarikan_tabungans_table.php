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
        Schema::create('pengajuan_penarikan_tabungans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pengajuan', 50)->unique();
            $table->foreignId('tabungan_id')->constrained('tabungans')->cascadeOnDelete();
            $table->decimal('nominal_pengajuan', 14, 2);
            $table->string('alasan_penarikan', 255)->nullable();
            $table->date('tanggal_pengajuan');

            $table->enum('status', ['Menunggu', 'Disetujui', 'Ditolak', 'Dicairkan', 'Dibatalkan'])->default('Menunggu');
            $table->foreignId('diajukan_oleh')->constrained('users')->cascadeOnDelete();
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('tanggal_disetujui')->nullable();
            $table->string('catatan_admin', 255)->nullable();

            // Hasil Kalkulasi Potongan
            $table->decimal('persentase_potongan', 5, 2)->default(0.00);
            $table->decimal('nominal_potongan', 14, 2)->default(0.00);
            $table->decimal('nominal_bersih', 14, 2)->default(0.00);

            $table->unsignedBigInteger('transaksi_tabungan_id')->nullable();
            $table->timestamps();

            $table->index('tabungan_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_penarikan_tabungans');
    }
};
