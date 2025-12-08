<!DOCTYPE html>
<html>
<head>
    <title>Order Details</title>
    <style>
        body {
            font-family: sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .total {
            text-align: right;
            margin-top: 20px;
            font-size: 1.2em;
            font-weight: bold;
        }
        .text-end {
            text-align: right;
        }
    </style>
</head>
<body>

    <div class="text-end">
        <img src="{{public_path('img/logoMejorado.jpg')}}" alt="" srcset="" width="200px"> 
    </div>
      
  
    <h2>Order #{{ $order->id }}</h2>

    <p><strong>Date:</strong> {{ $order->created_at->format('d/m/Y H:i:s') }}</p>
    <p><strong>Customer:</strong> {{ $order->customer->name }}</p>
    <p><strong>Warehouse:</strong> {{ $order->warehouse->name }}</p>
    <p><strong>Order Type:</strong> {{ ucfirst($order->order_type) }}</p>
    <p><strong>Status:</strong> {{ $order->status }}</p>

    <h2>Products</h2>
    <table>
        <thead>
            <tr>
                <th>SKU</th>
                <th>Prod</th>
                <th>Size</th>
                <th>Qty</th>
                <th>NW</th>
                <th>Price</th>
                <th>Price2</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalWeight = 0;
            @endphp
            @foreach ($order->orderProducts as $item)
                @php
                    $totalWeight += $item->product->GN * $item->quantity;
                @endphp
                <tr>
                    <td>{{ $item->product->sku }}</td>
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
                <td colspan="7" class="text-end"><strong>Total:</strong></td>
                <td>${{ number_format($order->total, 2) }}</td>
            </tr>

            <tr>
                <td colspan="7" class="text-end"><strong>Total Weight:</strong></td>
                <td>{{ number_format($totalWeight, 2) }}</td>
            </tr>
        </tfoot>
    </table>


   {{--  <div class="total">
        Total Amount: {{ number_format($order->total, 2) }}
    </div> --}}
</body>
</html>
