<?php

namespace App\Http\Controllers\Koperasi;

use App\Http\Controllers\Controller;
use App\Models\Koperasi\MutasiStokKoperasi;
use App\Models\Koperasi\PaketKoperasi;
use App\Models\Koperasi\PenjualanKoperasi;
use App\Models\Koperasi\ProdukKoperasi;
use App\Services\Koperasi\KoperasiService;
use Illuminate\Http\Request;

class KoperasiDashboardController extends Controller
{
    protected $koperasiService;

    public function __construct(KoperasiService $koperasiService)
    {
        $this->koperasiService = $koperasiService;
    }

    public function index(Request $request)
    {
        $ringkasan = $this->koperasiService->getRingkasanDashboard();

        // 10 Transaksi Penjualan Terakhir
        $transaksiTerbaru = PenjualanKoperasi::with(['petugas', 'murid', 'ustadz', 'details'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        // 5 Produk Terlaris Bulan Ini
        $thisMonth = date('Y-m');
        $topProduk = \App\Models\Koperasi\PenjualanDetailKoperasi::select(
            'kode_item',
            'nama_item',
            'tipe_item',
            \Illuminate\Support\Facades\DB::raw('SUM(jumlah) as total_terjual'),
            \Illuminate\Support\Facades\DB::raw('SUM(subtotal) as total_nominal')
        )
            ->whereHas('penjualan', function ($q) use ($thisMonth) {
                $q->where('tanggal', 'like', "{$thisMonth}%")->where('status', 'Selesai');
            })
            ->groupBy('kode_item', 'nama_item', 'tipe_item')
            ->orderBy('total_terjual', 'desc')
            ->limit(5)
            ->get();

        // Produk dengan Stok Menipis (<= stok_minimum)
        $stokMenipis = ProdukKoperasi::with('kategori')
            ->whereRaw('stok <= stok_minimum')
            ->orderBy('stok', 'asc')
            ->limit(8)
            ->get();

        // Paket Bundling Aktif
        $paketAktif = PaketKoperasi::with('level')->where('is_active', true)->limit(5)->get();

        return view('koperasi.dashboard', compact(
            'ringkasan',
            'transaksiTerbaru',
            'topProduk',
            'stokMenipis',
            'paketAktif'
        ));
    }
}
