<div>
    <div class="container py-4">
        {{-- Título y Navegación --}}
        <div class="row align-items-center mb-4">
            <div class="col-md-8">
                <h1>Editar Productos de la Orden #{{ $order->id }}</h1>
                <p class="text-muted">Cliente: {{ $order->customer->name ?? 'N/A' }}</p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="{{ route('orders.return', $order) }}" class="btn btn-info me-2">Procesar Devolución</a>
                <a href="{{ route('orders.show', $order) }}" class="btn btn-secondary">Volver a la Orden</a>
            </div>
        </div>

        {{-- Alertas de Sesión --}}
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session()->has('message'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Contenedor Principal --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Productos en la Orden</h5>
                @if(count($order->products) > 0)
                    <button type="button" 
                            class="btn btn-danger" 
                            wire:click="eliminarProductosSeleccionados"
                            wire:loading.attr="disabled"
                            onclick="return confirm('¿Estás seguro? Esta acción eliminará los productos seleccionados de la orden y revertirá el movimiento de inventario.')">
                        <span wire:loading.remove wire:target="eliminarProductosSeleccionados">
                            <i class="fas fa-trash-alt"></i> Eliminar Seleccionados
                        </span>
                        <span wire:loading wire:target="eliminarProductosSeleccionados">
                            <i class="fas fa-spinner fa-spin"></i> Procesando...
                        </span>
                    </button>
                @endif
            </div>

            <div class="card-body">
                @if(count($order->products) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;"></th>
                                    <th>Producto</th>
                                    <th class="text-end">Cantidad</th>
                                    <th class="text-end">Precio Unit.</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->products as $product)
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       wire:model.defer="productosParaEliminar" 
                                                       value="{{ $product->pivot->id }}">
                                            </div>
                                        </td>
                                        <td>{{ $product->name }}</td>
                                        <td class="text-end">{{ $product->pivot->quantity }}</td>
                                        <td class="text-end">${{ number_format($product->pivot->price, 2) }}</td>
                                        <td class="text-end">${{ number_format($product->pivot->quantity * $product->pivot->price, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">Subtotal:</td>
                                    <td class="text-end fw-bold">${{ number_format($order->subtotal, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">Total:</td>
                                    <td class="text-end fw-bold">${{ number_format($order->total, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="text-center p-4">
                        <p class="text-muted">No hay productos en esta orden o ya han sido eliminados todos.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>