<?php

namespace App\Models\Koperasi;

use Illuminate\Database\Eloquent\Model;

class ProdukKoperasi extends Model
{
    protected $table = 'produk_koperasis';
    protected $guarded = ['id'];

    protected $casts = [
        'harga_beli' => 'float',
        'harga_jual' => 'float',
        'stok' => 'integer',
        'stok_minimum' => 'integer',
    ];

    protected $appends = [
        'foto_url',
        'is_stok_menipis',
        'margin_laba',
    ];

    /**
     * URL Foto Produk untuk Tampilan Web & POS
     */
    public function getFotoUrlAttribute(): ?string
    {
        return $this->foto ? asset('storage/' . $this->foto) : null;
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriProduk::class, 'kategori_id');
    }

    public function mutasis()
    {
        return $this->hasMany(MutasiStokKoperasi::class, 'produk_id')->latest('id');
    }

    public function paketItems()
    {
        return $this->hasMany(PaketKoperasiItem::class, 'produk_id');
    }

    public function penjualanDetails()
    {
        return $this->hasMany(PenjualanDetailKoperasi::class, 'produk_id');
    }

    /**
     * Cek apakah stok menipis (di bawah stok minimum)
     */
    public function getIsStokMenipisAttribute(): bool
    {
        return $this->stok <= $this->stok_minimum;
    }

    /**
     * Hitung estimasi margin laba per pcs
     */
    public function getMarginLabaAttribute(): float
    {
        return max(0, (float) $this->harga_jual - (float) $this->harga_beli);
    }

    /**
     * Generator Barcode / SKU Produk Otomatis
     */
    public static function generateBarcode($kategoriId = 1): string
    {
        $prefix = "PRD" . str_pad($kategoriId, 2, '0', STR_PAD_LEFT);
        $random = mt_rand(100000, 999999);
        $barcode = $prefix . $random;
        while (self::where('kode_produk', $barcode)->exists()) {
            $random = mt_rand(100000, 999999);
            $barcode = $prefix . $random;
        }
        return $barcode;
    }
}
