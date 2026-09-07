<?php

namespace App\Models\Tabungan;

use App\Models\Murid;
use App\Models\Ruangan;
use App\Models\Ustadz;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tabungan extends Model
{
    protected $table = 'tabungans';
    protected $guarded = ['id'];

    protected $casts = [
        'saldo' => 'float',
        'total_setor' => 'float',
        'total_tarik' => 'float',
        'total_potongan' => 'float',
    ];

    public function murid()
    {
        return $this->belongsTo(Murid::class, 'murid_id');
    }

    public function ustadz()
    {
        return $this->belongsTo(Ustadz::class, 'ustadz_id');
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }

    public function periodeTabungan()
    {
        return $this->belongsTo(PeriodeTabungan::class, 'periode_tabungan_id');
    }

    public function transaksis()
    {
        return $this->hasMany(TransaksiTabungan::class, 'tabungan_id')->orderBy('tanggal', 'desc')->orderBy('id', 'desc');
    }

    public function pengajuans()
    {
        return $this->hasMany(PengajuanPenarikanTabungan::class, 'tabungan_id')->orderBy('id', 'desc');
    }

    /**
     * Nama identitas tampilan pemilik rekening
     */
    public function getNamaNasabahAttribute()
    {
        return match ($this->jenis_nasabah) {
            'Murid' => $this->murid->nama_lengkap ?? $this->murid->nama ?? 'Murid Tidak Dikenal',
            'Ustadz' => $this->ustadz->nama_lengkap ?? 'Ustadz Tidak Dikenal',
            'Kas Ruangan' => 'Kas Ruangan ' . ($this->ruangan->nama_ruangan ?? '-'),
            'Umum' => $this->nama_nasabah_umum ?? 'Nasabah Umum',
            default => 'Nasabah',
        };
    }

    /**
     * Kode / Identitas unik nasabah (NISM, NIGM, dll.)
     */
    public function getIdentitasNasabahAttribute()
    {
        return match ($this->jenis_nasabah) {
            'Murid' => 'NISM: ' . ($this->murid->nism ?? '-'),
            'Ustadz' => 'NIGM: ' . ($this->ustadz->nigm ?? $this->ustadz->kode_ustadz ?? '-'),
            'Kas Ruangan' => 'Ruangan ' . ($this->ruangan->nama_ruangan ?? '-'),
            'Umum' => 'Kontak: ' . ($this->kontak_umum ?? '-'),
            default => '-',
        };
    }

    /**
     * Generator Nomor Rekening Otomatis
     */
    public static function generateNomorRekening($jenisNasabah, $nasabah = null, $urutanKe = 1)
    {
        $suffix = $urutanKe > 1 ? "-{$urutanKe}" : "";

        return match ($jenisNasabah) {
            'Murid' => ($nasabah->nism ?: sprintf("MRD-%05d", $nasabah->id)) . $suffix,
            'Ustadz' => ($nasabah->nigm ?: $nasabah->kode_ustadz ?: sprintf("UST-%04d", $nasabah->id)) . $suffix,
            'Kas Ruangan' => "KAS-R" . ($nasabah?->id ?? '0') . "-" . strtoupper(Str::random(4)) . $suffix,
            'Umum' => "UMM-" . mt_rand(10000000, 99999999) . $suffix,
            default => "TAB-" . mt_rand(10000000, 99999999) . $suffix,
        };
    }
}
