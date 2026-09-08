<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produk_koperasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')->constrained('kategori_produks')->cascadeOnDelete();
            $table->string('kode_produk', 60)->unique()->index(); // Barcode / SKU
            $table->string('nama_produk', 150);
            $table->string('satuan', 30)->default('pcs'); // pcs, buku, pack, stel, botol, dll.
            $table->decimal('harga_beli', 12, 2)->default(0.00); // HPP (Harga Pokok)
            $table->decimal('harga_jual', 12, 2)->default(0.00);
            $table->integer('stok')->default(0);
            $table->integer('stok_minimum')->default(5); // Alert restock
            $table->string('foto')->nullable();
            $table->enum('status', ['Aktif', 'Nonaktif'])->default('Aktif');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk_koperasis');
    }
};
