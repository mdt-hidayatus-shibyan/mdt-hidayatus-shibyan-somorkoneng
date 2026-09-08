<?php

namespace App\Models\Tabungan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriPenarikan extends Model
{
    use HasFactory;

    protected $table = 'kategori_penarikans';

    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
        'urutan' => 'integer',
    ];

    /**
     * Relasi ke transaksi tabungan yang menggunakan kategori ini
     */
    public function transaksis()
    {
        return $this->hasMany(TransaksiTabungan::class, 'kategori_penarikan_id');
    }

    /**
     * Scope kategori yang aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope urutan tampilan
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('urutan', 'asc')->orderBy('id', 'asc');
    }
}
