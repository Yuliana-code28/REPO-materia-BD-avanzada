let empleadosActuales = [];
let rolesActuales = [];

document.addEventListener('DOMContentLoaded', () => {
    obtenerRoles();
    obtenerEmpleados();

    // Búsqueda
    const entradaBusqueda = document.getElementById('searchEmpleado');
    if (entradaBusqueda) {
        entradaBusqueda.addEventListener('keyup', filtrarYRenderizar);
    }

    // Formulario
    const form = document.getElementById('empleadoForm');
    if (form) {
        form.addEventListener('submit', manejarEnvioFormulario);
    }
});

async function obtenerRoles() {
    try {
        const respuesta = await fetch('/api/admin/roles');
        rolesActuales = await respuesta.json();
        
        const selectRol = document.getElementById('id_rol');
        if (selectRol) {
            selectRol.innerHTML = '<option value="">Seleccione un rol...</option>' + 
                rolesActuales.map(rol => `<option value="${rol.id_rol}">${rol.nombre_rol}</option>`).join('');
        }
    } catch (error) {
        console.error('Error al obtener roles:', error);
    }
}

async function obtenerEmpleados() {
    const tbody = document.getElementById('empleadosTableBody');
    if (!tbody) return;

    try {
        const respuesta = await fetch('/api/admin/empleados');
        empleadosActuales = await respuesta.json();
        filtrarYRenderizar();
    } catch (error) {
        console.error('Error al obtener empleados:', error);
        tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; color: var(--warning); padding: 2rem;">Error al cargar empleados.</td></tr>';
    }
}

function filtrarYRenderizar() {
    const busqueda = document.getElementById('searchEmpleado')?.value.toLowerCase() || '';
    
    let filtrados = empleadosActuales;

    if (busqueda) {
        filtrados = filtrados.filter(e => 
            e.nombre.toLowerCase().includes(busqueda) || 
            e.ap.toLowerCase().includes(busqueda) ||
            e.email.toLowerCase().includes(busqueda) ||
            (e.usuario && e.usuario.username.toLowerCase().includes(busqueda))
        );
    }

    renderizarTabla(filtrados);
}

function renderizarTabla(datos) {
    const tbody = document.getElementById('empleadosTableBody');
    if (!tbody) return;

    if (datos.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; color: var(--text-muted); padding: 2rem;">No se encontraron empleados.</td></tr>';
        return;
    }

    tbody.innerHTML = datos.map(empleado => {
        const rolNombre = empleado.usuario?.rol?.nombre_rol || 'Sin Rol';
        const rolClass = `rol-${rolNombre.toLowerCase()}`;
        
        return `
            <tr>
                <td style="font-weight: 500; color: var(--text-muted);">#${String(empleado.id_empleado).padStart(3, '0')}</td>
                <td>
                    <div style="font-weight: bold; color: var(--text-main);">${empleado.nombre} ${empleado.ap} ${empleado.am}</div>
                </td>
                <td>
                    <div style="font-size: 0.9rem;">${empleado.email}</div>
                    <div style="font-size: 0.8rem; color: var(--text-muted);">${empleado.telefono || 'N/A'}</div>
                </td>
                <td style="font-family: monospace; font-weight: 600;">
                    ${empleado.usuario ? empleado.usuario.username : '<span style="color: var(--warning)">Sin cuenta</span>'}
                </td>
                <td>
                    <span class="badge-rol ${rolClass}">${rolNombre.toUpperCase()}</span>
                </td>
                <td>
                    <div style="display: flex; gap: 0.6rem;">
                        <button class="filter-btn" style="padding: 0.4rem 0.8rem; background: #f9fafb; color: #374151; border: 1px solid #d1d5db; display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; font-weight: 500; border-radius: 6px; cursor: pointer; transition: all 0.2s;" onclick="editarEmpleado(${empleado.id_empleado})">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            Editar
                        </button>
                        <button class="filter-btn" style="padding: 0.4rem 0.8rem; background: rgba(239, 68, 68, 0.05); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; font-weight: 500; border-radius: 6px; cursor: pointer; transition: all 0.2s;" onclick="eliminarEmpleado(${empleado.id_empleado})">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            Eliminar
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

async function manejarEnvioFormulario(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    const id = document.getElementById('id_empleado').value;
    const metodo = id ? 'PUT' : 'POST';
    const url = id ? `/api/admin/empleados/${id}` : '/api/admin/empleados';

    // Convertir FormData a JSON para PUT (Laravel prefiere JSON para PUT con campos adicionales)
    const data = {};
    formData.forEach((value, key) => data[key] = value);

    try {
        const respuesta = await fetch(url, {
            method: metodo,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });

        const resultado = await respuesta.json();

        if (respuesta.ok) {
            alert(resultado.mensaje);
            closeModal('empleadoModal');
            obtenerEmpleados();
        } else {
            alert('Error: ' + (resultado.mensaje || 'Ocurrió un error inesperado'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al procesar la solicitud');
    }
}

function editarEmpleado(id) {
    const empleado = empleadosActuales.find(e => e.id_empleado === id);
    if (!empleado) return;

    document.getElementById('modalTitle').textContent = 'Editar Empleado';
    document.getElementById('id_empleado').value = empleado.id_empleado;
    document.getElementById('nombre').value = empleado.nombre;
    document.getElementById('ap').value = empleado.ap;
    document.getElementById('am').value = empleado.am;
    document.getElementById('telefono').value = empleado.telefono || '';
    document.getElementById('email').value = empleado.email;
    
    if (empleado.usuario) {
        document.getElementById('username').value = empleado.usuario.username;
        document.getElementById('id_rol').value = empleado.usuario.id_rol;
    }

    // Password no es obligatorio al editar
    document.getElementById('password').required = false;
    document.getElementById('passwordHelp').style.display = 'block';
    document.getElementById('labelPassword').textContent = 'Nueva Contraseña (opcional)';

    openModal('empleadoModal');
}

async function eliminarEmpleado(id) {
    if (!confirm('¿Está seguro de eliminar este empleado? Esto también eliminará su acceso al sistema.')) return;

    try {
        const respuesta = await fetch(`/api/admin/empleados/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        if (respuesta.ok) {
            obtenerEmpleados();
        } else {
            const error = await respuesta.json();
            alert('Error al eliminar: ' + error.mensaje);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

function openModal(id) {
    if (id === 'empleadoModal' && !document.getElementById('id_empleado').value) {
        // Reset form for fresh create
        document.getElementById('empleadoForm').reset();
        document.getElementById('id_empleado').value = '';
        document.getElementById('modalTitle').textContent = 'Registrar Empleado';
        document.getElementById('password').required = true;
        document.getElementById('passwordHelp').style.display = 'none';
        document.getElementById('labelPassword').textContent = 'Contraseña';
    }
    document.getElementById(id).style.display = 'block';
}

function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}

// Cerrar modal al hacer clic fuera
window.onclick = function(event) {
    if (event.target.className === 'modal') {
        event.target.style.display = 'none';
    }
}
