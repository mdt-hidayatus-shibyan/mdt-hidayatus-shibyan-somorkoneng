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
        Schema::create('wali_murids', function (Blueprint $table) {
            $table->id();

            $table->string('no_registrasi', 20)->unique();

            $table->string('no_kk', 16)->nullable()->unique();
            $table->enum('kepala_keluarga', ['Ayah', 'Ibu', 'Wali'])->default('Ayah');
            $table->string('nama_kepala_keluarga', 100);

            $table->string('no_hp', 15)->nullable();
            $table->text('alamat_detail')->nullable();

            $table->foreignId('kampung_id')->constrained('kampungs')->cascadeOnDelete();
            $table->boolean('is_ustadz')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wali_murids');
    }
};
