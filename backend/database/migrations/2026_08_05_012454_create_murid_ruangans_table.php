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
        Schema::create('murid_ruangans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tahun_pelajaran_id')->constrained('tahun_pelajarans')->cascadeOnDelete();

            $table->foreignId('ruangan_id')
                ->constrained('ruangans')
                ->cascadeOnDelete();

            $table->foreignId('murid_id')
                ->constrained('murids')
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('murid_ruangans');
    }
};
