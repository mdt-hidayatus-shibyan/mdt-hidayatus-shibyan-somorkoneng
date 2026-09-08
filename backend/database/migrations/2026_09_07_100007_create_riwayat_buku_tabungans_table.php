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
        Schema::create('riwayat_buku_tabungans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tabungan_id')->constrained('tabungans')->cascadeOnDelete();
            $table->string('nomor_rekening_lama', 35);
            $table->string('nomor_rekening_baru', 35);
            $table->string('alasan', 50)->default('Buku Hilang');
            $table->decimal('saldo_terakhir', 14, 2)->default(0.00);
            $table->text('catatan')->nullable();
            $table->foreignId('petugas_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('tabungan_id');
            $table->index('nomor_rekening_lama');
            $table->index('nomor_rekening_baru');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_buku_tabungans');
    }
};
