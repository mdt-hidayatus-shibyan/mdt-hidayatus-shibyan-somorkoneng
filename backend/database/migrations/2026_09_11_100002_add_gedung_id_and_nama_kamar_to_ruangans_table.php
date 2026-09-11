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
        Schema::table('ruangans', function (Blueprint $table) {
            $table->foreignId('gedung_id')->nullable()->after('level_id')->constrained('gedungs')->nullOnDelete();
            $table->string('nama_kamar', 100)->nullable()->after('nama_ruangan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ruangans', function (Blueprint $table) {
            $table->dropForeign(['gedung_id']);
            $table->dropColumn(['gedung_id', 'nama_kamar']);
        });
    }
};
