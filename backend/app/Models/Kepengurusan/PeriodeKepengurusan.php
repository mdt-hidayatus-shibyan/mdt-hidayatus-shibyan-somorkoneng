<?php

namespace App\Models\Kepengurusan;

use Illuminate\Database\Eloquent\Model;

class PeriodeKepengurusan extends Model
{
    protected $table = 'periode_kepengurusan';

    protected $fillable = [
        'nama_periode',
        'tanggal_mulai',
        'tanggal_selesai',
        'status_aktif',
    ];

    // Casts digunakan agar Laravel otomatis mengubah format data saat ditarik dari database
    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'status_aktif' => 'boolean',
    ];

    // Relasi ke tabel pengurus
    public function pengurus()
    {
        return $this->hasMany(Pengurus::class, 'periode_id');
    }
}
