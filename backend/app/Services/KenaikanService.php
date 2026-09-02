<?php

namespace App\Services;

use App\Models\Administrator;
use App\Models\Arsip\ArsipDokumen;
use App\Models\Kepengurusan\Pengurus;
use App\Models\Murid;
use App\Models\Ruangan;
use Illuminate\Support\Facades\Auth;


class KenaikanService
{
    /**
     * Dieksekusi setelah data di tabel RiwayatKenaikan tersimpan
     */
    public function terbitkanArsipSKdanIjazah($tahun_pelajaran_id, $ruangan_id, $murid_id, $status_keputusan, $no_sk, $nilai_akumulasi)
    {
        $murid = Murid::with('waliMurid.kampung')->findOrFail($murid_id);
        $ruangan = Ruangan::with(['level.tingkat', 'tahunPelajaran'])->findOrFail($ruangan_id);

        $pengasuh = Pengurus::getAktifByJabatan('Pengasuh');
        $kabid = Pengurus::getAktifByJabatan('Kepala Bidang Pendidikan', $ruangan->level->tingkat_id ?? null);
        $tingkat_id = $ruangan->level->tingkat_id ?? null;

        // 1. Cari admin spesifik untuk tingkat tersebut
        $tingkat_id = $ruangan->level->tingkat_id ?? null;
        $nama_tingkat = $ruangan->level->tingkat->nama_tingkat ?? 'MADRASAH';

        // 1. Cari admin spesifik
        $admin = Administrator::where('tingkat_id', $tingkat_id)->first();
        $admin_jabatan = 'Admin Tingkat ' . $nama_tingkat; // Jabatan default jika ketemu

        // 2. Jika tidak ketemu, fallback ke Admin Pusat
        if (!$admin) {
            $admin = Administrator::whereNull('tingkat_id')->first();
            $admin_jabatan = 'Kepala Administrator'; // Berubah jadi Admin Pusat
        }

        $levelNama = $ruangan->level->nama_level ?? '';
        $isKelasAkhir = in_array($levelNama, ['3 TPQ', '6 IBT', '3 TSA']);

        // =========================================================================
        // 1. BUAT ARSIP SK (UNTUK SEMUA STATUS: NAIK, TINGGAL, LULUS)
        // =========================================================================
        $snapshotSK = [
            'nomor_dokumen'      => $no_sk,
            'tahun_pelajaran'    => $ruangan->tahunPelajaran->nama_hijriyah . ' H - ' . $ruangan->tahunPelajaran->nama_masehi . ' M',
            'nama_murid'         => $murid->nama_lengkap,
            'nism'               => $murid->nism ?? '-',
            'nama_ruangan'       => $ruangan->nama_ruangan,
            'nilai_akumulasi'    => $nilai_akumulasi,
            'tempat_tgl_lahir' => ($murid->tempat_lahir ?? '-') . ', ' . ($murid->tanggal_lahir ? \Carbon\Carbon::parse($murid->tanggal_lahir)->translatedFormat('d F Y') : '-'),
            'nama_wali'          => $murid->waliMurid->nama_ayah ?? ($murid->nama_ayah ?? '-'),

            'lulus_dari_tingkat' => $ruangan->level->tingkat->nama_tingkat ?? 'MADRASAH',
            'status_keputusan'   => $status_keputusan, // Naik Kelas / Tinggal Kelas / Lulus

            // Penandatangan
            'pengasuh_nama'      => $pengasuh?->anggota?->nama_lengkap ?? '-',
            'pengasuh_id'        => $pengasuh->id ?? null,
            'kabid_nama'         => $kabid?->anggota?->nama_lengkap ?? '-',
            'kabid_id'           => $kabid->id ?? null,
            'admin_nama'         => $admin->nama_lengkap ?? 'Kepala Administrator',
            'admin_id'           => $admin->id ?? null,
            'admin_jabatan'      => $admin_jabatan,

            'tanggal_disahkan'   => now()->format('Y-m-d')
        ];

        ArsipDokumen::updateOrCreate(
            ['tipe_dokumen' => 'sk_keputusan', 'referensi_tipe' => get_class($murid), 'referensi_id' => $murid->id],
            ['dicetak_oleh' => Auth::id(), 'snapshot_data' => $snapshotSK]
        );

        // =========================================================================
        // 2. BUAT ARSIP IJAZAH (HANYA UNTUK YANG LULUS DARI KELAS AKHIR)
        // =========================================================================
        if ($isKelasAkhir && $status_keputusan === 'Lulus') {
            $nomorIjazah = 'IJZ/MDT-HS/' . now()->format('Y/m') . '/' . str_pad($murid->id, 4, '0', STR_PAD_LEFT);
            $snapshotIjazah = array_merge($snapshotSK, [
                'nomor_dokumen' => $nomorIjazah,
                'matriks_nilai' => [], // Nanti bisa diisi transkrip nilai
                'rata_rata'     => 0,
            ]);

            ArsipDokumen::updateOrCreate(
                ['tipe_dokumen' => 'ijazah', 'referensi_tipe' => get_class($murid), 'referensi_id' => $murid->id],
                ['dicetak_oleh' => Auth::id(), 'snapshot_data' => $snapshotIjazah]
            );
        }
    }
}
