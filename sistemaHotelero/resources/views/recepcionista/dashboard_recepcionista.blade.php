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
@endsection

@section('scripts')
<script src="{{ asset('js/dashboardRecepcionista.js') }}"></script>
@endsection