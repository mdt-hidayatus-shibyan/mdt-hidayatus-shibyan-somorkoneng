<?php

namespace App\Repositories;

use App\Models\KonfigurasiMenu\Menu;

class MenuRepository
{
    public function getMainMenuWithPermissions()
    {
        return Menu::with('permissions', 'subMenus.permissions')->whereNull('main_menu_id')->get();
    }


    public function getMainMenus()
    {
        return Menu::whereNull('main_menu_id')->select('id', 'name')->get();
    }

    public function getMenus()
    {
        return Menu::active()->with(['subMenus' => function ($query) {
            $query->orderBy('orders');
        }])->whereNull('main_menu_id')->orderBy('orders')->get();
    }
}
