<?php

namespace App\Models\Tabungan;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class RiwayatBukuTabungan extends Model
{
    protected $table = 'riwayat_buku_tabungans';
    protected $guarded = ['id'];

    protected $casts = [
        'saldo_terakhir' => 'float',
    ];

    public function tabungan()
    {
        return $this->belongsTo(Tabungan::class, 'tabungan_id');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }
}
