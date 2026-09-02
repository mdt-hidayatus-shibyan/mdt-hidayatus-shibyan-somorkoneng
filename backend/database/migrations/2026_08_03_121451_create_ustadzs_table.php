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
        Schema::create('ustadzs', function (Blueprint $table) {
            $table->id();
            $table->string('kode_ustadz', 10)->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            // Identitas Resmi
            $table->string('nigm', 30)->nullable()->unique();
            $table->string('nik', 16)->nullable()->unique(); // NIK biasanya 16 digit
            $table->string('nama_lengkap', 100);
            $table->enum('jenis_kelamin', ['L', 'P']);

            // Lahir & Alamat
            $table->string('tempat_lahir', 50)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_hp', 15)->nullable();

            // Karir & Berkas
            $table->year('tahun_mulai_mengajar')->nullable();
            $table->string('foto')->nullable();         // Path untuk file foto
            $table->string('tanda_tangan')->nullable(); // Path untuk file tanda tangan
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ustadzs');
    }
};
