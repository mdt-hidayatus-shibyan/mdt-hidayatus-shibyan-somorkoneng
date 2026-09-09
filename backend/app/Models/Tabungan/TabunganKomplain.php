<?php

namespace App\Models\Tabungan;

use App\Models\Murid;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class TabunganKomplain extends Model
{
    protected $table = 'tabungan_komplains';
    protected $guarded = ['id'];

    protected $casts = [
        'nominal_tercatat' => 'float',
        'nominal_klaim' => 'float',
        'selisih' => 'float',
        'diverifikasi_pada' => 'datetime',
    ];

    public function transaksiTabungan()
    {
        return $this->belongsTo(TransaksiTabungan::class, 'transaksi_tabungan_id');
    }

    public function tabungan()
    {
        return $this->belongsTo(Tabungan::class, 'tabungan_id');
    }

    public function murid()
    {
        return $this->belongsTo(Murid::class, 'murid_id');
    }

    public function wali()
    {
        return $this->belongsTo(User::class, 'wali_id');
    }

    public function diverifikasiOleh()
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }

    /**
     * Generate Kode Komplain Otomatis
     */
    public static function generateKodeKomplain(): string
    {
        $prefix = 'KMP-' . date('Ymd') . '-';
        $countToday = self::whereDate('created_at', date('Y-m-d'))->count() + 1;
        return $prefix . str_pad($countToday, 4, '0', STR_PAD_LEFT);
    }
}
