<?php

namespace App\Models\Tabungan;

use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class TransaksiTabungan extends Model
{
    protected $table = 'transaksi_tabungans';
    protected $guarded = ['id'];

    protected $casts = [
        'tanggal' => 'date',
        'nominal_kotor' => 'float',
        'persentase_potongan' => 'float',
        'nominal_potongan' => 'float',
        'nominal_bersih' => 'float',
        'saldo_awal' => 'float',
        'saldo_akhir' => 'float',
    ];

    public function tabungan()
    {
        return $this->belongsTo(Tabungan::class, 'tabungan_id');
    }

    public function pengajuan()
    {
        return $this->belongsTo(PengajuanPenarikanTabungan::class, 'pengajuan_id');
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }
}
