<div class="row justify-content-start">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">{{ __('Debt Report') }}</div>

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
                    <div class="col-md-3 mb-sm-2">
                        <label for="search">Search</label>
                        <input wire:model.live="search" type="text" class="form-control"
                            placeholder="Search orders...">
                    </div>
                    <div class="col-md-3 mb-sm-2">
                        <label for="customerId">Customer</label>
                        <select wire:model.live="customerId" class="form-control">
                            <option value="">All Customers</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-sm-2">
                        <label for="startDate">Start Date</label>
                        <input wire:model.live="startDate" type="date" class="form-control">
                    </div>
                    <div class="col-md-3 mb-sm-2">
                        <label for="endDate">End Date</label>
                        <input wire:model.live="endDate" type="date" class="form-control">
                    </div>
                    
                    {{-- <div class="col-md-3 mb-sm-2">
                        <label for="status">Status</label>
                        <select wire:model.live="status" class="form-control">
                            <option value="">All Statuses</option>
                            <option value="Pendiente">Pendiente</option>
                            <option value="Aprobada">Aprobada</option>                            
                        </select>
                    </div> --}}
                    <div class="col-md-3 mb-sm-2">
                        <label for="paymentStatus">Payment Status</label>
                        <select wire:model.live="paymentStatus" class="form-control">
                            <option value="">All Statuses</option>
                            <option value="pendiente">Pendiente</option>
                            <option value="parcial">Parcial</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-sm-2 align-self-end">
                        <button wire:click="generatePdf" class="btn btn-primary">Generate PDF</button>
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
                                        @can('update orders')
                                            <a href="{{ route('orders.edit', $order->id) }}" class="btn"><i
                                                    class="bi bi-pencil-square"></i></a>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="total">
                    <p>Total Pending: ${{ number_format($totalPending, 2) }}</p>
                </div>

                {{ $orders->links() }}
            </div>
        </div>
    </div>
</div>
