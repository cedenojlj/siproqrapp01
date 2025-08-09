<?php

namespace App\Livewire\Petition;

use Livewire\Component;
use App\Models\Petition;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    public function render()
    {
        $petitions = Petition::with('customer')
            ->where(function ($query) {
                $query->where('status', 'like', '%' . $this->search . '%')
                      ->orWhereHas('customer', function ($query) {
                          $query->where('name', 'like', '%' . $this->search . '%');
                      });
            })
            ->paginate(10);

        return view('livewire.petition.index', [
            'petitions' => $petitions,
        ])->layout('layouts.app');
    }

    public function delete(Petition $petition)
    {
        $petition->delete();
        session()->flash('message', 'Petition deleted successfully.');
    }
}