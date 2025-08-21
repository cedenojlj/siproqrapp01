<?php

namespace App\Livewire\Product;

use Livewire\Component;
use Livewire\Attributes\On;

class ModalQrscanner extends Component
{
    public $abierto = false;

    #[On('openScannerModal')]
    public function abrir()
    {
        $this->abierto = true;
    }

    #[On('closeScannerModal')]
    public function cerrar()
    {
        $this->abierto = false;
    }

    public function render()
    {
        return view('livewire.product.modal-qrscanner');
    }
}
