<?php

namespace App\Livewire\Order;

use Livewire\Component;

class QrscannerOrder extends Component
{
    
     public $result = '';

     public function setResult($data)
    {
        $this->result = $data;

        $decoded = json_decode($data, true);

        if (json_last_error() === JSON_ERROR_NONE && !empty($decoded['sku'])) {
            $this->dispatch('enviar-sku', $decoded['sku']);
            session()->flash('message', 'SKU encontrado: ' . $decoded['sku']);
        } else {
            session()->flash('error', 'QR no contiene un JSON válido o falta el campo "sku".');
           // $this->dispatch('skuInvalid');
        }
    }
    
    
    
    
    public function render()
    {
        return view('livewire.order.qrscanner-order');
    }
}
