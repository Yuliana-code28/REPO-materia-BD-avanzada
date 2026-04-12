let habitacionesActuales = []; 

async function obtenerHabitaciones(estado = '') {
    const tbody = document.getElementById('habitacionesTableBody');
    if (!tbody) return;
    
    tbody.innerHTML = '<tr class="loading-row"><td colspan="6" style="text-align: center; padding: 2rem;">Cargando habitaciones...</td></tr>';

    try {
        const url = estado ? `/api/admin/habitaciones?estado=${estado}` : '/api/admin/habitaciones';
        const respuesta = await fetch(url);
        habitacionesActuales = await respuesta.json();
        renderizarTabla(habitacionesActuales);
    } catch (error) {
        console.error('Error al obtener habitaciones:', error);
        tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; color: var(--warning); padding: 2rem;">Error al cargar los datos.</td></tr>';
    }
}

function renderizarTabla(datos) {
    const tbody = document.getElementById('habitacionesTableBody');
    if (!tbody) return;

    if (datos.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; color: var(--text-muted); padding: 3rem;">No se encontraron habitaciones en esta categoría.</td></tr>';
        return;
    }

    tbody.innerHTML = datos.map(habitacion => {
        const tipoNombre = habitacion.tipo ? habitacion.tipo.nombre_tipo : 'N/A';
        const precioBase = habitacion.tipo ? habitacion.tipo.precio_base : 0;
        const idPad = String(habitacion.id_habitacion).padStart(3, '0');
        const precioFormat = parseFloat(precioBase).toLocaleString('en-US', {minimumFractionDigits: 2});
        const estadoCapitalized = habitacion.estado.charAt(0).toUpperCase() + habitacion.estado.slice(1);
        
        return `
            <tr>
                <td style="font-weight: 500; color: var(--text-muted);">#${idPad}</td>
                <td style="font-weight: bold;">${habitacion.numero_habitacion}</td>
                <td><span style="text-transform: capitalize;">${tipoNombre}</span></td>
                <td>$${precioFormat}</td>
                <td>
                    <span class="status-badge ${habitacion.estado}">
                        ${estadoCapitalized}
                    </span>
                </td>
                <td class="action-group">
                    <div style="display: flex; gap: 0.6rem;">
                        <button class="filter-btn" style="padding: 0.4rem 0.8rem; background: #f9fafb; color: #374151; border: 1px solid #d1d5db; display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; font-weight: 500; border-radius: 6px; cursor: pointer; transition: all 0.2s;" onclick="abrirModalEdicion(${habitacion.id_habitacion}, '${habitacion.numero_habitacion}', ${habitacion.id_tipo}, '${habitacion.estado}')">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            Editar
                        </button>
                        <button class="filter-btn" style="padding: 0.4rem 0.8rem; background: rgba(239, 68, 68, 0.05); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; font-weight: 500; border-radius: 6px; cursor: pointer; transition: all 0.2s;" onclick="eliminarHabitacion(${habitacion.id_habitacion})">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            Eliminar
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

document.addEventListener('DOMContentLoaded', () => {
    
    // Obtain the initial param if exists in URL
    const urlParams = new URLSearchParams(window.location.search);
    const estadoInicial = urlParams.get('estado') || '';
    
    // Carga inicial
    obtenerHabitaciones(estadoInicial);
    cargarDatosFormularioModal();

    // Filtros
    document.querySelectorAll('.filter-btn').forEach(boton => {
        boton.addEventListener('click', function(e) {
            e.preventDefault(); 
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const state = this.dataset.estado || '';
            const newUrl = state ? window.location.pathname + '?estado=' + state : window.location.pathname;
            window.history.pushState({path:newUrl}, '', newUrl);
            
            obtenerHabitaciones(state);
        });
    });

    // Crear habitación
    const formCrear = document.getElementById('createHabitacionForm');
    if (formCrear) {
        formCrear.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            
            try {
                const btn = this.querySelector('button[type="submit"]');
                const orig = btn.innerHTML;
                btn.innerHTML = 'Guardando...';
                btn.disabled = true;

                const res = await fetch('/api/admin/habitaciones', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await res.json();
                btn.innerHTML = orig;
                btn.disabled = false;
                
                if (result.success) {
                    alert(result.message);
                    cerrarModal('createModal');
                    this.reset();
                    const filtroActivo = document.querySelector('.filter-btn.active')?.dataset.estado || '';
                    obtenerHabitaciones(filtroActivo);
                } else {
                    alert('Error al crear: ' + (result.message || 'Datos inválidos'));
                }
            } catch (err) {
                console.error(err);
                alert('Error en conexión');
            }
        });
    }

    // Editar habitación
    const formEditar = document.getElementById('editHabitacionForm');
    if (formEditar) {
        formEditar.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            const id = data.id_habitacion;
            
            try {
                const btn = this.querySelector('button[type="submit"]');
                const orig = btn.innerHTML;
                btn.innerHTML = 'Actualizando...';
                btn.disabled = true;

                const res = await fetch(`/api/admin/habitaciones/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await res.json();
                btn.innerHTML = orig;
                btn.disabled = false;
                
                if (result.success) {
                    alert(result.message);
                    cerrarModal('editModal');
                    const filtroActivo = document.querySelector('.filter-btn.active')?.dataset.estado || '';
                    obtenerHabitaciones(filtroActivo);
                } else {
                    alert('Error al actualizar: ' + (result.message || 'Datos inválidos'));
                }
            } catch (err) {
                console.error(err);
                alert('Error en conexión');
            }
        });
    }
});

