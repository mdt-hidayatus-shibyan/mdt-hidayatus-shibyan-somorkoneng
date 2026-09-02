<?php

namespace App\Traits;

use App\Models\KonfigurasiMenu\Menu;
use App\Models\KonfigurasiMenu\Permission;

trait HasMenuPermission
{
    public function attachMenuPermission(Menu $menu, array | null $permissions, array | null $roles)
    {
        /**
         * @var Permission $permission
         */
        if (!is_array($permissions)) {
            $permissions = ['create', 'read', 'update', 'delete'];
        };

        foreach ($permissions as $item) {
            $permission = Permission::create(['name' => $item . " {$menu->url}"]);
            $permission->menus()->attach($menu);
            if ($roles) {
                $permission->assignRole($roles);
            }
        }
    }
}
