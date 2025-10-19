<div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Actualizar Almacén desde QR</h3>
        </div>
        <div class="card-body">

            @if (session()->has('message'))
                <div class="alert alert-success">
                    {{ session('message') }}
                </div>
            @endif

            <div class="row">
                <div class="col-md-6 mx-auto mb-4">
                    <div id="qr-reader" style="width:100%;"></div>
                </div>
            </div>

            @if ($productFound)
                <form wire:submit.prevent="updateWarehouse">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nombre</label>
                                <input type="text" class="form-control" value="{{ $productData['name'] ?? '' }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>SKU</label>
                                <input type="text" class="form-control" value="{{ $productData['sku'] ?? '' }}" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tipo</label>
                                <input type="text" class="form-control" value="{{ $productData['type'] ?? '' }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tamaño</label>
                                <input type="text" class="form-control" value="{{ $productData['size'] ?? '' }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Peso Neto (NW)</label>
                                <input type="text" class="form-control" value="{{ $productData['NW'] ?? '' }}" readonly>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="warehouse">Asignar Almacén</label>
                                <select wire:model="selectedWarehouseId" id="warehouse" class="form-control @error('selectedWarehouseId') is-invalid @enderror">
                                    <option value="">-- Seleccione un Almacén --</option>
                                    @foreach ($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                    @endforeach
                                </select>
                                @error('selectedWarehouseId') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Actualizar Almacén</button>
                        <button type="button" wire:click="resetState" class="btn btn-secondary">Escanear Otro</button>
                    </div>
                </form>
            @else
                <div class="text-center">
                    <p class="text-muted">Esperando escaneo de código QR...</p>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script src="https://unpkg.com/html5-qrcode@2.0.9/dist/html5-qrcode.min.js"></script>
    <script type="text/javascript">
        function onScanSuccess(decodedText, decodedResult) {
            // Manejar el éxito del escaneo.
            console.log(`Scan result: ${decodedText}`, decodedResult);
            // Enviar el resultado al backend de Livewire
            @this.call('processQrCode', decodedText);
            // Opcional: detener el escáner después de una detección exitosa
            // html5QrcodeScanner.clear();
        }

        let html5QrcodeScanner = new Html5QrcodeScanner(
            "qr-reader", { fps: 10, qrbox: 250 });
        html5QrcodeScanner.render(onScanSuccess);
    </script>
    @endpush
</div>