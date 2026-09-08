<?php

namespace App\Http\Controllers\Bantuan;

use App\Http\Controllers\Controller;
use App\Models\LaporanKendala;
use Illuminate\Http\Request;

class LaporanKendalaAdminController extends Controller
{
    /**
     * Tampilkan List Laporan Kendala, Masalah & Rekomendasi dari Ustadz
     */
    public function index(Request $request)
    {
        $status = $request->input('status', 'Semua');
        $kategori = $request->input('kategori', 'Semua');
        $search = $request->input('search');

        $laporans = LaporanKendala::with(['user', 'ustadz', 'responder'])
            ->when($status !== 'Semua' && !empty($status), fn($q) => $q->where('status', $status))
            ->when($kategori !== 'Semua' && !empty($kategori), fn($q) => $q->where('kategori', $kategori))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('judul', 'like', "%{$search}%")
                        ->orWhere('deskripsi', 'like', "%{$search}%")
                        ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('ustadz', fn($ust) => $ust->where('nama_lengkap', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->paginate(15);

        $stats = [
            'total'    => LaporanKendala::count(),
            'menunggu' => LaporanKendala::where('status', 'Menunggu')->count(),
            'diproses' => LaporanKendala::where('status', 'Diproses')->count(),
            'selesai'  => LaporanKendala::where('status', 'Selesai')->count(),
        ];

        return view('laporan-kendala-admin.index', compact('laporans', 'stats', 'status', 'kategori', 'search'));
    }

    /**
     * Tanggapi / Update Status Laporan
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status'       => 'required|in:Menunggu,Diproses,Selesai,Ditolak',
            'respon_admin' => 'nullable|string|max:1000',
        ]);

        $laporan = LaporanKendala::findOrFail($id);

        $laporan->update([
            'status'       => $request->status,
            'respon_admin' => $request->respon_admin,
            'responded_by' => auth()->id(),
            'responded_at' => now(),
        ]);

        return redirect()->back()->with('success', "Status tiket #LP-{$laporan->id} berhasil diperbarui menjadi {$request->status}.");
    }

    /**
     * Hapus Laporan
     */
    public function destroy($id)
    {
        $laporan = LaporanKendala::findOrFail($id);
        $laporan->delete();

        return redirect()->back()->with('success', "Tiket #LP-{$id} berhasil dihapus.");
    }
}
