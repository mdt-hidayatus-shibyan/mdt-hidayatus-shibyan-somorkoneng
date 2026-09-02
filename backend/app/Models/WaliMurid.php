<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaliMurid extends Model
{

    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $latest = self::orderBy('id', 'desc')->first();

            if ($latest && is_numeric($latest->no_registrasi)) {
                $model->no_registrasi = (string) ($latest->no_registrasi + 1);
            } else {
                $model->no_registrasi = '50001';
            }
        });
    }

    // Relasi ke tabel Kampung
    public function kampung()
    {
        return $this->belongsTo(Kampung::class, 'kampung_id');
    }

    // Relasi ke tabel Murid
    public function murids()
    {
        return $this->hasMany(Murid::class, 'wali_murid_id');
    }
}
