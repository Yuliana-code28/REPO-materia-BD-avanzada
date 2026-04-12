<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Hotel LUX - @yield('title', 'Recepción')</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <style>
        :root {
            --primary: #8b5cf6;
            --primary-light: #ddd6fe;
        }
        .sidebar { background: #1e1b4b; }
        .brand span { color: white; }
        .nav-item.active { background: rgba(139, 92, 246, 0.1) !important; color: var(--primary) !important; }
        .nav-item:hover:not(.active) { background: rgba(255,255,255,0.05); }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="brand">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--primary);"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            <span style="letter-spacing: -0.025em; font-weight: 600;">Hotel <span style="color: var(--primary);">LUX</span></span>
        </div>
        <nav class="nav">
            <a href="/recepcionista-dashboard" class="nav-item {{ Request::is('recepcionista-dashboard') ? 'active' : '' }}">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                Inicio
            </a>
            <a href="/recepcionista/reservas" class="nav-item {{ Request::is('recepcionista/reservas*') ? 'active' : '' }}">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Reservaciones
            </a>
            <a href="/recepcionista/habitaciones" class="nav-item {{ Request::is('recepcionista/habitaciones*') ? 'active' : '' }}">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                Habitaciones
            </a>
            <a href="/recepcionista/servicios" class="nav-item {{ Request::is('recepcionista/servicios*') ? 'active' : '' }}">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                Servicios
            </a>
            
            <div style="margin-top: auto; padding-top: 2rem; border-top: 1px solid var(--border);">
                <a href="#" class="nav-item" id="btnLogout" style="color: #ef4444;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Cerrar Sesión
                </a>
            </div>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        @yield('content')
    </main>

    <script src="{{ asset('js/dashboardAdmin.js') }}"></script>
    @yield('scripts')
</body>
</html>
