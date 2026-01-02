<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carga CSV de Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-gradient: linear-gradient(135deg, #42e695 0%, #3bb2b8 100%);
            --warning-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        body {
            background: white;
            min-height: 100vh;
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .main-container {
            max-width: 700px;
            margin: 0 auto;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(50, 50, 93, 0.1), 0 5px 15px rgba(0, 0, 0, 0.07);
            overflow: hidden;
        }

        .card-header {
            background: white;
            border-bottom: 1px solid #eaeaea;
            padding: 2rem;
        }

        .card-body {
            padding: 2rem;
        }

        .alert-success {
            background: white;
            border-left: 5px solid #28a745;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .alert-warning {
            background: white;
            border-left: 5px solid #ffc107;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .btn-download {
            background: var(--warning-gradient);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }

        .btn-download:hover {
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 7px 14px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background: var(--success-gradient);
            border: none;
            padding: 15px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 14px rgba(0, 0, 0, 0.1);
        }

        .progress {
            height: 12px;
            border-radius: 10px;
            margin: 15px 0;
        }

        .progress-bar {
            background: var(--success-gradient);
            border-radius: 10px;
        }

        .form-control-lg {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #eaeaea;
            transition: all 0.3s;
        }

        .form-control-lg:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .file-input-container {
            position: relative;
            overflow: hidden;
            border: 2px dashed #667eea;
            border-radius: 15px;
            padding: 40px 20px;
            text-align: center;
            background: rgba(255, 255, 255, 0.9);
            transition: all 0.3s;
        }

        .file-input-container:hover {
            background: rgba(102, 126, 234, 0.05);
            border-color: #764ba2;
        }

        .file-input-container input[type="file"] {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin: 15px 0;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border-left: 4px solid;
        }

        .stats-success {
            border-left-color: #28a745;
        }

        .stats-warning {
            border-left-color: #ffc107;
        }

        .loading-spinner {
            display: none;
            text-align: center;
            padding: 20px;
        }
    </style>
</head>

<body>
    <div class="main-container">
        <!-- Tarjeta Principal -->
        <div class="card">
            <!-- Encabezado -->
            <div class="card-header text-center">
                <a href="{{ route('dashboard.index') }}">Regresar Dashboard</a>
                <h1 class="mb-3">
                    <i class="fas fa-file-csv text-primary me-2"></i>
                    Carga de Productos CSV
                </h1>
                <p class="text-muted mb-0">
                    Sube tu archivo CSV con los datos de productos para importar a la base de datos
                </p>
            </div>

            <!-- Contenido -->
            <div class="card-body">
                <!-- Mensajes de Éxito/Error -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle fa-2x text-success me-3"></i>
                            <div>
                                <h5 class="alert-heading mb-1">¡Proceso Completado!</h5>
                                <p class="mb-0">{{ session('success') }}</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-circle fa-2x text-danger me-3"></i>
                            <div>
                                <h5 class="alert-heading mb-1">¡Error!</h5>
                                <p class="mb-0">{{ session('error') }}</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Resultados de la Carga -->
                @if (session('guardados') !== null)
                    <!-- Estadísticas de Carga -->
                    <div class="stats-card stats-success mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5 class="mb-2">
                                    <i class="fas fa-chart-bar text-success me-2"></i>
                                    Resumen del Proceso
                                </h5>
                                <div class="d-flex">
                                    <div class="me-4">
                                        <span class="d-block text-muted small">Total Líneas</span>
                                        <span class="h4 mb-0">{{ session('total_lineas') }}</span>
                                    </div>
                                    <div class="me-4">
                                        <span class="d-block text-muted small">Guardados</span>
                                        <span class="h4 mb-0 text-success">{{ session('guardados') }}</span>
                                    </div>
                                    @if (session('total_errores') > 0)
                                        <div>
                                            <span class="d-block text-muted small">Errores</span>
                                            <span class="h4 mb-0 text-warning">{{ session('total_errores') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                @php
                                    $porcentaje =
                                        session('total_lineas') > 0
                                            ? round((session('guardados') / session('total_lineas')) * 100, 1)
                                            : 0;
                                @endphp
                                <div class="h2 mb-0">{{ $porcentaje }}%</div>
                                <small class="text-muted">Tasa de éxito</small>
                            </div>
                        </div>

                        <!-- Barra de Progreso -->
                        <div class="progress mt-3">
                            <div class="progress-bar" role="progressbar" style="width: {{ $porcentaje }}%"
                                aria-valuenow="{{ $porcentaje }}" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                    </div>

                    <!-- Mensaje de Errores (si hay) -->
                    @if (session('total_errores') > 0)
                        <div class="stats-card stats-warning mb-4">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-exclamation-triangle fa-2x text-warning me-3 mt-1"></i>
                                <div class="flex-grow-1">
                                    <h5 class="mb-2">Se encontraron errores</h5>
                                    <p class="mb-3">
                                        <strong>{{ session('total_errores') }}</strong> líneas no pudieron procesarse.
                                        Descarga el archivo de errores para revisar y corregir los problemas.
                                    </p>

                                    <!-- Botón de Descarga de Errores -->
                                    @if (session('tiene_errores'))
                                        <div class="d-flex align-items-center">
                                            <a href="{{ route('descargar.errores') }}" class="btn-download me-3"
                                                id="btnDescargarErrores">
                                                <i class="fas fa-download me-2"></i>
                                                Descargar Errores ({{ session('total_errores') }} líneas)
                                            </a>
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle"></i>
                                                Formato CSV con detalles de cada error
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <hr class="my-4">
                @endif

                <!-- Formulario de Carga -->
                <form method="POST" action="{{ route('process.csv') }}" enctype="multipart/form-data" id="uploadForm"
                    onsubmit="showLoading()">
                    @csrf

                    <!-- Área de Subida de Archivo -->
                    <div class="mb-4">
                        <label class="form-label fw-bold mb-3">
                            <i class="fas fa-cloud-upload-alt me-2"></i>
                            Seleccionar Archivo CSV
                        </label>

                        <div class="file-input-container">
                            <div class="mb-3">
                                <i class="fas fa-file-csv fa-4x text-primary mb-3"></i>
                                <h5>Arrastra y suelta tu archivo aquí</h5>
                                <p class="text-muted">o haz clic para seleccionar</p>
                            </div>
                            <input type="file" class="form-control" name="csv_file" id="csv_file" accept=".csv,.txt"
                                required>
                            <div class="mt-3" id="fileInfo"></div>
                        </div>

                        <div class="form-text mt-2">
                            <i class="fas fa-info-circle me-1"></i>
                            Formatos aceptados: .csv, .txt (máximo 5MB).
                            <a href="javascript:void(0);" onclick="descargarPlantilla()" class="text-decoration-none">
                                <i class="fas fa-download ms-2 me-1"></i>Descargar plantilla
                            </a>
                        </div>
                    </div>

                    <!-- Opciones de Configuración -->
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3">
                            <i class="fas fa-cogs me-2"></i>
                            Opciones de Importación
                        </h6>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-check card p-5 border">
                                    <input type="checkbox" class="form-check-input" name="skip_header"
                                        id="skip_header" checked>
                                    <label class="form-check-label fw-medium" for="skip_header">
                                        <i class="fas fa-heading me-2"></i>
                                        Primera línea son encabezados
                                    </label>
                                    <small class="text-muted d-block mt-1">
                                        La primera línea del CSV contiene los nombres de las columnas
                                    </small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check card p-5 border">
                                    <input type="checkbox" class="form-check-input" name="descargar_inmediato"
                                        id="descargar_inmediato">
                                    <label class="form-check-label fw-medium" for="descargar_inmediato">
                                        <i class="fas fa-download me-2"></i>
                                        Descargar errores automáticamente
                                    </label>
                                    <small class="text-muted d-block mt-1">
                                        Si hay errores, se descargará el archivo inmediatamente
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Selección de Almacén -->
                    <div class="mb-4">
                        <label for="warehouse_id" class="form-label fw-medium">
                            <i class="fas fa-warehouse me-2"></i>
                            Almacén
                        </label>
                        <select name="warehouse_id" id="warehouse_id" class="form-select">
                            <option value="">"Seleccione un almacén"</option>
                            @foreach ($warehouses as $warehouse)                                
                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Botón de Envío -->
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                            <i class="fas fa-play me-2"></i>
                            Procesar Archivo CSV
                        </button>
                    </div>
                </form>

                <!-- Spinner de Carga -->
                <div class="loading-spinner mt-4" id="loadingSpinner">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-3 text-muted">Procesando archivo, por favor espera...</p>
                </div>

                <!-- Información del Formato -->
                <div class="mt-5">
                    <div class="accordion" id="formatInfo">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#formatDetails">
                                    <i class="fas fa-table me-2"></i>
                                    <strong>📋 Formato Requerido del CSV (6 columnas)</strong>
                                </button>
                            </h2>
                            <div id="formatDetails" class="accordion-collapse collapse" data-bs-parent="#formatInfo">
                                <div class="accordion-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Columna</th>
                                                    <th>Ejemplo</th>
                                                    <th>Tipo de Dato</th>
                                                    <th>Requerido</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><code>name</code></td>
                                                    <td>"COPPER WIRE"</td>
                                                    <td>Texto</td>
                                                    <td><span class="badge bg-danger">Sí</span></td>
                                                </tr>
                                                
                                                <tr>
                                                    <td><code>type</code></td>
                                                    <td>"SC010101"</td>
                                                    <td>Texto</td>
                                                    <td><span class="badge bg-danger">Sí</span></td>
                                                </tr>
                                                <tr>
                                                    <td><code>size</code></td>
                                                    <td>"AWG 08"</td>
                                                    <td>Texto</td>
                                                    <td><span class="badge bg-danger">Sí</span></td>
                                                </tr>
                                                <tr>
                                                    <td><code>GN</code></td>
                                                    <td>1550.75</td>
                                                    <td>Número decimal</td>
                                                    <td><span class="badge bg-danger">Sí</span></td>
                                                </tr>

                                                <tr>
                                                    <td><code>Cantidad</code></td>
                                                    <td>2.00</td>
                                                    <td>Número decimal</td>
                                                    <td><span class="badge bg-danger">Sí</span></td>
                                                </tr>
                                                <tr>
                                                    <td><code>invoice_number</code></td>
                                                    <td>"INV-2023-001"</td>
                                                    <td>Texto</td>
                                                    <td><span class="badge bg-danger">Sí</span></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Ejemplo CSV -->
                                    <div class="mt-3">
                                        <h6>Ejemplo de contenido CSV:</h6>
                                        <pre class="bg-light p-3 rounded border"><code>name,type,size,GN,Cantidad,invoice_number
COPPER WIRE,"SC010101","AWG 08",1550.75,2,"INV-2023-001"
Mouse Logitech MX Master,"SC010103","Mediano",89.99,5,"INV-2023-002"
Monitor Samsung 27\","SC010105","27 pulgadas",349.99,3,"INV-2023-003"</code></pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pie de Página -->
            <div class="card-footer text-center text-muted py-3">
                <small>
                    <i class="fas fa-database me-1"></i>
                    Sistema de Carga CSV v1.0 •
                    Total de registros en base de datos:
                    <span class="badge bg-secondary">
                        {{ \App\Models\Product::count() ?? '0' }}
                    </span>
                </small>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mostrar información del archivo seleccionado
        document.getElementById('csv_file').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const fileInfo = document.getElementById('fileInfo');

            if (file) {
                const fileSize = (file.size / 1024).toFixed(2); // KB
                fileInfo.innerHTML = `
                    <div class="alert alert-info py-2">
                        <i class="fas fa-file me-2"></i>
                        <strong>${file.name}</strong>
                        <span class="badge bg-secondary ms-2">${fileSize} KB</span>
                        <br>
                        <small>Última modificación: ${new Date(file.lastModified).toLocaleDateString()}</small>
                    </div>
                `;
            } else {
                fileInfo.innerHTML = '';
            }
        });

        // Descargar plantilla CSV
        function descargarPlantilla() {
            const csvContent = `name,type,size,GN,Cantidad,invoice_number
Laptop Dell XPS 15,"SC010101","15.6 pulgadas",1550.75,2,"INV-2023-001"
Mouse Logitech MX Master,"SC010103","Mediano",89.99,5,"INV-2023-002"
Monitor Samsung 27\","SC010105","27 pulgadas",349.99,3,"INV-2023-003"
Teclado MecÃ¡nico Razer,"SC010107","Full Size",129.50,4,"INV-2023-004"
Silla Gamer Ergohuman,"SC010109","Grande",450.00,1,"INV-2023-005"
Disco SSD 1TB NVMe,"SC010111","M.2 2280",89.99,8,"INV-2023-006"
Memoria RAM 16GB DDR4,"SC010113","DIMM",65.75,10,"INV-2023-007"
Router WiFi 6 TP-Link,"SC010115","Estándar",120.00,3,"INV-2023-008"
Impresora Laser HP,"SC010117","Compacta",199.99,2,"INV-2023-009"
`;

            const blob = new Blob([csvContent], {
                type: 'text/csv;charset=utf-8;'
            });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'plantilla_productos.csv';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }

        // Mostrar spinner de carga al enviar formulario
        function showLoading() {
            // Validar archivo
            if (document.getElementById('csv_file').files.length === 0) {
                alert('❌ Selecciona un archivo CSV.');
                return false;
            }

            // Confirmar si es descarga inmediata
            const descargarInmediato = document.getElementById('descargar_inmediato');
            if (descargarInmediato.checked && !confirm('¿Descargar errores automáticamente si los hay?')) {
                return false;
            }

            // Mostrar spinner
            document.getElementById('submitBtn').disabled = true;
            document.getElementById('submitBtn').innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Procesando...';
            document.getElementById('loadingSpinner').style.display = 'block';

            // Timer de seguridad: ocultar después de 8 segundos
            setTimeout(() => {
                document.getElementById('loadingSpinner').style.display = 'none';
                document.getElementById('submitBtn').disabled = false;
                document.getElementById('submitBtn').innerHTML =
                    '<i class="fas fa-play me-2"></i>Procesar Archivo CSV';
            }, 8000);

            return true;
        }

        // Prevenir reenvío del formulario
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }

        // Auto-scroll a resultados si hay mensajes
        @if (session('guardados') !== null || session('error'))
            window.scrollTo(0, 0);
        @endif

        // Animación para el botón de descarga
        document.addEventListener('DOMContentLoaded', function() {
            const btnDescargar = document.getElementById('btnDescargarErrores');
            if (btnDescargar) {
                btnDescargar.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-3px) scale(1.02)';
                });
                btnDescargar.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            }
        });
    </script>
</body>

</html>
