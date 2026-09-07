<?php

namespace App\Services\Tabungan;

use App\Models\Murid;
use App\Models\Ruangan;
use App\Models\Tabungan\PengaturanPotonganTabungan;
use App\Models\Tabungan\PeriodeTabungan;
use App\Models\Tabungan\Tabungan;
use App\Models\Tabungan\TransaksiTabungan;
use App\Models\Ustadz;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TabunganService
{
    /**
     * Membuka rekening tabungan baru untuk nasabah
     */
    public function bukaRekening(array $data, ?int $petugasId = null)
    {
        return DB::transaction(function () use ($data, $petugasId) {
            $jenis = $data['jenis_nasabah'];
            $nasabah = null;

            if ($jenis === 'Murid') {
                $nasabah = Murid::findOrFail($data['murid_id']);
                $urutanKe = Tabungan::where('murid_id', $nasabah->id)->count() + 1;
            } elseif ($jenis === 'Ustadz') {
                $nasabah = Ustadz::findOrFail($data['ustadz_id']);
                $urutanKe = Tabungan::where('ustadz_id', $nasabah->id)->count() + 1;
            } elseif ($jenis === 'Kas Ruangan') {
                $nasabah = Ruangan::findOrFail($data['ruangan_id']);
                $urutanKe = Tabungan::where('ruangan_id', $nasabah->id)->count() + 1;
            } else {
                $urutanKe = 1;
            }

            // Gunakan barcode nomor rekening yang diinput/discan admin, atau generate jika kosong
            if (!empty($data['nomor_rekening'])) {
                $nomorRekening = trim($data['nomor_rekening']);
                if (Tabungan::where('nomor_rekening', $nomorRekening)->exists()) {
                    throw new \Exception("Nomor rekening / barcode '{$nomorRekening}' sudah terdaftar dalam sistem.");
                }
            } else {
                $nomorRekening = Tabungan::generateNomorRekening($jenis, $nasabah, $urutanKe);
                $counter = 1;
                $baseNomor = $nomorRekening;
                while (Tabungan::where('nomor_rekening', $nomorRekening)->exists()) {
                    $counter++;
                    $nomorRekening = $baseNomor . '-' . $counter;
                }
            }

            return Tabungan::create([
                'nomor_rekening' => $nomorRekening,
                'nama_rekening' => $data['nama_rekening'] ?? 'Tabungan Utama',
                'jenis_nasabah' => $jenis,
                'murid_id' => $data['murid_id'] ?? null,
                'ustadz_id' => $data['ustadz_id'] ?? null,
                'ruangan_id' => $data['ruangan_id'] ?? null,
                'nama_nasabah_umum' => $data['nama_nasabah_umum'] ?? null,
                'kontak_umum' => $data['kontak_umum'] ?? null,
                'alamat_umum' => $data['alamat_umum'] ?? null,
                'periode_tabungan_id' => $data['periode_tabungan_id'] ?? null,
                'saldo' => 0.00,
                'total_setor' => 0.00,
                'total_tarik' => 0.00,
                'total_potongan' => 0.00,
                'status' => 'Aktif',
                'catatan' => $data['catatan'] ?? null,
                'dibuat_oleh' => $petugasId,
            ]);
        });
    }

    /**
     * Hitung kalkulasi potongan madrasah dan nominal bersih penarikan
     */
    public function hitungPotongan(Tabungan $tabungan, float $nominalKotor): array
    {
        $persen = PengaturanPotonganTabungan::getPersentase($tabungan->jenis_nasabah, $tabungan->periode_tabungan_id);
        $nominalPotongan = round(($nominalKotor * $persen) / 100, 2);
        $nominalBersih = $nominalKotor - $nominalPotongan;

        return [
            'persentase_potongan' => $persen,
            'nominal_potongan' => $nominalPotongan,
            'nominal_bersih' => $nominalBersih,
            'persentase' => $persen,
            'potongan' => $nominalPotongan,
        ];
    }

    /**
     * Eksekusi transaksi setor tunai ke rekening tabungan
     */
    public function setorTunai(int $tabunganId, float $nominal, ?int $petugasId = null, ?string $tanggal = null, ?string $keterangan = null, string $metode = 'Tunai', ?int $ruanganId = null)
    {
        if ($nominal <= 0) {
            throw new \InvalidArgumentException('Nominal setoran harus lebih besar dari 0.');
        }

        return DB::transaction(function () use ($tabunganId, $nominal, $petugasId, $tanggal, $keterangan, $metode, $ruanganId) {
            $tabungan = Tabungan::where('id', $tabunganId)->lockForUpdate()->firstOrFail();

            if ($tabungan->status !== 'Aktif') {
                throw new \Exception("Rekening {$tabungan->nomor_rekening} berstatus {$tabungan->status}, tidak dapat menerima setoran.");
            }

            $saldoAwal = (float) $tabungan->saldo;
            $saldoAkhir = $saldoAwal + $nominal;

            $kodeTransaksi = 'TRX-IN-' . date('Ymd') . '-' . strtoupper(Str::random(5));

            $trx = TransaksiTabungan::create([
                'kode_transaksi' => $kodeTransaksi,
                'tabungan_id' => $tabungan->id,
                'jenis_transaksi' => 'Setor',
                'nominal_kotor' => $nominal,
                'persentase_potongan' => 0.00,
                'nominal_potongan' => 0.00,
                'nominal_bersih' => $nominal,
                'saldo_awal' => $saldoAwal,
                'saldo_akhir' => $saldoAkhir,
                'tanggal' => $tanggal ?? date('Y-m-d'),
                'ruangan_id' => $ruanganId ?? $tabungan->ruangan_id,
                'petugas_id' => $petugasId,
                'metode' => $metode,
                'keterangan' => $keterangan ?? 'Setoran Tabungan Tunai',
            ]);

            $tabungan->update([
                'saldo' => $saldoAkhir,
                'total_setor' => (float) $tabungan->total_setor + $nominal,
            ]);

            return $trx;
        });
    }

    /**
     * Eksekusi penarikan tunai langsung oleh Admin/Bendahara
     */
    public function tarikTunai(int $tabunganId, float $nominalKotor, ?int $petugasId = null, ?string $tanggal = null, ?string $keterangan = null, string $metode = 'Tunai', ?int $pengajuanId = null)
    {
        if ($nominalKotor <= 0) {
            throw new \InvalidArgumentException('Nominal penarikan harus lebih besar dari 0.');
        }

        return DB::transaction(function () use ($tabunganId, $nominalKotor, $petugasId, $tanggal, $keterangan, $metode, $pengajuanId) {
            $tabungan = Tabungan::where('id', $tabunganId)->lockForUpdate()->firstOrFail();

            if ($tabungan->saldo < $nominalKotor) {
                throw new \Exception("Saldo tidak mencukupi. Saldo saat ini: Rp " . number_format($tabungan->saldo, 0, ',', '.'));
            }

            $kalkulasi = $this->hitungPotongan($tabungan, $nominalKotor);

            $saldoAwal = (float) $tabungan->saldo;
            $saldoAkhir = $saldoAwal - $nominalKotor;

            $kodeTransaksi = 'TRX-OUT-' . date('Ymd') . '-' . strtoupper(Str::random(5));

            $trx = TransaksiTabungan::create([
                'kode_transaksi' => $kodeTransaksi,
                'tabungan_id' => $tabungan->id,
                'pengajuan_id' => $pengajuanId,
                'jenis_transaksi' => 'Tarik',
                'nominal_kotor' => $nominalKotor,
                'persentase_potongan' => $kalkulasi['persentase_potongan'],
                'nominal_potongan' => $kalkulasi['nominal_potongan'],
                'nominal_bersih' => $kalkulasi['nominal_bersih'],
                'saldo_awal' => $saldoAwal,
                'saldo_akhir' => $saldoAkhir,
                'tanggal' => $tanggal ?? date('Y-m-d'),
                'ruangan_id' => $tabungan->ruangan_id,
                'petugas_id' => $petugasId,
                'metode' => $metode,
                'keterangan' => $keterangan ?? 'Penarikan Tabungan Tunai',
            ]);

            $tabungan->update([
                'saldo' => $saldoAkhir,
                'total_tarik' => (float) $tabungan->total_tarik + $nominalKotor,
                'total_potongan' => (float) $tabungan->total_potongan + $kalkulasi['nominal_potongan'],
            ]);

            return $trx;
        });
    }

    /**
     * Update transaksi setor tunai dan sesuaikan saldo rekening secara atomik
     */
    public function updateSetorTunai(int $transaksiId, float $nominalBaru, string $tanggal, ?string $keterangan = null, ?int $petugasId = null)
    {
        if ($nominalBaru <= 0) {
            throw new \InvalidArgumentException('Nominal setoran harus lebih besar dari 0.');
        }

        return DB::transaction(function () use ($transaksiId, $nominalBaru, $tanggal, $keterangan, $petugasId) {
            $trx = TransaksiTabungan::where('id', $transaksiId)->lockForUpdate()->firstOrFail();

            if ($trx->jenis_transaksi !== 'Setor') {
                throw new \Exception('Hanya transaksi setoran yang dapat diedit melalui fitur ini.');
            }

            $tabungan = Tabungan::where('id', $trx->tabungan_id)->lockForUpdate()->firstOrFail();

            $nominalLama = (float) $trx->nominal_bersih;
            $selisih = $nominalBaru - $nominalLama;

            // Jika nominal baru lebih kecil, pastikan saldo tabungan mencukupi
            if ($selisih < 0 && ($tabungan->saldo + $selisih) < 0) {
                throw new \Exception("Saldo tidak mencukupi untuk pengurangan setoran ini. Saldo rekening saat ini: Rp " . number_format($tabungan->saldo, 0, ',', '.'));
            }

            $saldoBaru = (float) $tabungan->saldo + $selisih;
            $totalSetorBaru = max(0, (float) $tabungan->total_setor + $selisih);

            $trx->update([
                'nominal_kotor' => $nominalBaru,
                'nominal_bersih' => $nominalBaru,
                'saldo_akhir' => (float) $trx->saldo_awal + $nominalBaru,
                'tanggal' => $tanggal,
                'keterangan' => $keterangan ?? $trx->keterangan,
                'petugas_id' => $petugasId ?? $trx->petugas_id,
            ]);

            $tabungan->update([
                'saldo' => $saldoBaru,
                'total_setor' => $totalSetorBaru,
            ]);

            return $trx;
        });
    }

    /**
     * Hapus transaksi setor tunai dan kurangi saldo rekening
     */
    public function hapusSetorTunai(int $transaksiId)
    {
        return DB::transaction(function () use ($transaksiId) {
            $trx = TransaksiTabungan::where('id', $transaksiId)->lockForUpdate()->firstOrFail();

            if ($trx->jenis_transaksi !== 'Setor') {
                throw new \Exception('Hanya transaksi setoran yang dapat dihapus melalui fitur ini.');
            }

            $tabungan = Tabungan::where('id', $trx->tabungan_id)->lockForUpdate()->firstOrFail();
            $nominal = (float) $trx->nominal_bersih;

            if ($tabungan->saldo < $nominal) {
                throw new \Exception("Tidak dapat menghapus setoran ini karena saldo rekening saat ini (Rp " . number_format($tabungan->saldo, 0, ',', '.') . ") lebih kecil dari nominal setoran (Rp " . number_format($nominal, 0, ',', '.') . ") akibat penarikan saldo sebelumnya.");
            }

            $saldoBaru = (float) $tabungan->saldo - $nominal;
            $totalSetorBaru = max(0, (float) $tabungan->total_setor - $nominal);

            $tabungan->update([
                'saldo' => $saldoBaru,
                'total_setor' => $totalSetorBaru,
            ]);

            $kodeTrx = $trx->kode_transaksi;
            $trx->delete();

            return [
                'tabungan' => $tabungan,
                'kode_transaksi' => $kodeTrx,
                'nominal' => $nominal,
            ];
        });
    }

    /**
     * Rekap data ringkasan tabungan madrasah untuk dashboard
     */
    public function getRingkasanDashboard()
    {
        $totalSaldo = Tabungan::sum('saldo');
        $totalSetor = Tabungan::sum('total_setor');
        $totalTarik = Tabungan::sum('total_tarik');
        $totalPotongan = Tabungan::sum('total_potongan');

        $saldoMurid = Tabungan::where('jenis_nasabah', 'Murid')->sum('saldo');
        $saldoUstadz = Tabungan::where('jenis_nasabah', 'Ustadz')->sum('saldo');
        $saldoKas = Tabungan::where('jenis_nasabah', 'Kas Ruangan')->sum('saldo');
        $saldoUmum = Tabungan::where('jenis_nasabah', 'Umum')->sum('saldo');

        $totalRekening = Tabungan::count();
        $totalRekeningAktif = Tabungan::where('status', 'Aktif')->count();

        $transaksiHariIni = TransaksiTabungan::whereDate('tanggal', date('Y-m-d'))->get();
        $setorHariIni = $transaksiHariIni->where('jenis_transaksi', 'Setor')->sum('nominal_bersih');
        $tarikHariIni = $transaksiHariIni->where('jenis_transaksi', 'Tarik')->sum('nominal_bersih');

        return [
            'total_saldo' => (float) $totalSaldo,
            'total_setor' => (float) $totalSetor,
            'total_tarik' => (float) $totalTarik,
            'total_potongan' => (float) $totalPotongan,
            'saldo_murid' => (float) $saldoMurid,
            'saldo_ustadz' => (float) $saldoUstadz,
            'saldo_kas' => (float) $saldoKas,
            'saldo_umum' => (float) $saldoUmum,
            'total_rekening' => $totalRekening,
            'total_rekening_aktif' => $totalRekeningAktif,
            'setor_hari_ini' => (float) $setorHariIni,
            'tarik_hari_ini' => (float) $tarikHariIni,
        ];
    }
}
