<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paket_koperasi_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paket_koperasi_id')->constrained('paket_koperasis')->cascadeOnDelete();
            $table->foreignId('produk_id')->constrained('produk_koperasis')->cascadeOnDelete();
            $table->integer('jumlah')->default(1); // Qty produk per 1 paket
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paket_koperasi_items');
    }
};
