<?php

namespace App\Models\Ujian;

use App\Models\Level;
use App\Models\Murid;
use App\Models\Ruangan;
use Illuminate\Database\Eloquent\Model;

class RiwayatKenaikan extends Model
{
    protected $fillable = [
        'tahun_pelajaran_id',
        'ruangan_asal_id',
        'level_tujuan_id',
        'murid_id',
        'no_sk',
        'nilai_akumulasi',
        'status_keputusan',
        'catatan_wali_kelas',
        'diputuskan_oleh'
    ];

    public function ruanganAsal()
    {
        // Hubungkan ke model Ruangan menggunakan foreign key 'ruangan_asal_id'
        return $this->belongsTo(Ruangan::class, 'ruangan_asal_id');
    }
    public function levelTujuan()
    {
        // Hubungkan ke model Ruangan menggunakan foreign key 'ruangan_asal_id'
        return $this->belongsTo(Level::class, 'level_tujuan_id');
    }
    public function murid()
    {
        // Hubungkan ke model Ruangan menggunakan foreign key 'ruangan_asal_id'
        return $this->belongsTo(Murid::class, 'murid_id');
    }
}
