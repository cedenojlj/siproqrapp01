<?php

namespace App\Livewire\Warehouse;

use Livewire\Component;
use App\Models\Warehouse;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    public function render()
    {
        $warehouses = Warehouse::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('location', 'like', '%' . $this->search . '%')
            ->paginate(10);

        return view('livewire.warehouse.index', [
            'warehouses' => $warehouses,
        ]);
    }

    public function delete(Warehouse $warehouse)
    {
        $warehouse->delete();
        session()->flash('message', 'Warehouse deleted successfully.');
    }
}