<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Keuangan\AkunKeuangan;
use App\Models\Keuangan\Bank;
use App\Models\Keuangan\KategoriKeuangan;
use App\Models\Keuangan\Pinjaman;
use App\Models\Keuangan\TransaksiKeuangan;
use App\Models\TahunPelajaran;
use Illuminate\Http\Request;

class LaporanKeuanganMadrasahController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());
        $akunId = $request->input('akun_keuangan_id');

        // 1. Buku Kas Transaksi
        $queryTrx = TransaksiKeuangan::with(['akunKeuangan', 'kategoriKeuangan', 'bank', 'user'])
            ->where('status', 'sukses')
            ->whereDate('tanggal_transaksi', '>=', $startDate)
            ->whereDate('tanggal_transaksi', '<=', $endDate)
            ->when($akunId, function ($q, $akunId) {
                $q->where('akun_keuangan_id', $akunId);
            });

        $transaksis = (clone $queryTrx)->orderBy('tanggal_transaksi', 'asc')->orderBy('id', 'asc')->get();

        $totalPemasukan = $transaksis->whereIn('jenis_transaksi', ['pemasukan', 'simpanan'])->sum('nominal');
        $totalPengeluaran = $transaksis->where('jenis_transaksi', 'pengeluaran')->sum('nominal');
        $netCashFlow = $totalPemasukan - $totalPengeluaran;

        // 2. Posisi Kas & Bank Saat Ini
        $akuns = AkunKeuangan::active()->orderBy('nama_akun', 'asc')->get();
        $banks = Bank::active()->orderBy('nama_bank', 'asc')->get();
        $totalSaldoSemuaKas = $akuns->sum('saldo_berjalan');
        $totalSaldoSemuaBank = $banks->sum('saldo');

        // 3. Portofolio Pinjaman
        $pinjamans = Pinjaman::with(['nasabah', 'jaminans', 'angsurans'])
            ->whereDate('tanggal_pengajuan', '>=', $startDate)
            ->whereDate('tanggal_pengajuan', '<=', $endDate)
            ->orderBy('id', 'desc')
            ->get();

        $totalPencairanPinjaman = Pinjaman::whereIn('status', ['dicairkan', 'lunas'])->sum('nominal_pencairan');
        $totalPengembalianPinjaman = Pinjaman::whereIn('status', ['dicairkan', 'lunas'])->sum('total_terbayar');
        $totalSisaPiutang = Pinjaman::where('status', 'dicairkan')->sum('sisa_pinjaman');

        // 4. Kategori Breakdown
        $pemasukanPerKategori = TransaksiKeuangan::selectRaw('kategori_keuangan_id, SUM(nominal) as total')
            ->where('status', 'sukses')
            ->whereIn('jenis_transaksi', ['pemasukan', 'simpanan'])
            ->whereDate('tanggal_transaksi', '>=', $startDate)
            ->whereDate('tanggal_transaksi', '<=', $endDate)
            ->groupBy('kategori_keuangan_id')
            ->with('kategoriKeuangan')
            ->get();

        $pengeluaranPerKategori = TransaksiKeuangan::selectRaw('kategori_keuangan_id, SUM(nominal) as total')
            ->where('status', 'sukses')
            ->where('jenis_transaksi', 'pengeluaran')
            ->whereDate('tanggal_transaksi', '>=', $startDate)
            ->whereDate('tanggal_transaksi', '<=', $endDate)
            ->groupBy('kategori_keuangan_id')
            ->with('kategoriKeuangan')
            ->get();

        return view('keuangan.laporan.index', compact(
            'startDate',
            'endDate',
            'akunId',
            'transaksis',
            'totalPemasukan',
            'totalPengeluaran',
            'netCashFlow',
            'akuns',
            'banks',
            'totalSaldoSemuaKas',
            'totalSaldoSemuaBank',
            'pinjamans',
            'totalPencairanPinjaman',
            'totalPengembalianPinjaman',
            'totalSisaPiutang',
            'pemasukanPerKategori',
            'pengeluaranPerKategori'
        ));
    }

    public function cetakBukuKas(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());
        $akunId = $request->input('akun_keuangan_id');

        $selectedAkun = $akunId ? AkunKeuangan::find($akunId) : null;

        $query = TransaksiKeuangan::with(['akunKeuangan', 'kategoriKeuangan', 'bank', 'user'])
            ->where('status', 'sukses')
            ->whereDate('tanggal_transaksi', '>=', $startDate)
            ->whereDate('tanggal_transaksi', '<=', $endDate)
            ->when($akunId, function ($q, $akunId) {
                $q->where('akun_keuangan_id', $akunId);
            })
            ->orderBy('tanggal_transaksi', 'asc')
            ->orderBy('id', 'asc');

        $transaksis = $query->get();

        $totalPemasukan = $transaksis->whereIn('jenis_transaksi', ['pemasukan', 'simpanan'])->sum('nominal');
        $totalPengeluaran = $transaksis->where('jenis_transaksi', 'pengeluaran')->sum('nominal');
        $netCashFlow = $totalPemasukan - $totalPengeluaran;

        return view('keuangan.laporan.cetak-buku-kas', compact(
            'startDate',
            'endDate',
            'selectedAkun',
            'transaksis',
            'totalPemasukan',
            'totalPengeluaran',
            'netCashFlow'
        ));
    }

    public function cetakLaporanPinjaman(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfYear()->toDateString());
        $endDate = $request->input('end_date', now()->endOfYear()->toDateString());
        $status = $request->input('status');

        $pinjamans = Pinjaman::with(['nasabah', 'akunKeuangan', 'bank', 'jaminans', 'angsurans'])
            ->whereDate('tanggal_pengajuan', '>=', $startDate)
            ->whereDate('tanggal_pengajuan', '<=', $endDate)
            ->when($status, function ($q, $status) {
                $q->where('status', $status);
            })
            ->orderBy('tanggal_pengajuan', 'asc')
            ->get();

        $totalPinjaman = $pinjamans->sum('nominal_pinjaman');
        $totalPencairan = $pinjamans->whereIn('status', ['dicairkan', 'lunas'])->sum('nominal_pencairan');
        $totalTerbayar = $pinjamans->whereIn('status', ['dicairkan', 'lunas'])->sum('total_terbayar');
        $totalSisa = $pinjamans->where('status', 'dicairkan')->sum('sisa_pinjaman');

        return view('keuangan.laporan.cetak-laporan-pinjaman', compact(
            'startDate',
            'endDate',
            'status',
            'pinjamans',
            'totalPinjaman',
            'totalPencairan',
            'totalTerbayar',
            'totalSisa'
        ));
    }
}
