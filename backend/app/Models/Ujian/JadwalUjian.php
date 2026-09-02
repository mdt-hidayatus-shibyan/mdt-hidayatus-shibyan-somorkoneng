<?php

namespace App\Models\Ujian;

use App\Models\Level;
use App\Models\MataPelajaran;
use App\Models\Ustadz;
use Illuminate\Database\Eloquent\Model;

class JadwalUjian extends Model
{
    protected $table = 'jadwal_ujians';

    protected $fillable = [
        'ujian_id',
        'mata_pelajaran_id',
        'nama_mata_pelajaran_custom',
        'tanggal_ujian',
        'waktu_mulai',
        'waktu_selesai',
        'level_id',
        'ustadz_id',
    ];

    protected $casts = [
        'tanggal_ujian' => 'date',
    ];

    // Relasi
    public function ujian()
    {
        return $this->belongsTo(Ujian::class);
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function getNamaMapelAttribute()
    {
        if ($this->mata_pelajaran_id && $this->mataPelajaran) {
            return $this->mataPelajaran->nama_mapel;
        }
        return $this->nama_mata_pelajaran_custom ?? '-';
    }

    public function getHariTanggalAttribute()
    {
        $raw = $this->getRawOriginal('tanggal_ujian');
        if (!$raw) return '-';
        return \Carbon\Carbon::parse($raw)->locale('id')->isoFormat('dddd, D MMMM YYYY');
    }

    public function getHariTanggalSingkatAttribute()
    {
        $raw = $this->getRawOriginal('tanggal_ujian');
        if (!$raw) return '-';
        return \Carbon\Carbon::parse($raw)->locale('id')->isoFormat('dddd, DD MMM YYYY');
    }

    public function getJamMulaiFormatAttribute()
    {
        $raw = $this->getRawOriginal('waktu_mulai');
        return $raw ? substr($raw, 0, 5) : '07:30';
    }

    public function getJamSelesaiFormatAttribute()
    {
        $raw = $this->getRawOriginal('waktu_selesai');
        return $raw ? substr($raw, 0, 5) : '09:00';
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function pengawas()
    {
        return $this->belongsTo(Ustadz::class, 'ustadz_id');
    }

    public function presensiUjians()
    {
        return $this->hasMany(PresensiUjian::class, 'jadwal_ujian_id');
    }

    public function presensiPengawas()
    {
        return $this->hasMany(PresensiPengawasUjian::class, 'jadwal_ujian_id');
    }
}
