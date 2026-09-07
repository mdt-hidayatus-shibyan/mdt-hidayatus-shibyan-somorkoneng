<?php

namespace App\Services\Tabungan;

use App\Models\Ruangan;
use App\Models\Tabungan\PengaturanPotonganTabungan;
use App\Models\Tabungan\PeriodeTabungan;
use App\Models\Tabungan\Tabungan;
use App\Models\Tabungan\TransaksiTabungan;
use App\Repositories\MuridRuanganRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PembagianTabunganService
{
    protected $muridRuanganRepo;

    public function __construct(MuridRuanganRepository $muridRuanganRepo)
    {
        $this->muridRuanganRepo = $muridRuanganRepo;
    }

    /**
     * Menghitung simulasi pembagian tabungan murid per kelas / periode
     */
    public function hitungSimulasiPembagian(int $periodeId, ?int $ruanganId = null)
    {
        $periode = PeriodeTabungan::with('tahunPelajaran')->findOrFail($periodeId);
        $persenPotongan = PengaturanPotonganTabungan::getPersentase('Murid');

        $query = Tabungan::with(['murid', 'ruangan'])
            ->where('periode_tabungan_id', $periodeId)
            ->where('jenis_nasabah', 'Murid')
            ->where('saldo', '>', 0);

        if ($ruanganId) {
            $query->where('ruangan_id', $ruanganId);
        }

        $daftarTabungan = $query->get();

        $rekapList = $daftarTabungan->map(function ($tab) use ($persenPotongan) {
            $saldoKotor = (float) $tab->saldo;
            $potongan = round(($saldoKotor * $persenPotongan) / 100, 2);
            $bersihDiterima = $saldoKotor - $potongan;

            return [
                'tabungan' => $tab,
                'murid' => $tab->murid,
                'ruangan' => $tab->ruangan,
                'tabungan_id' => $tab->id,
                'nomor_rekening' => $tab->nomor_rekening,
                'nama_rekening' => $tab->nama_rekening,
                'murid_id' => $tab->murid_id,
                'nama_murid' => $tab->nama_nasabah,
                'nism' => $tab->murid?->nism ?? '-',
                'ruangan_id' => $tab->ruangan_id,
                'nama_ruangan' => $tab->ruangan?->nama_ruangan ?? '-',
                'saldo_kotor' => $saldoKotor,
                'potongan' => $potongan,
                'saldo_bersih' => $bersihDiterima,
                'persentase_potongan' => $persenPotongan,
                'nominal_potongan' => $potongan,
                'nominal_bersih' => $bersihDiterima,
            ];
        });

        return [
            'periode' => $periode,
            'persentase_potongan' => $persenPotongan,
            'total_murid' => $rekapList->count(),
            'total_rekening' => $rekapList->count(),
            'total_saldo_kotor' => (float) $rekapList->sum('saldo_kotor'),
            'total_potongan' => (float) $rekapList->sum('nominal_potongan'),
            'total_bersih_dibagikan' => (float) $rekapList->sum('nominal_bersih'),
            'total_saldo_bersih' => (float) $rekapList->sum('nominal_bersih'),
            'daftar_rekap' => $rekapList,
            'rincian' => $rekapList,
        ];
    }

    /**
     * Eksekusi pembagian akhir massal tabungan murid per kelas / periode
     */
    public function eksekusiPembagianMassal(int $periodeId, ?int $ruanganId, int $petugasId, ?string $tanggal = null)
    {
        return DB::transaction(function () use ($periodeId, $ruanganId, $petugasId, $tanggal) {
            $simulasi = $this->hitungSimulasiPembagian($periodeId, $ruanganId);
            $persenPotongan = $simulasi['persentase_potongan'];
            $tglTransaksi = $tanggal ?? date('Y-m-d');
            $jumlahDiproses = 0;

            foreach ($simulasi['daftar_rekap'] as $item) {
                $tabungan = Tabungan::where('id', $item['tabungan_id'])->lockForUpdate()->first();

                if ($tabungan && $tabungan->saldo > 0) {
                    $saldoAwal = (float) $tabungan->saldo;
                    $potongan = round(($saldoAwal * $persenPotongan) / 100, 2);
                    $nominalBersih = $saldoAwal - $potongan;

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
                        'keterangan' => 'Pembagian Akhir Tabungan Murid Periode ' . $simulasi['periode']->nama_periode,
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
}
