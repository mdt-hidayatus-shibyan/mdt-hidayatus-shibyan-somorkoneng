<?php

namespace App\Http\Controllers;

use App\Models\BulanHijriyah;
use App\Models\JadwalPelajaran;
use App\Models\PengaturanAkademik;
use App\Models\PresensiMurid;
use App\Models\Ruangan;
use App\Models\Semester;
use App\Repositories\MuridRuanganRepository;
use App\Services\PresensiMuridService;
use Illuminate\Http\Request;


class PresensiMuridController extends Controller
{
    protected $muridRuanganRepo;
    protected $presensiService;

    public function __construct(MuridRuanganRepository $muridRuanganRepo, PresensiMuridService $presensiService)
    {
        $this->muridRuanganRepo = $muridRuanganRepo;
        $this->presensiService = $presensiService;
    }

    public function index(Request $request)
    {
        // Ambil data master untuk Dropdown
        $ruangans = Ruangan::with('level')->berdasarkanHakAkses()->orderBy('level_id')->get();
        $jamList = ['Nadzoman', '1', '2', 'Ekstra'];

        // Tangkap input pencarian dari Admin
        $tanggal = $request->tanggal ?? date('Y-m-d');
        $ruangan_id = $request->ruangan_id;
        $jam_ke = $request->jam_ke;

        $jadwal = null;
        $murids = collect();
        $presensiTersimpan = collect();

        // Variabel penanda libur
        $isLibur = false;
        $keteranganLibur = null;
        $hari_ini = null;

        // Jika Admin sudah memilih Ruangan dan Jam, kita cari data murid & jadwalnya
        if ($ruangan_id && $jam_ke) {

            // ==========================================================
            // PENERJEMAH HARI: Memastikan "Minggu" atau "Sunday" menjadi "Ahad"
            // ==========================================================
            $nama_hari_inggris = \Carbon\Carbon::parse($tanggal)->format('l'); // Menghasilkan: Sunday, Monday, dll

            $mapHari = [
                'Sunday'    => 'Ahad',
                'Monday'    => 'Senin',
                'Tuesday'   => 'Selasa',
                'Wednesday' => 'Rabu',
                'Thursday'  => 'Kamis',
                'Friday'    => 'Jumat',
                'Saturday'  => 'Sabtu'
            ];

            // Timpa variabel $hari_ini dengan hasil terjemahan yang benar
            $hari_ini = $mapHari[$nama_hari_inggris];
            // ==========================================================

            // ==========================================================
            // CEK HARI LIBUR & JUMAT
            // ==========================================================
            $libur = \App\Models\HariLibur::where('tanggal_mulai', '<=', $tanggal)
                ->where('tanggal_selesai', '>=', $tanggal)
                ->first();

            if ($libur) {
                // Jika masuk rentang kalender libur madrasah
                $isLibur = true;
                $keteranganLibur = $libur->keterangan;
            } elseif ($hari_ini === 'Jumat') {
                // Jika hari Jumat (Libur rutin madrasah)
                $isLibur = true;
                $keteranganLibur = 'Libur Rutin (Jumat)';
            }
            // ==========================================================


            // JIKA TIDAK LIBUR, BARU EKSEKUSI PENCARIAN JADWAL DAN MURID
            if (!$isLibur) {
                // Cari jadwal spesifik di kelas tersebut, hari tersebut, dan jam tersebut
                $jadwal = JadwalPelajaran::with(['mataPelajaran', 'ustadz'])
                    ->where('ruangan_id', $ruangan_id)
                    ->where('hari', $hari_ini)
                    ->where('jam_ke', $jam_ke)
                    ->first();

                // Jika jadwalnya ada, panggil data murid kelas tersebut
                if ($jadwal) {

                    // ==========================================================
                    // KECERDASAN OTOMATIS: Cari Tahun Pelajaran dari Tanggal
                    // ==========================================================
                    // PERBAIKAN 1: Hapus ->with('semester')
                    $bulan = BulanHijriyah::where('tanggal_mulai_masehi', '<=', $tanggal)
                        ->where('tanggal_selesai_masehi', '>=', $tanggal)
                        ->first();

                    // PERBAIKAN 2: Langsung ambil tahun_pelajaran_id dari variabel $bulan
                    if ($bulan) {
                        $tahun_pelajaran_id = $bulan->tahun_pelajaran_id;
                    } else {
                        // Fallback: Jika tanggal di luar rentang, ambil dari semester aktif
                        $semesterAktif = Semester::where('is_active', 1)->first();
                        $tahun_pelajaran_id = $semesterAktif ? $semesterAktif->tahun_pelajaran_id : null;
                    }
                    // ==========================================================

                    // Panggil murid menggunakan relasi Many-to-Many ke tabel pivot
                    $murids = $this->muridRuanganRepo->getMuridByRuanganAndTahun($ruangan_id, $tahun_pelajaran_id, 'Aktif');

                    // Ambil presensi yang mungkin sudah pernah diinput sebelumnya (agar bisa di-edit)
                    $presensiTersimpan = PresensiMurid::where('tanggal', $tanggal)
                        ->where('jadwal_pelajaran_id', $jadwal->id)
                        ->get()
                        ->keyBy('murid_id'); // Kunci array pakai ID murid agar mudah dicari di View
                }
            }
        }

        return view('presensi-murid.harian', compact(
            'ruangans',
            'jamList',
            'tanggal',
            'ruangan_id',
            'jam_ke',
            'hari_ini',
            'jadwal',
            'murids',
            'presensiTersimpan',
            'isLibur', // Tambahan
            'keteranganLibur' // Tambahan
        ));
    }

