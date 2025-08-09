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
    </style>
</head>
<body>
    <h1>Order #{{ $order->id }}</h1>

    <p><strong>Customer:</strong> {{ $order->customer->name }}</p>
    <p><strong>Warehouse:</strong> {{ $order->warehouse->name }}</p>
    <p><strong>Order Type:</strong> {{ ucfirst($order->order_type) }}</p>
    <p><strong>Status:</strong> {{ $order->status }}</p>

    <h2>Products</h2>
    <table>
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

    <div class="total">
        Total Amount: {{ number_format($order->total_amount, 2) }}
    </div>
</body>
</html>
