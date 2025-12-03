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

        th,
        td {
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

    <h2>Report of Petition #{{ $petition->id }}</h2>
    <p><strong>Date:</strong> {{ $petition->created_at->format('d/m/Y H:i:s') }}</p>
    {{-- <p><strong>Customer:</strong> {{ $petition->customer->name }}</p> --}}
    {{-- cooca nombre de usuario --}}
    <p><strong>User:</strong> {{ $petition->user->name }}</p>
    <p><strong>Status:</strong> {{ $petition->status }}</p>

    <h3>Products</h3>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Size</th>
                <th>Quantity</th>
                {{-- <th>Price</th> --}}
                {{-- <th>Subtotal</th> --}}
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

    {{-- <div class="total">
        Total: {{ number_format($petition->total, 2) }}
    </div> --}}
</body>

</html>
