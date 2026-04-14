@extends('recepcionista.layout')

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

    .loading-row {
        opacity: 0.5;
        font-style: italic;
    }

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

    }
</style>
@endsection

@section('content')
<header>
    <div>
        <h1>Reservaciones</h1>
        <p style="color: var(--text-muted); margin-top: 0.25rem;">Gestión de entradas, salidas y nuevas reservas.</p>
    </div>
    <button class="btn-primary" onclick="abrirModal('createModal')" style="display: flex; align-items: center;">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right: 0.5rem;"><path d="M12 4v16m8-8H4"></path></svg>
        Nueva Reserva
    </button>
</header>

<div class="filter-container" id="filterContainer">
    <button class="filter-btn active" data-estado="">Todas</button>
    <button class="filter-btn" data-estado="activa">Activas</button>
    <button class="filter-btn" data-estado="pendiente">Pendientes</button>
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
                <th>Monto</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody id="reservasTableBody">
            <tr class="loading-row">
                <td colspan="7" style="text-align: center;">Cargando...</td>
            </tr>
        </tbody>
    </table>
</section>

<!-- Modal Crear Reserva -->
<div id="createModal" class="modal">
    <div class="modal-content">
        <button class="btn-close" onclick="cerrarModal('createModal')">&times;</button>
        <h2 style="margin-top: 0; margin-bottom: 1.5rem;">Nueva Reserva</h2>
        
        <form id="createReservaForm">
            @csrf
            <div class="form-group">
                <label>Cliente</label>
                <select name="id_cliente" id="id_cliente" class="form-control" required>
                    <option value="">Seleccione...</option>
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
                    <option value="">Seleccione fechas...</option>
                </select>
            </div>

            <div class="form-group">
                <label>Monto</label>
                <input type="number" step="0.01" name="monto_pago" id="monto_pago" class="form-control" required readonly>
            </div>

            <div class="form-group">
                <label>Método</label>
                <select name="metodo_pago" class="form-control" required>
                    <option value="efectivo">Efectivo</option>
                    <option value="tarjeta_credito">Tarjeta de Crédito</option>
                    <option value="tarjeta_debito">Tarjeta de Débito</option>
                    <option value="transferencia">Transferencia Bancaria</option>
                </select>
            </div>
            
            <div style="display: flex; justify-content: flex-end; gap: 1rem;">
                <button type="button" class="btn-primary" style="background: var(--surface); color: var(--text-main); border: 1px solid var(--border);" onclick="cerrarModal('createModal')">Cancelar</button>
                <button type="submit" class="btn-primary">Registrar</button>
            </div>
        </form>
    </div>
</div>
@endsection

<!-- Overlay de Simulación de Pago -->
<div id="paymentOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); backdrop-filter: blur(8px); z-index: 9999; flex-direction: column; align-items: center; justify-content: center; color: white; text-align: center;">
    <div style="background: rgba(255,255,255,0.1); padding: 3rem; border-radius: 24px; border: 1px solid rgba(255,255,255,0.2); box-shadow: 0 20px 50px rgba(0,0,0,0.3); max-width: 400px; width: 90%;">
        <div id="paymentIcon" style="margin-bottom: 1.5rem;">
            <svg class="animate-spin" width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin: 0 auto; color: var(--primary);"><path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83"></path></svg>
        </div>
        <h2 id="paymentStatusTitle" style="margin-bottom: 1rem; font-size: 1.5rem;">Procesando Pago</h2>
        <p id="paymentStatusMsg" style="color: rgba(255,255,255,0.7); font-size: 0.9rem;">Validando transacción con servidor externo...</p>
    </div>
</div>

<style>
    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    .animate-spin { animation: spin 1s linear infinite; }
</style>

@section('scripts')
<script src="{{ asset('js/adminReservas.js') }}"></script>
@endsection
