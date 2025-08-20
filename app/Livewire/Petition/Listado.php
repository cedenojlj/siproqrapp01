<?php

namespace App\Livewire\Petition;
use App\Models\Product;
use Livewire\Component;


class Listado extends Component
{
    public $productos;
    public $search;
    public $indice;

    public function mount()
    {
        $this->productos = Product::all();
    }

       

    public function agregarProducto($idproducto)
    {
        $this->dispatch('colocarProducto', idproducto: $idproducto);        
        $this->dispatch('cerrarModalListado');
    }


    public function render()
    {
         $this->productos = Product::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('size', 'like', '%' . $this->search . '%')
            ->orWhere('type', 'like', '%' . $this->search . '%')
            ->get();
        
        return view('livewire.petition.listado',[
            'productos' => $this->productos,
        ]);
    }
}
