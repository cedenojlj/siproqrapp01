<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Create New Customer') }}</div>

                <div class="card-body">
                    @if (session()->has('message'))
                        <div class="alert alert-success">
                            {{ session('message') }}
                        </div>
                    @endif

                    <form wire:submit.prevent="save">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                id="name" wire:model="name">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" class="form-control @error('address') is-invalid @enderror"
                                id="address" wire:model="address">
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                id="phone" wire:model="phone">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                id="email" wire:model="email">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-secondary">Save Customer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>



    {{-- FORMULARIO PARA LA CARGA DE PRECIOS DE LOS PRODUCTOS --}}


    {{-- <div class="row justify-content-center mt-5">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Create New Prices for Product') }}</div>

                <div class="card-body">
                    @if (session()->has('message'))
                        <div class="alert alert-success">
                            {{ session('message') }}
                        </div>
                    @endif

                    <form wire:submit.prevent="cargarProducto">
                        <div class="mb-3">
                            <label for="idproducto" class="form-label">ID Producto de Inicio</label>
                            <input type="number" class="form-control @error('idproducto') is-invalid @enderror"
                                id="idproducto" wire:model="idproducto">
                            @error('idproducto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-secondary">Save Prices</button>
                    </form>
                </div>
            </div>
        </div>
    </div> --}}

    {{-- FIN DEL FORMULARIO PARA LA CARGA DE PRECIOS DE LOS PRODUCTOS --}}



</div>
