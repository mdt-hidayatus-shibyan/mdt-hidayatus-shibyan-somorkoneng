<?php

namespace App\Http\Controllers\Kepengurusan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kepengurusan\PengurusRequest;
use App\Models\Kepengurusan\Anggota;
use App\Models\Kepengurusan\JabatanPengurus;
use App\Models\Kepengurusan\Pengurus;
use App\Models\Kepengurusan\PeriodeKepengurusan;
use App\Models\Tingkat;
use Illuminate\Http\Request;

class PengurusController extends Controller
{
    public function index()
    {
        $pengurus = Pengurus::with(['anggota.ustadz', 'jabatan', 'periode'])->latest()->get();
        return view('kepengurusan.pengurus.index', compact('pengurus'));
    }

    public function create()
    {
        $anggota = Anggota::orderBy('nama_lengkap', 'asc')->get();
        $jabatan = JabatanPengurus::orderBy('level', 'asc')->get();
        $tingkats = Tingkat::orderBy('urutan_tingkat', 'asc')->get();
        $periode = PeriodeKepengurusan::orderBy('tanggal_mulai', 'desc')->get();

        return view('kepengurusan.pengurus.form', compact('anggota', 'jabatan', 'periode', 'tingkats'));
    }

    public function store(PengurusRequest $request)
    {
        Pengurus::create($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Penugasan pengurus berhasil ditambahkan!'
        ], 200);
    }

    public function edit($id)
    {
        $pengurus = Pengurus::findOrFail($id);
        $anggota = Anggota::orderBy('nama_lengkap', 'asc')->get();
        $tingkats = Tingkat::orderBy('urutan_tingkat', 'asc')->get();
        $jabatan = JabatanPengurus::orderBy('level', 'asc')->get();
        $periode = PeriodeKepengurusan::orderBy('tanggal_mulai', 'desc')->get();

        return view('kepengurusan.pengurus.form', compact('pengurus', 'anggota', 'jabatan', 'periode', 'tingkats'));
    }

    public function update(PengurusRequest $request, $id)
    {
        $pengurus = Pengurus::findOrFail($id);
        $pengurus->update($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Penugasan pengurus berhasil diupdate!'
        ], 200);
    }

    public function destroy($id)
    {
        $pengurus = Pengurus::findOrFail($id);
        $pengurus->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data penugasan pengurus dihapus!'
        ], 200);
    }



    public function profilPublik($id)
    {
        $pengurus = Pengurus::with(['anggota.ustadz', 'jabatan', 'periode'])->findOrFail($id);

        return view('kepengurusan.profil_publik', compact('pengurus'));
    }
}
