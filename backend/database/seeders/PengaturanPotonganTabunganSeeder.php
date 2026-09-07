<?php

namespace Database\Seeders;

use App\Models\Tabungan\PengaturanPotonganTabungan;
use Illuminate\Database\Seeder;

class PengaturanPotonganTabunganSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'jenis_nasabah' => 'Murid',
                'persentase_potongan' => 10.00,
                'dasar_musyawarah' => 'Ketentuan Standar Musyawarah Pengurus Madrasah (10%)',
            ],
            [
                'jenis_nasabah' => 'Ustadz',
                'persentase_potongan' => 2.50,
                'dasar_musyawarah' => 'Infaq/Administrasi Ustadz Pengajar (2.5%)',
            ],
            [
                'jenis_nasabah' => 'Kas Ruangan',
                'persentase_potongan' => 0.00,
                'dasar_musyawarah' => 'Kas Kelas/Ruangan Tanpa Potongan (0%)',
            ],
            [
                'jenis_nasabah' => 'Umum',
                'persentase_potongan' => 10.00,
                'dasar_musyawarah' => 'Ketentuan Tabungan Umum/Reguler (10%)',
            ],
        ];

        foreach ($data as $item) {
            PengaturanPotonganTabungan::updateOrCreate(
                ['jenis_nasabah' => $item['jenis_nasabah']],
                [
                    'persentase_potongan' => $item['persentase_potongan'],
                    'dasar_musyawarah' => $item['dasar_musyawarah'],
                ]
            );
        }

        // Seed default Periode Tabungan Berjangka jika belum ada
        $tp = \App\Models\TahunPelajaran::where('is_active', true)->first() ?? \App\Models\TahunPelajaran::first();
        if ($tp && !\App\Models\Tabungan\PeriodeTabungan::exists()) {
            \App\Models\Tabungan\PeriodeTabungan::create([
                'tahun_pelajaran_id' => $tp->id,
                'nama_periode' => 'Tabungan Berjangka ' . $tp->nama_tahun_pelajaran,
                'tanggal_mulai' => date('Y-m-d'),
                'tanggal_penutupan' => date('Y-m-d', strtotime('+10 months')),
                'tanggal_pembagian' => date('Y-m-d', strtotime('+11 months')),
                'status' => 'Aktif',
                'is_active' => true,
                'catatan' => 'Periode Tabungan Reguler Murid Madrasah',
            ]);
        }
    }
}
