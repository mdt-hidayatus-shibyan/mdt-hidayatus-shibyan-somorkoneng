<?php

namespace App\Models\Ujian;

use App\Models\Ruangan;
use App\Models\User;
use App\Models\Ustadz;
use Illuminate\Database\Eloquent\Model;

class PresensiPengawasUjian extends Model
{
    protected $table = 'presensi_pengawas_ujians';

    protected $fillable = [
        'jadwal_ujian_id',
        'ruangan_id',
        'ustadz_id',
        'ustadz_pengganti_id',
        'status',
        'catatan_berita_acara',
        'diinput_oleh',
    ];

    public function jadwalUjian()
    {
        return $this->belongsTo(JadwalUjian::class, 'jadwal_ujian_id');
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }

    public function ustadz()
    {
        return $this->belongsTo(Ustadz::class, 'ustadz_id');
    }

    public function ustadzPengganti()
    {
        return $this->belongsTo(Ustadz::class, 'ustadz_pengganti_id');
    }

    public function penginput()
    {
        return $this->belongsTo(User::class, 'diinput_oleh');
    }
}
