<!DOCTYPE html>
<html>
<head>
    <title>Detalle de Inventario por Clasificación</title>
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
    <h1>Detalle de Inventario por Clasificación</h1>

    <p>Fecha: {{ now()->format('d/m/Y H:i') }}</p>

    <p>Almacén: {{ $warehouse->name ?? 'Todos los almacenes' }}</p>
    
    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Descripción</th>
                <th>Tamaño</th>
                <th>SKU</th>
                <th>Stock</th>
                <th>Tipo Unidad</th>
                <th>NW</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $item)
                <tr>
                    <td>{{ $item->code }}</td>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->size }}</td>
                    <td>{{ $item->sku }}</td>
                    <td>{{ $item->stock }}</td>
                    <td>{{ $item->unit_type }}</td>
                    <td>{{ $item->GN }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No hay datos disponibles</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot> 
            <tr>
                <td colspan="4" class="text-right"><strong>Total:</strong></td>
                <td>{{ $data->sum('stock') }}</td>
                <td></td>
                <td>{{ $data->sum('GN') }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
