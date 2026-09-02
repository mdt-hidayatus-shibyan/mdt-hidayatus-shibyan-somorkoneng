<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HariLibur;
use App\Models\JadwalPelajaran;
use App\Models\KalendarPendidikan;
use App\Models\Pengumuman;
use App\Models\PresensiMurid;
use App\Models\Ruangan;
use App\Models\TahunPelajaran;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $ustadz = $user->ustadz;
        $ustadzId = $ustadz->id ?? null;

        $tahunAktif = TahunPelajaran::where('is_active', true)->first();
        $todayDate = date('Y-m-d');

        $mapHari = [
            'Sunday'    => 'Ahad',
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu'
        ];
        $hariIni = $mapHari[Carbon::now()->format('l')];

        // Total Jadwal Mingguan
        $totalJadwalMingguan = $ustadzId
            ? JadwalPelajaran::where('ustadz_id', $ustadzId)->count()
            : 0;

        // Cek Libur Hari Ini
        $libur = HariLibur::where('tanggal_mulai', '<=', $todayDate)
            ->where('tanggal_selesai', '>=', $todayDate)
            ->first();

        $isLiburHariIni = ($libur != null) || ($hariIni === 'Jumat');
        $keteranganLiburHariIni = $libur ? $libur->keterangan : ($hariIni === 'Jumat' ? 'Libur Rutin Mingguan (Hari Jumat)' : null);

        // Jadwal Hari Ini (Hanya jika bukan hari libur)
        $jadwalHariIniList = collect();
        $jadwalHariIniCount = 0;
        $presensiSelesaiCount = 0;
        $formattedJadwal = collect();

        if (!$isLiburHariIni) {
            $jadwalHariIniQuery = JadwalPelajaran::with(['mataPelajaran', 'ruangan.level'])
                ->where('hari', $hariIni);

            if ($ustadzId) {
                $jadwalHariIniQuery->where('ustadz_id', $ustadzId);
            }

            $jadwalHariIniList = $jadwalHariIniQuery->orderBy('jam_ke')->get();
            $jadwalHariIniCount = $jadwalHariIniList->count();

            $formattedJadwal = $jadwalHariIniList->map(function ($j) use ($todayDate, &$presensiSelesaiCount) {
                $sudahAbsen = PresensiMurid::where('jadwal_pelajaran_id', $j->id)
                    ->where('tanggal', $todayDate)
                    ->exists();

                if ($sudahAbsen) {
                    $presensiSelesaiCount++;
                }

                $jamText = match ($j->jam_ke) {
                    'Nadzoman' => '13:45 - 14:00 WIB',
                    '1' => '14:00 - 14:45 WIB',
                    '2' => '15:30 - 16:15 WIB',
                    'Ekstra' => '20:00 - 21:00 WIB',
                    default => 'Jam Ke-' . $j->jam_ke,
                };

                return [
                    'id' => $j->id,
                    'jam_ke' => $j->jam_ke,
                    'jam' => $jamText,
                    'mapel' => $j->mataPelajaran->nama_mapel ?? 'Pelajaran',
                    'kelas' => $j->ruangan->nama_ruangan ?? '-',
                    'sudah_absen' => $sudahAbsen,
                ];
            });
        }

        // Total Murid Wali
        $totalMuridWali = 0;
        if ($tahunAktif && $ustadzId) {
            $ruanganWali = Ruangan::where('tahun_pelajaran_id', $tahunAktif->id)
                ->where('ustadz_id', $ustadzId)
                ->first();

            if ($ruanganWali) {
                $totalMuridWali = $ruanganWali->murids()->count();
            }
        }

        // Pengumuman Aktif
        $pengumuman = Pengumuman::where('is_published', true)
            ->where(function ($q) {
                $q->whereNull('tanggal_kadaluarsa')
                    ->orWhere('tanggal_kadaluarsa', '>=', date('Y-m-d'));
            })
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'judul' => $p->judul,
                    'konten' => strip_tags($p->konten ?? $p->isi ?? ''),
                    'tipe' => $p->kategori ?? 'Info',
                    'tanggal_mulai' => $p->created_at ? $p->created_at->format('Y-m-d') : date('Y-m-d'),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'is_libur_hari_ini' => $isLiburHariIni,
                'keterangan_libur_hari_ini' => $keteranganLiburHariIni,
                'statistik' => [
                    'total_jadwal_mingguan' => $totalJadwalMingguan,
                    'jadwal_hari_ini' => $jadwalHariIniCount,
                    'presensi_selesai_hari_ini' => $presensiSelesaiCount,
                    'total_murid_wali' => $totalMuridWali,
                ],
                'jadwal_hari_ini' => $formattedJadwal,
                'pengumuman' => $pengumuman,
            ]
        ], 200);
    }

    public function pengumuman()
    {
        $pengumuman = Pengumuman::where('is_published', true)
            ->latest()
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'judul' => $p->judul,
                    'konten' => strip_tags($p->konten ?? $p->isi ?? ''),
                    'tipe' => $p->kategori ?? 'Info',
                    'tanggal_mulai' => $p->created_at ? $p->created_at->format('Y-m-d') : date('Y-m-d'),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $pengumuman
        ], 200);
    }

    public function kalendar()
    {
        $kalendar = KalendarPendidikan::orderBy('tanggal_mulai')->get();
        return response()->json([
            'success' => true,
            'data' => $kalendar
        ], 200);
    }
}
