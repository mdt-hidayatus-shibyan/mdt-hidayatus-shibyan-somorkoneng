<?php

namespace App\Http\Controllers;

use App\Traits\HasPermission;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
// use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests, HasPermission;
}
