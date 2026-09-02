<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KasRuangan\PembayaranKasRuangan;
use App\Models\KasRuangan\PengaturanKasRuangan;
use App\Models\KasRuangan\SetoranKasRuangan;
use App\Models\Ruangan;
use App\Models\TahunPelajaran;
use App\Models\User;
use App\Repositories\MuridRuanganRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class KasRuanganController extends Controller
{
    protected $muridRuanganRepo;

    public function __construct(MuridRuanganRepository $muridRuanganRepo)
    {
        $this->muridRuanganRepo = $muridRuanganRepo;
    }

    public function getRingkasan(Request $request)
    {
        $user = $request->user();
        $ustadz = $user->ustadz;
        $ustadzId = $ustadz->id ?? null;

        $tahunAktif = TahunPelajaran::where('is_active', true)->first();

        // Cari ruangan di mana ustadz ini adalah walinya
        $accessibleRuangans = Ruangan::with('level')
            ->where('tahun_pelajaran_id', $tahunAktif->id ?? 0)
            ->where('ustadz_id', $ustadzId)
            ->get();

        if ($accessibleRuangans->isEmpty()) {
            $accessibleRuangans = Ruangan::with('level')
                ->where('ustadz_id', $ustadzId)
                ->get();
        }

        if ($request->filled('ruangan_id')) {
            $ruangan = $accessibleRuangans->firstWhere('id', $request->ruangan_id) ?? Ruangan::with('level')->find($request->ruangan_id);
        } else {
            $ruangan = $accessibleRuangans->first();
        }

        if (!$ruangan) {
            // Fallback cari ruangan pertama jika admin / test
            $ruangan = Ruangan::with('level')->first();
        }

        if (!$ruangan) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak terdaftar sebagai Wali Ruangan aktif.'
            ], 404);
        }

        $totalMurid = $this->muridRuanganRepo->getMuridByRuanganAndTahun($ruangan->id, $tahunAktif->id ?? $ruangan->tahun_pelajaran_id, 'Aktif')->count();
        $totalTerkumpul = PembayaranKasRuangan::where('ruangan_id', $ruangan->id)->sum('jumlah_bayar');
        $totalSudahDisetor = SetoranKasRuangan::where('ruangan_id', $ruangan->id)->sum('jumlah_setor');
        $sisaDiTanganWali = max(0, $totalTerkumpul - $totalSudahDisetor);

        $ruanganList = $accessibleRuangans->map(fn($r) => [
            'id' => $r->id,
            'nama_ruangan' => $r->nama_ruangan,
            'level_nama' => $r->level->nama_level ?? '-',
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'ruangan_id' => $ruangan->id,
                'nama_ruangan' => $ruangan->nama_ruangan,
                'level_nama' => $ruangan->level->nama_level ?? '-',
                'total_murid' => $totalMurid,
                'total_terkumpul' => (int) $totalTerkumpul,
                'total_sudah_disetor' => (int) $totalSudahDisetor,
                'sisa_di_tangan_wali' => (int) $sisaDiTanganWali,
                'ruangan_list' => $ruanganList,
            ]
        ], 200);
    }

    public function getMuridList(Request $request)
    {
        $ruanganId = $request->ruangan_id;
        $ruangan = Ruangan::findOrFail($ruanganId);

        $tahunAktif = TahunPelajaran::where('is_active', true)->first();
        $tahunId = $tahunAktif->id ?? $ruangan->tahun_pelajaran_id;

        $pengaturan = PengaturanKasRuangan::where('ruangan_id', $ruangan->id)->first();
        $targetLaki = $pengaturan->nominal_laki ?? 50000;
        $targetPerempuan = $pengaturan->nominal_perempuan ?? 50000;

        $murids = $this->muridRuanganRepo->getMuridByRuanganAndTahun($ruangan->id, $tahunId, 'Aktif');

        $bayarData = PembayaranKasRuangan::where('ruangan_id', $ruangan->id)
            ->select('murid_id', DB::raw('SUM(jumlah_bayar) as total_bayar'))
            ->groupBy('murid_id')
            ->pluck('total_bayar', 'murid_id');

        $data = $murids->map(function ($m) use ($targetLaki, $targetPerempuan, $bayarData) {
            $target = ($m->jenis_kelamin === 'P') ? $targetPerempuan : $targetLaki;
            $dibayar = (int) ($bayarData[$m->id] ?? 0);
            $status = ($dibayar >= $target && $target > 0) ? 'Lunas' : 'Belum Lunas';

            return [
                'murid_id' => $m->id,
                'nama' => $m->nama_lengkap ?? $m->nama,
                'nism' => $m->nism ?? '-',
                'jenis_kelamin' => $m->jenis_kelamin ?? 'L',
                'foto' => $m->foto ? asset('storage/' . $m->foto) : null,
                'target_kas' => (int) $target,
                'total_dibayar' => $dibayar,
                'sisa_tunggakan' => max(0, $target - $dibayar),
                'status' => $status,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ], 200);
    }

    public function simpanBayar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ruangan_id' => 'required|exists:ruangans,id',
            'murid_id' => 'required|exists:murids,id',
            'jumlah_bayar' => 'required|numeric|min:1000',
            'tanggal_bayar' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Input pembayaran kas tidak valid.',
                'errors' => $validator->errors()
            ], 422);
        }

        $userId = $request->user()->id;

        try {
            PembayaranKasRuangan::create([
                'ruangan_id' => $request->ruangan_id,
                'murid_id' => $request->murid_id,
                'jumlah_bayar' => $request->jumlah_bayar,
                'tanggal_bayar' => $request->tanggal_bayar,
                'diinput_oleh' => $userId,
                'is_disetor' => false,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran kas santri berhasil dicatat.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencatat kas: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateBayar(Request $request, $id)
    {
        $bayar = PembayaranKasRuangan::findOrFail($id);

        if ($bayar->is_disetor) {
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran tidak dapat diedit karena sudah disetor ke Bendahara Madrasah.'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'jumlah_bayar' => 'required|numeric|min:1000',
            'tanggal_bayar' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Input edit pembayaran tidak valid.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $bayar->update([
                'jumlah_bayar' => $request->jumlah_bayar,
                'tanggal_bayar' => $request->tanggal_bayar,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Catatan pembayaran kas berhasil diperbarui.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui kas: ' . $e->getMessage()
            ], 500);
        }
    }

    public function hapusBayar(Request $request, $id)
    {
        $bayar = PembayaranKasRuangan::findOrFail($id);

        if ($bayar->is_disetor) {
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran tidak dapat dibatalkan karena sudah disetor ke Bendahara Madrasah.'
            ], 422);
        }

        $bayar->delete();

        return response()->json([
            'success' => true,
            'message' => 'Catatan pembayaran kas berhasil dibatalkan.'
        ], 200);
    }

    public function getRiwayatMurid($muridId)
    {
        $riwayat = PembayaranKasRuangan::where('murid_id', $muridId)
            ->latest('tanggal_bayar')
            ->latest('id')
            ->get();

        $data = $riwayat->map(function ($r) {
            $tgl = $r->tanggal_bayar;
            $hariTanggal = null;
            if ($tgl) {
                try {
                    $carbon = Carbon::parse($tgl)->locale('id');
                    $hariTanggal = $carbon->isoFormat('dddd, D MMMM YYYY');
                } catch (\Exception $e) {}
            }

            return [
                'id' => $r->id,
                'tanggal_bayar' => is_string($r->tanggal_bayar) ? $r->tanggal_bayar : ($r->tanggal_bayar?->format('Y-m-d') ?? date('Y-m-d')),
                'hari_tanggal' => $hariTanggal ?? ($r->tanggal_bayar ? (string)$r->tanggal_bayar : date('Y-m-d')),
                'jumlah_bayar' => (int) $r->jumlah_bayar,
                'is_disetor' => (bool) $r->is_disetor,
                'keterangan_status' => $r->is_disetor ? 'Terkunci (Sudah disetor ke Bendahara)' : 'Belum Disetor (Bisa diedit/dihapus)',
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ], 200);
    }

    // =========================================================================
    // SETORAN KAS KE BENDAHARA MADRASAH
    // =========================================================================

    public function getRiwayatSetoran(Request $request)
    {
        $user = $request->user();
        $ustadzId = $user->ustadz->id ?? null;
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();

        $ruanganId = $request->ruangan_id;
        if (!$ruanganId) {
            $ruangan = Ruangan::where('tahun_pelajaran_id', $tahunAktif->id ?? 0)
                ->where('ustadz_id', $ustadzId)
                ->first();
            $ruanganId = $ruangan->id ?? Ruangan::first()?->id;
        }

        if (!$ruanganId) {
            return response()->json([
                'success' => false,
                'message' => 'Ruangan tidak ditemukan.'
            ], 404);
        }

        $ruangan = Ruangan::with('level')->findOrFail($ruanganId);

        $setorans = SetoranKasRuangan::with(['penyetor.ustadz', 'penerima.ustadz'])
            ->where('ruangan_id', $ruanganId)
            ->latest('tanggal_setor')
            ->latest('id')
            ->get();

        $terkumpul = PembayaranKasRuangan::where('ruangan_id', $ruanganId)->sum('jumlah_bayar');
        $disetor = $setorans->sum('jumlah_setor');
        $diWali = max(0, $terkumpul - $disetor);

        $list = $setorans->map(function ($s) {
            $tgl = $s->tanggal_setor;
            $hariTanggal = null;
            if ($tgl) {
                try {
                    $carbon = Carbon::parse($tgl)->locale('id');
                    $hariTanggal = $carbon->isoFormat('dddd, D MMMM YYYY');
                } catch (\Exception $e) {}
            }

            return [
                'id' => $s->id,
                'ruangan_id' => $s->ruangan_id,
                'tanggal_setor' => is_string($s->tanggal_setor) ? $s->tanggal_setor : ($s->tanggal_setor?->format('Y-m-d') ?? date('Y-m-d')),
                'hari_tanggal' => $hariTanggal ?? ($s->tanggal_setor ? (string)$s->tanggal_setor : date('Y-m-d')),
                'jumlah_setor' => (int) $s->jumlah_setor,
                'keterangan' => $s->keterangan ?? '-',
                'penerima_id' => $s->penerima_id,
                'penerima_nama' => $s->penerima?->ustadz?->nama_lengkap ?? $s->penerima?->name ?? 'Bendahara Madrasah',
                'disetor_oleh_nama' => $s->penyetor?->ustadz?->nama_lengkap ?? $s->penyetor?->name ?? 'Wali Ruangan',
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'ruangan_id' => $ruangan->id,
                'nama_ruangan' => $ruangan->nama_ruangan,
                'level_nama' => $ruangan->level->nama_level ?? '-',
                'total_terkumpul' => (int) $terkumpul,
                'total_disetor' => (int) $disetor,
                'sisa_di_tangan_wali' => (int) $diWali,
                'list' => $list,
            ]
        ], 200);
    }

    public function getPenerimaList(Request $request)
    {
        $users = User::role(['administrator', 'staff'])->get();
        if ($users->isEmpty()) {
            $users = User::all();
        }

        $data = $users->map(fn($u) => [
            'id' => $u->id,
            'name' => $u->ustadz?->nama_lengkap ?? $u->name,
            'username' => $u->username,
            'role' => $u->getRoleNames()->first() ?? 'Staff / Bendahara',
        ]);

        return response()->json([
            'success' => true,
            'data' => $data
        ], 200);
    }

    public function simpanSetoran(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ruangan_id' => 'required|exists:ruangans,id',
            'jumlah_setor' => 'required|numeric|min:1000',
            'tanggal_setor' => 'required|date',
            'penerima_id' => 'nullable|exists:users,id',
            'keterangan' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Input setoran kas tidak valid.',
                'errors' => $validator->errors()
            ], 422);
        }

        $ruanganId = $request->ruangan_id;
        $terkumpul = PembayaranKasRuangan::where('ruangan_id', $ruanganId)->sum('jumlah_bayar');
        $disetor = SetoranKasRuangan::where('ruangan_id', $ruanganId)->sum('jumlah_setor');
        $diWali = max(0, $terkumpul - $disetor);

        if ($request->jumlah_setor > $diWali) {
            return response()->json([
                'success' => false,
                'message' => 'Jumlah setoran (' . number_format($request->jumlah_setor, 0, ',', '.') . ') melebihi sisa uang kas fisik di tangan Wali (' . number_format($diWali, 0, ',', '.') . ').'
            ], 422);
        }

        $userId = $request->user()->id;
        $penerimaId = $request->penerima_id;
        if (!$penerimaId) {
            $admin = User::role(['administrator', 'staff'])->first() ?? User::first();
            $penerimaId = $admin->id ?? $userId;
        }

        DB::beginTransaction();
        try {
            $setoran = SetoranKasRuangan::create([
                'ruangan_id' => $ruanganId,
                'disetor_oleh' => $userId,
                'penerima_id' => $penerimaId,
                'tanggal_setor' => $request->tanggal_setor,
                'jumlah_setor' => $request->jumlah_setor,
                'keterangan' => $request->keterangan,
            ]);

            $this->kunciCicilan($ruanganId, $request->jumlah_setor);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Alhamdulillah, setoran kas ke Bendahara Madrasah berhasil dicatat dan masuk ke Brankas!',
                'data' => [
                    'id' => $setoran->id,
                ]
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencatat setoran: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateSetoran(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'jumlah_setor' => 'required|numeric|min:1000',
            'tanggal_setor' => 'required|date',
            'penerima_id' => 'nullable|exists:users,id',
            'keterangan' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Input update setoran tidak valid.',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $setoran = SetoranKasRuangan::findOrFail($id);
            $ruanganId = $setoran->ruangan_id;

            // Buka kunci lama terlebih dahulu (Kembalikan status uang ke Wali Kelas)
            $this->bukaKunciCicilan($ruanganId, $setoran->jumlah_setor);

            // Cek apakah uang di Wali Kelas cukup untuk target setoran yang baru
            $uangDiWali = PembayaranKasRuangan::where('ruangan_id', $ruanganId)->where('is_disetor', false)->sum('jumlah_bayar');
            if ($request->jumlah_setor > $uangDiWali) {
                throw new \Exception('Jumlah setoran baru melebihi total uang kas yang ada di tangan Wali Kelas.');
            }

            // Kunci ulang cicilan santri berdasarkan nominal baru
            $this->kunciCicilan($ruanganId, $request->jumlah_setor);

            // Update berkas setoran
            $setoran->update([
                'tanggal_setor' => $request->tanggal_setor,
                'jumlah_setor' => $request->jumlah_setor,
                'penerima_id' => $request->penerima_id ?? $setoran->penerima_id,
                'keterangan' => $request->keterangan,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data setoran ke Bendahara berhasil diperbarui!'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui setoran: ' . $e->getMessage()
            ], 422);
        }
    }

    public function hapusSetoran(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $setoran = SetoranKasRuangan::findOrFail($id);

            // Buka kembali semua cicilan santri yang pernah dikunci oleh nominal setoran ini
            $this->bukaKunciCicilan($setoran->ruangan_id, $setoran->jumlah_setor);

            // Hapus berkas setoran
            $setoran->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data setoran berhasil dibatalkan. Status uang telah dikembalikan ke Wali Kelas!'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan setoran: ' . $e->getMessage()
            ], 500);
        }
    }

    private function kunciCicilan($ruanganId, $nominal)
    {
        $sisa = $nominal;
        $cicilans = PembayaranKasRuangan::where('ruangan_id', $ruanganId)
            ->where('is_disetor', false)
            ->orderBy('tanggal_bayar', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        foreach ($cicilans as $c) {
            if ($sisa >= $c->jumlah_bayar) {
                $c->update(['is_disetor' => true]);
                $sisa -= $c->jumlah_bayar;
            } else {
                break;
            }
        }
    }

    private function bukaKunciCicilan($ruanganId, $nominal)
    {
        $sisaRefund = $nominal;
        // Ambil data yang dikunci, urutkan dari yang PALING BARU (Reverse FIFO)
        $cicilansTerkunci = PembayaranKasRuangan::where('ruangan_id', $ruanganId)
            ->where('is_disetor', true)
            ->orderBy('tanggal_bayar', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        foreach ($cicilansTerkunci as $c) {
            if ($sisaRefund >= $c->jumlah_bayar) {
                $c->update(['is_disetor' => false]);
                $sisaRefund -= $c->jumlah_bayar;
            } else {
                break;
            }
        }
    }

    /**
     * Ambil Pengaturan Nominal Kas Ruangan Khusus Wali Ruangan
     */
    public function getPengaturan(Request $request)
    {
        $user = $request->user();
        $ustadz = $user->ustadz;
        $ustadzId = $ustadz->id ?? null;

        $tahunAktif = TahunPelajaran::where('is_active', true)->first();

        // Cari ruangan di mana ustadz ini adalah walinya
        $ruangan = Ruangan::where('tahun_pelajaran_id', $tahunAktif->id ?? 0)
            ->where('ustadz_id', $ustadzId)
            ->first();

        if (!$ruangan) {
            $ruangan = Ruangan::where('ustadz_id', $ustadzId)->first();
        }

        if (!$ruangan) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak terdaftar sebagai Wali Ruangan aktif.'
            ], 403);
        }

        $pengaturan = PengaturanKasRuangan::firstOrCreate(
            ['ruangan_id' => $ruangan->id],
            [
                'nominal_laki' => 50000,
                'nominal_perempuan' => 50000,
            ]
        );

        return response()->json([
            'success' => true,
            'data' => [
                'ruangan_id' => $ruangan->id,
                'nama_ruangan' => $ruangan->nama_ruangan,
                'nominal_laki' => (int) $pengaturan->nominal_laki,
                'nominal_perempuan' => (int) $pengaturan->nominal_perempuan,
            ]
        ], 200);
    }

    /**
     * Update Pengaturan Nominal Kas Ruangan Khusus Wali Ruangan
     */
    public function updatePengaturan(Request $request)
    {
        $user = $request->user();
        $ustadz = $user->ustadz;
        $ustadzId = $ustadz->id ?? null;

        $validator = Validator::make($request->all(), [
            'ruangan_id' => 'nullable|exists:ruangans,id',
            'nominal_laki' => 'required|integer|min:0',
            'nominal_perempuan' => 'required|integer|min:0',
        ], [
            'nominal_laki.required' => 'Nominal santri putra wajib diisi.',
            'nominal_perempuan.required' => 'Nominal santri putri wajib diisi.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $tahunAktif = TahunPelajaran::where('is_active', true)->first();

        $ruanganId = $request->ruangan_id;
        if (!$ruanganId) {
            $ruangan = Ruangan::where('tahun_pelajaran_id', $tahunAktif->id ?? 0)
                ->where('ustadz_id', $ustadzId)
                ->first();
            $ruanganId = $ruangan->id ?? null;
        }

        if (!$ruanganId) {
            return response()->json([
                'success' => false,
                'message' => 'Ruangan binaan tidak ditemukan.'
            ], 404);
        }

        $pengaturan = PengaturanKasRuangan::updateOrCreate(
            ['ruangan_id' => $ruanganId],
            [
                'nominal_laki' => $request->nominal_laki,
                'nominal_perempuan' => $request->nominal_perempuan,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan nominal kas ruangan berhasil diperbarui.',
            'data' => [
                'ruangan_id' => $pengaturan->ruangan_id,
                'nominal_laki' => (int) $pengaturan->nominal_laki,
                'nominal_perempuan' => (int) $pengaturan->nominal_perempuan,
            ]
        ], 200);
    }
}
