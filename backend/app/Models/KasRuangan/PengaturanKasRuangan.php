<?php

namespace App\Models\KasRuangan;

use App\Models\Ruangan;
use Illuminate\Database\Eloquent\Model;

class PengaturanKasRuangan extends Model
{
    protected $table = 'pengaturan_kas_ruangans';

    protected $fillable = [
        'ruangan_id',
        'nominal_laki',
        'nominal_perempuan',
    ];

    // Relasi balik ke Ruangan
    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class);
    }
}
