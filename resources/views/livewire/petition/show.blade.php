<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">{{ __('Petition Details') }} #{{ $petition->id }}</div>

                <div class="card-body">

                    <div class="mb-3">
                        <strong>Fecha:</strong> {{ $petition->created_at->format('d/m/Y H:i:s') }}
                    </div>
                    <div class="mb-3">
                        <strong>User:</strong> {{ $petition->user->name }}
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
                               {{--  <th>Price</th>
                                <th>Subtotal</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($petition->petitionClassifications as $item)
                                <tr>
                                    <td>{{ $item->classification->name }}</td>
                                    <td>{{ $item->classification->size }}</td>
                                    <td>{{ number_format($item->quantity, 2) }}</td>
                                    {{-- <td>{{ number_format($item->price, 2) }}</td>
                                    <td>{{ number_format($item->quantity * $item->price, 2) }}</td> --}}
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2" class="text-end"><strong>Total:</strong></td>
                                <td>{{ number_format($petition->petitionClassifications->sum('quantity'), 2) }}</td>
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