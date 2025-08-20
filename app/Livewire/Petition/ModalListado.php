<?php

namespace App\Livewire\Petition;

use Livewire\Attributes\On;
use Livewire\Component;


class ModalListado extends Component
{
    public $abierto = false;  

     #[On('abrirModalListado')] 
    function abrirModal() {
        
        $this->abierto = true;

    } 

    //cerrar modal
     #[On('cerrarModalListado')]
    public function cerrarModal() {
        $this->abierto = false;
    }

    public function abrir() { $this->abierto = true; }
    public function cerrar() { $this->abierto = false;  }
    
        
    public function render()
    {
        return view('livewire.petition.modal-listado');
    }
}
