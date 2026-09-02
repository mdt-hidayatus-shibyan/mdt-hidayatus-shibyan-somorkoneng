<?php

namespace App\Services;

use App\Models\BulanHijriyah;
use App\Models\Murid;
use App\Models\PelanggaranMurid;
use App\Models\Ruangan;
use App\Models\Semester;
use App\Repositories\MuridRuanganRepository;
use Illuminate\Support\Facades\DB;

class PelanggaranMuridService
{
    protected $muridRuanganRepo;

    public function __construct(MuridRuanganRepository $muridRuanganRepo)
    {
        $this->muridRuanganRepo = $muridRuanganRepo;
    }

    /**
     * Hitung Rekapitulasi Pelanggaran Santri
     */
    public function hitungRekapPelanggaran($ruanganId, $semesterId, $bulanId = null)
    {
        $ruanganTerpilih = Ruangan::find($ruanganId);
        $semesterTerpilih = Semester::find($semesterId);

        if (!$ruanganTerpilih || !$semesterTerpilih) {
            return [
                'rekap'           => [],
                'ruanganTerpilih' => $ruanganTerpilih,
                'bulanTerpilih'   => null,
            ];
        }

        $murids = $this->muridRuanganRepo->getMuridByRuanganAndTahun($ruanganId, $semesterTerpilih->tahun_pelajaran_id);

        $rekap = [];
        foreach ($murids as $m) {
            $rekap[$m->id] = [
                'nism'        => $m->nism,
                'nama'        => $m->nama_lengkap,
                'total_kasus' => 0,
                'total_poin'  => 0
            ];
        }

        $query = PelanggaranMurid::with('referensiPelanggaran')
            ->where('ruangan_id', $ruanganId)
            ->where('semester_id', $semesterId);

        $bulanTerpilih = null;
        if ($bulanId) {
            $bulanTerpilih = BulanHijriyah::find($bulanId);
            if ($bulanTerpilih) {
                $query->whereBetween('tanggal', [$bulanTerpilih->tanggal_mulai_masehi, $bulanTerpilih->tanggal_selesai_masehi]);
            }
        }

        $pelanggarans = $query->get();
        foreach ($pelanggarans as $p) {
            if (isset($rekap[$p->murid_id])) {
                $rekap[$p->murid_id]['total_kasus']++;
                $rekap[$p->murid_id]['total_poin'] += $p->referensiPelanggaran->poin ?? 0;
            }
        }

        return [
            'rekap'           => $rekap,
            'ruanganTerpilih' => $ruanganTerpilih,
            'bulanTerpilih'   => $bulanTerpilih,
        ];
    }

    /**
     * Sinkronisasi massal data pelanggaran (Admin Mode / Import)
     */
    public function syncAdminMode(array $rows, $ruanganId, $tahunPelajaranId, $semesterId, $userId)
    {
        return DB::transaction(function () use ($rows, $ruanganId, $tahunPelajaranId, $semesterId, $userId) {
            $berhasil = 0;

            foreach ($rows as $row) {
                if (empty($row['nism']) || empty($row['referensi_pelanggaran_id'])) continue;

                $murid = Murid::where('nism', $row['nism'])->first();
                if (!$murid) continue;

                if (!empty($row['id'])) {
                    PelanggaranMurid::where('id', $row['id'])->update([
                        'tanggal'                  => $row['tanggal'] ?? date('Y-m-d'),
                        'referensi_pelanggaran_id' => $row['referensi_pelanggaran_id'],
                        'keterangan'               => $row['keterangan'] ?? null,
                    ]);
                } else {
                    PelanggaranMurid::create([
                        'tanggal'                  => $row['tanggal'] ?? date('Y-m-d'),
                        'ruangan_id'               => $ruanganId,
                        'tahun_pelajaran_id'       => $tahunPelajaranId,
                        'semester_id'              => $semesterId,
                        'murid_id'                 => $murid->id,
                        'referensi_pelanggaran_id' => $row['referensi_pelanggaran_id'],
                        'keterangan'               => $row['keterangan'] ?? null,
                        'diinput_oleh_id'          => $userId,
                    ]);
                }
                $berhasil++;
            }

            return $berhasil;
        });
    }
}
