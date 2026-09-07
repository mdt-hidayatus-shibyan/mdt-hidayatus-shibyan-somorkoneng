<?php

namespace App\Models\Tabungan;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class PengaturanPotonganTabungan extends Model
{
    protected $table = 'pengaturan_potongan_tabungans';
    protected $guarded = ['id'];

    protected $casts = [
        'persentase_potongan' => 'float',
    ];

    public function periodeTabungan()
    {
        return $this->belongsTo(PeriodeTabungan::class, 'periode_tabungan_id');
    }

    public function userPengubah()
    {
        return $this->belongsTo(User::class, 'diubah_oleh');
    }

    /**
     * Ambil persentase potongan untuk jenis nasabah tertentu per periode
     */
    public static function getPersentase($jenisNasabah, ?int $periodeTabunganId = null): float
    {
        // 1. Jika tidak ada periodeId yang diberikan, ambil periode yang sedang aktif
        if (!$periodeTabunganId) {
            $periodeTabunganId = PeriodeTabungan::where('is_active', true)->value('id');
        }

        // 2. Cari pengaturan potongan spesifik untuk periode tersebut
        if ($periodeTabunganId) {
            $setting = static::where('periode_tabungan_id', $periodeTabunganId)
                ->where('jenis_nasabah', $jenisNasabah)
                ->first();

            if ($setting) {
                return (float) $setting->persentase_potongan;
            }
        }

        // 3. Fallback ke setting global jika ada
        $globalSetting = static::whereNull('periode_tabungan_id')
            ->where('jenis_nasabah', $jenisNasabah)
            ->first();

        if ($globalSetting) {
            return (float) $globalSetting->persentase_potongan;
        }

        // 4. Default fallback jika belum diatur
        return match ($jenisNasabah) {
            'Ustadz' => 2.50,
            'Kas Ruangan' => 0.00,
            default => 10.00,
        };
    }

    /**
     * Inisialisasi otomatis potongan default per periode
     */
    public static function inisialisasiPotonganPeriode(int $periodeId, array $customPotongan = [], ?int $userId = null)
    {
        $defaults = [
            'Murid' => ['persen' => 10.00, 'desc' => 'Dikenakan saat penarikan / pembagian tabungan murid'],
            'Ustadz' => ['persen' => 2.50, 'desc' => 'Infaq sukarela dewan asatidz madrasah'],
            'Kas Ruangan' => ['persen' => 0.00, 'desc' => 'Kas operasional kelas (bebas potongan)'],
            'Umum' => ['persen' => 10.00, 'desc' => 'Infaq pembangunan dari nasabah umum / donatur'],
        ];

        foreach ($defaults as $jenis => $info) {
            $persen = isset($customPotongan[$jenis]['persentase'])
                ? (float) $customPotongan[$jenis]['persentase']
                : $info['persen'];
            $musyawarah = $customPotongan[$jenis]['dasar_musyawarah'] ?? null;

            static::updateOrCreate(
                [
                    'periode_tabungan_id' => $periodeId,
                    'jenis_nasabah' => $jenis
                ],
                [
                    'persentase_potongan' => $persen,
                    'dasar_musyawarah' => $musyawarah,
                    'diubah_oleh' => $userId,
                ]
            );
        }
    }
}
