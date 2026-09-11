<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Keuangan\AkunKeuanganRequest;
use App\Models\Keuangan\AkunKeuangan;
use Illuminate\Http\Request;

class AkunKeuanganController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $tipe = $request->input('tipe_akun');

        $akuns = AkunKeuangan::when($search, function ($query, $search) {
            return $query->where(function ($q) use ($search) {
                $q->where('kode_akun', 'like', "%{$search}%")
                    ->orWhere('nama_akun', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        })
            ->when($tipe, function ($query, $tipe) {
                return $query->where('tipe_akun', $tipe);
            })
            ->orderBy('kode_akun', 'asc')
            ->get();

        $totalSaldoKas = AkunKeuangan::where('is_active', true)->sum('saldo_berjalan');
        $totalAkun = AkunKeuangan::count();
        $totalAktif = AkunKeuangan::where('is_active', true)->count();

        if ($request->ajax() && $request->has('table_only')) {
            return view('keuangan.akun.list', compact('akuns'));
        }

        return view('keuangan.akun.index', compact('akuns', 'totalSaldoKas', 'totalAkun', 'totalAktif'));
    }

    public function create(Request $request)
    {
        return view('keuangan.akun.form');
    }

    public function store(AkunKeuanganRequest $request)
    {
        $data = $request->validated();
        $data['saldo_awal'] = (float) ($data['saldo_awal'] ?? 0);
        $data['saldo_berjalan'] = $data['saldo_awal'];
        $data['is_active'] = $request->boolean('is_active', true);

        AkunKeuangan::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Pos Akun Keuangan berhasil ditambahkan!'
        ]);
    }

    public function edit(Request $request, $id)
    {
        $akun = AkunKeuangan::findOrFail($id);
        return view('keuangan.akun.form', compact('akun'));
    }

    public function update(AkunKeuanganRequest $request, $id)
    {
        $akun = AkunKeuangan::findOrFail($id);
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);

        $akun->update([
            'kode_akun' => $data['kode_akun'],
            'nama_akun' => $data['nama_akun'],
            'tipe_akun' => $data['tipe_akun'],
            'deskripsi' => $data['deskripsi'] ?? null,
            'is_active' => $data['is_active'],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Pos Akun Keuangan berhasil diperbarui!'
        ]);
    }

    public function destroy($id)
    {
        $akun = AkunKeuangan::findOrFail($id);

        if ($akun->transaksi()->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pos Akun tidak dapat dihapus karena sudah memiliki riwayat transaksi!'
            ], 422);
        }

        $akun->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Pos Akun Keuangan berhasil dihapus!'
        ]);
    }

    public function toggleStatus(Request $request, $id)
    {
        $request->validate(['is_active' => 'required|boolean']);

        $akun = AkunKeuangan::findOrFail($id);
        $akun->is_active = $request->boolean('is_active');
        $akun->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Status Pos Akun berhasil diperbarui!'
        ]);
    }
}
