<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaporanKendala extends Model
{
    protected $table = 'laporan_kendalas';

    protected $fillable = [
        'user_id',
        'ustadz_id',
        'kategori',
        'judul',
        'deskripsi',
        'tipe_perangkat',
        'versi_aplikasi',
        'status',
        'respon_admin',
        'responded_by',
        'responded_at',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ustadz(): BelongsTo
    {
        return $this->belongsTo(Ustadz::class, 'ustadz_id');
    }

    public function responder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by');
    }
}
