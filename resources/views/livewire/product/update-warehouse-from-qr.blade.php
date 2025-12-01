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

            {{-- mensaje de error --}}
            @if (session()->has('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <div class="row">
                <div class="col-md-6 mx-auto mb-4" wire:ignore>
                    <div id="qr-reader" style="width:100%;"></div>
                </div>
            </div>

            @if ($productFound)
                <form wire:submit.prevent="updateWarehouse">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nombre</label>
                                <input type="text" class="form-control" value="{{ $productData['name'] ?? '' }}"
                                    readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>SKU</label>
                                <input type="text" class="form-control" value="{{ $productData['sku'] ?? '' }}"
                                    readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tipo</label>
                                <input type="text" class="form-control" value="{{ $productData['type'] ?? '' }}"
                                    readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tamaño</label>
                                <input type="text" class="form-control" value="{{ $productData['size'] ?? '' }}"
                                    readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Peso Neto (NW)</label>
                                <input type="text" class="form-control" value="{{ $productData['NW'] ?? '' }}"
                                    readonly>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        {{-- crea un div igual a selectedWarehouseId pero con warehousesAnterior --}}
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="warehouseAnterior">Almacén Actual</label>
                                <select wire:model="warehousesAnterior" id="warehouseAnterior"
                                    class="form-control @error('warehousesAnterior') is-invalid @enderror">
                                    <option value="">-- Seleccione un Almacén --</option>
                                    @foreach ($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                    @endforeach
                                </select>
                                @error('warehousesAnterior')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="warehouse">Asignar Almacén</label>
                                <select wire:model="selectedWarehouseId" id="warehouse"
                                    class="form-control @error('selectedWarehouseId') is-invalid @enderror">
                                    <option value="">-- Seleccione un Almacén --</option>
                                    @foreach ($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                    @endforeach
                                </select>
                                @error('selectedWarehouseId')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="cantidad">Cantidad</label>
                                <input type="number" wire:model.live="cantidad" id="cantidad" class="form-control" min="0">
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-3">
                        <button type="submit" class="btn btn-primary">Actualizar Almacén</button>                        
                    </div>
                </form>
            @else
                <div class="text-center">
                    <p class="text-muted">Esperando escaneo de código QR...</p>
                </div>
            @endif
           
        </div>

        @if ($escaneoInicial)
            <div class="d-grid gap-2 mb-3">
                <button id="llamado" class="btn btn-success" wire:click="$dispatch('abrirScanner')">Scannear</button>
            </div>
        @endif

        @if ($escaneoInicial)
            <div class="d-grid gap-2">
                <button id="llamadoCerrar" class="btn btn-secondary" wire:click="$dispatch('cerrarScanner')">Close
                    Scanner</button>
            </div>
        @endif

    </div>    

    @script
        <script>

            let html5QrcodeScanner=null;

            function onScanSuccess(decodedText, decodedResult) {

                // Manejar el éxito del escaneo.
                console.log(`Scan result: ${decodedText}`, decodedResult);
                // Enviar el resultado al backend de Livewire
                @this.call('processQrCode', decodedText);
                // Opcional: detener el escáner después de una detección exitosa
                html5QrcodeScanner.clear();
            }

            $wire.on('abrirScanner', () => {
                // Código a ejecutar cuando se muestra el modal
               // alert('¡El escáner ha sido mostrado exitosamente!');
                //$wire.dispatch('cerrarModal');

               html5QrcodeScanner = new Html5QrcodeScanner(
                    "qr-reader", {
                        fps: 10,
                        qrbox: 250
                    });
                html5QrcodeScanner.render(onScanSuccess);
            });


            $wire.on('cerrarScanner', () => {
                // Código a ejecutar cuando se cierra el modal
                //alert('¡El escáner ha sido cerrado exitosamente!');
                html5QrcodeScanner.clear();
            });
        </script>
    @endscript
</div>