    // ==========================================
    // FUNGSI SIMPAN KHUSUS HARIAN
    // ==========================================
    public function storeHarian(Request $request)
    {
        $jadwal_id = $request->jadwal_pelajaran_id;
        $tanggal = $request->tanggal;
        $dataPresensi = $request->presensi;

        if (!$dataPresensi) {
            return back()->with('error', 'Tidak ada data presensi yang diproses.');
        }

        // --- TAMBAHAN BARU: Cari Semester Berdasarkan Tanggal ---
        $bulan = BulanHijriyah::where('tanggal_mulai_masehi', '<=', $tanggal)
            ->where('tanggal_selesai_masehi', '>=', $tanggal)
            ->first();

        // Langsung ambil tahun_pelajaran_id dari bulan
        if ($bulan) {
            $tahun_pelajaran_id = $bulan->tahun_pelajaran_id;
        } else {
            // Fallback: Jika tanggal di luar rentang, ambil dari semester aktif
            $semesterAktif = Semester::where('is_active', 1)->first();
            $tahun_pelajaran_id = $semesterAktif ? $semesterAktif->tahun_pelajaran_id : null;
        }
        // -------------------------------------------------------

        foreach ($dataPresensi as $murid_id => $status) {
            PresensiMurid::updateOrCreate(
                [
                    'jadwal_pelajaran_id' => $jadwal_id,
                    'murid_id' => $murid_id,
                    'tanggal' => $tanggal
                ],
                [
                    'status' => $status,
                    'semester_id' => $semesterAktif->id // SIMPAN SEMESTER ID
                ]
            );
        }

        return back()->with('success', 'Data presensi berhasil disimpan!');
    }

    // ==========================================
    // 2. OPSI BULANAN (Leger 1-30 Admin Mode)
    // ==========================================
    public function bulanan(Request $request)
    {
        $ruangans = Ruangan::with('level')->berdasarkanHakAkses()->orderBy('level_id')->orderBy('nama_ruangan')->get();
        $bulans = BulanHijriyah::orderBy('urutan')->get();
        $jamList = ['Nadzoman', '1', '2', 'Ekstra'];

        $bulan_id = $request->bulan_id;
        $ruangan_id = $request->ruangan_id;
        $jam_ke = $request->jam_ke;

        $dates = [];
        $matrix = [];
        $murids = collect();
        $bulanTerpilih = null;

        if ($bulan_id && $ruangan_id && $jam_ke) {
            $dataBulanan = $this->presensiService->hitungMatriksBulanan($bulan_id, $ruangan_id, $jam_ke);
            $bulanTerpilih = $dataBulanan['bulanTerpilih'];
            $murids = $dataBulanan['murids'];
            $dates = $dataBulanan['dates'];
            $matrix = $dataBulanan['matrix'];
        }

        return view('presensi-murid.bulanan', compact(
            'ruangans',
            'bulans',
            'jamList',
            'bulan_id',
            'ruangan_id',
            'jam_ke',
            'dates',
            'matrix',
            'murids',
            'bulanTerpilih'
        ));
    }

