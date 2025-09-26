<div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Detalle de Inventario por Clasificación</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Almacén</label>
                        <select wire:model.live="warehouseId" class="form-control">
                            <option value="">Todos</option>
                            @foreach ($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Código</label>
                        <input wire:model.live="code" type="text" class="form-control"
                            placeholder="Filtrar por código">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Tamaño</label>
                        <input wire:model.live="size" type="text" class="form-control"
                            placeholder="Filtrar por tamaño">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Descripción</label>
                        <input wire:model.live="description" type="text" class="form-control"
                            placeholder="Filtrar por descripción">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>SKU</label>
                        <input wire:model.live="sku" type="text" class="form-control" placeholder="Filtrar por SKU">
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-12">
                    <button wire:click="exportPdf" class="btn btn-primary">Exportar a PDF</button>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Descripción</th>
                                <th>Tamaño</th>
                                <th>SKU</th>
                                <th>Stock</th>
                                <th>Tipo Unidad</th>
                                <th>G.N.</th>
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
                                <th colspan="4" class="text-right"><strong>Total:</strong></th>
                                <th>{{ $data->sum('stock') }}</th>
                                <th></th>
                                <th>{{ $data->sum('GN') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
