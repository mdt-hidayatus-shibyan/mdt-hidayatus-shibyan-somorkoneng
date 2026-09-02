<?php

namespace Database\Seeders;

use App\Models\Tingkat;
use Illuminate\Database\Seeder;

class TingkatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Tingkat::create([
            'kode_tingkat' => 'TPQ',
            'urutan_tingkat' => 1,
            'nama_tingkat' => 'TAMAN PENDIDIKAN ALQURAN',
            'kode_mdt_tingkat' => 'RA',
            'nama_mdt_tingkat' => 'RAUDLATUL ATHFAL',
            'kode_warna' => '#10B981',
            'is_active' => 1
        ]);
        Tingkat::create([
            'kode_tingkat' => 'IBT',
            'urutan_tingkat' => 2,
            'nama_tingkat' => 'IBTIDAIYAH',
            'kode_mdt_tingkat' => 'ULA',
            'nama_mdt_tingkat' => 'MADRASAH ULA',
            'kode_warna' => '#F97316',
            'is_active' => 1
        ]);
        Tingkat::create([
            'kode_tingkat' => 'TSA',
            'urutan_tingkat' => 3,
            'nama_tingkat' => 'TSANAWIYAH',
            'kode_mdt_tingkat' => 'WUSHTA',
            'nama_mdt_tingkat' => 'MADRASAH WUSHTA',
            'kode_warna' => '#3B82F6',
            'is_active' => 1
        ]);
    }
}
