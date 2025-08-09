
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="card text-white bg-primary mb-3">
                                <div class="card-header">Total Products</div>
                                <div class="card-body">
                                    <h5 class="card-title">{{ $totalProducts }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card text-white bg-success mb-3">
                                <div class="card-header">Total Orders</div>
                                <div class="card-body">
                                    <h5 class="card-title">{{ $totalOrders }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card text-white bg-info mb-3">
                                <div class="card-header">Total Petitions</div>
                                <div class="card-body">
                                    <h5 class="card-title">{{ $totalPetitions }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card text-white bg-warning mb-3">
                                <div class="card-header">Total Customers</div>
                                <div class="card-body">
                                    <h5 class="card-title">{{ $totalCustomers }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card text-white bg-danger mb-3">
                                <div class="card-header">Total Warehouses</div>
                                <div class="card-body">
                                    <h5 class="card-title">{{ $totalWarehouses }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

