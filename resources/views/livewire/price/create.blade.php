<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Create New Price') }}</div>

                <div class="card-body">
                    <form wire:submit.prevent="save">
                        <div class="mb-3">
                            <label for="product_id" class="form-label">Product</label>
                            <select class="form-control @error('product_id') is-invalid @enderror" id="product_id" wire:model="product_id">
                                <option value="">Select Product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                            @error('product_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="customer_id" class="form-label">Customer</label>
                            <select class="form-control @error('customer_id') is-invalid @enderror" id="customer_id" wire:model="customer_id">
                                <option value="">Select Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                            @error('customer_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="price_quantity" class="form-label">Price Quantity</label>
                            <input type="number" step="0.01" class="form-control @error('price_quantity') is-invalid @enderror" id="price_quantity" wire:model="price_quantity">
                            @error('price_quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="price_weight" class="form-label">Price Weight</label>
                            <input type="number" step="0.01" class="form-control @error('price_weight') is-invalid @enderror" id="price_weight" wire:model="price_weight">
                            @error('price_weight') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Save Price</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>