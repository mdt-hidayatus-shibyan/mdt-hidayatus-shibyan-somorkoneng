<?php

namespace App\Models\Koperasi;

use App\Models\Level;
use App\Models\Tingkat;
use Illuminate\Database\Eloquent\Model;

class PaketKoperasi extends Model
{
    protected $table = 'paket_koperasis';
    protected $guarded = ['id'];

    protected $casts = [
        'harga_paket' => 'float',
        'total_hpp_komponen' => 'float',
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'foto_url',
        'stok_tersedia',
    ];

    /**
     * URL Foto Paket Bundling untuk Tampilan Web & POS
     */
    public function getFotoUrlAttribute(): ?string
    {
        return $this->foto ? asset('storage/' . $this->foto) : null;
    }

    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id');
    }

    public function tingkat()
    {
        return $this->belongsTo(Tingkat::class, 'tingkat_id');
    }

    public function items()
    {
        return $this->hasMany(PaketKoperasiItem::class, 'paket_koperasi_id')->with('produk');
    }

    public function penjualanDetails()
    {
        return $this->hasMany(PenjualanDetailKoperasi::class, 'paket_koperasi_id');
    }

    /**
     * Hitung stok maksimal paket yang tersedia saat ini
     * (Berdasarkan stok terendah dari komponen-komponennya)
     */
    public function getStokTersediaAttribute(): int
    {
        $items = $this->items;
        if ($items->isEmpty()) {
            return 0;
        }

        $maxPaket = null;
        foreach ($items as $item) {
            $stokProduk = $item->produk?->stok ?? 0;
            $qtyPerPaket = max(1, $item->jumlah);
            $bisaDibuat = (int) floor($stokProduk / $qtyPerPaket);

            if ($maxPaket === null || $bisaDibuat < $maxPaket) {
                $maxPaket = $bisaDibuat;
            }
        }

        return max(0, (int) $maxPaket);
    }

    /**
     * Generator Barcode Paket
     */
    public static function generateBarcode($levelId = null): string
    {
        $prefix = "PKT" . ($levelId ? "-L" . $levelId : "");
        $random = mt_rand(1000, 9999);
        $barcode = $prefix . "-" . $random;
        while (self::where('kode_paket', $barcode)->exists()) {
            $random = mt_rand(1000, 9999);
            $barcode = $prefix . "-" . $random;
        }
        return $barcode;
    }
}
