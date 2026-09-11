<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Keuangan\PinjamanRequest;
use App\Models\Keuangan\AkunKeuangan;
use App\Models\Keuangan\AngsuranPinjaman;
use App\Models\Keuangan\Bank;
use App\Models\Keuangan\JaminanPinjaman;
use App\Models\Keuangan\NasabahPinjaman;
use App\Models\Keuangan\Pinjaman;
use App\Models\TahunPelajaran;
use App\Services\Keuangan\PinjamanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PinjamanController extends Controller
{
    public function __construct(
        protected PinjamanService $pinjamanService
    ) {}

    public function index(Request $request)
    {
        $status = $request->input('status');
        $search = $request->input('search');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = Pinjaman::with(['nasabah', 'akunKeuangan', 'bank', 'disetujuiOleh', 'dicairkanOleh', 'jaminans', 'angsurans'])
            ->when($status, function ($q, $status) {
                $q->where('status', $status);
            })
            ->when($search, function ($q, $search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('kode_pinjaman', 'like', "%{$search}%")
                        ->orWhere('keperluan_pinjaman', 'like', "%{$search}%")
                        ->orWhereHas('nasabah', function ($n) use ($search) {
                            $n->where('nama_lengkap', 'like', "%{$search}%")
                                ->orWhere('no_hp', 'like', "%{$search}%")
                                ->orWhere('kode_nasabah', 'like', "%{$search}%");
                        });
                });
            })
            ->when($startDate, function ($q, $startDate) {
                $q->whereDate('tanggal_pengajuan', '>=', $startDate);
            })
            ->when($endDate, function ($q, $endDate) {
                $q->whereDate('tanggal_pengajuan', '<=', $endDate);
            });

        $pinjamans = (clone $query)->orderBy('id', 'desc')->paginate(15)->withQueryString();

        // Metrics Summary
        $totalNominalPengajuan = Pinjaman::sum('nominal_pinjaman');
        $totalPencairan = Pinjaman::whereIn('status', ['dicairkan', 'lunas'])->sum('nominal_pencairan');
        $totalTerbayar = Pinjaman::whereIn('status', ['dicairkan', 'lunas'])->sum('total_terbayar');
        $totalSisaPiutang = Pinjaman::where('status', 'dicairkan')->sum('sisa_pinjaman');

        $countPengajuan = Pinjaman::where('status', 'pengajuan')->count();
        $countDisetujui = Pinjaman::where('status', 'disetujui')->count();
        $countBerjalan = Pinjaman::where('status', 'dicairkan')->count();
        $countLunas = Pinjaman::where('status', 'lunas')->count();

        if ($request->ajax() && $request->has('table_only')) {
            return view('keuangan.pinjaman.list', compact('pinjamans'));
        }

        return view('keuangan.pinjaman.index', compact(
            'pinjamans',
            'totalNominalPengajuan',
            'totalPencairan',
            'totalTerbayar',
            'totalSisaPiutang',
            'countPengajuan',
            'countDisetujui',
            'countBerjalan',
            'countLunas'
        ));
    }

    public function create(Request $request)
    {
        $nasabahs = NasabahPinjaman::active()->orderBy('nama_lengkap', 'asc')->get();
        $akuns = AkunKeuangan::active()->orderBy('nama_akun', 'asc')->get();
        $banks = Bank::active()->orderBy('nama_bank', 'asc')->get();
        $tahunPelajarans = TahunPelajaran::orderBy('id', 'desc')->get();
        $selectedNasabahId = $request->query('nasabah_id');

        return view('keuangan.pinjaman.create', compact('nasabahs', 'akuns', 'banks', 'tahunPelajarans', 'selectedNasabahId'));
    }

    public function store(PinjamanRequest $request)
    {
        try {
            $data = $request->validated();
            $jaminanList = [];

            if ($request->has('jaminan') && is_array($request->input('jaminan'))) {
                foreach ($request->input('jaminan') as $idx => $jmn) {
                    if (empty($jmn['nama_barang_jaminan'])) {
                        continue;
                    }

                    $fotoDokumen = null;
                    $fotoBarang = null;

                    if ($request->hasFile("jaminan.{$idx}.foto_dokumen")) {
                        $fotoDokumen = $request->file("jaminan.{$idx}.foto_dokumen")->store('keuangan/jaminan', 'public');
                    }
                    if ($request->hasFile("jaminan.{$idx}.foto_barang")) {
                        $fotoBarang = $request->file("jaminan.{$idx}.foto_barang")->store('keuangan/jaminan', 'public');
                    }

                    $jmn['foto_dokumen'] = $fotoDokumen;
                    $jmn['foto_barang'] = $fotoBarang;
                    $jaminanList[] = $jmn;
                }
            }

            $pinjaman = $this->pinjamanService->createPengajuan($data, $jaminanList);

            return response()->json([
                'status' => 'success',
                'message' => 'Pengajuan Pinjaman berhasil disimpan!',
                'redirect' => route('keuangan.pinjaman.show', $pinjaman->id),
                'data' => $pinjaman
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function show($id)
    {
        $pinjaman = Pinjaman::with([
            'nasabah',
            'akunKeuangan',
            'bank',
            'disetujuiOleh',
            'dicairkanOleh',
            'tahunPelajaran',
            'jaminans.penerimaJaminan',
            'jaminans.pengembaliJaminan',
            'angsurans.akunKeuangan',
            'angsurans.bank',
            'angsurans.diterimaOleh'
        ])->findOrFail($id);

        $akuns = AkunKeuangan::active()->orderBy('nama_akun', 'asc')->get();
        $banks = Bank::active()->orderBy('nama_bank', 'asc')->get();

        return view('keuangan.pinjaman.show', compact('pinjaman', 'akuns', 'banks'));
    }

    public function approve(Request $request, $id)
    {
        $request->validate(['catatan' => 'nullable|string|max:500']);

        try {
            $pinjaman = $this->pinjamanService->approvePinjaman($id, auth()->id() ?? 1, $request->input('catatan'));

            return response()->json([
                'status' => 'success',
                'message' => "Pengajuan Pinjaman {$pinjaman->kode_pinjaman} telah disetujui.",
                'data' => $pinjaman
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }

    public function tolak(Request $request, $id)
    {
        $request->validate(['alasan' => 'required|string|max:500']);

        try {
            $pinjaman = $this->pinjamanService->tolakPinjaman($id, auth()->id() ?? 1, $request->input('alasan'));

            return response()->json([
                'status' => 'success',
                'message' => "Pengajuan Pinjaman {$pinjaman->kode_pinjaman} telah ditolak.",
                'data' => $pinjaman
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }

    public function cairkan(Request $request, $id)
    {
        $request->validate([
            'tanggal_pencairan' => 'required|date',
            'akun_keuangan_id' => 'required|exists:akun_keuangans,id',
            'bank_id' => 'nullable|exists:banks,id',
        ]);

        try {
            $pinjaman = $this->pinjamanService->cairkanPinjaman($id, auth()->id() ?? 1, $request->only([
                'tanggal_pencairan',
                'akun_keuangan_id',
                'bank_id'
            ]));

            return response()->json([
                'status' => 'success',
                'message' => "Dana Pinjaman {$pinjaman->kode_pinjaman} berhasil dicairkan dan jadwal angsuran telah digenerate!",
                'data' => $pinjaman
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }

    public function bayarAngsuran(Request $request, $id)
    {
        $request->validate([
            'angsuran_id' => 'required|exists:angsuran_pinjamans,id',
            'tanggal_bayar' => 'required|date',
            'metode_pembayaran' => 'required|in:tunai,transfer_bank',
            'akun_keuangan_id' => 'required|exists:akun_keuangans,id',
            'bank_id' => 'nullable|required_if:metode_pembayaran,transfer_bank|exists:banks,id',
            'nominal_denda' => 'nullable|numeric|min:0',
            'catatan' => 'nullable|string|max:255',
            'bukti_bayar' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);

        try {
            $buktiPath = null;
            if ($request->hasFile('bukti_bayar')) {
                $buktiPath = $request->file('bukti_bayar')->store('keuangan/angsuran', 'public');
            }

            $angsuran = $this->pinjamanService->bayarAngsuran(
                (int) $request->input('angsuran_id'),
                $request->only([
                    'tanggal_bayar',
                    'metode_pembayaran',
                    'akun_keuangan_id',
                    'bank_id',
                    'nominal_denda',
                    'catatan'
                ]),
                $buktiPath
            );

            return response()->json([
                'status' => 'success',
                'message' => "Pembayaran Angsuran ke-{$angsuran->angsuran_ke} berhasil dicatat!",
                'data' => $angsuran
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }

    public function kembalikanJaminan(Request $request, $id)
    {
        $request->validate([
            'jaminan_id' => 'required|exists:jaminan_pinjamans,id',
            'catatan' => 'nullable|string|max:255'
        ]);

        try {
            $jaminan = $this->pinjamanService->kembalikanJaminan(
                (int) $request->input('jaminan_id'),
                auth()->id() ?? 1,
                $request->input('catatan')
            );

            return response()->json([
                'status' => 'success',
                'message' => "Agunan {$jaminan->nama_barang_jaminan} berhasil ditandai telah dikembalikan ke nasabah.",
                'data' => $jaminan
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }

    public function cetakSuratPerjanjian($id)
    {
        $pinjaman = Pinjaman::with([
            'nasabah',
            'akunKeuangan',
            'bank',
            'disetujuiOleh',
            'dicairkanOleh',
            'tahunPelajaran',
            'jaminans'
        ])->findOrFail($id);

        return view('keuangan.pinjaman.cetak-surat-perjanjian', compact('pinjaman'));
    }

    public function cetakTandaTerimaJaminan($id)
    {
        $pinjaman = Pinjaman::with([
            'nasabah',
            'jaminans.penerimaJaminan',
            'tahunPelajaran'
        ])->findOrFail($id);

        return view('keuangan.pinjaman.cetak-tanda-terima-jaminan', compact('pinjaman'));
    }

    public function cetakKartuAngsuran($id)
    {
        $pinjaman = Pinjaman::with([
            'nasabah',
            'akunKeuangan',
            'bank',
            'angsurans.diterimaOleh',
            'tahunPelajaran'
        ])->findOrFail($id);

        return view('keuangan.pinjaman.cetak-kartu-angsuran', compact('pinjaman'));
    }
}
