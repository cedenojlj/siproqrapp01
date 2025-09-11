<!DOCTYPE html>
<html>
<head>
    <title>Inventario por Clasificación</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h1>Inventario por Clasificación</h1>
    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Descripción</th>
                <th>Tamaño</th>
                <th>SKU Count</th>
                <th>Stock</th>
                <th>Tipo Unidad</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $item)
                <tr>
                    <td>{{ $item->code }}</td>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->size }}</td>
                    <td>{{ $item->sku_count }}</td>
                    <td>{{ $item->total_stock }}</td>
                    <td>{{ $item->unit_type }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No hay datos disponibles</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
