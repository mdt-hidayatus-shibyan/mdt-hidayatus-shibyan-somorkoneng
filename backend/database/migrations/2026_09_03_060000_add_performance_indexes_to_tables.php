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
        if (Schema::hasTable('presensi_murids')) {
            Schema::table('presensi_murids', function (Blueprint $table) {
                $table->index(['tanggal', 'jadwal_pelajaran_id'], 'idx_presensi_murids_tgl_jadwal');
                $table->index(['murid_id', 'status'], 'idx_presensi_murids_murid_status');
            });
        }

        if (Schema::hasTable('tagihan_murids')) {
            Schema::table('tagihan_murids', function (Blueprint $table) {
                $table->index(['ruangan_id', 'status_bayar'], 'idx_tagihan_murids_ruangan_status');
                $table->index(['murid_id', 'status_bayar'], 'idx_tagihan_murids_murid_status');
                $table->index(['pengaturan_tagihan_id', 'status_bayar'], 'idx_tagihan_murids_pengaturan_status');
            });
        }

        if (Schema::hasTable('pembayaran_kas_ruangans')) {
            Schema::table('pembayaran_kas_ruangans', function (Blueprint $table) {
                $table->index(['ruangan_id', 'murid_id'], 'idx_pembayaran_kas_ruangan_murid');
                $table->index('is_disetor', 'idx_pembayaran_kas_is_disetor');
            });
        }

        if (Schema::hasTable('pelanggaran_murids')) {
            Schema::table('pelanggaran_murids', function (Blueprint $table) {
                $table->index(['murid_id', 'tanggal'], 'idx_pelanggaran_murids_murid_tgl');
                $table->index(['ruangan_id', 'semester_id'], 'idx_pelanggaran_murids_ruangan_semester');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('presensi_murids')) {
            Schema::table('presensi_murids', function (Blueprint $table) {
                $table->dropIndex('idx_presensi_murids_tgl_jadwal');
                $table->dropIndex('idx_presensi_murids_murid_status');
            });
        }

        if (Schema::hasTable('tagihan_murids')) {
            Schema::table('tagihan_murids', function (Blueprint $table) {
                $table->dropIndex('idx_tagihan_murids_ruangan_status');
                $table->dropIndex('idx_tagihan_murids_murid_status');
                $table->dropIndex('idx_tagihan_murids_pengaturan_status');
            });
        }

        if (Schema::hasTable('pembayaran_kas_ruangans')) {
            Schema::table('pembayaran_kas_ruangans', function (Blueprint $table) {
                $table->dropIndex('idx_pembayaran_kas_ruangan_murid');
                $table->dropIndex('idx_pembayaran_kas_is_disetor');
            });
        }

        if (Schema::hasTable('pelanggaran_murids')) {
            Schema::table('pelanggaran_murids', function (Blueprint $table) {
                $table->dropIndex('idx_pelanggaran_murids_murid_tgl');
                $table->dropIndex('idx_pelanggaran_murids_ruangan_semester');
            });
        }
    }
};
