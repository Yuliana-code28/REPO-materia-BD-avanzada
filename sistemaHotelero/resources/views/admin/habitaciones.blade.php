@extends('admin.layout')

@section('title', 'Gestión de Habitaciones')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/adminHabitaciones.css') }}">
@endsection

@section('content')
<header>
    <div>
        <h1>Habitaciones</h1>
        <p style="color: var(--text-muted); margin-top: 0.25rem;">Administra el inventario de habitaciones, tipos y estados.</p>
    </div>
    <button class="btn-primary" onclick="abrirModal('createModal')" style="display: flex; align-items: center;">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right: 0.5rem;"><path d="M12 4v16m8-8H4"></path></svg>
        Nueva Habitación
    </button>
</header>



<div class="filter-container">
    <a href="#" data-estado="" class="filter-btn active">Todas</a>
    <a href="#" data-estado="disponible" class="filter-btn">Disponibles</a>
    <a href="#" data-estado="ocupada" class="filter-btn">Ocupadas</a>
    <a href="#" data-estado="mantenimiento" class="filter-btn">Mantenimiento</a>
</div>

<section class="glass-panel">
    <h2 class="panel-header">Lista de Habitaciones</h2>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Número</th>
                <th>Tipo</th>
                <th>Precio Base</th>
                <th>Estado</th>
                <th>Ocupante Actual</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="habitacionesTableBody">
            <tr class="loading-row">
                <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 3rem;">
                    Cargando habitaciones...
                </td>
            </tr>
        </tbody>
    </table>
</section>

<!-- Modal Crear -->
<div id="createModal" class="modal">
    <div class="modal-content">
        <button class="btn-close" onclick="cerrarModal('createModal')">&times;</button>
        <h2 style="margin-top: 0; margin-bottom: 1.5rem;">Nueva Habitación</h2>
        
        <form id="createHabitacionForm">
            <div class="form-group">
                <label>Número de Habitación</label>
                <input type="text" name="numero_habitacion" class="form-control" required maxlength="10" placeholder="Ej. 101, Suite-A...">
            </div>
            
            <div class="form-group">
                <label>Tipo de Habitación</label>
                <select name="id_tipo" id="create_tipo" class="form-control" required>
                    <option value="">Cargando tipos...</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Estado Inicial</label>
                <select name="estado" class="form-control" required>
                    <option value="disponible">Disponible</option>
                    <option value="ocupada">Ocupada</option>
                    <option value="mantenimiento">Mantenimiento</option>
                </select>
            </div>
            
            <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem;">
                <button type="button" class="btn-primary" style="background: var(--surface); color: var(--text-main); border: 1px solid var(--border);" onclick="cerrarModal('createModal')">Cancelar</button>
                <button type="submit" class="btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Editar -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <button class="btn-close" onclick="cerrarModal('editModal')">&times;</button>
        <h2 style="margin-top: 0; margin-bottom: 1.5rem;">Editar Habitación <span id="editTitleNum" style="color: var(--primary);"></span></h2>
        
        <form id="editHabitacionForm">
            <input type="hidden" id="edit_id_habitacion" name="id_habitacion">
            
            <div class="form-group">
                <label>Número de Habitación</label>
                <input type="text" name="numero_habitacion" id="edit_numero" class="form-control" required maxlength="10">
            </div>
            
            <div class="form-group">
                <label>Tipo de Habitación</label>
                <select name="id_tipo" id="edit_tipo" class="form-control" required>
                    <option value="">Cargando tipos...</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Estado</label>
                <select name="estado" id="edit_estado" class="form-control" required>
                    <option value="disponible">Disponible</option>
                    <option value="ocupada">Ocupada</option>
                    <option value="mantenimiento">Mantenimiento</option>
                </select>
            </div>
            
            <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem;">
                <button type="button" class="btn-primary" style="background: var(--surface); color: var(--text-main); border: 1px solid var(--border);" onclick="cerrarModal('editModal')">Cancelar</button>
                <button type="submit" class="btn-primary">Actualizar Cambios</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/adminHabitaciones.js') }}"></script>

@endsection
