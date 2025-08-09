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
    <h1>Petition #{{ $petition->id }}</h1>

    <p><strong>Customer:</strong> {{ $petition->customer->name }}</p>
    <p><strong>Status:</strong> {{ $petition->status }}</p>

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
            @foreach ($petition->petitionProducts as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        Total Amount: {{ number_format($petition->total_amount, 2) }}
    </div>
</body>
</html>
