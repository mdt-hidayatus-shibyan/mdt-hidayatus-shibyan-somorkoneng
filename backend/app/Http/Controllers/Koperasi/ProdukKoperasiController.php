<?php

namespace App\Http\Controllers\Koperasi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Koperasi\ProdukKoperasiRequest;
use App\Models\Koperasi\KategoriProduk;
use App\Models\Koperasi\MutasiStokKoperasi;
use App\Models\Koperasi\ProdukKoperasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProdukKoperasiController extends Controller
{
    /**
     * Daftar Master Produk & Kategori Koperasi
     */
    public function index(Request $request)
    {
        $query = ProdukKoperasi::with('kategori');

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('stok_filter')) {
            if ($request->stok_filter === 'menipis') {
                $query->whereRaw('stok <= stok_minimum');
            } elseif ($request->stok_filter === 'habis') {
                $query->where('stok', '<=', 0);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_produk', 'like', "%{$search}%")
                    ->orWhere('kode_produk', 'like', "%{$search}%");
            });
        }

        $produks = $query->orderBy('nama_produk')->paginate(20)->withQueryString();
        $kategoris = KategoriProduk::where('is_active', true)->orderBy('nama_kategori')->get();

        if ($request->ajax() || $request->wantsJson()) {
            return view('koperasi.produk.list', compact('produks', 'kategoris'))->render();
        }

        return view('koperasi.produk.index', compact('produks', 'kategoris'));
    }

    /**
     * Form Modal Tambah Produk
     */
    public function create()
    {
        $kategoris = KategoriProduk::where('is_active', true)->orderBy('nama_kategori')->get();
        $generatedBarcode = ProdukKoperasi::generateBarcode();

        return view('koperasi.produk.form', compact('kategoris', 'generatedBarcode'));
    }

    /**
     * Simpan Produk Baru
     */
    public function store(ProdukKoperasiRequest $request)
    {
        $data = $request->validated();

        if (empty($data['kode_produk'])) {
            $data['kode_produk'] = ProdukKoperasi::generateBarcode($data['kategori_id'] ?? null);
        }

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('uploads/koperasi/produk', 'public');
        }

        $stokAwal = (int) ($data['stok'] ?? 0);
        $produk = ProdukKoperasi::create($data);

        // Catat stok awal ke mutasi
        if ($stokAwal > 0) {
            MutasiStokKoperasi::create([
                'produk_id' => $produk->id,
                'jenis_mutasi' => 'Stok_Awal',
                'jumlah' => $stokAwal,
                'stok_sebelum' => 0,
                'stok_sesudah' => $stokAwal,
                'referensi' => 'SALDO-AWAL',
                'keterangan' => "Saldo stok awal produk baru ({$stokAwal} {$produk->satuan})",
                'petugas_id' => Auth::id(),
            ]);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Produk '{$produk->nama_produk}' berhasil ditambahkan.",
                'produk' => $produk,
            ]);
        }

        return redirect()->route('koperasi.produk.index')->with('success', "Produk '{$produk->nama_produk}' berhasil ditambahkan.");
    }

    /**
     * Form Modal Edit Produk
     */
    public function edit(ProdukKoperasi $produk)
    {
        $kategoris = KategoriProduk::where('is_active', true)->orderBy('nama_kategori')->get();

        return view('koperasi.produk.form', compact('produk', 'kategoris'));
    }

    /**
     * Update Produk
     */
    public function update(ProdukKoperasiRequest $request, ProdukKoperasi $produk)
    {
        $data = $request->validated();
        unset($data['stok']); // Perubahan stok harus lewat menu Stok / Opname

        if ($request->hasFile('foto')) {
            if ($produk->foto && Storage::disk('public')->exists($produk->foto)) {
                Storage::disk('public')->delete($produk->foto);
            }
            $data['foto'] = $request->file('foto')->store('uploads/koperasi/produk', 'public');
        }

        $produk->update($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Produk '{$produk->nama_produk}' berhasil diperbarui.",
                'produk' => $produk,
            ]);
        }

        return redirect()->route('koperasi.produk.index')->with('success', "Produk '{$produk->nama_produk}' berhasil diperbarui.");
    }

    /**
     * Hapus Produk (Cek relasi terlebih dahulu)
     */
    public function destroy(Request $request, ProdukKoperasi $produk)
    {
        if ($produk->penjualanDetails()->exists()) {
            $msg = "Produk '{$produk->nama_produk}' tidak dapat dihapus karena sudah memiliki riwayat transaksi penjualan. Silakan nonaktifkan statusnya.";
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => $msg], 422);
            }
            return back()->with('error', $msg);
        }

        if ($produk->paketItems()->exists()) {
            $msg = "Produk '{$produk->nama_produk}' masih terdaftar dalam Paket Bundling. Harap hapus item ini dari paket terlebih dahulu.";
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => $msg], 422);
            }
            return back()->with('error', $msg);
        }

        if ($produk->foto && Storage::disk('public')->exists($produk->foto)) {
            Storage::disk('public')->delete($produk->foto);
        }

        $nama = $produk->nama_produk;
        $produk->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Produk '{$nama}' berhasil dihapus.",
            ]);
        }

        return redirect()->route('koperasi.produk.index')->with('success', "Produk '{$nama}' berhasil dihapus.");
    }

    /**
     * Toggle Status Aktif/Nonaktif Produk via AJAX
     */
    public function toggleStatus(ProdukKoperasi $produk)
    {
        $newStatus = $produk->status === 'Aktif' ? 'Nonaktif' : 'Aktif';
        $produk->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'status' => $newStatus,
            'message' => "Status produk '{$produk->nama_produk}' diubah menjadi {$newStatus}."
        ]);
    }

    /**
     * Simpan Kategori Baru via AJAX / Form Modal
     */
    public function storeKategori(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:100|unique:kategori_produks,nama_kategori',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $kategori = KategoriProduk::create([
            'nama_kategori' => $request->nama_kategori,
            'slug' => Str::slug($request->nama_kategori),
            'keterangan' => $request->keterangan,
            'is_active' => true,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Kategori '{$kategori->nama_kategori}' berhasil ditambahkan.",
                'kategori' => $kategori,
            ]);
        }

        return back()->with('success', "Kategori '{$kategori->nama_kategori}' berhasil ditambahkan.");
    }
}
