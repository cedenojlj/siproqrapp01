<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use App\Models\Customer;

class Edit extends Component
{
    public $customer;
    public $name;
    public $address;
    public $phone;
    public $email;

    protected $rules = [
        'name' => 'required|string|max:255',
        'address' => 'nullable|string|max:255',
        'phone' => 'nullable|string|max:20',
        'email' => 'required|email|max:255',
        //'email' => 'nullable|email|max:255|unique:customers,email,' . '$this->customer->id',
    ];

    public function mount(Customer $customer)
    {
        $this->customer = $customer;
        $this->name = $customer->name;
        $this->address = $customer->address;
        $this->phone = $customer->phone;
        $this->email = $customer->email;
    }

    public function update()
    {
        $this->validate();

        $this->customer->update([
            'name' => $this->name,
            'address' => $this->address,
            'phone' => $this->phone,
            'email' => $this->email,
        ]);

        session()->flash('message', 'Customer updated successfully.');

        return redirect()->route('customers.index');
    }

    public function render()
    {
        return view('livewire.customer.edit');
    }
}