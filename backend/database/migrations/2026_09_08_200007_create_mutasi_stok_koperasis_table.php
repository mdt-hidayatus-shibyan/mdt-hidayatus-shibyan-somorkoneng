<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mutasi_stok_koperasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained('produk_koperasis')->cascadeOnDelete();
            $table->enum('jenis_mutasi', [
                'Stok_Awal',
                'Stok_Masuk',
                'Penjualan',
                'Penjualan_Paket',
                'Penyesuaian_Opname',
                'Retur',
                'Pembatalan_Transaksi'
            ])->index();
            $table->integer('jumlah'); // Positif (+) jika bertambah, Negatif (-) jika berkurang
            $table->integer('stok_sebelum');
            $table->integer('stok_sesudah');
            $table->string('referensi', 100)->nullable(); // Nomor nota atau nomor surat jalan
            $table->text('keterangan')->nullable();
            $table->foreignId('petugas_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mutasi_stok_koperasis');
    }
};
