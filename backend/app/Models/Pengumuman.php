<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    protected $table = 'pengumumans';
    protected $fillable = [
        'judul',
        'konten',
        'lampiran_pdf',
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

    /**
     * Accessor URL lengkap lampiran PDF
     */
    public function getLampiranPdfUrlAttribute()
    {
        if (!$this->lampiran_pdf) {
            return null;
        }

        if (str_starts_with($this->lampiran_pdf, 'http://') || str_starts_with($this->lampiran_pdf, 'https://')) {
            return $this->lampiran_pdf;
        }

        return asset('storage/' . $this->lampiran_pdf);
    }

    /**
     * Accessor nama file asli / basename PDF
     */
    public function getNamaFilePdfAttribute()
    {
        if (!$this->lampiran_pdf) {
            return null;
        }

        return basename($this->lampiran_pdf);
    }
}
