<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BulanHijriyah;
use App\Models\Level;
use App\Models\PelanggaranMurid;
use App\Models\PengaturanAkademik;
use App\Models\PresensiMurid;
use App\Models\PresensiUstadz;
use App\Models\Ruangan;
use App\Models\TahunPelajaran;
use App\Models\Ujian\NilaiUjian;
use App\Models\Ujian\RiwayatKenaikan;
use App\Models\Ujian\Ujian;
use App\Models\Ustadz;
use App\Repositories\MuridRuanganRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    protected $muridRuanganRepo;

    public function __construct(MuridRuanganRepository $muridRuanganRepo)
    {
        $this->muridRuanganRepo = $muridRuanganRepo;
    }

    /**
     * Helper to get active academic year and accessible rooms for logged-in Ustadz
     */
    private function getContextRuangans(Request $request)
    {
        $user = $request->user();
        $ustadzId = $user->ustadz->id ?? null;
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();
        $tahunId = $tahunAktif->id ?? 1;

        $accessibleRuangans = Ruangan::with('level')
            ->where('tahun_pelajaran_id', $tahunId)
            ->where('ustadz_id', $ustadzId)
            ->get();

        if ($accessibleRuangans->isEmpty()) {
            $accessibleRuangans = Ruangan::with('level')
                ->where('ustadz_id', $ustadzId)
                ->get();
        }

        if ($request->filled('ruangan_id')) {
            $ruangan = $accessibleRuangans->firstWhere('id', $request->ruangan_id) ?? Ruangan::with('level')->find($request->ruangan_id);
        } else {
            $ruangan = $accessibleRuangans->first();
        }

        if (!$ruangan) {
            $ruangan = Ruangan::with('level')->where('tahun_pelajaran_id', $tahunId)->first() ?? Ruangan::with('level')->first();
        }

        return [$tahunAktif, $tahunId, $accessibleRuangans, $ruangan, $ustadzId];
    }

    // =========================================================================
    // 1. LAPORAN PRESENSI MURID
    // =========================================================================
    public function getLaporanPresensiMurid(Request $request)
    {
        [$tahunAktif, $tahunId, $accessibleRuangans, $ruangan] = $this->getContextRuangans($request);

        if (!$ruangan) {
            return response()->json([
                'success' => false,
                'message' => 'Ruangan tidak ditemukan.'
            ], 404);
        }

        $murids = $this->muridRuanganRepo->getMuridByRuanganAndTahun($ruangan->id, $tahunId, 'Aktif');
        $muridIds = $murids->pluck('id');

        $query = PresensiMurid::whereIn('murid_id', $muridIds);

        // Filter Bulan Hijriyah jika ada
        $bulanHijriyahList = BulanHijriyah::where('tahun_pelajaran_id', $tahunId)
            ->orderBy('urutan', 'asc')
            ->get();

        if ($request->filled('bulan_hijriyah_id')) {
            $bulan = $bulanHijriyahList->firstWhere('id', $request->bulan_hijriyah_id);
            if ($bulan && $bulan->tanggal_mulai_masehi && $bulan->tanggal_selesai_masehi) {
                $query->whereBetween('tanggal', [$bulan->tanggal_mulai_masehi, $bulan->tanggal_selesai_masehi]);
            }
        } elseif ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('semester')) {
            $sem = (string)$request->semester;
            $bulanSem = $bulanHijriyahList->filter(function ($b) use ($sem) {
                return $sem === '1'
                    ? in_array((string)$b->semester, ['1', 'Ganjil', 'Semester 1'])
                    : in_array((string)$b->semester, ['2', 'Genap', 'Semester 2']);
            });
            $tglMulai = $bulanSem->min('tanggal_mulai_masehi');
            $tglSelesai = $bulanSem->max('tanggal_selesai_masehi');
            if ($tglMulai && $tglSelesai) {
                $query->whereBetween('tanggal', [$tglMulai, $tglSelesai]);
            }
        }

        $presensiData = $query->get();

        $totalPertemuan = $presensiData->pluck('tanggal')->unique()->count();
        $totalHadir = $presensiData->where('status', 'Hadir')->count();
        $totalIzin = $presensiData->where('status', 'Izin')->count();
        $totalSakit = $presensiData->where('status', 'Sakit')->count();
        $totalAlpha = $presensiData->where('status', 'Alpha')->count();
        $totalSemua = $totalHadir + $totalIzin + $totalSakit + $totalAlpha;
        $persentaseKelas = $totalSemua > 0 ? round(($totalHadir / $totalSemua) * 100, 1) : 0;

        $rekapMurid = [];
        foreach ($murids as $m) {
            $pMurid = $presensiData->where('murid_id', $m->id);
            $h = $pMurid->where('status', 'Hadir')->count();
            $i = $pMurid->where('status', 'Izin')->count();
            $s = $pMurid->where('status', 'Sakit')->count();
            $a = $pMurid->where('status', 'Alpha')->count();
            $tot = $h + $i + $s + $a;
            $persen = $tot > 0 ? round(($h / $tot) * 100, 1) : 0;

            $predikat = 'Sangat Baik';
            if ($persen < 60 || $a >= 5) {
                $predikat = 'Kurang';
            } elseif ($persen < 75 || $a >= 3) {
                $predikat = 'Cukup';
            } elseif ($persen < 90) {
                $predikat = 'Baik';
            }

            $rekapMurid[] = [
                'murid_id' => $m->id,
                'nama' => $m->nama_lengkap ?? $m->nama,
                'nism' => $m->nism ?? '',
                'jenis_kelamin' => $m->jenis_kelamin ?? 'L',
                'foto' => $m->foto ? asset('storage/' . $m->foto) : null,
                'wali' => $m->nama_ayah ?? $m->waliMurid->nama_kepala_keluarga ?? '-',
                'hadir_count' => $h,
                'izin_count' => $i,
                'sakit_count' => $s,
                'alpha_count' => $a,
                'total_presensi' => $tot,
                'persentase_kehadiran' => $persen,
                'predikat' => $predikat,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'ruangan_id' => $ruangan->id,
                'nama_ruangan' => $ruangan->nama_ruangan,
                'level_nama' => $ruangan->level->nama_level ?? '-',
                'tahun_pelajaran' => $tahunAktif->nama_lengkap ?? ($tahunAktif->nama_masehi ?? 'Tahun Aktif'),
                'total_santri' => $murids->count(),
                'total_hari_efektif' => $totalPertemuan,
                'total_hadir' => $totalHadir,
                'total_izin' => $totalIzin,
                'total_sakit' => $totalSakit,
                'total_alpha' => $totalAlpha,
                'persentase_kehadiran_kelas' => $persentaseKelas,
                'ruangan_list' => $accessibleRuangans->map(fn($r) => [
                    'id' => $r->id,
                    'nama_ruangan' => $r->nama_ruangan,
                    'level_nama' => $r->level->nama_level ?? '-',
                ]),
                'bulan_hijriyah_list' => $bulanHijriyahList->map(fn($b) => [
                    'id' => $b->id,
                    'nama_bulan' => $b->nama_bulan,
                    'tahun_hijriyah' => $b->tahun_hijriyah,
                    'semester' => $b->semester,
                ]),
                'rekap_murid' => $rekapMurid,
            ]
        ], 200);
    }

    // =========================================================================
    // 2. LAPORAN PRESENSI USTADZ
    // =========================================================================
    public function getLaporanPresensiUstadz(Request $request)
    {
        $user = $request->user();
        $currentUstadz = $user->ustadz;
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();
        $tahunId = $tahunAktif->id ?? 1;

        $targetUstadzId = $request->ustadz_id ?? ($currentUstadz->id ?? null);
        $ustadz = Ustadz::find($targetUstadzId) ?? $currentUstadz;

        if (!$ustadz) {
            return response()->json([
                'success' => false,
                'message' => 'Data Ustadz tidak ditemukan.'
            ], 404);
        }

        $bulanList = BulanHijriyah::where('tahun_pelajaran_id', $tahunId)
            ->orderBy('urutan', 'asc')
            ->get();

        $query = PresensiUstadz::where('ustadz_id', $ustadz->id);

        $minDate = $bulanList->min('tanggal_mulai_masehi');
        $maxDate = $bulanList->max('tanggal_selesai_masehi');
        if ($minDate && $maxDate) {
            $query->whereBetween('tanggal', [$minDate, $maxDate]);
        }

        if ($request->filled('bulan_hijriyah_id')) {
            $bulan = $bulanList->firstWhere('id', $request->bulan_hijriyah_id);
            if ($bulan && $bulan->tanggal_mulai_masehi && $bulan->tanggal_selesai_masehi) {
                $query->whereBetween('tanggal', [$bulan->tanggal_mulai_masehi, $bulan->tanggal_selesai_masehi]);
            }
        } elseif ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal', [$request->start_date, $request->end_date]);
        }

        $presensiList = $query->orderBy('tanggal', 'desc')->get();

        $h = $presensiList->where('status', 'Hadir')->count();
        $i = $presensiList->where('status', 'Izin')->count();
        $s = $presensiList->where('status', 'Sakit')->count();
        $t = $presensiList->where('status', 'Tugas')->count();
        $a = $presensiList->where('status', 'Alpha')->count();
        $totalSesi = $presensiList->count();
        $persen = $totalSesi > 0 ? round((($h + $t) / $totalSesi) * 100, 1) : 0;

        $riwayat = $presensiList->map(function ($p) {
            $hariTgl = null;
            try {
                $hariTgl = Carbon::parse($p->tanggal)->locale('id')->isoFormat('dddd, D MMMM YYYY');
            } catch (\Exception $e) {
            }

            return [
                'id' => $p->id,
                'tanggal' => (string)$p->tanggal,
                'hari_tanggal' => $hariTgl,
                'status' => $p->status,
                'jam_masuk' => $p->jam_masuk ? substr($p->jam_masuk, 0, 5) : '-',
                'jam_keluar' => $p->jam_keluar ? substr($p->jam_keluar, 0, 5) : null,
                'keterangan' => $p->keterangan ?? '-',
                'foto' => $p->foto ? asset('storage/' . $p->foto) : null,
            ];
        });

        // Daftar Ustadz untuk switcher (jika pengurus/wali kelas)
        $daftarUstadz = Ustadz::where('is_active', true)->orderBy('nama_lengkap', 'asc')->get()->map(fn($u) => [
            'id' => $u->id,
            'nama' => $u->nama_lengkap,
            'niup' => $u->niup ?? '-',
            'foto' => $u->foto ? asset('storage/' . $u->foto) : null,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'ustadz' => [
                    'id' => $ustadz->id,
                    'nama' => $ustadz->nama_lengkap,
                    'niup' => $ustadz->niup ?? '-',
                    'foto' => $ustadz->foto ? asset('storage/' . $ustadz->foto) : null,
                ],
                'tahun_pelajaran' => $tahunAktif->nama_lengkap ?? ($tahunAktif->nama_masehi ?? 'Tahun Aktif'),
                'total_sesi' => $totalSesi,
                'total_hadir' => $h,
                'total_tugas' => $t,
                'total_izin' => $i,
                'total_sakit' => $s,
                'total_alpha' => $a,
                'persentase_kehadiran' => $persen,
                'daftar_ustadz' => $daftarUstadz,
                'bulan_hijriyah_list' => $bulanList->map(fn($b) => [
                    'id' => $b->id,
                    'nama_bulan' => $b->nama_bulan,
                    'tahun_hijriyah' => $b->tahun_hijriyah,
                ]),
                'riwayat' => $riwayat,
            ]
        ], 200);
    }

    // =========================================================================
    // 3. LAPORAN PELANGGARAN SANTRI (BUKU KASUS KELAS)
    // =========================================================================
    public function getLaporanPelanggaranMurid(Request $request)
    {
        [$tahunAktif, $tahunId, $accessibleRuangans, $ruangan] = $this->getContextRuangans($request);

        if (!$ruangan) {
            return response()->json([
                'success' => false,
                'message' => 'Ruangan tidak ditemukan.'
            ], 404);
        }

        $murids = $this->muridRuanganRepo->getMuridByRuanganAndTahun($ruangan->id, $tahunId, 'Aktif');
        $muridIds = $murids->pluck('id');

        $pelanggaranQuery = PelanggaranMurid::with(['murid', 'referensiPelanggaran', 'penginput'])
            ->where('ruangan_id', $ruangan->id)
            ->where('tahun_pelajaran_id', $tahunId);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $pelanggaranQuery->whereBetween('tanggal', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('kategori')) {
            $kategori = $request->kategori;
            $pelanggaranQuery->whereHas('referensiPelanggaran', function ($q) use ($kategori) {
                $q->where('kategori', $kategori);
            });
        }

        $pelanggaranList = $pelanggaranQuery->orderBy('tanggal', 'desc')->get();

        $totalKasus = $pelanggaranList->count();
        $totalPoin = $pelanggaranList->sum(fn($p) => (float)($p->referensiPelanggaran->poin ?? 0));
        $kasusSelesai = $totalKasus;
        $kasusDiproses = 0;

        // Breakdown per Kategori
        $kasusRingan = $pelanggaranList->where('referensiPelanggaran.kategori', 'Ringan')->count();
        $kasusSedang = $pelanggaranList->where('referensiPelanggaran.kategori', 'Sedang')->count();
        $kasusBerat = $pelanggaranList->where('referensiPelanggaran.kategori', 'Berat')->count();

        // Rekap Poin per Santri (Leaderboard Pelanggaran)
        $rekapSantri = [];
        foreach ($murids as $m) {
            $pSantri = $pelanggaranList->where('murid_id', $m->id);
            $poinSantri = $pSantri->sum(fn($p) => (float)($p->referensiPelanggaran->poin ?? 0));
            $jmlKasus = $pSantri->count();

            $rekapSantri[] = [
                'murid_id' => $m->id,
                'nama' => $m->nama_lengkap ?? $m->nama,
                'nism' => $m->nism ?? '',
                'jenis_kelamin' => $m->jenis_kelamin ?? 'L',
                'foto' => $m->foto ? asset('storage/' . $m->foto) : null,
                'wali' => $m->nama_ayah ?? $m->waliMurid->nama_kepala_keluarga ?? '-',
                'total_kasus' => $jmlKasus,
                'total_poin' => (float)round($poinSantri, 2),
                'status_kedisiplinan' => $poinSantri == 0 ? 'Disiplin' : ($poinSantri <= 10 ? 'Perhatian' : ($poinSantri <= 25 ? 'Peringatan' : 'Kritis')),
            ];
        }

        // Urutkan santri berdasarkan poin tertinggi
        usort($rekapSantri, fn($a, $b) => $b['total_poin'] <=> $a['total_poin']);

        $riwayatLog = $pelanggaranList->map(function ($p) {
            $hariTgl = null;
            try {
                $hariTgl = Carbon::parse($p->tanggal)->locale('id')->isoFormat('dddd, D MMMM YYYY');
            } catch (\Exception $e) {
            }

            return [
                'id' => $p->id,
                'murid_id' => $p->murid_id,
                'nama_santri' => $p->murid->nama_lengkap ?? '-',
                'nism' => $p->murid->nism ?? '-',
                'tanggal' => (string)$p->tanggal,
                'hari_tanggal' => $hariTgl,
                'nama_pelanggaran' => $p->referensiPelanggaran->nama_pelanggaran ?? '-',
                'kategori' => $p->referensiPelanggaran->kategori ?? 'Ringan',
                'poin' => (float)($p->referensiPelanggaran->poin ?? 0),
                'keterangan' => $p->keterangan ?? '-',
                'pencatat' => $p->penginput->name ?? 'Ustadz',
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'ruangan_id' => $ruangan->id,
                'nama_ruangan' => $ruangan->nama_ruangan,
                'level_nama' => $ruangan->level->nama_level ?? '-',
                'tahun_pelajaran' => $tahunAktif->nama_lengkap ?? ($tahunAktif->nama_masehi ?? 'Tahun Aktif'),
                'total_santri' => $murids->count(),
                'total_kasus' => $totalKasus,
                'total_poin' => (float)round($totalPoin, 2),
                'kasus_selesai' => $kasusSelesai,
                'kasus_diproses' => $kasusDiproses,
                'kasus_ringan' => $kasusRingan,
                'kasus_sedang' => $kasusSedang,
                'kasus_berat' => $kasusBerat,
                'ruangan_list' => $accessibleRuangans->map(fn($r) => [
                    'id' => $r->id,
                    'nama_ruangan' => $r->nama_ruangan,
                    'level_nama' => $r->level->nama_level ?? '-',
                ]),
                'rekap_santri' => $rekapSantri,
                'riwayat_log' => $riwayatLog,
            ]
        ], 200);
    }

    // =========================================================================
    // 4. LAPORAN NILAI & UJIAN SANTRI
    // =========================================================================
    public function getLaporanUjian(Request $request)
    {
        [$tahunAktif, $tahunId, $accessibleRuangans, $ruangan] = $this->getContextRuangans($request);

        if (!$ruangan) {
            return response()->json([
                'success' => false,
                'message' => 'Ruangan tidak ditemukan.'
            ], 404);
        }

        $daftarUjian = Ujian::where('tahun_pelajaran_id', $tahunId)
            ->orderBy('id', 'desc')
            ->get();

        $selectedUjianId = $request->ujian_id ?? ($daftarUjian->first()->id ?? null);
        $ujian = $daftarUjian->firstWhere('id', $selectedUjianId) ?? $daftarUjian->first();

        if (!$ujian) {
            return response()->json([
                'success' => false,
                'message' => 'Data Ujian tidak ditemukan pada tahun pelajaran ini.'
            ], 404);
        }

        $murids = $this->muridRuanganRepo->getMuridByRuanganAndTahun($ruangan->id, $tahunId, 'Aktif');
        $muridIds = $murids->pluck('id');

        $nilaiList = NilaiUjian::with('mataPelajaran')
            ->where('ruangan_id', $ruangan->id)
            ->where('ujian_id', $ujian->id)
            ->get();

        $mapelList = $nilaiList->pluck('mataPelajaran')->filter()->unique('id')->values();

        $rekapMurid = [];
        $semuaRataRata = [];

        foreach ($murids as $m) {
            $nMurid = $nilaiList->where('murid_id', $m->id);
            $totalNilai = $nMurid->sum('nilai');
            $mapelCount = $nMurid->count();
            $rata = $mapelCount > 0 ? round($totalNilai / $mapelCount, 2) : 0;
            $semuaRataRata[] = $rata;

            $mapelNilai = [];
            foreach ($mapelList as $mpl) {
                $item = $nMurid->firstWhere('mata_pelajaran_id', $mpl->id);
                $mapelNilai[] = [
                    'mapel_id' => $mpl->id,
                    'nama_mapel' => $mpl->nama_mapel,
                    'nilai' => $item ? (float)$item->nilai : 0,
                ];
            }

            $rekapMurid[] = [
                'murid_id' => $m->id,
                'nama' => $m->nama_lengkap ?? $m->nama,
                'nism' => $m->nism ?? '',
                'jenis_kelamin' => $m->jenis_kelamin ?? 'L',
                'foto' => $m->foto ? asset('storage/' . $m->foto) : null,
                'wali' => $m->nama_ayah ?? $m->waliMurid->nama_kepala_keluarga ?? '-',
                'total_nilai' => (float)round($totalNilai, 2),
                'rata_rata' => $rata,
                'jumlah_mapel_diikuti' => $mapelCount,
                'status_tuntas' => $rata >= 60 ? 'Tuntas' : 'Belum Tuntas',
                'mapel_nilai' => $mapelNilai,
            ];
        }

        // Urutkan untuk menentukan Peringkat Bintang Pelajar
        usort($rekapMurid, fn($a, $b) => $b['rata_rata'] <=> $a['rata_rata']);
        foreach ($rekapMurid as $idx => &$item) {
            $item['ranking'] = $idx + 1;
        }
        unset($item);

        $rataKelas = count($semuaRataRata) > 0 ? round(array_sum($semuaRataRata) / count($semuaRataRata), 2) : 0;
        $nilaiTertinggi = count($semuaRataRata) > 0 ? max($semuaRataRata) : 0;
        $nilaiTerendah = count($semuaRataRata) > 0 ? min($semuaRataRata) : 0;
        $tuntasCount = count(array_filter($semuaRataRata, fn($r) => $r >= 60));
        $persenTuntas = count($semuaRataRata) > 0 ? round(($tuntasCount / count($semuaRataRata)) * 100, 1) : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'ruangan_id' => $ruangan->id,
                'nama_ruangan' => $ruangan->nama_ruangan,
                'level_nama' => $ruangan->level->nama_level ?? '-',
                'ujian' => [
                    'id' => $ujian->id,
                    'nama_ujian' => $ujian->nama_ujian,
                    'tipe_ujian' => $ujian->tipe_ujian,
                    'semester' => $ujian->semester ?? '-',
                ],
                'tahun_pelajaran' => $tahunAktif->nama_lengkap ?? ($tahunAktif->nama_masehi ?? 'Tahun Aktif'),
                'total_santri' => $murids->count(),
                'rata_rata_kelas' => $rataKelas,
                'nilai_tertinggi' => $nilaiTertinggi,
                'nilai_terendah' => $nilaiTerendah,
                'persentase_tuntas' => $persenTuntas,
                'jumlah_tuntas' => $tuntasCount,
                'jumlah_belum_tuntas' => count($semuaRataRata) - $tuntasCount,
                'daftar_ujian' => $daftarUjian->map(fn($u) => [
                    'id' => $u->id,
                    'nama_ujian' => $u->nama_ujian,
                    'tipe_ujian' => $u->tipe_ujian,
                ]),
                'ruangan_list' => $accessibleRuangans->map(fn($r) => [
                    'id' => $r->id,
                    'nama_ruangan' => $r->nama_ruangan,
                    'level_nama' => $r->level->nama_level ?? '-',
                ]),
                'mapel_header' => $mapelList->map(fn($m) => [
                    'id' => $m->id,
                    'nama_mapel' => $m->nama_mapel,
                ]),
                'rekap_murid' => $rekapMurid,
            ]
        ], 200);
    }

    // =========================================================================
    // 5. LAPORAN KENAIKAN KELAS & KELULUSAN
    // =========================================================================
    public function getLaporanKenaikanKelas(Request $request)
    {
        [$tahunAktif, $tahunId, $accessibleRuangans, $ruangan] = $this->getContextRuangans($request);

        if (!$ruangan) {
            return response()->json([
                'success' => false,
                'message' => 'Ruangan tidak ditemukan.'
            ], 404);
        }

        $levelNama = $ruangan->level->nama_level ?? '';
        $isKelasAkhir = in_array($levelNama, ['3 TPQ', '6 IBT', '3 TSA']);

        $config = PengaturanAkademik::where('tahun_pelajaran_id', $tahunId)->first();
        $bobotUjian = ($config->bobot_imda ?? 60) / 100;
        $bobotHadir = ($config->bobot_presensi ?? 24) / 100;
        $bobotPelanggaran = ($config->bobot_pelanggaran ?? 16) / 100;
        $tarifAlpha = (float)($config->poin_alpha ?? 1.00);
        $tarifIzin = (float)($config->poin_izin ?? 0.16);

        $daftarLevel = Level::where('id', '>=', $ruangan->level_id ?? 1)->orderBy('id', 'asc')->get();
        $levelNaik = $daftarLevel->count() > 1 ? $daftarLevel[1] : $ruangan->level;

        // Ujian IDs
        $semuaUjianTahunIni = Ujian::where('tahun_pelajaran_id', $tahunId)->get();
        $idDauri1 = $semuaUjianTahunIni->where('tipe_ujian', 'IMDA 1')->pluck('id')->toArray();
        $idUjianSem2 = $isKelasAkhir
            ? $semuaUjianTahunIni->where('tipe_ujian', 'IMNI')->pluck('id')->toArray()
            : $semuaUjianTahunIni->where('tipe_ujian', 'IMDA 2')->pluck('id')->toArray();

        $semuaBulanHijriyah = BulanHijriyah::where('tahun_pelajaran_id', $tahunId)->get();

        $murids = $this->muridRuanganRepo->getMuridByRuanganAndTahun($ruangan->id, $tahunId, 'Aktif');
        $muridIds = $murids->pluck('id');

        $semuaPresensi = PresensiMurid::whereIn('murid_id', $muridIds)->get();
        $semuaNilai = NilaiUjian::whereIn('murid_id', $muridIds)->where('ruangan_id', $ruangan->id)->get();
        $semuaPelanggaran = PelanggaranMurid::with('referensiPelanggaran')
            ->where('tahun_pelajaran_id', $tahunId)
            ->where('ruangan_id', $ruangan->id)
            ->get();

        $riwayatExisting = RiwayatKenaikan::where('tahun_pelajaran_id', $tahunId)
            ->whereIn('murid_id', $muridIds)
            ->get()
            ->keyBy('murid_id');

        $dataMurid = [];
        $countNaik = 0;
        $countTinggal = 0;
        $countLulus = 0;

        foreach ($murids as $murid) {
            $nilaiMurid = $semuaNilai->where('murid_id', $murid->id);
            $pelanggaranMuridIni = $semuaPelanggaran->where('murid_id', $murid->id);
            $presensiMuridIni = $semuaPresensi->where('murid_id', $murid->id);

            // Semester 1
            $rataUjian1 = $nilaiMurid->whereIn('ujian_id', $idDauri1)->avg('nilai') ?? 0;
            $presensiSem1 = $presensiMuridIni->filter(function ($p) use ($semuaBulanHijriyah) {
                $bulan = $semuaBulanHijriyah->first(fn($b) => $p->tanggal >= $b->tanggal_mulai_masehi && $p->tanggal <= $b->tanggal_selesai_masehi);
                return $bulan && in_array((string)$bulan->semester, ['1', 'Ganjil', 'Semester 1']);
            });
            $poinKehadiran1 = ($presensiSem1->where('status', 'Alpha')->count() * $tarifAlpha) + ($presensiSem1->where('status', 'Izin')->count() * $tarifIzin);
            $poinPelanggaran1 = $pelanggaranMuridIni->filter(function ($p) use ($semuaBulanHijriyah) {
                $bulan = $semuaBulanHijriyah->first(fn($b) => $p->tanggal >= $b->tanggal_mulai_masehi && $p->tanggal <= $b->tanggal_selesai_masehi);
                return $bulan && in_array((string)$bulan->semester, ['1', 'Ganjil', 'Semester 1']);
            })->sum(fn($p) => (float)($p->referensiPelanggaran->poin ?? 0));

            $nilaiHadir1 = max(0, ((15 - $poinKehadiran1) / 15) * 100);
            $nilaiPelanggaran1 = max(0, ((30 - $poinPelanggaran1) / 30) * 100);
            $skorSem1 = ($rataUjian1 * $bobotUjian) + ($nilaiHadir1 * $bobotHadir) + ($nilaiPelanggaran1 * $bobotPelanggaran);

            // Semester 2
            $rataUjian2 = $nilaiMurid->whereIn('ujian_id', $idUjianSem2)->avg('nilai') ?? 0;
            $presensiSem2 = $presensiMuridIni->filter(function ($p) use ($semuaBulanHijriyah) {
                $bulan = $semuaBulanHijriyah->first(fn($b) => $p->tanggal >= $b->tanggal_mulai_masehi && $p->tanggal <= $b->tanggal_selesai_masehi);
                return $bulan && in_array((string)$bulan->semester, ['2', 'Genap', 'Semester 2']);
            });
            $poinKehadiran2 = ($presensiSem2->where('status', 'Alpha')->count() * $tarifAlpha) + ($presensiSem2->where('status', 'Izin')->count() * $tarifIzin);
            $poinPelanggaran2 = $pelanggaranMuridIni->filter(function ($p) use ($semuaBulanHijriyah) {
                $bulan = $semuaBulanHijriyah->first(fn($b) => $p->tanggal >= $b->tanggal_mulai_masehi && $p->tanggal <= $b->tanggal_selesai_masehi);
                return $bulan && in_array((string)$bulan->semester, ['2', 'Genap', 'Semester 2']);
            })->sum(fn($p) => (float)($p->referensiPelanggaran->poin ?? 0));

            $nilaiHadir2 = max(0, ((15 - $poinKehadiran2) / 15) * 100);
            $nilaiPelanggaran2 = max(0, ((30 - $poinPelanggaran2) / 30) * 100);
            $skorSem2 = ($rataUjian2 * $bobotUjian) + ($nilaiHadir2 * $bobotHadir) + ($nilaiPelanggaran2 * $bobotPelanggaran);

            // Akumulasi Final
            $nilaiAkhir = round(($skorSem1 + $skorSem2) / 2, 2);
            $rekomendasi = 'Tinggal Kelas';
            if ($nilaiAkhir > 55) {
                $rekomendasi = $isKelasAkhir ? 'Lulus' : 'Naik Kelas';
            }

            $riwayat = $riwayatExisting->get($murid->id);
            $keputusanFinal = $riwayat ? $riwayat->status_keputusan : $rekomendasi;

            if ($keputusanFinal === 'Naik Kelas') {
                $countNaik++;
            } elseif ($keputusanFinal === 'Lulus') {
                $countLulus++;
            } else {
                $countTinggal++;
            }

            $dataMurid[] = [
                'murid_id' => $murid->id,
                'nama' => $murid->nama_lengkap ?? $murid->nama,
                'nism' => $murid->nism ?? '',
                'jenis_kelamin' => $murid->jenis_kelamin ?? 'L',
                'foto' => $murid->foto ? asset('storage/' . $murid->foto) : null,
                'wali' => $murid->nama_ayah ?? $murid->waliMurid->nama_kepala_keluarga ?? '-',
                'skor_sem1' => round($skorSem1, 2),
                'skor_sem2' => round($skorSem2, 2),
                'nilai_akumulasi' => $nilaiAkhir,
                'rekomendasi' => $rekomendasi,
                'keputusan_final' => $keputusanFinal,
                'level_tujuan_nama' => $keputusanFinal === 'Naik Kelas' ? ($levelNaik->nama_level ?? '-') : ($keputusanFinal === 'Lulus' ? 'Alumni (Lulus)' : ($ruangan->level->nama_level ?? '-')),
                'catatan' => $riwayat ? ($riwayat->catatan_wali_kelas ?? '') : '',
                'sudah_dikunci' => (bool)$riwayat,
                'detail_perhitungan' => [
                    'bobot_ujian' => (int)($bobotUjian * 100),
                    'bobot_presensi' => (int)($bobotHadir * 100),
                    'bobot_pelanggaran' => (int)($bobotPelanggaran * 100),
                    'rata_ujian_sem1' => round($rataUjian1, 2),
                    'rata_ujian_sem2' => round($rataUjian2, 2),
                    'poin_pelanggaran_sem1' => round($poinPelanggaran1, 2),
                    'poin_pelanggaran_sem2' => round($poinPelanggaran2, 2),
                ]
            ];
        }

        // Urutkan nilai akumulasi tertinggi
        usort($dataMurid, fn($a, $b) => $b['nilai_akumulasi'] <=> $a['nilai_akumulasi']);

        return response()->json([
            'success' => true,
            'data' => [
                'ruangan_id' => $ruangan->id,
                'nama_ruangan' => $ruangan->nama_ruangan,
                'level_nama' => $ruangan->level->nama_level ?? '-',
                'is_kelas_akhir' => $isKelasAkhir,
                'tahun_pelajaran' => $tahunAktif->nama_lengkap ?? ($tahunAktif->nama_masehi ?? 'Tahun Aktif'),
                'total_santri' => $murids->count(),
                'total_naik_kelas' => $countNaik,
                'total_lulus' => $countLulus,
                'total_tinggal_kelas' => $countTinggal,
                'ruangan_list' => $accessibleRuangans->map(fn($r) => [
                    'id' => $r->id,
                    'nama_ruangan' => $r->nama_ruangan,
                    'level_nama' => $r->level->nama_level ?? '-',
                ]),
                'bobot_konfigurasi' => [
                    'bobot_ujian' => (int)($bobotUjian * 100),
                    'bobot_presensi' => (int)($bobotHadir * 100),
                    'bobot_pelanggaran' => (int)($bobotPelanggaran * 100),
                ],
                'data_kenaikan' => $dataMurid,
            ]
        ], 200);
    }
}
