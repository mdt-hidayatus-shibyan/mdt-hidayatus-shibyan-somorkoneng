<?php

namespace App\Services;

use App\Models\BulanHijriyah;
use App\Models\HariLibur;
use App\Models\JadwalPelajaran;
use App\Models\PresensiMurid;
use App\Models\Semester;
use App\Repositories\MuridRuanganRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PresensiMuridService
{
    protected $muridRuanganRepo;

    public function __construct(MuridRuanganRepository $muridRuanganRepo)
    {
        $this->muridRuanganRepo = $muridRuanganRepo;
    }

    /**
     * Hitung Matriks Presensi Bulanan
     */
    public function hitungMatriksBulanan($bulanId, $ruanganId, $jamKe)
    {
        $bulanTerpilih = BulanHijriyah::findOrFail($bulanId);
        $tahun_pelajaran_id = $bulanTerpilih->tahun_pelajaran_id;

        $murids = $this->muridRuanganRepo->getMuridByRuanganAndTahun($ruanganId, $tahun_pelajaran_id, 'Aktif');

        $jadwals = JadwalPelajaran::with('mataPelajaran')
            ->where('ruangan_id', $ruanganId)
            ->where('jam_ke', $jamKe)
            ->get()
            ->keyBy('hari');

        $start = Carbon::parse($bulanTerpilih->tanggal_mulai_masehi);
        $end = Carbon::parse($bulanTerpilih->tanggal_selesai_masehi);
        $jumlahHari = $start->diffInDays($end) + 1;

        $hariLiburs = HariLibur::where(function ($q) use ($bulanTerpilih) {
            $q->whereBetween('tanggal_mulai', [$bulanTerpilih->tanggal_mulai_masehi, $bulanTerpilih->tanggal_selesai_masehi])
                ->orWhereBetween('tanggal_selesai', [$bulanTerpilih->tanggal_mulai_masehi, $bulanTerpilih->tanggal_selesai_masehi])
                ->orWhere(function ($sub) use ($bulanTerpilih) {
                    $sub->where('tanggal_mulai', '<=', $bulanTerpilih->tanggal_mulai_masehi)
                        ->where('tanggal_selesai', '>=', $bulanTerpilih->tanggal_selesai_masehi);
                });
        })->get();

        $mapHari = [
            'Sunday'    => 'Ahad',
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu'
        ];

        $dates = [];
        for ($i = 0; $i < $jumlahHari; $i++) {
            $currentDate = $start->copy()->addDays($i);
            $nama_hari_inggris = $currentDate->format('l');
            $hariIndo = $mapHari[$nama_hari_inggris];
            $tglMasehi = $currentDate->format('Y-m-d');

            $isAdaJadwal = $jadwals->has($hariIndo);
            $jadwalHariIni = $isAdaJadwal ? $jadwals->get($hariIndo) : null;

            $isLiburMadrasah = false;
            $keteranganLibur = null;

            foreach ($hariLiburs as $libur) {
                $liburMulaiStr = Carbon::parse($libur->tanggal_mulai)->format('Y-m-d');
                $liburSelesaiStr = Carbon::parse($libur->tanggal_selesai)->format('Y-m-d');

                if ($tglMasehi >= $liburMulaiStr && $tglMasehi <= $liburSelesaiStr) {
                    $isLiburMadrasah = true;
                    $keteranganLibur = $libur->keterangan;
                    break;
                }
            }

            $dates[$i + 1] = [
                'masehi'            => $tglMasehi,
                'hari'              => $hariIndo,
                'is_jadwal'         => $isAdaJadwal,
                'jadwal_id'         => $jadwalHariIni ? $jadwalHariIni->id : null,
                'mapel'             => $jadwalHariIni ? $jadwalHariIni->mataPelajaran->nama_mapel : null,
                'is_libur_madrasah' => $isLiburMadrasah,
                'keterangan_libur'  => $keteranganLibur
            ];
        }

        $matrix = [];
        $jadwalIds = $jadwals->pluck('id')->toArray();

        if (!empty($jadwalIds) && $murids->isNotEmpty()) {
            $presensiDb = PresensiMurid::whereIn('murid_id', $murids->pluck('id'))
                ->whereBetween('tanggal', [$bulanTerpilih->tanggal_mulai_masehi, $bulanTerpilih->tanggal_selesai_masehi])
                ->whereIn('jadwal_pelajaran_id', $jadwalIds)
                ->get();

            foreach ($presensiDb as $p) {
                $char = match ($p->status) {
                    'Hadir'      => 'H',
                    'Sakit'      => 'S',
                    'Izin'       => 'I',
                    'Alpha'      => 'A',
                    'Dispensasi' => 'D',
                    default      => '-'
                };

                $tglStr = is_string($p->tanggal) ? $p->tanggal : Carbon::parse($p->tanggal)->format('Y-m-d');

                // Simpan dalam format tanggal masehi (YYYY-MM-DD)
                $matrix[$p->murid_id][$tglStr] = $char;

                // Simpan juga dalam format nomor hari (1..30) untuk kompatibilitas
                foreach ($dates as $tglKe => $dInfo) {
                    if ($dInfo['masehi'] === $tglStr) {
                        $matrix[$p->murid_id][$tglKe] = $char;
                    }
                }
            }
        }

        return [
            'bulanTerpilih' => $bulanTerpilih,
            'murids'        => $murids,
            'dates'         => $dates,
            'matrix'        => $matrix,
        ];
    }

    /**
     * Simpan / update massal matriks presensi bulanan
     */
    public function simpanPresensiBulanan(array $matrixInput, $bulanId, $ruanganId, $jamKe, $userId)
    {
        return DB::transaction(function () use ($matrixInput, $bulanId, $ruanganId, $jamKe, $userId) {
            $matriksData = $this->hitungMatriksBulanan($bulanId, $ruanganId, $jamKe);
            $dates = $matriksData['dates'];
            $bulanTerpilih = $matriksData['bulanTerpilih'];

            $dateByMasehi = [];
            foreach ($dates as $tglKe => $dInfo) {
                $dateByMasehi[$dInfo['masehi']] = $dInfo;
            }

            $semesterAktif = Semester::where('is_active', true)->first();
            $semesterId = $bulanTerpilih->semester_id ?? ($semesterAktif ? $semesterAktif->id : null);

            $disimpan = 0;

            foreach ($matrixInput as $key1 => $subData) {
                if (!is_array($subData)) continue;

                foreach ($subData as $key2 => $val) {
                    $valClean = strtoupper(trim($val ?? ''));

                    // Tangani format presensi[masehi][murid_id] ATAU presensi[murid_id][masehi/tglKe]
                    if (isset($dateByMasehi[$key1])) {
                        // Format: presensi[masehi][murid_id]
                        $tglMasehi = $key1;
                        $muridId = $key2;
                        $dInfo = $dateByMasehi[$tglMasehi];
                    } elseif (isset($dateByMasehi[$key2])) {
                        // Format: presensi[murid_id][masehi]
                        $muridId = $key1;
                        $tglMasehi = $key2;
                        $dInfo = $dateByMasehi[$tglMasehi];
                    } elseif (isset($dates[$key2])) {
                        // Format: presensi[murid_id][tglKe]
                        $muridId = $key1;
                        $dInfo = $dates[$key2];
                        $tglMasehi = $dInfo['masehi'];
                    } else {
                        continue;
                    }

                    if (!$dInfo['is_jadwal'] || !$dInfo['jadwal_id']) continue;

                    $statusFull = match ($valClean) {
                        'H' => 'Hadir',
                        'S' => 'Sakit',
                        'I' => 'Izin',
                        'A' => 'Alpha',
                        'D' => 'Dispensasi',
                        default => null
                    };

                    if ($statusFull) {
                        PresensiMurid::updateOrCreate(
                            [
                                'jadwal_pelajaran_id' => $dInfo['jadwal_id'],
                                'murid_id'            => $muridId,
                                'tanggal'             => $tglMasehi,
                            ],
                            [
                                'semester_id'         => $semesterId,
                                'status'              => $statusFull,
                            ]
                        );
                        $disimpan++;
                    } else {
                        // Jika dikosongkan (val == ''), hapus presensi jika sebelumnya ada
                        if ($valClean === '' || $valClean === '-') {
                            PresensiMurid::where('jadwal_pelajaran_id', $dInfo['jadwal_id'])
                                ->where('murid_id', $muridId)
                                ->where('tanggal', $tglMasehi)
                                ->delete();
                        }
                    }
                }
            }

            return $disimpan;
        });
    }
}
