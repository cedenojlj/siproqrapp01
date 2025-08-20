<div>
    
    <div class="input-group mb-3">
        {{-- <span class="input-group-text" id="basic-addon1">@</span> --}}
        <input type="text" wire:model.live="search" class="form-control mb-3" placeholder="buscar por nombre o tamaño">
    </div>

       
    @forelse ($productos as $producto)
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">{{ $producto->sku }}</h5>
                <h6 class="card-subtitle mb-2 text-body-secondary">{{ $producto->type.'-'.$producto->size }}</h6>
                <p class="card-text">{{ $producto->name }}</p>
                <button type="button" wire:click="agregarProducto({{ $producto->id }})" class="btn btn-secondary">Agregar</button>
            </div>
        </div>
    @empty

        <p>No se encontraron resultados.</p>
    @endforelse
</div>

