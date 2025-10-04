<div>
    <div class="row mb-3">
        <div class="col-md-2">
            <label for="customerName">Customer Name</label>
            <input type="text" class="form-control" wire:model.live="customerName" id="customerName">
        </div>
        <div class="col-md-2">
            <label for="size">Size</label>
            <input type="text" class="form-control" wire:model.live="size" id="size">
        </div>
        <div class="col-md-2">
            <label for="productId">Product</label>
            <select wire:model.live="productId" id="productId" class="form-control">
                <option value="">All</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label for="warehouseId">Warehouse</label>
            <select wire:model.live="warehouseId" id="warehouseId" class="form-control">
                <option value="">All</option>
                @foreach($warehouses as $warehouse)
                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label for="movementType">Movement Type</label>
            <select wire:model.live="movementType" id="movementType" class="form-control">
                <option value="">All</option>
                <option value="Entrada">Entrada</option>
                <option value="Devolucion">Devolucion</option>               
                <option value="Salida">Salida</option>
            </select>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-3">
            <label for="startDate">Start Date</label>
            <input type="date" class="form-control" wire:model.live="startDate" id="startDate">
        </div>
        <div class="col-md-3">
            <label for="endDate">End Date</label>
            <input type="date" class="form-control" wire:model.live="endDate" id="endDate">
        </div>
        <div class="col-md-2 align-self-end">
            <button class="btn btn-primary" wire:click="generatePdf">Export to PDF</button>
        </div>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Date</th>
                <th>Product</th>
                <th>Warehouse</th>
                <th>Type</th>
                <th>Quantity</th>
                <th>Customer Name</th>
                <th>Size</th>
                <th>NW</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($movements as $movement)
                <tr>
                    <td>{{ $movement->created_at->format('Y-m-d H:i') }}</td>
                    <td>{{ $movement->product_name }}</td>
                    <td>{{ $movement->warehouse_name }}</td>
                    <td>{{ ucfirst($movement->type) }}</td>
                    <td>{{ $movement->quantity }}</td>
                    <td>{{ $movement->customer_name }}</td>
                    <td>{{ $movement->product_size }}</td>
                    <td>{{ $movement->product_gn }}</td>
                    <td>{{ number_format($movement->subtotal, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center">No movements found.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="8" class="text-right"><strong>Total:</strong></td>
                <td><strong>{{ number_format($totalSubtotal, 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>
</div>