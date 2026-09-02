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
        Schema::table('tagihan_murids', function (Blueprint $table) {
            $table->foreignId('bulan_hijriyah_id')
                ->nullable()
                ->after('pengaturan_tagihan_id')
                ->constrained('bulan_hijriyahs')
                ->nullOnDelete(); // Jika bulan dihapus, tagihan tidak hilang, tapi status bulannya jadi NULL

            // Tambahkan relasi ke semester (Untuk Uang Ujian / Buku / dll)
            $table->foreignId('semester_id')
                ->nullable()
                ->after('bulan_hijriyah_id')
                ->constrained('semesters')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tagihan_murids', function (Blueprint $table) {
            $table->dropForeign(['bulan_hijriyah_id']);
            $table->dropColumn('bulan_hijriyah_id');

            $table->dropForeign(['semester_id']);
            $table->dropColumn('semester_id');
        });
    }
};
