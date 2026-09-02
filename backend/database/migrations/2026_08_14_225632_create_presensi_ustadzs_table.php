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
        Schema::create('presensi_ustadzs', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->foreignId('jadwal_pelajaran_id')->constrained('jadwal_pelajarans')->cascadeOnDelete();
            $table->foreignId('ustadz_id')->constrained('ustadzs')->cascadeOnDelete();
            $table->enum('status', ['Hadir', 'Sakit', 'Izin', 'Alpha', 'Kosong'])->default('Hadir');
            $table->foreignId('ustadz_pengganti_id')->nullable()->constrained('ustadzs')->nullOnDelete();
            $table->string('keterangan')->nullable();
            $table->foreignId('diinput_oleh_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->unique(['tanggal', 'jadwal_pelajaran_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensi_ustadzs');
    }
};
