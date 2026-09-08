<?php

namespace App\Http\Controllers\Tabungan;

use App\Http\Controllers\Controller;
use App\Models\Tabungan\KategoriPenarikan;
use App\Models\Tabungan\PengaturanPotonganTabungan;
use App\Models\Tabungan\PeriodeTabungan;
use App\Models\Tabungan\Tabungan;
use App\Models\Tabungan\TransaksiTabungan;
use App\Services\Tabungan\TabunganService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RincianTabunganController extends Controller
{
    protected $tabunganService;

    public function __construct(TabunganService $tabunganService)
    {
        $this->tabunganService = $tabunganService;
    }

    /**
     * Halaman Utama Rincian & Rekap Kas Tabungan Madrasah
     */
    public function index(Request $request)
    {
        $data = $this->prepareRincianData($request);

        return view('tabungan.rincian.index', $data);
    }

    /**
     * Halaman Cetak Berita Acara & Laporan Rincian Kas Tabungan
     */
    public function cetak(Request $request)
    {
        $data = $this->prepareRincianData($request);

        return view('tabungan.rincian.cetak', $data);
    }

    /**
     * Siapkan seluruh kalkulasi data rincian tabungan
     */
    protected function prepareRincianData(Request $request): array
    {
        $tahun = (int) ($request->tahun ?: date('Y'));
        $periodeId = $request->periode_id ? (int) $request->periode_id : null;

        // Query dasar rekening tabungan
        $tabunganQuery = Tabungan::query();
        if ($periodeId) {
            $tabunganQuery->where('periode_tabungan_id', $periodeId);
        }

        // 1. KONTROL KAS FISIK & KEWAJIBAN MADRASAH
        $totalKasFisik = (float) (clone $tabunganQuery)->sum('saldo');
        $totalSetorSemua = (float) (clone $tabunganQuery)->sum('total_setor');
        $totalTarikSemua = (float) (clone $tabunganQuery)->sum('total_tarik');
        $totalRekening = (clone $tabunganQuery)->count();
        $totalRekeningAktif = (clone $tabunganQuery)->where('status', 'Aktif')->count();

        // Rekap Potongan & Hak Bersih per Jenis Nasabah
        $jenisNasabahList = ['Murid', 'Ustadz', 'Kas Ruangan', 'Umum'];
        $rekapPotongan = [];
        $totalHakBersihNasabah = 0;
        $totalPotonganMadrasah = 0;

        foreach ($jenisNasabahList as $jenis) {
            $rekeningJenis = (clone $tabunganQuery)->where('jenis_nasabah', $jenis)->get();
            $jumlahRekening = $rekeningJenis->count();
            $totalSetorJenis = (float) $rekeningJenis->sum('total_setor');
            $totalTarikJenis = (float) $rekeningJenis->sum('total_tarik');
            $totalSaldoJenis = (float) $rekeningJenis->sum('saldo');

            // Persentase potongan yang berlaku
            $persenPotongan = PengaturanPotonganTabungan::getPersentase($jenis, $periodeId);

            // Alokasi potongan madrasah dihitung per rekening dengan pembulatan ke kelipatan Rp 100
            $potonganJenis = 0;
            $sisaHakDitarik = 0;
            $hakBersihTotal = 0;

            foreach ($rekeningJenis as $rek) {
                $calc = $this->tabunganService->hitungPotongan($rek);
                $potonganJenis += $calc['nominal_potongan'];
                $sisaHakDitarik += $calc['saldo_dapat_ditarik'];
                $hakBersihTotal += $calc['saldo_bersih_total'];
            }

            $totalHakBersihNasabah += $sisaHakDitarik;
            $totalPotonganMadrasah += $potonganJenis;

            $rekapPotongan[$jenis] = [
                'jenis_nasabah' => $jenis,
                'jumlah_rekening' => $jumlahRekening,
                'total_setor' => $totalSetorJenis,
                'total_tarik' => $totalTarikJenis,
                'saldo_saat_ini' => $totalSaldoJenis,
                'persentase_potongan' => $persenPotongan,
                'nominal_potongan' => $potonganJenis,
                'hak_bersih_total' => $hakBersihTotal,
                'sisa_hak_ditarik' => $sisaHakDitarik,
            ];
        }

        // 2. PEROLEHAN UANG PER BULAN MASEHI (Januari - Desember)
        $namaBulanIndo = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $transaksiTahunQuery = TransaksiTabungan::whereYear('tanggal', $tahun);
        if ($periodeId) {
            $transaksiTahunQuery->whereHas('tabungan', function ($q) use ($periodeId) {
                $q->where('periode_tabungan_id', $periodeId);
            });
        }
        $transaksiTahun = $transaksiTahunQuery->get();

        $rekapBulanan = [];
        $totalSetorTahun = 0;
        $totalTarikTahun = 0;
        $totalNetFlowTahun = 0;
        $totalTrxSetorTahun = 0;
        $totalTrxTarikTahun = 0;
        $runningBalance = 0;

        for ($b = 1; $b <= 12; $b++) {
            $trxBulan = $transaksiTahun->filter(function ($t) use ($b) {
                return Carbon::parse($t->tanggal)->month === $b;
            });

            $setorBulan = $trxBulan->where('jenis_transaksi', 'Setor');
            $tarikBulan = $trxBulan->where('jenis_transaksi', 'Tarik');

            $jmlSetor = $setorBulan->count();
            $nominalSetor = (float) $setorBulan->sum('nominal_bersih');

            $jmlTarik = $tarikBulan->count();
            $nominalTarik = (float) $tarikBulan->sum('nominal_bersih');

            $netFlow = $nominalSetor - $nominalTarik;
            $runningBalance += $netFlow;

            $totalSetorTahun += $nominalSetor;
            $totalTarikTahun += $nominalTarik;
            $totalNetFlowTahun += $netFlow;
            $totalTrxSetorTahun += $jmlSetor;
            $totalTrxTarikTahun += $jmlTarik;

            $rekapBulanan[$b] = [
                'bulan_angka' => $b,
                'nama_bulan' => $namaBulanIndo[$b],
                'jumlah_setor' => $jmlSetor,
                'nominal_setor' => $nominalSetor,
                'jumlah_tarik' => $jmlTarik,
                'nominal_tarik' => $nominalTarik,
                'net_flow' => $netFlow,
                'running_balance' => $runningBalance,
            ];
        }

        // 3. JUMLAH PENARIKAN PER KATEGORI PENARIKAN
        $kategoriList = KategoriPenarikan::orderBy('urutan')->get();
        $rekapKategoriPenarikan = [];
        $penarikanQuery = TransaksiTabungan::where('jenis_transaksi', 'Tarik');
        if ($periodeId) {
            $penarikanQuery->whereHas('tabungan', function ($q) use ($periodeId) {
                $q->where('periode_tabungan_id', $periodeId);
            });
        }
        $semuaPenarikan = $penarikanQuery->get();
        $totalNominalSemuaTarik = (float) $semuaPenarikan->sum('nominal_bersih');

        foreach ($kategoriList as $kat) {
            $trxKat = $semuaPenarikan->where('kategori_penarikan_id', $kat->id);
            $totalNominalKat = (float) $trxKat->sum('nominal_bersih');
            $jmlTrxKat = $trxKat->count();
            $persentasePorsi = $totalNominalSemuaTarik > 0 ? round(($totalNominalKat / $totalNominalSemuaTarik) * 100, 1) : 0;

            $rekapKategoriPenarikan[] = [
                'id' => $kat->id,
                'nama_kategori' => $kat->nama_kategori,
                'kode_kategori' => $kat->kode_kategori,
                'jenis_tujuan' => $kat->jenis_tujuan,
                'jumlah_transaksi' => $jmlTrxKat,
                'total_nominal' => $totalNominalKat,
                'persentase' => $persentasePorsi,
                'warna_badge' => $kat->warna_badge ?? 'zinc',
            ];
        }

        // Transaksi Tarik Tanpa Kategori (jika ada data lama)
        $trxTanpaKat = $semuaPenarikan->whereNull('kategori_penarikan_id');
        if ($trxTanpaKat->count() > 0) {
            $totalNominalTanpaKat = (float) $trxTanpaKat->sum('nominal_bersih');
            $persentasePorsi = $totalNominalSemuaTarik > 0 ? round(($totalNominalTanpaKat / $totalNominalSemuaTarik) * 100, 1) : 0;
            $rekapKategoriPenarikan[] = [
                'id' => null,
                'nama_kategori' => 'Tarik Tunai Mandiri / Lainnya',
                'kode_kategori' => 'UMUM',
                'jenis_tujuan' => 'Tunai Mandiri',
                'jumlah_transaksi' => $trxTanpaKat->count(),
                'total_nominal' => $totalNominalTanpaKat,
                'persentase' => $persentasePorsi,
                'warna_badge' => 'amber',
            ];
        }

        // 4. DAFTAR PILIHAN FILTER (Tahun & Periode)
        $minTahunTrx = TransaksiTabungan::min('tanggal');
        $startYear = $minTahunTrx ? (int) Carbon::parse($minTahunTrx)->year : (int) date('Y');
        $endYear = (int) date('Y') + 1;
        $tahunList = range(max(2020, $startYear), max($endYear, $tahun));
        rsort($tahunList);

        $periodeList = PeriodeTabungan::orderBy('tanggal_mulai', 'desc')->get();
        $periodeTerpilih = $periodeId ? PeriodeTabungan::find($periodeId) : null;

        // 5. DENOMINASI PECAHAN UANG KAS FISIK BRANKAS
        $denominasiPecahan = [
            ['key' => '100000', 'nilai' => 100000, 'label' => 'Rp 100.000', 'tipe' => 'Kertas', 'badge_class' => 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/20'],
            ['key' => '50000', 'nilai' => 50000, 'label' => 'Rp 50.000', 'tipe' => 'Kertas', 'badge_class' => 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-500/20'],
            ['key' => '20000', 'nilai' => 20000, 'label' => 'Rp 20.000', 'tipe' => 'Kertas', 'badge_class' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20'],
            ['key' => '10000', 'nilai' => 10000, 'label' => 'Rp 10.000', 'tipe' => 'Kertas', 'badge_class' => 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-500/20'],
            ['key' => '5000', 'nilai' => 5000, 'label' => 'Rp 5.000', 'tipe' => 'Kertas', 'badge_class' => 'bg-amber-600/10 text-amber-700 dark:text-amber-400 border-amber-600/20'],
            ['key' => '2000', 'nilai' => 2000, 'label' => 'Rp 2.000', 'tipe' => 'Kertas', 'badge_class' => 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border-slate-500/20'],
            ['key' => '1000_kertas', 'nilai' => 1000, 'label' => 'Rp 1.000 (Kertas)', 'tipe' => 'Kertas', 'badge_class' => 'bg-cyan-600/10 text-cyan-700 dark:text-cyan-400 border-cyan-600/20'],
            ['key' => '1000_logam', 'nilai' => 1000, 'label' => 'Rp 1.000 (Logam)', 'tipe' => 'Logam', 'badge_class' => 'bg-yellow-600/10 text-yellow-700 dark:text-yellow-400 border-yellow-600/20'],
            ['key' => '500', 'nilai' => 500, 'label' => 'Rp 500 (Logam)', 'tipe' => 'Logam', 'badge_class' => 'bg-orange-500/10 text-orange-600 dark:text-orange-400 border-orange-500/20'],
            ['key' => '200', 'nilai' => 200, 'label' => 'Rp 200 (Logam)', 'tipe' => 'Logam', 'badge_class' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20'],
            ['key' => '100', 'nilai' => 100, 'label' => 'Rp 100 (Logam)', 'tipe' => 'Logam', 'badge_class' => 'bg-yellow-500/10 text-yellow-600 dark:text-yellow-400 border-yellow-500/20'],
        ];

        // 6. REKOMENDASI PECAHAN OTOMATIS DARI HAK BERSIH NASABAH (DENGAN PEMBULATAN KELIPATAN 100)
        $breakdownHakNasabah = $this->hitungBreakdownPecahan($totalHakBersihNasabah);
        $breakdownKasFisik = $this->hitungBreakdownPecahan($totalKasFisik);

        return [
            'tahun' => $tahun,
            'tahunList' => $tahunList,
            'periodeId' => $periodeId,
            'periodeList' => $periodeList,
            'periodeTerpilih' => $periodeTerpilih,
            // Kontrol Kas
            'totalKasFisik' => $totalKasFisik,
            'totalSetorSemua' => $totalSetorSemua,
            'totalTarikSemua' => $totalTarikSemua,
            'totalRekening' => $totalRekening,
            'totalRekeningAktif' => $totalRekeningAktif,
            'totalHakBersihNasabah' => $totalHakBersihNasabah,
            'totalPotonganMadrasah' => $totalPotonganMadrasah,
            'rekapPotongan' => $rekapPotongan,
            // Bulanan Masehi
            'rekapBulanan' => $rekapBulanan,
            'totalSetorTahun' => $totalSetorTahun,
            'totalTarikTahun' => $totalTarikTahun,
            'totalNetFlowTahun' => $totalNetFlowTahun,
            'totalTrxSetorTahun' => $totalTrxSetorTahun,
            'totalTrxTarikTahun' => $totalTrxTarikTahun,
            // Kategori Penarikan
            'rekapKategoriPenarikan' => $rekapKategoriPenarikan,
            'totalNominalSemuaTarik' => $totalNominalSemuaTarik,
            // Denominasi Pecahan & Rekomendasi
            'denominasiPecahan' => $denominasiPecahan,
            'breakdownHakNasabah' => $breakdownHakNasabah,
            'breakdownKasFisik' => $breakdownKasFisik,
        ];
    }

    /**
     * Hitung rekomendasi pecahan uang secara greedy dengan pembulatan kelipatan Rp 100
     */
    public function hitungBreakdownPecahan(float $nominal): array
    {
        $nominalAsli = $nominal;
        // Pembulatan ke atas ke kelipatan 100 terdekat karena tidak ada uang fisik pecahan di bawah Rp 100
        $nominalBulat = (float) ceil($nominal / 100) * 100;
        $selisihPembulatan = $nominalBulat - $nominalAsli;

        $pecahanRules = [
            '100000' => 100000,
            '50000' => 50000,
            '20000' => 20000,
            '10000' => 10000,
            '5000' => 5000,
            '2000' => 2000,
            '1000_kertas' => 1000,
            '1000_logam' => 1000,
            '500' => 500,
            '200' => 200,
            '100' => 100,
        ];

        $hasilCounts = [
            '100000' => 0,
            '50000' => 0,
            '20000' => 0,
            '10000' => 0,
            '5000' => 0,
            '2000' => 0,
            '1000_kertas' => 0,
            '1000_logam' => 0,
            '500' => 0,
            '200' => 0,
            '100' => 0,
        ];

        $sisa = $nominalBulat;
        $order = ['100000', '50000', '20000', '10000', '5000', '2000', '1000_kertas', '500', '200', '100'];

        foreach ($order as $key) {
            $val = $pecahanRules[$key];
            if ($sisa >= $val) {
                $lembar = (int) floor($sisa / $val);
                $hasilCounts[$key] = $lembar;
                $sisa -= ($lembar * $val);
            }
        }

        return [
            'nominal_asli' => $nominalAsli,
            'nominal_bulat' => $nominalBulat,
            'selisih_pembulatan' => $selisihPembulatan,
            'counts' => $hasilCounts,
        ];
    }
}
