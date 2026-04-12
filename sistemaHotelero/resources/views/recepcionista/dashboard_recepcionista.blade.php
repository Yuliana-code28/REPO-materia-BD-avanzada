@extends('recepcionista.layout')

@section('title', 'Dashboard Operativo')

@section('content')
<header>
    <div>
        <h1>Dashboard Recepción</h1>
        <p style="color: var(--text-muted); margin-top: 0.25rem;">Control operativo del hotel para el día de hoy.</p>
    </div>
</header>

<!-- Contenedor dinámico de estadísticas -->
<div class="stats-grid" id="statsGrid">
    <div style="grid-column: 1/-1; text-align: center; padding: 2rem; color: var(--text-muted);">
        Cargando estadísticas...
    </div>
</div>

<div style="margin-top: 2rem; display: grid; grid-template-columns: 1fr; gap: 2rem;">
    <section class="glass-panel" style="padding: 2rem;">
        <h2 class="panel-header">Distribución de Reservas</h2>
        <div style="height: 300px; display: flex; justify-content: center; align-items: center;">
            <canvas id="reservasChart"></canvas>
        </div>
    </section>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('js/dashboardRecepcionista.js') }}"></script>
@endsection