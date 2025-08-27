<div>

     <div class="row">

        <div class="col-md-12">
            @if (session()->has('message'))
                <div class="alert alert-success">
                    {{ session('message') }}
                </div>
            @endif
        </div>

        <div class="col-md-12">
            @if (session()->has('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
        </div>

    </div>
    
    <div class="row mb-3">
        <div class="col-sm-12 col-md-6 mb-3 mb-md-0">
            <input wire:model.live="search" type="text" class="form-control" placeholder="Search products...">
        </div>
        <div class="col-sm-12 col-md-6 text-end">

            <a href="{{ route('products.create') }}" class="btn btn-secondary"><i class="bi bi-plus"></i>
                Create</a>
            <a href="{{ route('products.generate-qrcodes') }}" class="btn btn-secondary"><i class="bi bi-qr-code"></i><span class="ps-2">Generate QR</span></a>
        </div>
    </div>
   

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Type - Size</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
                <tr>
                    <td>{{ $product->id }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->type .' - '. $product->size }}</td>
                    <td>
                        <a href="{{ route('products.edit', $product->id) }}" class="btn"><i class="bi bi-pencil-square"></i></a>
                        <button wire:click="delete({{ $product->id }})" class="btn" onclick="confirm('Are you sure you want to delete this product?') || event.stopImmediatePropagation()"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $products->links() }}
</div>