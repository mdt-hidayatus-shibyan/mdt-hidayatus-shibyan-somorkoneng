<?php

namespace App\Http\Controllers\Koperasi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Koperasi\PembelianKoperasiRequest;
use App\Models\Koperasi\KategoriProduk;
use App\Models\Koperasi\PembelianKoperasi;
use App\Models\Koperasi\ProdukKoperasi;
use App\Models\User;
use App\Services\Koperasi\KoperasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PembelianKoperasiController extends Controller
{
    protected $koperasiService;

    public function __construct(KoperasiService $koperasiService)
    {
        $this->koperasiService = $koperasiService;
    }

    /**
     * Riwayat Faktur Pembelian (Kulakan)
     */
    public function index(Request $request)
    {
        $query = PembelianKoperasi::with(['petugas', 'petugasPelunasan', 'details.produk']);

        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_akhir')) {
            $query->whereBetween('tanggal', [$request->tanggal_mulai . ' 00:00:00', $request->tanggal_akhir . ' 23:59:59']);
        } elseif ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        if ($request->filled('metode_pembayaran')) {
            $query->where('metode_pembayaran', $request->metode_pembayaran);
        }

        if ($request->filled('status_pembayaran')) {
            $query->where('status_pembayaran', $request->status_pembayaran);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_faktur', 'like', "%{$search}%")
                    ->orWhere('nomor_faktur_supplier', 'like', "%{$search}%")
                    ->orWhere('supplier', 'like', "%{$search}%");
            });
        }

        $pembelians = $query->orderBy('tanggal', 'desc')->orderBy('id', 'desc')->paginate(20)->withQueryString();

        // Ringkasan Statistik
        $thisMonth = date('Y-m');
        $totalBelanjaBulanIni = PembelianKoperasi::where('tanggal', 'like', "{$thisMonth}%")->where('status', 'Selesai')->sum('total_nominal');
        $totalHutangSupplier = PembelianKoperasi::where('status', 'Selesai')->where('status_pembayaran', 'Belum_Lunas')->sum('sisa_hutang');
        $countHutangSupplier = PembelianKoperasi::where('status', 'Selesai')->where('status_pembayaran', 'Belum_Lunas')->count();
        $totalTrxKulakan = (clone $query)->count();
        $totalNominalFiltered = (clone $query)->where('status', 'Selesai')->sum('total_nominal');

        return view('koperasi.pembelian.index', compact(
            'pembelians',
            'totalBelanjaBulanIni',
            'totalHutangSupplier',
            'countHutangSupplier',
            'totalTrxKulakan',
            'totalNominalFiltered'
        ));
    }

    /**
     * Form Transaksi Pembelian / Kulakan Baru
     */
    public function create()
    {
        $produks = ProdukKoperasi::with('kategori')
            ->orderBy('nama_produk')
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'kode_produk' => $p->kode_produk,
                    'nama_produk' => $p->nama_produk,
                    'kategori' => $p->kategori?->nama_kategori ?? '-',
                    'satuan' => $p->satuan,
                    'stok' => (int) $p->stok,
                    'harga_beli' => (float) $p->harga_beli,
                    'harga_jual' => (float) $p->harga_jual,
                    'foto_url' => $p->foto_url,
                ];
            });

        $kategoris = KategoriProduk::where('is_active', true)->orderBy('nama_kategori')->get();
        $nomorFakturPreview = PembelianKoperasi::generateNomorFaktur();

        return view('koperasi.pembelian.create', compact('produks', 'kategoris', 'nomorFakturPreview'));
    }

    /**
     * Simpan Transaksi Pembelian Baru
     */
    public function store(PembelianKoperasiRequest $request)
    {
        try {
            $foto = $request->hasFile('foto_faktur') ? $request->file('foto_faktur') : null;
            $pembelian = $this->koperasiService->simpanPembelian($request->validated(), Auth::id(), $foto);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Faktur pembelian {$pembelian->nomor_faktur} berhasil disimpan dan stok produk telah bertambah.",
                    'redirect' => route('koperasi.pembelian.show', $pembelian->id),
                    'pembelian_id' => $pembelian->id,
                ]);
            }

            return redirect()->route('koperasi.pembelian.show', $pembelian->id)
                ->with('success', "Faktur pembelian {$pembelian->nomor_faktur} berhasil disimpan.");
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return back()->withInput()->with('error', "Gagal menyimpan pembelian: " . $e->getMessage());
        }
    }

    /**
     * Detail Faktur Pembelian (Kulakan)
     */
    public function show(int $id)
    {
        $pembelian = PembelianKoperasi::with([
            'petugas',
            'petugasPelunasan',
            'details.produk.kategori'
        ])->findOrFail($id);

        return view('koperasi.pembelian.show', compact('pembelian'));
    }

    /**
     * Cetak Bukti Faktur Penerimaan Barang Kulakan
     */
    public function cetak(int $id)
    {
        $pembelian = PembelianKoperasi::with([
            'petugas',
            'petugasPelunasan',
            'details.produk'
        ])->findOrFail($id);

        return view('koperasi.pembelian.cetak', compact('pembelian'));
    }

    /**
     * AJAX / POST: Pelunasan Hutang ke Supplier
     */
    public function lunasi(Request $request, int $id)
    {
        $request->validate([
            'metode_pelunasan' => 'required|in:Tunai_Kas,Transfer_Bank',
            'nominal_bayar' => 'nullable|numeric|min:0',
            'catatan_pelunasan' => 'nullable|string|max:255',
        ]);

        try {
            $pembelian = $this->koperasiService->lunasiHutangSupplier($id, $request->all(), Auth::id());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Hutang faktur {$pembelian->nomor_faktur} kepada supplier {$pembelian->supplier} berhasil dilunasi.",
                    'redirect' => route('koperasi.pembelian.show', $pembelian->id),
                ]);
            }

            return back()->with('success', "Hutang faktur {$pembelian->nomor_faktur} berhasil dilunasi.");
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return back()->with('error', "Gagal memproses pelunasan hutang supplier: " . $e->getMessage());
        }
    }

    /**
     * AJAX / POST: Batalkan / Void Transaksi Pembelian (Kurangi Kembali Stok)
     */
    public function batal(Request $request, int $id)
    {
        $request->validate([
            'alasan' => 'required|string|max:255',
        ]);

        try {
            $pembelian = $this->koperasiService->batalPembelian($id, $request->alasan, Auth::id());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Faktur pembelian {$pembelian->nomor_faktur} berhasil dibatalkan dan penambahan stok telah dikurangi kembali.",
                    'redirect' => route('koperasi.pembelian.show', $pembelian->id),
                ]);
            }

            return back()->with('success', "Faktur pembelian {$pembelian->nomor_faktur} berhasil dibatalkan.");
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return back()->with('error', "Gagal membatalkan pembelian: " . $e->getMessage());
        }
    }
}