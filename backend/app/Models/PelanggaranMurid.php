<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PelanggaranMurid extends Model
{
    protected $guarded = ['id'];

    // Relasi ke Murid
    public function murid()
    {
        return $this->belongsTo(Murid::class, 'murid_id');
    }

    // Relasi Sejarah Kelas & Tahun
    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }

    public function tahunPelajaran()
    {
        return $this->belongsTo(TahunPelajaran::class, 'tahun_pelajaran_id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    // Relasi ke Master Pelanggaran
    public function referensiPelanggaran()
    {
        return $this->belongsTo(ReferensiPelanggaran::class, 'referensi_pelanggaran_id');
    }

    // Relasi ke User yang menginput
    public function penginput()
    {
        return $this->belongsTo(User::class, 'diinput_oleh_id');
    }

    public static function getCatatanRapor($totalPelanggaran)
    {
        // Jika pelanggaran sangat banyak (Bisa disesuaikan angkanya)
        if ($totalPelanggaran > 5) {
            return 'Murid memiliki <strong class="text-rose-600">' . $totalPelanggaran . ' catatan pelanggaran</strong>. Membutuhkan pembinaan karakter yang serius dan pengawasan ekstra dari orang tua di rumah.';
        }

        // Jika ada pelanggaran biasa
        elseif ($totalPelanggaran > 0) {
            return 'Murid memiliki <strong class="text-amber-600">' . $totalPelanggaran . ' catatan pelanggaran</strong> tata tertib madrasah. Diharapkan kerja sama wali murid untuk pengawasan berkala.';
        }

        // Jika bersih dari pelanggaran (0)
        else {
            return '<strong class="text-emerald-600"><i class="bi bi-patch-check-fill mr-0.5"></i> Sangat Baik.</strong> Murid mematuhi seluruh tata tertib madrasah dan menunjukkan akhlak yang terpuji.';
        }
    }
}
