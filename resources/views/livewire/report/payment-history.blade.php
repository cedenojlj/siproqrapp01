<div class="row justify-content-start">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">{{ __('Historial de Abonos') }}</div>

            <div class="card-body">

                <div class="row mb-3">
                    <div class="col-md-3 mb-sm-2">
                        <label for="search_order"># Orden</label>
                        <input wire:model.live="search_order" type="text" class="form-control"
                            placeholder="Buscar por # de orden...">
                    </div>
                    <div class="col-md-3 mb-sm-2">
                        <label for="customer_id">Cliente</label>
                        <select wire:model.live="customer_id" class="form-select">
                            <option value="">Todos los Clientes</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-sm-2">
                       <label for="fechaInicio">Fecha Inicio</label>
                        <input wire:model.live="fechaInicio" type="date" class="form-control">
                    </div>
                    <div class="col-md-3 mb-sm-2">
                       <label for="fechaFin">Fecha Fin</label>
                        <input wire:model.live="fechaFin" type="date" class="form-control">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <button wire:click="exportPdf" class="btn btn-danger"><i class="bi bi-file-pdf"></i> Exportar a PDF</button>
                    </div>
                    <div class="col-md-6 text-end">
                        <h4>Total Abonado: <span class="badge bg-success">${{ number_format($totalAbonado, 2) }}</span></h4>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Fecha Abono</th>
                                <th># Orden</th>
                                <th>Cliente</th>
                                <th>Monto Abonado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($paymentApplications as $application)
                                <tr>
                                    <td>{{ $application->payment->fecha_pago }}</td>
                                    <td>
                                        <a href="{{ route('orders.show', $application->order_id) }}">{{ $application->order_id }}</a>
                                    </td>
                                    <td>{{ $application->payment->customer->name }}</td>
                                    <td>${{ number_format($application->monto_aplicado, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No hay registros para mostrar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $paymentApplications->links() }}
            </div>
        </div>
    </div>
</div>