<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">{{ __('Create New Order') }}</div>

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

                     @if (session()->has('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form wire:submit.prevent="saveOrder">
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

                        <div class="mb-3">
                            <label for="order_type" class="form-label">Order Type</label>
                            <select class="form-select @error('order_type') is-invalid @enderror" id="order_type" wire:model="order_type">
                                <option value="">Select Order Type</option>
                                <option value="Entrada">Entry</option>
                                <option value="Salida">Exit</option>
                            </select>
                            @error('order_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <hr>

                        <h5>Products</h5>
                        <div class="mb-3">
                            <label for="scannedProductSku" class="form-label">Scan QR Code or Enter SKU</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="scannedProductSku" wire:model="scannedProductSku" placeholder="Scan QR or enter SKU">
                                <button class="btn btn-outline-secondary" type="button" wire:click="scanQrCode">Add Product</button>
                                <button class="btn btn-outline-secondary" type="button" wire:click="abrirScanQrCode">Scanner</button>
                            </div>
                        </div>

                        @foreach ($products as $index => $productItem)
                            <div class="row mb-3 align-items-end">
                                <div class="col-md-5">
                                    <label for="product-{{ $index }}" class="form-label">Product</label>
                                    <select class="form-select @error('products.' . $index . '.product_id') is-invalid @enderror" id="product-{{ $index }}" wire:model.live="products.{{ $index }}.product_id">
                                        <option value="">Select Product</option>
                                        @foreach ($availableProducts as $product)
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
                                    <input type="number" class="form-control" id="price-{{ $index }}" wire:model.live="products.{{ $index }}.price" readonly>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-danger" wire:click="removeProduct({{ $index }})"><svg
                                            xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" class="bi bi-trash3" viewBox="0 0 16 16">
                                            <path
                                                d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5" />
                                        </svg></button>
                                </div>
                            </div>
                        @endforeach

                        {{-- <div class="mb-3">
                            <button type="button" class="btn btn-secondary" wire:click="addProduct">Add Another Product</button>
                        </div> --}}

                        <div class="text-end">
                            <h4>Total: ${{ number_format($totalAmount, 2) }}</h4>
                        </div>

                        <button type="submit" class="btn btn-secondary" {{ count($products)>0 ? '' : 'disabled' }}>Create Order</button>

                    </form>
                </div>
            </div>
        </div>
    </div>
    <div><livewire:order.modal-order /></div>
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