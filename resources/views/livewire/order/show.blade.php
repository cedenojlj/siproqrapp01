<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">{{ __('Order Details') }} #{{ $order->id }}</div>

                <div class="card-body">
                    <div class="mb-3">
                        <strong>Fecha:</strong> {{ $order->created_at->format('d/m/Y H:i:s') }}
                    </div>
                    
                    <div class="mb-3">
                        <strong>Customer:</strong> {{ $order->customer->name }}
                    </div>
                    <div class="mb-3">
                        <strong>Warehouse:</strong> {{ $order->warehouse->name }}
                    </div>
                    <div class="mb-3">
                        <strong>Order Type:</strong> {{ ucfirst($order->order_type) }}
                    </div>
                    <div class="mb-3">
                        <strong>Total Amount:</strong> {{ number_format($order->total, 2) }}
                    </div>
                    <div class="mb-3">
                        <strong>Status:</strong> {{ $order->status }}
                    </div>

                    <hr>

                    <h5>Products in Order</h5>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Size</th>
                                <th>Quantity</th>
                                <th>NW</th>
                                <th>Price</th>
                                <th>Price2</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->orderProducts as $item)
                                <tr>
                                    <td>{{ $item->product->name }}</td>
                                    <td>{{ $item->product->size }}</td>
                                    <td>{{ number_format($item->quantity, 2) }}</td>
                                    <td>{{ $item->product->GN }}</td>
                                    <td>{{ number_format($item->price, 2) }}</td>
                                    
                                    @if ($item->product->classification->unit_type == 'Peso')

                                        <td>{{ number_format($item->price / $item->product->GN, 2) }}</td>  

                                    @else

                                        <td>{{ number_format($item->price, 2) }}</td>
                                        
                                    @endif   

                                    <td>{{ number_format($item->quantity * $item->price, 2) }}</td>
                                    
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                <td>{{ number_format($order->total, 2) }}</td>
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