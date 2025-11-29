<!DOCTYPE html>
<html>

<head>
    <title>Inventario por Clasificación</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
    </style>
</head>

<body>
    <img src="{{public_path('img/logoMejorado.jpg')}}" alt="" srcset="" width="200px">
    <h1>Inventario por Clasificación</h1>

    <p>Fecha: {{ now()->format('d/m/Y H:i') }}</p>

    <p>Almacén: {{ $warehouse->name ?? 'Todos los almacenes' }}</p>

    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Descripción</th>
                <th>Tamaño</th>
                <th>SKU Count</th>
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
                    <td>{{ $item->sku_count }}</td>
                    <td>{{ $item->total_stock }}</td>
                    <td>{{ $item->unit_type }}</td>
                    <td>{{ $item->total_gn }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No hay datos disponibles</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-right">Total:</th>
                <th>{{ $data->sum('sku_count') }}</th>
                <th>{{ $data->sum('total_stock') }}</th>
                <th></th>
                <th>{{ $data->sum('total_gn') }}</th>
            </tr>
        </tfoot>
    </table>
</body>

</html>
