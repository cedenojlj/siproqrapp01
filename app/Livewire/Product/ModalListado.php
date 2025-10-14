<?php

namespace App\Livewire\Product;

use Livewire\Component;
use Livewire\Attributes\On;

class ModalListado extends Component
{
    
    public $abierto = false;


     #[On('open-listado')]
    public function abrir()
    {
        $this->abierto = true;
    }

    #[On('close-listado')]
    public function cerrar()
    {
        $this->abierto = false;
    }
    
    
    
    
    
    
    public function render()
    {
        return view('livewire.product.modal-listado');
    }
}
