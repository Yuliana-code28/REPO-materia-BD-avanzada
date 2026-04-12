@extends('admin.layout')

@section('title', 'Gestión de Empleados')

@section('styles')
<style>
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
        margin: 5% auto;
        padding: 2rem;
        border-radius: 12px;
        width: 600px;
        max-width: 90%;
        border: 1px solid var(--border);
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    }
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    .form-group {
        margin-bottom: 1.25rem;
    }
    .form-group.full-width {
        grid-column: span 2;
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
    .section-title {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--primary);
        margin: 1rem 0;
        padding-bottom: 0.25rem;
        border-bottom: 1px solid var(--border);
        grid-column: span 2;
    }
    .search-input {
        padding: 0.5rem 1rem;
        border-radius: 8px;
        border: 1px solid var(--border);
        font-size: 0.9rem;
        width: 300px;
    }
    .badge-rol {
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .rol-admin { background: #fee2e2; color: #991b1b; }
    .rol-recepcionista { background: #dcfce7; color: #166534; }
    .rol-cliente { background: #f3f4f6; color: #374151; }
</style>
@endsection

@section('content')
<header>
    <div>
        <h1>Gestión de Personal</h1>
        <p style="color: var(--text-muted); margin-top: 0.25rem;">Administra los empleados y sus accesos al sistema.</p>
    </div>
    <button class="btn-primary" onclick="openModal('empleadoModal')" style="display: flex; align-items: center;">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right: 0.5rem;"><path d="M12 4v16m8-8H4"></path></svg>
        Nuevo Empleado
    </button>
</header>

<section class="glass-panel">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h2 class="panel-header" style="margin-bottom: 0;">Directorio de Empleados</h2>
        <input type="text" class="search-input" placeholder="Buscar por nombre, email o usuario..." id="searchEmpleado">
    </div>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Contacto</th>
                <th>Usuario</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="empleadosTableBody">
            <tr class="loading-row">
                <td colspan="6" style="text-align: center; padding: 2rem;">Cargando empleados...</td>
            </tr>
        </tbody>
    </table>
</section>

<!-- Modal Empleado -->
<div id="empleadoModal" class="modal">
    <div class="modal-content">
        <button class="btn-close" onclick="closeModal('empleadoModal')">&times;</button>
        <h2 id="modalTitle" style="margin-top: 0; margin-bottom: 1.5rem;">Registrar Empleado</h2>
        
        <form id="empleadoForm">
            @csrf
            <input type="hidden" name="id_empleado" id="id_empleado">
            
            <div class="form-grid">
                <div class="section-title">Datos Personales</div>
                
                <div class="form-group">
                    <label>Nombre(s)</label>
                    <input type="text" name="nombre" id="nombre" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Primer Apellido</label>
                    <input type="text" name="ap" id="ap" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Segundo Apellido</label>
                    <input type="text" name="am" id="am" class="form-control" required>
                </div>
                
                <div class="section-title">Contacto</div>
                
                <div class="form-group">
                    <label>Teléfono</label>
                    <input type="text" name="telefono" id="telefono" class="form-control">
                </div>
                <div class="form-group">
                    <label>Correo Electrónico</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>

                <div class="section-title">Acceso al Sistema</div>
                
                <div class="form-group">
                    <label>Usuario</label>
                    <input type="text" name="username" id="username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Rol de Sistema</label>
                    <select name="id_rol" id="id_rol" class="form-control" required>
                        <option value="">Seleccione un rol...</option>
                        <!-- Roles loaded via JS -->
                    </select>
                </div>
                <div class="form-group full-width">
                    <label id="labelPassword">Contraseña</label>
                    <input type="password" name="password" id="password" class="form-control" required minlength="6">
                    <small id="passwordHelp" style="color: var(--text-muted); display: none;">Deje en blanco para mantener la actual.</small>
                </div>
            </div>
            
            <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem;">
                <button type="button" class="btn-primary" style="background: var(--surface); color: var(--text-main); border: 1px solid var(--border);" onclick="closeModal('empleadoModal')">Cancelar</button>
                <button type="submit" class="btn-primary" id="btnSubmit">Guardar Empleado</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/adminEmpleados.js') }}"></script>
@endsection
