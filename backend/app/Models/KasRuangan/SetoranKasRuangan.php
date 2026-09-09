<?php

namespace App\Models\KasRuangan;

use App\Models\Ruangan;
use App\Models\Tabungan\TransaksiTabungan;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class SetoranKasRuangan extends Model
{
    protected $table = 'setoran_kas_ruangans';

    protected $fillable = [
        'ruangan_id',
        'disetor_oleh',
        'penerima_id',
        'tanggal_setor',
        'jumlah_setor',
        'keterangan',
        'status',
        'catatan_verifikasi',
        'diverifikasi_oleh',
        'diverifikasi_pada',
        'transaksi_tabungan_id',
    ];

    protected $casts = [
        'tanggal_setor' => 'date',
        'diverifikasi_pada' => 'datetime',
        'jumlah_setor' => 'integer',
    ];

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class);
    }

    // Relasi ke User (Wali Ruangan yang menyetor)
    public function penyetor()
    {
        return $this->belongsTo(User::class, 'disetor_oleh');
    }

    // Relasi ke User (Bendahara/Admin yang dituju)
    public function penerima()
    {
        return $this->belongsTo(User::class, 'penerima_id');
    }

    // Relasi ke User (Admin/Petugas Tabungan yang memverifikasi)
    public function verifikator()
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }

    // Relasi ke Transaksi Tabungan Madrasah
    public function transaksiTabungan()
    {
        return $this->belongsTo(TransaksiTabungan::class, 'transaksi_tabungan_id');
    }

    public function isMenungguVerifikasi(): bool
    {
        return $this->status === 'Menunggu Verifikasi';
    }

    public function isDiterima(): bool
    {
        return $this->status === 'Diterima';
    }

    public function isDitolak(): bool
    {
        return $this->status === 'Ditolak';
    }
}
