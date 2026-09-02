<?php

namespace Database\Seeders;

use App\Models\Level;
use Illuminate\Database\Seeder;

class LevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Level::create([
            'nama_level' => '1 TPQ',
            'urutan_level' => 1,
            'is_active' => 1,
            'tingkat_id' => 1
        ]);
        Level::create([
            'nama_level' => '2 TPQ',
            'urutan_level' => 2,
            'is_active' => 1,
            'tingkat_id' => 1
        ]);
        Level::create([
            'nama_level' => '3 TPQ',
            'urutan_level' => 3,
            'is_active' => 1,
            'tingkat_id' => 1
        ]);
        Level::create([
            'nama_level' => '1 IBT',
            'urutan_level' => 4,
            'is_active' => 1,
            'tingkat_id' => 2
        ]);
        Level::create([
            'nama_level' => '2 IBT',
            'urutan_level' => 5,
            'is_active' => 1,
            'tingkat_id' => 2
        ]);
        Level::create([
            'nama_level' => '3 IBT',
            'urutan_level' => 6,
            'is_active' => 1,
            'tingkat_id' => 2
        ]);
        Level::create([
            'nama_level' => '4 IBT',
            'urutan_level' => 7,
            'is_active' => 1,
            'tingkat_id' => 2
        ]);
        Level::create([
            'nama_level' => '5 IBT',
            'urutan_level' => 8,
            'is_active' => 1,
            'tingkat_id' => 2
        ]);
        Level::create([
            'nama_level' => '6 IBT',
            'urutan_level' => 9,
            'is_active' => 1,
            'tingkat_id' => 2
        ]);
        Level::create([
            'nama_level' => '1 TSA',
            'urutan_level' => 10,
            'is_active' => 1,
            'tingkat_id' => 3
        ]);
        Level::create([
            'nama_level' => '2 TSA',
            'urutan_level' => 11,
            'is_active' => 1,
            'tingkat_id' => 3
        ]);
        Level::create([
            'nama_level' => '3 TSA',
            'urutan_level' => 12,
            'is_active' => 1,
            'tingkat_id' => 3
        ]);
    }
}
