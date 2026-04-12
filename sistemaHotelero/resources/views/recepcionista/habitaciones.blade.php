@extends('recepcionista.layout')

@section('title', 'Estado de Habitaciones')

@section('styles')
<style>
    .filter-container {
        display: flex;
        gap: 0.75rem;
        margin-bottom: 2rem;
        flex-wrap: wrap;
    }
    
    .filter-btn {
        padding: 0.5rem 1.25rem;
        border-radius: 50px;
        border: 1px solid var(--border);
        background: var(--surface);
        color: var(--text-muted);
        font-weight: 500;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .filter-btn:hover {
        background: var(--bg);
        border-color: var(--primary);
        color: var(--primary);
    }
    
    .filter-btn.active {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
    }

    .room-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 2rem;
        margin-top: 1rem;
    }
    
    .room-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 2rem 1.5rem;
        text-align: center;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .room-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: transparent;
        transition: background 0.3s;
    }
    
    .room-card[data-estado="disponible"]::before { background: var(--success); }
    .room-card[data-estado="ocupada"]::before { background: var(--primary); }
    .room-card[data-estado="mantenimiento"]::before { background: var(--warning); }
    
    .room-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.06);
        border-color: var(--primary);
    }
    
    .room-number {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 0.25rem;
    }
    
    .room-type {
        font-size: 0.7rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.1em;
        font-weight: 600;
        margin-bottom: 1.5rem;
        display: block;
    }
    
    .room-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.4rem 0.75rem;
        border-radius: 8px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 1.5rem;
    }
    
    .badge-disponible { background: rgba(16, 185, 129, 0.1); color: var(--success); }
    .badge-ocupada { background: rgba(79, 70, 229, 0.1); color: var(--primary); }
    .badge-mantenimiento { background: rgba(245, 158, 11, 0.1); color: var(--warning); }
    
    .room-actions {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .btn-action {
        padding: 0.5rem;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        border: 1px solid transparent;
    }
</style>
@endsection

@section('content')
<header>
    <div>
        <h1>Disponibilidad de Habitaciones</h1>
        <p style="color: var(--text-muted); margin-top: 0.25rem;">Vista en tiempo real del inventario de habitaciones.</p>
    </div>
</header>

<div class="filter-container" style="margin-top: 2rem;">
    <button class="filter-btn active" onclick="filtrarHabitaciones('todas', this)">Todas</button>
    <button class="filter-btn" onclick="filtrarHabitaciones('disponible', this)">Disponibles</button>
    <button class="filter-btn" onclick="filtrarHabitaciones('ocupada', this)">Ocupadas</button>
</div>

<div class="room-grid" id="roomGrid">
    <div style="grid-column: 1/-1; text-align: center; padding: 4rem; color: var(--text-muted);">
        Consultando estados de habitación...
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/recepcHabitaciones.js') }}"></script>
@endsection
