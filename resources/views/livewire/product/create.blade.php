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

                    

                    <form class="row g-1" wire:submit.prevent="save">
                        <div class="col-md-12 ">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" wire:model="name">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-12">
                            <label for="type" class="form-label">Type</label>
                            <input type="text" class="form-control @error('type') is-invalid @enderror" id="type" wire:model.blur="type">
                            @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-12">
                            <livewire:Product.ModalProduct />
                        </div>

                        <div class="col-md-12 ">
                            <label for="sku" class="form-label">SKU</label>
                            <input type="text" class="form-control @error('sku') is-invalid @enderror" id="sku" wire:model="sku"> 
                            @error('sku') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        
                        </div>

                         <div class="col-12 col-md-12 mt-2">
                            <button type="button" class="btn btn-secondary" wire:click="crearSku">Generate SKU</button>
                        </div>

                        <div class="col-md-12 ">
                            <label for="size" class="form-label">Size</label>
                            <input type="text" class="form-control @error('size') is-invalid @enderror" id="size" wire:model="size">
                            @error('size') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-12 ">
                            <label for="GN" class="form-label">GN</label>
                            <input type="text" class="form-control @error('GN') is-invalid @enderror" id="GN" wire:model="GN">
                            @error('GN') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-12 ">
                            <label for="GW" class="form-label">GW</label>
                            <input type="text" class="form-control @error('GW') is-invalid @enderror" id="GW" wire:model="GW">
                            @error('GW') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-12 ">
                            <label for="Box" class="form-label">Box</label>
                            <input type="text" class="form-control @error('Box') is-invalid @enderror" id="Box" wire:model="Box">
                            @error('Box') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-12 ">
                            <label for="invoice_number" class="form-label">Invoice Number</label>
                            <input type="text" class="form-control @error('invoice_number') is-invalid @enderror" id="invoice_number" wire:model="invoice_number">
                            @error('invoice_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- <div class="col-md-12 ">
                            <label for="classification_id" class="form-label">Classification</label>
                            
                            <select class="form-select @error('classification_id') is-invalid @enderror" id="classification_id" wire:model="classification_id">
                                <option value="">Select Classification</option>
                                @foreach ($classifications as $classification)
                                    <option value="{{ $classification->id }}">{{ $classification->name }}</option>
                                @endforeach
                            </select>
                            
                            @error('classification_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div> --}}
                       
                        
                       {{--  <div class="col-md-12">
                            <label for="classification_id" class="form-label">Classification</label>
                            <input type="text" class="form-control @error('classification_id') is-invalid @enderror" id="classification_id" wire:model="classification_id" readonly>
                            @error('classification_id') <div class="invalid-feedback">{{ $message }}</div> @enderror

                        </div> --}}

                        
                        <div class="col-md-12 mb-2">
                            <label for="warehouse_id" class="form-label">Warehouse</label>
                            <select class="form-select @error('warehouse_id') is-invalid @enderror" id="warehouse_id" wire:model="warehouse_id">
                                <option value="">Select Warehouse</option>
                                @foreach ($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                            @error('warehouse_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-12 mb-2">
                            <label for="cantidad" class="form-label">Cantidad</label>
                            <input type="number" class="form-control @error('cantidad') is-invalid @enderror" id="cantidad" wire:model="cantidad">
                            @error('cantidad') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- input hide classification --}}

                        <div class="d-none">
                            <input type="number" class="form-control" id="hidden_classification_id" wire:model="classification_id">
                        </div>

                        <button type="submit" class="btn btn-secondary">Save Product</button>
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