
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
                            <input wire:model.live="search" type="text" class="form-control"
                                placeholder="Search petitions...">
                        </div>

                        @can('create petitions')
                            <div class="col-md-6 text-end">
                                <a href="{{ route('ensayo') }}" class="btn btn-secondary"><i
                                        class="bi bi-plus"></i>Create</a>
                            </div>
                        @endcan
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-responsive">
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
                                            @can('read petitions')
                                                <a href="{{ route('petitions.show', $petition->id) }}" class="btn"><i
                                                        class="bi bi-eye"></i></a>
                                            @endcan
                                            @can('update petitions')
                                                <a href="{{ route('petitions.edit', $petition->id) }}" class="btn"><i
                                                        class="bi bi-pencil-square"></i></a>
                                            @endcan
                                            @can('delete petitions')
                                                <button wire:click="delete({{ $petition->id }})" class="btn"><i
                                                        class="bi bi-trash"></i></button>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{ $petitions->links() }}
                </div>
            </div>
        </div>
    </div>

