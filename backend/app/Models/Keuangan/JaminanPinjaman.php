<?php

namespace App\Models\Keuangan;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JaminanPinjaman extends Model
{
    use HasFactory;

    protected $table = 'jaminan_pinjamans';

    protected $fillable = [
        'pinjaman_id',
        'jenis_jaminan',
        'nama_barang_jaminan',
        'nomor_dokumen_jaminan',
        'atas_nama_dokumen',
        'taksiran_nilai',
        'deskripsi_kondisi',
        'lokasi_penyimpanan',
        'foto_dokumen',
        'foto_barang',
        'status_jaminan',
        'tanggal_diserahkan',
        'tanggal_dikembalikan',
        'penerima_jaminan_id',
        'pengembali_jaminan_id',
        'catatan',
    ];

    protected $casts = [
        'taksiran_nilai' => 'decimal:2',
        'tanggal_diserahkan' => 'date',
        'tanggal_dikembalikan' => 'date',
    ];

    public function pinjaman(): BelongsTo
    {
        return $this->belongsTo(Pinjaman::class, 'pinjaman_id');
    }

    public function penerimaJaminan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penerima_jaminan_id');
    }

    public function pengembaliJaminan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pengembali_jaminan_id');
    }
}
