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
        Schema::table('tabungans', function (Blueprint $table) {
            $table->enum('status_verifikasi', ['Belum Diverifikasi', 'Cocok', 'Selisih'])->default('Belum Diverifikasi')->after('status');
            $table->decimal('saldo_buku_fisik', 14, 2)->nullable()->after('status_verifikasi');
            $table->foreignId('diverifikasi_oleh')->nullable()->after('saldo_buku_fisik')->constrained('users')->nullOnDelete();
            $table->timestamp('diverifikasi_pada')->nullable()->after('diverifikasi_oleh');
            $table->text('catatan_verifikasi')->nullable()->after('diverifikasi_pada');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tabungans', function (Blueprint $table) {
            $table->dropForeign(['diverifikasi_oleh']);
            $table->dropColumn([
                'status_verifikasi',
                'saldo_buku_fisik',
                'diverifikasi_oleh',
                'diverifikasi_pada',
                'catatan_verifikasi',
            ]);
        });
    }
};
