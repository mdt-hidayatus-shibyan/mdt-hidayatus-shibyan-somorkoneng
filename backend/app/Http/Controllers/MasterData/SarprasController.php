<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Http\Requests\MasterData\SarprasRequest;
use App\Models\Gedung;
use App\Models\Ruangan;
use App\Models\Sarpras;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SarprasController extends Controller
{
    public const KATEGORI_LIST = [
        'Mebel / Furnitur',
        'Elektronik & Multimedia',
        'Alat Tulis & Papan',
        'Perlengkapan Ibadah',
        'Sanitasi & Kebersihan',
        'Olahraga & Ekstrakurikuler',
        'Sarana Umum & Lainnya',
    ];

    public function index(Request $request)
    {
        $filters = [
            'search'     => $request->input('search'),
            'gedung_id'  => $request->input('gedung_id'),
            'ruangan_id' => $request->input('ruangan_id'),
            'kategori'   => $request->input('kategori'),
            'kondisi'    => $request->input('kondisi'),
        ];

        $sarprasQuery = Sarpras::with(['gedung', 'ruangan'])
            ->filter($filters)
            ->orderBy('id', 'desc');

        $sarprasItems = $sarprasQuery->paginate(20)->withQueryString();

        // Ringkasan Statistik Global
        $totalJenis = Sarpras::count();
        $totalUnit = (int) Sarpras::sum('jumlah');
        $totalTersedia = (int) Sarpras::where('kondisi', 'tersedia')->sum('jumlah');
        $totalRusakRingan = (int) Sarpras::where('kondisi', 'rusak_ringan')->sum('jumlah');
        $totalRusakBerat = (int) Sarpras::whereIn('kondisi', ['rusak', 'rusak_berat'])->sum('jumlah');

        $gedungs = Gedung::where('is_active', true)->orderBy('nama_gedung')->get();
        $ruangans = Ruangan::where('is_active', true)
            ->when($filters['gedung_id'], fn($q, $gid) => $q->where('gedung_id', $gid))
            ->orderBy('nama_ruangan')
            ->get();
        $kategoriList = self::KATEGORI_LIST;

        if ($request->ajax() && $request->has('ajax_table')) {
            return view('sarpras.list', compact('sarprasItems'));
        }

        return view('sarpras.index', compact(
            'sarprasItems',
            'totalJenis',
            'totalUnit',
            'totalTersedia',
            'totalRusakRingan',
            'totalRusakBerat',
            'gedungs',
            'ruangans',
            'kategoriList',
            'filters'
        ));
    }

    public function create(Request $request)
    {
        if ($request->ajax()) {
            $gedungs = Gedung::where('is_active', true)->orderBy('nama_gedung')->get();
            $ruangans = Ruangan::where('is_active', true)->orderBy('nama_ruangan')->get();
            $kategoriList = self::KATEGORI_LIST;

            // Generate auto kode sarpras
            $nextId = (Sarpras::max('id') ?? 0) + 1;
            $autoKode = 'SPR-' . date('Y') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

            return view('sarpras.form', compact('gedungs', 'ruangans', 'kategoriList', 'autoKode'));
        }
        return redirect()->route('sarpras.index')->with('error', 'Silakan gunakan tombol tambah data.');
    }

    public function store(SarprasRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('sarpras', 'public');
            $data['foto'] = $path;
        }

        Sarpras::create($data);

        return response()->json([
            'status'  => 'success',
            'message' => 'Data sarana & prasarana berhasil ditambahkan!'
        ], 200);
    }

    public function edit(Request $request, Sarpras $sarpra)
    {
        // Parameter route bisa berupa 'sarpra' atau 'sarpra->id'
        $sarpras = $sarpra;

        if ($request->ajax()) {
            $gedungs = Gedung::where('is_active', true)->orderBy('nama_gedung')->get();
            $ruangans = Ruangan::where('is_active', true)
                ->when($sarpras->gedung_id, fn($q, $gid) => $q->where('gedung_id', $gid))
                ->orderBy('nama_ruangan')
                ->get();
            $kategoriList = self::KATEGORI_LIST;

            return view('sarpras.form', compact('sarpras', 'gedungs', 'ruangans', 'kategoriList'));
        }
        return redirect()->route('sarpras.index')->with('error', 'Silakan gunakan tombol edit data.');
    }

    public function update(SarprasRequest $request, $id)
    {
        $sarpras = Sarpras::findOrFail($id);
        $data = $request->validated();

        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($sarpras->foto && Storage::disk('public')->exists($sarpras->foto)) {
                Storage::disk('public')->delete($sarpras->foto);
            }
            $path = $request->file('foto')->store('sarpras', 'public');
            $data['foto'] = $path;
        }

        $sarpras->update($data);

        return response()->json([
            'status'  => 'success',
            'message' => 'Data sarana & prasarana berhasil diperbarui!'
        ], 200);
    }

    public function destroy($id)
    {
        $sarpras = Sarpras::findOrFail($id);

        if ($sarpras->foto && Storage::disk('public')->exists($sarpras->foto)) {
            Storage::disk('public')->delete($sarpras->foto);
        }

        $sarpras->delete();

        if (request()->ajax()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Data sarana & prasarana berhasil dihapus!'
            ]);
        }

        return redirect()->back()->with('success', 'Data sarana & prasarana berhasil dihapus!');
    }

    public function toggleStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'is_active' => 'required|boolean'
            ]);

            $sarpras = Sarpras::findOrFail($id);
            $sarpras->is_active = $request->boolean('is_active');
            $sarpras->save();

            return response()->json([
                'status'  => 'success',
                'message' => 'Status Sarpras berhasil diperbarui!',
                'reload'  => false
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal memperbarui status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getRuanganByGedung(Request $request, $gedung_id = null)
    {
        $ruangans = Ruangan::where('is_active', true)
            ->when($gedung_id, fn($q) => $q->where('gedung_id', $gedung_id))
            ->orderBy('nama_ruangan')
            ->get(['id', 'nama_ruangan', 'nama_kamar']);

        return response()->json($ruangans);
    }
}
