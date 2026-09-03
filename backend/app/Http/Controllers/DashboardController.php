<?php

namespace App\Http\Controllers;

use App\Models\KalendarPendidikan;
use App\Models\KasRuangan\SetoranKasRuangan;
use App\Models\Murid;
use App\Models\PelanggaranMurid;
use App\Models\Pengumuman;
use App\Models\PresensiMurid;
use App\Models\Ruangan;
use App\Models\TagihanMurid;
use App\Models\TahunPelajaran;
use App\Models\Ustadz;
use App\Models\WaliMurid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $tahunPelajarans = TahunPelajaran::orderBy('id', 'asc')->get();
        $tahunAktif = TahunPelajaran::tahunAktif()->first();
        $selectedTahunId = $request->tahun_pelajaran_id ?? ($tahunAktif ? $tahunAktif->id : null);

        // 1. STATISTIK KESISWAAN & CIVITAS
        $queryMuridAktif = Murid::where('status', 'Aktif');
        $totalMurid = (clone $queryMuridAktif)->count();
        $totalLaki = (clone $queryMuridAktif)->where('jenis_kelamin', 'L')->count();
        $totalPerempuan = (clone $queryMuridAktif)->where('jenis_kelamin', 'P')->count();

        $totalWaliMurid = WaliMurid::where('is_active', 1)->count();
        $totalUstadz = Ustadz::count();
        $totalRombel = Ruangan::when($selectedTahunId, function ($q) use ($selectedTahunId) {
            $q->where('tahun_pelajaran_id', $selectedTahunId);
        })->count();

        // 2. DISTRIBUSI PER LEVEL
        // $muridPerLevel = DB::table('murids')
        //     ->join('levels', 'murids.level_masuk', '=', 'levels.id')
        //     ->where('murids.status', 'Aktif')
        //     ->select(
        //         'levels.nama_level',
        //         DB::raw('COUNT(*) as total'),
        //         DB::raw('SUM(CASE WHEN murids.jenis_kelamin = "L" THEN 1 ELSE 0 END) as total_l'),
        //         DB::raw('SUM(CASE WHEN murids.jenis_kelamin = "P" THEN 1 ELSE 0 END) as total_p')
        //     )
        //     ->groupBy('levels.id', 'levels.nama_level')
        //     ->get();

        $muridPerLevel = DB::table('murid_ruangans')
            ->join('murids', 'murid_ruangans.murid_id', '=', 'murids.id')
            ->join('ruangans', 'murid_ruangans.ruangan_id', '=', 'ruangans.id')
            ->join('levels', 'ruangans.level_id', '=', 'levels.id')
            ->join('tahun_pelajarans', 'murid_ruangans.tahun_pelajaran_id', '=', 'tahun_pelajarans.id')

            ->where('murids.status', 'Aktif')
            ->where('tahun_pelajarans.id', $selectedTahunId)
            ->select(
                'levels.nama_level',
                DB::raw('COUNT(murids.id) as total'),
                DB::raw('SUM(CASE WHEN murids.jenis_kelamin = "L" THEN 1 ELSE 0 END) as total_l'),
                DB::raw('SUM(CASE WHEN murids.jenis_kelamin = "P" THEN 1 ELSE 0 END) as total_p')
            )
            ->groupBy('levels.id', 'levels.nama_level')
            ->orderBy('levels.urutan_level', 'asc')
            ->get();

        $muridPerRuangan = DB::table('murid_ruangans')
            ->join('murids', 'murid_ruangans.murid_id', '=', 'murids.id')
            ->join('ruangans', 'murid_ruangans.ruangan_id', '=', 'ruangans.id')
            ->join('tahun_pelajarans', 'ruangans.tahun_pelajaran_id', '=', 'tahun_pelajarans.id')
            ->join('levels', 'ruangans.level_id', '=', 'levels.id')
            ->where('murids.status', 'Aktif')
            ->where('tahun_pelajarans.id', $selectedTahunId)
            ->select(
                'ruangans.nama_ruangan',
                DB::raw('COUNT(murids.id) as total'),
                DB::raw('SUM(CASE WHEN murids.jenis_kelamin = "L" THEN 1 ELSE 0 END) as total_l'),
                DB::raw('SUM(CASE WHEN murids.jenis_kelamin = "P" THEN 1 ELSE 0 END) as total_p')
            )
            ->groupBy('ruangans.id', 'ruangans.nama_ruangan')
            ->orderBy('levels.urutan_level', 'asc')
            ->orderBy('ruangans.id', 'asc') // Opsional: urutkan abjad
            ->get();

        // 3. STATISTIK KEUANGAN & TAGIHAN
        $queryTagihan = TagihanMurid::query();
        if ($selectedTahunId) {
            $queryTagihan->whereHas('ruangan', function ($q) use ($selectedTahunId) {
                $q->where('tahun_pelajaran_id', $selectedTahunId);
            });
        }
        $totalNominalTagihan = (clone $queryTagihan)->sum('nominal_tagihan');
        $totalNominalLunas = (clone $queryTagihan)->where('status_bayar', 'Lunas')->sum('nominal_tagihan');
        $totalTagihanBelumLunas = (clone $queryTagihan)->where('status_bayar', 'Belum Lunas')->count();
        $totalTagihanLunasCount = (clone $queryTagihan)->where('status_bayar', 'Lunas')->count();
        $totalTagihanDonaturBebas = (clone $queryTagihan)->whereIn('status_bayar', ['Bebas/Gratis', 'Ditanggung Donatur'])->count();

        $persenLunas = $totalNominalTagihan > 0 ? round(($totalNominalLunas / $totalNominalTagihan) * 100, 1) : 0;

        // 4. STATISTIK KAS RUANGAN
        $totalSetoranKas = SetoranKasRuangan::when($selectedTahunId, function ($q) use ($selectedTahunId) {
            $q->whereHas('ruangan', function ($rq) use ($selectedTahunId) {
                $rq->where('tahun_pelajaran_id', $selectedTahunId);
            });
        })->sum('jumlah_setor');

        // 5. STATISTIK PRESENSI MURID
        $queryPresensi = PresensiMurid::query();
        if ($selectedTahunId) {
            $queryPresensi->whereHas('jadwalPelajaran.ruangan', function ($q) use ($selectedTahunId) {
                $q->where('tahun_pelajaran_id', $selectedTahunId);
            });
        }
        $presensiHadir = (clone $queryPresensi)->where('status', 'Hadir')->count();
        $presensiSakit = (clone $queryPresensi)->where('status', 'Sakit')->count();
        $presensiIzin = (clone $queryPresensi)->where('status', 'Izin')->count();
        $presensiAlpha = (clone $queryPresensi)->where('status', 'Alpha')->count();

        // 6. OPERATIONAL FEEDS: PENGUMUMAN, KALENDER, PELANGGARAN
        $pengumumans = Pengumuman::whereIn('status', ['Terbit', 'Published'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $agendaKalender = KalendarPendidikan::with('kategoriKegiatan')
            ->when($selectedTahunId, function ($q) use ($selectedTahunId) {
                $q->where('tahun_pelajaran_id', $selectedTahunId);
            })
            ->orderBy('tanggal_mulai', 'asc')
            ->where('tanggal_selesai', '>=', now()->toDateString())
            ->take(5)
            ->get();

        if ($agendaKalender->isEmpty()) {
            $agendaKalender = KalendarPendidikan::with('kategoriKegiatan')
                ->when($selectedTahunId, function ($q) use ($selectedTahunId) {
                    $q->where('tahun_pelajaran_id', $selectedTahunId);
                })
                ->orderBy('tanggal_mulai', 'desc')
                ->take(5)
                ->get();
        }

        $pelanggaranTerbaru = PelanggaranMurid::with(['murid', 'referensiPelanggaran', 'ruangan'])
            ->when($selectedTahunId, function ($q) use ($selectedTahunId) {
                $q->where('tahun_pelajaran_id', $selectedTahunId);
            })
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'tahunPelajarans',
            'selectedTahunId',
            'totalMurid',
            'totalLaki',
            'totalPerempuan',
            'totalWaliMurid',
            'totalUstadz',
            'totalRombel',
            'muridPerLevel',
            'muridPerRuangan',
            'totalNominalTagihan',
            'totalNominalLunas',
            'totalTagihanBelumLunas',
            'totalTagihanLunasCount',
            'totalTagihanDonaturBebas',
            'persenLunas',
            'totalSetoranKas',
            'presensiHadir',
            'presensiSakit',
            'presensiIzin',
            'presensiAlpha',
            'pengumumans',
            'agendaKalender',
            'pelanggaranTerbaru'
        ));
    }
}
