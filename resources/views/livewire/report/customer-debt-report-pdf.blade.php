<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Deudas por Cliente</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 12px;
        }
        .container {
            width: 100%;
            margin: 0 auto;
        }
        .header, .footer {
            width: 100%;
            text-align: center;
            position: fixed;
        }
        .header {
            top: 0px;
        }
        .footer {
            bottom: 0px;
        }
        .content {
            margin-top: 100px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .font-weight-bold {
            font-weight: bold;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Deudas por Cliente</h1>
        <p>Fecha de Generación: {{ date('d/m/Y H:i') }}</p>
    </div>

    <div class="content">
        <table>
            <thead>
                <tr>
                    <th>ID Cliente</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th class="text-right">Deuda Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($customers as $customer)
                    <tr>
                        <td>{{ $customer->id }}</td>
                        <td>{{ $customer->name }}</td>
                        <td>{{ $customer->email }}</td>
                        <td>{{ $customer->phone }}</td>
                        <td class="text-right font-weight-bold">${{ number_format($customer->total_debt, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center;">No hay clientes con deudas.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-right font-weight-bold">Deuda Total General:</td>
                    <td class="text-right font-weight-bold">${{ number_format($grandTotalDebt, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="footer">
        <p>SIPROQR APP - Página <script type="text/php">echo $pdf->get_page_number();</script></p>
    </div>
</body>
</html>
