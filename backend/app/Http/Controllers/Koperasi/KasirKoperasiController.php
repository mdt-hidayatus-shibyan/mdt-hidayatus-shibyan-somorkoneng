<?php

namespace App\Http\Controllers\Koperasi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Koperasi\TransaksiKasirRequest;
use App\Models\Koperasi\KategoriProduk;
use App\Models\Koperasi\PaketKoperasi;
use App\Models\Koperasi\PenjualanKoperasi;
use App\Models\Koperasi\ProdukKoperasi;
use App\Models\Level;
use App\Models\Murid;
use App\Models\Tabungan\Tabungan;
use App\Models\Ustadz;
use App\Services\Koperasi\KoperasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KasirKoperasiController extends Controller
{
    protected $koperasiService;

    public function __construct(KoperasiService $koperasiService)
    {
        $this->koperasiService = $koperasiService;
    }

    /**
     * Layar Utama Antarmuka Kasir POS Toko Koperasi
     */
    public function index(Request $request)
    {
        $kategoris = KategoriProduk::where('is_active', true)->orderBy('nama_kategori')->get();
        $levels = Level::with('tingkat')->orderBy('urutan_level')->get();

        // Ambil produk aktif
        $produks = ProdukKoperasi::with('kategori')
            ->where('status', 'Aktif')
            ->orderBy('nama_produk')
            ->get();

        // Ambil paket bundling aktif
        $pakets = PaketKoperasi::with(['level', 'items.produk'])
            ->where('is_active', true)
            ->orderBy('nama_paket')
            ->get();

        return view('koperasi.pos.index', compact('kategoris', 'levels', 'produks', 'pakets'));
    }

    /**
     * AJAX: Cari Produk atau Paket berdasarkan Barcode / Kode SKU
     */
    public function cariBarcode(Request $request)
    {
        $barcode = trim($request->barcode ?? $request->q ?? '');
        if (!$barcode) {
            return response()->json(['success' => false, 'message' => 'Barcode kosong.'], 400);
        }

        // 1. Cek di tabel Produk Koperasi
        $produk = ProdukKoperasi::with('kategori')
            ->where('kode_produk', $barcode)
            ->where('status', 'Aktif')
            ->first();

        if ($produk) {
            return response()->json([
                'success' => true,
                'tipe' => 'Produk',
                'data' => [
                    'id' => $produk->id,
                    'kode' => $produk->kode_produk,
                    'nama' => $produk->nama_produk,
                    'satuan' => $produk->satuan,
                    'harga_jual' => (float) $produk->harga_jual,
                    'stok' => (int) $produk->stok,
                    'foto_url' => $produk->foto_url,
                    'kategori' => $produk->kategori?->nama_kategori ?? '-',
                ]
            ]);
        }

        // 2. Cek di tabel Paket Bundling
        $paket = PaketKoperasi::with(['level', 'items.produk'])
            ->where('kode_paket', $barcode)
            ->where('is_active', true)
            ->first();

        if ($paket) {
            return response()->json([
                'success' => true,
                'tipe' => 'Paket_Bundling',
                'data' => [
                    'id' => $paket->id,
                    'kode' => $paket->kode_paket,
                    'nama' => $paket->nama_paket,
                    'satuan' => 'paket',
                    'harga_jual' => (float) $paket->harga_paket,
                    'stok' => (int) $paket->stok_tersedia,
                    'foto_url' => $paket->foto_url,
                    'level' => $paket->level?->nama_level ?? 'Semua Kelas',
                    'items_count' => $paket->items->count(),
                ]
            ]);
        }

        return response()->json(['success' => false, 'message' => "Barcode '{$barcode}' tidak ditemukan."], 404);
    }

    /**
     * AJAX: Cari Murid / Ustadz Pelanggan & Cek Saldo Tabungan
     */
    public function cariPelanggan(Request $request)
    {
        $tipe = $request->tipe ?? 'Murid'; // 'Murid' atau 'Ustadz'
        $q = trim($request->q ?? '');

        if (!$q) {
            return response()->json(['success' => true, 'data' => []]);
        }

        if ($tipe === 'Murid') {
            $murids = Murid::with(['ruangans.level', 'ruanganMasuk.level'])
                ->where('nism', 'like', "%{$q}%")
                ->orWhere('nama_lengkap', 'like', "%{$q}%")
                ->limit(8)
                ->get();

            $results = $murids->map(function ($m) {
                $kelas = $m->ruangans->first()?->nama_ruangan ?? ($m->ruanganMasuk?->nama_ruangan ?? '-');
                $levelId = $m->ruangans->first()?->level_id ?? ($m->ruanganMasuk?->level_id ?? null);
                $levelNama = $m->ruangans->first()?->level?->nama_level ?? ($m->ruanganMasuk?->level?->nama_level ?? '-');

                // Cari saldo tabungan aktif jika ada
                $tabungan = Tabungan::where('murid_id', $m->id)->where('status', 'Aktif')->first();

                // Cari tagihan hutang belum lunas jika ada
                $hutangCount = PenjualanKoperasi::where('murid_id', $m->id)->where('status', 'Selesai')->where('status_pembayaran', 'Belum_Lunas')->count();
                $totalHutang = (float) PenjualanKoperasi::where('murid_id', $m->id)->where('status', 'Selesai')->where('status_pembayaran', 'Belum_Lunas')->sum('total_akhir');

                return [
                    'id' => $m->id,
                    'nama' => $m->nama_lengkap,
                    'identitas' => 'NISM: ' . ($m->nism ?: '-'),
                    'kelas' => $kelas,
                    'level_id' => $levelId,
                    'level_nama' => $levelNama,
                    'ada_tabungan' => (bool) $tabungan,
                    'saldo_tabungan' => $tabungan ? (float) $tabungan->saldo : 0.00,
                    'saldo_tabungan_format' => $tabungan ? 'Rp ' . number_format($tabungan->saldo, 0, ',', '.') : 'Rp 0',
                    'nomor_rekening' => $tabungan?->nomor_rekening ?? null,
                    'ada_hutang' => $hutangCount > 0,
                    'total_hutang' => $totalHutang,
                    'total_hutang_format' => 'Rp ' . number_format($totalHutang, 0, ',', '.'),
                    'count_hutang' => $hutangCount,
                ];
            });

            return response()->json(['success' => true, 'data' => $results]);
        } else {
            $ustadzs = Ustadz::where('nigm', 'like', "%{$q}%")
                ->orWhere('nama_lengkap', 'like', "%{$q}%")
                ->limit(8)
                ->get();

            $results = $ustadzs->map(function ($u) {
                $tabungan = Tabungan::where('ustadz_id', $u->id)->where('status', 'Aktif')->first();

                $hutangCount = PenjualanKoperasi::where('ustadz_id', $u->id)->where('status', 'Selesai')->where('status_pembayaran', 'Belum_Lunas')->count();
                $totalHutang = (float) PenjualanKoperasi::where('ustadz_id', $u->id)->where('status', 'Selesai')->where('status_pembayaran', 'Belum_Lunas')->sum('total_akhir');

                return [
                    'id' => $u->id,
                    'nama' => $u->nama_lengkap,
                    'identitas' => 'NIGM: ' . ($u->nigm ?: '-'),
                    'kelas' => 'Dewan Asatidz',
                    'level_id' => null,
                    'level_nama' => null,
                    'ada_tabungan' => (bool) $tabungan,
                    'saldo_tabungan' => $tabungan ? (float) $tabungan->saldo : 0.00,
                    'saldo_tabungan_format' => $tabungan ? 'Rp ' . number_format($tabungan->saldo, 0, ',', '.') : 'Rp 0',
                    'nomor_rekening' => $tabungan?->nomor_rekening ?? null,
                    'ada_hutang' => $hutangCount > 0,
                    'total_hutang' => $totalHutang,
                    'total_hutang_format' => 'Rp ' . number_format($totalHutang, 0, ',', '.'),
                    'count_hutang' => $hutangCount,
                ];
            });

            return response()->json(['success' => true, 'data' => $results]);
        }
    }

    /**
     * AJAX: Ambil Paket Rekomendasi berdasarkan Level / Kelas Murid
     */
    public function getPaketRekomendasi(Request $request)
    {
        $levelId = $request->level_id;
        if (!$levelId) {
            return response()->json(['success' => true, 'data' => []]);
        }

        $pakets = PaketKoperasi::with(['level', 'items.produk'])
            ->where('is_active', true)
            ->where('level_id', $levelId)
            ->get();

        $results = $pakets->map(function ($p) {
            return [
                'id' => $p->id,
                'kode' => $p->kode_paket,
                'nama' => $p->nama_paket,
                'harga_jual' => (float) $p->harga_paket,
                'harga_format' => 'Rp ' . number_format($p->harga_paket, 0, ',', '.'),
                'stok' => (int) $p->stok_tersedia,
                'foto_url' => $p->foto_url,
                'items_count' => $p->items->count(),
                'level' => $p->level?->nama_level ?? '-',
            ];
        });

        return response()->json(['success' => true, 'data' => $results]);
    }

    /**
     * AJAX / POST: Proses Checkout Transaksi Kasir POS
     */
    public function checkout(TransaksiKasirRequest $request)
    {
        try {
            $penjualan = $this->koperasiService->checkoutPenjualan($request->validated(), Auth::id());

            return response()->json([
                'success' => true,
                'message' => "Transaksi {$penjualan->nomor_nota} berhasil diproses.",
                'penjualan_id' => $penjualan->id,
                'nomor_nota' => $penjualan->nomor_nota,
                'total_akhir' => (float) $penjualan->total_akhir,
                'total_akhir_format' => 'Rp ' . number_format($penjualan->total_akhir, 0, ',', '.'),
                'nominal_bayar' => (float) $penjualan->nominal_bayar,
                'kembalian' => (float) $penjualan->kembalian,
                'kembalian_format' => 'Rp ' . number_format($penjualan->kembalian, 0, ',', '.'),
                'metode_pembayaran' => $penjualan->metode_pembayaran,
                'cetak_url' => route('koperasi.kasir.struk', $penjualan->id),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Halaman Cetak Struk Thermal Kasir (58mm / 80mm)
     */
    public function struk(int $id)
    {
        $penjualan = PenjualanKoperasi::with([
            'petugas',
            'murid.ruangans.level',
            'ustadz',
            'tabungan',
            'details.produk',
            'details.paket.items.produk'
        ])->findOrFail($id);

        return view('koperasi.pos.struk', compact('penjualan'));
    }
}
