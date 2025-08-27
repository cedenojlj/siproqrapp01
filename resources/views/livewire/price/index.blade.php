<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Prices') }}</div>

                <div class="card-body">
                    @if (session()->has('message'))
                        <div class="alert alert-success">
                            {{ session('message') }}
                        </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <input wire:model.live="search" type="text" class="form-control"
                                placeholder="Search by product or customer...">
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="{{ route('prices.create') }}" class="btn btn-secondary"><i class="bi bi-plus"></i>Create</a>
                        </div>
                    </div>

                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Product</th>
                                <th>Customer</th>
                                <th>Price Quantity</th>
                                <th>Price Weight</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($prices as $price)
                                <tr>
                                    <td>{{ $price->id }}</td>
                                    <td>{{ $price->product->name }}</td>
                                    <td>{{ $price->customer->name }}</td>
                                    <td>{{ $price->price_quantity }}</td>
                                    <td>{{ $price->price_weight }}</td>
                                    <td>
                                        <a href="{{ route('prices.edit', $price->id) }}"
                                            class="btn"><i class="bi bi-pencil-square"></i></a>
                                        <button wire:click="delete({{ $price->id }})" class="btn"
                                            onclick="confirm('Are you sure you want to delete this price?') || event.stopImmediatePropagation()"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{ $prices->links() }}
                </div>
            </div>
        </div>
    </div>
</div>