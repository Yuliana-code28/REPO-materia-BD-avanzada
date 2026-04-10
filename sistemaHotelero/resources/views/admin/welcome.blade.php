@extends('admin.layout')

@section('title', 'Vista General')

@section('content')
<header>
    <div>
        <h1>Vista General</h1>
        <p style="color: var(--text-muted); margin-top: 0.25rem;">Bienvenido de vuelta. Aquí está el resumen de hoy.</p>
    </div>
    <button class="btn-primary">+ Nueva Reserva</button>
</header>

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
@endsection
