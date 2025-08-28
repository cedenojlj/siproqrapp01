<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Livewire\WithPagination;

class UserManagement extends Component
{
    use WithPagination;

    public $roles;
    public $selectedRole = [];
    public $search = '';

    public function mount()
    {
        $this->roles = Role::all();
    }

    public function updateUserRole($userId)
    {
        $user = User::find($userId);
        $roleId = $this->selectedRole[$userId];
        $role = Role::find($roleId);

        if ($user && $role) {
            $user->syncRoles([$role->name]);
            session()->flash('message', 'User role updated successfully.');
        } else {
            session()->flash('error', 'Could not update user role.');
        }
    }

    public function render()
    {
        $users = User::with('roles')
            ->where(function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                      ->orWhere('email', 'like', '%'.$this->search.'%');
            })
            ->paginate(10);

        foreach ($users as $user) {
            if (!isset($this->selectedRole[$user->id])) {
                $this->selectedRole[$user->id] = $user->roles->first()->id ?? null;
            }
        }
        return view('livewire.user-management', [
            'users' => $users,
        ]);
    }
}
