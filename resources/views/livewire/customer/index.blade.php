<div class="container">
    <div class="row">
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
                        <div class="col-12 col-md-6">
                            <input wire:model.live="search" type="text" class="form-control"
                                placeholder="Search customers...">
                        </div>
                        <div class="col-12 col-md-6 text-end mt-sm-2">
                            <a href="{{ route('customers.create') }}" class="btn btn-secondary"><i
                                    class="bi bi-plus"></i>Create</a>
                        </div>
                    </div>

                    <div class="row table-responsive">
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
                                            @can('update customers')
                                                <a href="{{ route('customers.edit', $customer->id) }}" class="btn"><i
                                                        class="bi bi-pencil-square"></i></a>
                                            @endcan

                                            @can('delete customers')
                                                <button wire:click="delete({{ $customer->id }})" class="btn"><i
                                                        class="bi bi-trash"></i></button>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{ $customers->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
