<?php

namespace App\Http\Controllers\Tabungan;

use App\Http\Controllers\Controller;
use App\Models\Tabungan\PengajuanPenarikanTabungan;
use App\Models\Tabungan\Tabungan;
use App\Services\Tabungan\PengajuanPenarikanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengajuanPenarikanController extends Controller
{
    protected $pengajuanService;

    public function __construct(PengajuanPenarikanService $pengajuanService)
    {
        $this->pengajuanService = $pengajuanService;
    }

    /**
     * Daftar Pengajuan Penarikan Dana
     */
    public function index(Request $request)
    {
        $query = PengajuanPenarikanTabungan::with(['tabungan', 'pemohon', 'verifikator']);

        if ($request->status && in_array($request->status, ['Menunggu', 'Disetujui', 'Ditolak', 'Dicairkan'])) {
            $query->where('status', $request->status);
        }

        $pengajuans = $query->orderByRaw("FIELD(status, 'Menunggu', 'Disetujui', 'Dicairkan', 'Ditolak')")
            ->orderBy('id', 'desc')
            ->paginate(20)
            ->withQueryString();

        $countMenunggu = PengajuanPenarikanTabungan::where('status', 'Menunggu')->count();
        $countDisetujui = PengajuanPenarikanTabungan::where('status', 'Disetujui')->count();
        $totalDicairkan = PengajuanPenarikanTabungan::where('status', 'Dicairkan')->sum('nominal_pengajuan');

        return view('tabungan.pengajuan.index', compact('pengajuans', 'countMenunggu', 'countDisetujui', 'totalDicairkan'));
    }

    /**
     * Simpan Pengajuan Penarikan Baru (Bisa oleh Nasabah/Admin)
     */
    public function store(Request $request)
    {
        $request->validate([
            'tabungan_id' => 'required|exists:tabungans,id',
            'nominal_pengajuan' => 'required|numeric|min:1000',
            'alasan_penarikan' => 'nullable|string|max:255',
        ]);

        try {
            $pengajuan = $this->pengajuanService->buatPengajuan(
                (int) $request->tabungan_id,
                (float) $request->nominal_pengajuan,
                $request->alasan_penarikan,
                Auth::id()
            );

            return back()->with('success', "Permohonan penarikan dana {$pengajuan->kode_pengajuan} berhasil diajukan dan sedang menunggu persetujuan Admin!");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengajukan penarikan: ' . $e->getMessage());
        }
    }

    /**
     * Persetujuan Pengajuan oleh Admin
     */
    public function setujui(Request $request, $id)
    {
        try {
            $this->pengajuanService->setujuiPengajuan((int) $id, Auth::id(), $request->catatan_admin);
            return back()->with('success', 'Pengajuan penarikan berhasil disetujui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyetujui pengajuan: ' . $e->getMessage());
        }
    }

    /**
     * Penolakan Pengajuan oleh Admin
     */
    public function tolak(Request $request, $id)
    {
        $request->validate([
            'alasan_penolakan' => 'required|string|max:255',
        ]);

        try {
            $this->pengajuanService->tolakPengajuan((int) $id, Auth::id(), $request->alasan_penolakan);
            return back()->with('success', 'Pengajuan penarikan telah ditolak.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menolak pengajuan: ' . $e->getMessage());
        }
    }

    /**
     * Eksekusi Pencairan Dana Langsung
     */
    public function cairkan(Request $request, $id)
    {
        try {
            $trx = $this->pengajuanService->cairkanPengajuan(
                (int) $id,
                Auth::id(),
                $request->metode ?? 'Tunai',
                $request->catatan
            );

            return back()->with('success', "Pencairan dana berhasil dieksekusi! Kode Transaksi: {$trx->kode_transaksi}");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mencairkan dana: ' . $e->getMessage());
        }
    }

    /**
     * Cetak Kwitansi Penarikan Dana
     */
    public function cetakKwitansi($id)
    {
        $pengajuan = PengajuanPenarikanTabungan::with(['tabungan', 'pemohon', 'verifikator', 'transaksi'])->findOrFail($id);
        return view('tabungan.pengajuan.kwitansi', compact('pengajuan'));
    }
}
