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
        Schema::create('murids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wali_murid_id')->nullable()->constrained('wali_murids')->restrictOnDelete();

            // Data Akademik & Negara
            $table->string('nism', 20)->unique();
            $table->string('nisn', 20)->nullable()->unique();
            $table->string('nik', 16)->nullable()->unique();

            // Data Pribadi Anak
            $table->string('nama_lengkap', 100);
            $table->string('nama_panggilan', 50)->nullable();
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tempat_lahir', 50)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->integer('anak_ke')->nullable();
            $table->enum('hub_kel', ['Anak Kandung', 'Anak Tiri', 'Anak Angkat', 'Cucu', 'Lainnya'])->default('Anak Kandung');

            // --- DATA BIOLOGIS AYAH (Nempel di Anak) ---
            $table->string('nik_ayah', 16)->nullable();
            $table->string('nama_ayah', 100)->nullable();
            $table->enum('status_ayah', ['Hidup', 'Meninggal'])->default('Hidup');

            // --- DATA BIOLOGIS IBU (Nempel di Anak) ---
            $table->string('nik_ibu', 16)->nullable();
            $table->string('nama_ibu', 100)->nullable();
            $table->enum('status_ibu', ['Hidup', 'Meninggal'])->default('Hidup');

            $table->string('foto')->nullable();
            $table->enum('status', ['Aktif', 'Lulus', 'Pindah', 'Berhenti'])->default('Aktif');

            $table->foreignId('tahun_masuk')
                ->nullable()
                ->constrained('tahun_pelajarans')
                ->nullOnDelete();


            $table->foreignId('level_masuk')
                ->nullable()
                ->constrained('levels')
                ->nullOnDelete();


            $table->foreignId('ruangan_masuk')
                ->nullable()
                ->constrained('ruangans')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('murids');
    }
};
