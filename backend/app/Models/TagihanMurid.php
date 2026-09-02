<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TagihanMurid extends Model
{
    protected $table = 'tagihan_murids';

    protected $fillable = [
        'murid_id',
        'ruangan_id',
        'pengaturan_tagihan_id',
        'bulan_hijriyah_id', // <-- BARU: Ditambahkan agar bisa diisi
        'semester_id',
        'nama_tagihan_spesifik',
        'nominal_tagihan',
        'status_bayar'
    ];

    /**
     * Relasi: Tagihan ini ditujukan untuk satu Murid
     */
    public function murid()
    {
        return $this->belongsTo(Murid::class, 'murid_id');
    }

    /**
     * Relasi: Tagihan ini diterbitkan di Ruangan/Kelas tertentu
     */
    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }

    /**
     * Relasi: Tagihan ini merujuk pada Master Jenis Tagihan apa?
     */
    public function pengaturanTagihan()
    {
        return $this->belongsTo(PengaturanTagihan::class, 'pengaturan_tagihan_id');
    }

    public function pembayaranTagihan()
    {
        return $this->belongsTo(PembayaranTagihan::class, 'pembayaran_tagihan_id');
    }
    public function bulanHijriyah()
    {
        return $this->belongsTo(BulanHijriyah::class, 'bulan_hijriyah_id');
    }

    /**
     * Relasi BARU: Tagihan semester terikat ke Semester tertentu
     */
    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }
}
