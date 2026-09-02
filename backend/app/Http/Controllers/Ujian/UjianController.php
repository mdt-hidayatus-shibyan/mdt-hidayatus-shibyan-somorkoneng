<?php

namespace App\Http\Controllers\Ujian;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ujian\UjianRequest;
use App\Models\Semester;
use App\Models\TahunPelajaran;
use App\Models\Ujian\Ujian;
use Illuminate\Http\Request;

class UjianController extends Controller
{
    public function index(Request $request)
    {
        $tahunPelajarans = TahunPelajaran::orderBy('id', 'asc')->get();
        $tahunAktif = TahunPelajaran::where('is_active', 1)->first();
        $selectedTahunId = $request->input('tahun_pelajaran_id', $tahunAktif ? $tahunAktif->id : null);
        $query = Ujian::with(['tahunPelajaran', 'semester_relasi']);
        if ($selectedTahunId) {
            $query->where('tahun_pelajaran_id', $selectedTahunId);
        }
        if ($request->filled('search')) {
            $query->where('nama_ujian', 'like', '%' . $request->search . '%');
        }

        $ujians = $query->latest()->get();
        return view('ujian.index', compact('ujians', 'tahunPelajarans', 'selectedTahunId'));
    }

    public function create(Request $request)
    {
        if ($request->ajax()) {
            $tahun_pelajarans = TahunPelajaran::orderBy('id', 'asc')->get();
            $tahunAktif = TahunPelajaran::where('is_active', true)->first();
            $semesters = $tahunAktif
                ? Semester::where('tahun_pelajaran_id', $tahunAktif->id)->get()
                : collect();

            return view('ujian.form', compact('tahun_pelajarans', 'tahunAktif', 'semesters'));
        }

        return redirect()->route('ujian.index')->with('error', 'Silakan gunakan tombol tambah data.');
    }

    public function store(UjianRequest $request)
    {
        Ujian::create($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Data Ujian berhasil ditambahkan!'
        ], 200);
    }

    public function edit($id, Request $request)
    {
        if ($request->ajax()) {
            $ujian = Ujian::findOrFail($id);
            $tahun_pelajarans = TahunPelajaran::orderBy('id', 'asc')->get();
            $semesters = Semester::where('tahun_pelajaran_id', $ujian->tahun_pelajaran_id)->get();
            return view('ujian.form', compact('ujian', 'tahun_pelajarans', 'semesters'));
        }

        return redirect()->route('ujian.index')->with('error', 'Silakan gunakan tombol edit data.');
    }

    public function update(UjianRequest $request, $id)
    {
        $ujian = Ujian::findOrFail($id);
        $ujian->update($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Data Ujian berhasil diperbarui!'
        ], 200);
    }

    public function destroy($id)
    {
        if (request()->ajax()) {
            Ujian::findOrFail($id)->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Data Ujian berhasil dihapus!'
            ], 200);
        }
        return redirect()->route('ujian.index')->with('error', 'Silakan gunakan tombol hapus data.');
    }
}
