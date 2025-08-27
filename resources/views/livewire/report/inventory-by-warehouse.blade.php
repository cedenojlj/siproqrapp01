<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">{{ __('Inventory Report by Warehouse') }}</div>

                <div class="card-body">
                    <div class="mb-3">
                        <label for="warehouseFilter" class="form-label">Filter by Warehouse:</label>
                        <select wire:model.live="selectedWarehouseId" id="warehouseFilter" class="form-select">
                            <option value="">All Warehouses</option>
                            @foreach ($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <button wire:click="generatePdf" class="btn btn-secondary">Generate PDF Report</button>
                    </div>

                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Warehouse</th>
                                <th>Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($inventory as $item)
                                <tr>
                                    <td>{{ $item->product->name }}</td>
                                    <td>{{ $item->warehouse->name }}</td>
                                    <td>{{ $item->stock }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3">No inventory data found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>