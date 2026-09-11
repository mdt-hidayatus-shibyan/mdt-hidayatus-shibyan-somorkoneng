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
        Schema::create('sarpras', function (Blueprint $table) {
            $table->id();
            $table->string('kode_sarpras', 50)->unique();
            $table->string('nama_sarpras', 150);
            $table->string('kategori', 50);
            $table->foreignId('gedung_id')->nullable()->constrained('gedungs')->nullOnDelete();
            $table->foreignId('ruangan_id')->nullable()->constrained('ruangans')->nullOnDelete();
            $table->integer('jumlah')->default(1);
            $table->string('satuan', 30)->default('Unit');
            $table->enum('kondisi', ['tersedia', 'rusak_ringan', 'rusak_berat', 'rusak'])->default('tersedia');
            $table->string('sumber_dana', 100)->nullable();
            $table->date('tanggal_pengadaan')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('foto')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sarpras');
    }
};
