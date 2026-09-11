<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gedung extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'jumlah_lantai' => 'integer',
        'is_active'     => 'boolean',
    ];

    public function ruangans()
    {
        return $this->hasMany(Ruangan::class, 'gedung_id');
    }

    public function sarpras()
    {
        return $this->hasMany(Sarpras::class, 'gedung_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
