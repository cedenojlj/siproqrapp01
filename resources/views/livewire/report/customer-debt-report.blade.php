<div>
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-md-8">
                <h2>Reporte de Deudas por Cliente</h2>
            </div>
            <div class="col-md-4 text-right">
                <button wire:click="generatePdf" class="btn btn-danger">
                    <i class="fas fa-file-pdf"></i> Generar PDF
                </button>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <input wire:model.live.debounce.300ms="search" type="text" class="form-control" placeholder="Buscar cliente por nombre o email...">
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="thead-dark">
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
                                    <td colspan="5" class="text-center">No hay clientes con deudas.</td>
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
                <div class="mt-3">
                    {{ $customers->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
