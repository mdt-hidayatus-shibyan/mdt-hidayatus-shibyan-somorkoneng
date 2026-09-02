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
        Schema::create('presensi_murids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semester_id')
                ->nullable()
                ->constrained('semesters')
                ->cascadeOnDelete();
            $table->foreignId('jadwal_pelajaran_id')->constrained('jadwal_pelajarans')->cascadeOnDelete();
            $table->foreignId('murid_id')->constrained('murids')->cascadeOnDelete();
            $table->date('tanggal');
            $table->enum('status', ['Hadir', 'Sakit', 'Izin', 'Alpha', 'Dispensasi'])->default('Hadir');


            $table->timestamps();

            $table->unique(['jadwal_pelajaran_id', 'murid_id', 'tanggal'], 'presensi_murid_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensi_murids');
    }
};
