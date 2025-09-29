<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Edit Product') }}</div>

                <div class="card-body">
                    @if (session()->has('message'))
                        <div class="alert alert-success">
                            {{ session('message') }}
                        </div>
                    @endif

                    <form wire:submit.prevent="update">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" wire:model="name">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="type" class="form-label">Type</label>
                            <input type="text" class="form-control @error('type') is-invalid @enderror" id="type" wire:model.blur="type" disabled>
                            @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror                       

                        <div class="mb-3">
                            <label for="sku" class="form-label">SKU</label>
                            <input type="text" class="form-control @error('sku') is-invalid @enderror" id="sku" wire:model="sku" disabled>
                            @error('sku') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="size" class="form-label">Size</label>
                            <input type="text" class="form-control @error('size') is-invalid @enderror" id="size" wire:model="size" disabled>
                            @error('size') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="GN" class="form-label">GN</label>
                            <input type="number" class="form-control @error('GN') is-invalid @enderror" id="GN" wire:model="GN" step="0.01">
                            @error('GN') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                       {{--  <div class="mb-3">
                            <label for="GW" class="form-label">GW</label>
                            <input type="text" class="form-control @error('GW') is-invalid @enderror" id="GW" wire:model="GW">
                            @error('GW') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="Box" class="form-label">Box</label>
                            <input type="text" class="form-control @error('Box') is-invalid @enderror" id="Box" wire:model="Box">
                            @error('Box') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div> --}}

                        <div class="mb-3">
                            <label for="invoice_number" class="form-label">Invoice Number</label>
                            <input type="number" class="form-control @error('invoice_number') is-invalid @enderror" id="invoice_number" wire:model="invoice_number">
                            @error('invoice_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- input hide classification --}}

                        <div class="d-none">
                            <input type="number" class="form-control" id="hidden_classification_id" wire:model="classification_id">
                        </div>

                        <button type="submit" class="btn btn-primary">Update Product</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>