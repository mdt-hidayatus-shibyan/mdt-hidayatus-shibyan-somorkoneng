<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BulanHijriyah;
use App\Models\Murid;
use App\Models\PelanggaranMurid;
use App\Models\PresensiMurid;
use App\Models\TagihanMurid;
use App\Models\TahunPelajaran;
use App\Models\Ujian\NilaiUjian;
use App\Models\WaliMurid;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WaliMuridApiController extends Controller
{
    /**
     * Dapatkan data Wali Murid berdasarkan user session atau parameter
     */
    private function resolveWali(Request $request)
    {
        $user = $request->user();

        // Cek jika usernamewali
        if (str_starts_with($user->username ?? '', 'wali_')) {
            $noReg = substr($user->username, 5);
            $wali = WaliMurid::where('no_registrasi', $noReg)->first();
            if ($wali) return $wali;
        }

        if ($request->filled('wali_id')) {
            return WaliMurid::find($request->wali_id);
        }

        // Fallback wali pertama untuk testing jika admin
        return WaliMurid::where('is_active', true)->first();
    }

    /**
     * Dashboard Aplikasi Wali Murid (app_murid)
     */
    public function getDashboard(Request $request)
    {
        $wali = $this->resolveWali($request);

        if (!$wali) {
            return response()->json([
                'success' => false,
                'message' => 'Data Wali Murid tidak ditemukan.'
            ], 404);
        }

        $tahunAktif = TahunPelajaran::where('is_active', true)->first();
        $tahunId = $tahunAktif?->id;

        // Ambil daftar anak aktif
        $anakList = Murid::with([
            'ruangans' => function ($q) use ($tahunId) {
                if ($tahunId) {
                    $q->where('murid_ruangans.tahun_pelajaran_id', $tahunId);
                }
            },
            'levelMasuk',
            'ruanganMasuk'
        ])
            ->where('wali_murid_id', $wali->id)
            ->where('status', 'Aktif')
            ->get();

        $anakIds = $anakList->pluck('id')->toArray();

        // Ringkasan Tagihan Semua Anak
        $tagihanQuery = TagihanMurid::whereIn('murid_id', $anakIds);
        if ($tahunId) {
            $tagihanQuery->whereHas('ruangan', fn($q) => $q->where('tahun_pelajaran_id', $tahunId));
        }
        $totalTagihan = (clone $tagihanQuery)->sum('nominal_tagihan');
        $totalLunas = (clone $tagihanQuery)->where('status_bayar', 'Lunas')->sum('nominal_tagihan');
        $totalTunggakan = max(0, $totalTagihan - $totalLunas);

        // Data Ringkas per Anak
        $dataAnak = $anakList->map(function ($anak) {
            // Presensi hari ini
            $presensiHariIni = PresensiMurid::where('murid_id', $anak->id)
                ->whereDate('tanggal', date('Y-m-d'))
                ->orderBy('id', 'desc')
                ->first();

            return [
                'id'             => $anak->id,
                'nism'           => $anak->nism,
                'nisn'           => $anak->nisn,
                'nama_lengkap'   => $anak->nama_lengkap,
                'nama_panggilan' => $anak->nama_panggilan,
                'jenis_kelamin'  => $anak->jenis_kelamin,
                'foto'           => $anak->foto_url,
                'ruangan'        => $anak->nama_ruangan_aktif,
                'status_hari_ini' => $presensiHariIni->status ?? 'Belum Ada Sesi',
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => [
                'wali' => [
                    'id'                   => $wali->id,
                    'nama_kepala_keluarga' => $wali->nama_kepala_keluarga,
                    'no_registrasi'        => $wali->no_registrasi,
                    'no_kk'                => $wali->no_kk,
                    'alamat'               => $wali->alamat_detail,
                    'kampung'              => $wali->kampung->nama_kampung ?? '-',
                ],
                'tahun_pelajaran' => [
                    'nama_hijriyah' => $tahunAktif->nama_hijriyah ?? '-',
                    'nama_masehi'   => $tahunAktif->nama_masehi ?? '-',
                ],
                'ringkasan_keuangan' => [
                    'total_tagihan'   => (int) $totalTagihan,
                    'total_lunas'     => (int) $totalLunas,
                    'total_tunggakan' => (int) $totalTunggakan,
                ],
                'anak' => $dataAnak,
            ]
        ], 200);
    }

    /**
     * Detail Profil & Biodata Murid
     */
    public function getDetailAnak($id, Request $request)
    {
        $murid = Murid::with(['waliMurid.kampung', 'ruangans', 'levelMasuk', 'tahunMasuk'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => [
                'id'             => $murid->id,
                'nism'           => $murid->nism,
                'nisn'           => $murid->nisn,
                'nik'            => $murid->nik,
                'nama_lengkap'   => $murid->nama_lengkap,
                'nama_panggilan' => $murid->nama_panggilan,
                'jenis_kelamin'  => $murid->jenis_kelamin,
                'tempat_lahir'   => $murid->tempat_lahir,
                'tanggal_lahir'  => $murid->tanggal_lahir ? Carbon::parse($murid->tanggal_lahir)->format('d-m-Y') : null,
                'anak_ke'        => $murid->anak_ke,
                'hub_kel'        => $murid->hub_kel,
                'nama_ayah'      => $murid->nama_ayah,
                'status_ayah'    => $murid->status_ayah,
                'nama_ibu'       => $murid->nama_ibu,
                'status_ibu'     => $murid->status_ibu,
                'foto'           => $murid->foto_url,
                'status'         => $murid->status,
                'ruangan'        => $murid->nama_ruangan_aktif,
                'kampung'        => $murid->waliMurid->kampung->nama_kampung ?? '-',
            ]
        ], 200);
    }

    /**
     * Detail Tagihan & Riwayat Pembayaran Murid
     */
    public function getTagihanAnak($id, Request $request)
    {
        $murid = Murid::findOrFail($id);
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();
        $tahunId = $tahunAktif?->id;

        $tagihans = TagihanMurid::with(['pengaturanTagihan', 'bulanHijriyah', 'pembayaranTagihan', 'semester'])
            ->where('murid_id', $murid->id)
            ->when($tahunId, function ($q) use ($tahunId) {
                $q->whereHas('ruangan', fn($rq) => $rq->where('tahun_pelajaran_id', $tahunId));
            })
            ->orderBy('id', 'asc')
            ->get();

        // SPP Bulanan
        $sppList = $tagihans->where('pengaturanTagihan.tipe', 'bulanan')->values()->map(function ($t) {
            return [
                'id'            => $t->id,
                'bulan'         => $t->bulanHijriyah->nama_bulan ?? $t->nama_tagihan_spesifik,
                'nominal'       => (int) $t->nominal_tagihan,
                'status_bayar'  => $t->status_bayar,
                'tanggal_bayar' => $t->pembayaranTagihan ? Carbon::parse($t->pembayaranTagihan->tanggal_bayar)->format('d-m-Y') : null,
                'no_transaksi'  => $t->pembayaranTagihan->no_transaksi ?? null,
            ];
        });

        // Tagihan Non-SPP (Insidental / Semester)
        $nonSppList = $tagihans->where('pengaturanTagihan.tipe', '!=', 'bulanan')->values()->map(function ($t) {
            return [
                'id'            => $t->id,
                'nama_tagihan'  => $t->nama_tagihan_spesifik,
                'tipe'          => $t->pengaturanTagihan->tipe ?? 'insidental',
                'nominal'       => (int) $t->nominal_tagihan,
                'status_bayar'  => $t->status_bayar,
                'tanggal_bayar' => $t->pembayaranTagihan ? Carbon::parse($t->pembayaranTagihan->tanggal_bayar)->format('d-m-Y') : null,
                'no_transaksi'  => $t->pembayaranTagihan->no_transaksi ?? null,
            ];
        });

        $totalTagihan = $tagihans->sum('nominal_tagihan');
        $totalLunas = $tagihans->where('status_bayar', 'Lunas')->sum('nominal_tagihan');

        return response()->json([
            'success' => true,
            'data'    => [
                'murid' => [
                    'id'           => $murid->id,
                    'nama_lengkap' => $murid->nama_lengkap,
                    'nism'         => $murid->nism,
                ],
                'summary' => [
                    'total_tagihan'   => (int) $totalTagihan,
                    'total_lunas'     => (int) $totalLunas,
                    'total_tunggakan' => (int) max(0, $totalTagihan - $totalLunas),
                ],
                'spp'     => $sppList,
                'non_spp' => $nonSppList,
            ]
        ], 200);
    }

    /**
     * Rekap Presensi / Kehadiran Murid
     */
    public function getPresensiAnak($id, Request $request)
    {
        $murid = Murid::findOrFail($id);

        $presensis = PresensiMurid::with(['jadwalPelajaran.mataPelajaran'])
            ->where('murid_id', $murid->id)
            ->orderBy('tanggal', 'desc')
            ->limit(50)
            ->get();

        $stats = [
            'hadir'      => PresensiMurid::where('murid_id', $murid->id)->where('status', 'Hadir')->count(),
            'sakit'      => PresensiMurid::where('murid_id', $murid->id)->where('status', 'Sakit')->count(),
            'izin'       => PresensiMurid::where('murid_id', $murid->id)->where('status', 'Izin')->count(),
            'alpha'      => PresensiMurid::where('murid_id', $murid->id)->where('status', 'Alpha')->count(),
            'dispensasi' => PresensiMurid::where('murid_id', $murid->id)->where('status', 'Dispensasi')->count(),
        ];

        $totalSesi = array_sum($stats);
        $persentaseHadir = $totalSesi > 0 ? round(($stats['hadir'] / $totalSesi) * 100, 1) : 0;

        $riwayat = $presensis->map(function ($p) {
            return [
                'id'        => $p->id,
                'tanggal'   => Carbon::parse($p->tanggal)->format('d-m-Y'),
                'hari'      => Carbon::parse($p->tanggal)->translatedFormat('l'),
                'mapel'     => $p->jadwalPelajaran->mataPelajaran->nama_mapel ?? 'Pelajaran',
                'status'    => $p->status,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => [
                'murid' => [
                    'id'           => $murid->id,
                    'nama_lengkap' => $murid->nama_lengkap,
                    'nism'         => $murid->nism,
                ],
                'statistik' => [
                    'total_sesi'       => $totalSesi,
                    'persentase_hadir' => $persentaseHadir,
                    'rincian'          => $stats,
                ],
                'riwayat' => $riwayat,
            ]
        ], 200);
    }

    /**
     * Catatan Pelanggaran & Poin Murid
     */
    public function getPelanggaranAnak($id, Request $request)
    {
        $murid = Murid::findOrFail($id);

        $pelanggarans = PelanggaranMurid::with(['referensiPelanggaran', 'ruangan'])
            ->where('murid_id', $murid->id)
            ->orderBy('tanggal', 'desc')
            ->get();

        $totalPoin = $pelanggarans->sum(fn($p) => $p->referensiPelanggaran->poin ?? 0);

        $riwayat = $pelanggarans->map(function ($p) {
            return [
                'id'         => $p->id,
                'tanggal'    => Carbon::parse($p->tanggal)->format('d-m-Y'),
                'kasus'      => $p->referensiPelanggaran->nama_pelanggaran ?? 'Pelanggaran',
                'kategori'   => $p->referensiPelanggaran->kategori ?? 'Ringan',
                'poin'       => $p->referensiPelanggaran->poin ?? 0,
                'keterangan' => $p->keterangan ?: '-',
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => [
                'murid' => [
                    'id'           => $murid->id,
                    'nama_lengkap' => $murid->nama_lengkap,
                    'nism'         => $murid->nism,
                ],
                'total_poin' => (int) $totalPoin,
                'total_kasus' => $pelanggarans->count(),
                'riwayat'    => $riwayat,
            ]
        ], 200);
    }

    /**
     * Rapor Nilai & Hasil Ujian Murid
     */
    public function getNilaiAnak($id, Request $request)
    {
        $murid = Murid::findOrFail($id);
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();
        $tahunId = $tahunAktif?->id;

        $nilais = NilaiUjian::with(['ujian.semester', 'mataPelajaran', 'ruangan'])
            ->where('murid_id', $murid->id)
            ->where('is_published', true)
            ->when($tahunId, function ($q) use ($tahunId) {
                $q->whereHas('ujian', fn($uq) => $uq->where('tahun_pelajaran_id', $tahunId));
            })
            ->get();

        // Group by Ujian
        $grouped = $nilais->groupBy('ujian_id')->map(function ($items) use ($murid) {
            $first = $items->first();
            $ujian = $first->ujian;
            $ruangan = $first->ruangan;

            $mapelList = $items->map(function ($n) {
                $kkm = $n->kkm ?? 65;
                $angka = $n->nilai_angka ?? 0;
                $huruf = $n->nilai_huruf ?? ($angka >= 85 ? 'A' : ($angka >= 75 ? 'B' : ($angka >= 65 ? 'C' : 'D')));
                return [
                    'id'             => $n->id,
                    'mapel'          => $n->mataPelajaran->nama_mapel ?? 'Mata Pelajaran',
                    'kkm'            => $kkm,
                    'nilai_angka'    => (float) $angka,
                    'nilai_huruf'    => $huruf,
                    'is_lulus'       => $angka >= $kkm,
                    'catatan'        => $n->catatan ?: '-',
                ];
            });

            $rataRata = $items->count() > 0 ? round($items->avg('nilai_angka'), 2) : 0;
            $totalNilai = $items->sum('nilai_angka');

            return [
                'ujian_id'       => $ujian->id ?? null,
                'nama_ujian'     => $ujian->nama_ujian ?? 'Ujian Madrasah',
                'tipe_ujian'     => $ujian->tipe_ujian ?? $ujian->jenis_ujian ?? 'IMDA',
                'semester'       => $ujian->semester->nama_semester ?? 'Semester Aktif',
                'ruangan'        => $ruangan->nama_ruangan ?? '-',
                'total_mapel'    => $items->count(),
                'total_nilai'    => (float) $totalNilai,
                'rata_rata'      => (float) $rataRata,
                'daftar_nilai'   => $mapelList,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data'    => [
                'murid' => [
                    'id'           => $murid->id,
                    'nama_lengkap' => $murid->nama_lengkap,
                    'nism'         => $murid->nism,
                    'ruangan'      => $murid->nama_ruangan_aktif,
                ],
                'daftar_ujian' => $grouped,
            ]
        ], 200);
    }

    /**
     * Jadwal Pelajaran Murid di Ruangan Aktif
     */
    public function getJadwalAnak($id, Request $request)
    {
        $murid = Murid::findOrFail($id);
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();
        $tahunId = $tahunAktif?->id;

        $ruanganAktif = $murid->ruangans()
            ->when($tahunId, fn($q) => $q->where('murid_ruangans.tahun_pelajaran_id', $tahunId))
            ->first();

        if (!$ruanganAktif) {
            return response()->json([
                'success' => true,
                'data'    => [
                    'murid' => [
                        'id'           => $murid->id,
                        'nama_lengkap' => $murid->nama_lengkap,
                        'nism'         => $murid->nism,
                    ],
                    'ruangan' => '-',
                    'jadwal'  => [],
                ]
            ], 200);
        }

        $jadwals = \App\Models\JadwalPelajaran::with(['mataPelajaran', 'ustadz'])
            ->where('ruangan_id', $ruanganAktif->id)
            ->get();

        $hariOrder = ['Sabtu' => 1, 'Ahad' => 2, 'Senin' => 3, 'Selasa' => 4, 'Rabu' => 5, 'Kamis' => 6];

        $sorted = $jadwals->sortBy(function ($j) use ($hariOrder) {
            return ($hariOrder[$j->hari] ?? 99) * 100 + (is_numeric($j->jam_ke) ? (int)$j->jam_ke : 10);
        })->values()->map(function ($j) {
            return [
                'id'       => $j->id,
                'hari'     => $j->hari,
                'jam_ke'   => $j->jam_ke,
                'waktu'    => $j->jam_mulai ? ($j->jam_mulai . ' - ' . $j->jam_selesai) : 'Sesuai Jadwal',
                'mapel'    => $j->mataPelajaran->nama_mapel ?? 'Pelajaran',
                'ustadz'   => $j->ustadz->nama_lengkap ?? 'Ustadz Pengampu',
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => [
                'murid' => [
                    'id'           => $murid->id,
                    'nama_lengkap' => $murid->nama_lengkap,
                    'nism'         => $murid->nism,
                ],
                'ruangan' => $ruanganAktif->nama_ruangan,
                'jadwal'  => $sorted,
            ]
        ], 200);
    }

    /**
     * Dokumen Arsip Murid: Rapor (IMDA 1 & IMDA 2 / IMNI), SK Kelulusan, dan Ijazah
     */
    public function getDokumenAnak($id, Request $request)
    {
        $murid = Murid::with(['ruangans.level'])->findOrFail($id);
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();
        $tahunId = $tahunAktif?->id;

        $ruanganAktif = $murid->ruangans()
            ->when($tahunId, fn($q) => $q->where('murid_ruangans.tahun_pelajaran_id', $tahunId))
            ->first();

        $levelNama = $ruanganAktif?->level?->nama_level ?? '';
        $isKelasAkhir = in_array($levelNama, ['3 TPQ', '6 IBT', '3 TSA']);

        // Ambil semua arsip dokumen murid ini
        $arsips = \App\Models\Arsip\ArsipDokumen::where('referensi_id', $murid->id)
            ->where('referensi_tipe', Murid::class)
            ->orderBy('created_at', 'desc')
            ->get();

        // 1. Rapor Murid (IMDA 1, IMDA 2, IMNI)
        $raporList = $arsips->where('tipe_dokumen', 'rapor_murid')->values()->map(function ($a) {
            $data = $a->snapshot_data ?? [];
            return [
                'id'               => $a->id,
                'nama_dokumen'     => $data['nama_ujian'] ?? 'Rapor Ujian',
                'tipe_ujian'       => $data['tipe_ujian'] ?? 'IMDA',
                'tahun_pelajaran'  => $data['tahun_pelajaran'] ?? '-',
                'nomor_dokumen'    => $data['nomor_dokumen'] ?? '-',
                'ruangan'          => $data['nama_ruangan'] ?? '-',
                'rata_rata'        => (float) ($data['rata_rata'] ?? 0),
                'total_mapel'      => is_array($data['matriks_nilai'] ?? null) ? count($data['matriks_nilai']) : 0,
                'tanggal_disahkan' => $data['tanggal_disahkan'] ?? $a->created_at->format('d-m-Y'),
                'download_url'     => url("/arsip-dokumen/{$a->id}/download"),
                'cetak_url'        => url("/arsip-dokumen/{$a->id}/cetak"),
            ];
        });

        // 2. Surat Keterangan Kelulusan / SKTB
        $skList = $arsips->where('tipe_dokumen', 'sk_keputusan')->values()->map(function ($a) {
            $data = $a->snapshot_data ?? [];
            return [
                'id'               => $a->id,
                'nama_dokumen'     => 'Surat Keterangan Kelulusan',
                'nomor_dokumen'    => $data['nomor_dokumen'] ?? '-',
                'tahun_pelajaran'  => $data['tahun_pelajaran'] ?? '-',
                'status_keputusan' => $data['status_keputusan'] ?? 'LULUS',
                'tanggal_disahkan' => $data['tanggal_disahkan'] ?? $a->created_at->format('d-m-Y'),
                'download_url'     => url("/arsip-dokumen/{$a->id}/download"),
                'cetak_url'        => url("/arsip-dokumen/{$a->id}/cetak"),
            ];
        });

        // 3. Ijazah Madrasah (Khusus Kelas Akhir)
        $ijazahList = $arsips->where('tipe_dokumen', 'ijazah')->values()->map(function ($a) {
            $data = $a->snapshot_data ?? [];
            return [
                'id'                 => $a->id,
                'nama_dokumen'       => 'Ijazah Madrasah ' . ($data['lulus_dari_tingkat'] ?? ''),
                'nomor_dokumen'      => $data['nomor_dokumen'] ?? '-',
                'tahun_pelajaran'    => $data['tahun_pelajaran'] ?? '-',
                'lulus_dari_tingkat' => $data['lulus_dari_tingkat'] ?? '-',
                'rata_rata'          => (float) ($data['rata_rata'] ?? 0),
                'tanggal_disahkan'   => $data['tanggal_disahkan'] ?? $a->created_at->format('d-m-Y'),
                'download_url'       => url("/arsip-dokumen/{$a->id}/download"),
                'cetak_url'          => url("/arsip-dokumen/{$a->id}/cetak"),
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => [
                'murid' => [
                    'id'             => $murid->id,
                    'nama_lengkap'   => $murid->nama_lengkap,
                    'nism'           => $murid->nism,
                    'ruangan'        => $ruanganAktif?->nama_ruangan ?? '-',
                    'level'          => $levelNama,
                    'is_kelas_akhir' => $isKelasAkhir,
                ],
                'rapor'  => $raporList,
                'sk'     => $skList,
                'ijazah' => $ijazahList,
            ]
        ], 200);
    }
}
