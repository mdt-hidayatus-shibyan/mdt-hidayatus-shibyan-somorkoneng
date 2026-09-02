<?php

namespace App\Models\KasRuangan;

use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class SetoranKasRuangan extends Model
{
    protected $table = 'setoran_kas_ruangans';

    protected $fillable = [
        'ruangan_id',
        'disetor_oleh',
        'penerima_id',
        'tanggal_setor',
        'jumlah_setor',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_setor' => 'date',
    ];

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class);
    }

    // Relasi ke User (Bendahara/Admin yang menerima uang)
    public function penyetor()
    {
        return $this->belongsTo(User::class, 'disetor_oleh');
    }
    public function penerima()
    {
        return $this->belongsTo(User::class, 'penerima_id');
    }
}
