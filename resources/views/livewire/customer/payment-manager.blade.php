<div>
    @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <form wire:submit.prevent="applyPayment">
        <div class="card">
            <div class="card-header">
                <h4>Registrar Abono de Cliente</h4>
            </div>
            <div class="card-body">
                <!-- Selector de Cliente (se puede mejorar con un buscador) -->
                <div class="form-group">
                    <label for="customer">Cliente</label>
                    <select id="customer" wire:model.live="customer_id" class="form-control">
                        <option value="">-- Seleccione un cliente --</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>
                    @error('customer_id') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                @if($selectedCustomer)
                    <div class="mt-3">
                        <p><strong>Saldo a favor actual:</strong> <span class="badge text-bg-success">${{ number_format($selectedCustomer->credit_balance, 2) }}</span></p>

                        <p><strong>Deuda total actual:</strong> <span class="badge text-bg-warning">${{ number_format($selectedCustomer->orders->sum('deuda'), 2) }}</span></p>

                        <hr>
                        <h5>Órdenes Pendientes:</h5>
                        @if($selectedCustomer->orders->isNotEmpty())
                            <ul class="list-group">
                                @foreach($selectedCustomer->orders as $order)
                                    <li class="list-group-item">
                                        Orden #{{ $order->id }} ({{ $order->created_at->format('d/m/Y H:i:s') }}) <span class="badge text-bg-warning">Deuda: ${{ number_format($order->deuda, 2) }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p>Este cliente no tiene órdenes pendientes.</p>
                        @endif
                    </div>
                @endif

                <hr>

                <!-- Campos del Abono -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="monto_abono">Monto del Abono</label>
                            <input type="number" step="0.01" id="monto_abono" wire:model="monto_abono" class="form-control">
                            @error('monto_abono') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="fecha_pago">Fecha del Pago</label>
                            <input type="date" id="fecha_pago" wire:model="fecha_pago" class="form-control">
                            @error('fecha_pago') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="metodo_pago">Metodo de Pago</label>
                            <select id="metodo_pago" wire:model="metodo_pago" class="form-control">
                                <option value="">-- Seleccione un Metodo --</option>
                                <option value="Efectivo">Efectivo</option>
                                <option value="Transferencia">Transferencia</option>
                                <option value="Pago_Movil">Pago Movil</option>
                                <option value="Zelle">Zelle</option>
                                <option value="Divisa">Divisa</option>
                                <option value="Euro">Banesco Panama</option>
                            </select>
                            @error('metodo_pago') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <div class="form-group mt-3">
                    <label for="notas">Notas (Opcional)</label>
                    <textarea id="notas" wire:model="notas" class="form-control"></textarea>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="applyPayment">Aplicar Pago</span>
                    <span wire:loading wire:target="applyPayment">Procesando...</span>
                </button>
            </div>
        </div>
    </form>
</div>