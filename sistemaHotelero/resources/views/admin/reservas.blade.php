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

    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
        backdrop-filter: blur(4px);
    }
    .modal-content {
        background-color: var(--surface);
        margin: 10% auto;
        padding: 2rem;
        border-radius: 12px;
        width: 500px;
        max-width: 90%;
        border: 1px solid var(--border);
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    }
    .form-group {
        margin-bottom: 1.5rem;
    }
    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: var(--text-main);
    }
    .form-control {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid var(--border);
        border-radius: 8px;
        background: var(--bg);
        color: var(--text-main);
        font-family: inherit;
        outline: none;
    }
    .form-control:focus {
        border-color: var(--primary);
    }
    .btn-close {
        float: right;
        font-size: 1.5rem;
        font-weight: bold;
        cursor: pointer;
        color: var(--text-muted);
        border: none;
        background: none;
    }
</style>
@endsection

@section('content')
<header>
    <div>
        <h1>Reservaciones</h1>
        <p style="color: var(--text-muted); margin-top: 0.25rem;">Administra todas las reservas del sistema.</p>
    </div>
    <button class="btn-primary" onclick="openModal('createModal')" style="display: flex; align-items: center;">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right: 0.5rem;"><path d="M12 4v16m8-8H4"></path></svg>
        Nueva Reserva
    </button>
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

<!-- Modal Crear Reserva -->
<div id="createModal" class="modal">
    <div class="modal-content">
        <button class="btn-close" onclick="closeModal('createModal')">&times;</button>
        <h2 style="margin-top: 0; margin-bottom: 1.5rem;">Nueva Reserva</h2>
        
        <form id="createReservaForm">
            @csrf
            
            <div class="form-group">
                <label>Cliente</label>
                <select name="id_cliente" id="id_cliente" class="form-control" required>
                    <option value="">Cargando clientes...</option>
                </select>
            </div>
            
            <div style="display: flex; gap: 1rem;">
                <div class="form-group" style="flex: 1;">
                    <label>Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" required min="{{ date('Y-m-d') }}">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Fecha Fin</label>
                    <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" required min="{{ date('Y-m-d') }}">
                </div>
            </div>

            <div class="form-group">
                <label>Habitación</label>
                <select name="id_habitacion" id="id_habitacion" class="form-control" required disabled>
                    <option value="" data-precio="0">Seleccione primero las fechas...</option>
                </select>
            </div>

            <div class="form-group">
                <label>Monto Pago</label>
                <input type="number" step="0.01" name="monto_pago" id="monto_pago" class="form-control" required placeholder="Calculado automáticamente" readonly>
            </div>

            <div class="form-group">
                <label>Método de Pago</label>
                <select name="metodo_pago" class="form-control" required>
                    <option value="efectivo">Efectivo</option>
                    <option value="tarjeta_credito">Tarjeta de Crédito</option>
                    <option value="tarjeta_debito">Tarjeta de Débito</option>
                    <option value="transferencia">Transferencia</option>
                </select>
            </div>
            
            <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem;">
                <button type="button" class="btn-primary" style="background: var(--surface); color: var(--text-main); border: 1px solid var(--border);" onclick="closeModal('createModal')">Cancelar</button>
                <button type="submit" class="btn-primary">Guardar Reserva</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/adminReservas.js') }}"></script>
@endsection
