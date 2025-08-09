<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Product;
use App\Models\Order;
use App\Models\Petition;
use App\Models\Customer;
use App\Models\Warehouse;

class Index extends Component
{
    public $totalProducts;
    public $totalOrders;
    public $totalPetitions;
    public $totalCustomers;
    public $totalWarehouses;

    public function mount()
    {
        $this->totalProducts = Product::count();
        $this->totalOrders = Order::count();
        $this->totalPetitions = Petition::count();
        $this->totalCustomers = Customer::count();
        $this->totalWarehouses = Warehouse::count();
    }

    public function render()
    {
        return view('livewire.dashboard.index');
    }
}