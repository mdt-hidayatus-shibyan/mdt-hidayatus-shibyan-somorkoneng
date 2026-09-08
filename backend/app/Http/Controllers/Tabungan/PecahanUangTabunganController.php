<?php

namespace App\Http\Controllers\Tabungan;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\Ruangan;
use App\Models\Tabungan\PeriodeTabungan;
use App\Models\Tabungan\Tabungan;
use App\Services\Tabungan\PembagianTabunganService;
use App\Services\Tabungan\TabunganService;
use Illuminate\Http\Request;

class PecahanUangTabunganController extends Controller
{
    protected $pembagianService;
    protected $tabunganService;

    public function __construct(PembagianTabunganService $pembagianService, TabunganService $tabunganService)
    {
        $this->pembagianService = $pembagianService;
        $this->tabunganService = $tabunganService;
    }

    /**
     * Halaman Rincian & Kalkulator Uang Pecahan Kas Fisik Tabungan
     */
    public function index(Request $request)
    {
        $periodeId = $request->filled('periode_id') ? (int) $request->periode_id : null;
        $jenisNasabah = $request->jenis_nasabah; // 'Murid', 'Ustadz', 'Kas Ruangan', 'Umum', or null/'Semua'
        $levelId = $request->filled('level_id') ? (int) $request->level_id : null;
        $ruanganId = $request->filled('ruangan_id') ? (int) $request->ruangan_id : null;
        $statusVerifikasi = $request->status_verifikasi;
        $q = trim($request->q ?? '');

        // Load Master Data Filter
        $periodes = PeriodeTabungan::with('tahunPelajaran')->orderBy('id', 'desc')->get();
        $levels = Level::with('tingkat')->orderBy('id')->get();
        $ruangans = Ruangan::with('level')->orderBy('nama_ruangan')->get();

        // Hitung rekap pecahan
        $rekap = $this->pembagianService->hitungRekapPecahanUang(
            $periodeId,
            $jenisNasabah,
            $levelId,
            $ruanganId,
            $statusVerifikasi,
            $q
        );

        return view('tabungan.pecahan.index', compact(
            'rekap',
            'periodes',
            'levels',
            'ruangans',
            'periodeId',
            'jenisNasabah',
            'levelId',
            'ruanganId',
            'statusVerifikasi',
            'q'
        ));
    }

    /**
     * Cetak Lembar Rekapitulasi Kebutuhan Uang Pecahan (A4)
     */
    public function cetak(Request $request)
    {
        $periodeId = $request->filled('periode_id') ? (int) $request->periode_id : null;
        $jenisNasabah = $request->jenis_nasabah;
        $levelId = $request->filled('level_id') ? (int) $request->level_id : null;
        $ruanganId = $request->filled('ruangan_id') ? (int) $request->ruangan_id : null;
        $statusVerifikasi = $request->status_verifikasi;
        $q = trim($request->q ?? '');

        $rekap = $this->pembagianService->hitungRekapPecahanUang(
            $periodeId,
            $jenisNasabah,
            $levelId,
            $ruanganId,
            $statusVerifikasi,
            $q
        );

        $filterInfo = [
            'level' => $levelId ? Level::find($levelId)?->nama_level : 'Semua Level',
            'ruangan' => $ruanganId ? Ruangan::find($ruanganId)?->nama_ruangan : 'Semua Ruangan',
            'nasabah' => $jenisNasabah ?: 'Semua Kategori Nasabah',
        ];

        return view('tabungan.pecahan.cetak', compact('rekap', 'filterInfo'));
    }

    /**
     * Cetak Slip Pecahan Uang Individual (Untuk Diselipkan di Buku Tabungan)
     */
    public function cetakSlip(Request $request, int $id)
    {
        $tabungan = Tabungan::with(['murid.ruangans.level.tingkat', 'ustadz', 'ruangan.level.tingkat', 'periodeTabungan', 'diverifikasiOleh'])
            ->findOrFail($id);

        $kalkulasi = $this->tabunganService->hitungPotongan($tabungan);

        // Hitung pecahan
        $denominasiList = [
            '100000' => ['label' => 'Rp 100.000', 'nilai' => 100000, 'tipe' => 'Lembar'],
            '50000'  => ['label' => 'Rp 50.000',  'nilai' => 50000,  'tipe' => 'Lembar'],
            '20000'  => ['label' => 'Rp 20.000',  'nilai' => 20000,  'tipe' => 'Lembar'],
            '10000'  => ['label' => 'Rp 10.000',  'nilai' => 10000,  'tipe' => 'Lembar'],
            '5000'   => ['label' => 'Rp 5.000',   'nilai' => 5000,   'tipe' => 'Lembar'],
            '2000'   => ['label' => 'Rp 2.000',   'nilai' => 2000,   'tipe' => 'Lembar'],
            '1000'   => ['label' => 'Rp 1.000',   'nilai' => 1000,   'tipe' => 'Lembar'],
            '500'    => ['label' => 'Rp 500',     'nilai' => 500,    'tipe' => 'Koin'],
            '200'    => ['label' => 'Rp 200',     'nilai' => 200,    'tipe' => 'Koin'],
            '100'    => ['label' => 'Rp 100',     'nilai' => 100,    'tipe' => 'Koin'],
        ];

        $hakBersih = (float) ($kalkulasi['saldo_bersih_total'] ?? $tabungan->saldo);
        $sisa = $hakBersih;
        $pecahan = [];
        foreach ($denominasiList as $k => $info) {
            $lembar = 0;
            $val = $info['nilai'];
            if ($sisa >= $val) {
                $lembar = (int) floor($sisa / $val);
                $sisa -= ($lembar * $val);
            }
            $pecahan[$k] = $lembar;
        }

        return view('tabungan.pecahan.cetak_slip', compact('tabungan', 'kalkulasi', 'pecahan', 'denominasiList'));
    }

    /**
     * Cetak Slip Pecahan Massal (Multiple Slip per Halaman A4 untuk diselipkan ke buku)
     */
    public function cetakSlipMassal(Request $request)
    {
        $periodeId = $request->filled('periode_id') ? (int) $request->periode_id : null;
        $jenisNasabah = $request->jenis_nasabah;
        $levelId = $request->filled('level_id') ? (int) $request->level_id : null;
        $ruanganId = $request->filled('ruangan_id') ? (int) $request->ruangan_id : null;
        $statusVerifikasi = $request->status_verifikasi;
        $q = trim($request->q ?? '');

        $rekap = $this->pembagianService->hitungRekapPecahanUang(
            $periodeId,
            $jenisNasabah,
            $levelId,
            $ruanganId,
            $statusVerifikasi,
            $q
        );

        return view('tabungan.pecahan.cetak_slip_massal', compact('rekap'));
    }
}
