<?php

namespace App\Models\Kepengurusan;

use Illuminate\Database\Eloquent\Model;

class JabatanPengurus extends Model
{
    protected $table = 'jabatan_pengurus';

    protected $fillable = [
        'nama_jabatan',
        'level',
    ];

    // Relasi ke tabel pengurus
    public function pengurus()
    {
        return $this->hasMany(Pengurus::class, 'jabatan_id');
    }
}
