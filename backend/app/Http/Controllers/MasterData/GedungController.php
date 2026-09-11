<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Http\Requests\MasterData\GedungRequest;
use App\Models\Gedung;
use App\Models\Ruangan;
use App\Models\Sarpras;
use Illuminate\Http\Request;

class GedungController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $gedungs = Gedung::withCount(['ruangans', 'sarpras'])
            ->when($search, function ($query, $search) {
                return $query->where('kode_gedung', 'like', '%' . $search . '%')
                    ->orWhere('nama_gedung', 'like', '%' . $search . '%')
                    ->orWhere('keterangan', 'like', '%' . $search . '%');
            })
            ->orderBy('kode_gedung', 'asc')
            ->get();

        $totalGedung = Gedung::count();
        $totalGedungAktif = Gedung::where('is_active', true)->count();
        $totalRuangan = Ruangan::count();
        $totalSarpras = Sarpras::count();

        if ($request->ajax() && $request->has('ajax_table')) {
            return view('gedung.list', compact('gedungs'));
        }

        return view('gedung.index', compact(
            'gedungs',
            'totalGedung',
            'totalGedungAktif',
            'totalRuangan',
            'totalSarpras'
        ));
    }

    public function create(Request $request)
    {
        if ($request->ajax()) {
            return view('gedung.form');
        }
        return redirect()->route('gedung.index')->with('error', 'Silakan gunakan tombol tambah gedung.');
    }

    public function store(GedungRequest $request)
    {
        Gedung::create([
            'kode_gedung'   => strtoupper($request->kode_gedung),
            'nama_gedung'   => $request->nama_gedung,
            'jumlah_lantai' => $request->jumlah_lantai ?? 1,
            'keterangan'    => $request->keterangan,
            'is_active'     => true,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Data gedung berhasil ditambahkan!'
        ], 200);
    }

    public function edit(Request $request, Gedung $gedung)
    {
        if ($request->ajax()) {
            return view('gedung.form', compact('gedung'));
        }
        return redirect()->route('gedung.index')->with('error', 'Silakan gunakan tombol edit gedung.');
    }

    public function update(GedungRequest $request, $id)
    {
        $gedung = Gedung::findOrFail($id);

        $gedung->update([
            'kode_gedung'   => strtoupper($request->kode_gedung),
            'nama_gedung'   => $request->nama_gedung,
            'jumlah_lantai' => $request->jumlah_lantai ?? 1,
            'keterangan'    => $request->keterangan,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Data gedung berhasil diperbarui!'
        ], 200);
    }

    public function destroy($id)
    {
        $gedung = Gedung::findOrFail($id);
        $gedung->delete();

        if (request()->ajax()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Data gedung berhasil dihapus!'
            ]);
        }

        return redirect()->back()->with('success', 'Data gedung berhasil dihapus!');
    }

    public function toggleStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'is_active' => 'required|boolean'
            ]);

            $gedung = Gedung::findOrFail($id);
            $gedung->is_active = $request->boolean('is_active');
            $gedung->save();

            return response()->json([
                'status'  => 'success',
                'message' => 'Status Gedung berhasil diperbarui!',
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
