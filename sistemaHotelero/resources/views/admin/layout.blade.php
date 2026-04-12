<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sistema Hotelero - @yield('title', 'Dashboard')</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    @yield('styles')
</head>
<body>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="brand">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            Hotel LUX
        </div>
        <nav class="nav">
            <a href="/admin-dashboard" class="nav-item {{ Request::is('admin-dashboard') ? 'active' : '' }}">Dashboard</a>
            <a href="/admin/reservas" class="nav-item {{ Request::is('admin/reservas*') ? 'active' : '' }}">Reservaciones</a>
            <a href="/admin/habitaciones" class="nav-item {{ Request::is('admin/habitaciones*') ? 'active' : '' }}">Habitaciones</a>
            <a href="/admin/clientes" class="nav-item {{ Request::is('admin/clientes*') ? 'active' : '' }}">Clientes</a>
            <a href="/admin/empleados" class="nav-item {{ Request::is('admin/empleados*') ? 'active' : '' }}">
                
                Empleados
            </a>
            <a href="/admin/facturacion" class="nav-item {{ Request::is('admin/facturacion*') ? 'active' : '' }}">Facturación</a>
            <a href="#" class="nav-item" id="btnLogout">Cerrar Sesión</a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        @yield('content')
    </main>

    @yield('scripts')
    <script src="{{ asset('js/dashboardAdmin.js') }}"></script>
</body>
</html>
