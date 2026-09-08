<?php

namespace Database\Seeders;

use App\Models\Tabungan\PeriodeTabungan;
use App\Models\Tabungan\Tabungan;
use App\Models\Tabungan\TransaksiTabungan;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DummySetorTabunganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // Ambil semua user/petugas yang ada untuk variasi petugas pencatat
            $petugasList = User::pluck('id')->toArray();
            if (empty($petugasList)) {
                $petugasList = [null];
            }

            // Ambil semua rekening tabungan
            $tabungans = Tabungan::with(['periodeTabungan', 'murid.ruangans', 'ruangan'])->get();

            if ($tabungans->isEmpty()) {
                $this->command->warn('Tidak ada data rekening tabungan yang ditemukan untuk di-seed.');
                return;
            }

            // Bersihkan transaksi setor dummy lama agar saldo terkalkulasi rapi dari awal
            TransaksiTabungan::whereIn('tabungan_id', $tabungans->pluck('id'))->delete();

            $totalTransaksiDibuat = 0;
            $grandTotalSetoran = 0;

            foreach ($tabungans as $tabungan) {
                // Tentukan rentang tanggal setor berdasarkan periode tabungan
                $periode = $tabungan->periodeTabungan ?? PeriodeTabungan::where('is_active', true)->first();

                $startDate = $periode && $periode->tanggal_mulai
                    ? Carbon::parse($periode->tanggal_mulai)
                    : Carbon::parse('2026-09-07');

                $endDate = $periode && $periode->tanggal_penutupan
                    ? Carbon::parse($periode->tanggal_penutupan)
                    : Carbon::parse('2027-01-07');

                // Jika tanggal penutupan lebih kecil dari tanggal mulai, sesuaikan
                if ($endDate->lessThan($startDate)) {
                    $endDate = $startDate->copy()->addMonths(4);
                }

                $runningSaldo = 0.00;
                $runningTotalSetor = 0.00;
                $ruanganId = $tabungan->ruangan_id ?? ($tabungan->murid?->ruangans?->first()?->id ?? null);

                // Buat periode harian
                $period = CarbonPeriod::create($startDate, $endDate);

                // Pola setoran sesuai jenis nasabah
                switch ($tabungan->jenis_nasabah) {
                    case 'Murid':
                        $nominalChoices = [2000, 3000, 5000, 5000, 10000, 10000, 15000, 20000, 25000, 50000];
                        $keteranganChoices = [
                            'Setoran Harian Murid',
                            'Nabung Uang Saku',
                            'Setoran Tabungan Rutin',
                            'Setoran Mingguan',
                            'Tabungan Madrasah',
                            'Nabung Saku Madrasah'
                        ];
                        // Murid menabung 2-4 kali per minggu (selain hari libur/Jumat)
                        foreach ($period as $date) {
                            if ($date->isFriday()) {
                                continue;
                            }

                            // Peluang menabung 45% setiap hari aktif
                            if (mt_rand(1, 100) <= 45) {
                                $nominal = $nominalChoices[array_rand($nominalChoices)];
                                $keterangan = $keteranganChoices[array_rand($keteranganChoices)];
                                $petugasId = $petugasList[array_rand($petugasList)];

                                $saldoAwal = $runningSaldo;
                                $saldoAkhir = $saldoAwal + $nominal;
                                $runningSaldo = $saldoAkhir;
                                $runningTotalSetor += $nominal;

                                $kodeTransaksi = 'TRX-IN-' . $date->format('Ymd') . '-' . strtoupper(Str::random(5));

                                TransaksiTabungan::create([
                                    'kode_transaksi' => $kodeTransaksi,
                                    'tabungan_id' => $tabungan->id,
                                    'jenis_transaksi' => 'Setor',
                                    'nominal_kotor' => $nominal,
                                    'persentase_potongan' => 0.00,
                                    'nominal_potongan' => 0.00,
                                    'nominal_bersih' => $nominal,
                                    'saldo_awal' => $saldoAwal,
                                    'saldo_akhir' => $saldoAkhir,
                                    'tanggal' => $date->format('Y-m-d'),
                                    'ruangan_id' => $ruanganId,
                                    'petugas_id' => $petugasId,
                                    'metode' => 'Tunai',
                                    'keterangan' => $keterangan,
                                    'created_at' => $date->copy()->setTime(mt_rand(7, 16), mt_rand(0, 59), mt_rand(0, 59)),
                                    'updated_at' => $date->copy()->setTime(mt_rand(7, 16), mt_rand(0, 59), mt_rand(0, 59)),
                                ]);

                                $totalTransaksiDibuat++;
                                $grandTotalSetoran += $nominal;
                            }
                        }
                        break;

                    case 'Ustadz':
                        $nominalChoices = [50000, 100000, 100000, 150000, 200000, 250000, 500000];
                        $keteranganChoices = [
                            'Setoran Tabungan Ustadz',
                            'Nabung Honor Bulanan',
                            'Infaq & Tabungan Ustadz',
                            'Setoran Mingguan Ustadz',
                            'Tabungan Rutin Pengajar'
                        ];
                        // Ustadz menabung 1-2 kali seminggu
                        foreach ($period as $date) {
                            if (($date->isSaturday() || $date->isSunday()) && mt_rand(1, 100) <= 60) {
                                $nominal = $nominalChoices[array_rand($nominalChoices)];
                                $keterangan = $keteranganChoices[array_rand($keteranganChoices)];
                                $petugasId = $petugasList[array_rand($petugasList)];

                                $saldoAwal = $runningSaldo;
                                $saldoAkhir = $saldoAwal + $nominal;
                                $runningSaldo = $saldoAkhir;
                                $runningTotalSetor += $nominal;

                                $kodeTransaksi = 'TRX-IN-' . $date->format('Ymd') . '-' . strtoupper(Str::random(5));

                                TransaksiTabungan::create([
                                    'kode_transaksi' => $kodeTransaksi,
                                    'tabungan_id' => $tabungan->id,
                                    'jenis_transaksi' => 'Setor',
                                    'nominal_kotor' => $nominal,
                                    'persentase_potongan' => 0.00,
                                    'nominal_potongan' => 0.00,
                                    'nominal_bersih' => $nominal,
                                    'saldo_awal' => $saldoAwal,
                                    'saldo_akhir' => $saldoAkhir,
                                    'tanggal' => $date->format('Y-m-d'),
                                    'ruangan_id' => $ruanganId,
                                    'petugas_id' => $petugasId,
                                    'metode' => 'Tunai',
                                    'keterangan' => $keterangan,
                                    'created_at' => $date->copy()->setTime(mt_rand(8, 17), mt_rand(0, 59), mt_rand(0, 59)),
                                    'updated_at' => $date->copy()->setTime(mt_rand(8, 17), mt_rand(0, 59), mt_rand(0, 59)),
                                ]);

                                $totalTransaksiDibuat++;
                                $grandTotalSetoran += $nominal;
                            }
                        }
                        break;

                    case 'Kas Ruangan':
                        $nominalChoices = [20000, 25000, 30000, 50000, 75000, 100000];
                        $keteranganChoices = [
                            'Setoran Kas Mingguan Kelas',
                            'Infaq Kas Ruangan',
                            'Tabungan Kas Kelas',
                            'Setoran Kas Operasional Kelas'
                        ];
                        // Kas Ruangan disetor setiap hari Senin atau Sabtu
                        foreach ($period as $date) {
                            if ($date->isMonday() || $date->isSaturday()) {
                                if (mt_rand(1, 100) <= 75) {
                                    $nominal = $nominalChoices[array_rand($nominalChoices)];
                                    $keterangan = $keteranganChoices[array_rand($keteranganChoices)];
                                    $petugasId = $petugasList[array_rand($petugasList)];

                                    $saldoAwal = $runningSaldo;
                                    $saldoAkhir = $saldoAwal + $nominal;
                                    $runningSaldo = $saldoAkhir;
                                    $runningTotalSetor += $nominal;

                                    $kodeTransaksi = 'TRX-IN-' . $date->format('Ymd') . '-' . strtoupper(Str::random(5));

                                    TransaksiTabungan::create([
                                        'kode_transaksi' => $kodeTransaksi,
                                        'tabungan_id' => $tabungan->id,
                                        'jenis_transaksi' => 'Setor',
                                        'nominal_kotor' => $nominal,
                                        'persentase_potongan' => 0.00,
                                        'nominal_potongan' => 0.00,
                                        'nominal_bersih' => $nominal,
                                        'saldo_awal' => $saldoAwal,
                                        'saldo_akhir' => $saldoAkhir,
                                        'tanggal' => $date->format('Y-m-d'),
                                        'ruangan_id' => $ruanganId,
                                        'petugas_id' => $petugasId,
                                        'metode' => 'Tunai',
                                        'keterangan' => $keterangan,
                                        'created_at' => $date->copy()->setTime(mt_rand(8, 15), mt_rand(0, 59), mt_rand(0, 59)),
                                        'updated_at' => $date->copy()->setTime(mt_rand(8, 15), mt_rand(0, 59), mt_rand(0, 59)),
                                    ]);

                                    $totalTransaksiDibuat++;
                                    $grandTotalSetoran += $nominal;
                                }
                            }
                        }
                        break;

                    case 'Umum':
                        $nominalChoices = [50000, 100000, 150000, 200000, 250000, 300000, 500000];
                        $keteranganChoices = [
                            'Setoran Tabungan Umum',
                            'Nabung Titipan Warga/Umum',
                            'Setoran Rutin Tabungan Umum',
                            'Tabungan Mandiri Umum'
                        ];
                        // Umum menabung tiap 1-2 minggu
                        foreach ($period as $date) {
                            if ($date->isSunday() && mt_rand(1, 100) <= 70) {
                                $nominal = $nominalChoices[array_rand($nominalChoices)];
                                $keterangan = $keteranganChoices[array_rand($keteranganChoices)];
                                $petugasId = $petugasList[array_rand($petugasList)];

                                $saldoAwal = $runningSaldo;
                                $saldoAkhir = $saldoAwal + $nominal;
                                $runningSaldo = $saldoAkhir;
                                $runningTotalSetor += $nominal;

                                $kodeTransaksi = 'TRX-IN-' . $date->format('Ymd') . '-' . strtoupper(Str::random(5));

                                TransaksiTabungan::create([
                                    'kode_transaksi' => $kodeTransaksi,
                                    'tabungan_id' => $tabungan->id,
                                    'jenis_transaksi' => 'Setor',
                                    'nominal_kotor' => $nominal,
                                    'persentase_potongan' => 0.00,
                                    'nominal_potongan' => 0.00,
                                    'nominal_bersih' => $nominal,
                                    'saldo_awal' => $saldoAwal,
                                    'saldo_akhir' => $saldoAkhir,
                                    'tanggal' => $date->format('Y-m-d'),
                                    'ruangan_id' => $ruanganId,
                                    'petugas_id' => $petugasId,
                                    'metode' => 'Tunai',
                                    'keterangan' => $keterangan,
                                    'created_at' => $date->copy()->setTime(mt_rand(9, 17), mt_rand(0, 59), mt_rand(0, 59)),
                                    'updated_at' => $date->copy()->setTime(mt_rand(9, 17), mt_rand(0, 59), mt_rand(0, 59)),
                                ]);

                                $totalTransaksiDibuat++;
                                $grandTotalSetoran += $nominal;
                            }
                        }
                        break;
                }

                // Perbarui total saldo dan total setor pada rekening
                $tabungan->update([
                    'saldo' => $runningSaldo,
                    'total_setor' => $runningTotalSetor,
                    'total_tarik' => 0.00,
                    'total_potongan' => 0.00,
                ]);
            }
        });
    }
}
