<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pengajuan_penarikan_tabungans')) {
            Schema::table('pengajuan_penarikan_tabungans', function (Blueprint $table) {
                $table->unsignedBigInteger('diajukan_oleh')->nullable()->change();
            });
        }

        Schema::table('transaksi_tabungans', function (Blueprint $table) {
            $table->unsignedBigInteger('petugas_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('pengajuan_penarikan_tabungans')) {
            Schema::table('pengajuan_penarikan_tabungans', function (Blueprint $table) {
                $table->unsignedBigInteger('diajukan_oleh')->nullable(false)->change();
            });
        }

        Schema::table('transaksi_tabungans', function (Blueprint $table) {
            $table->unsignedBigInteger('petugas_id')->nullable(false)->change();
        });
    }
};
