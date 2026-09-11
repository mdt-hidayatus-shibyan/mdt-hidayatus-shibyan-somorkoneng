<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Keuangan\KategoriKeuanganRequest;
use App\Models\Keuangan\KategoriKeuangan;
use Illuminate\Http\Request;

class KategoriKeuanganController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $jenis = $request->input('jenis');

        $kategoris = KategoriKeuangan::with(['children', 'parent'])
            ->when($search, function ($query, $search) {
                return $query->where('nama_kategori', 'like', "%{$search}%")
                    ->orWhere('kode_kategori', 'like', "%{$search}%");
            })
            ->when($jenis, function ($query, $jenis) {
                return $query->where('jenis', $jenis);
            })
            ->whereNull('parent_id')
            ->orderBy('jenis', 'asc')
            ->orderBy('kode_kategori', 'asc')
            ->get();

        $totalKategori = KategoriKeuangan::count();
        $totalPemasukan = KategoriKeuangan::where('jenis', 'pemasukan')->count();
        $totalPengeluaran = KategoriKeuangan::where('jenis', 'pengeluaran')->count();
        $totalSimpanan = KategoriKeuangan::where('jenis', 'simpanan')->count();

        if ($request->ajax() && $request->has('table_only')) {
            return view('keuangan.kategori.list', compact('kategoris'));
        }

        return view('keuangan.kategori.index', compact('kategoris', 'totalKategori', 'totalPemasukan', 'totalPengeluaran', 'totalSimpanan'));
    }

    public function create(Request $request)
    {
        $parentList = KategoriKeuangan::whereNull('parent_id')->orderBy('nama_kategori', 'asc')->get();
        return view('keuangan.kategori.form', compact('parentList'));
    }

    public function store(KategoriKeuanganRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);

        // Jika memiliki parent, sesuaikan jenis mengikuti parent
        if (!empty($data['parent_id'])) {
            $parent = KategoriKeuangan::find($data['parent_id']);
            if ($parent) {
                $data['jenis'] = $parent->jenis;
            }
        }

        KategoriKeuangan::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Kategori Keuangan berhasil ditambahkan!'
        ]);
    }

    public function edit(Request $request, $id)
    {
        $kategori = KategoriKeuangan::findOrFail($id);
        $parentList = KategoriKeuangan::whereNull('parent_id')
            ->where('id', '!=', $id)
            ->orderBy('nama_kategori', 'asc')
            ->get();

        return view('keuangan.kategori.form', compact('kategori', 'parentList'));
    }

    public function update(KategoriKeuanganRequest $request, $id)
    {
        $kategori = KategoriKeuangan::findOrFail($id);
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);

        if (!empty($data['parent_id'])) {
            $parent = KategoriKeuangan::find($data['parent_id']);
            if ($parent) {
                $data['jenis'] = $parent->jenis;
            }
        }

        $kategori->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Kategori Keuangan berhasil diperbarui!'
        ]);
    }

    public function destroy($id)
    {
        $kategori = KategoriKeuangan::findOrFail($id);

        if ($kategori->children()->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kategori tidak dapat dihapus karena memiliki subkategori turunan!'
            ], 422);
        }

        if ($kategori->transaksi()->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kategori tidak dapat dihapus karena sudah memiliki riwayat transaksi!'
            ], 422);
        }

        $kategori->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Kategori Keuangan berhasil dihapus!'
        ]);
    }

    public function toggleStatus(Request $request, $id)
    {
        $request->validate(['is_active' => 'required|boolean']);

        $kategori = KategoriKeuangan::findOrFail($id);
        $kategori->is_active = $request->boolean('is_active');
        $kategori->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Status Kategori Keuangan berhasil diperbarui!'
        ]);
    }
}
