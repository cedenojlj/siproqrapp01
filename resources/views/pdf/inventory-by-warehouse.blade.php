<!DOCTYPE html>
<html>
<head>
    <title>Inventory Report</title>
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
    </style>
</head>
<body>
    <img src="{{public_path('img/logoMejorado.jpg')}}" alt="" srcset="" width="200px">
    
    <h1>Inventory Report - {{ $warehouseName }}</h1>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Size</th>
                <th>Warehouse</th>
                <th>Stock</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($inventory as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->product->size }}</td>
                    <td>{{ $item->warehouse->name }}</td>
                    <td>{{ $item->stock }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">No inventory data found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
