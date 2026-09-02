<?php

namespace App\Models\Ujian;

use Illuminate\Database\Eloquent\Model;

class DispensasiUjian extends Model
{
    protected $fillable = ['ujian_id', 'murid_id', 'alasan_izin', 'diizinkan_oleh'];
}
