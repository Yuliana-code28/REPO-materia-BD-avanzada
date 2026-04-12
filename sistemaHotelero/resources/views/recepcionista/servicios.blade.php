@extends('recepcionista.layout')

@section('title', 'Cargos por Servicio')

@section('styles')
<style>
    .service-layout {
        display: grid;
        grid-template-columns: 380px 1fr;
        gap: 2.5rem;
        align-items: start;
    }

    /* Guest List Styling */
    .guest-list {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
        max-height: calc(100vh - 200px);
        overflow-y: auto;
        padding-right: 0.5rem;
    }

    .guest-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 1.25rem;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        gap: 1rem;
        position: relative;
    }

    .guest-card:hover {
        transform: translateX(8px);
        border-color: var(--primary);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    .guest-card.selected {
        background: rgba(79, 70, 229, 0.04);
        border-color: var(--primary);
        box-shadow: 0 0 0 2px var(--primary);
    }

    .guest-avatar {
        width: 48px;
        height: 48px;
        background: var(--bg);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: var(--primary);
    }

    .guest-info {
        flex-grow: 1;
    }

    .guest-name {
        font-weight: 700;
        color: var(--text-main);
        display: block;
        margin-bottom: 0.2rem;
    }

    .guest-room {
        font-size: 0.75rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 600;
    }

    /* Form Styling */
    .charge-panel {
        background: var(--surface);
        border-radius: 20px;
        padding: 2.5rem;
        border: 1px solid var(--border);
        box-shadow: 0 10px 30px rgba(0,0,0,0.04);
        position: sticky;
        top: 2rem;
    }

    .panel-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .modern-form .form-group {
        margin-bottom: 1.75rem;
    }

    .modern-form label {
        display: block;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-muted);
        margin-bottom: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .modern-form .input-wrapper {
        position: relative;
    }

    .modern-form .form-control {
        width: 100%;
        padding: 1rem 1.25rem;
        border: 1.5px solid var(--border);
        border-radius: 12px;
        font-size: 1rem;
        transition: all 0.2s;
        background: var(--bg);
    }

    .modern-form .form-control:focus {
        border-color: var(--primary);
        background: var(--surface);
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        outline: none;
    }

    .modern-form .form-control[readonly] {
        background: #f9fafb;
        cursor: not-allowed;
        color: var(--text-muted);
    }

    .btn-submit-premium {
        width: 100%;
        padding: 1.25rem;
        background: var(--primary);
        color: white;
        border: none;
        border-radius: 14px;
        font-weight: 700;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        box-shadow: 0 8px 16px rgba(79, 70, 229, 0.2);
    }

    .btn-submit-premium:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 12px 24px rgba(79, 70, 229, 0.3);
    }

    .btn-submit-premium:disabled {
        background: #d1d5db;
        box-shadow: none;
        cursor: not-allowed;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: #f9fafb;
        border-radius: 16px;
        border: 2px dashed var(--border);
        color: var(--text-muted);
    }
</style>
@endsection

@section('content')
<header>
    <div>
        <h1>Cargos por Servicio</h1>
        <p style="color: var(--text-muted); margin-top: 0.25rem;">Gestión de consumos adicionales para huéspedes activos.</p>
    </div>
</header>

<div class="service-layout" style="margin-top: 2.5rem;">
    <!-- Lista de Huéspedes -->
    <section>
        <div style="font-weight: 700; color: var(--text-main); margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between;">
            DASHBOARD DE OCUPACIÓN
            <span style="font-size: 0.75rem; background: var(--primary); color: white; padding: 0.2rem 0.6rem; border-radius: 6px;" id="countBadge">0</span>
        </div>
        <div class="guest-list" id="activeReservationsList">
            <div style="text-align: center; color: var(--text-muted); padding: 3rem;">
                Cargando huéspedes activos...
            </div>
        </div>
    </section>

    <!-- Panel de Cargo -->
    <section class="charge-panel">
        <h2 class="panel-title">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.407 2.67 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.407-2.67-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Nueva Transacción
        </h2>
        
        <form id="serviceForm" class="modern-form">
            <input type="hidden" id="selected_reserva_id" name="id_reserva">
            
            <div class="form-group">
                <label>Habitación / Huésped Seleccionado</label>
                <div class="input-wrapper">
                    <input type="text" id="display_room" class="form-control" readonly placeholder="Selecciona un huésped del panel izquierdo">
                </div>
            </div>

            <div class="form-group">
                <label>Servicio o Producto</label>
                <div class="input-wrapper">
                    <select name="id_servicio" id="id_servicio" class="form-control" required>
                        <option value="">Cargando catálogo...</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Cantidad</label>
                <div class="input-wrapper">
                    <input type="number" name="cantidad" class="form-control" value="1" min="1" required>
                </div>
            </div>

            <button type="submit" class="btn-submit-premium" id="btnSubmit" disabled>
                Registrar Cargo
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"></path></svg>
            </button>
        </form>
        
        <div style="margin-top: 2rem; padding: 1rem; border-radius: 12px; border: 1px solid #f9fafb; display: flex; align-items: center; gap: 0.75rem; font-size: 0.85rem; color: var(--text-muted);">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            El cargo se sumará automáticamente a la facturación final del huésped.
        </div>
    </section>
</div>

@endsection

@section('scripts')
<script src="{{ asset('js/recepcServicios.js') }}"></script>
@endsection
