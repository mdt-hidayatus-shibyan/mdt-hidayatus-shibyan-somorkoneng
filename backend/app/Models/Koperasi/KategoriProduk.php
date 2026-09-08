<?php

namespace App\Models\Koperasi;

use Illuminate\Database\Eloquent\Model;

class KategoriProduk extends Model
{
    protected $table = 'kategori_produks';
    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function produks()
    {
        return $this->hasMany(ProdukKoperasi::class, 'kategori_id');
    }
}
