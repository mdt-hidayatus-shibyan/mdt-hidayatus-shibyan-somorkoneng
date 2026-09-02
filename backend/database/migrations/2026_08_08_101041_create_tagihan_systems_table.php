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
        Schema::create('pengaturan_tagihans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_pelajaran_id')->constrained('tahun_pelajarans')->onDelete('cascade');
            $table->foreignId('level_id')->nullable()->constrained('levels')->onDelete('cascade');
            $table->string('kode_tagihan');
            $table->string('nama_tagihan');
            $table->enum('tipe', ['bulanan', 'semester', 'insidental']);
            $table->bigInteger('nominal');
            $table->timestamps();
        });

        Schema::create('pembayaran_tagihans', function (Blueprint $table) {
            $table->id();
            $table->string('no_transaksi')->unique();
            $table->date('tanggal_bayar');
            $table->string('tipe_pembayar')->default('Wali Murid');
            $table->string('nama_pembayar');
            $table->text('alamat_pembayar')->nullable();
            $table->string('metode_pembayaran');
            $table->string('rekening_penerima')->nullable();
            $table->bigInteger('total_nominal');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::create('tagihan_murids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('murid_id')->constrained('murids')->onDelete('cascade');
            $table->foreignId('ruangan_id')->constrained('ruangans')->onDelete('cascade');
            $table->foreignId('pengaturan_tagihan_id')->constrained('pengaturan_tagihans')->onDelete('cascade');
            $table->foreignId('bulan_hijriyah_id')
                ->nullable()
                ->constrained('bulan_hijriyahs')
                ->nullOnDelete(); // Jika bulan dihapus, tagihan tidak hilang, tapi status bulannya jadi NULL

            // Tambahkan relasi ke semester (Untuk Uang Ujian / Buku / dll)
            $table->foreignId('semester_id')
                ->nullable()
                ->constrained('semesters')
                ->nullOnDelete();
            $table->string('nama_tagihan_spesifik');
            $table->bigInteger('nominal_tagihan');
            $table->enum('status_bayar', ['Belum Lunas', 'Lunas', 'Bebas/Gratis', 'Ditanggung Donatur'])->default('Belum Lunas');
            $table->foreignId('pembayaran_tagihan_id')->nullable()->constrained('pembayaran_tagihans')->onDelete('SET NULL');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihan_murids');
        Schema::dropIfExists('pembayaran_tagihans');
        Schema::dropIfExists('pengaturan_tagihans');
    }
};
