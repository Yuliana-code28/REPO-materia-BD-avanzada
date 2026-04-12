@extends('admin.layout')

@section('title', 'Facturación y Finanzas')

@section('styles')
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: var(--surface);
        padding: 1.5rem;
        border-radius: 12px;
        border: 1px solid var(--border);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .stat-value {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--primary);
        margin: 0.5rem 0;
    }

    .stat-label {
        font-size: 0.85rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .report-flex {
        display: flex;
        gap: 2rem;
        margin-bottom: 2rem;
        flex-wrap: wrap;
    }

    .report-panel {
        flex: 1;
        min-width: 300px;
        display: flex;
        flex-direction: column;
    }

    .chart-container {
        position: relative;
        height: 250px;
        margin-top: 1rem;
    }

    .mini-table {
        width: 100%;
        font-size: 0.9rem;
    }

    .method-badge {
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: capitalize;
    }

    .method-efectivo { background: rgba(16, 185, 129, 0.1); color: #10b981; }
    .method-tarjeta { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
    .method-transferencia { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
</style>
@endsection

@section('content')
<header>
    <div>
        <h1>Facturación y Finanzas</h1>
        <p style="color: var(--text-muted); margin-top: 0.25rem;">Reportes financieros basados en ingresos reales y proyecciones anuales.</p>
    </div>
</header>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Ingresos Históricos</div>
        <div class="stat-value" id="statHistorico">$0.00</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Ticket Promedio</div>
        <div class="stat-value" id="statPromedio">$0.00</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Transacciones</div>
        <div class="stat-value" id="statConteo">0</div>
    </div>
</div>

<div class="report-flex">
    <section class="glass-panel report-panel">
        <h2 class="panel-header">Ingresos Mensuales</h2>
        <div class="chart-container">
            <canvas id="monthlyChart"></canvas>
        </div>
        <table class="mini-table" style="margin-top: 1.5rem;">
            <thead>
                <tr>
                    <th>Periodo</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody id="reporteMensualBody">
                <tr><td colspan="2" style="text-align: center; padding: 1rem;">Cargando...</td></tr>
            </tbody>
        </table>
    </section>

    <section class="glass-panel report-panel">
        <h2 class="panel-header">Resumen Anual</h2>
        <div class="chart-container">
            <canvas id="yearlyChart"></canvas>
        </div>
        <table class="mini-table" style="margin-top: 1.5rem;">
            <thead>
                <tr>
                    <th>Año</th>
                    <th style="text-align: right;">Ingreso</th>
                </tr>
            </thead>
            <tbody id="reporteAnualBody">
                <tr><td colspan="2" style="text-align: center; padding: 1rem;">Cargando...</td></tr>
            </tbody>
        </table>
    </section>
</div>

<section class="glass-panel">
    <h2 class="panel-header">Historial de Transacciones</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Reserva</th>
                <th>Monto</th>
                <th>Fecha</th>
                <th>Método</th>
            </tr>
        </thead>
        <tbody id="pagosTableBody">
            <tr class="loading-row">
                <td colspan="6" style="text-align: center; padding: 3rem;">Analizando movimientos...</td>
            </tr>
        </tbody>
    </table>
</section>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('js/adminFacturacion.js') }}"></script>
@endsection
