<?php

namespace App\Models\Koperasi;

use Illuminate\Database\Eloquent\Model;

class PembelianDetailKoperasi extends Model
{
    protected $table = 'pembelian_detail_koperasis';
    protected $guarded = ['id'];

    protected $casts = [
        'harga_beli_satuan' => 'float',
        'harga_jual_satuan' => 'float',
        'subtotal' => 'float',
    ];

    public function pembelian()
    {
        return $this->belongsTo(PembelianKoperasi::class, 'pembelian_id');
    }

    public function produk()
    {
        return $this->belongsTo(ProdukKoperasi::class, 'produk_id');
    }
}