<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AkunKeuangan extends Model
{
    use HasFactory;

    protected $table = 'akun_keuangans';

    protected $fillable = [
        'kode_akun',
        'nama_akun',
        'tipe_akun',
        'saldo_awal',
        'saldo_berjalan',
        'deskripsi',
        'is_active',
    ];

    protected $casts = [
        'saldo_awal' => 'decimal:2',
        'saldo_berjalan' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function transaksi(): HasMany
    {
        return $this->hasMany(TransaksiKeuangan::class, 'akun_keuangan_id');
    }

    public function pinjamans(): HasMany
    {
        return $this->hasMany(Pinjaman::class, 'akun_keuangan_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
