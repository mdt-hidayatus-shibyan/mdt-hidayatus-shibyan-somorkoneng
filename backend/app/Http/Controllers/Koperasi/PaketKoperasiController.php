<?php

namespace App\Http\Controllers\Koperasi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Koperasi\PaketKoperasiRequest;
use App\Models\Koperasi\PaketKoperasi;
use App\Models\Koperasi\PaketKoperasiItem;
use App\Models\Koperasi\ProdukKoperasi;
use App\Models\Level;
use App\Models\Tingkat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PaketKoperasiController extends Controller
{
    /**
     * Daftar Master Paket Bundling
     */
    public function index(Request $request)
    {
        $query = PaketKoperasi::with(['level.tingkat', 'tingkat', 'items.produk']);

        if ($request->filled('level_id')) {
            $query->where('level_id', $request->level_id);
        }

        if ($request->filled('tingkat_id')) {
            $query->where('tingkat_id', $request->tingkat_id);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'aktif');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_paket', 'like', "%{$search}%")
                    ->orWhere('kode_paket', 'like', "%{$search}%");
            });
        }

        $pakets = $query->orderBy('nama_paket')->paginate(15)->withQueryString();
        $levels = Level::with('tingkat')->orderBy('urutan_level')->get();
        $tingkats = Tingkat::orderBy('id')->get();

        if ($request->ajax() || $request->wantsJson()) {
            return view('koperasi.paket.list', compact('pakets', 'levels', 'tingkats'))->render();
        }

        return view('koperasi.paket.index', compact('pakets', 'levels', 'tingkats'));
    }

    /**
     * Form Modal Tambah Paket Bundling
     */
    public function create()
    {
        $levels = Level::with('tingkat')->orderBy('urutan_level')->get();
        $tingkats = Tingkat::orderBy('id')->get();
        $produks = ProdukKoperasi::where('status', 'Aktif')->orderBy('nama_produk')->get();
        $generatedBarcode = PaketKoperasi::generateBarcode();

        return view('koperasi.paket.form', compact('levels', 'tingkats', 'produks', 'generatedBarcode'));
    }

    /**
     * Simpan Paket Bundling Baru
     */
    public function store(PaketKoperasiRequest $request)
    {
        $data = $request->validated();

        if (empty($data['kode_paket'])) {
            $data['kode_paket'] = PaketKoperasi::generateBarcode($data['level_id'] ?? null);
        }

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('uploads/koperasi/paket', 'public');
        }

        $paket = DB::transaction(function () use ($data) {
            $items = $data['items'] ?? [];
            $totalHpp = 0.00;

            foreach ($items as $item) {
                $prod = ProdukKoperasi::find($item['produk_id']);
                if ($prod) {
                    $totalHpp += (float) $prod->harga_beli * (int) $item['jumlah'];
                }
            }

            $data['total_hpp_komponen'] = $totalHpp;

            $paket = PaketKoperasi::create([
                'kode_paket' => $data['kode_paket'],
                'nama_paket' => $data['nama_paket'],
                'level_id' => $data['level_id'] ?? null,
                'tingkat_id' => $data['tingkat_id'] ?? null,
                'harga_paket' => $data['harga_paket'],
                'total_hpp_komponen' => $totalHpp,
                'foto' => $data['foto'] ?? null,
                'is_active' => (bool) ($data['is_active'] ?? true),
                'deskripsi' => $data['deskripsi'] ?? null,
            ]);

            foreach ($items as $item) {
                PaketKoperasiItem::create([
                    'paket_koperasi_id' => $paket->id,
                    'produk_id' => $item['produk_id'],
                    'jumlah' => (int) $item['jumlah'],
                ]);
            }

            return $paket;
        });

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Paket Bundling '{$data['nama_paket']}' berhasil dibuat.",
                'paket' => $paket,
            ]);
        }

        return redirect()->route('koperasi.paket.index')->with('success', "Paket Bundling '{$data['nama_paket']}' berhasil dibuat.");
    }

    /**
     * Form Modal Edit Paket Bundling
     */
    public function edit(PaketKoperasi $paket)
    {
        $paket->load('items.produk');
        $levels = Level::with('tingkat')->orderBy('urutan_level')->get();
        $tingkats = Tingkat::orderBy('id')->get();
        $produks = ProdukKoperasi::where('status', 'Aktif')->orderBy('nama_produk')->get();

        return view('koperasi.paket.form', compact('paket', 'levels', 'tingkats', 'produks'));
    }

    /**
     * Update Paket Bundling
     */
    public function update(PaketKoperasiRequest $request, PaketKoperasi $paket)
    {
        $data = $request->validated();

        if ($request->hasFile('foto')) {
            if ($paket->foto && Storage::disk('public')->exists($paket->foto)) {
                Storage::disk('public')->delete($paket->foto);
            }
            $data['foto'] = $request->file('foto')->store('uploads/koperasi/paket', 'public');
        }

        DB::transaction(function () use ($data, $paket) {
            $items = $data['items'] ?? [];
            $totalHpp = 0.00;

            // Hapus items lama
            $paket->items()->delete();

            foreach ($items as $item) {
                $prod = ProdukKoperasi::find($item['produk_id']);
                if ($prod) {
                    $totalHpp += (float) $prod->harga_beli * (int) $item['jumlah'];
                }

                PaketKoperasiItem::create([
                    'paket_koperasi_id' => $paket->id,
                    'produk_id' => $item['produk_id'],
                    'jumlah' => (int) $item['jumlah'],
                ]);
            }

            $data['total_hpp_komponen'] = $totalHpp;

            $updateData = [
                'kode_paket' => $data['kode_paket'] ?? $paket->kode_paket,
                'nama_paket' => $data['nama_paket'],
                'level_id' => $data['level_id'] ?? null,
                'tingkat_id' => $data['tingkat_id'] ?? null,
                'harga_paket' => $data['harga_paket'],
                'total_hpp_komponen' => $totalHpp,
                'is_active' => (bool) ($data['is_active'] ?? true),
                'deskripsi' => $data['deskripsi'] ?? null,
            ];

            if (isset($data['foto'])) {
                $updateData['foto'] = $data['foto'];
            }

            $paket->update($updateData);
        });

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Paket Bundling '{$paket->nama_paket}' berhasil diperbarui.",
                'paket' => $paket,
            ]);
        }

        return redirect()->route('koperasi.paket.index')->with('success', "Paket Bundling '{$paket->nama_paket}' berhasil diperbarui.");
    }

    /**
     * Hapus Paket Bundling
     */
    public function destroy(Request $request, PaketKoperasi $paket)
    {
        if ($paket->penjualanDetails()->exists()) {
            $msg = "Paket '{$paket->nama_paket}' tidak dapat dihapus karena sudah memiliki riwayat transaksi penjualan. Anda dapat menonaktifkannya.";
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => $msg], 422);
            }
            return back()->with('error', $msg);
        }

        if ($paket->foto && Storage::disk('public')->exists($paket->foto)) {
            Storage::disk('public')->delete($paket->foto);
        }

        $nama = $paket->nama_paket;
        $paket->items()->delete();
        $paket->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Paket '{$nama}' berhasil dihapus.",
            ]);
        }

        return redirect()->route('koperasi.paket.index')->with('success', "Paket '{$nama}' berhasil dihapus.");
    }

    /**
     * Toggle Status Aktif/Nonaktif Paket
     */
    public function toggleStatus(PaketKoperasi $paket)
    {
        $newStatus = !$paket->is_active;
        $paket->update(['is_active' => $newStatus]);

        return response()->json([
            'success' => true,
            'is_active' => $newStatus,
            'message' => "Status paket '{$paket->nama_paket}' berhasil diubah."
        ]);
    }
}
