<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">{{ __('Historical Movements Report') }}</div>

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="productFilter" class="form-label">Product:</label>
                            <select wire:model.live="productId" id="productFilter" class="form-select">
                                <option value="">All Products</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="warehouseFilter" class="form-label">Warehouse:</label>
                            <select wire:model.live="warehouseId" id="warehouseFilter" class="form-select">
                                <option value="">All Warehouses</option>
                                @foreach ($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="typeFilter" class="form-label">Movement Type:</label>
                            <select wire:model.live="movementType" id="typeFilter" class="form-select">
                                <option value="">All Types</option>
                                <option value="Entrada">Entrada</option>
                                <option value="Salida">Salida</option>
                                <option value="Devolucion">Devolucion</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="startDateFilter" class="form-label">Start Date:</label>
                            <input type="date" wire:model.live="startDate" id="startDateFilter" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label for="endDateFilter" class="form-label">End Date:</label>
                            <input type="date" wire:model.live="endDate" id="endDateFilter" class="form-control">
                        </div>

                        <div class="col-md-3">
                            <label for="sizeFilter" class="form-label">Size:</label>
                            <input type="text" wire:model.live="size" id="sizeFilter" class="form-control" placeholder="Enter size">
                        </div>
                    </div>

                    <div class="mb-3">
                        <button wire:click="generatePdf" class="btn btn-secondary">Generate PDF Report</button>
                    </div>

                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Product</th>
                                <th>Size</th>
                                <th>Warehouse</th>
                                <th>Quantity</th>
                                <th>Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($movements as $movement)
                                <tr>
                                    <td>{{ $movement->created_at->format('Y-m-d H:i:s') }}</td>
                                    <td>{{ $movement->product->name }}</td>
                                    <td>{{ $movement->product->size }}</td>
                                    <td>{{ $movement->warehouse->name }}</td>
                                    <td>{{ $movement->quantity }}</td>
                                    <td>{{ ucfirst($movement->type) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">No movements found matching the criteria.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>