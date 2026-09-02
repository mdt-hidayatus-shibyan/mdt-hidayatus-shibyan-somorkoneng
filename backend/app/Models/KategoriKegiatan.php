<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriKegiatan extends Model
{
    protected $guarded = ['id'];

    // 1 Kategori bisa dimiliki oleh banyak jadwal Kalender
    public function kalendarPendidikans()
    {
        return $this->hasMany(KalendarPendidikan::class);
    }
}
