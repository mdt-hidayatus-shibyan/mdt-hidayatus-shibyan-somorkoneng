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
        Schema::table('penjualan_koperasis', function (Blueprint $table) {
            $table->enum('status_pembayaran', ['Lunas', 'Belum_Lunas'])->default('Lunas')->after('status');
            $table->datetime('tanggal_pelunasan')->nullable()->after('status_pembayaran');
            $table->string('metode_pelunasan', 50)->nullable()->after('tanggal_pelunasan');
            $table->foreignId('petugas_pelunasan_id')->nullable()->after('metode_pelunasan')->constrained('users')->nullOnDelete();
            $table->text('catatan_pelunasan')->nullable()->after('petugas_pelunasan_id');
        });

        // Set existing Hutang transactions as Belum_Lunas if they haven't been paid
        DB::table('penjualan_koperasis')
            ->where('metode_pembayaran', 'Hutang')
            ->where('nominal_bayar', '<=', 0)
            ->update(['status_pembayaran' => 'Belum_Lunas']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penjualan_koperasis', function (Blueprint $table) {
            $table->dropForeign(['petugas_pelunasan_id']);
            $table->dropColumn([
                'status_pembayaran',
                'tanggal_pelunasan',
                'metode_pelunasan',
                'petugas_pelunasan_id',
                'catatan_pelunasan',
            ]);
        });
    }
};
