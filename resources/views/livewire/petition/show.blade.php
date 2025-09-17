<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">{{ __('Petition Details') }} #{{ $petition->id }}</div>

                <div class="card-body">
                    <div class="mb-3">
                        <strong>Customer:</strong> {{ $petition->customer->name }}
                    </div>
                    <div class="mb-3">
                        <strong>Total Amount:</strong> {{ number_format($petition->total, 2) }}
                    </div>
                    <div class="mb-3">
                        <strong>Status:</strong> {{ $petition->status }}
                    </div>

                    <hr>

                    <h5>Products in Petition</h5>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Size</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($petition->petitionProducts as $item)
                                <tr>
                                    <td>{{ $item->product->name }}</td>
                                    <td>{{ $item->product->size }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ number_format($item->price, 2) }}</td>
                                    <td>{{ number_format($item->quantity * $item->price, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                <td>{{ number_format($petition->total, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>

                    <div class="text-end">
                        <button wire:click="generatePdf" class="btn btn-primary">Generate PDF</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>