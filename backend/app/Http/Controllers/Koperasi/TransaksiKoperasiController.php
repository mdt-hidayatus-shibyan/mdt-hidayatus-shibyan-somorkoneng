<?php

namespace App\Http\Controllers\Koperasi;

use App\Http\Controllers\Controller;
use App\Models\Koperasi\PenjualanKoperasi;
use App\Models\User;
use App\Services\Koperasi\KoperasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransaksiKoperasiController extends Controller
{
    protected $koperasiService;

    public function __construct(KoperasiService $koperasiService)
    {
        $this->koperasiService = $koperasiService;
    }

    /**
     * Riwayat Seluruh Transaksi Penjualan Koperasi
     */
    public function index(Request $request)
    {
        $query = PenjualanKoperasi::with(['petugas', 'murid', 'ustadz', 'details']);

        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_akhir')) {
            $query->whereBetween('tanggal', [$request->tanggal_mulai . ' 00:00:00', $request->tanggal_akhir . ' 23:59:59']);
        } elseif ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        if ($request->filled('metode_pembayaran')) {
            $query->where('metode_pembayaran', $request->metode_pembayaran);
        }

        if ($request->filled('jenis_pelanggan')) {
            $query->where('jenis_pelanggan', $request->jenis_pelanggan);
        }

        if ($request->filled('status_pembayaran')) {
            $query->where('status_pembayaran', $request->status_pembayaran);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('petugas_id')) {
            $query->where('petugas_id', $request->petugas_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_nota', 'like', "%{$search}%")
                    ->orWhere('nama_pelanggan_umum', 'like', "%{$search}%")
                    ->orWhereHas('murid', function ($mq) use ($search) {
                        $mq->where('nama_lengkap', 'like', "%{$search}%")->orWhere('nism', 'like', "%{$search}%");
                    })
                    ->orWhereHas('ustadz', function ($uq) use ($search) {
                        $uq->where('nama_lengkap', 'like', "%{$search}%")->orWhere('nigm', 'like', "%{$search}%");
                    });
            });
        }

        $transaksis = $query->orderBy('tanggal', 'desc')->orderBy('id', 'desc')->paginate(20)->withQueryString();
        $petugasList = User::permission('akses-koperasi')->get();

        // Hitung total ringkasan dari filter
        $totalOmzet = (clone $query)->where('status', 'Selesai')->sum('total_akhir');
        $totalTrx = (clone $query)->count();
        $totalHutangAktif = PenjualanKoperasi::where('status', 'Selesai')->where('status_pembayaran', 'Belum_Lunas')->sum('total_akhir');
        $countHutangAktif = PenjualanKoperasi::where('status', 'Selesai')->where('status_pembayaran', 'Belum_Lunas')->count();

        return view('koperasi.transaksi.index', compact('transaksis', 'petugasList', 'totalOmzet', 'totalTrx', 'totalHutangAktif', 'countHutangAktif'));
    }

    /**
     * Detail Nota Transaksi Penjualan
     */
    public function show(int $id)
    {
        $penjualan = PenjualanKoperasi::with([
            'petugas',
            'petugasPelunasan',
            'murid.ruangans.level',
            'ustadz',
            'tabungan',
            'details.produk',
            'details.paket'
        ])->findOrFail($id);

        return view('koperasi.transaksi.show', compact('penjualan'));
    }

    /**
     * Memproses Pelunasan Transaksi Hutang
     */
    public function lunasiHutang(Request $request, int $id)
    {
        $request->validate([
            'metode_pelunasan' => 'required|in:Tunai,Potong_Tabungan,QRIS,Transfer',
            'nominal_bayar' => 'nullable|numeric|min:0',
            'catatan_pelunasan' => 'nullable|string|max:255',
        ]);

        try {
            $penjualan = $this->koperasiService->lunasiHutang($id, $request->all(), Auth::id());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Pelunasan hutang nota {$penjualan->nomor_nota} berhasil diproses.",
                    'redirect' => route('koperasi.transaksi.show', $penjualan->id),
                    'penjualan_id' => $penjualan->id,
                    'nomor_nota' => $penjualan->nomor_nota,
                    'total_akhir' => (float) $penjualan->total_akhir,
                    'total_akhir_format' => 'Rp ' . number_format($penjualan->total_akhir, 0, ',', '.'),
                    'nominal_bayar' => (float) $penjualan->nominal_bayar,
                    'kembalian' => (float) $penjualan->kembalian,
                    'kembalian_format' => 'Rp ' . number_format($penjualan->kembalian, 0, ',', '.'),
                    'metode_pelunasan' => $penjualan->metode_pelunasan,
                    'tanggal_pelunasan' => $penjualan->tanggal_pelunasan?->format('d/m/Y H:i'),
                ]);
            }

            return back()->with('success', "Pelunasan hutang nota {$penjualan->nomor_nota} berhasil diproses.");
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return back()->with('error', "Gagal memproses pelunasan: " . $e->getMessage());
        }
    }

    /**
     * Batalkan / Void Transaksi Penjualan
     */
    public function batal(Request $request, int $id)
    {
        $request->validate([
            'alasan' => 'required|string|max:255',
        ]);

        try {
            $penjualan = $this->koperasiService->batalTransaksi($id, $request->alasan, Auth::id());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Transaksi nota {$penjualan->nomor_nota} berhasil dibatalkan dan stok telah dikembalikan.",
                    'redirect' => route('koperasi.transaksi.show', $penjualan->id),
                ]);
            }

            return back()->with('success', "Transaksi nota {$penjualan->nomor_nota} berhasil dibatalkan.");
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return back()->with('error', "Gagal membatalkan transaksi: " . $e->getMessage());
        }
    }
}
