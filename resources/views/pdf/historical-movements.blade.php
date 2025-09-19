<!DOCTYPE html>
<html>
<head>
    <title>Historical Movements Report</title>
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
    <h1>Historical Movements Report</h1>

    <p><strong>Filters:</strong></p>
    <ul>
        <li>Product: {{ $filters['product'] }}</li>
        <li>Warehouse: {{ $filters['warehouse'] }}</li>
        <li>Type: {{ $filters['type'] }}</li>
        <li>Start Date: {{ $filters['startDate'] ?? 'N/A' }}</li>
        <li>End Date: {{ $filters['endDate'] ?? 'N/A' }}</li>
    </ul>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Product</th>
                <th>Size</th>
                <th>Warehouse</th>
                <th>Quantity</th>
                <th>Type</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($movements as $movement)
                <tr>
                    <td>{{ $movement->created_at->format('Y-m-d H:i:s') }}</td>
                    <td>{{ $movement->product->name }}</td>
                    <td>{{ $movement->product->size }}</td>
                    <td>{{ $movement->warehouse->name }}</td>
                    <td>{{ $movement->quantity }}</td>
                    <td>{{ ucfirst($movement->type) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No movements found matching the criteria.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
