<?php

namespace App\Models\Arsip;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ArsipDokumen extends Model
{
    use HasUuids; // Aktifkan UUID

    protected $table = 'arsip_dokumens';

    protected $fillable = [
        'tipe_dokumen',
        'referensi_tipe',
        'referensi_id',
        'file_path',
        'snapshot_data',
        'dicetak_oleh',
    ];

    // Beritahu Laravel agar kolom JSON otomatis diubah jadi Array saat dipanggil
    protected $casts = [
        'snapshot_data' => 'array',
    ];

    // Relasi Polymorphic untuk mengambil data induknya
    public function referensi()
    {
        return $this->morphTo();
    }

    // Relasi ke tabel User (Siapa yang mencetak)
    public function pencetak()
    {
        return $this->belongsTo(User::class, 'dicetak_oleh');
    }
}
