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
        if (Schema::hasTable('transaksi_tabungans')) {
            Schema::table('transaksi_tabungans', function (Blueprint $table) {
                if (Schema::hasColumn('transaksi_tabungans', 'pengajuan_id')) {
                    $table->dropForeign(['pengajuan_id']);
                    $table->dropColumn('pengajuan_id');
                }
            });
        }

        Schema::dropIfExists('pengajuan_penarikan_tabungans');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to recreate table in reverse
    }
};
