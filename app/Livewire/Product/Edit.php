<?php

namespace App\Livewire\Product;

use Livewire\Component;
use App\Models\Product;
use App\Models\Classification;
use App\Models\Warehouse;

class Edit extends Component
{
    public $product;
    public $name;
    public $description;
    public $sku;
    public $price;
    public $stock;
    public $classification_id;
    public $warehouse_id;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'sku' => 'required|string|max:255|unique:products,sku',
        'price' => 'required|numeric|min:0',
        'stock' => 'required|integer|min:0',
        'classification_id' => 'required|exists:classifications,id',
        'warehouse_id' => 'required|exists:warehouses,id',
    ];

    public function mount(Product $product)
    {
        $this->product = $product;
        $this->name = $product->name;
        $this->description = $product->description;
        $this->sku = $product->sku;
        $this->price = $product->price;
        $this->stock = $product->stock;
        $this->classification_id = $product->classification_id;
        $this->warehouse_id = $product->warehouse_id;
    }

    public function update()
    {
        $this->validate();

        $this->product->update([
            'name' => $this->name,
            'description' => $this->description,
            'sku' => $this->sku,
            'price' => $this->price,
            'stock' => $this->stock,
            'classification_id' => $this->classification_id,
            'warehouse_id' => $this->warehouse_id,
        ]);

        session()->flash('message', 'Product updated successfully.');

        return redirect()->route('products.index');
    }

    public function render()
    {
        $classifications = Classification::all();
        $warehouses = Warehouse::all();

        return view('livewire.product.edit', [
            'classifications' => $classifications,
            'warehouses' => $warehouses,
        ]);
    }
}