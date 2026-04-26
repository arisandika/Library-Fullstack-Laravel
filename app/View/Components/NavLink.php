<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class NavLink extends Component
{
    /**
     * @var bool
     */
    public bool $active;

    /**
     * @param bool $active
     */
    public function __construct(bool $active = false)
    {
        $this->active = $active;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.nav-link');
    }
}
