<div>
    <div class="container-fluid">
        <div class="mb-3">
            <h1 class="h3 d-inline align-middle">Dashboard</h1>
        </div>
        <div class="row">
            <div class="col-12 col-md-6 col-xl-3 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="row d-flex align-items-center">
                            <div class="col-auto">
                                <div class="avatar bg-primary text-white rounded-circle p-3">
                                    <i class="bi bi-box-seam-fill fs-4"></i>
                                </div>
                            </div>
                            <div class="col">
                                <h5 class="card-title text-muted fw-light mb-1">Productos</h5>
                                <h3 class="fw-bold mb-0">{{ $productosCount }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="row d-flex align-items-center">
                            <div class="col-auto">
                                <div class="avatar bg-success text-white rounded-circle p-3">
                                    <i class="bi bi-people-fill fs-4"></i>
                                </div>
                            </div>
                            <div class="col">
                                <h5 class="card-title text-muted fw-light mb-1">Ordenes</h5>
                                <h3 class="fw-bold mb-0">{{ $ordenesCount }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="row d-flex align-items-center">
                            <div class="col-auto">
                                <div class="avatar bg-warning text-white rounded-circle p-3">
                                    <i class="bi bi-receipt fs-4"></i>
                                </div>
                            </div>
                            <div class="col">
                                <h5 class="card-title text-muted fw-light mb-1">Pedidos</h5>
                                <h3 class="fw-bold mb-0">{{ $peticionesCount }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="row d-flex align-items-center">
                            <div class="col-auto">
                                <div class="avatar bg-info text-white rounded-circle p-3">
                                    <i class="bi bi-graph-up-arrow fs-4"></i>
                                </div>
                            </div>
                            <div class="col">
                                <h5 class="card-title text-muted fw-light mb-1">Ventas</h5>
                                <h3 class="fw-bold mb-0">${{ $ordenesTotal }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-md-12 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Monthly Orders and Petitions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-11">
                                <canvas id="monthly-activity-chart" height="400px"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('monthly-activity-chart').getContext('2d');
        const chartData = @json($this->chartData);

        // Helper to convert hex to rgba for a softer area fill
        const hexToRgba = (hex, alpha) => {
            let r = 0, g = 0, b = 0;
            if (hex.match(/^#/)) {
                if (hex.length === 4) {
                    r = parseInt(hex[1] + hex[1], 16);
                    g = parseInt(hex[2] + hex[2], 16);
                    b = parseInt(hex[3] + hex[3], 16);
                } else if (hex.length === 7) {
                    r = parseInt(hex.substring(1, 3), 16);
                    g = parseInt(hex.substring(3, 5), 16);
                    b = parseInt(hex.substring(5, 7), 16);
                }
            }
            return `rgba(${r}, ${g}, ${b}, ${alpha})`;
        };

        // Smooth lines and add area fill
        chartData.datasets.forEach(dataset => {
            dataset.tension = 0.4; // Smoothens the line
            dataset.fill = true; // Adds area under the line
            if (dataset.borderColor) {
                // Use a semi-transparent version of the border color for the fill
                dataset.backgroundColor = hexToRgba(dataset.borderColor, 0.2);
            }
        });

        new Chart(ctx, {
            type: 'line',
            data: chartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    }
                }
            }
        });
    });
</script>
