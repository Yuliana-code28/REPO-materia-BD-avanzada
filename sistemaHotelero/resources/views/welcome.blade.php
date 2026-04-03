<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Hotelero - Dashboard</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4F46E5;
            --primary-hover: #4338CA;
            --bg: #F3F4F6;
            --surface: #FFFFFF;
            --text-main: #1F2937;
            --text-muted: #6B7280;
            --success: #10B981;
            --warning: #F59E0B;
            --border: #E5E7EB;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: var(--bg);
            color: var(--text-main);
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Sidebar */
        .sidebar {
            width: 250px;
            background-color: var(--surface);
            border-right: 1px solid var(--border);
            padding: 2rem 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 2rem;
            box-shadow: 2px 0 5px rgba(0,0,0,0.02);
            z-index: 10;
        }

        .brand {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .nav-item {
            text-decoration: none;
            color: var(--text-muted);
            padding: 0.75rem 1rem;
            border-radius: 8px;
            transition: all 0.2s ease;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .nav-item.active {
            background-color: rgba(79, 70, 229, 0.1);
            color: var(--primary);
        }

        .nav-item:hover {
            background-color: var(--bg);
            color: var(--text-main);
            transform: translateX(4px);
        }

        /* Main Content */
        .main-content {
            flex-grow: 1;
            padding: 2.5rem 3rem;
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        header h1 {
            font-size: 1.75rem;
            color: var(--text-main);
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 0.6rem 1.25rem;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s;
            box-shadow: 0 4px 6px rgba(79, 70, 229, 0.2);
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
        }

        .stat-card {
            background-color: var(--surface);
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.03);
            border: 1px solid var(--border);
            transition: transform 0.2s ease-in-out;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.05);
        }

        .stat-title {
            font-size: 0.9rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 600;
            color: var(--text-main);
        }

        .stat-icon {
            float: right;
            font-size: 2rem;
            opacity: 0.2;
        }

        /* Table Section */
        .glass-panel {
            background-color: var(--surface);
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.03);
            border: 1px solid var(--border);
            animation: fadeIn 0.5s ease-out;
        }

        .panel-header {
            margin-bottom: 1.5rem;
            font-size: 1.25rem;
            font-weight: 600;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        th {
            font-weight: 600;
            color: var(--text-muted);
            font-size: 0.85rem;
            text-transform: uppercase;
        }

        tr:hover td {
            background-color: var(--bg);
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-activa {
            background-color: rgba(16, 185, 129, 0.15);
            color: var(--success);
        }

        .badge-finalizada {
            background-color: rgba(107, 114, 128, 0.15);
            color: var(--text-muted);
        }

        .badge-cancelada {
            background-color: rgba(245, 158, 11, 0.15);
            color: var(--warning);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

    </style>
</head>
<body>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="brand">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            Hotel LUX
        </div>
        <nav class="nav">
            <a href="#" class="nav-item active">Dashboard</a>
            <a href="#" class="nav-item">Reservaciones</a>
            <a href="#" class="nav-item">Habitaciones</a>
            <a href="#" class="nav-item">Clientes</a>
            <a href="#" class="nav-item">Facturación</a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <header>
            <div>
                <h1>Vista General</h1>
                <p style="color: var(--text-muted); margin-top: 0.25rem;">Bienvenido de vuelta. Aquí está el resumen de hoy.</p>
            </div>
            <button class="btn-primary">+ Nueva Reserva</button>
        </header>

        <!-- Stats -->
        <section class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-title">Total Clientes</div>
                <div class="stat-value">{{ $clientesCount }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🛏️</div>
                <div class="stat-title">Habitaciones Disp.</div>
                <div class="stat-value">{{ $habitacionesDisponibles }}</div>
            </div>
            <div class="stat-card" style="border-left: 4px solid var(--primary);">
                <div class="stat-icon">📅</div>
                <div class="stat-title">Reservas Activas</div>
                <div class="stat-value" style="color: var(--primary);">{{ $reservasActivas }}</div>
            </div>
        </section>

        <!-- Table -->
        <section class="glass-panel">
            <h2 class="panel-header">Últimas Reservas</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID Reserva</th>
                        <th>Cliente</th>
                        <th>Fecha de Registro</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ultimasReservas as $reserva)
                    <tr>
                        <td style="font-weight: 500;">#{{ str_pad($reserva->id_reserva, 4, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $reserva->cliente->nombre ?? 'N/A' }}</td>
                        <td>{{ \Carbon\Carbon::parse($reserva->fecha_registro)->format('d M, Y') }}</td>
                        <td>
                            <span class="status-badge badge-{{ strtolower($reserva->estado) }}">
                                {{ $reserva->estado }}
                            </span>
                        </td>
                        <td>
                            <a href="#" style="color: var(--primary); text-decoration: none; font-size: 0.9rem; font-weight: 500;">Ver detalle</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    </main>

</body>
</html>
