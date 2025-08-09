<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">{{ __('Order Details') }} #{{ $order->id }}</div>

                <div class="card-body">
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
                        <strong>Total Amount:</strong> {{ number_format($order->total_amount, 2) }}
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
                                <th>Quantity</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->orderProducts as $item)
                                <tr>
                                    <td>{{ $item->product->name }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ number_format($item->price, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="text-end">
                        <button wire:click="generatePdf" class="btn btn-primary">Generate PDF</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>