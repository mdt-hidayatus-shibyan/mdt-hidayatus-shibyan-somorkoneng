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
        Schema::create('pengaturan_potongan_tabungans', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis_nasabah', ['Murid', 'Ustadz', 'Kas Ruangan', 'Umum'])->unique();
            $table->decimal('persentase_potongan', 5, 2)->default(0.00);
            $table->string('dasar_musyawarah', 255)->nullable();
            $table->foreignId('diubah_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaturan_potongan_tabungans');
    }
};
