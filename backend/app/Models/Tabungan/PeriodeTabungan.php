<?php

namespace App\Models\Tabungan;

use App\Models\TahunPelajaran;
use Illuminate\Database\Eloquent\Model;

class PeriodeTabungan extends Model
{
    protected $table = 'periode_tabungans';
    protected $guarded = ['id'];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_penutupan' => 'date',
        'tanggal_pembagian' => 'date',
        'is_active' => 'boolean',
    ];

    public function tahunPelajaran()
    {
        return $this->belongsTo(TahunPelajaran::class, 'tahun_pelajaran_id');
    }

    public function tabungans()
    {
        return $this->hasMany(Tabungan::class, 'periode_tabungan_id');
    }

    public function potongans()
    {
        return $this->hasMany(PengaturanPotonganTabungan::class, 'periode_tabungan_id');
    }
}
