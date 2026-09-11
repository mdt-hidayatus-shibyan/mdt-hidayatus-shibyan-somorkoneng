<?php

namespace App\Services\Keuangan;

use App\Models\Keuangan\AngsuranPinjaman;
use App\Models\Keuangan\JaminanPinjaman;
use App\Models\Keuangan\NasabahPinjaman;
use App\Models\Keuangan\Pinjaman;
use App\Models\TahunPelajaran;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PinjamanService
{
    public function __construct(
        protected TransaksiKeuanganService $transaksiService
    ) {}

    /**
     * Generate Kode Pinjaman Otomatis
     */
    public function generateKodePinjaman(): string
    {
        $date = now()->format('Ym');
        $count = Pinjaman::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count() + 1;
        return sprintf('PJ-%s-%04d', $date, $count);
    }

    /**
     * Generate Kode Nasabah Otomatis
     */
    public function generateKodeNasabah(): string
    {
        $count = NasabahPinjaman::count() + 1;
        return sprintf('NSB-%05d', $count);
    }

    /**
     * Buat Pengajuan Pinjaman Baru beserta Agunan/Jaminan
     */
    public function createPengajuan(array $data, array $jaminanList = []): Pinjaman
    {
        return DB::transaction(function () use ($data, $jaminanList) {
            $tahunPelajaranId = $data['tahun_pelajaran_id'] ?? getTahunPelajaranAktif()?->id;
            if (!$tahunPelajaranId) {
                $tpAktif = TahunPelajaran::where('is_active', true)->first();
                $tahunPelajaranId = $tpAktif?->id ?? TahunPelajaran::latest()->value('id');
            }

            $nominalPinjaman = (float) $data['nominal_pinjaman'];
            $biayaAdmin = (float) ($data['biaya_administrasi'] ?? 0);
            $tenorBulan = (int) $data['tenor_bulan'];
            $marginPersen = (float) ($data['margin_infaq_persen'] ?? 0);

            // Perhitungan Pinjaman
            $nominalPencairan = $nominalPinjaman - $biayaAdmin;
            $angsuranPokok = round($nominalPinjaman / $tenorBulan, 2);
            $infaqBulanan = round(($nominalPinjaman * ($marginPersen / 100)) / $tenorBulan, 2);
            $angsuranTotal = $angsuranPokok + $infaqBulanan;
            $totalKembali = $nominalPinjaman + ($infaqBulanan * $tenorBulan);

            $pinjaman = Pinjaman::create([
                'kode_pinjaman' => $this->generateKodePinjaman(),
                'tahun_pelajaran_id' => $tahunPelajaranId,
                'nasabah_pinjaman_id' => $data['nasabah_pinjaman_id'],
                'akun_keuangan_id' => $data['akun_keuangan_id'],
                'bank_id' => $data['bank_id'] ?? null,
                'nominal_pinjaman' => $nominalPinjaman,
                'biaya_administrasi' => $biayaAdmin,
                'nominal_pencairan' => $nominalPencairan,
                'tenor_bulan' => $tenorBulan,
                'margin_infaq_persen' => $marginPersen,
                'nominal_infaq_bulanan' => $infaqBulanan,
                'nominal_angsuran_pokok' => $angsuranPokok,
                'nominal_angsuran_total' => $angsuranTotal,
                'total_pinjaman_dikembalikan' => $totalKembali,
                'total_terbayar' => 0,
                'sisa_pinjaman' => $nominalPinjaman,
                'tanggal_pengajuan' => $data['tanggal_pengajuan'] ?? now()->toDateString(),
                'keperluan_pinjaman' => $data['keperluan_pinjaman'],
                'status' => 'pengajuan',
                'catatan' => $data['catatan'] ?? null,
            ]);

            // Simpan Data Jaminan jika ada
            foreach ($jaminanList as $jmn) {
                if (!empty($jmn['nama_barang_jaminan'])) {
                    JaminanPinjaman::create([
                        'pinjaman_id' => $pinjaman->id,
                        'jenis_jaminan' => $jmn['jenis_jaminan'] ?? 'lainnya',
                        'nama_barang_jaminan' => $jmn['nama_barang_jaminan'],
                        'nomor_dokumen_jaminan' => $jmn['nomor_dokumen_jaminan'] ?? null,
                        'atas_nama_dokumen' => $jmn['atas_nama_dokumen'] ?? null,
                        'taksiran_nilai' => (float) ($jmn['taksiran_nilai'] ?? 0),
                        'deskripsi_kondisi' => $jmn['deskripsi_kondisi'] ?? null,
                        'lokasi_penyimpanan' => $jmn['lokasi_penyimpanan'] ?? 'Brankas MDT Hidayatus Shibyan',
                        'foto_dokumen' => $jmn['foto_dokumen'] ?? null,
                        'foto_barang' => $jmn['foto_barang'] ?? null,
                        'status_jaminan' => 'ditahan_madrasah',
                        'tanggal_diserahkan' => $pinjaman->tanggal_pengajuan,
                        'penerima_jaminan_id' => auth()->id() ?? 1,
                        'catatan' => $jmn['catatan'] ?? null,
                    ]);
                }
            }

            return $pinjaman;
        });
    }

    /**
     * Setujui Pengajuan Pinjaman
     */
    public function approvePinjaman(int $id, int $userId, ?string $catatan = null): Pinjaman
    {
        return DB::transaction(function () use ($id, $userId, $catatan) {
            $pinjaman = Pinjaman::where('id', $id)->lockForUpdate()->firstOrFail();

            if ($pinjaman->status !== 'pengajuan') {
                throw new \Exception("Pinjaman tidak dalam status pengajuan.");
            }

            $pinjaman->status = 'disetujui';
            $pinjaman->disetujui_oleh = $userId;
            if ($catatan) {
                $pinjaman->catatan = trim($pinjaman->catatan . "\n[ACC]: " . $catatan);
            }
            $pinjaman->save();

            return $pinjaman;
        });
    }

    /**
     * Tolak Pengajuan Pinjaman
     */
    public function tolakPinjaman(int $id, int $userId, string $alasan): Pinjaman
    {
        return DB::transaction(function () use ($id, $userId, $alasan) {
            $pinjaman = Pinjaman::where('id', $id)->lockForUpdate()->firstOrFail();

            if (!in_array($pinjaman->status, ['pengajuan', 'disetujui'])) {
                throw new \Exception("Pinjaman yang sudah dicairkan atau lunas tidak dapat ditolak.");
            }

            $pinjaman->status = 'ditolak';
            $pinjaman->catatan = trim($pinjaman->catatan . "\n[DITOLAK]: " . $alasan);
            $pinjaman->save();

            // Kembalikan status jaminan jika ada
            foreach ($pinjaman->jaminans as $jmn) {
                $jmn->status_jaminan = 'dikembalikan';
                $jmn->tanggal_dikembalikan = now()->toDateString();
                $jmn->pengembali_jaminan_id = $userId;
                $jmn->catatan = 'Ditolak: ' . $alasan;
                $jmn->save();
            }

            return $pinjaman;
        });
    }

    /**
     * Pencairan Dana Pinjaman & Generate Jadwal Angsuran
     */
    public function cairkanPinjaman(int $id, int $userId, array $cairkanData = []): Pinjaman
    {
        return DB::transaction(function () use ($id, $userId, $cairkanData) {
            $pinjaman = Pinjaman::where('id', $id)->lockForUpdate()->firstOrFail();

            if ($pinjaman->status !== 'disetujui') {
                throw new \Exception("Pinjaman harus berstatus disetujui sebelum dicairkan.");
            }

            $tanggalPencairan = $cairkanData['tanggal_pencairan'] ?? now()->toDateString();
            $akunId = $cairkanData['akun_keuangan_id'] ?? $pinjaman->akun_keuangan_id;
            $bankId = $cairkanData['bank_id'] ?? $pinjaman->bank_id;
            $metode = !empty($bankId) ? 'transfer_bank' : 'tunai';

            // 1. Catat Pengeluaran Kas Pencairan Pinjaman
            $this->transaksiService->createTransaksi([
                'tahun_pelajaran_id' => $pinjaman->tahun_pelajaran_id,
                'akun_keuangan_id' => $akunId,
                'bank_id' => $bankId,
                'jenis_transaksi' => 'pengeluaran',
                'metode_pembayaran' => $metode,
                'nominal' => $pinjaman->nominal_pencairan,
                'tanggal_transaksi' => $tanggalPencairan,
                'nomor_referensi' => $pinjaman->kode_pinjaman,
                'keterangan' => "Pencairan Pinjaman [{$pinjaman->kode_pinjaman}] an. {$pinjaman->nasabah->nama_lengkap} (Tenor: {$pinjaman->tenor_bulan} Bln)",
                'user_id' => $userId,
            ]);

            // Jika ada biaya administrasi, catat sebagai Pemasukan
            if ($pinjaman->biaya_administrasi > 0) {
                $this->transaksiService->createTransaksi([
                    'tahun_pelajaran_id' => $pinjaman->tahun_pelajaran_id,
                    'akun_keuangan_id' => $akunId,
                    'bank_id' => $bankId,
                    'jenis_transaksi' => 'pemasukan',
                    'metode_pembayaran' => $metode,
                    'nominal' => $pinjaman->biaya_administrasi,
                    'tanggal_transaksi' => $tanggalPencairan,
                    'nomor_referensi' => $pinjaman->kode_pinjaman,
                    'keterangan' => "Biaya Administrasi Pinjaman [{$pinjaman->kode_pinjaman}] an. {$pinjaman->nasabah->nama_lengkap}",
                    'user_id' => $userId,
                ]);
            }

            // 2. Generate Jadwal Angsuran Bulanan
            $tglCair = Carbon::parse($tanggalPencairan);
            for ($i = 1; $i <= $pinjaman->tenor_bulan; $i++) {
                $tglJatuhTempo = $tglCair->copy()->addMonthsNoOverflow($i);

                AngsuranPinjaman::create([
                    'pinjaman_id' => $pinjaman->id,
                    'angsuran_ke' => $i,
                    'tanggal_jatuh_tempo' => $tglJatuhTempo->toDateString(),
                    'nominal_pokok' => $pinjaman->nominal_angsuran_pokok,
                    'nominal_infaq_margin' => $pinjaman->nominal_infaq_bulanan,
                    'nominal_denda' => 0,
                    'total_bayar' => $pinjaman->nominal_angsuran_total,
                    'metode_pembayaran' => $metode,
                    'akun_keuangan_id' => $akunId,
                    'bank_id' => $bankId,
                    'status' => 'belum_bayar',
                ]);
            }

            // 3. Update Status Pinjaman
            $pinjaman->status = 'dicairkan';
            $pinjaman->tanggal_pencairan = $tanggalPencairan;
            $pinjaman->tanggal_jatuh_tempo = $tglCair->copy()->addMonthsNoOverflow($pinjaman->tenor_bulan)->toDateString();
            $pinjaman->dicairkan_oleh = $userId;
            $pinjaman->akun_keuangan_id = $akunId;
            $pinjaman->bank_id = $bankId;
            $pinjaman->save();

            return $pinjaman;
        });
    }

    /**
     * Bayar Angsuran Pinjaman
     */
    public function bayarAngsuran(int $angsuranId, array $data, ?string $buktiPath = null): AngsuranPinjaman
    {
        return DB::transaction(function () use ($angsuranId, $data, $buktiPath) {
            $angsuran = AngsuranPinjaman::where('id', $angsuranId)->lockForUpdate()->firstOrFail();
            $pinjaman = Pinjaman::where('id', $angsuran->pinjaman_id)->lockForUpdate()->firstOrFail();

            if ($angsuran->status === 'lunas') {
                throw new \Exception("Angsuran ke-{$angsuran->angsuran_ke} sudah lunas.");
            }

            $userId = auth()->id() ?? 1;
            $tglBayar = $data['tanggal_bayar'] ?? now()->toDateString();
            $metode = $data['metode_pembayaran'] ?? 'tunai';
            $akunId = $data['akun_keuangan_id'] ?? $pinjaman->akun_keuangan_id;
            $bankId = $metode === 'transfer_bank' ? ($data['bank_id'] ?? $pinjaman->bank_id) : null;
            $denda = (float) ($data['nominal_denda'] ?? 0);
            $totalBayar = $angsuran->nominal_pokok + $angsuran->nominal_infaq_margin + $denda;
            $noKwitansi = sprintf('KW-ANG-%s-%04d', now()->format('Ymd'), $angsuran->id);

            // 1. Catat Pemasukan Kas Angsuran
            $this->transaksiService->createTransaksi([
                'tahun_pelajaran_id' => $pinjaman->tahun_pelajaran_id,
                'akun_keuangan_id' => $akunId,
                'bank_id' => $bankId,
                'jenis_transaksi' => 'pemasukan',
                'metode_pembayaran' => $metode,
                'nominal' => $totalBayar,
                'tanggal_transaksi' => $tglBayar,
                'nomor_referensi' => $noKwitansi,
                'keterangan' => "Penerimaan Angsuran Ke-{$angsuran->angsuran_ke}/{$pinjaman->tenor_bulan} Pinjaman [{$pinjaman->kode_pinjaman}] an. {$pinjaman->nasabah->nama_lengkap}",
                'bukti_transaksi' => $buktiPath,
                'user_id' => $userId,
            ]);

            // 2. Update Status Angsuran
            $angsuran->status = 'lunas';
            $angsuran->tanggal_bayar = $tglBayar;
            $angsuran->nominal_denda = $denda;
            $angsuran->total_bayar = $totalBayar;
            $angsuran->metode_pembayaran = $metode;
            $angsuran->akun_keuangan_id = $akunId;
            $angsuran->bank_id = $bankId;
            $angsuran->nomor_bukti_bayar = $noKwitansi;
            $angsuran->bukti_bayar = $buktiPath;
            $angsuran->catatan = $data['catatan'] ?? null;
            $angsuran->diterima_oleh = $userId;
            $angsuran->save();

            // 3. Update Status Pinjaman
            $pinjaman->total_terbayar += $totalBayar;
            $pinjaman->sisa_pinjaman = max(0, $pinjaman->sisa_pinjaman - $angsuran->nominal_pokok);

            $sisaBelumBayar = AngsuranPinjaman::where('pinjaman_id', $pinjaman->id)
                ->where('status', '!=', 'lunas')
                ->count();

            if ($sisaBelumBayar === 0 || $pinjaman->sisa_pinjaman <= 0) {
                $pinjaman->status = 'lunas';
            }
            $pinjaman->save();

            return $angsuran;
        });
    }

    /**
     * Pengembalian Jaminan Agunan kepada Nasabah
     */
    public function kembalikanJaminan(int $jaminanId, int $userId, ?string $catatan = null): JaminanPinjaman
    {
        return DB::transaction(function () use ($jaminanId, $userId, $catatan) {
            $jaminan = JaminanPinjaman::where('id', $jaminanId)->lockForUpdate()->firstOrFail();

            $jaminan->status_jaminan = 'dikembalikan';
            $jaminan->tanggal_dikembalikan = now()->toDateString();
            $jaminan->pengembali_jaminan_id = $userId;
            if ($catatan) {
                $jaminan->catatan = trim($jaminan->catatan . "\n[DIKEMBALIKAN]: " . $catatan);
            }
            $jaminan->save();

            return $jaminan;
        });
    }
}
