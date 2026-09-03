<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PendaftaranSpmb extends Model
{
    use HasFactory;

    protected $table = 'pendaftaran_spmbs';

    protected $guarded = ['id'];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_verifikasi' => 'datetime',
    ];

    public function tahunPelajaran()
    {
        return $this->belongsTo(TahunPelajaran::class, 'tahun_pelajaran_id');
    }

    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id');
    }

    public function waliMurid()
    {
        return $this->belongsTo(WaliMurid::class, 'wali_murid_id');
    }

    public function murid()
    {
        return $this->belongsTo(Murid::class, 'murid_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Accessor Foto URL
     */
    public function getFotoUrlAttribute()
    {
        if (!$this->foto) {
            return null;
        }

        if (str_starts_with($this->foto, 'http://') || str_starts_with($this->foto, 'https://')) {
            return $this->foto;
        }

        if (str_starts_with($this->foto, 'storage/')) {
            return asset($this->foto);
        }

        return asset('storage/' . $this->foto);
    }

    /**
     * Helper generator nomor pendaftaran otomatis
     */
    public static function generateNomorPendaftaran($tahunId = null)
    {
        $tahun = TahunPelajaran::find($tahunId) ?? TahunPelajaran::where('is_active', true)->first();
        $tahunStr = date('Y');

        if ($tahun && $tahun->nama_masehi) {
            $parts = explode('-', $tahun->nama_masehi);
            $tahunStr = trim($parts[0]);
        }

        $prefix = 'SPMB-' . $tahunStr . '-';

        $latest = self::where('nomor_pendaftaran', 'LIKE', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($latest) {
            $lastNumber = (int) substr($latest->nomor_pendaftaran, strlen($prefix));
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        return $prefix . $nextNumber;
    }
}
