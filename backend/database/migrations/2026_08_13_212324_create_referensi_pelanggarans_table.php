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
        Schema::create('referensi_pelanggarans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pelanggaran');
            $table->enum('kategori', ['Ringan', 'Sedang', 'Berat'])->default('Ringan');
            $table->decimal('poin', 8, 1)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referensi_pelanggarans');
    }
};
