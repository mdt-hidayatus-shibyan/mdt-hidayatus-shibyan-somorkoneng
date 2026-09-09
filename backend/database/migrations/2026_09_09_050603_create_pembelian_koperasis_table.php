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
        Schema::create('pembelian_koperasis', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_faktur', 50)->unique();
            $table->string('nomor_faktur_supplier', 100)->nullable();
            $table->string('supplier', 150);
            $table->datetime('tanggal');
            $table->foreignId('petugas_id')->constrained('users')->cascadeOnDelete();
            $table->integer('total_item')->default(0);
            $table->decimal('total_nominal', 14, 2)->default(0.00);
            $table->string('metode_pembayaran', 50)->default('Tunai_Kas'); // Tunai_Kas, Transfer_Bank, Hutang_Supplier
            $table->decimal('nominal_bayar', 14, 2)->default(0.00);
            $table->decimal('kembalian', 14, 2)->default(0.00);
            $table->decimal('sisa_hutang', 14, 2)->default(0.00);
            $table->enum('status_pembayaran', ['Lunas', 'Belum_Lunas'])->default('Lunas');
            $table->datetime('tanggal_pelunasan')->nullable();
            $table->string('metode_pelunasan', 50)->nullable();
            $table->foreignId('petugas_pelunasan_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['Selesai', 'Dibatalkan'])->default('Selesai');
            $table->string('foto_faktur')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index('tanggal');
            $table->index('status_pembayaran');
            $table->index('status');
        });

        Schema::create('pembelian_detail_koperasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembelian_id')->constrained('pembelian_koperasis')->cascadeOnDelete();
            $table->foreignId('produk_id')->constrained('produk_koperasis')->cascadeOnDelete();
            $table->string('kode_produk', 50);
            $table->string('nama_produk', 200);
            $table->string('satuan', 30)->default('pcs');
            $table->integer('jumlah')->default(1);
            $table->decimal('harga_beli_satuan', 12, 2)->default(0.00);
            $table->decimal('harga_jual_satuan', 12, 2)->default(0.00);
            $table->decimal('subtotal', 14, 2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembelian_detail_koperasis');
        Schema::dropIfExists('pembelian_koperasis');
    }
};
