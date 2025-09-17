<!DOCTYPE html>
<html>
<head>
    <title>Detailed Report</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f2f2f2; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <h1>Detailed Report</h1>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Product</th>
                <th>Warehouse</th>
                <th>Type</th>
                <th>Quantity</th>
                <th>Customer Name</th>
                <th>Size</th>
                <th>GN</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($movements as $movement)
                <tr>
                    <td>{{ $movement->created_at->format('Y-m-d H:i') }}</td>
                    <td>{{ $movement->product_name }}</td>
                    <td>{{ $movement->warehouse_name }}</td>
                    <td>{{ ucfirst($movement->type) }}</td>
                    <td>{{ $movement->quantity }}</td>
                    <td>{{ $movement->customer_name }}</td>
                    <td>{{ $movement->product_size }}</td>
                    <td>{{ $movement->product_gn }}</td>
                    <td>{{ number_format($movement->subtotal, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center">No movements found.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="8" class="text-right"><strong>Total:</strong></td>
                <td><strong>{{ number_format($totalSubtotal, 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