    // ==========================================
    // UPDATE FUNGSI SIMPAN LEGER BULANAN
    // ==========================================
    public function storeBulanan(Request $request)
    {
        $dataPresensi = $request->input('presensi');
        $bulan_id = $request->bulan_id;
        $ruangan_id = $request->ruangan_id;
        $jam_ke = $request->jam_ke;

        if (!$dataPresensi || !$bulan_id || !$ruangan_id || !$jam_ke) {
            return back()->with('error', 'Data presensi bulanan tidak lengkap.');
        }

        try {
            $this->presensiService->simpanPresensiBulanan(
                $dataPresensi,
                $bulan_id,
                $ruangan_id,
                $jam_ke,
                \Illuminate\Support\Facades\Auth::id()
            );

            return back()->with('success', 'Data rekap bulanan berhasil disimpan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan rekap bulanan: ' . $e->getMessage());
        }
    }

    // ==========================================
    // 3. REKAPITULASI PRESENSI
    // ==========================================
    public function rekap(Request $request)
    {
        $semesters = Semester::with('tahunPelajaran')->orderBy('id', 'desc')->get();
        $ruangans = Ruangan::with('level')->berdasarkanHakAkses()->orderBy('level_id')->orderBy('nama_ruangan')->get();

        $semester_id = $request->semester_id;
        $ruangan_id = $request->ruangan_id;
        $bulan_id = $request->bulan_id; // 1. Tambahan parameter Bulan

        // 2. Jika semester dipilih, ambil daftar bulan yang ada di semester tersebut
        $bulans = collect();
        if ($semester_id) {
            $semesterDicari = Semester::find($semester_id);

            if ($semesterDicari && $semesterDicari->tanggal_mulai && $semesterDicari->tanggal_selesai) {
                // MENCARI BULAN YANG BERSINGGUNGAN DENGAN SEMESTER (RUMUS OVERLAP)
                $bulans = BulanHijriyah::where('tahun_pelajaran_id', $semesterDicari->tahun_pelajaran_id)
                    ->where('tanggal_selesai_masehi', '>=', $semesterDicari->tanggal_mulai)
                    ->where('tanggal_mulai_masehi', '<=', $semesterDicari->tanggal_selesai)
                    ->orderBy('urutan')
                    ->get();
            }
        }

        $murids = collect();
        $rekap = [];
        $konfig = PengaturanAkademik::first();
        $semesterTerpilih = null;
        $bulanTerpilih = null; // Tambahan

        if ($semester_id && $ruangan_id) {
            $semesterTerpilih = Semester::findOrFail($semester_id);
            $tahun_pelajaran_id = $semesterTerpilih->tahun_pelajaran_id;

            if ($bulan_id) {
                $bulanTerpilih = BulanHijriyah::find($bulan_id);
            }

            // Ambil murid yang aktif di kelas & tahun ajaran tersebut
            $murids = $this->muridRuanganRepo->getMuridByRuanganAndTahun($ruangan_id, $tahun_pelajaran_id);

            // QUERY PRESENSI (Dinamis: Bisa se-semester, bisa per bulan)
            // =========================================================
            $presensiQuery = PresensiMurid::whereIn('murid_id', $murids->pluck('id'))
                ->where('semester_id', $semester_id);
            if ($bulan_id && $bulanTerpilih) {


                if (isset($bulanTerpilih->tanggal_mulai_masehi) && isset($bulanTerpilih->tanggal_selesai_masehi)) {
                    $presensiQuery->whereBetween('tanggal', [
                        $bulanTerpilih->tanggal_mulai_masehi,
                        $bulanTerpilih->tanggal_selesai_masehi
                    ]);
                }
            }

            $presensiDb = $presensiQuery->get();
            foreach ($murids as $murid) {
                $pMurid = $presensiDb->where('murid_id', $murid->id);

                $h = $pMurid->where('status', 'Hadir')->count();
                $s = $pMurid->where('status', 'Sakit')->count();
                $i = $pMurid->where('status', 'Izin')->count();
                $a = $pMurid->where('status', 'Alpha')->count();
                $d = $pMurid->where('status', 'Dispensasi')->count();

                $poinAlpha = $a * ($konfig->poin_alpha ?? 1);
                $poinIzin = $i * ($konfig->poin_izin ?? 0.16);

                $totalPoin = $poinAlpha + $poinIzin;

                $rekap[$murid->id] = [
                    'H' => $h,
                    'S' => $s,
                    'I' => $i,
                    'A' => $a,
                    'D' => $d,
                    'total_pertemuan' => $h + $s + $i + $a + $d,
                    'akumulasi_poin' => round($totalPoin, 2)
                ];
            }
        }

        // 4. Pastikan $bulans, $bulan_id, dan $bulanTerpilih di-compact ke view
        return view('presensi-murid.rekap', compact(
            'semesters',
            'ruangans',
            'bulans',
            'semester_id',
            'ruangan_id',
            'bulan_id',
            'murids',
            'rekap',
            'semesterTerpilih',
            'bulanTerpilih',
            'konfig'
        ));
    }


