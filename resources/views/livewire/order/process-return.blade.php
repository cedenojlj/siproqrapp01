<div>
    <div class="container py-4">
        <div class="card">
            <div class="card-header">
                <h3>Asistente de Devolución para la Orden #{{ $order->id }}</h3>
            </div>
            <div class="card-body">

                {{-- STEP 1: Input de Cantidades --}}
                @if ($step == 1)
                    <h5 class="card-title">Paso 1: Introduce las cantidades a devolver</h5>
                    <p>Para cada producto, introduce el número de unidades que el cliente está devolviendo.</p>
                    
                    @if (session()->has('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-center">Cantidad en Orden</th>
                                    <th style="width: 150px;">Cantidad a Devolver</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->orderProducts as $orderProduct)
                                    <tr>
                                        <td>{{ $orderProduct->product->name }}</td>
                                        <td class="text-center">{{ $orderProduct->quantity }}</td>
                                        <td>
                                            <input type="number" class="form-control"
                                                   wire:model.lazy="devoluciones.{{ $orderProduct->id }}"
                                                   min="0"
                                                   max="{{ $orderProduct->quantity }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button wire:click="goToStep2" wire:loading.attr="disabled" class="btn btn-primary">
                            <span wire:loading.remove>Continuar</span>
                            <span wire:loading>Procesando...</span>
                        </button>
                    </div>

                {{-- STEP 2: Confirmación --}}
                @elseif ($step == 2)
                    <h5 class="card-title">Paso 2: Confirma la devolución</h5>
                    <p>Por favor, revisa los detalles de la devolución antes de confirmar. Esta acción es irreversible.</p>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-center">Cantidad a Devolver</th>
                                    <th class="text-right">Precio Unitario</th>
                                    <th class="text-right">Subtotal a Acreditar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($summary['items'] as $item)
                                    <tr>
                                        <td>{{ $item['name'] }}</td>
                                        <td class="text-center">{{ $item['quantity'] }}</td>
                                        <td class="text-right">${{ number_format($item['price'], 2) }}</td>
                                        <td class="text-right">${{ number_format($item['subtotal'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-right"><strong>Total a acreditar al cliente:</strong></td>
                                    <td class="text-right"><strong>${{ number_format($summary['total_credito'], 2) }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="alert alert-info">
                        <strong>Nota:</strong> Al confirmar, el stock de los productos devueltos se incrementará y se actualizará el total de la orden. Se añadirá un crédito de <strong>${{ number_format($summary['total_credito'], 2) }}</strong> al balance del cliente.
                    </div>

                    <div class="d-flex justify-content-between">
                        <button wire:click="goToStep1" class="btn btn-secondary">Volver</button>
                        <button wire:click="confirmReturn" wire:loading.attr="disabled" class="btn btn-success">
                            <span wire:loading.remove>Confirmar Devolución</span>
                            <span wire:loading>Confirmando...</span>
                        </button>
                    </div>

                {{-- STEP 3: Éxito --}}
                @elseif ($step == 3)
                    <div class="text-center">
                        <h5 class="card-title">Paso 3: Devolución Procesada con Éxito</h5>
                        <div class="alert alert-success">
                            La devolución se ha procesado correctamente. El stock y los totales de la orden han sido actualizados.
                        </div>
                        <a href="{{ route('orders.show', $order) }}" class="btn btn-primary">Volver a la Orden</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>