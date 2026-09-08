<?php

namespace App\Http\Controllers\Tabungan;

use App\Http\Controllers\Controller;
use App\Models\Tabungan\Tabungan;
use App\Models\Tabungan\TransaksiTabungan;
use App\Services\Tabungan\PembagianTabunganService;
use App\Services\Tabungan\TabunganService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CekMutasiTabunganController extends Controller
{
    protected $tabunganService;
    protected $pembagianService;

    public function __construct(TabunganService $tabunganService, PembagianTabunganService $pembagianService)
    {
        $this->tabunganService = $tabunganService;
        $this->pembagianService = $pembagianService;
    }

    /**
     * Halaman Cek Mutasi Bulanan & Verifikasi Buku Tabungan
     */
    public function index(Request $request)
    {
        $queryInput = trim($request->nomor_rekening ?? $request->q ?? $request->tabungan_id ?? '');
        $tabungan = null;
        $mutasi = null;
        $kalkulasi = null;
        $adjacent = ['prev' => null, 'next' => null];

        // Deteksi prefix aktif dari database, default '1000'
        $sampleRekening = Tabungan::whereRaw('LENGTH(nomor_rekening) = 7')
            ->whereRaw('nomor_rekening REGEXP "^[0-9]+$"')
            ->latest('id')
            ->first();
        $prefix = ($sampleRekening && strlen($sampleRekening->nomor_rekening) === 7)
            ? substr($sampleRekening->nomor_rekening, 0, 4)
            : '1000';

        if ($queryInput) {
            // 1. Coba exact match nomor rekening atau ID
            $tabungan = Tabungan::with(['murid.ruangans', 'ustadz', 'ruangan', 'periodeTabungan', 'diverifikasiOleh'])
                ->where('nomor_rekening', $queryInput)
                ->orWhere('id', $queryInput)
                ->first();

            // 2. Jika input 1-4 digit angka (misal '1' atau '001'), pad dengan prefix
            if (!$tabungan && ctype_digit($queryInput) && strlen($queryInput) <= 4) {
                $padded = $prefix . str_pad($queryInput, 3, '0', STR_PAD_LEFT);
                $tabungan = Tabungan::with(['murid.ruangans', 'ustadz', 'ruangan', 'periodeTabungan', 'diverifikasiOleh'])
                    ->where('nomor_rekening', $padded)
                    ->first();

                if (!$tabungan) {
                    $tabungan = Tabungan::with(['murid.ruangans', 'ustadz', 'ruangan', 'periodeTabungan', 'diverifikasiOleh'])
                        ->where('nomor_rekening', 'like', '%' . str_pad($queryInput, 3, '0', STR_PAD_LEFT))
                        ->first();
                }
            }

            // 3. Jika belum ketemu, coba pencarian murid/ustadz
            if (!$tabungan) {
                $tabungan = Tabungan::with(['murid.ruangans', 'ustadz', 'ruangan', 'periodeTabungan', 'diverifikasiOleh'])
                    ->whereHas('murid', function ($m) use ($queryInput) {
                        $m->where('nism', $queryInput)
                            ->orWhere('nama_lengkap', 'like', "%{$queryInput}%");
                    })
                    ->orWhereHas('ustadz', function ($u) use ($queryInput) {
                        $u->where('nigm', $queryInput)
                            ->orWhere('nama_lengkap', 'like', "%{$queryInput}%");
                    })
                    ->orWhere('nama_nasabah_umum', 'like', "%{$queryInput}%")
                    ->first();
            }
        }

        $filterBulan = $request->bulan; // e.g. '2026-09' or null / 'semua'

        if ($tabungan) {
            $kalkulasi = $this->tabunganService->hitungPotongan($tabungan);
            $mutasi = $this->tabunganService->getRekapMutasiBulanan($tabungan, $filterBulan);
            $adjacent = $this->tabunganService->getAdjacentTabungan($tabungan);
        }

        // 8 verifikasi terakhir untuk widget samping
        $riwayatVerifikasiTerakhir = Tabungan::whereNotNull('diverifikasi_pada')
            ->with(['murid.ruangans', 'ustadz', 'ruangan', 'diverifikasiOleh'])
            ->orderBy('diverifikasi_pada', 'desc')
            ->limit(8)
            ->get();

        return view('tabungan.mutasi.index', compact(
            'tabungan',
            'queryInput',
            'prefix',
            'filterBulan',
            'mutasi',
            'kalkulasi',
            'adjacent',
            'riwayatVerifikasiTerakhir'
        ));
    }

    /**
     * Simpan Status Verifikasi Buku Tabungan Fisik
     */
    public function verifikasi(Request $request, int $id)
    {
        $request->validate([
            'buku_tabungan_ada' => 'nullable|boolean',
            'status_verifikasi' => 'required|in:Belum Diverifikasi,Cocok,Selisih,Buku Tidak Ada',
            'saldo_buku_fisik' => 'nullable|numeric|min:0',
            'catatan_verifikasi' => 'nullable|string|max:255',
            'lanjut_berikutnya' => 'nullable|boolean',
        ]);

        try {
            $bukuAda = $request->has('buku_tabungan_ada') ? $request->boolean('buku_tabungan_ada') : true;
            $status = $bukuAda ? $request->status_verifikasi : 'Buku Tidak Ada';
            $saldoFisik = ($bukuAda && $request->filled('saldo_buku_fisik')) ? (float) $request->saldo_buku_fisik : null;

            $tabungan = $this->pembagianService->verifikasiRekening(
                $id,
                $status,
                $saldoFisik,
                $request->catatan_verifikasi,
                Auth::id(),
                $bukuAda
            );

            if ($status === 'Buku Tidak Ada') {
                $pesan = "Status rekening No. {$tabungan->nomor_rekening} ({$tabungan->nama_nasabah}) dicatat BUKU TIDAK ADA / HILANG. Anda dapat mencetak lembar mutasi A6 sebagai pengganti buku fisik.";
            } elseif ($status === 'Cocok') {
                $pesan = "Alhamdulillah! Buku tabungan No. {$tabungan->nomor_rekening} ({$tabungan->nama_nasabah}) terverifikasi COCOK dan SIAP DIKEMBALIKAN.";
            } elseif ($status === 'Selisih') {
                $pesan = "Status verifikasi buku No. {$tabungan->nomor_rekening} dicatat dengan status ADA SELISIH.";
            } else {
                $pesan = "Status verifikasi buku No. {$tabungan->nomor_rekening} berhasil diperbarui.";
            }

            // Jika user memilih lanjut ke buku berikutnya secara berurutan
            if ($request->boolean('lanjut_berikutnya')) {
                $adjacent = $this->tabunganService->getAdjacentTabungan($tabungan);
                if ($adjacent['next']) {
                    return redirect()->route('tabungan.cek-mutasi.index', ['nomor_rekening' => $adjacent['next']->nomor_rekening])
                        ->with('success', $pesan);
                }
            }

            return redirect()->route('tabungan.cek-mutasi.index', ['nomor_rekening' => $tabungan->nomor_rekening])
                ->with('success', $pesan);
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memverifikasi buku tabungan: ' . $e->getMessage());
        }
    }

    /**
     * Cetak Lembar Rekap Mutasi & Verifikasi Buku Tabungan (A4)
     */
    public function cetak(Request $request, int $id)
    {
        $tabungan = Tabungan::with(['murid.ruangans', 'ustadz', 'ruangan', 'periodeTabungan', 'diverifikasiOleh'])->findOrFail($id);
        $kalkulasi = $this->tabunganService->hitungPotongan($tabungan);
        $mutasi = $this->tabunganService->getRekapMutasiBulanan($tabungan);

        return view('tabungan.mutasi.cetak', compact('tabungan', 'kalkulasi', 'mutasi'));
    }

    /**
     * Cetak Lembar Mutasi Pengganti Buku Tabungan Hilang / Tidak Ada (Ukuran A6)
     */
    public function cetakA6(Request $request, int $id)
    {
        $tabungan = Tabungan::with(['murid.ruangans', 'ustadz', 'ruangan', 'periodeTabungan', 'diverifikasiOleh'])->findOrFail($id);
        $kalkulasi = $this->tabunganService->hitungPotongan($tabungan);
        $mutasi = $this->tabunganService->getRekapMutasiBulanan($tabungan);

        return view('tabungan.mutasi.cetak_a6', compact('tabungan', 'kalkulasi', 'mutasi'));
    }
}
