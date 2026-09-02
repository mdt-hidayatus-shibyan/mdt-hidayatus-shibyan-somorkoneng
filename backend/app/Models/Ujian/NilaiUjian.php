<?php

namespace App\Models\Ujian;

use App\Models\MataPelajaran;
use App\Models\Murid;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class NilaiUjian extends Model
{
    protected $table = 'nilai_ujians';

    protected $fillable = [
        'ujian_id',
        'murid_id',
        'ruangan_id',
        'jadwal_ujian_id',
        'nilai',
        'diinput_oleh',
        'is_published'
    ];

    // Relasi
    public function ujian()
    {
        return $this->belongsTo(Ujian::class);
    }
    public function murid()
    {
        return $this->belongsTo(Murid::class);
    }
    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class);
    }
    public function jadwalUjian()
    {
        return $this->belongsTo(JadwalUjian::class);
    }
    public function penginput()
    {
        return $this->belongsTo(User::class, 'diinput_oleh');
    }

    public function getNamaMapelAttribute()
    {
        return $this->jadwalUjian?->nama_mapel ?? '-';
    }

    public function getPredikatHuruf()
    {
        if ($this->nilai >= 90) return 'A+';
        if ($this->nilai >= 85) return 'A';
        if ($this->nilai >= 80) return 'B+';
        if ($this->nilai >= 75) return 'B';
        if ($this->nilai >= 70) return 'C+';
        if ($this->nilai >= 65) return 'C';
        if ($this->nilai >= 60) return 'D';
        return 'E';
    }

    /**
     * Mendapatkan class warna Tailwind sesuai predikat
     */
    // public function getWarnaPredikat()
    // {
    //     if ($this->nilai >= 90) return 'text-emerald-600';
    //     if ($this->nilai >= 80) return 'text-blue-600';
    //     if ($this->nilai >= 75) return 'text-amber-500';
    //     return 'text-rose-600';
    // }

    /**
     * Mendapatkan Catatan/Deskripsi Guru yang dinamis
     */
    public function getCatatanGuru()
    {
        if ($this->nilai >= 90) {
            return 'Menunjukkan penguasaan materi yang sangat baik dalam semester ini.';
        } elseif ($this->nilai >= 80) {
            return 'Penguasaan materi baik, tingkatkan terus prestasinya.';
        } elseif ($this->nilai >= 75) {
            return 'Cukup menguasai materi, perbanyak latihan di rumah.';
        } else {
            return 'Perlu bimbingan intensif dan pengulangan materi di rumah.';
        }
    }
}
