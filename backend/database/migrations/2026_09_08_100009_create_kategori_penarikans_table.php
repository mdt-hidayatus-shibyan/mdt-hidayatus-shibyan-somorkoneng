<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('kategori_penarikans')) {
            Schema::create('kategori_penarikans', function (Blueprint $table) {
                $table->id();
                $table->string('nama_kategori', 100);
                $table->string('kode_kategori', 50)->nullable()->unique();
                $table->enum('jenis_tujuan', ['Tunai', 'Tagihan', 'Kas Ruangan', 'Lainnya'])->default('Tunai');
                $table->text('keterangan')->nullable();
                $table->integer('urutan')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });

            // Seed default categories
            DB::table('kategori_penarikans')->insert([
                [
                    'nama_kategori' => 'Tarik Tunai Mandiri',
                    'kode_kategori' => 'TUNAI',
                    'jenis_tujuan' => 'Tunai',
                    'keterangan' => 'Penarikan uang tunai langsung untuk uang saku / keperluan pribadi nasabah.',
                    'urutan' => 1,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'nama_kategori' => 'Pembayaran Tagihan Madrasah',
                    'kode_kategori' => 'TAGIHAN',
                    'jenis_tujuan' => 'Tagihan',
                    'keterangan' => 'Penarikan tabungan dialokasikan untuk pelunasan tagihan SPP, kitab, atau administrasi madrasah.',
                    'urutan' => 2,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'nama_kategori' => 'Pembayaran Kas Ruangan / Kelas',
                    'kode_kategori' => 'KAS_RUANGAN',
                    'jenis_tujuan' => 'Kas Ruangan',
                    'keterangan' => 'Penarikan tabungan dialokasikan untuk pembayaran kas kelas atau kas ruangan murid.',
                    'urutan' => 3,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'nama_kategori' => 'Pembagian Akhir Tabungan',
                    'kode_kategori' => 'AKHIR_PERIODE',
                    'jenis_tujuan' => 'Lainnya',
                    'keterangan' => 'Penarikan atau pembagian saldo saat penutupan siklus periode tabungan / kelulusan murid.',
                    'urutan' => 4,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'nama_kategori' => 'Keperluan & Kebutuhan Murid',
                    'kode_kategori' => 'KEPERLUAN_MURID',
                    'jenis_tujuan' => 'Lainnya',
                    'keterangan' => 'Pembelian seragam, atribut, kitab/buku pelajaran, atau kebutuhan mendesak murid.',
                    'urutan' => 5,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        if (Schema::hasTable('transaksi_tabungans')) {
            Schema::table('transaksi_tabungans', function (Blueprint $table) {
                if (!Schema::hasColumn('transaksi_tabungans', 'kategori_penarikan_id')) {
                    $table->foreignId('kategori_penarikan_id')
                        ->nullable()
                        ->after('ruangan_id')
                        ->constrained('kategori_penarikans')
                        ->nullOnDelete();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('transaksi_tabungans')) {
            Schema::table('transaksi_tabungans', function (Blueprint $table) {
                if (Schema::hasColumn('transaksi_tabungans', 'kategori_penarikan_id')) {
                    $table->dropForeign(['kategori_penarikan_id']);
                    $table->dropColumn('kategori_penarikan_id');
                }
            });
        }

        Schema::dropIfExists('kategori_penarikans');
    }
};
