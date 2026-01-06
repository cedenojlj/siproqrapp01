<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Abonos</title>
    <style>
        body {
            font-family: sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
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
            font-weight: bold;
            font-size: 1.2em;
            text-align: right;
            padding-top: 10px;
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
    <h1>Historial de Abonos</h1>
    <table>
        <thead>
            <tr>
                <th>Fecha Abono</th>
                <th># Orden</th>
                <th>Cliente</th>
                <th>Monto Abonado</th>
                <th>Metodo de Pago</th>
                <th>Notas</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($paymentApplications as $application)
                <tr>
                    <td>{{ $application->payment->fecha_pago }}</td>
                    <td>{{ $application->order_id }}</td>
                    <td>{{ $application->payment->customer->name }}</td>
                    <td>${{ number_format($application->monto_aplicado, 2) }}</td>
                    <td>{{ $application->payment->metodo_pago }}</td>
                    <td>{{ $application->payment->notas }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center;">No hay registros para mostrar.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="total">
        Total Abonado: ${{ number_format($totalAbonado, 2) }}
    </div>
</body>
</html>
