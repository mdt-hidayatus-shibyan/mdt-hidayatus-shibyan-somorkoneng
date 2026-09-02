<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    protected $guarded = ['id'];

    public function scopeBerdasarkanHakAkses(Builder $query)
    {
        /** @var \App\Models\User $user */
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
            return $query->where('tingkat_id', $user->tingkat_id);
        }

        // 4. Asatidz / Guru: Hanya melihat level dari kelas yang dia ajar
        if ($user->hasAnyRole(['ustadz', 'Ustadz'])) {
            return $query->whereHas('ruangans.jadwalPelajarans', function ($q) use ($user) {
                $q->where('ustadz_id', $user->ustadz->id ?? null);
            });
        }

        // 5. Default Tolak Akses
        return $query->whereRaw('0 = 1');
    }



    public function tingkat()
    {
        return $this->belongsTo(Tingkat::class);
    }

    public function ruangans()
    {
        return $this->hasMany(Ruangan::class);
    }

    public function mataPelajarans()
    {
        return $this->hasMany(MataPelajaran::class, 'level_id');
    }
}
