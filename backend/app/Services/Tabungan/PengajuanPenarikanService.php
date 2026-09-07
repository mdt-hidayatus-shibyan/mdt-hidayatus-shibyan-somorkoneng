<?php

namespace App\Services\Tabungan;

use App\Models\Tabungan\PengajuanPenarikanTabungan;
use App\Models\Tabungan\Tabungan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PengajuanPenarikanService
{
    protected $tabunganService;

    public function __construct(TabunganService $tabunganService)
    {
        $this->tabunganService = $tabunganService;
    }

    /**
     * Membuat pengajuan permohonan penarikan dana baru
     */
    public function buatPengajuan(int $tabunganId, float $nominalPengajuan, ?string $alasan, ?int $diajukanOleh = null, ?string $tanggal = null)
    {
        if ($nominalPengajuan <= 0) {
            throw new \InvalidArgumentException('Nominal pengajuan harus lebih besar dari 0.');
        }

        $tabungan = Tabungan::findOrFail($tabunganId);

        if ($tabungan->saldo < $nominalPengajuan) {
            throw new \Exception("Saldo tidak mencukupi untuk pengajuan ini. Saldo saat ini: Rp " . number_format($tabungan->saldo, 0, ',', '.'));
        }

        $kalkulasi = $this->tabunganService->hitungPotongan($tabungan, $nominalPengajuan);

        $kodePengajuan = 'REQ-TARIK-' . date('Ymd') . '-' . strtoupper(Str::random(5));

        return PengajuanPenarikanTabungan::create([
            'kode_pengajuan' => $kodePengajuan,
            'tabungan_id' => $tabungan->id,
            'nominal_pengajuan' => $nominalPengajuan,
            'alasan_penarikan' => $alasan,
            'tanggal_pengajuan' => $tanggal ?? date('Y-m-d'),
            'status' => 'Menunggu',
            'diajukan_oleh' => $diajukanOleh,
            'persentase_potongan' => $kalkulasi['persentase'],
            'nominal_potongan' => $kalkulasi['potongan'],
            'nominal_bersih' => $kalkulasi['nominal_bersih'],
        ]);
    }

    /**
     * Menyetujui pengajuan penarikan
     */
    public function setujuiPengajuan(int $pengajuanId, ?int $adminId = null, ?string $catatanAdmin = null)
    {
        return DB::transaction(function () use ($pengajuanId, $adminId, $catatanAdmin) {
            $pengajuan = PengajuanPenarikanTabungan::where('id', $pengajuanId)->lockForUpdate()->firstOrFail();

            if ($pengajuan->status !== 'Menunggu') {
                throw new \Exception("Pengajuan ini sudah berstatus {$pengajuan->status}.");
            }

            $pengajuan->update([
                'status' => 'Disetujui',
                'disetujui_oleh' => $adminId,
                'tanggal_disetujui' => now(),
                'catatan_admin' => $catatanAdmin,
            ]);

            return $pengajuan;
        });
    }

    /**
     * Menolak pengajuan penarikan dana
     */
    public function tolakPengajuan(int $pengajuanId, ?int $adminId = null, string $alasanPenolakan = '')
    {
        return DB::transaction(function () use ($pengajuanId, $adminId, $alasanPenolakan) {
            $pengajuan = PengajuanPenarikanTabungan::where('id', $pengajuanId)->lockForUpdate()->firstOrFail();

            if ($pengajuan->status === 'Dicairkan') {
                throw new \Exception("Pengajuan yang sudah dicairkan tidak dapat ditolak.");
            }

            $pengajuan->update([
                'status' => 'Ditolak',
                'disetujui_oleh' => $adminId,
                'tanggal_disetujui' => now(),
                'catatan_admin' => $alasanPenolakan,
            ]);

            return $pengajuan;
        });
    }

    /**
     * Eksekusi pencairan dana dari pengajuan yang disetujui
     */
    public function cairkanPengajuan(int $pengajuanId, ?int $adminId = null, string $metode = 'Tunai', ?string $catatan = null)
    {
        return DB::transaction(function () use ($pengajuanId, $adminId, $metode, $catatan) {
            $pengajuan = PengajuanPenarikanTabungan::where('id', $pengajuanId)->lockForUpdate()->firstOrFail();

            if ($pengajuan->status === 'Dicairkan') {
                throw new \Exception("Pengajuan ini sudah pernah dicairkan.");
            }

            if ($pengajuan->status === 'Ditolak') {
                throw new \Exception("Pengajuan berstatus Ditolak tidak dapat dicairkan.");
            }

            $tabungan = $pengajuan->tabungan;

            // Eksekusi mutasi penarikan
            $trx = $this->tabunganService->tarikTunai(
                $tabungan->id,
                (float) $pengajuan->nominal_pengajuan,
                $adminId,
                date('Y-m-d'),
                $catatan ?? ($pengajuan->alasan_penarikan ? "Pencairan pengajuan: {$pengajuan->alasan_penarikan}" : "Pencairan pengajuan {$pengajuan->kode_pengajuan}"),
                $metode,
                $pengajuan->id
            );

            $pengajuan->update([
                'status' => 'Dicairkan',
                'disetujui_oleh' => $adminId,
                'tanggal_disetujui' => $pengajuan->tanggal_disetujui ?? now(),
                'transaksi_tabungan_id' => $trx->id,
            ]);

            return $trx;
        });
    }
}
