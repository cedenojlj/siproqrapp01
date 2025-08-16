<?php

namespace App\Livewire\Product;

use Livewire\Component;
use App\Models\Product;
use Livewire\WithPagination;


class Table extends Component
{
    use WithPagination;

    public $search = '';

   
    public function render()
    {
        $products = Product::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('description', 'like', '%' . $this->search . '%')
            ->paginate(10);

        return view('livewire.product.table', [
            'products' => $products,
        ]);
    }

    public function delete(Product $product)
    {
        // Verificar si el producto tiene órdenes asociadas
        if ($product->orderProducts()->count() > 0) {
            session()->flash('error', 'Cannot delete product with existing orders.');
            return;
        }

        //eliminar producto de la tabla productwarehouses
        $product->productWarehouses()->delete();

        $product->delete();
        session()->flash('message', 'Product deleted successfully.');
    }
}
