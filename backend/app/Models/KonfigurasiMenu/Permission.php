<?php

namespace App\Models\KonfigurasiMenu;

use Spatie\Permission\Models\Permission as ModelsPermission;

class Permission extends ModelsPermission
{
    public function menus()
    {
        return $this->belongsToMany(Menu::class);
    }
}
