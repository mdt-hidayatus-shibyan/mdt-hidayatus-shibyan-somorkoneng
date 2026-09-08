<?php

namespace App\Models\Koperasi;

use App\Models\Murid;
use App\Models\Tabungan\Tabungan;
use App\Models\User;
use App\Models\Ustadz;
use Illuminate\Database\Eloquent\Model;

class PenjualanKoperasi extends Model
{
    protected $table = 'penjualan_koperasis';
    protected $guarded = ['id'];

    protected $casts = [
        'tanggal' => 'datetime',
        'tanggal_pelunasan' => 'datetime',
        'total_hpp' => 'float',
        'subtotal' => 'float',
        'diskon' => 'float',
        'total_akhir' => 'float',
        'nominal_bayar' => 'float',
        'kembalian' => 'float',
    ];

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    public function petugasPelunasan()
    {
        return $this->belongsTo(User::class, 'petugas_pelunasan_id');
    }

    public function murid()
    {
        return $this->belongsTo(Murid::class, 'murid_id');
    }

    public function ustadz()
    {
        return $this->belongsTo(Ustadz::class, 'ustadz_id');
    }

    public function tabungan()
    {
        return $this->belongsTo(Tabungan::class, 'tabungan_id');
    }

    public function details()
    {
        return $this->hasMany(PenjualanDetailKoperasi::class, 'penjualan_id');
    }

    /**
     * Nama identitas pelanggan
     */
    public function getNamaPelangganAttribute(): string
    {
        return match ($this->jenis_pelanggan) {
            'Murid' => $this->murid->nama_lengkap ?? $this->murid->nama ?? 'Murid Tidak Dikenal',
            'Ustadz' => $this->ustadz->nama_lengkap ?? 'Ustadz Tidak Dikenal',
            'Umum' => $this->nama_pelanggan_umum ?: 'Umum / Tamu',
            default => 'Pelanggan Umum',
        };
    }

    /**
     * Hitung total laba kotor transaksi ini
     */
    public function getLabaKotorAttribute(): float
    {
        return max(0, (float) $this->total_akhir - (float) $this->total_hpp);
    }

    /**
     * Generator Nomor Nota Kasir Otomatis
     * Format: KOP-YYYYMMDD-XXXX (contoh: KOP-20260908-0001)
     */
    public static function generateNomorNota(): string
    {
        $today = date('Ymd');
        $prefix = "KOP-{$today}-";
        $lastNota = self::where('nomor_nota', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->value('nomor_nota');

        if ($lastNota) {
            $lastNumber = (int) substr($lastNota, -4);
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        return $prefix . $nextNumber;
    }
}
