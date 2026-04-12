@extends('admin.layout')

@section('title', 'Vista General')

@section('content')
<header>
    <div>
        <h1>Vista General</h1>
        <p style="color: var(--text-muted); margin-top: 0.25rem;">Bienvenido de vuelta. Aquí está el resumen de hoy.</p>
    </div>
    <button class="btn-primary" onclick="window.location.href='{{ route('admin.reservas') }}'">Ver Reservaciones</button>
</header>

<section class="stats-grid">
    <div class="stat-card" style="border-top: 4px solid #6366f1;">
        <div class="stat-icon" style="opacity: 1; color: #6366f1;">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
        </div>
        <div class="stat-title">Habitaciones</div>
        <div class="stat-value">{{ $totales }}</div>
    </div>
    <div class="stat-card" style="border-top: 4px solid var(--success);">
        <div class="stat-icon" style="opacity: 1; color: var(--success)">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"></path></svg>
        </div>
        <div class="stat-title">Disponibles</div>
        <div class="stat-value">{{ $disponibles }}</div>
    </div>
    <div class="stat-card" style="border-top: 4px solid var(--primary);">
        <div class="stat-icon" style="opacity: 1; color: var(--primary)">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
        </div>
        <div class="stat-title">Activas</div>
        <div class="stat-value">{{ $activas }}</div>
    </div>
    <div class="stat-card" style="border-top: 4px solid var(--warning);">
        <div class="stat-icon" style="opacity: 1; color: var(--warning)">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
        </div>
        <div class="stat-title">Mantenimiento</div>
        <div class="stat-value">{{ $mantenimiento }}</div>
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
