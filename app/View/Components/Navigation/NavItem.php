<?php

namespace App\View\Components\Navigation;

use Illuminate\View\Component;

class NavItem extends Component
{
    public $route;
    public $icon;
    public $badge;
    public $href;
    
    public function __construct($route, $icon, $badge = null, $href = null)
    {
        $this->route = $route;
        $this->icon = $icon;
        $this->badge = $badge;
        $this->href = $href ?? route(str_replace('.*', '.index', $route));
    }
    
    public function isActive()
    {
        return request()->routeIs($this->route);
    }
    
    public function render()
    {
        return view('components.navigation.nav-item');
    }
}