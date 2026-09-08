<?php

namespace App\Http\Controllers\Koperasi;

use App\Http\Controllers\Controller;
use App\Models\Koperasi\KategoriProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KategoriProdukController extends Controller
{
    /**
     * Tampilkan daftar kategori produk
     */
    public function index(Request $request)
    {
        $kategoris = KategoriProduk::withCount('produks')->orderBy('nama_kategori')->get();

        if ($request->ajax() || $request->wantsJson()) {
            return view('koperasi.kategori.list', compact('kategoris'))->render();
        }

        return view('koperasi.kategori.index', compact('kategoris'));
    }

    /**
     * Form Modal Tambah Kategori
     */
    public function create()
    {
        return view('koperasi.kategori.form');
    }

    /**
     * Simpan Kategori Baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:100|unique:kategori_produks,nama_kategori',
            'icon' => 'nullable|string|max:50',
            'warna' => 'nullable|string|max:30',
            'keterangan' => 'nullable|string|max:255',
            'is_active' => 'nullable',
        ]);

        $kategori = KategoriProduk::create([
            'nama_kategori' => $validated['nama_kategori'],
            'slug' => Str::slug($validated['nama_kategori']),
            'icon' => !empty($validated['icon']) ? $validated['icon'] : 'bi-tag-fill',
            'warna' => !empty($validated['warna']) ? $validated['warna'] : 'emerald',
            'keterangan' => $validated['keterangan'] ?? null,
            'is_active' => $request->has('is_active') ? (bool) $request->is_active : true,
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

    /**
     * Form Modal Edit Kategori
     */
    public function edit(KategoriProduk $kategori)
    {
        return view('koperasi.kategori.form', compact('kategori'));
    }

    /**
     * Update Kategori
     */
    public function update(Request $request, KategoriProduk $kategori)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:100|unique:kategori_produks,nama_kategori,' . $kategori->id,
            'icon' => 'nullable|string|max:50',
            'warna' => 'nullable|string|max:30',
            'keterangan' => 'nullable|string|max:255',
            'is_active' => 'nullable',
        ]);

        $kategori->update([
            'nama_kategori' => $validated['nama_kategori'],
            'slug' => Str::slug($validated['nama_kategori']),
            'icon' => !empty($validated['icon']) ? $validated['icon'] : 'bi-tag-fill',
            'warna' => !empty($validated['warna']) ? $validated['warna'] : 'emerald',
            'keterangan' => $validated['keterangan'] ?? null,
            'is_active' => $request->has('is_active') ? (bool) $request->is_active : $kategori->is_active,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Kategori '{$kategori->nama_kategori}' berhasil diperbarui.",
                'kategori' => $kategori,
            ]);
        }

        return back()->with('success', "Kategori '{$kategori->nama_kategori}' berhasil diperbarui.");
    }

    /**
     * Hapus Kategori
     */
    public function destroy(Request $request, KategoriProduk $kategori)
    {
        if ($kategori->produks()->exists()) {
            $count = $kategori->produks()->count();
            $msg = "Kategori '{$kategori->nama_kategori}' tidak dapat dihapus karena masih digunakan oleh {$count} produk. Pindahkan produk ke kategori lain terlebih dahulu.";
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => $msg], 422);
            }
            return back()->with('error', $msg);
        }

        $nama = $kategori->nama_kategori;
        $kategori->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Kategori '{$nama}' berhasil dihapus.",
            ]);
        }

        return back()->with('success', "Kategori '{$nama}' berhasil dihapus.");
    }

    /**
     * Toggle Status Aktif/Nonaktif
     */
    public function toggleStatus(KategoriProduk $kategori)
    {
        $kategori->update([
            'is_active' => !$kategori->is_active,
        ]);

        $statusText = $kategori->is_active ? 'Aktif' : 'Nonaktif';

        return response()->json([
            'success' => true,
            'is_active' => $kategori->is_active,
            'message' => "Status kategori '{$kategori->nama_kategori}' diubah menjadi {$statusText}.",
        ]);
    }
}
