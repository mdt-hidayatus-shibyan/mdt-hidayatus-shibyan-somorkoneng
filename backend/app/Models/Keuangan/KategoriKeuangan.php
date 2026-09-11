<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriKeuangan extends Model
{
    use HasFactory;

    protected $table = 'kategori_keuangans';

    protected $fillable = [
        'parent_id',
        'kode_kategori',
        'nama_kategori',
        'jenis',
        'deskripsi',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(KategoriKeuangan::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(KategoriKeuangan::class, 'parent_id');
    }

    public function allChildren(): HasMany
    {
        return $this->children()->with('allChildren');
    }

    public function transaksi(): HasMany
    {
        return $this->hasMany(TransaksiKeuangan::class, 'kategori_keuangan_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOnlyParents($query)
    {
        return $query->whereNull('parent_id');
    }
}
