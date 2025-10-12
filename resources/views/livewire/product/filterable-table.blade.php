<div>
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-3">
                    <input wire:model.live="filterSku" type="text" class="form-control" placeholder="Buscar por SKU...">
                </div>
                <div class="col-md-3">
                    <input wire:model.live="filterInvoiceNumber" type="text" class="form-control" placeholder="Buscar por N° Factura...">
                </div>
                <div class="col-md-3">
                    <input wire:model.live="filterDate" type="date" class="form-control">
                </div>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>SKU</th>
                        <th>Producto</th>
                        <th>N° Factura</th>
                        <th>Fecha Creación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td>{{ $product->sku }}</td>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->invoice_number }}</td>
                            <td>{{ $product->created_at->format('d/m/Y') }}</td>
                            <td>
                                <button wire:click="downloadQrPdf({{ $product->id }})" class="btn btn-danger btn-sm">PDF</button>
                                {{-- <button wire:click="downloadQrImage({{ $product->id }})" class="btn btn-success btn-sm">Imagen</button> --}}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No se encontraron productos.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $products->links() }}
        </div>
    </div>
</div>
