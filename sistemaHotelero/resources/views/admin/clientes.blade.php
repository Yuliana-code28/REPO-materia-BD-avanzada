@extends('admin.layout')

@section('title', 'Análisis de Clientes')

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

    .badge-diamante { background: rgba(59, 130, 246, 0.15); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.3); }
    .badge-platino { background: rgba(168, 85, 247, 0.15); color: #a855f7; border: 1px solid rgba(168, 85, 247, 0.3); }
    .badge-estandar { background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); }
    .badge-nuevo { background: rgba(100, 116, 139, 0.15); color: #64748b; border: 1px solid rgba(100, 116, 139, 0.3); }

    .top-payer-badge {
        font-size: 0.70rem;
        padding: 0.15rem 0.4rem;
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        color: #fff;
        border-radius: 4px;
        display: inline-block;
        width: fit-content;
        font-weight: bold;
        box-shadow: 0 2px 4px rgba(245, 158, 11, 0.2);
    }

    .search-input {
        padding: 0.5rem 1rem;
        border-radius: 8px;
        border: 1px solid var(--border);
        font-size: 0.9rem;
        width: 300px;
    }
</style>
@endsection

@section('content')
<header>
    <div>
        <h1>Análisis de Clientes</h1>
        <p style="color: var(--text-muted); margin-top: 0.25rem;">Visualiza y segmenta el historial de hospedaje</p>
    </div>
</header>

<div class="filter-container">
    <button class="filter-btn active" data-clasificacion="">Todos</button>
    <button class="filter-btn" data-clasificacion="CLIENTE DIAMANTE">Diamantes</button>
    <button class="filter-btn" data-clasificacion="CLIENTE PLATINO">Platinos</button>
    <button class="filter-btn" data-clasificacion="CLIENTE ESTANDAR">Estándar</button>
    <button class="filter-btn" data-clasificacion="TOP_PAYERS">Top Pagadores</button>
</div>

<section class="glass-panel">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h2 class="panel-header" style="margin-bottom: 0;">Directorio Analítico</h2>
        <input type="text" class="search-input" placeholder="Buscar por nombre, email..." id="searchCliente">
    </div>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre del Cliente</th>
                <th>Contacto</th>
                <th style="text-align: center;">Total Reservas</th>
                <th>Inversión Total</th>
                <th>Clasificación (CASE)</th>
            </tr>
        </thead>
        <tbody id="clientesTableBody">
            <tr class="loading-row">
                <td colspan="6" style="text-align: center; padding: 3rem; color: var(--text-muted); font-style: italic;">Ejecutando consultas en la base de datos...</td>
            </tr>
        </tbody>
    </table>
</section>
@endsection

@section('scripts')
<script src="{{ asset('js/adminClientes.js') }}"></script>
@endsection
