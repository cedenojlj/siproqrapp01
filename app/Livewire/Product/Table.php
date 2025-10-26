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
            ->orWhere('type', 'like', '%' . $this->search . '%')
            ->orWhere('size', 'like', '%' . $this->search . '%')
            ->orWhere('sku', 'like', '%' . $this->search . '%')
            ->orWhere('invoice_number', 'like', '%' . $this->search . '%')
            ->orWhere('GN', 'like', '%' . $this->search . '%')
            ->orderBy('id', 'desc')
            ->paginate(10);

       /*  $products = Product::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('description', 'like', '%' . $this->search . '%')
            ->get(); */


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
