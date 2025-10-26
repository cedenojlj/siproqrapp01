<!DOCTYPE html>
<html>
<head>
    <title>Petition Details</title>
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
    </style>
</head>
<body>
    
    
    <h2>Report of Petition #{{ $petition->id }}</h2>
    <p><strong>Date:</strong> {{ $petition->created_at->format('d/m/Y H:i:s') }}</p>
    <p><strong>Customer:</strong> {{ $petition->customer->name }}</p>
    <p><strong>Status:</strong> {{ $petition->status }}</p>

    <h3>Products</h3>
    <table>
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
                    <td>{{ number_format($item->quantity, 2) }}</td>
                    <td>{{ number_format($item->price, 2) }}</td>
                    <td>{{ number_format($item->quantity * $item->price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-end"><strong>Total:</strong></td>
                <td>{{ number_format($petition->total, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- <div class="total">
        Total: {{ number_format($petition->total, 2) }}
    </div> --}}
</body>
</html>
