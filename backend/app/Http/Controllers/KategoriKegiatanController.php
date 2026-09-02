<?php

namespace App\Http\Controllers;

use App\Http\Requests\KategoriKegiatanRequest;
use App\Models\KategoriKegiatan;
use Illuminate\Http\Request;


class KategoriKegiatanController extends Controller
{

    public function create(Request $request)
    {
        if ($request->ajax()) {
            return view('kalendar.form-kategori');
        }
        return redirect()->route('kalendar-pendidikan.index')->with('error', 'Silakan gunakan tombol tambah data.');
    }

    public function store(KategoriKegiatanRequest $request)
    {
        KategoriKegiatan::create([
            'nama_kategori' => $request->nama_kategori,
            'kode_warna'    => $request->kode_warna,
        ]);

        // 2. RESPON AJAX UNTUK SIMPAN DATA
        if ($request->ajax()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Kategori baru berhasil ditambahkan!'
            ]);
        }
    }

    public function edit(Request $request, $id)
    {
        if ($request->ajax()) {
            $kategori_kegiatan = KategoriKegiatan::findOrFail($id);
            return view('kalendar.form-kategori', compact('kategori_kegiatan'));
        }
        return redirect()->route('kalendar-pendidikan.index')->with('error', 'Silakan gunakan tombol edit data.');
    }

    public function update(KategoriKegiatanRequest $request, $id)
    {

        $kategoriKegiatan = KategoriKegiatan::findOrFail($id);
        $kategoriKegiatan->update([
            'nama_kategori' => $request->nama_kategori,
            'kode_warna'    => $request->kode_warna,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Data Tahun Pelajaran berhasil diperbarui!'
        ], 200);
    }



    public function destroy($id)
    {
        if (request()->ajax()) {
            KategoriKegiatan::findOrFail($id)->delete();
            return response()->json([
                'status'  => 'success',
                'message' => 'Kategori berhasil dihapus secara permanen!'
            ]);
        }
        return redirect()->route('kalendar-pendidikan.index')->with('error', 'Silakan gunakan tombol hapus data.');
    }
}
