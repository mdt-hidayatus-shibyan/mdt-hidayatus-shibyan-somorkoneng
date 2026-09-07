<?php

namespace App\Http\Controllers\Tabungan;

use App\Http\Controllers\Controller;
use App\Models\Ruangan;
use App\Models\Tabungan\PeriodeTabungan;
use App\Services\Tabungan\PembagianTabunganService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PembagianTabunganController extends Controller
{
    protected $pembagianService;

    public function __construct(PembagianTabunganService $pembagianService)
    {
        $this->pembagianService = $pembagianService;
    }

    /**
     * Halaman Simulasi & Rekap Pembagian Tabungan Murid
     */
    public function index(Request $request)
    {
        $periodes = PeriodeTabungan::with('tahunPelajaran')->orderBy('id', 'desc')->get();
        $periodeId = $request->periode_id ?? PeriodeTabungan::where('is_active', true)->value('id') ?? $periodes->first()?->id;

        $daftarRuangan = collect();
        $simulasi = null;

        if ($periodeId) {
            $periode = PeriodeTabungan::find($periodeId);
            $daftarRuangan = Ruangan::where('tahun_pelajaran_id', $periode->tahun_pelajaran_id)->orderBy('nama_ruangan')->get();
            $ruanganId = $request->ruangan_id;

            $simulasi = $this->pembagianService->hitungSimulasiPembagian((int) $periodeId, $ruanganId ? (int) $ruanganId : null);
        }

        return view('tabungan.pembagian.index', compact('periodes', 'periodeId', 'daftarRuangan', 'simulasi'));
    }

    /**
     * Eksekusi Pembagian Serentak Massal
     */
    public function eksekusi(Request $request)
    {
        $request->validate([
            'periode_id' => 'required|exists:periode_tabungans,id',
            'tanggal' => 'required|date',
            'ruangan_id' => 'nullable|exists:ruangans,id',
        ]);

        try {
            $jumlah = $this->pembagianService->eksekusiPembagianMassal(
                (int) $request->periode_id,
                $request->ruangan_id ? (int) $request->ruangan_id : null,
                Auth::id(),
                $request->tanggal
            );

            return back()->with('success', "Berhasil mengeksekusi pembagian tabungan untuk {$jumlah} rekening murid!");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses pembagian tabungan: ' . $e->getMessage());
        }
    }

    /**
     * Cetak Rekap Laporan Pembagian Tabungan Kelas
     */
    public function cetakLaporan(Request $request)
    {
        $periodeId = $request->periode_id;
        $ruanganId = $request->ruangan_id;

        $simulasi = $this->pembagianService->hitungSimulasiPembagian((int) $periodeId, $ruanganId ? (int) $ruanganId : null);
        $ruangan = $ruanganId ? Ruangan::find($ruanganId) : null;

        return view('tabungan.pembagian.cetak_laporan', compact('simulasi', 'ruangan'));
    }
}
