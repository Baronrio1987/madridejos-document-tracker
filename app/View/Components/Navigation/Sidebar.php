<?php

namespace App\View\Components\Navigation;

use Illuminate\View\Component;

class Sidebar extends Component
{
    public $pendingDocumentsCount;
    
    public function __construct($pendingDocumentsCount = 0)
    {
        $this->pendingDocumentsCount = $pendingDocumentsCount;
    }
    
    public function render()
    {
        return view('components.navigation.sidebar');
    }
}