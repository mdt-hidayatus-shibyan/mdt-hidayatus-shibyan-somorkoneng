<?php

namespace App\Models;

use App\Models\KasRuangan\PembayaranKasRuangan;
use Illuminate\Database\Eloquent\Model;

class Murid extends Model
{
    protected $guarded = ['id'];

    // Relasi balik ke Wali Murid
    public function waliMurid()
    {
        return $this->belongsTo(WaliMurid::class, 'wali_murid_id');
    }
    public function ruangans()
    {
        return $this->belongsToMany(Ruangan::class, 'murid_ruangans', 'murid_id', 'ruangan_id')
            ->withTimestamps();
    }
    /**
     * Relasi ke Tahun Pelajaran saat pertama masuk
     */
    public function tahunMasuk()
    {
        return $this->belongsTo(TahunPelajaran::class, 'tahun_masuk');
    }

    /**
     * Relasi ke Jenjang/Level saat pertama masuk
     */
    public function levelMasuk()
    {
        return $this->belongsTo(Level::class, 'level_masuk');
    }

    /**
     * Relasi ke Ruangan tempat belajar saat ini
     */
    public function ruanganMasuk()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_masuk');
    }

    public function pembayaranKas()
    {
        return $this->hasMany(PembayaranKasRuangan::class, 'murid_id');
    }

    public function presensiUjians()
    {
        return $this->hasMany(\App\Models\Ujian\PresensiUjian::class, 'murid_id');
    }
}
