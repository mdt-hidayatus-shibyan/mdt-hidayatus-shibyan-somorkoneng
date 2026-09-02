<?php

use App\Models\Setting;
use App\Repositories\MenuRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;


if (!function_exists('getSetting')) {
    /**
     * Mengambil nilai pengaturan berdasarkan key.
     * Menggunakan Cache agar tidak membebani database.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function getSetting($key, $default = null)
    {
        // Menyimpan semua pengaturan ke dalam Cache selamanya (sampai diperbarui)
        $settings = Cache::rememberForever('app_settings', function () {
            return Setting::pluck('value', 'key')->toArray();
        });

        // Kembalikan nilai jika ada, jika tidak kembalikan nilai default
        return $settings[$key] ?? $default;
    }
}



if (!function_exists('menus')) {
    /**
     * @return Collection
     */

    function menus()
    {
        \Illuminate\Support\Facades\Cache::forget('menus');
        \Illuminate\Support\Facades\Cache::forget('urlMenu');
        if (!Cache::has('menus')) {
            $menus = (new MenuRepository())->getMenus()->groupBy('category');

            Cache::forever('menus', $menus);
        } else {
            $menus = Cache::get('menus');
        }
        return $menus;
    }
}



if (!function_exists('urlMenu')) {
    function urlMenu()
    {
        if (!Cache::has('urlMenu')) {

            $menus = menus()->flatMap(fn($item) => $item);

            $url = [];
            foreach ($menus as $mm) {
                $url[] = $mm->url;
                foreach ($mm->subMenus as $sm) {
                    $url[] = $sm->url;
                }
            }

            Cache::forever('urlMenu', $url);
        } else {
            $url = Cache::get('urlMenu');
        }
        return $url;
    }
}


if (!function_exists('user')) {
    /**
     * @param string $id
     * @return \App\Models\User | string
     */
    function user($id = null)
    {
        if ($id) {
            return request()->user()->{$id};
        }
        return request()->user();
    }
}



if (!function_exists('canAccessMenu')) {
    /**
     * @return bool
     */
    function canAccessMenu($menu)
    {
        $user = auth()->user();
        if (!$user) return false;

        // Jika ini adalah Menu Tunggal / Sub-Menu yang memiliki URL asli (bukan '#')
        if ($menu->url !== '#') {
            // Cek langsung dengan format trait Anda: "read {url}"
            if ($user->can('read ' . $menu->url)) {
                return true;
            }

            // Fallback: Jika dia tidak punya 'read', tapi punya 'create' / 'update', izinkan juga
            $menuPerms = $menu->permissions->pluck('name')->toArray();
            if (!empty($menuPerms) && $user->hasAnyPermission($menuPerms)) {
                return true;
            }
        }

        // Jika ini Menu Utama (Dropdown) yang URL-nya '#'
        if ($menu->url === '#' && $menu->subMenus && $menu->subMenus->count() > 0) {
            foreach ($menu->subMenus as $sub) {
                // Lakukan pengecekan rekursif ke anak-anaknya
                // Jika ADA MINIMAL 1 anak yang bisa diakses, BUKA Menu Utama ini!
                if (canAccessMenu($sub)) {
                    return true;
                }
            }
        }

        // Jika menu ini tidak dipasangi permission apa pun di DB (bersifat menu publik)
        if ($menu->permissions->count() == 0 && (!isset($menu->subMenus) || $menu->subMenus->count() == 0)) {
            return true;
        }

        return false;
    }
}

if (!function_exists('getTodayDateInfo')) {
    /**
     * Mendapatkan informasi tanggal Masehi dan Hijriyah hari ini berdasarkan kalender pendidikan.
     *
     * @param string|null $tanggal
     * @return array
     */
    function getTodayDateInfo($tanggal = null)
    {
        $date = $tanggal ? \Carbon\Carbon::parse($tanggal) : \Carbon\Carbon::now();
        $dateString = $date->toDateString();

        // Cari data bulan hijriyah di database yang mencakup tanggal ini
        $bulan = \App\Models\BulanHijriyah::with('tahunPelajaran')
            ->where('tanggal_mulai_masehi', '<=', $dateString)
            ->where('tanggal_selesai_masehi', '>=', $dateString)
            ->first();

        // Jika tidak ditemukan rentang tanggal persis, fallback ke bulan hijriyah yang sedang aktif
        if (!$bulan) {
            $bulan = \App\Models\BulanHijriyah::with('tahunPelajaran')
                ->where('is_active', true)
                ->first();
        }

        $hijriString = null;
        if ($bulan) {
            $diffDays = (int) \Carbon\Carbon::parse($bulan->tanggal_mulai_masehi)->diffInDays($date, false);
            $hijriDay = $diffDays >= 0 ? $diffDays + 1 : 1;
            if ($hijriDay > 30) $hijriDay = 30;

            $tahunHijri = $bulan->tahun_hijriyah;
            if (!$tahunHijri && $bulan->tahunPelajaran) {
                $tahunHijri = explode('/', $bulan->tahunPelajaran->nama_hijriyah)[0] ?? '';
            }

            $hijriString = "{$hijriDay} {$bulan->nama_bulan}" . ($tahunHijri ? " {$tahunHijri} H" : ' H');
        } else {
            // Fallback tahun pelajaran aktif jika bulan belum di-set
            $tp = \App\Models\TahunPelajaran::where('is_active', true)->first();
            if ($tp) {
                $hijriString = "T.P. {$tp->nama_hijriyah} H";
            }
        }

        // Format nama hari dan tanggal masehi bahasa Indonesia
        $hariArray = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jum\'at', 'Sabtu'];
        $bulanArray = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        $namaHari = $hariArray[$date->dayOfWeek];
        $namaBulan = $bulanArray[$date->month];
        $masehiString = "{$namaHari}, {$date->day} {$namaBulan} {$date->year}";
        $masehiCompact = "{$date->day} " . substr($namaBulan, 0, 3) . " {$date->year}";

        return [
            'masehi'         => $masehiString,
            'masehi_compact' => $masehiCompact,
            'hijri'          => $hijriString,
            'nama_hari'      => $namaHari,
            'tanggal'        => $date->day,
            'bulan'          => $namaBulan,
            'tahun'          => $date->year,
        ];
    }
}
