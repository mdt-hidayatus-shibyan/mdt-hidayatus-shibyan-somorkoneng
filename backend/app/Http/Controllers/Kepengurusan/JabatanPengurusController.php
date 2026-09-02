<?php

namespace App\Http\Controllers\Kepengurusan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kepengurusan\JabatanPengurusRequest;
use App\Models\Kepengurusan\JabatanPengurus;
use Illuminate\Http\Request;

class JabatanPengurusController extends Controller
{
    public function index()
    {
        $jabatan = JabatanPengurus::latest()->get();
        return view('kepengurusan.jabatan.index', compact('jabatan'));
    }

    public function create(Request $request)
    {
        if ($request->ajax()) {
            return view('kepengurusan.jabatan.form');
        }
        return redirect()->route('jabatan-pengurus.index')->with('error', 'Silakan gunakan tombol tambah data.');
    }

    public function store(JabatanPengurusRequest $request)
    {
        JabatanPengurus::create($request->validated());
        return response()->json([
            'status' => 'success',
            'message' => 'Data Jabatan Pengurus berhasil ditambahkan!'
        ], 200);
    }

    public function edit(Request $request, $id)
    {
        if ($request->ajax()) {
            $jabatan = JabatanPengurus::findOrFail($id);
            return view('kepengurusan.jabatan.form', compact('jabatan'));
        }
        return redirect()->route('jabatan-pengurus.index')->with('error', 'Silakan gunakan tombol edit data.');
    }

    public function update(JabatanPengurusRequest $request, $id)
    {
        JabatanPengurus::update($request->validated());
        return response()->json([
            'status' => 'success',
            'message' => 'Data Jabatan Pengurus berhasil dirubah!'
        ], 200);
    }

    public function destroy($id)
    {
        if (request()->ajax()) {
            JabatanPengurus::findOrFail($id)->delete();
            return response()->json(['message' => 'Jabatan berhasil dihapus!']);
        }
        return redirect()->route('jabatan-pengurus.index')->with('error', 'Silakan gunakan tombol hapus data.');
    }
}
