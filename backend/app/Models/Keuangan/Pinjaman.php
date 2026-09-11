<?php

namespace App\Models\Keuangan;

use App\Models\TahunPelajaran;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pinjaman extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pinjamans';

    protected $fillable = [
        'kode_pinjaman',
        'tahun_pelajaran_id',
        'nasabah_pinjaman_id',
        'akun_keuangan_id',
        'bank_id',
        'nominal_pinjaman',
        'biaya_administrasi',
        'nominal_pencairan',
        'tenor_bulan',
        'margin_infaq_persen',
        'nominal_infaq_bulanan',
        'nominal_angsuran_pokok',
        'nominal_angsuran_total',
        'total_pinjaman_dikembalikan',
        'total_terbayar',
        'sisa_pinjaman',
        'tanggal_pengajuan',
        'tanggal_pencairan',
        'tanggal_jatuh_tempo',
        'keperluan_pinjaman',
        'status',
        'catatan',
        'disetujui_oleh',
        'dicairkan_oleh',
    ];

    protected $casts = [
        'nominal_pinjaman' => 'decimal:2',
        'biaya_administrasi' => 'decimal:2',
        'nominal_pencairan' => 'decimal:2',
        'margin_infaq_persen' => 'decimal:2',
        'nominal_infaq_bulanan' => 'decimal:2',
        'nominal_angsuran_pokok' => 'decimal:2',
        'nominal_angsuran_total' => 'decimal:2',
        'total_pinjaman_dikembalikan' => 'decimal:2',
        'total_terbayar' => 'decimal:2',
        'sisa_pinjaman' => 'decimal:2',
        'tanggal_pengajuan' => 'date',
        'tanggal_pencairan' => 'date',
        'tanggal_jatuh_tempo' => 'date',
    ];

    public function tahunPelajaran(): BelongsTo
    {
        return $this->belongsTo(TahunPelajaran::class, 'tahun_pelajaran_id');
    }

    public function nasabah(): BelongsTo
    {
        return $this->belongsTo(NasabahPinjaman::class, 'nasabah_pinjaman_id');
    }

    public function akunKeuangan(): BelongsTo
    {
        return $this->belongsTo(AkunKeuangan::class, 'akun_keuangan_id');
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class, 'bank_id');
    }

    public function disetujuiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    public function dicairkanOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicairkan_oleh');
    }

    public function jaminans(): HasMany
    {
        return $this->hasMany(JaminanPinjaman::class, 'pinjaman_id');
    }

    public function angsurans(): HasMany
    {
        return $this->hasMany(AngsuranPinjaman::class, 'pinjaman_id')->orderBy('angsuran_ke', 'asc');
    }
}
