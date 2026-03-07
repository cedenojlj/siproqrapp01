<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">{{ __('Classifications') }}</div>

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
                    <div class="col-12 col-md-6">
                        <input wire:model.live="search" type="text" class="form-control"
                            placeholder="Search classifications...">
                    </div>
                    <div class="col-12 col-md-6 text-end mt-sm-2">
                        <button wire:click="create()" class="btn btn-secondary"><i class="bi bi-plus"></i> Create</button>
                    </div>
                </div>

                @if ($isOpen)
                    @include('livewire.classification.modal')
                @endif

                @if ($isDeleteModalOpen)
                    @include('livewire.classification.delete-modal')
                @endif

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Description</th>
                                <th>Size</th>
                                <th>Unit Price</th>
                                <th>Weight Price</th>
                                <th>Unit Type</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($classifications as $classification)
                                <tr>
                                    <td>{{ $classification->id }}</td>
                                    <td>{{ $classification->name }}</td>
                                    <td>{{ $classification->code }}</td>
                                    <td>{{ $classification->description }}</td>
                                    <td>{{ $classification->size }}</td>
                                    <td>{{ $classification->precio_unidad }}</td>
                                    <td>{{ $classification->precio_peso }}</td>
                                    <td>{{ $classification->unit_type }}</td>
                                    <td>
                                        <button wire:click="edit({{ $classification->id }})" class="btn"><i
                                                class="bi bi-pencil-square"></i></button>
                                        <button wire:click="openDeleteModal({{ $classification->id }})" class="btn"><i
                                                class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{ $classifications->links() }}
            </div>
        </div>
    </div>
</div>
