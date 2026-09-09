<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Murid;
use App\Models\Ruangan;
use App\Models\Tabungan\KategoriPenarikan;
use App\Models\Tabungan\PeriodeTabungan;
use App\Models\Tabungan\Tabungan;
use App\Models\Tabungan\TransaksiTabungan;
use App\Models\TahunPelajaran;
use App\Repositories\MuridRuanganRepository;
use App\Services\Tabungan\TabunganService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TabunganApiController extends Controller
{
    protected $tabunganService;
    protected $muridRuanganRepo;

    public function __construct(TabunganService $tabunganService, MuridRuanganRepository $muridRuanganRepo)
    {
        $this->tabunganService = $tabunganService;
        $this->muridRuanganRepo = $muridRuanganRepo;
    }

    /**
     * Dapatkan data rekening tabungan pribadi Ustadz yang sedang login
     */
    public function getRingkasanUstadz(Request $request)
    {
        $user = $request->user();
        $ustadz = $user->ustadz;

        if (!$ustadz) {
            return response()->json([
                'success' => false,
                'message' => 'Data profil ustadz tidak ditemukan.'
            ], 404);
        }

        $semuaTabungan = Tabungan::with(['periodeTabungan', 'ustadz'])
            ->where('ustadz_id', $ustadz->id)
            ->orderBy('id', 'asc')
            ->get();

        if ($semuaTabungan->isEmpty()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'has_tabungan' => false,
                    'ustadz' => [
                        'id' => $ustadz->id,
                        'nama_lengkap' => $ustadz->nama_lengkap,
                        'nigm' => $ustadz->nigm ?? $ustadz->kode_ustadz ?? '-',
                    ],
                    'daftar_rekening' => [],
                    'message' => 'Anda belum memiliki rekening tabungan ustadz aktif di madrasah.'
                ]
            ], 200);
        }

        if ($request->filled('tabungan_id')) {
            $tabungan = $semuaTabungan->firstWhere('id', $request->tabungan_id) ?? $semuaTabungan->first();
        } else {
            $tabungan = $semuaTabungan->first();
        }

        $daftarRekening = $semuaTabungan->map(function ($tab) {
            $k = $this->tabunganService->hitungPotongan($tab);
            return [
                'id' => $tab->id,
                'nomor_rekening' => $tab->nomor_rekening,
                'nama_rekening' => $tab->nama_rekening,
                'nama_nasabah' => $tab->nama_nasabah,
                'jenis_nasabah' => $tab->jenis_nasabah,
                'status' => $tab->status,
                'periode' => $tab->periodeTabungan->nama_periode ?? 'Tabungan Bebas',
                'saldo' => (int) $tab->saldo,
                'total_setor' => (int) $tab->total_setor,
                'total_tarik' => (int) $tab->total_tarik,
                'total_potongan' => (int) ($k['nominal_potongan'] ?? 0),
                'saldo_dapat_ditarik' => (int) ($k['saldo_dapat_ditarik'] ?? $tab->saldo),
            ];
        });

        $kalkulasi = $this->tabunganService->hitungPotongan($tabungan);
        $rekapMutasi = $this->tabunganService->getRekapMutasiBulanan($tabungan, $request->bulan);

        // 10 Transaksi Terkini
        $transaksiTerbaru = TransaksiTabungan::where('tabungan_id', $tabungan->id)
            ->with(['petugas', 'kategoriPenarikan'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($t) {
                return [
                    'id' => $t->id,
                    'kode_transaksi' => $t->kode_transaksi,
                    'tanggal' => Carbon::parse($t->tanggal)->format('d-m-Y'),
                    'jenis_transaksi' => $t->jenis_transaksi,
                    'nominal' => (int) $t->nominal_bersih,
                    'saldo_awal' => (int) $t->saldo_awal,
                    'saldo_akhir' => (int) $t->saldo_akhir,
                    'kategori' => $t->kategoriPenarikan->nama_kategori ?? ($t->jenis_transaksi === 'Setor' ? 'Setoran Tunai' : $t->jenis_transaksi),
                    'keterangan' => $t->keterangan ?: '-',
                    'petugas' => $t->petugas->name ?? 'Sistem',
                    'metode' => $t->metode,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'has_tabungan' => true,
                'daftar_rekening' => $daftarRekening,
                'rekening' => [
                    'id' => $tabungan->id,
                    'nomor_rekening' => $tabungan->nomor_rekening,
                    'nama_rekening' => $tabungan->nama_rekening,
                    'nama_nasabah' => $tabungan->nama_nasabah,
                    'jenis_nasabah' => $tabungan->jenis_nasabah,
                    'status' => $tabungan->status,
                    'periode' => $tabungan->periodeTabungan->nama_periode ?? 'Tabungan Bebas',
                    'saldo' => (int) $tabungan->saldo,
                    'total_setor' => (int) $tabungan->total_setor,
                    'total_tarik' => (int) $tabungan->total_tarik,
                    'total_potongan' => (int) ($kalkulasi['nominal_potongan'] ?? 0),
                    'persentase_potongan' => (float) ($kalkulasi['persentase_potongan'] ?? 0),
                    'saldo_bersih_total' => (int) ($kalkulasi['saldo_bersih_total'] ?? $tabungan->saldo),
                    'saldo_dapat_ditarik' => (int) ($kalkulasi['saldo_dapat_ditarik'] ?? $tabungan->saldo),
                ],
                'rekap_bulanan' => $rekapMutasi['rekap_bulanan'] ?? [],
                'transaksi_terbaru' => $transaksiTerbaru,
            ]
        ], 200);
    }

    /**
     * Dapatkan data rekening tabungan Kas Ruangan untuk ruangan binaan (Wali Ruangan)
     */
    public function getTabunganRuangan(Request $request)
    {
        $user = $request->user();
        $ustadz = $user->ustadz;
        $ustadzId = $ustadz->id ?? null;

        $tahunAktif = TahunPelajaran::where('is_active', true)->first();
        $tahunId = $tahunAktif->id ?? null;

        // Cari ruangan binaan ustadz
        $accessibleRuangans = Ruangan::with('level')
            ->when($tahunId, fn($q) => $q->where('tahun_pelajaran_id', $tahunId))
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
            // Fallback ruangan pertama untuk ustadz yang bukan wali ruangan / demo
            $ruangan = Ruangan::with('level')->first();
        }

        if (!$ruangan) {
            return response()->json([
                'success' => false,
                'message' => 'Data ruangan tidak ditemukan.'
            ], 404);
        }

        $ruanganList = $accessibleRuangans->map(fn($r) => [
            'id' => $r->id,
            'nama_ruangan' => $r->nama_ruangan,
            'level_nama' => $r->level->nama_level ?? '-',
        ]);

        // Cari rekening tabungan Kas Ruangan untuk ruangan ini
        $tabungan = Tabungan::with(['periodeTabungan', 'ruangan.level'])
            ->where('jenis_nasabah', 'Kas Ruangan')
            ->where('ruangan_id', $ruangan->id)
            ->first();

        if (!$tabungan) {
            return response()->json([
                'success' => true,
                'data' => [
                    'has_tabungan' => false,
                    'ruangan' => [
                        'id' => $ruangan->id,
                        'nama_ruangan' => $ruangan->nama_ruangan,
                        'level_nama' => $ruangan->level->nama_level ?? '-',
                    ],
                    'ruangan_list' => $ruanganList,
                    'rekening' => null,
                    'rekap_bulanan' => [],
                    'transaksi_terbaru' => [],
                    'message' => 'Ruangan ' . $ruangan->nama_ruangan . ' belum memiliki rekening tabungan kas ruangan aktif.'
                ]
            ], 200);
        }

        $kalkulasi = $this->tabunganService->hitungPotongan($tabungan);
        $rekapMutasi = $this->tabunganService->getRekapMutasiBulanan($tabungan, $request->bulan);

        $transaksiTerbaru = TransaksiTabungan::where('tabungan_id', $tabungan->id)
            ->with(['petugas', 'kategoriPenarikan'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($t) {
                return [
                    'id' => $t->id,
                    'kode_transaksi' => $t->kode_transaksi,
                    'tanggal' => Carbon::parse($t->tanggal)->format('d-m-Y'),
                    'jenis_transaksi' => $t->jenis_transaksi,
                    'nominal' => (int) $t->nominal_bersih,
                    'saldo_awal' => (int) $t->saldo_awal,
                    'saldo_akhir' => (int) $t->saldo_akhir,
                    'kategori' => $t->kategoriPenarikan->nama_kategori ?? ($t->jenis_transaksi === 'Setor' ? 'Setoran Tunai' : $t->jenis_transaksi),
                    'keterangan' => $t->keterangan ?: '-',
                    'petugas' => $t->petugas->name ?? 'Sistem',
                    'metode' => $t->metode,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'has_tabungan' => true,
                'ruangan' => [
                    'id' => $ruangan->id,
                    'nama_ruangan' => $ruangan->nama_ruangan,
                    'level_nama' => $ruangan->level->nama_level ?? '-',
                ],
                'ruangan_list' => $ruanganList,
                'rekening' => [
                    'id' => $tabungan->id,
                    'nomor_rekening' => $tabungan->nomor_rekening,
                    'nama_rekening' => $tabungan->nama_rekening,
                    'nama_nasabah' => $tabungan->nama_nasabah,
                    'jenis_nasabah' => $tabungan->jenis_nasabah,
                    'status' => $tabungan->status,
                    'periode' => $tabungan->periodeTabungan->nama_periode ?? 'Tabungan Bebas',
                    'saldo' => (int) $tabungan->saldo,
                    'total_setor' => (int) $tabungan->total_setor,
                    'total_tarik' => (int) $tabungan->total_tarik,
                    'total_potongan' => (int) ($kalkulasi['nominal_potongan'] ?? 0),
                    'persentase_potongan' => (float) ($kalkulasi['persentase_potongan'] ?? 0),
                    'saldo_bersih_total' => (int) ($kalkulasi['saldo_bersih_total'] ?? $tabungan->saldo),
                    'saldo_dapat_ditarik' => (int) ($kalkulasi['saldo_dapat_ditarik'] ?? $tabungan->saldo),
                ],
                'rekap_bulanan' => $rekapMutasi['rekap_bulanan'] ?? [],
                'transaksi_terbaru' => $transaksiTerbaru,
            ]
        ], 200);
    }

    /**
     * Dapatkan detail buku tabungan dan riwayat mutasi
     */
    public function getDetailTabungan($id, Request $request)
    {
        $tabungan = Tabungan::with(['murid.ruangans', 'ustadz', 'ruangan', 'periodeTabungan'])->find($id);

        if (!$tabungan) {
            return response()->json([
                'success' => false,
                'message' => 'Rekening tabungan tidak ditemukan.'
            ], 404);
        }

        $kalkulasi = $this->tabunganService->hitungPotongan($tabungan);
        $rekapMutasi = $this->tabunganService->getRekapMutasiBulanan($tabungan, $request->bulan);

        $daftarTransaksi = TransaksiTabungan::where('tabungan_id', $tabungan->id)
            ->with(['petugas', 'kategoriPenarikan'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($t) {
                return [
                    'id' => $t->id,
                    'kode_transaksi' => $t->kode_transaksi,
                    'tanggal' => Carbon::parse($t->tanggal)->format('d-m-Y'),
                    'jenis_transaksi' => $t->jenis_transaksi,
                    'nominal' => (int) $t->nominal_bersih,
                    'saldo_awal' => (int) $t->saldo_awal,
                    'saldo_akhir' => (int) $t->saldo_akhir,
                    'kategori' => $t->kategoriPenarikan->nama_kategori ?? ($t->jenis_transaksi === 'Setor' ? 'Setoran Tunai' : $t->jenis_transaksi),
                    'keterangan' => $t->keterangan ?: '-',
                    'petugas' => $t->petugas->name ?? 'Sistem',
                    'metode' => $t->metode,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'rekening' => [
                    'id' => $tabungan->id,
                    'nomor_rekening' => $tabungan->nomor_rekening,
                    'nama_rekening' => $tabungan->nama_rekening,
                    'nama_nasabah' => $tabungan->nama_nasabah,
                    'jenis_nasabah' => $tabungan->jenis_nasabah,
                    'identitas_nasabah' => $tabungan->identitas_nasabah,
                    'status' => $tabungan->status,
                    'periode' => $tabungan->periodeTabungan->nama_periode ?? 'Tabungan Bebas',
                    'saldo' => (int) $tabungan->saldo,
                    'total_setor' => (int) $tabungan->total_setor,
                    'total_tarik' => (int) $tabungan->total_tarik,
                    'total_potongan' => (int) ($kalkulasi['nominal_potongan'] ?? 0),
                    'persentase_potongan' => (float) ($kalkulasi['persentase_potongan'] ?? 0),
                    'saldo_bersih_total' => (int) ($kalkulasi['saldo_bersih_total'] ?? $tabungan->saldo),
                    'saldo_dapat_ditarik' => (int) ($kalkulasi['saldo_dapat_ditarik'] ?? $tabungan->saldo),
                ],
                'rekap_bulanan' => $rekapMutasi['rekap_bulanan'] ?? [],
                'riwayat' => $daftarTransaksi,
            ]
        ], 200);
    }

    /**
     * Setor Tunai Tabungan Santri / Nasabah oleh Ustadz
     */
    public function setorTunai(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tabungan_id' => 'required|exists:tabungans,id',
            'nominal' => 'required|numeric|min:1000',
            'tanggal' => 'nullable|date',
            'keterangan' => 'nullable|string|max:255',
        ], [
            'tabungan_id.required' => 'Rekening tabungan wajib dipilih.',
            'nominal.required' => 'Nominal setoran wajib diisi.',
            'nominal.min' => 'Nominal setoran minimal Rp 1.000.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = $request->user();
            $tanggal = $request->tanggal ?? date('Y-m-d');
            $keterangan = $request->keterangan ?: 'Setoran Tabungan via App Ustadz';

            $trx = $this->tabunganService->setorTunai(
                (int) $request->tabungan_id,
                (float) $request->nominal,
                $user->id,
                $tanggal,
                $keterangan,
                'Tunai',
                $request->ruangan_id ?? null
            );

            $tabungan = $trx->tabungan->fresh();

            return response()->json([
                'success' => true,
                'message' => 'Alhamdulillah, setoran tabungan sebesar Rp ' . number_format($trx->nominal_bersih, 0, ',', '.') . ' berhasil dicatat.',
                'data' => [
                    'transaksi' => [
                        'id' => $trx->id,
                        'kode_transaksi' => $trx->kode_transaksi,
                        'nominal' => (int) $trx->nominal_bersih,
                        'saldo_awal' => (int) $trx->saldo_awal,
                        'saldo_akhir' => (int) $trx->saldo_akhir,
                        'tanggal' => Carbon::parse($trx->tanggal)->format('d-m-Y'),
                        'keterangan' => $trx->keterangan,
                    ],
                    'tabungan' => [
                        'id' => $tabungan->id,
                        'nomor_rekening' => $tabungan->nomor_rekening,
                        'nama_nasabah' => $tabungan->nama_nasabah,
                        'saldo' => (int) $tabungan->saldo,
                        'total_setor' => (int) $tabungan->total_setor,
                    ]
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencatat setoran: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Pencarian Rekening Tabungan berdasarkan Barcode / NISM / Nama
     */
    public function cariRekening(Request $request)
    {
        $q = trim($request->barcode ?? $request->q ?? $request->nomor_rekening ?? '');

        if (!$q) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan masukkan barcode atau kata kunci pencarian.'
            ], 400);
        }

        // Cek exact match nomor_rekening
        $tabungan = Tabungan::where('nomor_rekening', $q)
            ->with(['murid.ruangans.level', 'ustadz', 'ruangan', 'periodeTabungan'])
            ->first();

        // Cek kombinasi prefix jika 1 - 4 digit
        if (!$tabungan && ctype_digit($q) && strlen($q) <= 4) {
            $padded = '1000' . str_pad($q, 3, '0', STR_PAD_LEFT);
            $tabungan = Tabungan::where('nomor_rekening', $padded)
                ->with(['murid.ruangans.level', 'ustadz', 'ruangan', 'periodeTabungan'])
                ->first();
        }

        // Cek berdasarkan NISM / NISN / NIGM / Nama
        if (!$tabungan) {
            $tabungan = Tabungan::whereHas('murid', function ($m) use ($q) {
                $m->where('nism', $q)
                    ->orWhere('nisn', $q)
                    ->orWhere('nama_lengkap', 'like', "%{$q}%");
            })->orWhereHas('ustadz', function ($u) use ($q) {
                $u->where('nigm', $q)
                    ->orWhere('kode_ustadz', $q)
                    ->orWhere('nama_lengkap', 'like', "%{$q}%");
            })->with(['murid.ruangans.level', 'ustadz', 'ruangan', 'periodeTabungan'])
                ->first();
        }

        if (!$tabungan) {
            return response()->json([
                'success' => false,
                'message' => "Rekening tabungan dengan barcode/identitas \"{$q}\" tidak ditemukan."
            ], 404);
        }

        $kalkulasi = $this->tabunganService->hitungPotongan($tabungan);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $tabungan->id,
                'nomor_rekening' => $tabungan->nomor_rekening,
                'nama_nasabah' => $tabungan->nama_nasabah,
                'jenis_nasabah' => $tabungan->jenis_nasabah,
                'identitas_nasabah' => $tabungan->identitas_nasabah,
                'status' => $tabungan->status,
                'periode' => $tabungan->periodeTabungan->nama_periode ?? 'Tabungan Bebas',
                'saldo' => (int) $tabungan->saldo,
                'total_setor' => (int) $tabungan->total_setor,
                'total_tarik' => (int) $tabungan->total_tarik,
                'total_potongan' => (int) ($kalkulasi['nominal_potongan'] ?? 0),
                'persentase_potongan' => (float) ($kalkulasi['persentase_potongan'] ?? 0),
                'saldo_bersih_total' => (int) ($kalkulasi['saldo_bersih_total'] ?? $tabungan->saldo),
                'saldo_dapat_ditarik' => (int) ($kalkulasi['saldo_dapat_ditarik'] ?? $tabungan->saldo),
            ]
        ], 200);
    }
}
