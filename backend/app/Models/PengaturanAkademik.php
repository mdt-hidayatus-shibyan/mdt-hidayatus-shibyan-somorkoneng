<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengaturanAkademik extends Model
{
    protected $fillable = [
        'tahun_pelajaran_id',
        'bobot_imda',
        'bobot_akhlaq',
        'bobot_presensi',
        'bobot_pelanggaran',
        'poin_alpha',
        'poin_izin',
        'poin_hadir',
        'poin_dispen'
    ];

    public function tahunPelajaran()
    {
        return $this->belongsTo(TahunPelajaran::class);
    }
}
