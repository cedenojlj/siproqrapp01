
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Petitions') }}</div>

                <div class="card-body">
                    @if (session()->has('message'))
                        <div class="alert alert-success">
                            {{ session('message') }}
                        </div>
                    @endif
                    
                    @if (session()->has('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <input wire:model.live="search" type="text" class="form-control" placeholder="Search petitions...">
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="{{ route('petitions.create') }}" class="btn btn-secondary"><i class="bi bi-plus"></i>Create Petition</a>
                        </div>
                    </div>

                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($petitions as $petition)
                                <tr>
                                    <td>{{ $petition->id }}</td>
                                    <td>{{ $petition->customer->name }}</td>
                                    <td>{{ number_format($petition->total, 2) }}</td>
                                    <td>{{ $petition->status }}</td>
                                    <td>
                                        <a href="{{ route('petitions.show', $petition->id) }}" class="btn"><i class="bi bi-eye"></i></a>
                                        <a href="{{ route('petitions.edit', $petition->id) }}" class="btn"><i class="bi bi-pencil-square"></i></a>
                                        <button wire:click="delete({{ $petition->id }})" class="btn" onclick="confirm('Are you sure you want to delete this petition?') || event.stopImmediatePropagation()"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{ $petitions->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
