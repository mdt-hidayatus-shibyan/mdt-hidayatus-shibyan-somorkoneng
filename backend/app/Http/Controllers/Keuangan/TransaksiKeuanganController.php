<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Keuangan\TransaksiKeuanganRequest;
use App\Models\Keuangan\AkunKeuangan;
use App\Models\Keuangan\Bank;
use App\Models\Keuangan\KategoriKeuangan;
use App\Models\Keuangan\TransaksiKeuangan;
use App\Models\TahunPelajaran;
use App\Services\Keuangan\TransaksiKeuanganService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TransaksiKeuanganController extends Controller
{
    public function __construct(
        protected TransaksiKeuanganService $transaksiService
    ) {}

    public function index(Request $request)
    {
        $search = $request->input('search');
        $jenis = $request->input('jenis_transaksi');
        $akunId = $request->input('akun_keuangan_id');
        $kategoriId = $request->input('kategori_keuangan_id');
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());

        $query = TransaksiKeuangan::with(['akunKeuangan', 'kategoriKeuangan', 'bank', 'akunTujuan', 'bankTujuan', 'user', 'tahunPelajaran'])
            ->whereDate('tanggal_transaksi', '>=', $startDate)
            ->whereDate('tanggal_transaksi', '<=', $endDate)
            ->when($search, function ($q, $search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('kode_transaksi', 'like', "%{$search}%")
                        ->orWhere('nomor_referensi', 'like', "%{$search}%")
                        ->orWhere('keterangan', 'like', "%{$search}%");
                });
            })
            ->when($jenis, function ($q, $jenis) {
                $q->where('jenis_transaksi', $jenis);
            })
            ->when($akunId, function ($q, $akunId) {
                $q->where('akun_keuangan_id', $akunId);
            })
            ->when($kategoriId, function ($q, $kategoriId) {
                $q->where('kategori_keuangan_id', $kategoriId);
            });

        $transaksis = (clone $query)->orderBy('tanggal_transaksi', 'desc')->orderBy('id', 'desc')->paginate(20)->withQueryString();

        $totalPemasukan = (clone $query)->whereIn('jenis_transaksi', ['pemasukan', 'simpanan'])->where('status', 'sukses')->sum('nominal');
        $totalPengeluaran = (clone $query)->where('jenis_transaksi', 'pengeluaran')->where('status', 'sukses')->sum('nominal');
        $totalMutasi = (clone $query)->where('jenis_transaksi', 'mutasi')->where('status', 'sukses')->sum('nominal');
        $surplusDefisit = $totalPemasukan - $totalPengeluaran;

        $akuns = AkunKeuangan::active()->orderBy('nama_akun', 'asc')->get();
        $kategoris = KategoriKeuangan::active()->with('children')->whereNull('parent_id')->orderBy('nama_kategori', 'asc')->get();
        $banks = Bank::active()->orderBy('nama_bank', 'asc')->get();

        if ($request->ajax() && $request->has('table_only')) {
            return view('keuangan.transaksi.list', compact('transaksis'));
        }

        return view('keuangan.transaksi.index', compact(
            'transaksis',
            'totalPemasukan',
            'totalPengeluaran',
            'totalMutasi',
            'surplusDefisit',
            'akuns',
            'kategoris',
            'banks',
            'startDate',
            'endDate'
        ));
    }

    public function create(Request $request)
    {
        $akuns = AkunKeuangan::active()->orderBy('nama_akun', 'asc')->get();
        $banks = Bank::active()->orderBy('nama_bank', 'asc')->get();
        $kategoris = KategoriKeuangan::active()->with('children')->orderBy('nama_kategori', 'asc')->get();
        $tahunPelajarans = TahunPelajaran::orderBy('id', 'desc')->get();
        $defaultJenis = $request->query('jenis', 'pemasukan');

        if ($request->ajax()) {
            return view('keuangan.transaksi.form-modal', compact('akuns', 'banks', 'kategoris', 'tahunPelajarans', 'defaultJenis'));
        }

        return view('keuangan.transaksi.create', compact('akuns', 'banks', 'kategoris', 'tahunPelajarans', 'defaultJenis'));
    }

    public function store(TransaksiKeuanganRequest $request)
    {
        try {
            $data = $request->validated();
            $buktiPath = null;

            if ($request->hasFile('bukti_transaksi')) {
                $buktiPath = $request->file('bukti_transaksi')->store('keuangan/bukti_transaksi', 'public');
            }

            $transaksi = $this->transaksiService->createTransaksi($data, $buktiPath);

            return response()->json([
                'status' => 'success',
                'message' => 'Transaksi Keuangan berhasil dicatat!',
                'redirect' => route('keuangan.transaksi.index'),
                'data' => $transaksi
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function show(Request $request, $id)
    {
        $transaksi = TransaksiKeuangan::with(['akunKeuangan', 'kategoriKeuangan', 'bank', 'akunTujuan', 'bankTujuan', 'user', 'tahunPelajaran'])->findOrFail($id);

        if ($request->ajax()) {
            return view('keuangan.transaksi.detail-modal', compact('transaksi'));
        }

        return view('keuangan.transaksi.show', compact('transaksi'));
    }

    public function batalTransaksi(Request $request, $id)
    {
        $request->validate([
            'alasan' => 'nullable|string|max:255'
        ]);

        try {
            $transaksi = $this->transaksiService->batalkanTransaksi($id, $request->input('alasan'));

            return response()->json([
                'status' => 'success',
                'message' => "Transaksi {$transaksi->kode_transaksi} berhasil dibatalkan dan saldo telah dikembalikan."
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function cetakKwitansi($id)
    {
        $transaksi = TransaksiKeuangan::with(['akunKeuangan', 'kategoriKeuangan', 'bank', 'akunTujuan', 'bankTujuan', 'user', 'tahunPelajaran'])->findOrFail($id);

        return view('keuangan.transaksi.cetak-kwitansi', compact('transaksi'));
    }
}
