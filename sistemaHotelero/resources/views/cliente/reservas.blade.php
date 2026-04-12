@extends('cliente.layout')

@section('title', 'Hacer una Reserva')

@section('content')
<header>
    <div>
        <h1>Hacer una Reserva</h1>
        <p style="color: var(--text-muted); margin-top: 0.25rem;">Elige tus fechas y encuentra tu habitación ideal.</p>
    </div>
</header>

<div style="max-width: 800px; margin-top: 2rem;">
    <section class="glass-panel">
        <h2 class="panel-header">Detalles de tu próxima estancia</h2>
        
        <div id="limitAlert" style="display: none; background: rgba(239, 68, 68, 0.1); border: 1px solid #ef4444; color: #ef4444; padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <div>
                    <strong style="display: block;">Límite de Reservas Alcanzado</strong>
                    <span style="font-size: 0.9rem;">Has llegado al máximo de 4 habitaciones permitidas simultáneamente. Por favor gestiona tus estancias previas.</span>
                </div>
            </div>
        </div>
        
        <form id="reservaClienteForm">
            @csrf
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Fecha de Llegada</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control" required min="{{ date('Y-m-d') }}" style="width: 100%; padding: 0.75rem; border-radius: 8px; border: 1px solid var(--border);">
                </div>
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Fecha de Salida</label>
                    <input type="date" id="fecha_fin" name="fecha_fin" class="form-control" required min="{{ date('Y-m-d') }}" style="width: 100%; padding: 0.75rem; border-radius: 8px; border: 1px solid var(--border);">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 2rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Habitaciones Disponibles</label>
                <select id="id_habitacion" name="id_habitacion" class="form-control" required disabled style="width: 100%; padding: 0.75rem; border-radius: 8px; border: 1px solid var(--border);">
                    <option value="">Selecciona tus fechas primero...</option>
                </select>
            </div>

            <div style="background: var(--bg); padding: 2rem; border-radius: 12px; border: 1px solid var(--border); margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <span style="display: block; font-size: 0.9rem; color: var(--text-muted);">Total estimado</span>
                    <span id="labelPrecioTotal" style="font-size: 2rem; font-weight: 600; color: var(--primary);">$0.00</span>
                </div>
                
                <div class="form-group" style="margin-bottom: 0; width: 200px;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Método de Pago</label>
                    <select id="metodo_pago" name="metodo_pago" class="form-control" required style="width: 100%; padding: 0.75rem; border-radius: 8px; border: 1px solid var(--border);">
                        <option value="tarjeta_credito">Tarjeta de Crédito</option>
                        <option value="tarjeta_debito">Tarjeta de Débito</option>
                        <option value="transferencia">Transferencia Bancaria</option>
                        <option value="efectivo">Efectivo (en recepción)</option>
                    </select>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 1rem;">
                <a href="/cliente-dashboard" class="btn-primary" style="background: var(--surface); color: var(--text-main); border: 1px solid var(--border);">Volver</a>
                <button type="submit" id="btnConfirmar" class="btn-primary" disabled>Confirmar Reservación</button>
            </div>
        </form>
    </section>
</div>

<!-- Overlay de Simulación de Pago -->
<div id="paymentOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); backdrop-filter: blur(8px); z-index: 9999; flex-direction: column; align-items: center; justify-content: center; color: white; text-align: center;">
    <div style="background: rgba(255,255,255,0.1); padding: 3rem; border-radius: 24px; border: 1px solid rgba(255,255,255,0.2); box-shadow: 0 20px 50px rgba(0,0,0,0.3); max-width: 400px; width: 90%;">
        <div id="paymentIcon" style="margin-bottom: 1.5rem;">
            <!-- Spinner -->
            <svg class="animate-spin" width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin: 0 auto; color: var(--primary);"><path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83"></path></svg>
        </div>
        <h2 id="paymentStatusTitle" style="margin-bottom: 1rem; font-size: 1.5rem;">Procesando Pago</h2>
        <p id="paymentStatusMsg" style="color: rgba(255,255,255,0.7); font-size: 0.9rem;">Conectando con la entidad bancaria...</p>
    </div>
</div>

<style>
    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    .animate-spin { animation: spin 1s linear infinite; }
</style>

<style>
    .form-control:disabled {
        background: #f3f4f6;
        cursor: not-allowed;
    }
    .btn-primary:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
</style>
@endsection

@section('scripts')
<script src="{{ asset('js/clienteReservas.js') }}"></script>
@endsection
