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
        Schema::create('pendaftaran_spmbs', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pendaftaran', 30)->unique();
            $table->foreignId('tahun_pelajaran_id')->constrained('tahun_pelajarans')->cascadeOnDelete();
            $table->foreignId('level_id')->constrained('levels')->cascadeOnDelete();
            $table->foreignId('wali_murid_id')->nullable()->constrained('wali_murids')->nullOnDelete();

            // Data Pribadi Calon Santri
            $table->string('nama_lengkap', 100);
            $table->string('nama_panggilan', 50)->nullable();
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('nik', 16)->nullable();
            $table->string('nisn', 20)->nullable();
            $table->string('tempat_lahir', 50)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->integer('anak_ke')->nullable();
            $table->enum('hub_kel', ['Anak Kandung', 'Anak Tiri', 'Anak Angkat', 'Cucu', 'Lainnya'])->default('Anak Kandung');

            // Data Biologis Orang Tua
            $table->string('nik_ayah', 16)->nullable();
            $table->string('nama_ayah', 100)->nullable();
            $table->enum('status_ayah', ['Hidup', 'Meninggal'])->default('Hidup');

            $table->string('nik_ibu', 16)->nullable();
            $table->string('nama_ibu', 100)->nullable();
            $table->enum('status_ibu', ['Hidup', 'Meninggal'])->default('Hidup');

            $table->string('foto')->nullable();

            // Status Pendaftaran & Verifikasi
            $table->enum('status_pendaftaran', ['Menunggu Verifikasi', 'Diterima', 'Ditolak'])->default('Menunggu Verifikasi');
            $table->text('catatan_admin')->nullable();

            // Hasil Penerimaan
            $table->foreignId('murid_id')->nullable()->constrained('murids')->nullOnDelete();
            $table->string('nism_diberikan', 20)->nullable();
            $table->timestamp('tanggal_verifikasi')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();

            // Keuangan SPMB
            $table->bigInteger('nominal_biaya')->default(0);
            $table->enum('status_pembayaran', ['Belum Lunas', 'Lunas', 'Gratis'])->default('Belum Lunas');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendaftaran_spmbs');
    }
};
