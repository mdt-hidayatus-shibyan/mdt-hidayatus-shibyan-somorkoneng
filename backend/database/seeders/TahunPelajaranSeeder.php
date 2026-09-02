<?php

namespace Database\Seeders;

use App\Models\TahunPelajaran;
use Illuminate\Database\Seeder;

class TahunPelajaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TahunPelajaran::create([
            'id' => 1,
            'nama_hijriyah' => '1446-1447',
            'nama_masehi' => '2025-2026',
            'is_active' => 1
        ]);
    }
}
