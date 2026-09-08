<?php

namespace App\Services\Tabungan;

use App\Models\Ruangan;
use App\Models\Tabungan\PengaturanPotonganTabungan;
use App\Models\Tabungan\PeriodeTabungan;
use App\Models\Tabungan\Tabungan;
use App\Models\Tabungan\TransaksiTabungan;
use App\Repositories\MuridRuanganRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PembagianTabunganService
{
    protected $muridRuanganRepo;
    protected $tabunganService;

    public function __construct(MuridRuanganRepository $muridRuanganRepo, TabunganService $tabunganService)
    {
        $this->muridRuanganRepo = $muridRuanganRepo;
        $this->tabunganService = $tabunganService;
    }

    /**
     * Menghitung simulasi pembagian tabungan berdasarkan periode, jenis nasabah, dan ruangan
     */
    public function hitungSimulasiPembagian(int $periodeId, ?string $jenisNasabah = null, ?int $ruanganId = null, ?string $statusVerifikasi = null)
    {
        $periode = PeriodeTabungan::with('tahunPelajaran')->findOrFail($periodeId);

        $query = Tabungan::with(['murid.ruangans', 'murid.ruanganMasuk', 'ustadz', 'ruangan', 'periodeTabungan', 'diverifikasiOleh'])
            ->where(function ($q) use ($periodeId) {
                $q->where('periode_tabungan_id', $periodeId)
                    ->orWhereNull('periode_tabungan_id');
            })
            ->where('saldo', '>', 0);

        // Filter Jenis Nasabah (Murid, Ustadz, Kas Ruangan, Umum)
        if ($jenisNasabah && in_array($jenisNasabah, ['Murid', 'Ustadz', 'Kas Ruangan', 'Umum'])) {
            $query->where('jenis_nasabah', $jenisNasabah);
        }

        // Filter Ruangan (Hanya relevan jika jenis nasabah adalah Murid atau Semua)
        if ($ruanganId && (!$jenisNasabah || $jenisNasabah === 'Murid')) {
            $query->where(function ($q) use ($ruanganId) {
                $q->where('ruangan_id', $ruanganId)
                    ->orWhereHas('murid', function ($mq) use ($ruanganId) {
                        $mq->whereHas('ruangans', function ($rq) use ($ruanganId) {
                            $rq->where('ruangans.id', $ruanganId);
                        })->orWhere('ruangan_masuk', $ruanganId);
                    });
            });
        }

        // Filter Status Verifikasi
        if ($statusVerifikasi && in_array($statusVerifikasi, ['Belum Diverifikasi', 'Cocok', 'Selisih', 'Buku Tidak Ada'])) {
            $query->where('status_verifikasi', $statusVerifikasi);
        }

        $daftarTabungan = $query->orderBy('jenis_nasabah')->orderBy('saldo', 'desc')->get();

        $rekapList = $daftarTabungan->map(function ($tab) use ($periodeId) {
            $saldoKotor = (float) $tab->saldo;
            $persenPotongan = PengaturanPotonganTabungan::getPersentase($tab->jenis_nasabah, $tab->periode_tabungan_id ?? $periodeId);

            // Hitung potongan dengan pembulatan ke atas ke kelipatan Rp 100 terdekat
            $rawPotongan = ($saldoKotor * $persenPotongan) / 100;
            $potongan = $rawPotongan > 0 ? (float) (ceil($rawPotongan / 100) * 100) : 0.00;
            $bersihDiterima = max(0, $saldoKotor - $potongan);

            // Selisih verifikasi jika ada saldo buku fisik
            $saldoBuku = $tab->saldo_buku_fisik !== null ? (float) $tab->saldo_buku_fisik : null;
            $selisihVerifikasi = $saldoBuku !== null ? ($saldoBuku - $saldoKotor) : null;

            return [
                'tabungan' => $tab,
                'tabungan_id' => $tab->id,
                'nomor_rekening' => $tab->nomor_rekening,
                'nama_rekening' => $tab->nama_rekening,
                'jenis_nasabah' => $tab->jenis_nasabah,
                'nama_nasabah' => $tab->nama_nasabah,
                'identitas_nasabah' => $tab->identitas_nasabah,
                'murid' => $tab->murid,
                'ustadz' => $tab->ustadz,
                'ruangan' => $tab->ruangan,
                'ruangan_id' => $tab->ruangan_id ?? ($tab->murid?->ruangans?->first()?->id ?? null),
                'nama_ruangan' => $tab->ruangan?->nama_ruangan ?? ($tab->murid?->ruangans?->first()?->nama_ruangan ?? '-'),
                'saldo_kotor' => $saldoKotor,
                'persentase_potongan' => $persenPotongan,
                'nominal_potongan' => $potongan,
                'saldo_bersih' => $bersihDiterima,
                'nominal_bersih' => $bersihDiterima,
                // Kolom Verifikasi
                'buku_tabungan_ada' => $tab->buku_tabungan_ada ?? true,
                'status_verifikasi' => $tab->status_verifikasi ?? 'Belum Diverifikasi',
                'saldo_buku_fisik' => $saldoBuku,
                'selisih_verifikasi' => $selisihVerifikasi,
                'catatan_verifikasi' => $tab->catatan_verifikasi,
                'diverifikasi_oleh' => $tab->diverifikasiOleh?->name,
                'diverifikasi_pada' => $tab->diverifikasi_pada ? $tab->diverifikasi_pada->format('d/m/Y H:i') : null,
            ];
        });

        $totalRekening = $rekapList->count();
        $totalDiverifikasi = $rekapList->where('status_verifikasi', 'Cocok')->count();
        $totalBelumDiverifikasi = $rekapList->where('status_verifikasi', 'Belum Diverifikasi')->count();
        $totalSelisih = $rekapList->where('status_verifikasi', 'Selisih')->count();
        $totalBukuTidakAda = $rekapList->where('status_verifikasi', 'Buku Tidak Ada')->count();

        return [
            'periode' => $periode,
            'jenis_nasabah' => $jenisNasabah,
            'ruangan_id' => $ruanganId,
            'status_verifikasi_filter' => $statusVerifikasi,
            'total_rekening' => $totalRekening,
            'total_diverifikasi' => $totalDiverifikasi,
            'total_belum_diverifikasi' => $totalBelumDiverifikasi,
            'total_selisih' => $totalSelisih,
            'total_buku_tidak_ada' => $totalBukuTidakAda,
            'total_saldo_kotor' => (float) $rekapList->sum('saldo_kotor'),
            'total_potongan' => (float) $rekapList->sum('nominal_potongan'),
            'total_saldo_bersih' => (float) $rekapList->sum('nominal_bersih'),
            'total_bersih_dibagikan' => (float) $rekapList->sum('nominal_bersih'),
            'daftar_rekap' => $rekapList,
            'rincian' => $rekapList,
        ];
    }

    /**
     * Verifikasi saldo buku tabungan fisik secara individual
     */
    public function verifikasiRekening(int $tabunganId, string $statusVerifikasi, ?float $saldoBukuFisik = null, ?string $catatan = null, ?int $petugasId = null, bool $bukuAda = true)
    {
        return DB::transaction(function () use ($tabunganId, $statusVerifikasi, $saldoBukuFisik, $catatan, $petugasId, $bukuAda) {
            $tabungan = Tabungan::where('id', $tabunganId)->lockForUpdate()->firstOrFail();

            if (!$bukuAda || $statusVerifikasi === 'Buku Tidak Ada') {
                $statusVerifikasi = 'Buku Tidak Ada';
                $saldoBukuFisik = null;
                $bukuAda = false;
                $catatan = $catatan ?: 'Buku tabungan tidak ada / hilang (dicetak lembar mutasi A6)';
            } else {
                if (!in_array($statusVerifikasi, ['Belum Diverifikasi', 'Cocok', 'Selisih'])) {
                    throw new \InvalidArgumentException('Status verifikasi tidak valid.');
                }

                // Jika status cocok dan saldo buku fisik tidak diinput, set sama dengan saldo sistem
                if ($statusVerifikasi === 'Cocok' && $saldoBukuFisik === null) {
                    $saldoBukuFisik = (float) $tabungan->saldo;
                }
                $bukuAda = true;
            }

            $tabungan->update([
                'buku_tabungan_ada' => $bukuAda,
                'status_verifikasi' => $statusVerifikasi,
                'saldo_buku_fisik' => $saldoBukuFisik,
                'catatan_verifikasi' => $catatan,
                'diverifikasi_oleh' => $petugasId,
                'diverifikasi_pada' => now(),
            ]);

            return $tabungan;
        });
    }

    /**
     * Verifikasi massal: menandai seluruh rekening yang tersaring sebagai 'Cocok'
     */
    public function verifikasiSemuaCocok(int $periodeId, ?string $jenisNasabah = null, ?int $ruanganId = null, ?int $petugasId = null)
    {
        return DB::transaction(function () use ($periodeId, $jenisNasabah, $ruanganId, $petugasId) {
            $simulasi = $this->hitungSimulasiPembagian($periodeId, $jenisNasabah, $ruanganId);
            $tabunganIds = collect($simulasi['daftar_rekap'])->pluck('tabungan_id')->all();

            $updated = Tabungan::whereIn('id', $tabunganIds)
                ->update([
                    'status_verifikasi' => 'Cocok',
                    'saldo_buku_fisik' => DB::raw('saldo'),
                    'diverifikasi_oleh' => $petugasId,
                    'diverifikasi_pada' => now(),
                ]);

            return $updated;
        });
    }

    /**
     * Eksekusi pembagian akhir massal tabungan per kategori / ruangan / periode
     */
    public function eksekusiPembagianMassal(int $periodeId, ?string $jenisNasabah, ?int $ruanganId, int $petugasId, ?string $tanggal = null)
    {
        return DB::transaction(function () use ($periodeId, $jenisNasabah, $ruanganId, $petugasId, $tanggal) {
            $simulasi = $this->hitungSimulasiPembagian($periodeId, $jenisNasabah, $ruanganId);
            $tglTransaksi = $tanggal ?? date('Y-m-d');
            $jumlahDiproses = 0;

            foreach ($simulasi['daftar_rekap'] as $item) {
                $tabungan = Tabungan::where('id', $item['tabungan_id'])->lockForUpdate()->first();

                if ($tabungan && $tabungan->saldo > 0) {
                    $saldoAwal = (float) $tabungan->saldo;
                    $persenPotongan = $item['persentase_potongan'];
                    $potongan = $item['nominal_potongan'];
                    $nominalBersih = $item['nominal_bersih'];

                    $kodeTrx = 'TRX-BAGI-' . date('Ymd') . '-' . strtoupper(Str::random(5));

                    TransaksiTabungan::create([
                        'kode_transaksi' => $kodeTrx,
                        'tabungan_id' => $tabungan->id,
                        'jenis_transaksi' => 'Pembagian_Akhir',
                        'nominal_kotor' => $saldoAwal,
                        'persentase_potongan' => $persenPotongan,
                        'nominal_potongan' => $potongan,
                        'nominal_bersih' => $nominalBersih,
                        'saldo_awal' => $saldoAwal,
                        'saldo_akhir' => 0.00,
                        'tanggal' => $tglTransaksi,
                        'ruangan_id' => $tabungan->ruangan_id,
                        'petugas_id' => $petugasId,
                        'metode' => 'Tunai',
                        'keterangan' => 'Pembagian Akhir Tabungan ' . $tabungan->jenis_nasabah . ' Periode ' . $simulasi['periode']->nama_periode,
                    ]);

                    $tabungan->update([
                        'saldo' => 0.00,
                        'total_tarik' => (float) $tabungan->total_tarik + $saldoAwal,
                        'total_potongan' => (float) $tabungan->total_potongan + $potongan,
                        'status' => 'Dibagikan',
                    ]);

                    $jumlahDiproses++;
                }
            }

            return $jumlahDiproses;
        });
    }

    /**
     * Menghitung rincian kebutuhan uang pecahan kas fisik per murid / rekening dan rekapitulasi global
     */
    public function hitungRekapPecahanUang(
        ?int $periodeId = null,
        ?string $jenisNasabah = null,
        ?int $levelId = null,
        ?int $ruanganId = null,
        ?string $statusVerifikasi = null,
        ?string $q = null
    ) {
        $periode = $periodeId
            ? PeriodeTabungan::find($periodeId)
            : (PeriodeTabungan::where('is_active', true)->first() ?? PeriodeTabungan::first());

        $query = Tabungan::with(['murid.ruangans.level.tingkat', 'ustadz', 'ruangan.level.tingkat', 'periodeTabungan', 'diverifikasiOleh'])
            ->where('saldo', '>', 0);

        if ($periode) {
            $query->where(function ($sq) use ($periode) {
                $sq->where('periode_tabungan_id', $periode->id)
                    ->orWhereNull('periode_tabungan_id');
            });
        }

        // Filter Jenis Nasabah
        if ($jenisNasabah && $jenisNasabah !== 'Semua' && in_array($jenisNasabah, ['Murid', 'Ustadz', 'Kas Ruangan', 'Umum'])) {
            $query->where('jenis_nasabah', $jenisNasabah);
        }

        // Filter Level / Kelas
        if ($levelId) {
            $query->where(function ($sq) use ($levelId) {
                $sq->whereHas('ruangan', function ($rq) use ($levelId) {
                    $rq->where('level_id', $levelId);
                })->orWhereHas('murid', function ($mq) use ($levelId) {
                    $mq->whereHas('ruangans', function ($rq) use ($levelId) {
                        $rq->where('level_id', $levelId);
                    });
                });
            });
        }

        // Filter Ruangan Spesifik
        if ($ruanganId) {
            $query->where(function ($sq) use ($ruanganId) {
                $sq->where('ruangan_id', $ruanganId)
                    ->orWhereHas('murid', function ($mq) use ($ruanganId) {
                        $mq->whereHas('ruangans', function ($rq) use ($ruanganId) {
                            $rq->where('ruangans.id', $ruanganId);
                        })->orWhere('ruangan_masuk', $ruanganId);
                    });
            });
        }

        // Filter Status Verifikasi
        if ($statusVerifikasi && in_array($statusVerifikasi, ['Belum Diverifikasi', 'Cocok', 'Selisih', 'Buku Tidak Ada'])) {
            $query->where('status_verifikasi', $statusVerifikasi);
        }

        // Filter Search Keyword
        if ($q) {
            $query->where(function ($sq) use ($q) {
                $sq->where('nomor_rekening', 'like', "%{$q}%")
                    ->orWhereHas('murid', function ($mq) use ($q) {
                        $mq->where('nama_lengkap', 'like', "%{$q}%")
                            ->orWhere('nism', 'like', "%{$q}%");
                    })
                    ->orWhereHas('ustadz', function ($uq) use ($q) {
                        $uq->where('nama_lengkap', 'like', "%{$q}%")
                            ->orWhere('nigm', 'like', "%{$q}%");
                    })
                    ->orWhere('nama_nasabah_umum', 'like', "%{$q}%");
            });
        }

        $daftarTabungan = $query->orderBy('jenis_nasabah')->orderBy('nomor_rekening')->get();

        $denominasiList = [
            '100000' => ['label' => 'Rp 100.000', 'nilai' => 100000, 'tipe' => 'Lembar'],
            '50000'  => ['label' => 'Rp 50.000',  'nilai' => 50000,  'tipe' => 'Lembar'],
            '20000'  => ['label' => 'Rp 20.000',  'nilai' => 20000,  'tipe' => 'Lembar'],
            '10000'  => ['label' => 'Rp 10.000',  'nilai' => 10000,  'tipe' => 'Lembar'],
            '5000'   => ['label' => 'Rp 5.000',   'nilai' => 5000,   'tipe' => 'Lembar'],
            '2000'   => ['label' => 'Rp 2.000',   'nilai' => 2000,   'tipe' => 'Lembar'],
            '1000'   => ['label' => 'Rp 1.000',   'nilai' => 1000,   'tipe' => 'Lembar'],
            '500'    => ['label' => 'Rp 500',     'nilai' => 500,    'tipe' => 'Koin'],
            '200'    => ['label' => 'Rp 200',     'nilai' => 200,    'tipe' => 'Koin'],
            '100'    => ['label' => 'Rp 100',     'nilai' => 100,    'tipe' => 'Koin'],
        ];

        $totalPecahanGlobal = [];
        foreach ($denominasiList as $denomKey => $denomInfo) {
            $totalPecahanGlobal[$denomKey] = [
                'label' => $denomInfo['label'],
                'nilai' => $denomInfo['nilai'],
                'tipe' => $denomInfo['tipe'],
                'count' => 0,
                'total_nominal' => 0.00,
            ];
        }

        $rekapPerNasabah = $daftarTabungan->map(function ($tab) use ($periode, $denominasiList, &$totalPecahanGlobal) {
            $saldoKotor = (float) $tab->saldo;
            $persenPotongan = PengaturanPotonganTabungan::getPersentase($tab->jenis_nasabah, $tab->periode_tabungan_id ?? $periode?->id);

            $rawPotongan = ($saldoKotor * $persenPotongan) / 100;
            $potongan = $rawPotongan > 0 ? (float) (ceil($rawPotongan / 100) * 100) : 0.00;
            $hakBersih = max(0, $saldoKotor - $potongan);

            // Hitung denominasi pecahan secara greedy
            $pecahanAkun = [];
            $sisa = $hakBersih;
            foreach ($denominasiList as $denomKey => $denomInfo) {
                $lembar = 0;
                $val = $denomInfo['nilai'];
                if ($sisa >= $val) {
                    $lembar = (int) floor($sisa / $val);
                    $sisa -= ($lembar * $val);
                }
                $pecahanAkun[$denomKey] = $lembar;
                $totalPecahanGlobal[$denomKey]['count'] += $lembar;
                $totalPecahanGlobal[$denomKey]['total_nominal'] += ($lembar * $val);
            }

            // Dapatkan info nama ruangan & level
            $ruanganObj = $tab->ruangan ?? $tab->murid?->ruangans?->first();
            $levelObj = $ruanganObj?->level;

            return [
                'tabungan' => $tab,
                'tabungan_id' => $tab->id,
                'nomor_rekening' => $tab->nomor_rekening,
                'nama_nasabah' => $tab->nama_nasabah,
                'jenis_nasabah' => $tab->jenis_nasabah,
                'identitas_nasabah' => $tab->identitas_nasabah,
                'nama_ruangan' => $ruanganObj?->nama_ruangan ?? ($tab->jenis_nasabah),
                'nama_level' => $levelObj?->nama_level ?? '-',
                'saldo_kotor' => $saldoKotor,
                'persentase_potongan' => $persenPotongan,
                'nominal_potongan' => $potongan,
                'hak_bersih' => $hakBersih,
                'pecahan' => $pecahanAkun,
                'buku_tabungan_ada' => $tab->buku_tabungan_ada ?? true,
                'status_verifikasi' => $tab->status_verifikasi ?? 'Belum Diverifikasi',
            ];
        });

        $totalSaldoKotor = (float) $rekapPerNasabah->sum('saldo_kotor');
        $totalPotongan = (float) $rekapPerNasabah->sum('nominal_potongan');
        $totalHakBersih = (float) $rekapPerNasabah->sum('hak_bersih');
        $totalRekening = $rekapPerNasabah->count();

        return [
            'periode' => $periode,
            'jenis_nasabah' => $jenisNasabah,
            'level_id' => $levelId,
            'ruangan_id' => $ruanganId,
            'status_verifikasi' => $statusVerifikasi,
            'q' => $q,
            'denominasi_list' => $denominasiList,
            'rekap_per_nasabah' => $rekapPerNasabah,
            'total_pecahan_global' => $totalPecahanGlobal,
            'total_rekening' => $totalRekening,
            'total_saldo_kotor' => $totalSaldoKotor,
            'total_potongan' => $totalPotongan,
            'total_hak_bersih' => $totalHakBersih,
        ];
    }
}
