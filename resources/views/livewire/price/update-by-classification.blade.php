<div>
    <div class="container py-4">
        <div class="card">
            <div class="card-header">
                <h3>Actualizar Precios de Productos por Clasificación</h3>
            </div>
            <div class="card-body">
                <form wire:submit.prevent="updatePrices">
                    @csrf
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label for="classification" class="form-label">Clasificación</label>
                            <select wire:model.lazy="selectedClassification" id="classification" class="form-select">
                                <option value="">Seleccione una clasificación</option>
                                @foreach($classifications as $classification)
                                    <option value="{{ $classification->id }}">{{ $classification->code }} - {{ $classification->description }}</option>
                                @endforeach
                            </select>
                            @error('selectedClassification') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="precio_unidad" class="form-label">Nuevo Precio por Unidad</label>
                            <input wire:model.lazy="precio_unidad" type="number" step="0.01" class="form-control" id="precio_unidad" placeholder="Ej: 12.50">
                            @error('precio_unidad') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="precio_peso" class="form-label">Nuevo Precio por Peso</label>
                            <input wire:model.lazy="precio_peso" type="number" step="0.01" class="form-control" id="precio_peso" placeholder="Ej: 25.75">
                            @error('precio_peso') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <hr>

                    <h5 class="mt-4">Seleccionar Clientes</h5>
                    @error('selectedCustomers') <div class="alert alert-danger">{{ $message }}</div> @enderror

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>
                                        <!-- Podrías agregar aquí una funcionalidad para seleccionar/deseleccionar todos -->
                                    </th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Teléfono</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customers as $customer)
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="{{ $customer->id }}" wire:model.lazy="selectedCustomers" id="customer-{{ $customer->id }}">
                                            </div>
                                        </td>
                                        <td>{{ $customer->name }}</td>
                                        <td>{{ $customer->email }}</td>
                                        <td>{{ $customer->phone }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No se encontraron clientes activos.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary">
                            <span wire:loading.remove wire:target="updatePrices">Actualizar Precios</span>
                            <span wire:loading wire:target="updatePrices">Actualizando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>