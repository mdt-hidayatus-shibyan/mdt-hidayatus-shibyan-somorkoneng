<?php

namespace App\Http\Controllers\Tabungan;

use App\Http\Controllers\Controller;
use App\Models\Murid;
use App\Models\Ruangan;
use App\Models\Tabungan\PeriodeTabungan;
use App\Models\Tabungan\Tabungan;
use App\Models\Tabungan\TransaksiTabungan;
use App\Models\Ustadz;
use App\Services\Tabungan\TabunganService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TabunganMadrasahController extends Controller
{
    protected $tabunganService;

    public function __construct(TabunganService $tabunganService)
    {
        $this->tabunganService = $tabunganService;
    }

    /**
     * Dashboard Tabungan Madrasah
     */
    public function index(Request $request)
    {
        $ringkasan = $this->tabunganService->getRingkasanDashboard();

        // 10 Transaksi Terakhir
        $transaksiTerbaru = TransaksiTabungan::with(['tabungan', 'petugas'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        // Rekening dengan Saldo Terbesar
        $topRekening = Tabungan::with(['murid', 'ustadz', 'ruangan'])
            ->orderBy('saldo', 'desc')
            ->limit(5)
            ->get();

        // Periode Aktif
        $periodeAktif = PeriodeTabungan::where('is_active', true)->first();

        return view('tabungan.dashboard', compact(
            'ringkasan',
            'transaksiTerbaru',
            'topRekening',
            'periodeAktif'
        ));
    }

    /**
     * Master Rekening Tabungan (Semua Kategori)
     */
    public function rekening(Request $request)
    {
        $query = Tabungan::with(['murid.ruangans', 'murid.ruanganMasuk', 'ustadz', 'ruangan', 'periodeTabungan']);

        if ($request->jenis && in_array($request->jenis, ['Murid', 'Ustadz', 'Kas Ruangan', 'Umum'])) {
            $query->where('jenis_nasabah', $request->jenis);
        }

        // Filter ruangan saat memilih filter murid
        if ($request->jenis === 'Murid' && $request->filled('ruangan_id')) {
            $ruanganId = $request->ruangan_id;
            $query->whereHas('murid', function ($mq) use ($ruanganId) {
                $mq->where(function ($q) use ($ruanganId) {
                    $q->whereHas('ruangans', function ($rq) use ($ruanganId) {
                        $rq->where('ruangans.id', $ruanganId);
                    })->orWhere('ruangan_masuk', $ruanganId);
                });
            });
        }

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_rekening', 'like', "%{$search}%")
                    ->orWhere('nama_rekening', 'like', "%{$search}%")
                    ->orWhere('nama_nasabah_umum', 'like', "%{$search}%")
                    ->orWhereHas('murid', function ($mq) use ($search) {
                        $mq->where('nama_lengkap', 'like', "%{$search}%")
                            ->orWhere('nism', 'like', "%{$search}%");
                    })
                    ->orWhereHas('ustadz', function ($uq) use ($search) {
                        $uq->where('nama_lengkap', 'like', "%{$search}%")
                            ->orWhere('nigm', 'like', "%{$search}%");
                    })
                    ->orWhereHas('ruangan', function ($rq) use ($search) {
                        $rq->where('nama_ruangan', 'like', "%{$search}%");
                    });
            });
        }

        $tabungans = $query->orderBy('saldo', 'desc')->paginate(20)->withQueryString();

        // Ambil daftar ruangan diurutkan berdasarkan urutan_level kemudian nama_ruangan
        $daftarRuangan = Ruangan::select('ruangans.*')
            ->join('levels', 'ruangans.level_id', '=', 'levels.id')
            ->with('level')
            ->orderBy('levels.urutan_level', 'asc')
            ->orderBy('ruangans.nama_ruangan', 'asc')
            ->get();

        return view('tabungan.rekening.index', compact('tabungans', 'daftarRuangan'));
    }

    /**
     * AJAX Lookup Murid berdasarkan NISM atau Nama
     */
    public function cariMuridByNism(Request $request)
    {
        $nism = trim($request->nism ?? $request->q ?? '');
        if (!$nism) {
            return response()->json([
                'status' => 'empty',
                'message' => 'Silakan masukkan NISM atau nama murid.'
            ]);
        }

        // 1. Cek exact match NISM, NISN, atau ID
        $murid = Murid::where('nism', $nism)
            ->orWhere('nisn', $nism)
            ->orWhere('id', $nism)
            ->with(['ruangans.level', 'ruanganMasuk.level'])
            ->first();

        // 2. Jika tidak exact, cari LIKE query
        if (!$murid) {
            $matches = Murid::where('nism', 'like', "%{$nism}%")
                ->orWhere('nama_lengkap', 'like', "%{$nism}%")
                ->with(['ruangans.level', 'ruanganMasuk.level'])
                ->limit(6)
                ->get();

            if ($matches->count() === 1) {
                $murid = $matches->first();
            } elseif ($matches->count() > 1) {
                return response()->json([
                    'status' => 'multiple',
                    'message' => 'Ditemukan ' . $matches->count() . ' data murid yang cocok:',
                    'data' => $matches->map(function ($m) {
                        $kelas = $m->ruangans->pluck('nama_ruangan')->implode(', ') ?: ($m->ruanganMasuk->nama_ruangan ?? 'Belum Ada Ruangan');
                        return [
                            'id' => $m->id,
                            'nama' => $m->nama_lengkap,
                            'nism' => $m->nism ?? '-',
                            'nisn' => $m->nisn ?? '-',
                            'kelas' => $kelas,
                            'status' => $m->status,
                        ];
                    })
                ]);
            }
        }

        if (!$murid) {
            return response()->json([
                'status' => 'not_found',
                'message' => "Murid dengan NISM/Nama \"{$nism}\" tidak ditemukan atau belum terdaftar."
            ]);
        }

        $kelas = $murid->ruangans->pluck('nama_ruangan')->implode(', ') ?: ($murid->ruanganMasuk->nama_ruangan ?? 'Belum Ada Ruangan');

        return response()->json([
            'status' => 'found',
            'data' => [
                'id' => $murid->id,
                'nama' => $murid->nama_lengkap,
                'nism' => $murid->nism ?? '-',
                'nisn' => $murid->nisn ?? '-',
                'kelas' => $kelas,
                'status' => $murid->status,
            ]
        ]);
    }

    /**
     * AJAX Lookup Ustadz berdasarkan NIGM, Kode, atau Nama
     */
    public function cariUstadzByNigm(Request $request)
    {
        $nigm = trim($request->nigm ?? $request->q ?? '');
        if (!$nigm) {
            return response()->json([
                'status' => 'empty',
                'message' => 'Silakan masukkan NIGM atau nama ustadz.'
            ]);
        }

        // 1. Cek exact match NIGM, kode_ustadz, atau ID
        $ustadz = Ustadz::where('nigm', $nigm)
            ->orWhere('kode_ustadz', $nigm)
            ->orWhere('id', $nigm)
            ->first();

        // 2. Jika tidak exact, cari LIKE query
        if (!$ustadz) {
            $matches = Ustadz::where('nigm', 'like', "%{$nigm}%")
                ->orWhere('nama_lengkap', 'like', "%{$nigm}%")
                ->orWhere('kode_ustadz', 'like', "%{$nigm}%")
                ->limit(6)
                ->get();

            if ($matches->count() === 1) {
                $ustadz = $matches->first();
            } elseif ($matches->count() > 1) {
                return response()->json([
                    'status' => 'multiple',
                    'message' => 'Ditemukan ' . $matches->count() . ' data ustadz yang cocok:',
                    'data' => $matches->map(function ($u) {
                        return [
                            'id' => $u->id,
                            'nama' => $u->nama_lengkap,
                            'nigm' => $u->nigm ?? ($u->kode_ustadz ?? '-'),
                            'kode_ustadz' => $u->kode_ustadz ?? '-',
                            'status' => $u->is_active ? 'Aktif' : 'Non-Aktif',
                        ];
                    })
                ]);
            }
        }

        if (!$ustadz) {
            return response()->json([
                'status' => 'not_found',
                'message' => "Ustadz dengan NIGM/Nama \"{$nigm}\" tidak ditemukan atau belum terdaftar."
            ]);
        }

        return response()->json([
            'status' => 'found',
            'data' => [
                'id' => $ustadz->id,
                'nama' => $ustadz->nama_lengkap,
                'nigm' => $ustadz->nigm ?? ($ustadz->kode_ustadz ?? '-'),
                'kode_ustadz' => $ustadz->kode_ustadz ?? '-',
                'status' => $ustadz->is_active ? 'Aktif' : 'Non-Aktif',
            ]
        ]);
    }

    /**
     * Halaman Generator Barcode Buku Tabungan (7 Angka Prefix Tahun Hijriyah)
     */
    public function generatorBarcode(Request $request)
    {
        $daftarTahun = \App\Models\TahunPelajaran::orderBy('id', 'desc')->get();
        $tahunAktif = \App\Models\TahunPelajaran::where('is_active', true)->first() ?? $daftarTahun->first();
        $tahunId = $request->tahun_id ?? ($tahunAktif?->id);

        $selectedTahun = $daftarTahun->firstWhere('id', $tahunId) ?? $tahunAktif;

        // Ekstrak prefix dari nama hijriyah (mis. "1447-1448" -> "4748")
        $rawHijri = $selectedTahun?->nama_hijriyah ?? '1447-1448';
        preg_match_all('/\d{2,4}/', $rawHijri, $matches);
        if (!empty($matches[0]) && count($matches[0]) >= 2) {
            $prefix = substr($matches[0][0], -2) . substr($matches[0][1], -2);
        } elseif (!empty($matches[0])) {
            $prefix = substr($matches[0][0], -4);
        } else {
            $prefix = '4748';
        }

        $mulaiDari = (int) ($request->mulai ?? 1);
        $jumlah = (int) ($request->jumlah ?? 500);
        if ($jumlah > 1000) $jumlah = 1000;
        if ($jumlah < 1) $jumlah = 500;

        $barcodes = [];
        for ($i = $mulaiDari; $i < ($mulaiDari + $jumlah); $i++) {
            $barcode = $prefix . str_pad($i, 3, '0', STR_PAD_LEFT);
            $barcodes[] = [
                'no' => $i,
                'barcode' => $barcode,
                'is_registered' => Tabungan::where('nomor_rekening', $barcode)->exists(),
            ];
        }

        return view('tabungan.barcode.generator', compact('daftarTahun', 'selectedTahun', 'prefix', 'mulaiDari', 'jumlah', 'barcodes'));
    }

    /**
     * Download CSV Barcode Buku Tabungan
     */
    public function exportBarcode(Request $request)
    {
        $prefix = $request->prefix ?? '4748';
        $mulaiDari = (int) ($request->mulai ?? 1);
        $jumlah = (int) ($request->jumlah ?? 500);

        $filename = "barcode_buku_tabungan_{$prefix}_{$mulaiDari}_sd_" . ($mulaiDari + $jumlah - 1) . ".csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($prefix, $mulaiDari, $jumlah) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['No', 'Nomor_Barcode', 'Format', 'Status_Database']);

            for ($i = $mulaiDari; $i < ($mulaiDari + $jumlah); $i++) {
                $barcode = $prefix . str_pad($i, 3, '0', STR_PAD_LEFT);
                $isRegistered = Tabungan::where('nomor_rekening', $barcode)->exists() ? 'Sudah Dipakai' : 'Tersedia';
                fputcsv($file, [$i, $barcode, '7 Digit (Prefix: ' . $prefix . ')', $isRegistered]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Form Buka Rekening Baru
     */
    public function createRekening(Request $request)
    {
        $ruangans = Ruangan::select('ruangans.*')
            ->join('levels', 'ruangans.level_id', '=', 'levels.id')
            ->orderBy('levels.urutan_level', 'asc')
            ->orderBy('ruangans.nama_ruangan', 'asc')
            ->get(['ruangans.id', 'ruangans.nama_ruangan', 'ruangans.level_id']);

        $periodes = PeriodeTabungan::orderBy('id', 'desc')->get();
        $periodeAktifId = PeriodeTabungan::where('is_active', true)->value('id') ?? $periodes->first()?->id;

        // Ambil riwayat pendaftaran buku tabungan terkini (10 pendaftaran terakhir)
        $riwayatPendaftaran = Tabungan::with(['murid.ruangans', 'ustadz', 'ruangan', 'periodeTabungan'])
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        return view('tabungan.rekening.create', compact('ruangans', 'periodes', 'periodeAktifId', 'riwayatPendaftaran'));
    }

    /**
     * Simpan Rekening Baru
     */
    public function storeRekening(Request $request)
    {
        $request->validate([
            'nomor_rekening' => 'required|string|max:50|unique:tabungans,nomor_rekening',
            'jenis_nasabah' => 'required|in:Murid,Ustadz,Kas Ruangan,Umum',
            'nama_rekening' => 'required|string|max:100',
            'murid_id' => 'required_if:jenis_nasabah,Murid|nullable|exists:murids,id',
            'ustadz_id' => 'required_if:jenis_nasabah,Ustadz|nullable|exists:ustadzs,id',
            'ruangan_id' => 'required_if:jenis_nasabah,Kas Ruangan|nullable|exists:ruangans,id',
            'nama_nasabah_umum' => 'required_if:jenis_nasabah,Umum|nullable|string|max:150',
            'kontak_umum' => 'nullable|string|max:30',
            'alamat_umum' => 'nullable|string|max:255',
            'periode_tabungan_id' => 'nullable|exists:periode_tabungans,id',
            'setoran_awal' => 'nullable|numeric|min:0',
            'catatan' => 'nullable|string',
        ], [
            'nomor_rekening.required' => 'Nomor rekening / barcode buku tabungan wajib diisi atau discan.',
            'nomor_rekening.unique' => 'Nomor rekening / barcode buku tabungan ini sudah terdaftar sebelumnya.',
            'murid_id.required_if' => 'Silakan pilih atau cari murid yang bersangkutan.',
            'ustadz_id.required_if' => 'Silakan pilih atau cari ustadz yang bersangkutan.',
            'ruangan_id.required_if' => 'Silakan pilih ruangan kelas kas yang bersangkutan.',
            'nama_nasabah_umum.required_if' => 'Nama nasabah umum wajib diisi.',
        ]);

        try {
            $tabungan = $this->tabunganService->bukaRekening($request->all(), Auth::id());

            if ($request->setoran_awal && (float)$request->setoran_awal > 0) {
                $this->tabunganService->setorTunai(
                    $tabungan->id,
                    (float) $request->setoran_awal,
                    Auth::id(),
                    date('Y-m-d'),
                    'Setoran Awal Buka Rekening'
                );
            }

            return redirect()->route('tabungan.rekening.create')
                ->with('success', "Buku tabungan nomor {$tabungan->nomor_rekening} atas nama {$tabungan->nama_nasabah} berhasil didaftarkan!");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal membuka rekening: ' . $e->getMessage());
        }
    }

    /**
     * Detail Buku Tabungan & Mutasi Transaksi
     */
    public function detailRekening($id)
    {
        $tabungan = Tabungan::with(['murid', 'ustadz', 'ruangan', 'periodeTabungan'])->findOrFail($id);
        $transaksis = $tabungan->transaksis()->with('petugas')->paginate(25);
        $kalkulasiPenarikan = $this->tabunganService->hitungPotongan($tabungan, (float) $tabungan->saldo);

        return view('tabungan.rekening.detail', compact('tabungan', 'transaksis', 'kalkulasiPenarikan'));
    }

    /**
     * Halaman Utama Setor Tunai Tabungan (Scan Barcode / 3 Digit Manual)
     */
    public function formSetorTunai(Request $request)
    {
        $daftarTahun = \App\Models\TahunPelajaran::orderBy('id', 'desc')->get();
        $tahunAktif = \App\Models\TahunPelajaran::where('is_active', true)->first() ?? $daftarTahun->first();

        // Ekstrak prefix dari nama hijriyah (mis. "1447-1448" -> "4748")
        $rawHijri = $tahunAktif?->nama_hijriyah ?? '1447-1448';
        preg_match_all('/\d{2,4}/', $rawHijri, $matches);
        if (!empty($matches[0]) && count($matches[0]) >= 2) {
            $prefix = substr($matches[0][0], -2) . substr($matches[0][1], -2);
        } elseif (!empty($matches[0])) {
            $prefix = substr($matches[0][0], -4);
        } else {
            $prefix = '4748';
        }

        // Riwayat Setoran Terkini (10 transaksi terakhir)
        $riwayatSetoran = TransaksiTabungan::where('jenis_transaksi', 'Setor')
            ->with(['tabungan.murid.ruangans', 'tabungan.ustadz', 'tabungan.ruangan', 'petugas'])
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        return view('tabungan.setor.index', compact('daftarTahun', 'tahunAktif', 'prefix', 'riwayatSetoran'));
    }

    /**
     * AJAX Lookup Rekening Tabungan berdasarkan Barcode / Nomor Rekening / 3 Digit Akhir
     */
    public function cariRekeningByBarcode(Request $request)
    {
        $q = trim($request->barcode ?? $request->q ?? $request->nomor_rekening ?? '');
        if (!$q) {
            return response()->json([
                'status' => 'empty',
                'message' => 'Silakan scan barcode atau masukkan nomor rekening.'
            ]);
        }

        $daftarTahun = \App\Models\TahunPelajaran::orderBy('id', 'desc')->get();
        $tahunAktif = \App\Models\TahunPelajaran::where('is_active', true)->first() ?? $daftarTahun->first();
        $rawHijri = $tahunAktif?->nama_hijriyah ?? '1447-1448';
        preg_match_all('/\d{2,4}/', $rawHijri, $matches);
        if (!empty($matches[0]) && count($matches[0]) >= 2) {
            $prefix = substr($matches[0][0], -2) . substr($matches[0][1], -2);
        } elseif (!empty($matches[0])) {
            $prefix = substr($matches[0][0], -4);
        } else {
            $prefix = '4748';
        }

        // 1. Coba exact match nomor_rekening
        $tabungan = Tabungan::where('nomor_rekening', $q)
            ->with(['murid.ruangans.level', 'murid.ruanganMasuk.level', 'ustadz', 'ruangan', 'periodeTabungan'])
            ->first();

        // 2. Jika input berupa 1 - 3 digit (contoh: '001' atau '1' atau '25'), coba kombinasi dengan prefix tahun (contoh: '4748' + '001' -> '4748001')
        if (!$tabungan && ctype_digit($q) && strlen($q) <= 4) {
            $padded = $prefix . str_pad($q, 3, '0', STR_PAD_LEFT);
            $tabungan = Tabungan::where('nomor_rekening', $padded)
                ->with(['murid.ruangans.level', 'murid.ruanganMasuk.level', 'ustadz', 'ruangan', 'periodeTabungan'])
                ->first();
        }

        // 3. Jika belum ketemu, coba cari berdasarkan NISM murid atau NIGM ustadz
        if (!$tabungan) {
            $tabungan = Tabungan::whereHas('murid', function ($m) use ($q) {
                $m->where('nism', $q)->orWhere('nisn', $q);
            })->orWhereHas('ustadz', function ($u) use ($q) {
                $u->where('nigm', $q)->orWhere('kode_ustadz', $q);
            })->with(['murid.ruangans.level', 'murid.ruanganMasuk.level', 'ustadz', 'ruangan', 'periodeTabungan'])
                ->first();
        }

        if (!$tabungan) {
            return response()->json([
                'status' => 'not_found',
                'message' => "Buku rekening dengan nomor/barcode \"{$q}\" belum terdaftar atau tidak ditemukan."
            ]);
        }

        // Ekstrak identitas spesifik
        $identitasTambahan = '';
        if ($tabungan->jenis_nasabah === 'Murid' && $tabungan->murid) {
            $kelas = $tabungan->murid->ruangans->pluck('nama_ruangan')->implode(', ') ?: ($tabungan->murid->ruanganMasuk->nama_ruangan ?? '-');
            $identitasTambahan = "Kelas: {$kelas} • NISM: " . ($tabungan->murid->nism ?? '-');
        } elseif ($tabungan->jenis_nasabah === 'Ustadz' && $tabungan->ustadz) {
            $identitasTambahan = "NIGM: " . ($tabungan->ustadz->nigm ?? $tabungan->ustadz->kode_ustadz ?? '-');
        } elseif ($tabungan->jenis_nasabah === 'Kas Ruangan' && $tabungan->ruangan) {
            $identitasTambahan = "Kas Ruangan: " . $tabungan->ruangan->nama_ruangan;
        } elseif ($tabungan->jenis_nasabah === 'Umum') {
            $identitasTambahan = "Kontak: " . ($tabungan->kontak_umum ?? '-');
        }

        return response()->json([
            'status' => 'found',
            'data' => [
                'id' => $tabungan->id,
                'nomor_rekening' => $tabungan->nomor_rekening,
                'nama_nasabah' => $tabungan->nama_nasabah,
                'jenis_nasabah' => $tabungan->jenis_nasabah,
                'identitas_nasabah' => $tabungan->identitas_nasabah,
                'identitas_tambahan' => $identitasTambahan,
                'nama_rekening' => $tabungan->nama_rekening,
                'periode_nama' => $tabungan->periodeTabungan?->nama_periode ?? 'Tabungan Bebas',
                'saldo' => (float) $tabungan->saldo,
                'formatted_saldo' => 'Rp ' . number_format($tabungan->saldo, 0, ',', '.'),
                'status' => $tabungan->status,
            ]
        ]);
    }

    /**
     * Proses Setor Tunai Langsung
     */
    public function setorTunai(Request $request)
    {
        $request->validate([
            'tabungan_id' => 'required|exists:tabungans,id',
            'nominal' => 'required|numeric|min:1000',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string|max:255',
        ]);

        try {
            $trx = $this->tabunganService->setorTunai(
                (int) $request->tabungan_id,
                (float) $request->nominal,
                Auth::id(),
                $request->tanggal,
                $request->keterangan
            );

            return redirect()->route('tabungan.setor.index')
                ->with('success', "Alhamdulillah! Setoran sebesar Rp " . number_format($trx->nominal_bersih, 0, ',', '.') . " untuk rekening {$trx->tabungan->nomor_rekening} ({$trx->tabungan->nama_nasabah}) berhasil dicatat! Kode: {$trx->kode_transaksi}");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memproses setoran: ' . $e->getMessage());
        }
    }

    /**
     * Proses Penarikan Tunai Langsung oleh Admin
     */
    public function tarikTunai(Request $request)
    {
        $request->validate([
            'tabungan_id' => 'required|exists:tabungans,id',
            'nominal' => 'required|numeric|min:1000',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string|max:255',
        ]);

        try {
            $trx = $this->tabunganService->tarikTunai(
                (int) $request->tabungan_id,
                (float) $request->nominal,
                Auth::id(),
                $request->tanggal,
                $request->keterangan
            );

            $pesan = "Penarikan sebesar Rp " . number_format($trx->nominal_kotor, 0, ',', '.') .
                " berhasil! (Potongan: Rp " . number_format($trx->nominal_potongan, 0, ',', '.') .
                ", Bersih diserahkan: Rp " . number_format($trx->nominal_bersih, 0, ',', '.') . ")";

            return back()->with('success', $pesan);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses penarikan: ' . $e->getMessage());
        }
    }

    /**
     * Modal Form Edit Transaksi Setoran Tunai (AJAX)
     */
    public function editTransaksiSetor(Request $request, $id)
    {
        $transaksi = TransaksiTabungan::with(['tabungan.murid', 'tabungan.ustadz', 'tabungan.ruangan'])->findOrFail($id);

        if ($request->ajax()) {
            return view('tabungan.setor.modal_edit', compact('transaksi'));
        }

        return redirect()->route('tabungan.setor.index');
    }

    /**
     * Update Transaksi Setoran Tunai
     */
    public function updateTransaksiSetor(Request $request, $id)
    {
        $request->validate([
            'nominal' => 'required|numeric|min:1000',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string|max:255',
        ], [
            'nominal.required' => 'Nominal setoran wajib diisi.',
            'nominal.min' => 'Nominal setoran minimal Rp 1.000.',
            'tanggal.required' => 'Tanggal setoran wajib diisi.',
        ]);

        try {
            $trx = $this->tabunganService->updateSetorTunai(
                (int) $id,
                (float) $request->nominal,
                $request->tanggal,
                $request->keterangan,
                Auth::id()
            );

            $pesan = "Alhamdulillah! Setoran ({$trx->kode_transaksi}) berhasil diperbarui menjadi Rp " . number_format($trx->nominal_bersih, 0, ',', '.') . "!";

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => $pesan,
                ], 200);
            }

            return back()->with('success', $pesan);
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal memperbarui setoran: ' . $e->getMessage(),
                ], 422);
            }

            return back()->with('error', 'Gagal memperbarui setoran: ' . $e->getMessage());
        }
    }

    /**
     * Hapus Transaksi Setoran Tunai
     */
    public function destroyTransaksiSetor(Request $request, $id)
    {
        try {
            $result = $this->tabunganService->hapusSetorTunai((int) $id);

            $pesan = "Setoran ({$result['kode_transaksi']}) sebesar Rp " . number_format($result['nominal'], 0, ',', '.') . " untuk rekening {$result['tabungan']->nomor_rekening} berhasil dihapus. Saldo telah disesuaikan.";

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => $pesan,
                ], 200);
            }

            return back()->with('success', $pesan);
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal menghapus setoran: ' . $e->getMessage(),
                ], 422);
            }

            return back()->with('error', 'Gagal menghapus setoran: ' . $e->getMessage());
        }
    }

    /**
     * Cetak Buku Tabungan / Mutasi
     */
    public function cetakBukuTabungan($id)
    {
        $tabungan = Tabungan::with(['murid', 'ustadz', 'ruangan', 'periodeTabungan'])->findOrFail($id);
        $transaksis = $tabungan->transaksis()->with('petugas')->orderBy('tanggal', 'asc')->orderBy('id', 'asc')->get();

        return view('tabungan.rekening.cetak_buku', compact('tabungan', 'transaksis'));
    }
}
