<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Http\Requests\MasterData\RuanganBulkRequest;
use App\Http\Requests\MasterData\RuanganRequest;
use App\Models\Gedung;
use App\Models\Level;
use App\Models\Ruangan;
use App\Models\TahunPelajaran;
use App\Models\Ustadz;
use Illuminate\Http\Request;

class RuanganController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $filterTp = $request->input('tahun_pelajaran_id');
        $filterGedung = $request->input('gedung_id');

        if (!$filterTp) {
            $activeTp = TahunPelajaran::where('is_active', true)->first();
            $filterTp = $activeTp ? $activeTp->id : null;
        }

        $ruangans = Ruangan::with(['level.tingkat', 'waliRuangan', 'tahunPelajaran', 'gedung'])
            ->when($filterTp, function ($query, $filterTp) {
                return $query->where('tahun_pelajaran_id', $filterTp);
            })
            ->when($filterGedung, function ($query, $filterGedung) {
                return $query->where('gedung_id', $filterGedung);
            })
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('nama_ruangan', 'like', '%' . $search . '%')
                        ->orWhere('nama_kamar', 'like', '%' . $search . '%')
                        ->orWhereHas('waliRuangan', function ($sub) use ($search) {
                            $sub->where('nama_lengkap', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('gedung', function ($sub) use ($search) {
                            $sub->where('nama_gedung', 'like', '%' . $search . '%')
                                ->orWhere('kode_gedung', 'like', '%' . $search . '%');
                        });
                });
            })
            ->berdasarkanHakAkses()
            ->orderBy('level_id', 'asc')
            ->orderBy('nama_ruangan', 'asc')
            ->get();

        $tahunPelajarans = TahunPelajaran::orderBy('id', 'asc')->get();
        $gedungs = Gedung::where('is_active', true)->orderBy('kode_gedung', 'asc')->get();

        return view('ruangan.index', compact('ruangans', 'tahunPelajarans', 'gedungs', 'filterTp', 'filterGedung'));
    }

    public function create()
    {
        $tahunPelajarans = TahunPelajaran::orderBy('is_active', 'desc')->orderBy('id', 'desc')->get();
        $dataAsatidz = Ustadz::where('is_active', true)->orderBy('nama_lengkap', 'asc')->get();
        $gedungs = Gedung::where('is_active', true)->orderBy('kode_gedung', 'asc')->get();
        $levels = Level::with('tingkat')
            ->berdasarkanHakAkses()->get();

        return view('ruangan.create', compact('tahunPelajarans', 'dataAsatidz', 'gedungs', 'levels'));
    }

    public function store(RuanganBulkRequest $request)
    {
        $tahunPelajaranId = $request->tahun_pelajaran_id;
        $barisRuangan = $request->ruangan;

        // Loop untuk menyimpan data secara massal
        foreach ($barisRuangan as $row) {
            Ruangan::create([
                'tahun_pelajaran_id' => $tahunPelajaranId,
                'level_id'           => $row['level_id'],
                'gedung_id'          => $row['gedung_id'] ?? null,
                'ustadz_id'          => $row['ustadz_id'] ?? $row['asatidz_id'] ?? null,
                'nama_ruangan'       => $row['nama_ruangan'],
                'nama_kamar'         => $row['nama_kamar'] ?? null,
                'kapasitas'          => $row['kapasitas'] ?? 30,
                'is_active'          => true,
            ]);
        }

        return redirect()->route('ruangan.index')
            ->with('success', count($barisRuangan) . ' Ruangan baru berhasil ditambahkan secara massal!');
    }

    public function edit(Request $request, Ruangan $ruangan)
    {
        if ($request->ajax()) {
            $ruangan = Ruangan::findOrFail($ruangan->id);
            $tahunPelajarans = TahunPelajaran::orderBy('id', 'desc')->get();
            $dataAsatidz = Ustadz::where('is_active', true)->orderBy('nama_lengkap', 'asc')->get();
            $gedungs = Gedung::where('is_active', true)->orderBy('kode_gedung', 'asc')->get();

            $levels = Level::with('tingkat')
                ->berdasarkanHakAkses()->get();

            return view('ruangan.edit', compact('ruangan', 'tahunPelajarans', 'dataAsatidz', 'gedungs', 'levels'));
        }
        return redirect()->route('ruangan.index')->with('error', 'Silakan gunakan tombol edit data.');
    }

    public function update(RuanganRequest $request, $id)
    {
        $ruangan = Ruangan::findOrFail($id);
        $ruangan->update([
            'tahun_pelajaran_id' => $request->tahun_pelajaran_id,
            'level_id'           => $request->level_id,
            'gedung_id'          => $request->gedung_id,
            'ustadz_id'          => $request->ustadz_id,
            'nama_ruangan'       => $request->nama_ruangan,
            'nama_kamar'         => $request->nama_kamar,
            'kapasitas'          => $request->kapasitas,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status'   => 'success',
                'message'  => 'Data ruangan berhasil diperbarui!',
                'redirect' => route('ruangan.index', ['tahun_pelajaran_id' => $request->tahun_pelajaran_id])
            ]);
        }
        return redirect()->route('ruangan.index', ['tahun_pelajaran_id' => $request->tahun_pelajaran_id])
            ->with('success', 'Data ruangan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $ruangan = Ruangan::findOrFail($id);
        $ruangan->delete();

        return response()->json(['status' => 'success', 'message' => 'Ruangan berhasil dihapus!'], 200);
    }

    public function toggleStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'is_active' => 'required|boolean'
            ]);

            $ruangan = Ruangan::findOrFail($id);
            $ruangan->is_active = $request->boolean('is_active');
            $ruangan->save();

            return response()->json([
                'status'  => 'success',
                'message' => 'Status Ruangan berhasil diperbarui!',
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
