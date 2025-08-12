<div>

   
    <div class="input-group mb-3">
        {{-- <span class="input-group-text" id="basic-addon1">@</span> --}}
        <input type="text" wire:model.live="search" class="form-control mb-3" placeholder="buscar por nombre o tamaño">
    </div>

    @forelse ($codigos as $codigo)
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">{{ $codigo->code }}</h5>
                <h6 class="card-subtitle mb-2 text-body-secondary">{{ $codigo->size }}</h6>
                <p class="card-text">{{ $codigo->description }}</p>
                <button type="button" wire:click="agregarCodigo({{ $codigo->id }})" class="btn btn-secondary">Agregar</button>
            </div>
        </div>
    @empty

        <p>No se encontraron resultados.</p>
    @endforelse





</div>
