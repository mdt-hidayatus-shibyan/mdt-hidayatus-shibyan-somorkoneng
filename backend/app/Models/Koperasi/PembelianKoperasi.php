<?php

namespace App\Models\Koperasi;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class PembelianKoperasi extends Model
{
    protected $table = 'pembelian_koperasis';
    protected $guarded = ['id'];

    protected $casts = [
        'tanggal' => 'datetime',
        'tanggal_jatuh_tempo' => 'date',
        'tanggal_pelunasan' => 'datetime',
        'total_nominal' => 'float',
        'ongkir' => 'float',
        'diskon' => 'float',
        'nominal_bayar' => 'float',
        'kembalian' => 'float',
        'sisa_hutang' => 'float',
    ];

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    public function petugasPelunasan()
    {
        return $this->belongsTo(User::class, 'petugas_pelunasan_id');
    }

    public function details()
    {
        return $this->hasMany(PembelianDetailKoperasi::class, 'pembelian_id');
    }

    public function getFotoUrlAttribute(): ?string
    {
        if (!$this->foto_faktur) {
            return null;
        }
        return asset('storage/' . $this->foto_faktur);
    }

    /**
     * Generator Nomor Faktur Pembelian Kulakan Otomatis
     * Format: KUL-YYYYMMDD-XXXX (contoh: KUL-20260909-0001)
     */
    public static function generateNomorFaktur(): string
    {
        $today = date('Ymd');
        $prefix = "KUL-{$today}-";
        $lastFaktur = self::where('nomor_faktur', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->value('nomor_faktur');

        if ($lastFaktur) {
            $lastNumber = (int) substr($lastFaktur, -4);
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        return $prefix . $nextNumber;
    }
}
