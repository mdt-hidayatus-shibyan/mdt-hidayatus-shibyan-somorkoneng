<?php

namespace App\Http\Controllers\Koperasi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Koperasi\StokKoperasiRequest;
use App\Models\Koperasi\MutasiStokKoperasi;
use App\Models\Koperasi\ProdukKoperasi;
use App\Services\Koperasi\KoperasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StokKoperasiController extends Controller
{
    protected $koperasiService;

    public function __construct(KoperasiService $koperasiService)
    {
        $this->koperasiService = $koperasiService;
    }

    /**
     * Riwayat Mutasi & Kartu Stok Produk
     */
    public function index(Request $request)
    {
        $query = MutasiStokKoperasi::with(['produk.kategori', 'petugas']);

        if ($request->filled('produk_id')) {
            $query->where('produk_id', $request->produk_id);
        }

        if ($request->filled('jenis_mutasi')) {
            $query->where('jenis_mutasi', $request->jenis_mutasi);
        }

        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_akhir')) {
            $query->whereBetween('created_at', [$request->tanggal_mulai . ' 00:00:00', $request->tanggal_akhir . ' 23:59:59']);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('referensi', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%")
                    ->orWhereHas('produk', function ($pq) use ($search) {
                        $pq->where('nama_produk', 'like', "%{$search}%")
                            ->orWhere('kode_produk', 'like', "%{$search}%");
                    });
            });
        }

        $mutasis = $query->orderBy('id', 'desc')->paginate(25)->withQueryString();
        $produks = ProdukKoperasi::where('status', 'Aktif')->orderBy('nama_produk')->get();

        if ($request->ajax() || $request->wantsJson()) {
            return view('koperasi.stok.list', compact('mutasis', 'produks'))->render();
        }

        return view('koperasi.stok.index', compact('mutasis', 'produks'));
    }

    /**
     * Modal Form Restock / Stok Masuk
     */
    public function restockModal()
    {
        $produks = ProdukKoperasi::where('status', 'Aktif')->orderBy('nama_produk')->get();
        return view('koperasi.stok.restock-modal', compact('produks'));
    }

    /**
     * Modal Form Penyesuaian Stock Opname
     */
    public function opnameModal()
    {
        $produks = ProdukKoperasi::where('status', 'Aktif')->orderBy('nama_produk')->get();
        return view('koperasi.stok.opname-modal', compact('produks'));
    }

    /**
     * Proses Simpan Stok Masuk atau Stock Opname
     */
    public function store(StokKoperasiRequest $request)
    {
        $data = $request->validated();

        try {
            if ($data['tipe_aksi'] === 'Masuk') {
                $mutasi = $this->koperasiService->tambahStokMasuk(
                    (int) $data['produk_id'],
                    (int) $data['jumlah'],
                    $data['harga_beli'] ? (float) $data['harga_beli'] : null,
                    $data['referensi'] ?? null,
                    $data['keterangan'],
                    Auth::id()
                );
                $pesan = "Penambahan stok masuk sebanyak {$data['jumlah']} {$mutasi->produk->satuan} berhasil dicatat.";
            } else {
                $mutasi = $this->koperasiService->penyesuaianStockOpname(
                    (int) $data['produk_id'],
                    (int) $data['stok_fisik'],
                    $data['keterangan'],
                    Auth::id()
                );
                $pesan = "Penyesuaian stok opname produk '{$mutasi->produk->nama_produk}' menjadi {$data['stok_fisik']} {$mutasi->produk->satuan} berhasil disimpan.";
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $pesan,
                ]);
            }

            return back()->with('success', $pesan);
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return back()->with('error', "Gagal memproses stok: " . $e->getMessage());
        }
    }
}
