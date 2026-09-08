<?php

namespace App\Http\Controllers\Koperasi;

use App\Http\Controllers\Controller;
use App\Models\Koperasi\PenjualanDetailKoperasi;
use App\Models\Koperasi\PenjualanKoperasi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanKoperasiController extends Controller
{
    /**
     * Halaman Utama Laporan Keuangan & Penjualan Koperasi
     */
    public function index(Request $request)
    {
        $data = $this->prepareLaporanData($request);

        return view('koperasi.laporan.index', $data);
    }

    /**
     * Cetak Laporan Rekap Penjualan (A4 Print / PDF)
     */
    public function cetak(Request $request)
    {
        $data = $this->prepareLaporanData($request);

        return view('koperasi.laporan.cetak', $data);
    }

    /**
     * Helper menyiapkan kalkulasi metrik laporan
     */
    protected function prepareLaporanData(Request $request): array
    {
        $periodeType = $request->periode_type ?? 'bulan'; // 'hari', 'bulan', 'custom'
        $tglMulai = null;
        $tglAkhir = null;

        if ($periodeType === 'hari') {
            $tgl = $request->tanggal ?: date('Y-m-d');
            $tglMulai = $tgl . ' 00:00:00';
            $tglAkhir = $tgl . ' 23:59:59';
            $judulPeriode = "Hari " . Carbon::parse($tgl)->translatedFormat('l, d F Y');
        } elseif ($periodeType === 'custom') {
            $tglMulai = ($request->tanggal_mulai ?: date('Y-m-01')) . ' 00:00:00';
            $tglAkhir = ($request->tanggal_akhir ?: date('Y-m-d')) . ' 23:59:59';
            $judulPeriode = Carbon::parse($tglMulai)->translatedFormat('d F Y') . " s/d " . Carbon::parse($tglAkhir)->translatedFormat('d F Y');
        } else {
            // Bulan
            $bulan = $request->bulan ?: date('Y-m');
            $tglMulai = $bulan . '-01 00:00:00';
            $tglAkhir = Carbon::parse($bulan . '-01')->endOfMonth()->format('Y-m-d 23:59:59');
            $judulPeriode = "Bulan " . Carbon::parse($bulan . '-01')->translatedFormat('F Y');
        }

        // Query Dasar Penjualan Sukses
        $query = PenjualanKoperasi::with(['petugas', 'murid', 'ustadz', 'details'])
            ->whereBetween('tanggal', [$tglMulai, $tglAkhir])
            ->where('status', 'Selesai');

        if ($request->filled('metode_pembayaran')) {
            $query->where('metode_pembayaran', $request->metode_pembayaran);
        }

        if ($request->filled('jenis_pelanggan')) {
            $query->where('jenis_pelanggan', $request->jenis_pelanggan);
        }

        $penjualans = (clone $query)->orderBy('tanggal', 'desc')->get();

        // 1. Rekapitulasi Finansial
        $totalTrx = $penjualans->count();
        $totalItemTerjual = $penjualans->sum('total_item');
        $totalOmzet = (float) $penjualans->sum('total_akhir');
        $totalHpp = (float) $penjualans->sum('total_hpp');
        $totalDiskon = (float) $penjualans->sum('diskon');
        $totalLabaKotor = max(0, $totalOmzet - $totalHpp);

        // 2. Rekap Berdasarkan Metode Pembayaran
        $rekapMetode = [
            'Tunai' => (float) $penjualans->where('metode_pembayaran', 'Tunai')->sum('total_akhir'),
            'QRIS' => (float) $penjualans->where('metode_pembayaran', 'QRIS')->sum('total_akhir'),
            'Transfer' => (float) $penjualans->where('metode_pembayaran', 'Transfer')->sum('total_akhir'),
            'Potong_Tabungan' => (float) $penjualans->where('metode_pembayaran', 'Potong_Tabungan')->sum('total_akhir'),
            'Hutang' => (float) $penjualans->where('metode_pembayaran', 'Hutang')->sum('total_akhir'),
        ];

        // 3. Rekap Berdasarkan Jenis Pelanggan
        $rekapPelanggan = [
            'Murid' => (float) $penjualans->where('jenis_pelanggan', 'Murid')->sum('total_akhir'),
            'Ustadz' => (float) $penjualans->where('jenis_pelanggan', 'Ustadz')->sum('total_akhir'),
            'Umum' => (float) $penjualans->where('jenis_pelanggan', 'Umum')->sum('total_akhir'),
        ];

        // 4. Top 10 Produk / Paket Terlaris pada Periode Ini
        $penjualanIds = $penjualans->pluck('id')->all();
        $topItem = PenjualanDetailKoperasi::select(
            'kode_item',
            'nama_item',
            'tipe_item',
            'satuan',
            DB::raw('SUM(jumlah) as total_qty'),
            DB::raw('SUM(subtotal) as total_omzet'),
            DB::raw('SUM(keuntungan) as total_profit')
        )
            ->whereIn('penjualan_id', $penjualanIds)
            ->groupBy('kode_item', 'nama_item', 'tipe_item', 'satuan')
            ->orderBy('total_qty', 'desc')
            ->limit(10)
            ->get();

        return compact(
            'periodeType',
            'judulPeriode',
            'tglMulai',
            'tglAkhir',
            'penjualans',
            'totalTrx',
            'totalItemTerjual',
            'totalOmzet',
            'totalHpp',
            'totalDiskon',
            'totalLabaKotor',
            'rekapMetode',
            'rekapPelanggan',
            'topItem'
        );
    }
}
