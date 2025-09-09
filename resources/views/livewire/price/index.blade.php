
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

                        @can('create precios')
                            <div class="col-md-6 text-end">
                                <a href="{{ route('prices.create') }}" class="btn btn-secondary"><i
                                        class="bi bi-plus"></i>Create</a>
                            </div>
                        @endcan
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-responsive">
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
                                            @can('update precios')
                                                <a href="{{ route('prices.edit', $price->id) }}" class="btn"><i
                                                        class="bi bi-pencil-square"></i></a>
                                            @endcan
                                            @can('delete precios')
                                                <button wire:click="delete({{ $price->id }})" class="btn"><i
                                                        class="bi bi-trash"></i></button>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{ $prices->links() }}
                </div>
            </div>
        </div>
    </div>

