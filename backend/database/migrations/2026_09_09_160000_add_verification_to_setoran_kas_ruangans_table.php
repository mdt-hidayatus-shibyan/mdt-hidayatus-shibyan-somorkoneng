<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('setoran_kas_ruangans', function (Blueprint $table) {
            $table->enum('status', ['Menunggu Verifikasi', 'Diterima', 'Ditolak'])
                ->default('Menunggu Verifikasi')
                ->after('jumlah_setor');
            $table->text('catatan_verifikasi')->nullable()->after('status');
            $table->foreignId('diverifikasi_oleh')->nullable()->after('catatan_verifikasi')->constrained('users')->nullOnDelete();
            $table->timestamp('diverifikasi_pada')->nullable()->after('diverifikasi_oleh');
            $table->foreignId('transaksi_tabungan_id')->nullable()->after('diverifikasi_pada')->constrained('transaksi_tabungans')->nullOnDelete();
        });

        // Update data setoran yang sudah ada sebelumnya agar berstatus 'Diterima'
        DB::table('setoran_kas_ruangans')->whereNull('status')->orWhere('status', '')->update([
            'status' => 'Diterima',
            'diverifikasi_pada' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('setoran_kas_ruangans', function (Blueprint $table) {
            $table->dropForeign(['diverifikasi_oleh']);
            $table->dropForeign(['transaksi_tabungan_id']);
            $table->dropColumn([
                'status',
                'catatan_verifikasi',
                'diverifikasi_oleh',
                'diverifikasi_pada',
                'transaksi_tabungan_id',
            ]);
        });
    }
};
