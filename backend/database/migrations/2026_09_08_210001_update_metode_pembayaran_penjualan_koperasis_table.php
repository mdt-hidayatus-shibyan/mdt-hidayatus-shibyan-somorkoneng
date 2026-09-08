<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE penjualan_koperasis MODIFY COLUMN metode_pembayaran VARCHAR(50) NOT NULL DEFAULT 'Tunai'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE penjualan_koperasis MODIFY COLUMN metode_pembayaran ENUM('Tunai', 'QRIS', 'Transfer', 'Potong_Tabungan') NOT NULL DEFAULT 'Tunai'");
    }
};