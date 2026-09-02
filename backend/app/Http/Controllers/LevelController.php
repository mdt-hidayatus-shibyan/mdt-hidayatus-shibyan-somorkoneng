<?php

namespace App\Http\Controllers;


use App\Http\Requests\LevelRequest;
use App\Models\Level;
use App\Models\Tingkat;
use Illuminate\Http\Request;

class LevelController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $levels = Level::with('tingkat')
            ->when($search, function ($query, $search) {
                return $query->where('nama_level', 'like', '%' . $search . '%')
                    ->orWhereHas('tingkat', function ($q) use ($search) {
                        $q->where('nama_tingkat', 'like', '%' . $search . '%')
                            ->orWhere('kode_tingkat', 'like', '%' . $search . '%');
                    });
            })
            ->berdasarkanHakAkses()
            ->orderBy('urutan_level', 'asc')
            ->get();
        $groupedLevels = $levels->groupBy('tingkat_id');

        return view('level.index', compact('groupedLevels'));
    }

    public function create(Request $request)
    {
        if ($request->ajax()) {
            $tingkats = Tingkat::orderBy('urutan_tingkat', 'asc')->berdasarkanHakAkses()->get();
            return view('level.form', compact('tingkats'));
        }
        return redirect()->route('level.index')->with('error', 'Silakan gunakan tombol tambah data.');
    }

    public function store(LevelRequest $request)
    {
        Level::create([
            'nama_level' => $request->nama_level,
            'urutan_level' => $request->urutan_level,
            'tingkat_id' => $request->tingkat_id,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Level/Kelas berhasil ditambahkan!'
        ], 200);
    }

    public function edit(Request $request, $id)
    {
        if ($request->ajax()) {
            $level = Level::findOrFail($id);
            $tingkats = Tingkat::orderBy('urutan_tingkat', 'asc')->berdasarkanHakAkses()->get();
            return view('level.form', compact('level', 'tingkats'));
        }
        return redirect()->route('level.index')->with('error', 'Silakan gunakan tombol edit data.');
    }

    public function update(LevelRequest $request, $id)
    {
        $level = Level::findOrFail($id);

        $level->update([
            'nama_level' => $request->nama_level,
            'urutan_level' => $request->urutan_level,
            'tingkat_id' => $request->tingkat_id,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Data kelas berhasil diperbarui!'
        ], 200);
    }

    public function destroy($id)
    {

        if (request()->ajax()) {
            Level::findOrFail($id)->delete();
            return response()->json(['message' => 'Data berhasil dihapus!']);
        }
        return redirect()->route('level.index')->with('error', 'Silakan gunakan tombol tambah hapus.');
    }

    public function toggleStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'is_active' => 'required|boolean'
            ]);

            $level = Level::findOrFail($id);

            $level->is_active = $request->boolean('is_active');
            $level->save();

            return response()->json([
                'status'  => 'success',
                'message' => 'Status Level berhasil diperbarui!',
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
