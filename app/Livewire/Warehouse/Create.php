<?php

namespace App\Livewire\Warehouse;

use Livewire\Component;
use App\Models\Warehouse;

class Create extends Component
{
    public $name;
    public $location;

    protected $rules = [
        'name' => 'required|string|max:255|unique:warehouses,name',
        'location' => 'required|string|max:255',
    ];

    public function save()
    {
        $this->validate();

        Warehouse::create([
            'name' => $this->name,
            'location' => $this->location,
        ]);

        session()->flash('message', 'Warehouse created successfully.');

        $this->reset();

        return redirect()->route('warehouses.index');
    }

    public function render()
    {
        return view('livewire.warehouse.create');
    }
}