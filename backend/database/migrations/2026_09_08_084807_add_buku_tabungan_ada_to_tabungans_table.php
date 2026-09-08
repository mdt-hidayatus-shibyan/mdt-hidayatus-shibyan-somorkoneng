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
            $table->boolean('buku_tabungan_ada')->default(true)->after('status_verifikasi');
            $table->string('status_verifikasi', 50)->default('Belum Diverifikasi')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tabungans', function (Blueprint $table) {
            $table->dropColumn('buku_tabungan_ada');
        });
    }
};
