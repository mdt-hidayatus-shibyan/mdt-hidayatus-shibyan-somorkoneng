<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    protected $fillable = [
        'tahun_pelajaran_id',
        'nama_semester',
        'tanggal_mulai',    // <-- Tambahkan ini
        'tanggal_selesai',  // <-- Tambahkan ini
        'is_active',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function tahunPelajaran()
    {
        return $this->belongsTo(TahunPelajaran::class);
    }

    public function bulanHijriyahs()
    {
        // Urutkan otomatis berdasarkan kolom 'urutan'
        return $this->hasMany(BulanHijriyah::class)->orderBy('urutan');
    }
}
