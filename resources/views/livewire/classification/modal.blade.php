<div class="modal fade show" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: block;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ $classification_id ? 'Edit Classification' : 'Create New Classification' }}</h5>
                <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" wire:model="name">
                        @error('name') <span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="code" class="form-label">Code</label>
                        <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" wire:model="code">
                        @error('code') <span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" wire:model="description"></textarea>
                        @error('description') <span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="size" class="form-label">Size</label>
                        <input type="text" class="form-control @error('size') is-invalid @enderror" id="size" wire:model="size">
                        @error('size') <span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="precio_unidad" class="form-label">Unit Price</label>
                        <input type="number" step="0.01" class="form-control @error('precio_unidad') is-invalid @enderror" id="precio_unidad" wire:model="precio_unidad">
                        @error('precio_unidad') <span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="precio_peso" class="form-label">Weight Price</label>
                        <input type="number" step="0.01" class="form-control @error('precio_peso') is-invalid @enderror" id="precio_peso" wire:model="precio_peso">
                        @error('precio_peso') <span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="unit_type" class="form-label">Unit Type</label>
                        <input type="text" class="form-control @error('unit_type') is-invalid @enderror" id="unit_type" wire:model="unit_type">
                        @error('unit_type') <span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" wire:click="closeModal">Cancel</button>
                <button type="button" wire:click.prevent="store()" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>
<div class="modal-backdrop fade show"></div>
