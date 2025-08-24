<?php

namespace App\Livewire\Order;

use Livewire\Component;
use Livewire\Attributes\On;

class ModalOrder extends Component
{
    public $abierto = false;


     #[On('open-qr-scanner')]
    public function abrir()
    {
        $this->abierto = true;
    }

    #[On('close-qr-scanner')]
    public function cerrar()
    {
        $this->abierto = false;
    }

    public function render()
    {
        return view('livewire.order.modal-order');
    }
}
