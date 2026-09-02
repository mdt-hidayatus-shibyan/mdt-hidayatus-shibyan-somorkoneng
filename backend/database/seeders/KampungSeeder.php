<?php

namespace Database\Seeders;


use App\Models\Kampung;
use Illuminate\Database\Seeder;

class KampungSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Kampung::create([
            'id' => 1,
            'kode' => 'A',
            'nama_kampung' => 'HALTAH'
        ]);
        Kampung::create([
            'id' => 4,
            'kode' => 'B',
            'nama_kampung' => 'ONJENAN'
        ]);
        Kampung::create([
            'id' => 5,
            'kode' => 'C',
            'nama_kampung' => 'KARDU'
        ]);
        Kampung::create([
            'id' => 6,
            'kode' => 'D',
            'nama_kampung' => 'LEMBHUNG'
        ]);
        Kampung::create([
            'id' => 7,
            'kode' => 'E',
            'nama_kampung' => 'GUNUNG CEPLONG'
        ]);
        Kampung::create([
            'id' => 8,
            'kode' => 'F',
            'nama_kampung' => 'BEREK PASAR'
        ]);
        Kampung::create([
            'id' => 9,
            'kode' => 'G',
            'nama_kampung' => 'DEJEH PASAR'
        ]);
        Kampung::create([
            'id' => 10,
            'kode' => 'H',
            'nama_kampung' => 'KALEAN'
        ]);
        Kampung::create([
            'id' => 11,
            'kode' => 'I',
            'nama_kampung' => 'MORPAO'
        ]);
        Kampung::create([
            'id' => 12,
            'kode' => 'J',
            'nama_kampung' => 'DEJEH LORONG'
        ]);
        Kampung::create([
            'id' => 13,
            'kode' => 'K',
            'nama_kampung' => 'PONG PATEN'
        ]);
        Kampung::create([
            'id' => 14,
            'kode' => 'L',
            'nama_kampung' => 'PIKATOL'
        ]);
        Kampung::create([
            'id' => 15,
            'kode' => 'M',
            'nama_kampung' => 'BATU GURIS'
        ]);
        Kampung::create([
            'id' => 16,
            'kode' => 'N',
            'nama_kampung' => 'BETES'
        ]);
        Kampung::create([
            'id' => 17,
            'kode' => 'O',
            'nama_kampung' => 'BETES LAOK'
        ]);
        Kampung::create([
            'id' => 18,
            'kode' => 'P',
            'nama_kampung' => 'LAOK LORONG'
        ]);
        Kampung::create([
            'id' => 19,
            'kode' => 'Q',
            'nama_kampung' => 'PONG BHERRUH'
        ]);
    }
}
