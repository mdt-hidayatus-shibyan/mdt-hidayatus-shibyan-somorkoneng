<?php

namespace App\Http\Controllers;

use App\Http\Requests\TingkatRequest;
use App\Models\Tingkat;
use Illuminate\Http\Request;


class TingkatController extends Controller
{

    public function index(Request $request)
    {
        $search = $request->input('search');
        $tingkats = Tingkat::when($search, function ($query, $search) {
            return $query->where('kode_tingkat', 'like', '%' . $search . '%')
                ->orWhere('nama_tingkat', 'like', '%' . $search . '%')
                ->orWhere('kode_mdt_tingkat', 'like', '%' . $search . '%')
                ->orWhere('nama_mdt_tingkat', 'like', '%' . $search . '%');
        })->orderBy('urutan_tingkat', 'asc')->get();

        return view('tingkat.index', compact('tingkats'));
    }

    public function create(Request $request)
    {
        if ($request->ajax()) {
            return view('tingkat.form');
        }
        return redirect()->route('tingkat.index')->with('error', 'Silakan gunakan tombol tambah data.');
    }

    public function store(TingkatRequest $request)
    {
        Tingkat::create([
            'kode_mdt_tingkat' => $request->kode_mdt_tingkat,
            'kode_tingkat'     => $request->kode_tingkat,
            'urutan_tingkat'   => $request->urutan_tingkat,
            'nama_tingkat'     => $request->nama_tingkat,
            'nama_mdt_tingkat' => $request->nama_mdt_tingkat,
            'kode_warna'       => $request->kode_warna,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Data tingkat berhasil ditambahkan!'
        ], 200);
    }

    public function edit(Request $request, Tingkat $tingkat)
    {
        if ($request->ajax()) {
            return view('tingkat.form', compact('tingkat'));
        }
        return redirect()->route('tingkat.index')->with('error', 'Silakan gunakan tombol tambah data.');
    }

    public function update(TingkatRequest $request, $id)
    {
        $tingkat = Tingkat::findOrFail($id);

        $tingkat->update([
            'kode_mdt_tingkat'     => $request->kode_mdt_tingkat,
            'kode_tingkat'     => $request->kode_tingkat,
            'urutan_tingkat'   => $request->urutan_tingkat,
            'nama_tingkat'     => $request->nama_tingkat,
            'nama_mdt_tingkat' => $request->nama_mdt_tingkat,
            'kode_warna'       => $request->kode_warna,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Data tingkat berhasil diperbarui!'
        ], 200);
    }

    public function destroy($id)
    {
        Tingkat::findOrFail($id)->delete();
        if (request()->ajax()) {
            return response()->json(['message' => 'Data berhasil dihapus!']);
        }
        return redirect()->back()->with('success', 'Data berhasil dihapus!');
    }


    public function toggleStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'is_active' => 'required|boolean'
            ]);

            $tingkat = Tingkat::findOrFail($id);

            $tingkat->is_active = $request->boolean('is_active');
            $tingkat->save();

            return response()->json([
                'status'  => 'success',
                'message' => 'Status Tingkat berhasil diperbarui!',
                'reload'  => false
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal memperbarui status: ' . $e->getMessage()
            ], 500);
        }
    }
}
