<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use App\Models\Customer;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    public function render()
    {
        $customers = Customer::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('email', 'like', '%' . $this->search . '%')
            ->paginate(10);

        return view('livewire.customer.index', [
            'customers' => $customers,
        ])->layout('layouts.app');
    }

    public function delete(Customer $customer)
    {
        $customer->delete();
        session()->flash('message', 'Customer deleted successfully.');
    }
}