<?php

namespace App\Models\Koperasi;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class MutasiStokKoperasi extends Model
{
    protected $table = 'mutasi_stok_koperasis';
    protected $guarded = ['id'];

    protected $casts = [
        'jumlah' => 'integer',
        'stok_sebelum' => 'integer',
        'stok_sesudah' => 'integer',
    ];

    public function produk()
    {
        return $this->belongsTo(ProdukKoperasi::class, 'produk_id');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }
}
