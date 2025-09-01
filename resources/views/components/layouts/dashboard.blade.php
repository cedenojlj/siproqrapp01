<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>

<body class="antialiased">

    <div class="wrapper">
        <!-- Sidebar -->
        <aside id="sidebar">
            <div class="d-flex">
                {{-- <button class="toggle-btn" type="button">
                    <i class="bi bi-grid"></i>
                </button> --}}
                <div class="sidebar-logo">
                    <a href="{{ route('dashboard.index') }}"><i class="bi bi-grid"></i><span
                            class="ps-2">SiproqApp</span></a>
                </div>
            </div>
            <ul class="sidebar-nav">
                <li class="sidebar-item active">
                    <a href="{{ route('dashboard.index') }}" class="sidebar-link">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                @can('read products')
                    <li class="sidebar-item">
                        <a href="{{ route('products.index') }}" class="sidebar-link">
                            <i class="bi bi-box-seam"></i>
                            <span>Productos</span>
                        </a>
                    </li>
                @endcan

                @can('read customers')
                    <li class="sidebar-item">
                        <a href="{{ route('customers.index') }}" class="sidebar-link">
                            <i class="bi bi-people"></i>
                            <span>Clientes</span>
                        </a>
                    </li>
                @endcan

                @can('read warehouses')
                    <li class="sidebar-item">
                        <a href="{{ route('warehouses.index') }}" class="sidebar-link">
                            <i class="bi bi-houses"></i>
                            <span>Almacenes</span>
                        </a>
                    </li>
                @endcan

                @can('read petitions')
                    <li class="sidebar-item">
                        <a href="{{ route('petitions.index') }}" class="sidebar-link">
                            <i class="bi bi-receipt"></i>
                            <span>Pedidos</span>
                        </a>
                    </li>
                @endcan

                @can('read orders')
                    <li class="sidebar-item">
                        <a href="{{ route('orders.index') }}" class="sidebar-link">
                            <i class="bi bi-inboxes"></i>
                            <span>Ordenes</span>
                        </a>
                    </li>
                @endcan

                @can('read prices')
                    <li class="sidebar-item">
                        <a href="{{ route('prices.index') }}" class="sidebar-link">
                            <i class="bi bi-receipt"></i>
                            <span>Precios</span>
                        </a>
                    </li>
                @endcan

                @can('read users')
                    <li class="sidebar-item">
                        <a href="{{ route('users.index') }}" class="sidebar-link">
                            <i class="bi bi-person-lock"></i>
                            <span>Roles</span>
                        </a>
                    </li>
                @endcan



                {{-- inicio de reportes --}}
                @can('ver reportes')
                    <li class="sidebar-item">
                        <a href="#reports-submenu" data-bs-toggle="collapse" class="sidebar-link collapsed">
                            <i class="bi bi-file-earmark-bar-graph"></i>
                            <span>Reportes</span>
                            <i class="bi bi-chevron-down ms-auto"></i>
                        </a>
                        <ul id="reports-submenu" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                            <li class="sidebar-item">
                                <a href="{{ route('reports.inventory-by-warehouse') }}" class="sidebar-link">Inv. por
                                    Almacén</a>
                            </li>
                            <li class="sidebar-item">
                                <a href="{{ route('reports.historical-movements') }}" class="sidebar-link">Mov.
                                    Históricos</a>
                            </li>
                        </ul>
                    </li>
                @endcan
                {{-- fin de reportes --}}

                @auth
                    <li class="sidebar-item">
                        <a href="#login-submenu" data-bs-toggle="collapse" class="sidebar-link collapsed">
                            <i class="bi bi-file-earmark-bar-graph"></i>
                            <span>{{ Auth::user()->name }}</span>
                            <i class="bi bi-chevron-down ms-auto"></i>
                        </a>
                        <ul id="login-submenu" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                            <li class="sidebar-item">
                                <a class="sidebar-link" href="{{ route('logout') }}"
                                    onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">{{ __('Logout') }}</a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>

                        </ul>
                    </li>

                @endauth


                {{-- <li class="sidebar-item">
                    <a href="#" class="sidebar-link">
                        <i class="bi bi-gear"></i>
                        <span>Configuración</span>
                    </a>
                </li> --}}
            </ul>
            <div class="sidebar-footer">
                <a href="#" id="theme-toggle" class="sidebar-link">
                    <i class="bi bi-sun"></i>
                    <span>Modo Claro</span>
                </a>
            </div>
        </aside>

        <!-- Page Content -->
        <div class="main">
            <nav class="navbar navbar-expand px-3 border-bottom">
                <button class="btn" id="sidebar-toggle" type="button">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <!-- Navbar elements here -->
            </nav>
            <main class="content px-4 py-3">
                <div class="container-fluid">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>
</body>

</html>
