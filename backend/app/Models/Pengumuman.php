<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    protected $table = 'pengumumans';
    protected $fillable = [
        'judul',
        'konten',
        'tipe',
        'target_audience',
        'status',
        'tanggal_mulai',
        'tanggal_selesai',
        'user_id'
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    // Relasi ke User pembuat pengumuman
    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function routeNotification()
    {
        return route('pengumuman.show', [$this->id, 'ref' => 'notification']);
    }
}
