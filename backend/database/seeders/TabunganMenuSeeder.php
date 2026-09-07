<?php

namespace Database\Seeders;

use App\Models\KonfigurasiMenu\Menu;
use App\Models\KonfigurasiMenu\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;

class TabunganMenuSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Parent Menu Tabungan Madrasah
        $parentMenu = Menu::firstOrNew(['name' => 'Tabungan Madrasah', 'category' => 'KEUANGAN']);
        $parentMenu->name = 'Tabungan Madrasah';
        $parentMenu->url = '#';
        $parentMenu->category = 'KEUANGAN';
        $parentMenu->icon = 'bi-wallet2';
        $parentMenu->is_active = 1;
        $parentMenu->orders = 35;
        $parentMenu->save();

        // 2. Submenus
        $submenus = [
            [
                'name' => 'Dashboard Tabungan',
                'url' => 'tabungan.dashboard',
                'icon' => 'bi-speedometer2',
                'orders' => 1,
            ],
            [
                'name' => 'Master Rekening',
                'url' => 'tabungan.rekening.index',
                'icon' => 'bi-journal-bookmark-fill',
                'orders' => 2,
            ],
            [
                'name' => 'Setor Tunai',
                'url' => 'tabungan.setor.index',
                'icon' => 'bi-arrow-down-circle-fill',
                'orders' => 3,
            ],
            [
                'name' => 'Pengajuan Penarikan',
                'url' => 'tabungan.pengajuan.index',
                'icon' => 'bi-cash-coin',
                'orders' => 4,
            ],
            [
                'name' => 'Pembagian Akhir',
                'url' => 'tabungan.pembagian.index',
                'icon' => 'bi-gift-fill',
                'orders' => 5,
            ],
            [
                'name' => 'Pengaturan Tabungan',
                'url' => 'tabungan.pengaturan.index',
                'icon' => 'bi-gear-fill',
                'orders' => 6,
            ],
        ];

        // URLs to clean up / remove if previously created
        $deletedUrls = [
            'tabungan.rekening.create',
            'tabungan.murid.index',
            'tabungan.ustadz.index',
            'tabungan.kas-ruangan.index',
            'tabungan.umum.index',
        ];

        $menusToDelete = Menu::whereIn('url', $deletedUrls)->get();
        foreach ($menusToDelete as $m) {
            $m->permissions()->detach();
            $m->delete();
        }

        $actions = ['read', 'create', 'update', 'delete'];
        $permIds = [];

        foreach ($submenus as $sub) {
            $child = Menu::firstOrNew(['url' => $sub['url']]);
            $child->name = $sub['name'];
            $child->url = $sub['url'];
            $child->category = 'KEUANGAN';
            $child->icon = $sub['icon'];
            $child->is_active = 1;
            $child->main_menu_id = $parentMenu->id;
            $child->orders = $sub['orders'];
            $child->save();

            // Permissions
            foreach ($actions as $act) {
                $permName = "{$act} {$sub['url']}";
                $perm = Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
                $permIds[] = $perm->id;
            }

            $child->permissions()->syncWithoutDetaching($permIds);
        }

        // Parent permissions
        foreach ($actions as $act) {
            $perm = Permission::firstOrCreate(['name' => "{$act} tabungan", 'guard_name' => 'web']);
            $permIds[] = $perm->id;
        }
        $parentMenu->permissions()->syncWithoutDetaching($permIds);

        // Assign ke Administrator & Staff
        $roles = Role::whereIn('name', ['administrator', 'staff', 'Admin', 'Bendahara'])->get();
        foreach ($roles as $role) {
            foreach ($permIds as $pId) {
                $permission = Permission::find($pId);
                if ($permission && !$role->hasPermissionTo($permission)) {
                    $role->givePermissionTo($permission);
                }
            }
        }

        Cache::forget('menus_hierarchy_with_permissions');
        Cache::flush();
    }
}
