<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penjualan_koperasis', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_nota', 50)->unique()->index(); // e.g. KOP-20260908-0001
            $table->dateTime('tanggal')->index();
            $table->foreignId('petugas_id')->constrained('users')->cascadeOnDelete();
            $table->enum('jenis_pelanggan', ['Murid', 'Ustadz', 'Umum'])->default('Umum')->index();
            $table->foreignId('murid_id')->nullable()->constrained('murids')->nullOnDelete();
            $table->foreignId('ustadz_id')->nullable()->constrained('ustadzs')->nullOnDelete();
            $table->string('nama_pelanggan_umum', 150)->nullable();
            $table->integer('total_item')->default(0);
            $table->decimal('total_hpp', 12, 2)->default(0.00); // Total HPP
            $table->decimal('subtotal', 12, 2)->default(0.00);
            $table->decimal('diskon', 12, 2)->default(0.00);
            $table->decimal('total_akhir', 12, 2)->default(0.00);
            $table->enum('metode_pembayaran', ['Tunai', 'QRIS', 'Transfer', 'Potong_Tabungan'])->default('Tunai');
            $table->decimal('nominal_bayar', 12, 2)->default(0.00);
            $table->decimal('kembalian', 12, 2)->default(0.00);
            $table->foreignId('tabungan_id')->nullable()->constrained('tabungans')->nullOnDelete();
            $table->enum('status', ['Selesai', 'Dibatalkan'])->default('Selesai');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penjualan_koperasis');
    }
};
