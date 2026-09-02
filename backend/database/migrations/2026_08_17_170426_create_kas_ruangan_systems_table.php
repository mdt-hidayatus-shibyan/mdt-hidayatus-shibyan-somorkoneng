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
        Schema::create('pengaturan_kas_ruangans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ruangan_id')->constrained('ruangans')->cascadeOnDelete();
            $table->integer('nominal_laki')->default(0);
            $table->integer('nominal_perempuan')->default(0);
            $table->timestamps();
        });
        Schema::create('pembayaran_kas_ruangans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ruangan_id')->constrained('ruangans')->cascadeOnDelete();
            $table->foreignId('murid_id')->constrained('murids')->cascadeOnDelete();
            $table->date('tanggal_bayar');
            $table->integer('jumlah_bayar');
            $table->foreignId('diinput_oleh')->constrained('users')->onDelete('cascade');
            $table->boolean('is_disetor')->default(false);
            $table->timestamps();
        });
        Schema::create('setoran_kas_ruangans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ruangan_id')->constrained('ruangans')->cascadeOnDelete();
            $table->foreignId('disetor_oleh')->constrained('users')->onDelete('cascade');
            $table->foreignId('penerima_id')->constrained('users');
            $table->date('tanggal_setor');
            $table->integer('jumlah_setor');
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setoran_kas_ruangans');
        Schema::dropIfExists('pembayaran_kas_ruangans');
        Schema::dropIfExists('pengaturan_kas_ruangans');
    }
};
