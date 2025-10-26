<div class="row justify-content-start">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">{{ __('Orders') }}</div>

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

                <div class="row mb-3 justify-content-start">
                    <div class="col-sm-12">
                        @can('create orders')
                            <div class="col-md-6 text-start">
                                <a href="{{ route('orders.create') }}" class="btn btn-secondary"><i
                                        class="bi bi-plus"></i>Create</a>
                            </div>
                        @endcan


                    </div>


                </div>

                <div class="row mb-3">
                    <div class="col-md-6 mb-sm-2">
                        <label for="search">Search</label>
                        <input wire:model.live="search" type="text" class="form-control"
                            placeholder="Search orders...">
                    </div>
                    <div class="col-md-3 mb-sm-2">
                       <label for="fechaInicio">Fecha Inicio</label>
                        <input wire:model.live="fechaInicio" type="date" class="form-control"
                            placeholder="fecha Inicio">
                    </div>
                    <div class="col-md-3 mb-sm-2">
                       <label for="fechaFin">Fecha Fin</label>
                        <input wire:model.live="fechaFin" type="date" class="form-control" placeholder="fecha Fin">
                    </div>
                </div>
                
                <div class="row mb-3">

                    <div class="col-md-3 mb-sm-2">
                        <label for="customer_id">Customer</label>
                        <select wire:model.live="customer_id" class="form-select">
                            <option value="">All Customers</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-sm-2">
                        <label for="status_pago">Payment Status</label>
                        <select wire:model.live="status_pago" class="form-select">
                            <option value="">All</option>
                            <option value="pagado">Pagado</option>
                            <option value="parcial">Parcial</option>
                            <option value="pendiente">Pendiente</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-sm-2">
                        <label for="status_general">Status</label>
                        <select wire:model.live="status_general" class="form-select">
                            <option value="">All</option>
                            <option value="Pendiente">Pendiente</option>
                            <option value="Aprobada">Aprobada</option>
                            <option value="Rechazada">Rechazada</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-sm-2">
                        <label for="order_type">Type</label>
                        <select wire:model.live="order_type" class="form-select">
                            <option value="">All</option>
                            <option value="Entrada">Entrada</option>
                            <option value="Devolucion">Devolucion</option>
                            <option value="Interna">Interna</option>                            
                            <option value="Salida">Salida</option>
                        </select>
                    </div>
                    
                    
                </div>                

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Fecha</th>
                                <th>Customer</th>
                                <th>Warehouse</th>
                                <th>Type</th>
                                <th>Total Amount</th>
                                <th>Payment Status</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr>
                                    <td>{{ $order->id }}</td>
                                    <td>{{ $order->created_at->format('d/m/Y H:i:s') }}</td>
                                    <td>{{ $order->customer->name }}</td>
                                    <td>{{ $order->warehouse->name }}</td>
                                    <td>{{ ucfirst($order->order_type) }}</td>
                                    <td>${{ number_format($order->monto_pagado, 2) }} / ${{ number_format($order->total, 2) }}</td>
                                    <td>
                                        @php
                                            $badgeClass = '';
                                            switch ($order->payment_status) {
                                                case 'pagado':
                                                    $badgeClass = 'text-bg-success';
                                                    break;
                                                case 'parcial':
                                                    $badgeClass = 'text-bg-warning';
                                                    break;
                                                case 'pendiente':
                                                default:
                                                    $badgeClass = 'text-bg-danger';
                                                    break;
                                            }
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}</span>
                                    </td>
                                    <td>{{ $order->status }}</td>
                                    <td>
                                        @can('read orders')
                                            <a href="{{ route('orders.show', $order->id) }}" class="btn"><i
                                                    class="bi bi-eye"></i></a>
                                        @endcan
                                        {{-- @can('update orders')
                                            <a href="{{ route('orders.edit', $order->id) }}" class="btn"><i
                                                    class="bi bi-pencil-square"></i></a>
                                        @endcan --}}
                                         @can('delete orders')
                                                <button wire:click="delete({{ $order->id }})" class="btn"><i
                                                        class="bi bi-trash"></i></button>
                                            @endcan
                                            {{--<button wire:click="borrar({{ $order->id }})" class="btn" onclick="confirm('Are you sure you want to delete this order?') || event.stopImmediatePropagation()"><i class="bi bi-trash"></i></button> --}}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{ $orders->links() }}
            </div>
        </div>
    </div>
</div>
