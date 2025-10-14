<?php

namespace App\Livewire\Product;

use App\Models\Classification;
use App\Models\Product;

use Livewire\Component;

class Lista extends Component
{

    public $codigos;
    public $search;

    
    public function mount()
    {
        $this->codigos = Classification::all();
    }

    //funcion para agregar el codigo al formulario de productos
    public function agregarCodigo($idcodigo)
    {
        $this->dispatch('colocarCodigo', idcodigo: $idcodigo);
        $this->dispatch('close-listado');
    }

    public function render()
    {
        $this->codigos = Classification::where('description', 'like', '%' . $this->search . '%')
            ->orWhere('size', 'like', '%' . $this->search . '%')
            ->get();
        
        return view('livewire.product.lista', [
            'codigos' => $this->codigos,
        ]);
    }
}
