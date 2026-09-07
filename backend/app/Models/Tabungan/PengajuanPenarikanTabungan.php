<?php

namespace App\Models\Tabungan;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class PengajuanPenarikanTabungan extends Model
{
    protected $table = 'pengajuan_penarikan_tabungans';
    protected $guarded = ['id'];

    protected $casts = [
        'tanggal_pengajuan' => 'date',
        'tanggal_disetujui' => 'datetime',
        'nominal_pengajuan' => 'float',
        'persentase_potongan' => 'float',
        'nominal_potongan' => 'float',
        'nominal_bersih' => 'float',
    ];

    public function tabungan()
    {
        return $this->belongsTo(Tabungan::class, 'tabungan_id');
    }

    public function pemohon()
    {
        return $this->belongsTo(User::class, 'diajukan_oleh');
    }

    public function verifikator()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    public function transaksi()
    {
        return $this->belongsTo(TransaksiTabungan::class, 'transaksi_tabungan_id');
    }

    public function getNominalBersihDiterimaAttribute()
    {
        return $this->nominal_bersih;
    }
}
