<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PresensiUstadz extends Model
{
    protected $guarded = ['id'];
    public function jadwalPelajaran()
    {
        return $this->belongsTo(JadwalPelajaran::class, 'jadwal_pelajaran_id');
    }

    // Relasi ke Guru Utama
    public function ustadz()
    {
        return $this->belongsTo(Ustadz::class, 'ustadz_id');
    }

    // Relasi ke Guru Pengganti (Badal)
    public function guruPengganti()
    {
        return $this->belongsTo(Ustadz::class, 'ustadz_pengganti_id');
    }

    // Relasi ke User yang menginput
    public function penginput()
    {
        return $this->belongsTo(User::class, 'diinput_oleh_id');
    }
}
