<?php

namespace App\Livewire\Price;

use Livewire\Component;
use App\Models\Price;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    public function render()
    {
        $prices = Price::with(['product', 'customer'])
            ->whereHas('product', function ($query) {   
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->orWhereHas('customer', function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->orwhereHas('product', function ($query) {
                $query->where('sku', 'like', '%' . $this->search . '%');
            })
            ->orwhereHas('product', function ($query) {
                $query->where('size', 'like', '%' . $this->search . '%');
            })
            ->orwhereHas('product', function ($query) {
                $query->where('type', 'like', '%' . $this->search . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.price.index', [
            'prices' => $prices,
        ]);
    }

    public function delete(Price $price)
    {
        $price->delete();
        session()->flash('message', 'Price deleted successfully.');
    }
}