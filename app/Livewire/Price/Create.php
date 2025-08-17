<?php

namespace App\Livewire\Price;

use Livewire\Component;
use App\Models\Price;
use App\Models\Product;
use App\Models\Customer;

class Create extends Component
{
    public $product_id;
    public $customer_id;
    public $price_quantity;
    public $price_weight;

    public $products;
    public $customers;

    protected $rules = [
        'product_id' => 'required|exists:products,id',
        'customer_id' => 'required|exists:customers,id',
        'price_quantity' => 'required|numeric',
        'price_weight' => 'required|numeric',
    ];

    public function mount()
    {
        $this->products = Product::all();
        $this->customers = Customer::all();
    }

    public function save()
    {
        $this->validate();

        Price::create([
            'product_id' => $this->product_id,
            'customer_id' => $this->customer_id,
            'price_quantity' => $this->price_quantity,
            'price_weight' => $this->price_weight,
        ]);

        session()->flash('message', 'Price created successfully.');

        return redirect()->route('prices.index');
    }

    public function render()
    {
        return view('livewire.price.create');
    }
}