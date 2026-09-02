<?php

namespace App\Models\KasRuangan;

use App\Models\Murid;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class PembayaranKasRuangan extends Model
{
    protected $table = 'pembayaran_kas_ruangans';

    protected $fillable = [
        'ruangan_id',
        'murid_id',
        'tanggal_bayar',
        'jumlah_bayar',
        'diinput_oleh',
        'is_disetor',
    ];

    protected $casts = [
        'tanggal_bayar' => 'date',
        'is_disetor' => 'boolean',
    ];

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class);
    }

    public function murid()
    {
        return $this->belongsTo(Murid::class, 'murid_id');
    }
    public function penginput()
    {
        return $this->belongsTo(User::class, 'diinput_oleh');
    }
}
