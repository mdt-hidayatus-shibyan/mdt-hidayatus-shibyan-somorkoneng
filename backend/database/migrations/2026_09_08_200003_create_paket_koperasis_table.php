<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paket_koperasis', function (Blueprint $table) {
            $table->id();
            $table->string('kode_paket', 60)->unique()->index(); // Barcode Paket (e.g. PKT-KLS5-IBT)
            $table->string('nama_paket', 150); // e.g. "Paket Kitab Kelas 5 Ibtidaiyah"
            $table->foreignId('level_id')->nullable()->constrained('levels')->nullOnDelete(); // Kelas/Level peruntukan
            $table->foreignId('tingkat_id')->nullable()->constrained('tingkats')->nullOnDelete(); // Tingkat Ula/Wustha
            $table->decimal('harga_paket', 12, 2)->default(0.00); // Harga jual bundling
            $table->decimal('total_hpp_komponen', 12, 2)->default(0.00); // Total HPP akumulasi item
            $table->string('foto')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paket_koperasis');
    }
};
