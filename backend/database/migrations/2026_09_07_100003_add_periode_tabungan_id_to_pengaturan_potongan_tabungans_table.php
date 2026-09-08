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
        if (!Schema::hasColumn('pengaturan_potongan_tabungans', 'periode_tabungan_id')) {
            Schema::table('pengaturan_potongan_tabungans', function (Blueprint $table) {
                $table->foreignId('periode_tabungan_id')->nullable()->after('id')->constrained('periode_tabungans')->cascadeOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('pengaturan_potongan_tabungans', 'periode_tabungan_id')) {
            Schema::table('pengaturan_potongan_tabungans', function (Blueprint $table) {
                $table->dropForeign(['periode_tabungan_id']);
                $table->dropColumn('periode_tabungan_id');
            });
        }
    }
};
