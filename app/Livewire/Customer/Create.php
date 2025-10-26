<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Price;


class Create extends Component
{
    public $name;
    public $address;
    public $phone;
    public $email;

    protected $rules = [
        'name' => 'required|string|max:255',
        'address' => 'nullable|string|max:255',
        'phone' => 'nullable|string|max:20',
        'email' => 'nullable|email|max:255|unique:customers,email',
    ];

    public function save()
    {
        $this->validate();

        Customer::create([
            'name' => $this->name,
            'address' => $this->address,
            'phone' => $this->phone,
            'email' => $this->email,
        ]);

        //recuperar el id del cliente recien creado
        $customer = Customer::where('email', $this->email)->first();

        //crear precios por defecto para el cliente
        $products = Product::all();

        foreach ($products as $product) {
            Price::create([
                'product_id' => $product->id,
                'customer_id' => $customer->id,
                'price_weight' => $product->classification->precio_peso ?? 0,
                'price_quantity' => $product->classification->precio_unidad ?? 0,
            ]);
        }

        session()->flash('message', 'Customer created successfully.');

        $this->reset();

        return redirect()->route('customers.index');
    }

    public function render()
    {
        return view('livewire.customer.create');
    }
}