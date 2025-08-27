<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Customers') }}</div>

                <div class="card-body">
                    @if (session()->has('message'))
                        <div class="alert alert-success">
                            {{ session('message') }}
                        </div>
                    @endif

                    @if (session()->has('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <input wire:model.live="search" type="text" class="form-control"
                                placeholder="Search customers...">
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="{{ route('customers.create') }}" class="btn btn-secondary"><i class="bi bi-plus"></i>Create</a>
                        </div>
                    </div>

                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($customers as $customer)
                                <tr>
                                    <td>{{ $customer->id }}</td>
                                    <td>{{ $customer->name }}</td>
                                    <td>{{ $customer->email }}</td>
                                    <td>{{ $customer->phone }}</td>
                                    <td>
                                        <a href="{{ route('customers.edit', $customer->id) }}"
                                            class="btn"><i class="bi bi-pencil-square"></i></a>
                                        <button wire:click="delete({{ $customer->id }})" class="btn"
                                            onclick="confirm('Are you sure you want to delete this customer?') || event.stopImmediatePropagation()"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{ $customers->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
