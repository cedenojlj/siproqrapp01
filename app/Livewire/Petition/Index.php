<?php

namespace App\Livewire\Petition;

use Livewire\Component;
use App\Models\Petition;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

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
            })->where('user_id', Auth::id())
            ->paginate(10);

        return view('livewire.petition.index', [
            'petitions' => $petitions,
        ]);
    }

    public function delete(Petition $petition)
    {
        $petition->petitionProducts()->delete();

        $petition->delete();
        session()->flash('message', 'Petition deleted successfully.');
    }
}