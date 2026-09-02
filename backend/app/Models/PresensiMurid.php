<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PresensiMurid extends Model
{
    protected $guarded = []; // Buka gembok agar bisa diisi massal

    public function jadwalPelajaran()
    {
        return $this->belongsTo(JadwalPelajaran::class);
    }

    public function murid()
    {
        return $this->belongsTo(Murid::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }
}
