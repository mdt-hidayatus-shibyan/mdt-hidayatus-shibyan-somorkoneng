<?php

namespace App\Http\Controllers\Tabungan;

use App\Http\Controllers\Controller;
use App\Models\Tabungan\TabunganKomplain;
use App\Services\Tabungan\TabunganService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KomplainTabunganController extends Controller
{
    protected $tabunganService;

    public function __construct(TabunganService $tabunganService)
    {
        $this->tabunganService = $tabunganService;
    }

    /**
     * Halaman Daftar Komplain Setor Tunai Tabungan Santri
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'semua');
        $q = trim($request->get('q', ''));

        $query = TabunganKomplain::with([
            'transaksiTabungan.petugas',
            'tabungan.murid.ruangans',
            'tabungan.ruangan',
            'murid',
            'wali',
            'diverifikasiOleh'
        ]);

        if ($status && $status !== 'semua') {
            $query->where('status', $status);
        }

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('kode_komplain', 'like', "%{$q}%")
                    ->orWhereHas('transaksiTabungan', fn($t) => $t->where('kode_transaksi', 'like', "%{$q}%"))
                    ->orWhereHas('murid', fn($m) => $m->where('nama_lengkap', 'like', "%{$q}%")->orWhere('nism', 'like', "%{$q}%"))
                    ->orWhere('alasan', 'like', "%{$q}%");
            });
        }

        $komplains = $query->orderByRaw("CASE WHEN status = 'Menunggu_Verifikasi' THEN 0 ELSE 1 END")
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Statistik Counter
        $counts = [
            'total' => TabunganKomplain::count(),
            'pending' => TabunganKomplain::where('status', 'Menunggu_Verifikasi')->count(),
            'disetujui' => TabunganKomplain::where('status', 'Disetujui')->count(),
            'ditolak' => TabunganKomplain::where('status', 'Ditolak')->count(),
            'total_selisih_pending' => (float) TabunganKomplain::where('status', 'Menunggu_Verifikasi')->sum('selisih'),
        ];

        return view('tabungan.komplain.index', compact('komplains', 'status', 'q', 'counts'));
    }

    /**
     * Detail Komplain via AJAX / JSON
     */
    public function show($id)
    {
        $komplain = TabunganKomplain::with([
            'transaksiTabungan.petugas',
            'tabungan.murid',
            'tabungan.ruangan',
            'murid',
            'wali',
            'diverifikasiOleh'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $komplain
        ]);
    }

    /**
     * Eksekusi Verifikasi Komplain (Setujui / Tolak)
     */
    public function verifikasi(Request $request, $id)
    {
        $request->validate([
            'tindakan' => 'required|in:setujui,tolak',
            'catatan' => 'required_if:tindakan,tolak|nullable|string|max:500',
            'nominal_disetujui' => 'nullable|numeric|min:1000',
        ], [
            'tindakan.required' => 'Pilih tindakan verifikasi.',
            'catatan.required_if' => 'Alasan penolakan wajib diisi jika komplain ditolak.',
        ]);

        try {
            $user = Auth::user();
            $nominalDisetujui = $request->filled('nominal_disetujui') ? (float) $request->nominal_disetujui : null;

            $komplain = $this->tabunganService->verifikasiKomplainSetoran(
                (int) $id,
                $request->tindakan,
                $request->catatan ?: ($request->tindakan === 'setujui' ? 'Komplain disetujui, saldo telah disesuaikan.' : 'Komplain ditolak.'),
                $user->id,
                $nominalDisetujui
            );

            $pesan = $request->tindakan === 'setujui'
                ? "Alhamdulillah, komplain {$komplain->kode_komplain} berhasil DISETUJUI. Saldo tabungan santri {$komplain->murid->nama_lengkap} telah disesuaikan menjadi Rp " . number_format($komplain->transaksiTabungan->nominal_bersih, 0, ',', '.') . "."
                : "Komplain {$komplain->kode_komplain} telah DITOLAK.";

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $pesan,
                    'data' => $komplain
                ]);
            }

            return redirect()->back()->with('success', $pesan);
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memproses verifikasi komplain: ' . $e->getMessage()
                ], 400);
            }

            return redirect()->back()->with('error', 'Gagal memproses verifikasi: ' . $e->getMessage());
        }
    }
}
