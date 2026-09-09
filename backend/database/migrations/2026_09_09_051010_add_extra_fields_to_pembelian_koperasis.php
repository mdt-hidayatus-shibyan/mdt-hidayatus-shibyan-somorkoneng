<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembelian_koperasis', function (Blueprint $table) {
            if (!Schema::hasColumn('pembelian_koperasis', 'ongkir')) {
                $table->decimal('ongkir', 14, 2)->default(0.00)->after('total_nominal');
            }
            if (!Schema::hasColumn('pembelian_koperasis', 'diskon')) {
                $table->decimal('diskon', 14, 2)->default(0.00)->after('ongkir');
            }
            if (!Schema::hasColumn('pembelian_koperasis', 'tanggal_jatuh_tempo')) {
                $table->date('tanggal_jatuh_tempo')->nullable()->after('status_pembayaran');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pembelian_koperasis', function (Blueprint $table) {
            $table->dropColumn(['ongkir', 'diskon', 'tanggal_jatuh_tempo']);
        });
    }
};