<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MataPelajaran extends Model
{
    protected $guarded = ['id'];

    /**
     * Relasi ke tabel Level
     */
    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id');
    }
}
