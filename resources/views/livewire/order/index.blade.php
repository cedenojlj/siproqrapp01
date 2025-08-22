
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Orders') }}</div>

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
                            <input wire:model.debounce.300ms="search" type="text" class="form-control" placeholder="Search orders...">
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="{{ route('orders.create') }}" class="btn btn-primary">Create Order</a>
                        </div>
                    </div>

                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Warehouse</th>
                                <th>Type</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr>
                                    <td>{{ $order->id }}</td>
                                    <td>{{ $order->customer->name }}</td>
                                    <td>{{ $order->warehouse->name }}</td>
                                    <td>{{ ucfirst($order->order_type) }}</td>
                                    <td>{{ number_format($order->total, 2) }}</td>
                                    <td>{{ $order->status }}</td>
                                    <td>
                                        <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-info">View</a>
                                        <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-sm btn-secondary">Edit</a>
                                        <button wire:click="delete({{ $order->id }})" class="btn btn-sm btn-danger" onclick="confirm('Are you sure you want to delete this order?') || event.stopImmediatePropagation()">Delete</button>
                                        <button wire:click="borrar({{ $order->id }})" class="btn btn-sm btn-warning" onclick="confirm('Are you sure you want to delete this order?') || event.stopImmediatePropagation()">Borrar</button>   
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
