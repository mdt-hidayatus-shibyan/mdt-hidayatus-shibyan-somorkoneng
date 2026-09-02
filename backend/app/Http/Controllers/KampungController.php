<?php

namespace App\Http\Controllers;

use App\Http\Requests\KampungRequest;
use App\Models\Kampung;
use Illuminate\Http\Request;


class KampungController extends Controller
{


    public function index(Request $request)
    {
        $search = $request->input('search');
        $kampungs = Kampung::when($search, function ($query, $search) {
            return $query->where('kode', 'like', '%' . $search . '%')
                ->orWhere('nama_kampung', 'like', '%' . $search . '%');
        })
            ->orderBy('kode', 'asc')
            ->get();

        return view('kampung.index', compact('kampungs'));
    }

    public function create(Request $request)
    {
        if ($request->ajax()) {
            return view('kampung.form');
        }
        return redirect()->route('kampung.index')->with('error', 'Silakan gunakan tombol tambah data.');
    }

    public function store(KampungRequest $request)
    {
        Kampung::create([
            'kode' => $request->kode,
            'nama_kampung' => $request->nama_kampung,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Data Tahun Pelajaran berhasil ditambahkan!'
        ], 200);
    }


    public function edit(Request $request, Kampung $kampung)
    {
        if ($request->ajax()) {
            return view('kampung.form', compact('kampung'));
        }

        return redirect()->route('kampung.index')->with('error', 'Silakan gunakan tombol tambah edit.');
    }

    public function update(KampungRequest $request, $id)
    {
        $kampung = Kampung::findOrFail($id);
        // Langsung update data spesifik ini saja
        $kampung->update([
            'kode' => $request->kode,
            'nama_kampung' => $request->nama_kampung,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Data Tahun Pelajaran berhasil diperbarui!'
        ], 200);
    }

    public function destroy($id)
    {
        if (request()->ajax()) {
            Kampung::findOrFail($id)->delete();
            return response()->json(['message' => 'Data berhasil dihapus!']);
        }
    }
}
