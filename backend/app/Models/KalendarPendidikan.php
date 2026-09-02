<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KalendarPendidikan extends Model
{

    protected $fillable = ['tahun_pelajaran_id', 'nama_kegiatan', 'kategori_kegiatan_id', 'tanggal_mulai', 'tanggal_selesai'];
    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    // Relasi ke Tahun Pelajaran
    public function tahunPelajaran()
    {
        return $this->belongsTo(TahunPelajaran::class);
    }

    // Relasi ke Kategori Kegiatan
    public function kategoriKegiatan()
    {
        return $this->belongsTo(KategoriKegiatan::class);
    }
}
