<?php

namespace App\Repositories;

use App\Models\Murid;
use Illuminate\Database\Eloquent\Collection;

class MuridRuanganRepository
{
    protected $murid;

    public function __construct(Murid $murid)
    {
        $this->murid = $murid;
    }

    /**
     * Mengambil data murid berdasarkan ruangan dan tahun pelajaran, 
     * diurutkan berdasarkan jenis kelamin dan nama.
     *
     * @param int|string $ruangan_id
     * @param int|string $tahun_pelajaran_id
     * @return Collection
     */
    public function getMuridByRuanganAndTahun($ruangan_id, $tahun_pelajaran_id, $status = null): Collection
    {
        $query = $this->murid->whereHas('ruangans', function ($query) use ($ruangan_id, $tahun_pelajaran_id) {
            $query->where('ruangans.id', $ruangan_id)
                ->where('murid_ruangans.tahun_pelajaran_id', $tahun_pelajaran_id);
        });

        // Jika parameter $status diisi, tambahkan filter where
        if ($status) {
            $query->where('status', $status);
        }

        return $query->orderBy('jenis_kelamin', 'asc') // 1. Pisahkan L/P
            ->orderBy('nama_lengkap', 'asc')  // 2. Urutkan nama sesuai abjad
            ->get();
    }
}
