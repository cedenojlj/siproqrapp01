<!DOCTYPE html>
<html>
<head>
    <title>Debt Report</title>
    <style>
        body {
            font-family: sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
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

    <h1>Debt Report</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Fecha</th>
                <th>Customer</th>
                <th>Warehouse</th>
                <th>Type</th>
                <th>Total Amount</th>
                <th>Payment Status</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->created_at->format('d/m/Y H:i:s') }}</td>
                    <td>{{ $order->customer->name }}</td>
                    <td>{{ $order->warehouse->name }}</td>
                    <td>{{ ucfirst($order->order_type) }}</td>
                    <td>${{ number_format($order->monto_pagado, 2) }} / ${{ number_format($order->total, 2) }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}</td>
                    <td>{{ $order->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="total">
        <p>Total Pending: ${{ number_format($totalPending, 2) }}</p>
    </div>
</body>
</html>
