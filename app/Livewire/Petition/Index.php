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
          /** @disregard P1013 */
        $user = auth()->user();

        if ($user->hasRole('superadmin')) {

            // Fetch petitions with user relationship and apply search filter            
            
            $petitions = Petition::with('user')
            ->where(function ($query) {
                $query->where('status', 'like', '%' . $this->search . '%')
                      ->orWhereHas('user', function ($query) {
                          $query->where('name', 'like', '%' . $this->search . '%');
                      });
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.petition.index', [
            'petitions' => $petitions,
        ]);
            
        } else {

            $petitions = Petition::with('user')
            ->where(function ($query) {
                $query->where('status', 'like', '%' . $this->search . '%')
                      ->orWhereHas('user', function ($query) {
                          $query->where('name', 'like', '%' . $this->search . '%');
                      });
            })
            ->where('user_id', Auth::id())
            ->orderBy('id', 'desc')
            ->paginate(10);

        }     
        
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