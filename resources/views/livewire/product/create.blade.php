<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Create New Product') }}</div>

                <div class="card-body">
                    @if (session()->has('message'))
                        <div class="alert alert-success">
                            {{ session('message') }}
                        </div>
                    @endif

                    @if (session()->has('qr_message'))
                        <div class="alert alert-success">
                            {{ session('qr_message') }}
                        </div>
                    @endif

                    @if (session()->has('qr_error'))
                        <div class="alert alert-danger">
                            {{ session('qr_error') }}
                        </div>
                    @endif

                    <form wire:submit.prevent="save">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" wire:model="name">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" wire:model="description"></textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="sku" class="form-label">SKU</label>
                            <input type="text" class="form-control @error('sku') is-invalid @enderror" id="sku" wire:model="sku">
                            @error('sku') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label">Price</label>
                            <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" id="price" wire:model="price">
                            @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="stock" class="form-label">Stock</label>
                            <input type="number" class="form-control @error('stock') is-invalid @enderror" id="stock" wire:model="stock">
                            @error('stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="classification_id" class="form-label">Classification</label>
                            <select class="form-select @error('classification_id') is-invalid @enderror" id="classification_id" wire:model="classification_id">
                                <option value="">Select Classification</option>
                                @foreach ($classifications as $classification)
                                    <option value="{{ $classification->id }}">{{ $classification->name }}</option>
                                @endforeach
                            </select>
                            @error('classification_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="warehouse_id" class="form-label">Warehouse</label>
                            <select class="form-select @error('warehouse_id') is-invalid @enderror" id="warehouse_id" wire:model="warehouse_id">
                                <option value="">Select Warehouse</option>
                                @foreach ($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                            @error('warehouse_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Save Product</button>
                    </form>

                    <hr class="my-4">

                    <h5>Scan QR Code to Fill Form</h5>
                    <div class="mb-3">
                        <!-- Placeholder for QR code scanner. In a real application, this would be a JavaScript-based scanner library -->
                        <textarea id="qr-data-input" class="form-control" rows="3" placeholder="Paste QR code data here or integrate a scanner..."></textarea>
                        <button type="button" class="btn btn-info mt-2" onclick="Livewire.dispatch('fillFormFromQrCode', document.getElementById('qr-data-input').value)">Fill from QR Data</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('qr-scanned-success', () => {
            const audio = new Audio('/sounds/scan-success.mp3'); // You'll need to provide this sound file
            audio.play();
        });
    });
</script>
@endpush