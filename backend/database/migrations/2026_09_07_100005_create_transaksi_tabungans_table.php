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
        Schema::create('transaksi_tabungans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_transaksi', 50)->unique();
            $table->foreignId('tabungan_id')->constrained('tabungans')->cascadeOnDelete();
            $table->enum('jenis_transaksi', ['Setor', 'Tarik', 'Potongan', 'Pembagian_Akhir', 'Koreksi']);

            $table->decimal('nominal_kotor', 14, 2);
            $table->decimal('persentase_potongan', 5, 2)->default(0.00);
            $table->decimal('nominal_potongan', 14, 2)->default(0.00);
            $table->decimal('nominal_bersih', 14, 2);

            $table->decimal('saldo_awal', 14, 2);
            $table->decimal('saldo_akhir', 14, 2);

            $table->date('tanggal');
            $table->foreignId('ruangan_id')->nullable()->constrained('ruangans')->nullOnDelete();
            $table->foreignId('petugas_id')->constrained('users')->cascadeOnDelete();
            $table->enum('metode', ['Tunai', 'Transfer', 'Auto_Debet', 'Sistem'])->default('Tunai');
            $table->string('keterangan', 255)->nullable();
            $table->timestamps();

            $table->index(['tabungan_id', 'tanggal']);
            $table->index(['ruangan_id', 'tanggal']);
            $table->index('jenis_transaksi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_tabungans');
    }
};
