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
     * Halaman Simulasi, Verifikasi Buku Fisik & Rekap Pembagian Tabungan
     */
    public function index(Request $request)
    {
        $periodes = PeriodeTabungan::with('tahunPelajaran')->orderBy('id', 'desc')->get();
        $periodeId = $request->periode_id ?? PeriodeTabungan::where('is_active', true)->value('id') ?? $periodes->first()?->id;

        $jenisNasabah = $request->jenis_nasabah; // null (Semua), 'Murid', 'Ustadz', 'Kas Ruangan', 'Umum'
        $ruanganId = $request->ruangan_id;
        $statusVerifikasi = $request->status_verifikasi;

        $daftarRuangan = collect();
        $simulasi = null;

        if ($periodeId) {
            $periode = PeriodeTabungan::find($periodeId);
            $daftarRuangan = Ruangan::where('tahun_pelajaran_id', $periode->tahun_pelajaran_id)->orderBy('nama_ruangan')->get();

            $simulasi = $this->pembagianService->hitungSimulasiPembagian(
                (int) $periodeId,
                $jenisNasabah ? (string) $jenisNasabah : null,
                $ruanganId ? (int) $ruanganId : null,
                $statusVerifikasi ? (string) $statusVerifikasi : null
            );
        }

        return view('tabungan.pembagian.index', compact(
            'periodes',
            'periodeId',
            'jenisNasabah',
            'ruanganId',
            'statusVerifikasi',
            'daftarRuangan',
            'simulasi'
        ));
    }

    /**
     * Verifikasi pencocokan saldo buku fisik individual
     */
    public function verifikasi(Request $request, int $id)
    {
        $request->validate([
            'status_verifikasi' => 'required|in:Belum Diverifikasi,Cocok,Selisih',
            'saldo_buku_fisik' => 'nullable|numeric|min:0',
            'catatan_verifikasi' => 'nullable|string|max:255',
        ]);

        try {
            $tabungan = $this->pembagianService->verifikasiRekening(
                $id,
                $request->status_verifikasi,
                $request->filled('saldo_buku_fisik') ? (float) $request->saldo_buku_fisik : null,
                $request->catatan_verifikasi,
                Auth::id()
            );

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Status verifikasi rekening berhasil diperbarui.',
                    'tabungan' => $tabungan,
                ]);
            }

            return back()->with('success', "Status verifikasi rekening {$tabungan->nomor_rekening} berhasil diperbarui.");
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return back()->with('error', 'Gagal memverifikasi rekening: ' . $e->getMessage());
        }
    }

    /**
     * Verifikasi massal: Menandai semua rekening yang tersaring sebagai 'Cocok'
     */
    public function verifikasiSemua(Request $request)
    {
        $request->validate([
            'periode_id' => 'required|exists:periode_tabungans,id',
            'jenis_nasabah' => 'nullable|in:Murid,Ustadz,Kas Ruangan,Umum',
            'ruangan_id' => 'nullable|exists:ruangans,id',
        ]);

        try {
            $jumlah = $this->pembagianService->verifikasiSemuaCocok(
                (int) $request->periode_id,
                $request->jenis_nasabah ? (string) $request->jenis_nasabah : null,
                $request->ruangan_id ? (int) $request->ruangan_id : null,
                Auth::id()
            );

            return back()->with('success', "Berhasil menandai {$jumlah} rekening sebagai 'Cocok' dengan buku tabungan!");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses verifikasi massal: ' . $e->getMessage());
        }
    }

    /**
     * Eksekusi Pembagian Serentak Massal
     */
    public function eksekusi(Request $request)
    {
        $request->validate([
            'periode_id' => 'required|exists:periode_tabungans,id',
            'tanggal' => 'required|date',
            'jenis_nasabah' => 'nullable|in:Murid,Ustadz,Kas Ruangan,Umum',
            'ruangan_id' => 'nullable|exists:ruangans,id',
        ]);

        try {
            $jumlah = $this->pembagianService->eksekusiPembagianMassal(
                (int) $request->periode_id,
                $request->jenis_nasabah ? (string) $request->jenis_nasabah : null,
                $request->ruangan_id ? (int) $request->ruangan_id : null,
                Auth::id(),
                $request->tanggal
            );

            $targetLabel = $request->jenis_nasabah ? $request->jenis_nasabah : 'semua nasabah';
            return back()->with('success', "Berhasil mengeksekusi pembagian tabungan untuk {$jumlah} rekening ({$targetLabel})!");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses pembagian tabungan: ' . $e->getMessage());
        }
    }

    /**
     * Cetak Rekap Laporan Pembagian Tabungan
     */
    public function cetakLaporan(Request $request)
    {
        $periodeId = $request->periode_id;
        $jenisNasabah = $request->jenis_nasabah;
        $ruanganId = $request->ruangan_id;
        $statusVerifikasi = $request->status_verifikasi;

        $simulasi = $this->pembagianService->hitungSimulasiPembagian(
            (int) $periodeId,
            $jenisNasabah ? (string) $jenisNasabah : null,
            $ruanganId ? (int) $ruanganId : null,
            $statusVerifikasi ? (string) $statusVerifikasi : null
        );

        $ruangan = $ruanganId ? Ruangan::find($ruanganId) : null;

        return view('tabungan.pembagian.cetak_laporan', compact('simulasi', 'ruangan', 'jenisNasabah'));
    }
}
