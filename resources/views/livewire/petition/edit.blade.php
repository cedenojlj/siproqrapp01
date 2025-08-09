<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">{{ __('Edit Petition') }}</div>

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

                    <form wire:submit.prevent="updatePetition">
                        <div class="mb-3">
                            <label for="customer_id" class="form-label">Customer</label>
                            <select class="form-select @error('customer_id') is-invalid @enderror" id="customer_id" wire:model.live="customer_id">
                                <option value="">Select Customer</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                            @error('customer_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <hr>

                        <h5>Products</h5>
                        <div class="mb-3">
                            <label for="scannedProductSku" class="form-label">Scan QR Code or Enter SKU</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="scannedProductSku" wire:model="scannedProductSku" placeholder="Scan QR or enter SKU">
                                <button class="btn btn-outline-secondary" type="button" wire:click="scanQrCode">Add Product (Manual/QR)</button>
                            </div>
                        </div>

                        @foreach ($products as $index => $productItem)
                            <div class="row mb-3 align-items-end">
                                <div class="col-md-5">
                                    <label for="product-{{ $index }}" class="form-label">Product</label>
                                    <select class="form-select @error('products.' . $index . '.product_id') is-invalid @enderror" id="product-{{ $index }}" wire:model.live="products.{{ $index }}.product_id">
                                        <option value="">Select Product</option>
                                        @foreach ($allProducts as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }} (SKU: {{ $product->sku }})</option>
                                        @endforeach
                                    </select>
                                    @error('products.' . $index . '.product_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-3">
                                    <label for="quantity-{{ $index }}" class="form-label">Quantity</label>
                                    <input type="number" class="form-control @error('products.' . $index . '.quantity') is-invalid @enderror" id="quantity-{{ $index }}" wire:model.live="products.{{ $index }}.quantity" min="1">
                                    @error('products.' . $index . '.quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-2">
                                    <label for="price-{{ $index }}" class="form-label">Price</label>
                                    <input type="text" class="form-control" id="price-{{ $index }}" wire:model="products.{{ $index }}.price" readonly>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-danger" wire:click="removeProduct({{ $index }})">Remove</button>
                                </div>
                            </div>
                        @endforeach

                        <div class="mb-3">
                            <button type="button" class="btn btn-secondary" wire:click="addProduct">Add Another Product</button>
                        </div>

                        <div class="text-end">
                            <h4>Total: ${{ array_sum(array_column($products, 'price')) }}</h4>
                        </div>

                        <button type="submit" class="btn btn-primary">Update Petition</button>
                    </form>
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