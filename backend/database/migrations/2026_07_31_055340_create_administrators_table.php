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
        Schema::create('administrators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('tingkat_id')->nullable()->constrained('tingkats')->nullOnDelete();

            $table->string('nik', 16)->unique(); // NIK biasanya 16 digit
            $table->string('nama_lengkap', 100);
            $table->enum('jenis_kelamin', ['L', 'P']);

            // Lahir & Alamat
            $table->string('tempat_lahir', 50);
            $table->date('tanggal_lahir')->nullable();
            $table->text('alamat');
            $table->string('no_hp', 15);

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
        Schema::dropIfExists('administrators');
    }
};
