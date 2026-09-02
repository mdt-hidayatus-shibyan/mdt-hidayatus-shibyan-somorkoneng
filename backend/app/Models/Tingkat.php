<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;


class Tingkat extends Model
{
    protected $guarded = ['id'];
    public function scopeBerdasarkanHakAkses(Builder $query)
    {
        $user = auth()->user();

        // 1. Keamanan ekstra
        if (!$user) {
            return $query->whereRaw('0 = 1');
        }

        // 2. Super Admin & Admin Madrasah: Akses penuh
        if ($user->hasAnyRole(['administrator'])) {
            return $query;
        }

        // 3. Admin Tingkat: Hanya level yang berada di bawah tingkatnya
        if ($user->hasRole('staff')) {
            return $query->where('id', $user->tingkat_id);
        }

        // 5. Default Tolak Akses
        return $query->whereRaw('0 = 1');
    }
    public function user()
    {
        return $this->hasMany(User::class);
    }
    public function level()
    {
        return $this->hasMany(Level::class);
    }
}
