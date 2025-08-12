<div>
    <div class="row mb-3">
        <div class="col-md-6">
            <input wire:model.debounce.300ms="search" type="text" class="form-control" placeholder="Search products...">
        </div>
        <div class="col-md-6 text-end">
            <i class="bi bi-plus"></i>
            <a href="{{ route('products.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i>Create</a>
            <a href="{{ route('products.generate-qrcodes') }}" class="btn btn-info"><i class="bi bi-qr-code"></i>Generate QR</a>            
        </div>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
                <tr>
                    <td>{{ $product->id }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->description }}</td>
                    <td>
                        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-secondary">Edit</a>
                        <button wire:click="delete({{ $product->id }})" class="btn btn-sm btn-danger" onclick="confirm('Are you sure you want to delete this product?') || event.stopImmediatePropagation()">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $products->links() }}
</div>