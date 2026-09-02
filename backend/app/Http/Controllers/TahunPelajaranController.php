<?php

namespace App\Http\Controllers;

use App\Http\Requests\TahunPelajaranRequest;
use App\Models\TahunPelajaran;
use Illuminate\Http\Request;



class TahunPelajaranController extends Controller
{


    public function index(Request $request)
    {
        $search = $request->input('search');
        $tahun_pelajarans = TahunPelajaran::when($search, function ($query, $search) {
            return $query->where('nama_hijriyah', 'like', '%' . $search . '%')
                ->orWhere('nama_masehi', 'like', '%' . $search . '%');
        })
            ->orderBy('id', 'asc')
            ->get();

        return view('tahun-pelajaran.index', compact('tahun_pelajarans'));
    }

    public function create(Request $request)
    {
        if ($request->ajax()) {
            return view('tahun-pelajaran.form');
        }
        return redirect()->route('tahun-pelajaran.index')->with('error', 'Silakan gunakan tombol tambah data.');
    }

    public function store(TahunPelajaranRequest $request)
    {

        if ($request->has('is_active')) {
            TahunPelajaran::query()->update(['is_active' => false]);
        }

        TahunPelajaran::create([
            'nama_hijriyah' => $request->nama_hijriyah,
            'nama_masehi' => $request->nama_masehi,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Data Tahun Pelajaran berhasil ditambahkan!'
        ], 200);
    }


    public function edit(Request $request, TahunPelajaran $tahunPelajaran)
    {
        if ($request->ajax()) {
            return view('tahun-pelajaran.form', compact('tahunPelajaran'));
        }

        return redirect()->route('tahun-pelajaran.index')->with('error', 'Silakan gunakan tombol tambah edit.');
    }

    public function update(TahunPelajaranRequest $request, $id)
    {
        $tahunPelajaran = TahunPelajaran::findOrFail($id);

        if ($request->has('is_active')) {
            TahunPelajaran::where('id', '!=', $id)->update(['is_active' => false]);
        }

        // Langsung update data spesifik ini saja
        $tahunPelajaran->update([
            'nama_hijriyah' => $request->nama_hijriyah,
            'nama_masehi'   => $request->nama_masehi,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Data Tahun Pelajaran berhasil diperbarui!'
        ], 200);
    }

    public function destroy($id)
    {
        if (request()->ajax()) {
            TahunPelajaran::findOrFail($id)->delete();
            return response()->json(['message' => 'Data berhasil dihapus!']);
        }
    }

    public function toggleStatus(Request $request, $id)
    {
        $request->validate([
            'is_active' => 'required|boolean'
        ]);

        $tahunPelajaran = TahunPelajaran::findOrFail($id);

        if ($request->is_active == 1) {
            TahunPelajaran::where('id', '!=', $id)->update(['is_active' => 0]);
        }

        $tahunPelajaran->is_active = $request->is_active;
        $tahunPelajaran->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Status Tahun Pelajaran berhasil diperbarui!',
            'reload' => $request->is_active == 1 ? true : false
        ], 200);
    }
}
