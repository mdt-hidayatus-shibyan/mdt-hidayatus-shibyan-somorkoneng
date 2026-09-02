<?php

namespace App\Http\Controllers;

use App\Models\BulanHijriyah;
use App\Models\HariLibur;
use App\Models\JadwalPelajaran;
use App\Models\PresensiUstadz;
use App\Models\Ruangan;
use App\Models\Ustadz;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PresensiUstadzController extends Controller
{
    public function index(Request $request)
    {
        // 1. Tangkap parameter atau gunakan default hari ini
        $tanggal = $request->tanggal ?? date('Y-m-d');
        $ruangan_id = $request->ruangan_id;

        // 2. Siapkan data master untuk dropdown
        $ruangans = Ruangan::with('level')->berdasarkanHakAkses()->orderBy('level_id')->orderBy('nama_ruangan')->get();
        $semuaGuru = Ustadz::orderBy('nama_lengkap')->where('is_active', true)->get(); // Untuk daftar guru pengganti

        $jadwals = collect();
        $riwayatPresensi = collect();

        // Variabel penanda libur
        $isLibur = false;
        $keteranganLibur = null;

        // 3. Jika ruangan dan tanggal sudah dipilih, eksekusi pencarian
        if ($tanggal && $ruangan_id) {

            // LOGIKA HARI: Terjemahkan ke Bahasa Indonesia
            $nama_hari_inggris = \Carbon\Carbon::parse($tanggal)->format('l');
            $mapHari = [
                'Sunday' => 'Ahad',
                'Monday' => 'Senin',
                'Tuesday' => 'Selasa',
                'Wednesday' => 'Rabu',
                'Thursday' => 'Kamis',
                'Friday' => 'Jumat',
                'Saturday' => 'Sabtu'
            ];
            $hariIndo = $mapHari[$nama_hari_inggris];

            // ==========================================================
            // CEK HARI LIBUR & JUMAT
            // ==========================================================
            $libur = HariLibur::where('tanggal_mulai', '<=', $tanggal)
                ->where('tanggal_selesai', '>=', $tanggal)
                ->first();

            if ($libur) {
                // Jika masuk rentang kalender libur madrasah
                $isLibur = true;
                $keteranganLibur = $libur->keterangan;
            } elseif ($hariIndo === 'Jumat') {
                // Jika hari Jumat (Libur rutin madrasah)
                $isLibur = true;
                $keteranganLibur = 'Libur Rutin (Jumat)';
            }
            // ==========================================================

            // Jika TIDAK LIBUR, baru kita cari jadwal dan riwayat presensinya
            if (!$isLibur) {
                // Ambil jadwal pelajaran khusus ruangan dan hari tersebut
                $jadwals = JadwalPelajaran::with(['mataPelajaran', 'ustadz'])
                    ->where('ruangan_id', $ruangan_id)
                    ->where('hari', $hariIndo)
                    ->orderBy('jam_ke')
                    ->get();

                // Jika ada jadwal, cari riwayat presensi yang sudah diinput
                if ($jadwals->isNotEmpty()) {
                    $riwayatPresensi = PresensiUstadz::with(['ustadz', 'guruPengganti', 'penginput'])
                        ->where('tanggal', $tanggal)
                        ->whereIn('jadwal_pelajaran_id', $jadwals->pluck('id'))
                        ->get()
                        ->keyBy('jadwal_pelajaran_id');
                }
            }
        }

        return view('presensi-ustadz.harian', compact('tanggal', 'ruangan_id', 'ruangans', 'semuaGuru', 'jadwals', 'riwayatPresensi', 'isLibur', 'keteranganLibur'));
    }


    public function storeHarian(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'ruangan_id' => 'required|exists:ruangans,id',
            'presensi' => 'required|array'
        ]);

        $tanggal = $request->tanggal;

        // Looping semua data presensi yang dikirim dari tabel form
        foreach ($request->presensi as $jadwal_id => $data) {

            // Pastikan status diisi
            if (!empty($data['status'])) {

                // Gunakan updateOrCreate: Jika sudah ada data, update. Jika belum, buat baru.
                PresensiUstadz::updateOrCreate(
                    [
                        'tanggal' => $tanggal,
                        'jadwal_pelajaran_id' => $jadwal_id,
                    ],
                    [
                        'ustadz_id' => $data['ustadz_id'],
                        'status' => $data['status'],
                        // Guru pengganti HANYA disimpan jika status bukan Hadir/Kosong
                        'ustadz_pengganti_id' => in_array($data['status'], ['Izin', 'Sakit', 'Alpha']) ? ($data['ustadz_pengganti_id'] ?? null) : null,
                        'keterangan' => $data['keterangan'] ?? null,
                        'diinput_oleh_id' => Auth::id(), // Catat siapa yang klik simpan
                    ]
                );
            }
        }

        return back()->with('success', 'Data presensi guru berhasil disimpan!');
    }

    public function destroyHarian($id)
    {
        $presensi = PresensiUstadz::findOrFail($id);

        // Hapus data dari database
        $presensi->delete();

        return back()->with('success', 'Data presensi berhasil dihapus (dibatalkan)!');
    }


    public function bulanan(Request $request)
    {
        $ruangans = Ruangan::with('level')->berdasarkanHakAkses()->orderBy('level_id')->orderBy('nama_ruangan')->get();
        $bulans = BulanHijriyah::orderBy('urutan')->get();
        $jamList = ['Nadzoman', '1', '2', 'Ekstra'];

        $semuaGuru = Ustadz::orderBy('nama_lengkap')->get();

        $bulan_id = $request->bulan_id;
        $ruangan_id = $request->ruangan_id;

        $dates = [];
        $matrix = [];
        $bulanTerpilih = null;

        if ($bulan_id && $ruangan_id) {
            $bulanTerpilih = BulanHijriyah::findOrFail($bulan_id);

            $start = Carbon::parse($bulanTerpilih->tanggal_mulai_masehi);
            $end = Carbon::parse($bulanTerpilih->tanggal_selesai_masehi);
            $jumlahHari = $start->diffInDays($end) + 1;

            // Ambil semua jadwal di ruangan ini lalu kelompokkan per hari
            $jadwals = JadwalPelajaran::with(['mataPelajaran', 'ustadz'])
                ->where('ruangan_id', $ruangan_id)
                ->get()
                ->groupBy('hari');

            // Ambil kalender libur
            $hariLiburs = HariLibur::where(function ($q) use ($bulanTerpilih) {
                $q->whereBetween('tanggal_mulai', [$bulanTerpilih->tanggal_mulai_masehi, $bulanTerpilih->tanggal_selesai_masehi])
                    ->orWhereBetween('tanggal_selesai', [$bulanTerpilih->tanggal_mulai_masehi, $bulanTerpilih->tanggal_selesai_masehi])
                    ->orWhere(function ($sub) use ($bulanTerpilih) {
                        $sub->where('tanggal_mulai', '<=', $bulanTerpilih->tanggal_mulai_masehi)
                            ->where('tanggal_selesai', '>=', $bulanTerpilih->tanggal_selesai_masehi);
                    });
            })->get();

            // Ambil presensi guru bulan ini (Riwayat inputan)
            $presensiDb = PresensiUstadz::with(['ustadz', 'guruPengganti'])
                ->whereBetween('tanggal', [$bulanTerpilih->tanggal_mulai_masehi, $bulanTerpilih->tanggal_selesai_masehi])
                ->whereIn('jadwal_pelajaran_id', $jadwals->flatten()->pluck('id'))
                ->get();

            // Format data presensi agar mudah dicari di tabel
            $presensiFormatted = [];
            foreach ($presensiDb as $p) {
                $presensiFormatted[$p->tanggal][$p->jadwal_pelajaran_id] = $p;
            }

            $mapHari = [
                'Sunday' => 'Ahad',
                'Monday' => 'Senin',
                'Tuesday' => 'Selasa',
                'Wednesday' => 'Rabu',
                'Thursday' => 'Kamis',
                'Friday' => 'Jumat',
                'Saturday' => 'Sabtu'
            ];

            // RAKIT MATRIKS VERTIKAL
            for ($i = 0; $i < $jumlahHari; $i++) {
                $currentDate = $start->copy()->addDays($i);
                $nama_hari_inggris = $currentDate->format('l');
                $hariIndo = $mapHari[$nama_hari_inggris];
                $tglMasehi = $currentDate->format('Y-m-d');

                // Cek Libur
                $isLibur = false;
                $keteranganLibur = null;

                if ($hariIndo === 'Jumat') {
                    $isLibur = true;
                    $keteranganLibur = 'Libur Rutin (Jumat)';
                } else {
                    foreach ($hariLiburs as $libur) {
                        $liburMulaiStr = Carbon::parse($libur->tanggal_mulai)->format('Y-m-d');
                        $liburSelesaiStr = Carbon::parse($libur->tanggal_selesai)->format('Y-m-d');
                        if ($tglMasehi >= $liburMulaiStr && $tglMasehi <= $liburSelesaiStr) {
                            $isLibur = true;
                            $keteranganLibur = $libur->keterangan;
                            break;
                        }
                    }
                }

                $dates[$tglMasehi] = [
                    'hari' => $hariIndo,
                    'is_libur' => $isLibur,
                    'keterangan_libur' => $keteranganLibur
                ];

                // Susun matriks per jam jika tidak libur
                if (!$isLibur) {
                    $jadwalHariIni = $jadwals->get($hariIndo);

                    foreach ($jamList as $jam) {
                        $jadwalJamIni = $jadwalHariIni ? $jadwalHariIni->firstWhere('jam_ke', $jam) : null;

                        if ($jadwalJamIni) {
                            $presensi = $presensiFormatted[$tglMasehi][$jadwalJamIni->id] ?? null;
                            $matrix[$tglMasehi][$jam] = [
                                'is_jadwal' => true,
                                // 2. TAMBAHKAN 2 BARIS INI KE DALAM ARRAY:
                                'jadwal_id' => $jadwalJamIni->id,
                                'ustadz_id' => $jadwalJamIni->ustadz_id,

                                'mapel' => $jadwalJamIni->mataPelajaran->nama_mapel,
                                'guru_utama' => $jadwalJamIni->ustadz->nama_lengkap,
                                'presensi' => $presensi
                            ];
                        } else {
                            $matrix[$tglMasehi][$jam] = ['is_jadwal' => false];
                        }
                    }
                }
            }
        }

        return view('presensi-ustadz.bulanan', compact('ruangans', 'bulans', 'jamList', 'bulan_id', 'ruangan_id', 'dates', 'matrix', 'bulanTerpilih', 'semuaGuru'));
    }


    public function storeBulanan(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jadwal_pelajaran_id' => 'required',
            'ustadz_id' => 'required',
            'status' => 'required'
        ]);

        PresensiUstadz::updateOrCreate(
            [
                'tanggal' => $request->tanggal,
                'jadwal_pelajaran_id' => $request->jadwal_pelajaran_id,
            ],
            [
                'ustadz_id' => $request->ustadz_id,
                'status' => $request->status,
                'ustadz_pengganti_id' => in_array($request->status, ['Izin', 'Sakit', 'Alpha']) ? $request->ustadz_pengganti_id : null,
                'keterangan' => $request->keterangan,
                'diinput_oleh_id' => Auth::id(),
            ]
        );

        return back()->with('success', 'Data presensi berhasil diperbarui langsung dari matriks bulanan!');
    }


    public function rekapSemua(Request $request)
    {
        $bulans = BulanHijriyah::orderBy('urutan')->get();
        $bulan_id = $request->bulan_id;
        $bulanTerpilih = null;

        // Ambil data Master Ruangan untuk dijadikan Kolom Tabel
        $ruangans = Ruangan::orderBy('level_id')->orderBy('nama_ruangan')->get();
        $rekap = [];

        if ($bulan_id) {
            $bulanTerpilih = BulanHijriyah::findOrFail($bulan_id);
            $start = $bulanTerpilih->tanggal_mulai_masehi;
            $end = $bulanTerpilih->tanggal_selesai_masehi;

            // 1. Siapkan Wadah Array (Baris = Guru, Kolom = Ruangan)
            $asatidzs = Ustadz::orderBy('nama_lengkap')->get();
            foreach ($asatidzs as $guru) {
                $rekap[$guru->id] = [
                    'nama' => $guru->nama_lengkap,
                    'ruangan' => [],
                    'total' => 0
                ];
                // Buat nilai awal 0 untuk setiap ruangan
                foreach ($ruangans as $ruangan) {
                    $rekap[$guru->id]['ruangan'][$ruangan->id] = 0;
                }
            }

            // 2. Ambil data presensi berserta relasi ke jadwal (untuk mengetahui ruangan)
            $presensis = PresensiUstadz::with('jadwalPelajaran')
                ->whereBetween('tanggal', [$start, $end])
                ->get();

            // 3. Masukkan jumlah kehadiran ke ruangan yang tepat
            foreach ($presensis as $p) {
                // Pastikan jadwalnya valid
                if ($p->jadwalPelajaran) {
                    $ruangan_id = $p->jadwalPelajaran->ruangan_id;

                    // Jika Guru Utama HADIR
                    if ($p->status === 'Hadir' && isset($rekap[$p->ustadz_id])) {
                        $rekap[$p->ustadz_id]['ruangan'][$ruangan_id]++;
                        $rekap[$p->ustadz_id]['total']++;
                    }

                    // Jika digantikan, maka yang dihitung "Hadir" adalah Guru Pengganti (Badal)
                    if (in_array($p->status, ['Sakit', 'Izin', 'Alpha']) && $p->ustadz_pengganti_id && isset($rekap[$p->ustadz_pengganti_id])) {
                        $rekap[$p->ustadz_pengganti_id]['ruangan'][$ruangan_id]++;
                        $rekap[$p->ustadz_pengganti_id]['total']++;
                    }
                }
            }
        }

        return view('presensi-ustadz.rekap_semua', compact('bulans', 'bulan_id', 'bulanTerpilih', 'rekap', 'ruangans'));
    }


    public function cetakRekap(Request $request)
    {
        $bulan_id = $request->bulan_id;

        if (!$bulan_id) {
            return back()->with('error', 'Silakan pilih bulan terlebih dahulu.');
        }

        $bulanTerpilih = BulanHijriyah::with('semester.tahunPelajaran')->findOrFail($bulan_id);
        $start = $bulanTerpilih->tanggal_mulai_masehi;
        $end = $bulanTerpilih->tanggal_selesai_masehi;
        $ruangans = Ruangan::orderBy('level_id')->orderBy('nama_ruangan')->get();

        // Ambil data ustadz
        $semuaGuru = Ustadz::orderBy('nama_lengkap', 'asc')->get();

        // 1. Siapkan struktur array dasar (Semuanya bernilai 0 di awal)
        $rekap = [];
        foreach ($semuaGuru as $guru) {
            $rekap[$guru->id] = [
                'nama' => $guru->nama_lengkap,
                'ruangan' => [],
                'total' => 0
            ];

            foreach ($ruangans as $ruangan) {
                $rekap[$guru->id]['ruangan'][$ruangan->id] = 0;
            }
        }

        // 2. Ambil seluruh presensi dalam bulan tersebut
        $presensi = PresensiUstadz::with('jadwalPelajaran')
            ->whereDate('tanggal', '>=', $start)
            ->whereDate('tanggal', '<=', $end)
            ->get();

        // ================================================================
        // 3. PERHITUNGAN BARU (Super Cepat) MENGGUNAKAN LOGIKA ANDA
        // ================================================================
        foreach ($presensi as $p) {
            if ($p->jadwalPelajaran) {
                $ruangan_id = $p->jadwalPelajaran->ruangan_id;
                $idUtama = $p->ustadz_id;
                $idPengganti = $p->ustadz_pengganti_id;

                // A. Tambah poin untuk Guru Utama yang HADIR
                if ($p->status === 'Hadir' && isset($rekap[$idUtama])) {
                    if (isset($rekap[$idUtama]['ruangan'][$ruangan_id])) {
                        $rekap[$idUtama]['ruangan'][$ruangan_id]++;
                    }
                    $rekap[$idUtama]['total']++;
                }

                // B. Tambah poin untuk Guru Pengganti / Badal (Jika Utama tidak hadir)
                if (in_array($p->status, ['Sakit', 'Izin', 'Alpha']) && $idPengganti && isset($rekap[$idPengganti])) {
                    if (isset($rekap[$idPengganti]['ruangan'][$ruangan_id])) {
                        $rekap[$idPengganti]['ruangan'][$ruangan_id]++;
                    }
                    $rekap[$idPengganti]['total']++;
                }
            }
        }

        return view('cetak-baru.cetak_rekap_presensi_ustadz', compact('bulanTerpilih', 'ruangans', 'rekap'));
    }

    public function exportExcel(Request $request)
    {
        $bulan_id = $request->bulan_id;
        if (!$bulan_id) return back()->with('error', 'Pilih bulan terlebih dahulu!');

        $bulanTerpilih = BulanHijriyah::findOrFail($bulan_id);
        $start = $bulanTerpilih->tanggal_mulai_masehi;
        $end = $bulanTerpilih->tanggal_selesai_masehi;

        $ruangans = Ruangan::orderBy('level_id')->orderBy('nama_ruangan')->get();
        $rekap = [];

        $asatidzs = Ustadz::orderBy('nama_lengkap')->get();
        foreach ($asatidzs as $guru) {
            $rekap[$guru->id] = ['nama' => $guru->nama_lengkap, 'ruangan' => [], 'total' => 0];
            foreach ($ruangans as $ruangan) {
                $rekap[$guru->id]['ruangan'][$ruangan->id] = 0;
            }
        }

        $presensis = PresensiUstadz::with('jadwalPelajaran')->whereBetween('tanggal', [$start, $end])->get();
        foreach ($presensis as $p) {
            if ($p->jadwalPelajaran) {
                $ruangan_id = $p->jadwalPelajaran->ruangan_id;
                if ($p->status === 'Hadir' && isset($rekap[$p->ustadz_id])) {
                    $rekap[$p->ustadz_id]['ruangan'][$ruangan_id]++;
                    $rekap[$p->ustadz_id]['total']++;
                }
                if (in_array($p->status, ['Sakit', 'Izin', 'Alpha']) && $p->ustadz_pengganti_id && isset($rekap[$p->ustadz_pengganti_id])) {
                    $rekap[$p->ustadz_pengganti_id]['ruangan'][$ruangan_id]++;
                    $rekap[$p->ustadz_pengganti_id]['total']++;
                }
            }
        }

        // ==========================================
        // PROSES PEMBUATAN EXCEL (CSV)
        // ==========================================
        $fileName = "Rekap_Kehadiran_Per_Ruangan_" . str_replace(' ', '_', $bulanTerpilih->nama_bulan) . ".csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function () use ($rekap, $ruangans) {
            $file = fopen('php://output', 'w');

            // Tulis Header Kolom
            $headerRow = ['No', 'Nama Guru'];
            foreach ($ruangans as $r) {
                $headerRow[] = $r->nama_ruangan;
            }
            $headerRow[] = 'Total Hadir';
            fputcsv($file, $headerRow);

            // Tulis Data Baris per Baris
            $no = 1;
            foreach ($rekap as $row) {
                $dataRow = [$no++, $row['nama']];
                foreach ($ruangans as $r) {
                    $dataRow[] = $row['ruangan'][$r->id];
                }
                $dataRow[] = $row['total'];
                fputcsv($file, $dataRow);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
