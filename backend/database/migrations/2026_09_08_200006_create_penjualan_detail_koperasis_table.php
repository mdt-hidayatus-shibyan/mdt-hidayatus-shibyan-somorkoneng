<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penjualan_detail_koperasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penjualan_id')->constrained('penjualan_koperasis')->cascadeOnDelete();
            $table->enum('tipe_item', ['Produk', 'Paket_Bundling'])->default('Produk');
            $table->foreignId('produk_id')->nullable()->constrained('produk_koperasis')->nullOnDelete();
            $table->foreignId('paket_koperasi_id')->nullable()->constrained('paket_koperasis')->nullOnDelete();
            $table->string('kode_item', 60);
            $table->string('nama_item', 150);
            $table->string('satuan', 30)->default('pcs');
            $table->decimal('harga_beli', 12, 2)->default(0.00); // HPP snapshot
            $table->decimal('harga_jual', 12, 2)->default(0.00); // Harga satuan transaksi
            $table->integer('jumlah')->default(1);
            $table->decimal('diskon_item', 12, 2)->default(0.00);
            $table->decimal('subtotal', 12, 2)->default(0.00);
            $table->decimal('keuntungan', 12, 2)->default(0.00); // subtotal - (harga_beli * jumlah)
            $table->json('rincian_paket_json')->nullable(); // Snapshot komponen jika tipe Paket_Bundling
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penjualan_detail_koperasis');
    }
};
