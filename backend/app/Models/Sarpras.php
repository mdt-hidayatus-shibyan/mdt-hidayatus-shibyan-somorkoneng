<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sarpras extends Model
{
    use HasFactory;

    protected $table = 'sarpras';

    protected $guarded = ['id'];

    protected $casts = [
        'tanggal_pengadaan' => 'date',
        'jumlah'            => 'integer',
        'is_active'         => 'boolean',
    ];

    public function gedung()
    {
        return $this->belongsTo(Gedung::class, 'gedung_id');
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeTersedia($query)
    {
        return $query->where('kondisi', 'tersedia');
    }

    public function scopeRusak($query)
    {
        return $query->whereIn('kondisi', ['rusak', 'rusak_ringan', 'rusak_berat']);
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($q, $search) {
            $q->where(function ($sub) use ($search) {
                $sub->where('nama_sarpras', 'like', '%' . $search . '%')
                    ->orWhere('kode_sarpras', 'like', '%' . $search . '%')
                    ->orWhere('kategori', 'like', '%' . $search . '%')
                    ->orWhere('sumber_dana', 'like', '%' . $search . '%');
            });
        })
            ->when($filters['gedung_id'] ?? null, function ($q, $gedungId) {
                $q->where('gedung_id', $gedungId);
            })
            ->when($filters['ruangan_id'] ?? null, function ($q, $ruanganId) {
                $q->where('ruangan_id', $ruanganId);
            })
            ->when($filters['kategori'] ?? null, function ($q, $kategori) {
                $q->where('kategori', $kategori);
            })
            ->when($filters['kondisi'] ?? null, function ($q, $kondisi) {
                $q->where('kondisi', $kondisi);
            });
    }
}
