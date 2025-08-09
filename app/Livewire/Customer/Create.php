<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use App\Models\Customer;

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

        session()->flash('message', 'Customer created successfully.');

        $this->reset();

        return redirect()->route('customers.index');
    }

    public function render()
    {
        return view('livewire.customer.create');
    }
}