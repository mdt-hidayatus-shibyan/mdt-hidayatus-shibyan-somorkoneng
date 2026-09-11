<?php

namespace App\Models\Keuangan;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AngsuranPinjaman extends Model
{
    use HasFactory;

    protected $table = 'angsuran_pinjamans';

    protected $fillable = [
        'pinjaman_id',
        'angsuran_ke',
        'tanggal_jatuh_tempo',
        'tanggal_bayar',
        'nominal_pokok',
        'nominal_infaq_margin',
        'nominal_denda',
        'total_bayar',
        'metode_pembayaran',
        'akun_keuangan_id',
        'bank_id',
        'nomor_bukti_bayar',
        'bukti_bayar',
        'status',
        'catatan',
        'diterima_oleh',
    ];

    protected $casts = [
        'nominal_pokok' => 'decimal:2',
        'nominal_infaq_margin' => 'decimal:2',
        'nominal_denda' => 'decimal:2',
        'total_bayar' => 'decimal:2',
        'tanggal_jatuh_tempo' => 'date',
        'tanggal_bayar' => 'date',
    ];

    public function pinjaman(): BelongsTo
    {
        return $this->belongsTo(Pinjaman::class, 'pinjaman_id');
    }

    public function akunKeuangan(): BelongsTo
    {
        return $this->belongsTo(AkunKeuangan::class, 'akun_keuangan_id');
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class, 'bank_id');
    }

    public function diterimaOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diterima_oleh');
    }
}
