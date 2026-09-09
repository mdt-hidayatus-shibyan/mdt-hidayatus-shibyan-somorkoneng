<?php

namespace App\Services\Tabungan;

use App\Models\Murid;
use App\Models\Ruangan;
use App\Models\Tabungan\PengaturanPotonganTabungan;
use App\Models\Tabungan\PeriodeTabungan;
use App\Models\Tabungan\RiwayatBukuTabungan;
use App\Models\Tabungan\Tabungan;
use App\Models\Tabungan\TabunganKomplain;
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
     * Ganti buku tabungan fisik ke barcode baru (karena hilang, rusak, atau penuh)
     */
    public function gantiBukuTabungan(int $tabunganId, string $nomorRekeningBaru, string $alasan, ?string $catatan = null, ?int $petugasId = null)
    {
        $nomorRekeningBaru = trim($nomorRekeningBaru);

        if (empty($nomorRekeningBaru)) {
            throw new \InvalidArgumentException('Nomor rekening / barcode baru tidak boleh kosong.');
        }

        return DB::transaction(function () use ($tabunganId, $nomorRekeningBaru, $alasan, $catatan, $petugasId) {
            $tabungan = Tabungan::where('id', $tabunganId)->lockForUpdate()->firstOrFail();

            if ($tabungan->nomor_rekening === $nomorRekeningBaru) {
                throw new \Exception("Nomor barcode baru ({$nomorRekeningBaru}) sama dengan barcode buku tabungan saat ini.");
            }

            // Cek apakah barcode baru sudah digunakan oleh rekening lain
            $exists = Tabungan::where('nomor_rekening', $nomorRekeningBaru)
                ->where('id', '!=', $tabunganId)
                ->exists();

            if ($exists) {
                throw new \Exception("Nomor barcode '{$nomorRekeningBaru}' sudah terdaftar pada rekening lain.");
            }

            $nomorLama = $tabungan->nomor_rekening;
            $saldoSaatIni = (float) $tabungan->saldo;

            // Catat ke riwayat penggantian buku tabungan
            $riwayat = RiwayatBukuTabungan::create([
                'tabungan_id' => $tabungan->id,
                'nomor_rekening_lama' => $nomorLama,
                'nomor_rekening_baru' => $nomorRekeningBaru,
                'alasan' => $alasan,
                'saldo_terakhir' => $saldoSaatIni,
                'catatan' => $catatan,
                'petugas_id' => $petugasId,
            ]);

            // Update nomor rekening di model Tabungan
            $tabungan->update([
                'nomor_rekening' => $nomorRekeningBaru,
            ]);

            return [
                'tabungan' => $tabungan,
                'riwayat' => $riwayat,
                'nomor_lama' => $nomorLama,
                'nomor_baru' => $nomorRekeningBaru,
            ];
        });
    }

    /**
     * Hitung kalkulasi potongan madrasah dari total akumulasi tabungan (total_setor)
     * Batas yang bisa ditarik = (Total Tabungan - Potongan Madrasah) - Total yang Sudah Ditarik
     */
    public function hitungPotongan(Tabungan $tabungan, ?float $customTotalSetor = null, ?float $customTotalTarik = null, ?float $customSaldo = null): array
    {
        $persen = PengaturanPotonganTabungan::getPersentase($tabungan->jenis_nasabah, $tabungan->periode_tabungan_id);

        $totalSetor = (float) ($customTotalSetor !== null ? $customTotalSetor : $tabungan->total_setor);
        if ($totalSetor <= 0 && $tabungan->saldo > 0) {
            $totalSetor = (float) $tabungan->saldo;
        }

        $totalTarik = (float) ($customTotalTarik !== null ? $customTotalTarik : $tabungan->total_tarik);
        $saldoSaatIni = (float) ($customSaldo !== null ? $customSaldo : $tabungan->saldo);

        $rawPotongan = ($totalSetor * $persen) / 100;
        $nominalPotongan = $rawPotongan > 0 ? (float) (ceil($rawPotongan / 100) * 100) : 0.00;
        $saldoBersihTotal = max(0, $totalSetor - $nominalPotongan);
        $saldoDapatDitarik = max(0, $saldoBersihTotal - $totalTarik);

        // Batas penarikan tidak boleh melebihi saldo kas fisik yang tersedia di rekening saat ini
        $saldoDapatDitarik = min($saldoDapatDitarik, $saldoSaatIni);

        return [
            'persentase_potongan' => $persen,
            'nominal_potongan' => $nominalPotongan,
            'total_tabungan' => $totalSetor,
            'total_setor' => $totalSetor,
            'total_tarik' => $totalTarik,
            'saldo_saat_ini' => $saldoSaatIni,
            'saldo_total' => $totalSetor,
            'saldo_bersih_total' => $saldoBersihTotal,
            'saldo_dapat_ditarik' => $saldoDapatDitarik,
            'nominal_bersih' => $saldoDapatDitarik,
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
     * Uang tunai diserahkan utuh, dengan batas maksimal penarikan = (Total Tabungan - Potongan) - Total Sudah Ditarik
     */
    public function tarikTunai(int $tabunganId, float $nominal, ?int $petugasId = null, ?string $tanggal = null, ?string $keterangan = null, string $metode = 'Tunai', ?int $kategoriPenarikanId = null)
    {
        if ($nominal <= 0) {
            throw new \InvalidArgumentException('Nominal penarikan harus lebih besar dari 0.');
        }

        return DB::transaction(function () use ($tabunganId, $nominal, $petugasId, $tanggal, $keterangan, $metode, $kategoriPenarikanId) {
            $tabungan = Tabungan::where('id', $tabunganId)->lockForUpdate()->firstOrFail();

            $kalkulasi = $this->hitungPotongan($tabungan);
            $saldoDapatDitarik = $kalkulasi['saldo_dapat_ditarik'];

            if ($nominal > $saldoDapatDitarik) {
                throw new \Exception("Nominal penarikan (Rp " . number_format($nominal, 0, ',', '.') . ") melebihi batas saldo yang dapat ditarik (Maksimal: Rp " . number_format($saldoDapatDitarik, 0, ',', '.') . ", setelah alokasi potongan madrasah {$kalkulasi['persentase_potongan']}% sebesar Rp " . number_format($kalkulasi['nominal_potongan'], 0, ',', '.') . " dari total tabungan Rp " . number_format($kalkulasi['total_tabungan'], 0, ',', '.') . ").");
            }

            $saldoAwal = (float) $tabungan->saldo;
            $saldoAkhir = $saldoAwal - $nominal;

            $kodeTransaksi = 'TRX-OUT-' . date('Ymd') . '-' . strtoupper(Str::random(5));

            $trx = TransaksiTabungan::create([
                'kode_transaksi' => $kodeTransaksi,
                'tabungan_id' => $tabungan->id,
                'kategori_penarikan_id' => $kategoriPenarikanId,
                'jenis_transaksi' => 'Tarik',
                'nominal_kotor' => $nominal,
                'persentase_potongan' => 0.00,
                'nominal_potongan' => 0.00,
                'nominal_bersih' => $nominal,
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
                'total_tarik' => (float) $tabungan->total_tarik + $nominal,
            ]);

            // Jika rekening adalah tipe Kas Ruangan, buka kunci cicilan pembayaran kas murid (uang kembali ke tangan Wali)
            if ($tabungan->jenis_nasabah === 'Kas Ruangan' && $tabungan->ruangan_id) {
                $this->bukaKunciCicilanKas($tabungan->ruangan_id, $nominal);
            }

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
                'tanggal' => $tanggal,
                'keterangan' => $keterangan ?? $trx->keterangan,
                'petugas_id' => $petugasId ?? $trx->petugas_id,
            ]);

            $this->recalibrasiSaldoRekening($tabungan->id);

            return $trx->fresh();
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

            $kodeTrx = $trx->kode_transaksi;
            $trx->delete();

            $this->recalibrasiSaldoRekening($tabungan->id);

            return [
                'tabungan' => $tabungan->fresh(),
                'kode_transaksi' => $kodeTrx,
                'nominal' => $nominal,
            ];
        });
    }

    /**
     * Update transaksi penarikan tunai dan sesuaikan saldo rekening secara atomik (Tanpa potongan madrasah)
     */
    public function updateTarikTunai(int $transaksiId, float $nominalKotorBaru, string $tanggal, ?string $keterangan = null, ?int $petugasId = null, ?int $kategoriPenarikanId = null)
    {
        if ($nominalKotorBaru <= 0) {
            throw new \InvalidArgumentException('Nominal penarikan harus lebih besar dari 0.');
        }

        return DB::transaction(function () use ($transaksiId, $nominalKotorBaru, $tanggal, $keterangan, $petugasId, $kategoriPenarikanId) {
            $trx = TransaksiTabungan::where('id', $transaksiId)->lockForUpdate()->firstOrFail();

            if ($trx->jenis_transaksi !== 'Tarik') {
                throw new \Exception('Hanya transaksi penarikan yang dapat diedit melalui fitur ini.');
            }

            $tabungan = Tabungan::where('id', $trx->tabungan_id)->lockForUpdate()->firstOrFail();

            $nominalKotorLama = (float) $trx->nominal_kotor;
            $selisihKotor = $nominalKotorBaru - $nominalKotorLama;

            // Kembalikan dulu saldo tabungan ke sebelum penarikan ini, lalu cek apakah saldo mencukupi
            $saldoTersedia = (float) $tabungan->saldo + $nominalKotorLama;
            $totalTarikSebelumnya = max(0, (float) $tabungan->total_tarik - $nominalKotorLama);
            $kalkulasi = $this->hitungPotongan($tabungan, null, $totalTarikSebelumnya, $saldoTersedia);
            $saldoDapatDitarik = $kalkulasi['saldo_dapat_ditarik'];

            if ($nominalKotorBaru > $saldoDapatDitarik) {
                throw new \Exception("Nominal penarikan baru (Rp " . number_format($nominalKotorBaru, 0, ',', '.') . ") melebihi batas saldo yang dapat ditarik (Maksimal: Rp " . number_format($saldoDapatDitarik, 0, ',', '.') . ", setelah alokasi potongan madrasah {$kalkulasi['persentase_potongan']}% sebesar Rp " . number_format($kalkulasi['nominal_potongan'], 0, ',', '.') . " dari total tabungan Rp " . number_format($kalkulasi['total_tabungan'], 0, ',', '.') . ").");
            }

            $trx->update([
                'nominal_kotor' => $nominalKotorBaru,
                'persentase_potongan' => 0.00,
                'nominal_potongan' => 0.00,
                'nominal_bersih' => $nominalKotorBaru,
                'tanggal' => $tanggal,
                'kategori_penarikan_id' => $kategoriPenarikanId ?? $trx->kategori_penarikan_id,
                'keterangan' => $keterangan ?? $trx->keterangan,
                'petugas_id' => $petugasId ?? $trx->petugas_id,
            ]);

            // Jika rekening Kas Ruangan, sesuaikan pembukaan/penguncian cicilan
            if ($tabungan->jenis_nasabah === 'Kas Ruangan' && $tabungan->ruangan_id) {
                if ($selisihKotor > 0) {
                    $this->bukaKunciCicilanKas($tabungan->ruangan_id, $selisihKotor);
                } elseif ($selisihKotor < 0) {
                    $this->kunciCicilanKas($tabungan->ruangan_id, abs($selisihKotor));
                }
            }

            $this->recalibrasiSaldoRekening($tabungan->id);

            return $trx->fresh();
        });
    }

    /**
     * Hapus transaksi penarikan tunai dan kembalikan saldo rekening
     */
    public function hapusTarikTunai(int $transaksiId)
    {
        return DB::transaction(function () use ($transaksiId) {
            $trx = TransaksiTabungan::where('id', $transaksiId)->lockForUpdate()->firstOrFail();

            if ($trx->jenis_transaksi !== 'Tarik') {
                throw new \Exception('Hanya transaksi penarikan yang dapat dihapus melalui fitur ini.');
            }

            $tabungan = Tabungan::where('id', $trx->tabungan_id)->lockForUpdate()->firstOrFail();
            $nominalKotor = (float) $trx->nominal_kotor;
            $kodeTrx = $trx->kode_transaksi;
            $nominalBersih = (float) $trx->nominal_bersih;

            // Jika rekening Kas Ruangan, kunci kembali cicilan pembayaran kas
            if ($tabungan->jenis_nasabah === 'Kas Ruangan' && $tabungan->ruangan_id) {
                $this->kunciCicilanKas($tabungan->ruangan_id, $nominalKotor);
            }

            $trx->delete();

            $this->recalibrasiSaldoRekening($tabungan->id);

            return [
                'tabungan' => $tabungan->fresh(),
                'kode_transaksi' => $kodeTrx,
                'nominal_kotor' => $nominalKotor,
                'nominal_bersih' => $nominalBersih,
            ];
        });
    }

    /**
     * Rekalibrasi saldo berjalan seluruh transaksi dan total saldo rekening secara kronologis
     */
    public function recalibrasiSaldoRekening(int $tabunganId)
    {
        return DB::transaction(function () use ($tabunganId) {
            $tabungan = Tabungan::where('id', $tabunganId)->lockForUpdate()->firstOrFail();
            $transaksis = TransaksiTabungan::where('tabungan_id', $tabunganId)
                ->orderBy('tanggal', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            $currentBalance = 0.00;
            $totalSetor = 0.00;
            $totalTarik = 0.00;
            $totalPotongan = 0.00;

            foreach ($transaksis as $trx) {
                $saldoAwal = $currentBalance;

                if ($trx->jenis_transaksi === 'Setor') {
                    $currentBalance += (float) $trx->nominal_bersih;
                    $totalSetor += (float) $trx->nominal_bersih;
                    $saldoAkhir = $currentBalance;
                } elseif ($trx->jenis_transaksi === 'Tarik') {
                    $currentBalance -= (float) $trx->nominal_bersih;
                    $totalTarik += (float) $trx->nominal_kotor;
                    $totalPotongan += (float) $trx->nominal_potongan;
                    $saldoAkhir = $currentBalance;
                } elseif ($trx->jenis_transaksi === 'Pembagian_Akhir') {
                    $totalTarik += (float) $trx->nominal_kotor;
                    $totalPotongan += (float) $trx->nominal_potongan;
                    $currentBalance = 0.00;
                    $saldoAkhir = 0.00;
                } else {
                    $saldoAkhir = $currentBalance;
                }

                $trx->update([
                    'saldo_awal' => $saldoAwal,
                    'saldo_akhir' => $saldoAkhir,
                ]);
            }

            $tabungan->update([
                'saldo' => $currentBalance,
                'total_setor' => $totalSetor,
                'total_tarik' => $totalTarik,
                'total_potongan' => $totalPotongan,
            ]);

            return $tabungan;
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
        $komplainPending = TabunganKomplain::where('status', 'Menunggu_Verifikasi')->count();

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
            'komplain_pending' => $komplainPending,
        ];
    }

    /**
     * Mengambil rekapitulasi mutasi transaksi per bulan Masehi untuk buku tabungan tertentu
     */
    public function getRekapMutasiBulanan(Tabungan $tabungan, ?string $filterBulan = null)
    {
        // Query transaksi secara kronologis (dari terlama ke terbaru)
        $transaksis = TransaksiTabungan::where('tabungan_id', $tabungan->id)
            ->with(['petugas', 'kategoriPenarikan'])
            ->orderBy('tanggal', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $runningBalance = 0.00;
        $monthlyData = [];
        $daftarTransaksi = [];

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
            12 => 'Desember'
        ];

        foreach ($transaksis as $trx) {
            $isSetor = $trx->jenis_transaksi === 'Setor';
            $isTarik = in_array($trx->jenis_transaksi, ['Tarik', 'Pembagian_Akhir', 'Koreksi']);

            $masuk = $isSetor ? (float) $trx->nominal_bersih : 0.00;
            $keluar = $isTarik ? (float) $trx->nominal_bersih : 0.00;

            $saldoSebelum = $runningBalance;
            if ($isSetor) {
                $runningBalance += $masuk;
            } elseif ($trx->jenis_transaksi === 'Pembagian_Akhir') {
                $runningBalance = 0.00;
            } else {
                $runningBalance -= $keluar;
            }
            $saldoAkhir = $runningBalance;

            $dateObj = \Carbon\Carbon::parse($trx->tanggal);
            $yearMonth = $dateObj->format('Y-m');
            $monthNum = (int) $dateObj->format('n');
            $yearNum = $dateObj->format('Y');
            $labelBulan = ($namaBulanIndo[$monthNum] ?? $dateObj->format('F')) . ' ' . $yearNum;

            if (!isset($monthlyData[$yearMonth])) {
                $monthlyData[$yearMonth] = [
                    'key' => $yearMonth,
                    'label' => $labelBulan,
                    'tahun' => (int) $yearNum,
                    'bulan' => $monthNum,
                    'saldo_awal' => $saldoSebelum,
                    'total_setor' => 0.00,
                    'frekuensi_setor' => 0,
                    'total_tarik' => 0.00,
                    'frekuensi_tarik' => 0,
                    'net_mutasi' => 0.00,
                    'saldo_akhir' => 0.00,
                    'transaksis' => [],
                ];
            }

            if ($isSetor) {
                $monthlyData[$yearMonth]['total_setor'] += $masuk;
                $monthlyData[$yearMonth]['frekuensi_setor']++;
            } else {
                $monthlyData[$yearMonth]['total_tarik'] += $keluar;
                $monthlyData[$yearMonth]['frekuensi_tarik']++;
            }

            $monthlyData[$yearMonth]['net_mutasi'] = $monthlyData[$yearMonth]['total_setor'] - $monthlyData[$yearMonth]['total_tarik'];
            $monthlyData[$yearMonth]['saldo_akhir'] = $saldoAkhir;

            $itemTrx = [
                'id' => $trx->id,
                'kode_transaksi' => $trx->kode_transaksi,
                'tanggal' => $trx->tanggal,
                'tanggal_formatted' => $dateObj->format('d/m/Y'),
                'year_month' => $yearMonth,
                'jenis_transaksi' => $trx->jenis_transaksi,
                'kategori' => $trx->kategoriPenarikan?->nama_kategori ?? ($trx->jenis_transaksi === 'Setor' ? 'Setoran Tunai' : $trx->jenis_transaksi),
                'masuk' => $masuk,
                'keluar' => $keluar,
                'saldo_awal' => $saldoSebelum,
                'saldo_akhir' => $saldoAkhir,
                'keterangan' => $trx->keterangan,
                'petugas' => $trx->petugas?->name ?? 'Sistem',
                'metode' => $trx->metode,
            ];

            $monthlyData[$yearMonth]['transaksis'][] = $itemTrx;
            $daftarTransaksi[] = $itemTrx;
        }

        // Filter daftar transaksi jika user memilih bulan spesifik
        $filteredDaftarTransaksi = $daftarTransaksi;
        if ($filterBulan && $filterBulan !== 'semua') {
            $filteredDaftarTransaksi = array_values(array_filter($daftarTransaksi, function ($item) use ($filterBulan) {
                return $item['year_month'] === $filterBulan;
            }));
        }

        // Hitung akumulasi statistik
        $totalSetoran = (float) $transaksis->where('jenis_transaksi', 'Setor')->sum('nominal_bersih');
        $totalPenarikan = (float) $transaksis->whereIn('jenis_transaksi', ['Tarik', 'Pembagian_Akhir', 'Koreksi'])->sum('nominal_bersih');

        return [
            'tabungan' => $tabungan,
            'filter_bulan' => $filterBulan,
            'rekap_bulanan' => array_values($monthlyData),
            'daftar_transaksi' => $filteredDaftarTransaksi, // Urutan kronologis buku tabungan (terlama ke terbaru)
            'daftar_transaksi_kronologis' => $filteredDaftarTransaksi, // Terlama di atas untuk buku
            'total_transaksi' => $transaksis->count(),
            'total_transaksi_filtered' => count($filteredDaftarTransaksi),
            'total_setoran' => $totalSetoran,
            'total_penarikan' => $totalPenarikan,
            'saldo_terakhir' => (float) $tabungan->saldo,
        ];
    }

    /**
     * Mendapatkan rekening tabungan sebelum dan sesudah berdasarkan nomor rekening
     */
    public function getAdjacentTabungan(Tabungan $tabungan)
    {
        $prev = Tabungan::where('nomor_rekening', '<', $tabungan->nomor_rekening)
            ->orderBy('nomor_rekening', 'desc')
            ->first();

        $next = Tabungan::where('nomor_rekening', '>', $tabungan->nomor_rekening)
            ->orderBy('nomor_rekening', 'asc')
            ->first();

        return [
            'prev' => $prev,
            'next' => $next,
        ];
    }

    /**
     * Verifikasi komplain setoran tunai oleh Admin / Bendahara
     */
    public function verifikasiKomplainSetoran(int $komplainId, string $tindakan, string $catatan, ?int $petugasId = null, ?float $nominalDisetujui = null)
    {
        return DB::transaction(function () use ($komplainId, $tindakan, $catatan, $petugasId, $nominalDisetujui) {
            $komplain = TabunganKomplain::with(['transaksiTabungan', 'tabungan'])->where('id', $komplainId)->lockForUpdate()->firstOrFail();

            if ($komplain->status !== 'Menunggu_Verifikasi') {
                throw new \Exception("Komplain {$komplain->kode_komplain} sudah diproses sebelumnya (Status: {$komplain->status}).");
            }

            if ($tindakan === 'setujui') {
                $nominalBaru = (float) ($nominalDisetujui !== null ? $nominalDisetujui : $komplain->nominal_klaim);
                $tanggal = $komplain->transaksiTabungan->tanggal ? \Carbon\Carbon::parse($komplain->transaksiTabungan->tanggal)->format('Y-m-d') : date('Y-m-d');
                $keteranganKoreksi = "Koreksi komplain ({$komplain->kode_komplain}): " . ($catatan ?: 'Penyesuaian nominal setoran sesuai klaim wali murid');

                // Update transaksi dan rekalibrasi saldo buku tabungan
                $this->updateSetorTunai(
                    $komplain->transaksi_tabungan_id,
                    $nominalBaru,
                    $tanggal,
                    $keteranganKoreksi,
                    $petugasId
                );

                $komplain->update([
                    'status' => 'Disetujui',
                    'diverifikasi_oleh' => $petugasId,
                    'diverifikasi_pada' => now(),
                    'catatan_verifikasi' => $catatan ?: 'Komplain disetujui, nominal setoran telah disesuaikan.',
                ]);
            } elseif ($tindakan === 'tolak') {
                if (empty(trim($catatan))) {
                    throw new \InvalidArgumentException('Catatan / alasan penolakan wajib diisi.');
                }

                $komplain->update([
                    'status' => 'Ditolak',
                    'diverifikasi_oleh' => $petugasId,
                    'diverifikasi_pada' => now(),
                    'catatan_verifikasi' => $catatan,
                ]);
            } else {
                throw new \InvalidArgumentException("Tindakan '{$tindakan}' tidak valid. Pilih 'setujui' atau 'tolak'.");
            }

            return $komplain->fresh(['transaksiTabungan', 'tabungan', 'murid', 'diverifikasiOleh']);
        });
    }

    /**
     * Kunci cicilan pembayaran kas murid saat disetor ke Tabungan Madrasah
     */
    public function kunciCicilanKas(int $ruanganId, float $nominal)
    {
        $sisa = $nominal;
        $cicilans = \App\Models\KasRuangan\PembayaranKasRuangan::where('ruangan_id', $ruanganId)
            ->where('is_disetor', false)
            ->orderBy('tanggal_bayar', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        foreach ($cicilans as $c) {
            if ($sisa >= $c->jumlah_bayar) {
                $c->update(['is_disetor' => true]);
                $sisa -= $c->jumlah_bayar;
            } else {
                break;
            }
        }
    }

    /**
     * Buka kunci cicilan pembayaran kas murid saat terjadi penarikan tunai tabungan kas (uang kembali ke tangan Wali)
     */
    public function bukaKunciCicilanKas(int $ruanganId, float $nominal)
    {
        $sisaRefund = $nominal;
        $cicilansTerkunci = \App\Models\KasRuangan\PembayaranKasRuangan::where('ruangan_id', $ruanganId)
            ->where('is_disetor', true)
            ->orderBy('tanggal_bayar', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        foreach ($cicilansTerkunci as $c) {
            if ($sisaRefund >= $c->jumlah_bayar) {
                $c->update(['is_disetor' => false]);
                $sisaRefund -= $c->jumlah_bayar;
            } else {
                break;
            }
        }
    }
}
