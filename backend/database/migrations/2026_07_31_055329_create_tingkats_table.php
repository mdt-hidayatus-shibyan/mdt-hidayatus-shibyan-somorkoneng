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
        Schema::create('tingkats', function (Blueprint $table) {
            $table->id();
            $table->string('kode_tingkat')->length('5')->unique();
            $table->tinyInteger('urutan_tingkat');
            $table->string('nama_tingkat');
            $table->string('kode_mdt_tingkat');
            $table->string('nama_mdt_tingkat');
            $table->string('kode_warna');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tingkats');
    }
};
