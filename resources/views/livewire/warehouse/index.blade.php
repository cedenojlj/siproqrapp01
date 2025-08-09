
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Warehouses') }}</div>

                <div class="card-body">
                    @if (session()->has('message'))
                        <div class="alert alert-success">
                            {{ session('message') }}
                        </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <input wire:model.debounce.300ms="search" type="text" class="form-control" placeholder="Search warehouses...">
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="{{ route('warehouses.create') }}" class="btn btn-primary">Create Warehouse</a>
                        </div>
                    </div>

                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Location</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($warehouses as $warehouse)
                                <tr>
                                    <td>{{ $warehouse->id }}</td>
                                    <td>{{ $warehouse->name }}</td>
                                    <td>{{ $warehouse->location }}</td>
                                    <td>
                                        <a href="{{ route('warehouses.edit', $warehouse->id) }}" class="btn btn-sm btn-secondary">Edit</a>
                                        <button wire:click="delete({{ $warehouse->id }})" class="btn btn-sm btn-danger" onclick="confirm('Are you sure you want to delete this warehouse?') || event.stopImmediatePropagation()">Delete</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{ $warehouses->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
