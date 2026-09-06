<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\KonfigurasiMenu\Menu;

class LaporanKendalaMenuSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = ['read bantuan', 'update bantuan', 'delete bantuan'];
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $adminRole = Role::where('name', 'administrator')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
        }

        $menu = Menu::firstOrCreate(
            ['url' => 'laporan-kendala-admin.index'],
            [
                'name' => 'Laporan & Kendala Ustadz',
                'category' => 'Layanan & Bantuan',
                'icon' => 'bi-headset',
                'is_active' => 1,
                'orders' => 12,
                'main_menu_id' => null
            ]
        );

        $readPerm = Permission::where('name', 'read bantuan')->first();
        if ($readPerm && !$menu->permissions->contains($readPerm->id)) {
            $menu->permissions()->attach($readPerm->id);
        }
    }
}
