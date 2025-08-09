<?php

namespace App\Livewire\Product;

use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        return view('livewire.product.index')
            ->layout('layouts.app');
    }
}
