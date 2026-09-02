<?php

namespace Database\Seeders;

use App\Models\KonfigurasiMenu\Menu;
use App\Models\KonfigurasiMenu\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;

class PresensiUjianMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cari parent menu ujian jika menggunakan submenu dropdown
        $parentMenu = Menu::where(function ($q) {
            $q->where('name', 'LIKE', '%Ujian%')
                ->orWhere('category', 'LIKE', '%Ujian%');
        })->whereNull('main_menu_id')->first();

        // Cari menu presensi ujian jika sudah ada
        $menu = Menu::firstOrNew(['url' => 'presensi-ujian.index']);
        $menu->name = 'Presensi Ujian';
        $menu->url = 'presensi-ujian.index';
        $menu->category = $parentMenu ? $parentMenu->category : 'UJIAN';
        $menu->icon = 'bi-person-check-fill';
        $menu->is_active = 1;
        $menu->main_menu_id = $parentMenu ? $parentMenu->id : null;
        $menu->orders = 25;
        $menu->save();

        // Buat permissions untuk presensi-ujian
        $actions = ['read', 'create', 'update', 'delete'];
        $urlPatterns = ['presensi-ujian.index', 'presensi-ujian'];
        $permIds = [];

        foreach ($urlPatterns as $urlPattern) {
            foreach ($actions as $act) {
                $permName = "{$act} {$urlPattern}";
                $perm = Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
                $permIds[] = $perm->id;
            }
        }

        // Attach permissions ke menu
        $menu->permissions()->syncWithoutDetaching($permIds);

        // Assign permission ke role administrator, staff, dan ustadz
        $roles = Role::whereIn('name', ['administrator', 'staff', 'ustadz'])->get();
        foreach ($roles as $role) {
            foreach ($permIds as $pId) {
                $permission = Permission::find($pId);
                if ($permission && !$role->hasPermissionTo($permission)) {
                    $role->givePermissionTo($permission);
                }
            }
        }

        // Bersihkan cache navigasi menu
        Cache::forget('menus');
        Cache::forget('urlMenu');
    }
}
