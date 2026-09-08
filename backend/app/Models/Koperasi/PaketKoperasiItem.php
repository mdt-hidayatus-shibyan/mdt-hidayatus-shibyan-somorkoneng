<?php

namespace App\Models\Koperasi;

use Illuminate\Database\Eloquent\Model;

class PaketKoperasiItem extends Model
{
    protected $table = 'paket_koperasi_items';
    protected $guarded = ['id'];

    protected $casts = [
        'jumlah' => 'integer',
    ];

    public function paket()
    {
        return $this->belongsTo(PaketKoperasi::class, 'paket_koperasi_id');
    }

    public function produk()
    {
        return $this->belongsTo(ProdukKoperasi::class, 'produk_id');
    }
}
