<div>
    <div class="row mb-3">
        <div class="col-md-12">
            @if (session()->has('message'))
                <div class="alert {{ request()->cookie('theme') === 'dark' ? 'alert-light' : 'alert-success' }}">
                    {{ session('message') }}
                </div>
            @endif
            @if (session()->has('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h3>User Management</h3>
                </div>
                <div class="col-md-6">
                    <input wire:model.live="search" type="text" class="form-control" placeholder="Search by name or email...">
                </div>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <select wire:model="selectedRole.{{ $user->id }}" class="form-select">
                                    <option value="">No Role</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                @can('update users')
                                    <button wire:click="updateUserRole({{ $user->id }})" class="btn"><i class="bi bi-pencil-square"></i></button>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $users->links() }}
        </div>
    </div>
</div>
