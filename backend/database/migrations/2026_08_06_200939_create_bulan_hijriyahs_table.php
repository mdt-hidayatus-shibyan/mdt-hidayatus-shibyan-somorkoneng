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
        Schema::create('bulan_hijriyahs', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('semester_id')->constrained('semesters')->cascadeOnDelete();
            $table->foreignId('tahun_pelajaran_id')
                ->constrained('tahun_pelajarans')
                ->cascadeOnDelete();
            $table->string('nama_bulan');
            $table->string('tahun_hijriyah', 4)->nullable();
            $table->integer('urutan');
            $table->date('tanggal_mulai_masehi');
            $table->date('tanggal_selesai_masehi');

            // Bulan yang sedang berjalan bulan ini
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bulan_hijriyahs');
    }
};
