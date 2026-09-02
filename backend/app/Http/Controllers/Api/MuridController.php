<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ruangan;
use App\Models\TahunPelajaran;
use App\Repositories\MuridRuanganRepository;
use Illuminate\Http\Request;

class MuridController extends Controller
{
    protected $muridRuanganRepo;

    public function __construct(MuridRuanganRepository $muridRuanganRepo)
    {
        $this->muridRuanganRepo = $muridRuanganRepo;
    }

    public function getMuridRuangan(Request $request)
    {
        $user = $request->user();
        $ustadz = $user->ustadz;
        $ustadzId = $ustadz->id ?? null;

        $tahunAktif = TahunPelajaran::where('is_active', true)->first();

        $ruanganId = $request->ruangan_id;
        if (!$ruanganId && $ustadzId) {
            $rw = Ruangan::where('tahun_pelajaran_id', $tahunAktif->id ?? 0)
                ->where('ustadz_id', $ustadzId)
                ->first();
            $ruanganId = $rw->id ?? null;
        }

        if (!$ruanganId) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter ruangan_id tidak ditemukan.'
            ], 404);
        }

        $ruangan = Ruangan::findOrFail($ruanganId);
        $tahunId = $tahunAktif->id ?? $ruangan->tahun_pelajaran_id;

        $murids = $this->muridRuanganRepo->getMuridByRuanganAndTahun($ruangan->id, $tahunId);

        $data = $murids->map(function ($m) {
            $isYatim = ($m->status_ayah === 'Meninggal' || $m->status_ayah === 'Almarhum');

            return [
                'id' => $m->id,
                'nism' => $m->nism,
                'nisn' => $m->nisn,
                'nik' => $m->nik,
                'nama_lengkap' => $m->nama_lengkap ?? $m->nama,
                'nama_panggilan' => $m->nama_panggilan,
                'jenis_kelamin' => $m->jenis_kelamin,
                'tempat_lahir' => $m->tempat_lahir,
                'tanggal_lahir' => $m->tanggal_lahir ? (is_string($m->tanggal_lahir) ? $m->tanggal_lahir : $m->tanggal_lahir->format('Y-m-d')) : null,
                'foto' => $m->foto ? asset('storage/' . $m->foto) : null,
                'nama_ayah' => $m->nama_ayah,
                'status_ayah' => $m->status_ayah,
                'nama_ibu' => $m->nama_ibu,
                'status_ibu' => $m->status_ibu,
                'status' => $m->status ?? 'Aktif',
                'is_yatim' => $isYatim,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'ruangan' => $ruangan->nama_ruangan,
                'murids' => $data,
            ]
        ], 200);
    }
}
