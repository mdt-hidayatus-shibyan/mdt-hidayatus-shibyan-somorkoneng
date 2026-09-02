<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Murid;
use App\Models\PelanggaranMurid;
use App\Models\ReferensiPelanggaran;
use App\Models\Ruangan;
use App\Models\Semester;
use App\Models\TahunPelajaran;
use App\Repositories\MuridRuanganRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PelanggaranMuridController extends Controller
{
    protected $muridRuanganRepo;

    public function __construct(MuridRuanganRepository $muridRuanganRepo)
    {
        $this->muridRuanganRepo = $muridRuanganRepo;
    }

    /**
     * Helper: Ambil ID seluruh ruangan yang diampu oleh ustadz yang login
     * (Sebagai Wali Ruangan ATAU Pengajar Jadwal di ruangan tersebut)
     */
    protected function getAccessibleRuanganIds($user, $tahunId)
    {
        $ustadzId = $user->ustadz->id ?? null;
        if (!$ustadzId) {
            return [];
        }

        return Ruangan::where('tahun_pelajaran_id', $tahunId)
            ->where(function ($q) use ($ustadzId) {
                $q->where('ustadz_id', $ustadzId)
                    ->orWhereHas('jadwalPelajarans', function ($jq) use ($ustadzId) {
                        $jq->where('ustadz_id', $ustadzId);
                    });
            })
            ->pluck('id')
            ->toArray();
    }

    /**
     * Master Referensi Pelanggaran
     */
    public function getReferensi(Request $request)
    {
        $query = ReferensiPelanggaran::query();

        if ($request->filled('id')) {
            $query->where('id', $request->id);
        }

        if ($request->filled('query')) {
            $search = $request->input('query');
            $query->where(function ($q) use ($search) {
                $q->where('nama_pelanggaran', 'like', "%{$search}%")
                    ->orWhere('id', $search);
            });
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        $referensi = $query->orderBy('id', 'asc')->get();

        $data = $referensi->map(function ($r) {
            return [
                'id' => $r->id,
                'nama_pelanggaran' => $r->nama_pelanggaran,
                'kategori' => $r->kategori,
                'poin' => (float) $r->poin,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ], 200);
    }

    /**
     * Daftar Ruangan / Kelas Khusus Ustadz yang Login
     * (Sebagai Wali Ruangan atau Pengajar Jadwal di ruangan tersebut)
     */
    public function getRuanganList(Request $request)
    {
        $user = $request->user();
        $ustadzId = $user->ustadz->id ?? null;

        $tahunAktif = TahunPelajaran::where('is_active', true)->first();
        $tahunId = $tahunAktif->id ?? 0;

        $query = Ruangan::with(['level', 'waliRuangan'])
            ->where('tahun_pelajaran_id', $tahunId);

        if ($ustadzId) {
            $query->where(function ($q) use ($ustadzId) {
                // 1. Sebagai Wali Ruangan
                $q->where('ustadz_id', $ustadzId)
                    // 2. Atau mengajar jadwal pelajaran di ruangan tersebut
                    ->orWhereHas('jadwalPelajarans', function ($jq) use ($ustadzId) {
                        $jq->where('ustadz_id', $ustadzId);
                    });
            });
        }

        $ruangans = $query->orderBy('nama_ruangan', 'asc')->get();

        $data = $ruangans->map(function ($r) use ($tahunId) {
            $totalMurid = $this->muridRuanganRepo->getMuridByRuanganAndTahun($r->id, $tahunId, 'Aktif')->count();
            return [
                'id' => $r->id,
                'nama_ruangan' => $r->nama_ruangan,
                'level_nama' => $r->level->nama_level ?? '-',
                'wali_ustadz' => $r->waliRuangan->nama_lengkap ?? $r->waliRuangan->nama ?? '-',
                'total_murid' => $totalMurid,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ], 200);
    }

    /**
     * Daftar Murid Berdasarkan Ruangan
     */
    public function getMuridByRuangan(Request $request, $ruanganId)
    {
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();
        $tahunId = $tahunAktif->id ?? 0;

        $murids = $this->muridRuanganRepo->getMuridByRuanganAndTahun($ruanganId, $tahunId, 'Aktif');

        // Ambil total akumulasi poin per murid di tahun aktif
        $poinMap = PelanggaranMurid::with('referensiPelanggaran')
            ->where('tahun_pelajaran_id', $tahunId)
            ->where('ruangan_id', $ruanganId)
            ->get()
            ->groupBy('murid_id')
            ->map(function ($group) {
                return $group->sum(function ($item) {
                    return (float) ($item->referensiPelanggaran->poin ?? 0);
                });
            });

        $data = $murids->map(function ($m) use ($poinMap) {
            return [
                'id' => $m->id,
                'nama_lengkap' => $m->nama_lengkap ?? $m->nama,
                'nism' => $m->nism ?? '-',
                'jenis_kelamin' => $m->jenis_kelamin ?? 'L',
                'foto' => $m->foto ? asset('storage/' . $m->foto) : null,
                'total_poin' => round((float) ($poinMap[$m->id] ?? 0), 2),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ], 200);
    }

    /**
     * Tab Harian: Catatan Pelanggaran Hari Ini & Statistik Harian
     */
    public function getHarian(Request $request)
    {
        $user = $request->user();
        $tanggal = $request->tanggal ?? date('Y-m-d');
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();
        $tahunId = $tahunAktif->id ?? 0;

        $accessibleIds = $this->getAccessibleRuanganIds($user, $tahunId);

        $query = PelanggaranMurid::with(['murid', 'ruangan', 'referensiPelanggaran', 'penginput.ustadz'])
            ->whereDate('tanggal', $tanggal);

        if ($request->filled('ruangan_id')) {
            $query->where('ruangan_id', $request->ruangan_id);
        } elseif (!empty($accessibleIds)) {
            $query->whereIn('ruangan_id', $accessibleIds);
        }

        $records = $query->latest('id')->get();

        $totalKasus = $records->count();
        $totalPoin = $records->sum(function ($r) {
            return (float) ($r->referensiPelanggaran->poin ?? 0);
        });
        $totalSantri = $records->pluck('murid_id')->unique()->count();

        $list = $records->map(function ($r) {
            return [
                'id' => $r->id,
                'tanggal' => $r->tanggal,
                'murid_id' => $r->murid_id,
                'murid_nama' => $r->murid->nama_lengkap ?? $r->murid->nama ?? '-',
                'nism' => $r->murid->nism ?? '-',
                'ruangan_id' => $r->ruangan_id,
                'ruangan_nama' => $r->ruangan->nama_ruangan ?? '-',
                'referensi_id' => $r->referensi_pelanggaran_id,
                'pelanggaran' => $r->referensiPelanggaran->nama_pelanggaran ?? '-',
                'kategori' => $r->referensiPelanggaran->kategori ?? 'Ringan',
                'poin' => round((float) ($r->referensiPelanggaran->poin ?? 0), 2),
                'keterangan' => $r->keterangan,
                'diinput_oleh' => $r->penginput->ustadz->nama_lengkap ?? $r->penginput->name ?? 'Ustadz',
                'waktu' => $r->created_at ? $r->created_at->format('H:i') : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'tanggal' => $tanggal,
                'total_kasus' => $totalKasus,
                'total_poin' => round((float) $totalPoin, 2),
                'total_santri' => $totalSantri,
                'list' => $list,
            ]
        ], 200);
    }

    /**
     * Tab Riwayat: Seluruh Riwayat Pelanggaran dengan Filter
     */
    public function getRiwayat(Request $request)
    {
        $user = $request->user();
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();
        $tahunId = $tahunAktif->id ?? 0;

        $accessibleIds = $this->getAccessibleRuanganIds($user, $tahunId);

        $query = PelanggaranMurid::with(['murid', 'ruangan', 'referensiPelanggaran', 'penginput.ustadz']);

        if ($request->filled('ruangan_id')) {
            $query->where('ruangan_id', $request->ruangan_id);
        } elseif (!empty($accessibleIds)) {
            $query->whereIn('ruangan_id', $accessibleIds);
        }

        if ($request->filled('kategori')) {
            $query->whereHas('referensiPelanggaran', function ($q) use ($request) {
                $q->where('kategori', $request->kategori);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('murid', function ($m) use ($search) {
                    $m->where('nama_lengkap', 'like', "%{$search}%")
                        ->orWhere('nism', 'like', "%{$search}%");
                })->orWhereHas('referensiPelanggaran', function ($r) use ($search) {
                    $r->where('nama_pelanggaran', 'like', "%{$search}%");
                })->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {
            $query->whereBetween('tanggal', [$request->tanggal_mulai, $request->tanggal_selesai]);
        }

        $riwayat = $query->latest('tanggal')->latest('id')->take(100)->get();

        $data = $riwayat->map(function ($r) {
            return [
                'id' => $r->id,
                'tanggal' => $r->tanggal,
                'murid_id' => $r->murid_id,
                'murid_nama' => $r->murid->nama_lengkap ?? $r->murid->nama ?? '-',
                'nism' => $r->murid->nism ?? '-',
                'ruangan_id' => $r->ruangan_id,
                'ruangan_nama' => $r->ruangan->nama_ruangan ?? '-',
                'referensi_id' => $r->referensi_pelanggaran_id,
                'pelanggaran' => $r->referensiPelanggaran->nama_pelanggaran ?? '-',
                'kategori' => $r->referensiPelanggaran->kategori ?? 'Ringan',
                'poin' => round((float) ($r->referensiPelanggaran->poin ?? 0), 2),
                'keterangan' => $r->keterangan,
                'diinput_oleh' => $r->penginput->ustadz->nama_lengkap ?? $r->penginput->name ?? 'Ustadz',
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ], 200);
    }

    /**
     * Catat Pelanggaran Tunggal
     */
    public function simpan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal' => 'required|date',
            'ruangan_id' => 'required|exists:ruangans,id',
            'murid_id' => 'required|exists:murids,id',
            'referensi_pelanggaran_id' => 'required|exists:referensi_pelanggarans,id',
            'keterangan' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Input tidak valid.',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();
        $semesterAktif = Semester::where('is_active', true)->first();

        try {
            $record = PelanggaranMurid::create([
                'murid_id' => $request->murid_id,
                'ruangan_id' => $request->ruangan_id,
                'tahun_pelajaran_id' => $tahunAktif->id ?? 1,
                'semester_id' => $semesterAktif->id ?? null,
                'referensi_pelanggaran_id' => $request->referensi_pelanggaran_id,
                'tanggal' => $request->tanggal,
                'keterangan' => $request->keterangan,
                'diinput_oleh_id' => $user->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pelanggaran santri berhasil dicatat ke Buku Kasus.',
                'data' => [
                    'id' => $record->id,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencatat pelanggaran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Catat Pelanggaran Massal (Banyak Murid Sekaligus)
     */
    public function simpanMassal(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal' => 'required|date',
            'ruangan_id' => 'required|exists:ruangans,id',
            'murid_ids' => 'required|array|min:1',
            'murid_ids.*' => 'exists:murids,id',
            'referensi_pelanggaran_id' => 'required|exists:referensi_pelanggarans,id',
            'keterangan' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Input data massal tidak valid.',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();
        $semesterAktif = Semester::where('is_active', true)->first();

        DB::beginTransaction();
        try {
            $createdCount = 0;
            foreach ($request->murid_ids as $mId) {
                PelanggaranMurid::create([
                    'murid_id' => $mId,
                    'ruangan_id' => $request->ruangan_id,
                    'tahun_pelajaran_id' => $tahunAktif->id ?? 1,
                    'semester_id' => $semesterAktif->id ?? null,
                    'referensi_pelanggaran_id' => $request->referensi_pelanggaran_id,
                    'tanggal' => $request->tanggal,
                    'keterangan' => $request->keterangan,
                    'diinput_oleh_id' => $user->id,
                ]);
                $createdCount++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Berhasil mencatat pelanggaran untuk {$createdCount} santri.",
                'total_dicatat' => $createdCount,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencatat pelanggaran massal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hapus Catatan Pelanggaran
     */
    public function destroy(Request $request, $id)
    {
        $record = PelanggaranMurid::findOrFail($id);
        $record->delete();

        return response()->json([
            'success' => true,
            'message' => 'Catatan pelanggaran berhasil dihapus.'
        ], 200);
    }
}
