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
     * Detail Profil & Biodata Santri
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
     * Detail Tagihan & Riwayat Pembayaran Santri
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
                'santri' => [
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
     * Rekap Presensi / Kehadiran Santri
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
                'santri' => [
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
     * Catatan Pelanggaran & Poin Santri
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
                'santri' => [
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
}
