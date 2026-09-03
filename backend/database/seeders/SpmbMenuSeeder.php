<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\KonfigurasiMenu\Menu;

class SpmbMenuSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = ['read spmb', 'create spmb', 'update spmb', 'delete spmb'];
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $adminRole = Role::where('name', 'administrator')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
        }

        $menu = Menu::firstOrCreate(
            ['url' => 'spmb-admin.index'],
            [
                'name' => 'Penerimaan Murid (SPMB)',
                'category' => 'Master Data',
                'icon' => 'bi-mortarboard',
                'is_active' => 1,
                'orders' => 4,
                'main_menu_id' => null
            ]
        );

        $readPerm = Permission::where('name', 'read spmb')->first();
        if ($readPerm && !$menu->permissions->contains($readPerm->id)) {
            $menu->permissions()->attach($readPerm->id);
        }
    }
}
