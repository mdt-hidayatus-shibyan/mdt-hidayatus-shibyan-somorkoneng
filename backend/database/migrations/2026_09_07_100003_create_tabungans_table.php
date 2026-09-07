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
        Schema::create('tabungans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_rekening', 35)->unique();
            $table->string('nama_rekening', 100)->default('Tabungan Utama');
            $table->enum('jenis_nasabah', ['Murid', 'Ustadz', 'Kas Ruangan', 'Umum']);

            // Foreign Keys Nasabah
            $table->foreignId('murid_id')->nullable()->constrained('murids')->cascadeOnDelete();
            $table->foreignId('ustadz_id')->nullable()->constrained('ustadzs')->cascadeOnDelete();
            $table->foreignId('ruangan_id')->nullable()->constrained('ruangans')->cascadeOnDelete();

            // Khusus Nasabah Umum
            $table->string('nama_nasabah_umum', 150)->nullable();
            $table->string('kontak_umum', 30)->nullable();
            $table->text('alamat_umum')->nullable();

            // Relasi Periode Berjangka (khusus tabungan murid / program berjangka)
            $table->foreignId('periode_tabungan_id')->nullable()->constrained('periode_tabungans')->nullOnDelete();

            // Cache Saldo & Akumulasi Keuangan O(1)
            $table->decimal('saldo', 14, 2)->default(0.00);
            $table->decimal('total_setor', 14, 2)->default(0.00);
            $table->decimal('total_tarik', 14, 2)->default(0.00);
            $table->decimal('total_potongan', 14, 2)->default(0.00);

            $table->enum('status', ['Aktif', 'Ditangguhkan', 'Ditutup', 'Dibagikan'])->default('Aktif');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index('murid_id');
            $table->index('ustadz_id');
            $table->index('ruangan_id');
            $table->index('jenis_nasabah');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tabungans');
    }
};
