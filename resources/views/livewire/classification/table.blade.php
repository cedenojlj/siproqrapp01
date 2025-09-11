<div>
    <div class="container py-4">
        <div class="card">
            <div class="card-header">
                <h2>Gestión de Precios por Clasificación</h2>
            </div>
            <div class="card-body">
                @if (session()->has('message'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('message') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session()->has('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="mb-3">
                    <input wire:model.live="search" type="text" class="form-control" placeholder="Buscar por código, descripción o tamaño...">
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Descripción</th>
                                <th>Tamaño</th>
                                <th>Tipo Unidad</th>
                                <th>Precio_por_Unidad</th>
                                <th>Precio_por_Peso</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($classifications as $classification)
                                <tr wire:key="{{ $classification->id }}">
                                    <td>{{ $classification->code }}</td>
                                    <td>{{ $classification->description }}</td>
                                    <td>{{ $classification->size }}</td>
                                    <td>{{ $classification->unit_type }}</td>
                                    <td>
                                        <input type="number" class="form-control @error('preciosUnidad.'. $classification->id) is-invalid @enderror" wire:model.lazy="preciosUnidad.{{ $classification->id }}">
                                        @error('preciosUnidad.'. $classification->id)<span class="text-danger">{{ $message }}</span>@enderror
                                    </td>
                                    <td>
                                        <input type="number" class="form-control @error('preciosPeso.'. $classification->id) is-invalid @enderror" wire:model.lazy="preciosPeso.{{ $classification->id }}">
                                        @error('preciosPeso.'. $classification->id)<span class="text-danger">{{ $message }}</span>@enderror
                                    </td>
                                    <td>
                                        <button class="btn btn-primary btn-sm" wire:click="update({{ $classification->id }})" wire:loading.attr="disabled">
                                            <span wire:loading.remove wire:target="update({{ $classification->id }})">Guardar</span>
                                            <span wire:loading wire:target="update({{ $classification->id }})">Guardando...</span>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $classifications->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
