<?php

namespace App\Models\Koperasi;

use Illuminate\Database\Eloquent\Model;

class PenjualanDetailKoperasi extends Model
{
    protected $table = 'penjualan_detail_koperasis';
    protected $guarded = ['id'];

    protected $casts = [
        'harga_beli' => 'float',
        'harga_jual' => 'float',
        'jumlah' => 'integer',
        'diskon_item' => 'float',
        'subtotal' => 'float',
        'keuntungan' => 'float',
        'rincian_paket_json' => 'array',
    ];

    public function penjualan()
    {
        return $this->belongsTo(PenjualanKoperasi::class, 'penjualan_id');
    }

    public function produk()
    {
        return $this->belongsTo(ProdukKoperasi::class, 'produk_id');
    }

    public function paket()
    {
        return $this->belongsTo(PaketKoperasi::class, 'paket_koperasi_id');
    }
}
