<?php

namespace App\Livewire\Warehouse;

use Livewire\Component;
use App\Models\Warehouse;

class Edit extends Component
{
    public $warehouse;
    public $name;
    public $location;

    protected $rules = [
        'name' => 'required|string|max:255|unique:warehouses,name,' . '$this->warehouse->id',
        'location' => 'nullable|string|max:255',
    ];

    public function mount(Warehouse $warehouse)
    {
        $this->warehouse = $warehouse;
        $this->name = $warehouse->name;
        $this->location = $warehouse->location;
    }

    public function update()
    {
        $this->validate();

        $this->warehouse->update([
            'name' => $this->name,
            'location' => $this->location,
        ]);

        session()->flash('message', 'Warehouse updated successfully.');

        return redirect()->route('warehouses.index');
    }

    public function render()
    {
        return view('livewire.warehouse.edit')
            ->layout('layouts.app');
    }
}