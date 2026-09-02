<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BulanHijriyah extends Model
{
    protected $guarded = ['id'];

    public function tahunPelajaran()
    {
        return $this->belongsTo(TahunPelajaran::class, 'tahun_pelajaran_id');
    }
}