    public function cetakRekap(Request $request)
    {
        $semester_id = $request->semester_id;
        $ruangan_id = $request->ruangan_id;
        $bulan_id = $request->bulan_id;

        // Jika tidak ada data yang dipilih, tendang balik
        if (!$semester_id || !$ruangan_id) {
            return back()->with('error', 'Pilih semester dan ruangan terlebih dahulu untuk mencetak.');
        }

        $semesterTerpilih = Semester::with('tahunPelajaran')->findOrFail($semester_id);
        $ruanganTerpilih = Ruangan::findOrFail($ruangan_id);
        $konfig = PengaturanAkademik::first();
        $bulanTerpilih = $bulan_id ? BulanHijriyah::find($bulan_id) : null;
        $tahun_pelajaran_id = $semesterTerpilih->tahun_pelajaran_id;

        // Ambil murid
        $murids = $this->muridRuanganRepo->getMuridByRuanganAndTahun($ruangan_id, $tahun_pelajaran_id);

        // Query Presensi
        $presensiQuery = PresensiMurid::whereIn('murid_id', $murids->pluck('id'))
            ->where('semester_id', $semester_id);

        // Jika ada filter bulan
        if ($bulan_id && $bulanTerpilih && isset($bulanTerpilih->tanggal_mulai_masehi) && isset($bulanTerpilih->tanggal_selesai_masehi)) {
            $presensiQuery->whereBetween('tanggal', [
                $bulanTerpilih->tanggal_mulai_masehi,
                $bulanTerpilih->tanggal_selesai_masehi
            ]);
        }
        $presensiDb = $presensiQuery->get();

        // Hitung Data
        $rekap = [];
        foreach ($murids as $murid) {
            $pMurid = $presensiDb->where('murid_id', $murid->id);

            $h = $pMurid->where('status', 'Hadir')->count();
            $s = $pMurid->where('status', 'Sakit')->count();
            $i = $pMurid->where('status', 'Izin')->count();
            $a = $pMurid->where('status', 'Alpha')->count();
            $d = $pMurid->where('status', 'Dispensasi')->count();

            $poinAlpha = $a * ($konfig->poin_alpha ?? 1);
            $poinIzin = $i * ($konfig->poin_izin ?? 0.16);

            $rekap[$murid->id] = [
                'H' => $h,
                'S' => $s,
                'I' => $i,
                'A' => $a,
                'D' => $d,
                'akumulasi_poin' => round($poinAlpha + $poinIzin, 2)
            ];
        }

        return view('cetak-baru.cetak_rekap_presensi_murid', compact(
            'semesterTerpilih',
            'ruanganTerpilih',
            'bulanTerpilih',
            'murids',
            'rekap',
            'konfig'
        ));
    }
}
