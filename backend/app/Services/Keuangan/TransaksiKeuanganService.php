<?php

namespace App\Services\Keuangan;

use App\Models\Keuangan\AkunKeuangan;
use App\Models\Keuangan\Bank;
use App\Models\Keuangan\TransaksiKeuangan;
use App\Models\TahunPelajaran;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransaksiKeuanganService
{
    /**
     * Generate Kode Transaksi Otomatis
     */
    public function generateKodeTransaksi(string $jenis = 'TRX'): string
    {
        $date = now()->format('Ymd');
        $prefix = strtoupper($jenis);
        $countToday = TransaksiKeuangan::whereDate('created_at', now()->toDateString())->count() + 1;
        return sprintf('%s-%s-%04d', $prefix, $date, $countToday);
    }

    /**
     * Catat Transaksi Baru dengan Locking Saldo
     */
    public function createTransaksi(array $data, ?string $buktiPath = null): TransaksiKeuangan
    {
        return DB::transaction(function () use ($data, $buktiPath) {
            $tahunPelajaranId = $data['tahun_pelajaran_id'] ?? getTahunPelajaranAktif()?->id;
            if (!$tahunPelajaranId) {
                $tpAktif = TahunPelajaran::where('is_active', true)->first();
                $tahunPelajaranId = $tpAktif?->id ?? TahunPelajaran::latest()->value('id');
            }

            $nominal = (float) $data['nominal'];
            $jenis = $data['jenis_transaksi'];
            $metode = $data['metode_pembayaran'] ?? 'tunai';
            $kodeTransaksi = $data['kode_transaksi'] ?? $this->generateKodeTransaksi(match ($jenis) {
                'pemasukan' => 'IN',
                'pengeluaran' => 'OUT',
                'mutasi' => 'MUT',
                'simpanan' => 'SAV',
                default => 'TRX'
            });

            // 1. Lock Sumber Akun
            $akun = AkunKeuangan::where('id', $data['akun_keuangan_id'])->lockForUpdate()->firstOrFail();
            $bank = null;
            if ($metode === 'transfer_bank' && !empty($data['bank_id'])) {
                $bank = Bank::where('id', $data['bank_id'])->lockForUpdate()->first();
            }

            // 2. Adjust Saldo berdasarkan Jenis Transaksi
            if ($jenis === 'pemasukan' || $jenis === 'simpanan') {
                $akun->saldo_berjalan += $nominal;
                $akun->save();

                if ($bank) {
                    $bank->saldo += $nominal;
                    $bank->save();
                }
            } elseif ($jenis === 'pengeluaran') {
                if ($akun->saldo_berjalan < $nominal) {
                    throw new \Exception("Saldo pada akun '{$akun->nama_akun}' tidak mencukupi. Saldo saat ini: Rp " . number_format($akun->saldo_berjalan, 0, ',', '.'));
                }
                if ($bank && $bank->saldo < $nominal) {
                    throw new \Exception("Saldo pada rekening bank '{$bank->nama_bank}' tidak mencukupi. Saldo saat ini: Rp " . number_format($bank->saldo, 0, ',', '.'));
                }

                $akun->saldo_berjalan -= $nominal;
                $akun->save();

                if ($bank) {
                    $bank->saldo -= $nominal;
                    $bank->save();
                }
            } elseif ($jenis === 'mutasi') {
                // Mutasi Antar Kas / Rekening
                if ($akun->saldo_berjalan < $nominal) {
                    throw new \Exception("Saldo akun asal '{$akun->nama_akun}' tidak mencukupi untuk mutasi.");
                }

                $akun->saldo_berjalan -= $nominal;
                $akun->save();

                if ($bank) {
                    $bank->saldo -= $nominal;
                    $bank->save();
                }

                // Akun Tujuan
                if (!empty($data['akun_tujuan_id'])) {
                    $akunTujuan = AkunKeuangan::where('id', $data['akun_tujuan_id'])->lockForUpdate()->firstOrFail();
                    $akunTujuan->saldo_berjalan += $nominal;
                    $akunTujuan->save();
                }

                // Bank Tujuan
                if (!empty($data['bank_tujuan_id'])) {
                    $bankTujuan = Bank::where('id', $data['bank_tujuan_id'])->lockForUpdate()->firstOrFail();
                    $bankTujuan->saldo += $nominal;
                    $bankTujuan->save();
                }
            }

            // 3. Simpan Record Transaksi
            return TransaksiKeuangan::create([
                'kode_transaksi' => $kodeTransaksi,
                'tahun_pelajaran_id' => $tahunPelajaranId,
                'akun_keuangan_id' => $akun->id,
                'kategori_keuangan_id' => $data['kategori_keuangan_id'] ?? null,
                'jenis_transaksi' => $jenis,
                'metode_pembayaran' => $metode,
                'bank_id' => $bank?->id,
                'akun_tujuan_id' => $data['akun_tujuan_id'] ?? null,
                'bank_tujuan_id' => $data['bank_tujuan_id'] ?? null,
                'nominal' => $nominal,
                'tanggal_transaksi' => $data['tanggal_transaksi'] ?? now()->toDateString(),
                'nomor_referensi' => $data['nomor_referensi'] ?? null,
                'keterangan' => $data['keterangan'] ?? null,
                'bukti_transaksi' => $buktiPath,
                'user_id' => auth()->id() ?? $data['user_id'] ?? 1,
                'status' => 'sukses',
            ]);
        });
    }

    /**
     * Batalkan Transaksi & Kembalikan Saldo
     */
    public function batalkanTransaksi(int $id, ?string $alasan = null): TransaksiKeuangan
    {
        return DB::transaction(function () use ($id, $alasan) {
            $trx = TransaksiKeuangan::where('id', $id)->lockForUpdate()->firstOrFail();

            if ($trx->status === 'batal') {
                throw new \Exception("Transaksi {$trx->kode_transaksi} sudah berstatus batal.");
            }

            $nominal = (float) $trx->nominal;
            $akun = AkunKeuangan::where('id', $trx->akun_keuangan_id)->lockForUpdate()->firstOrFail();
            $bank = $trx->bank_id ? Bank::where('id', $trx->bank_id)->lockForUpdate()->first() : null;

            // Rollback saldo
            if ($trx->jenis_transaksi === 'pemasukan' || $trx->jenis_transaksi === 'simpanan') {
                if ($akun->saldo_berjalan < $nominal) {
                    throw new \Exception("Tidak dapat membatalkan transaksi pemasukan karena saldo saat ini lebih kecil dari nominal transaksi.");
                }
                $akun->saldo_berjalan -= $nominal;
                $akun->save();

                if ($bank) {
                    $bank->saldo -= $nominal;
                    $bank->save();
                }
            } elseif ($trx->jenis_transaksi === 'pengeluaran') {
                $akun->saldo_berjalan += $nominal;
                $akun->save();

                if ($bank) {
                    $bank->saldo += $nominal;
                    $bank->save();
                }
            } elseif ($trx->jenis_transaksi === 'mutasi') {
                $akun->saldo_berjalan += $nominal;
                $akun->save();

                if ($bank) {
                    $bank->saldo += $nominal;
                    $bank->save();
                }

                if ($trx->akun_tujuan_id) {
                    $akunTujuan = AkunKeuangan::where('id', $trx->akun_tujuan_id)->lockForUpdate()->first();
                    if ($akunTujuan) {
                        $akunTujuan->saldo_berjalan -= $nominal;
                        $akunTujuan->save();
                    }
                }

                if ($trx->bank_tujuan_id) {
                    $bankTujuan = Bank::where('id', $trx->bank_tujuan_id)->lockForUpdate()->first();
                    if ($bankTujuan) {
                        $bankTujuan->saldo -= $nominal;
                        $bankTujuan->save();
                    }
                }
            }

            $trx->status = 'batal';
            $trx->keterangan = trim($trx->keterangan . " [DIBATALKAN: " . ($alasan ?? 'Pembatalan oleh administrator') . "]");
            $trx->save();

            return $trx;
        });
    }
}
