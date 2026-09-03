<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AuthLayout extends Component
{
    public string $maxWidth;

    /**
     * Create a new component instance.
     */
    public function __construct(string $maxWidth = 'max-w-[440px]')
    {
        $this->maxWidth = $maxWidth;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('layouts.auth');
    }
}
