@extends('admin.layout')

@section('title', 'Gestión de Reservaciones')

@section('styles')
<style>
    .filter-container {
        display: flex;
        gap: 1rem;
        margin-bottom: 2rem;
        align-items: center;
    }

    .filter-btn {
        padding: 0.5rem 1rem;
        border-radius: 8px;
        border: 1px solid var(--border);
        background: var(--surface);
        color: var(--text-muted);
        text-decoration: none;
        font-size: 0.9rem;
        transition: all 0.2s;
        cursor: pointer;
    }

    .filter-btn.active {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    .filter-btn:hover:not(.active) {
        background: var(--bg);
        color: var(--text-main);
    }

    .search-input {
        padding: 0.5rem 1rem;
        border-radius: 8px;
        border: 1px solid var(--border);
        font-size: 0.9rem;
        width: 300px;
    }

    /* Skeleton or loading state */
    .loading-row {
        opacity: 0.5;
        font-style: italic;
    }
</style>
@endsection

@section('content')
<header>
    <div>
        <h1>Reservaciones</h1>
        <p style="color: var(--text-muted); margin-top: 0.25rem;">Administra todas las reservas del sistema (API-driven).</p>
    </div>
    <button class="btn-primary">+ Nueva Reserva</button>
</header>

<div class="filter-container" id="filterContainer">
    <button class="filter-btn active" data-estado="">Todas</button>
    <button class="filter-btn" data-estado="activa">Activas</button>
    <button class="filter-btn" data-estado="finalizada">Finalizadas</button>
    <button class="filter-btn" data-estado="cancelada">Canceladas</button>
</div>

<section class="glass-panel">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h2 class="panel-header" style="margin-bottom: 0;">Lista de Reservaciones</h2>
        <input type="text" class="search-input" placeholder="Buscar por cliente o ID..." id="searchReserva">
    </div>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Habitación</th>
                <th>Período</th>
                <th>Estado</th>
                <th>Monto (Calculado)</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody id="reservasTableBody">
            <tr class="loading-row">
                <td colspan="7" style="text-align: center;">Cargando reservaciones...</td>
            </tr>
        </tbody>
    </table>
</section>
@endsection

@section('scripts')
<script src="{{ asset('js/adminReservas.js') }}"></script>
@endsection
