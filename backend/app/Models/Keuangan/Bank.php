<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bank extends Model
{
    use HasFactory;

    protected $table = 'banks';

    protected $fillable = [
        'nama_bank',
        'kode_bank',
        'nomor_rekening',
        'atas_nama',
        'cabang',
        'saldo',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'saldo' => 'decimal:2',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function transaksi(): HasMany
    {
        return $this->hasMany(TransaksiKeuangan::class, 'bank_id');
    }

    public function pinjamans(): HasMany
    {
        return $this->hasMany(Pinjaman::class, 'bank_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
