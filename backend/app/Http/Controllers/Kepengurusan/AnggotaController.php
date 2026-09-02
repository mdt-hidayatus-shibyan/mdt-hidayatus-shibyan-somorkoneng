<?php

namespace App\Http\Controllers\Kepengurusan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kepengurusan\AnggotaRequest;
use App\Models\Kepengurusan\Anggota;
use App\Models\Ustadz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AnggotaController extends Controller
{
    public function index()
    {
        $anggota = Anggota::with('ustadz')->latest()->get();
        return view('kepengurusan.anggota.index', compact('anggota'));
    }

    public function create(Request $request)
    {
        if ($request->ajax()) {
            $ustadzs = Ustadz::orderBy('nama_lengkap', 'asc')->get();
            return view('kepengurusan.anggota.form', compact('ustadzs'));
        }
        return redirect()->route('anggota.index')->with('error', 'Silakan gunakan tombol tambah data.');
    }

    public function store(AnggotaRequest $request)
    {
        $data = $request->except(['foto', 'tanda_tangan']);

        // Upload foto jika ada
        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('uploads/anggota/foto', 'public');
        }

        // Upload TTD jika ada
        if ($request->hasFile('tanda_tangan')) {
            $data['tanda_tangan'] = $request->file('tanda_tangan')->store('uploads/anggota/ttd', 'public');
        }

        Anggota::create($data);

        return redirect()->route('anggota.index')->with('success', 'Anggota berhasil ditambahkan!');
    }

    public function edit(Request $request, $id)
    {
        if ($request->ajax()) {
            $anggota = Anggota::findOrFail($id);
            $ustadzs = Ustadz::orderBy('nama_lengkap', 'asc')->get();
            return view('kepengurusan.anggota.form', compact('anggota', 'ustadzs'));
        }
        return redirect()->route('anggota.index')->with('error', 'Silakan gunakan tombol edit data.');
    }

    public function update(AnggotaRequest $request, $id)
    {

        $anggota = Anggota::findOrFail($id);
        $data = $request->except(['foto', 'tanda_tangan']);

        if ($request->hasFile('foto')) {
            if ($anggota->foto) Storage::disk('public')->delete($anggota->foto);
            $data['foto'] = $request->file('foto')->store('uploads/anggota/foto', 'public');
        }

        if ($request->hasFile('tanda_tangan')) {
            if ($anggota->tanda_tangan) Storage::disk('public')->delete($anggota->tanda_tangan);
            $data['tanda_tangan'] = $request->file('tanda_tangan')->store('uploads/anggota/ttd', 'public');
        }

        $anggota->update($data);

        return redirect()->route('anggota.index')->with('success', 'Anggota berhasil diupdate!');
    }

    public function destroy($id)
    {
        if (request()->ajax()) {
            $anggota = Anggota::findOrFail($id);

            // Hapus file fisik jika ada
            if ($anggota->foto) Storage::disk('public')->delete($anggota->foto);
            if ($anggota->tanda_tangan) Storage::disk('public')->delete($anggota->tanda_tangan);

            $anggota->delete();

            return response()->json(['message' => 'Anggota berhasil dihapus!']);
        }
        return redirect()->route('anggota.index')->with('error', 'Silakan gunakan tombol hapus data.');
    }
}
