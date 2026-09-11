<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Keuangan\BankRequest;
use App\Models\Keuangan\Bank;
use Illuminate\Http\Request;

class BankController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $banks = Bank::when($search, function ($query, $search) {
            return $query->where(function ($q) use ($search) {
                $q->where('nama_bank', 'like', "%{$search}%")
                    ->orWhere('nomor_rekening', 'like', "%{$search}%")
                    ->orWhere('atas_nama', 'like', "%{$search}%")
                    ->orWhere('cabang', 'like', "%{$search}%");
            });
        })
            ->orderBy('is_default', 'desc')
            ->orderBy('nama_bank', 'asc')
            ->get();

        $totalSaldoBank = Bank::where('is_active', true)->sum('saldo');
        $totalBank = Bank::count();
        $totalAktif = Bank::where('is_active', true)->count();

        if ($request->ajax() && $request->has('table_only')) {
            return view('keuangan.bank.list', compact('banks'));
        }

        return view('keuangan.bank.index', compact('banks', 'totalSaldoBank', 'totalBank', 'totalAktif'));
    }

    public function create(Request $request)
    {
        return view('keuangan.bank.form');
    }

    public function store(BankRequest $request)
    {
        $data = $request->validated();
        $data['saldo'] = (float) ($data['saldo'] ?? 0);
        $data['is_default'] = $request->boolean('is_default', false);
        $data['is_active'] = $request->boolean('is_active', true);

        if ($data['is_default']) {
            Bank::query()->update(['is_default' => false]);
        }

        Bank::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Rekening Bank berhasil ditambahkan!'
        ]);
    }

    public function edit(Request $request, $id)
    {
        $bank = Bank::findOrFail($id);
        return view('keuangan.bank.form', compact('bank'));
    }

    public function update(BankRequest $request, $id)
    {
        $bank = Bank::findOrFail($id);
        $data = $request->validated();
        $data['is_default'] = $request->boolean('is_default', false);
        $data['is_active'] = $request->boolean('is_active', true);

        if ($data['is_default']) {
            Bank::where('id', '!=', $id)->update(['is_default' => false]);
        }

        $bank->update([
            'nama_bank' => $data['nama_bank'],
            'kode_bank' => $data['kode_bank'] ?? null,
            'nomor_rekening' => $data['nomor_rekening'],
            'atas_nama' => $data['atas_nama'],
            'cabang' => $data['cabang'] ?? null,
            'is_default' => $data['is_default'],
            'is_active' => $data['is_active'],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Rekening Bank berhasil diperbarui!'
        ]);
    }

    public function destroy($id)
    {
        $bank = Bank::findOrFail($id);

        if ($bank->transaksi()->exists() || $bank->pinjamans()->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Rekening Bank tidak dapat dihapus karena sudah memiliki riwayat transaksi / pinjaman!'
            ], 422);
        }

        $bank->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Rekening Bank berhasil dihapus!'
        ]);
    }

    public function toggleStatus(Request $request, $id)
    {
        $request->validate(['is_active' => 'required|boolean']);

        $bank = Bank::findOrFail($id);
        $bank->is_active = $request->boolean('is_active');
        $bank->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Status Rekening Bank berhasil diperbarui!'
        ]);
    }
}
