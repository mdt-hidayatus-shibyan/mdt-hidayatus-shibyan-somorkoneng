<?php

namespace App\Models\Keuangan;

use App\Models\TahunPelajaran;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransaksiKeuangan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'transaksi_keuangans';

    protected $fillable = [
        'kode_transaksi',
        'tahun_pelajaran_id',
        'akun_keuangan_id',
        'kategori_keuangan_id',
        'jenis_transaksi',
        'metode_pembayaran',
        'bank_id',
        'akun_tujuan_id',
        'bank_tujuan_id',
        'nominal',
        'tanggal_transaksi',
        'nomor_referensi',
        'keterangan',
        'bukti_transaksi',
        'user_id',
        'status',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'tanggal_transaksi' => 'date',
    ];

    public function tahunPelajaran(): BelongsTo
    {
        return $this->belongsTo(TahunPelajaran::class, 'tahun_pelajaran_id');
    }

    public function akunKeuangan(): BelongsTo
    {
        return $this->belongsTo(AkunKeuangan::class, 'akun_keuangan_id');
    }

    public function kategoriKeuangan(): BelongsTo
    {
        return $this->belongsTo(KategoriKeuangan::class, 'kategori_keuangan_id');
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class, 'bank_id');
    }

    public function akunTujuan(): BelongsTo
    {
        return $this->belongsTo(AkunKeuangan::class, 'akun_tujuan_id');
    }

    public function bankTujuan(): BelongsTo
    {
        return $this->belongsTo(Bank::class, 'bank_tujuan_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
