@extends('cliente.layout')

@section('title', 'Mi Dashboard')

@section('content')
<header>
    <div>
        <h1 id="welcomeMessage">Bienvenido</h1>
        <p style="color: var(--text-muted); margin-top: 0.25rem;">Aquí puedes ver un resumen de tus estancias y consumos.</p>
    </div>
</header>

<div class="stats-grid" style="margin-top: 2rem;">
    <div class="stat-card" style="border-top: 4px solid var(--primary);">
        <div class="stat-title">Reservas Totales</div>
        <div class="stat-value" id="statTotalReservas">-</div>
    </div>
    <div class="stat-card" style="border-top: 4px solid var(--success);">
        <div class="stat-title">Invertido en Mi Descanso</div>
        <div class="stat-value" id="statTotalInvertido">$0.00</div>
    </div>
</div>

<div style="margin-top: 2rem; display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
    <!-- Estancia Actual -->
    <section class="glass-panel" style="margin-bottom: 0;">
        <h2 class="panel-header">Tu Estancia Actual / Próxima</h2>
        <div id="estanciaContainer" style="min-height: 100px; display: flex; align-items: center; justify-content: center;">
            <p style="color: var(--text-muted);">Cargando información...</p>
        </div>
    </section>

    <!-- Mis Consumos -->
    <section class="glass-panel" style="margin-bottom: 0;">
        <h2 class="panel-header">Detalle de Cargos y Servicios</h2>
        <div id="consumosContainer" style="overflow-y: auto; max-height: 250px;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--border); font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">
                        <th style="text-align: left; padding-bottom: 0.5rem;">Servicio / Hab</th>
                        <th style="text-align: center; padding-bottom: 0.5rem;">Cant</th>
                        <th style="text-align: right; padding-bottom: 0.5rem;">Subtotal</th>
                    </tr>
                </thead>
                <tbody id="listaConsumosBody">
                    <tr><td colspan="3" style="text-align: center; color: var(--text-muted); padding: 1rem;">Cargando consumos...</td></tr>
                </tbody>
            </table>
        </div>
        <div id="totalServiciosContainer" style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border); display: none;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="font-weight: 500; font-size: 0.9rem;">Subtotal Servicios:</span>
                <span id="labelTotalServicios" style="font-weight: 600; color: var(--primary);">$0.00</span>
            </div>
        </div>
    </section>
</div>

<div style="margin-top: 2rem;">
    <!-- Mi Info -->
    <section class="glass-panel" style="margin-bottom: 0;">
        <h2 class="panel-header">Mi Información</h2>
        <div id="userInfo" style="display: flex; gap: 3rem; align-items: center;">
            <div>
                <label style="font-size: 0.8rem; color: var(--text-muted); display: block;">Usuario</label>
                <span id="infoUsername" style="font-weight: 500;">-</span>
            </div>
            <div>
                <label style="font-size: 0.8rem; color: var(--text-muted); display: block;">Rol</label>
                <span id="infoRol" style="font-weight: 500;">Huésped Distinguido</span>
            </div>
            <div>
                <label style="font-size: 0.8rem; color: var(--text-muted); display: block;">Mi ID de Cliente</label>
                <span id="infoIdCliente" style="font-weight: 500;">#0000</span>
            </div>
        </div>
    </section>
</div>

<section class="glass-panel" style="margin-top: 2rem;">
    <h2 class="panel-header">Historial de Reservas</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Habitación</th>
                <th>Fecha Inicio</th>
                <th>Fecha Fin</th>
                <th>Estado</th>
                <th>Monto</th>
            </tr>
        </thead>
        <tbody id="historialReservasBody">
            <tr><td colspan="6" style="text-align: center;">Cargando historial...</td></tr>
        </tbody>
    </table>
</section>
@endsection

@section('scripts')
<script src="{{ asset('js/clienteDashboard.js') }}"></script>
@endsection