function abrirModal(id) {
    document.getElementById(id).style.display = 'block';
    document.body.style.overflow = 'hidden'; // Prevent scrolling
}

function cerrarModal(id) {
    document.getElementById(id).style.display = 'none';
    document.body.style.overflow = 'auto';
}

function abrirModalEdicion(id, numero, tipo, estado) {
    document.getElementById('editTitleNum').innerText = '#' + numero;
    document.getElementById('edit_id_habitacion').value = id;
    document.getElementById('edit_numero').value = numero;
    document.getElementById('edit_tipo').value = tipo;
    document.getElementById('edit_estado').value = estado;
    abrirModal('editModal');
}

async function eliminarHabitacion(id) {
    if(!confirm('¿Estás seguro de que deseas eliminar esta habitación?')) return;
    
    try {
        const respuesta = await fetch(`/api/admin/habitaciones/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            }
        });
        const result = await respuesta.json();
        
        if (result.success) {
            alert(result.message);
            const filtroActivo = document.querySelector('.filter-btn.active')?.dataset.estado || '';
            obtenerHabitaciones(filtroActivo);
        } else {
            alert('Error: ' + result.message);
        }
    } catch(err) {
        console.error('Error:', err);
        alert('Ocurrió un error al eliminar');
    }
}

async function cargarDatosFormularioModal() {
    try {
        const res = await fetch('/api/admin/habitaciones/form-data');
        const data = await res.json();

        const selectCreate = document.getElementById('create_tipo');
        const selectEdit = document.getElementById('edit_tipo');

        if (data.tipos) {
            const optionsHTML = '<option value="">Seleccione un tipo...</option>' + 
                data.tipos.map(t => `<option value="${t.id_tipo}">${t.nombre_tipo.charAt(0).toUpperCase() + t.nombre_tipo.slice(1)} - $${parseFloat(t.precio_base).toFixed(2)}</option>`).join('');
            
            if (selectCreate) selectCreate.innerHTML = optionsHTML;
            if (selectEdit) selectEdit.innerHTML = optionsHTML;
        }
    } catch (error) {
        console.error('Error al cargar datos del formulario:', error);
        const optionsError = '<option value="">Error al cargar</option>';
        if (document.getElementById('create_tipo')) document.getElementById('create_tipo').innerHTML = optionsError;
        if (document.getElementById('edit_tipo')) document.getElementById('edit_tipo').innerHTML = optionsError;
    }
}

// Close modal if clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}
