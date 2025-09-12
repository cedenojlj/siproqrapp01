<div>
    <div class="container py-4">
        <div class="card">
            <div class="card-header">
                <h3>Actualizar Precios por Cliente y Clasificación</h3>
            </div>
            <div class="card-body">
                @if (session()->has('message'))
                    <div class="alert alert-success">
                        {{ session('message') }}
                    </div>
                @endif
                @if (session()->has('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="row mb-4">
                    <div class="col-md-12">
                        <input wire:model.live.debounce.300ms="search" type="text" class="form-control" placeholder="Buscar por cliente, código o descripción de la clasificación...">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th>Cliente</th>
                                <th>Código</th>
                                <th>Descripción</th>
                                <th style="width: 150px;">Nuevo Precio Unit.</th>
                                <th style="width: 150px;">Nuevo Precio Peso</th>
                                <th style="width: 100px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $item)
                                <tr wire:key="{{ $item->customer_id }}-{{ $item->classification_id }}">
                                    <td>{{ $item->customer_name }}</td>
                                    <td>{{ $item->classification_code }}</td>
                                    <td>{{ $item->classification_description }}</td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control"
                                               wire:model="inputs.{{ $item->customer_id }}.{{ $item->classification_id }}.price_quantity"
                                               placeholder="0.00">
                                        @error('inputs.' . $item->customer_id . '.' . $item->classification_id . '.price_quantity') <span class="text-danger">{{ $message }}</span> @enderror
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control"
                                               wire:model="inputs.{{ $item->customer_id }}.{{ $item->classification_id }}.price_weight"
                                               placeholder="0.00">
                                        @error('inputs.' . $item->customer_id . '.' . $item->classification_id . '.price_weight') <span class="text-danger">{{ $message }}</span> @enderror
                                    </td>
                                    <td class="text-center">
                                        <button wire:click="save({{ $item->customer_id }}, {{ $item->classification_id }})" class="btn btn-sm btn-primary">
                                            <span wire:loading.remove wire:target="save({{ $item->customer_id }}, {{ $item->classification_id }})">Actualizar</span>
                                            <span wire:loading wire:target="save({{ $item->customer_id }}, {{ $item->classification_id }})">...</span>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No se encontraron registros.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $data->links() }}
                </div>
            </div>
        </div>
    </div>
</div>