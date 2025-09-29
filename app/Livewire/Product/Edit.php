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
    public $sku;
    public $size;    
    public $classification_id;
    public $type;
    public $GN;
    //public $GW;
    //public $Box;
    public $invoice_number;
    

    protected $rules = [
        'name' => 'required|string|max:255',
        //'size' => 'nullable|string|max:255',
        //'sku' => 'required|string|max:255|unique:products,sku',
        //'type' => 'required|string|max:255',
        'GN' => 'required|decimal|min:0',
        // 'GW' => 'nullable|string|max:255',
        // 'Box' => 'nullable|string|max:255',
        'invoice_number' => 'required|integer|min:0',
        'classification_id' => 'required|exists:classifications,id',        
    ];

    public function mount(Product $product)
    {
        $this->product = $product;
        $this->name = $product->name;
        $this->type = $product->type;
        $this->sku = $product->sku;
        $this->size = $product->size;
        $this->GN = $product->GN;
        // $this->GW = $product->GW;
        // $this->Box = $product->Box;
        $this->invoice_number = $product->invoice_number;
        $this->classification_id = $product->classification_id;
        
    }

    public function update()
    {
        $this->validate();

        $this->product->update([
            'name' => $this->name,            
            //'sku' => $this->sku,
            //'size' => $this->size,
            //'type' => $this->type,
            'GN' => $this->GN,
            // 'GW' => $this->GW,
            // 'Box' => $this->Box,
            'invoice_number' => $this->invoice_number,
            'classification_id' => $this->classification_id,
            
        ]);

        session()->flash('message', 'Product updated successfully.');

        return redirect()->route('products.index');
    }

    public function render()
    {
        $classifications = Classification::all();
        

        return view('livewire.product.edit', [
            'classifications' => $classifications
        ]);
    }
}