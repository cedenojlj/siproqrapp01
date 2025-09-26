<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">{{ __('Generate Product QR Codes') }}</div>

                <div class="card-body">
                    @if (session()->has('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="mb-3">
                        <button wire:click="generatePdf" class="btn btn-secondary">Generate PDF for Selected Products</button>
                    </div>

                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Select</th>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Size</th>
                                <th>SKU</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr>
                                    <td>
                                        <input type="checkbox" wire:model="selectedProducts" value="{{ $product->id }}">
                                    </td>
                                    <td>{{ $product->id }}</td>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->size }}</td>
                                    <td>{{ $product->sku }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                     {{-- {{ $products->links() }} --}}
                </div>
            </div>
        </div>
    </div>
</div>