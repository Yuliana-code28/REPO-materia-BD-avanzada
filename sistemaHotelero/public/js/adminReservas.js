let reservasActuales = []; // Cache para búsquedas

async function obtenerReservas(estado = '') {
    const tbody = document.getElementById('reservasTableBody');
    if (!tbody) return;
    
    tbody.innerHTML = '<tr class="loading-row"><td colspan="7" style="text-align: center;">Actualizando lista...</td></tr>';

    try {
        const url = estado ? `/api/admin/reservas?estado=${estado}` : '/api/admin/reservas';
        const respuesta = await fetch(url);
        reservasActuales = await respuesta.json();
        renderizarTabla(reservasActuales);
    } catch (error) {
        console.error('Error al obtener reservaciones:', error);
        tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; color: var(--warning);">Error al cargar los datos.</td></tr>';
    }
}

function renderizarTabla(datos) {
    const tbody = document.getElementById('reservasTableBody');
    if (!tbody) return;

    if (datos.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" style="text-align: center;">No se encontraron reservaciones.</td></tr>';
        return;
    }

    tbody.innerHTML = datos.map(reserva => `
        <tr>
            <td style="font-weight: 600;">#${String(reserva.id_reserva).padStart(4, '0')}</td>
            <td>
                <div style="font-weight: 500;">${reserva.nombre_cliente}</div>
                <div style="font-size: 0.8rem; color: var(--text-muted);">${reserva.email_cliente}</div>
            </td>
            <td>
                <span style="display: block;">Hab. ${reserva.numero_habitacion || '?'}</span>
            </td>
            <td>
                <div style="font-size: 0.9rem;">
                    ${formatearFecha(reserva.fecha_inicio)} - ${formatearFecha(reserva.fecha_fin)}
                </div>
            </td>
            <td>
                <span class="status-badge badge-${reserva.estado.toLowerCase()}">
                    ${reserva.estado}
                </span>
            </td>
            <td style="font-weight: 600;">
                $${parseFloat(reserva.costo_total).toLocaleString('en-US', {minimumFractionDigits: 2})}
            </td>
            <td>
                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                    ${reserva.estado === 'activa' ? `
                        <button class="action-badge" onclick="confirmarFinalizacion(${reserva.id_reserva})" style="background: rgba(16, 185, 129, 0.05); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); padding: 0.4rem 0.8rem; border-radius: 6px; cursor: pointer; font-size: 0.85rem; font-weight: 500; display: flex; align-items: center; gap: 0.5rem; transition: all 0.2s;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"></path></svg>
                            Finalizar
                        </button>
                    ` : ''}
                    ${reserva.estado === 'activa' || reserva.estado === 'pendiente' ? `
                        <button class="action-badge" onclick="confirmarCancelacion(${reserva.id_reserva})" style="background: rgba(245, 158, 11, 0.05); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3); padding: 0.4rem 0.8rem; border-radius: 6px; cursor: pointer; font-size: 0.85rem; font-weight: 500; display: flex; align-items: center; gap: 0.5rem; transition: all 0.2s;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"></path></svg>
                            Cancelar
                        </button>
                    ` : ''}
                    ${reserva.estado !== 'activa' && reserva.estado !== 'pendiente' ? '<span style="color: var(--text-muted); font-size: 0.8rem;">Sin acciones</span>' : ''}
                </div>
            </td>
        </tr>
    `).join('');
}

async function iniciarSimulacionPago(metodo) {
    const overlay = document.getElementById('paymentOverlay');
    const title = document.getElementById('paymentStatusTitle');
    const msg = document.getElementById('paymentStatusMsg');
    const icon = document.getElementById('paymentIcon');

    if (!overlay) return true;

    overlay.style.display = 'flex';
    title.innerText = 'Procesando Pago';
    msg.innerText = `Conectando con pasarela de ${metodo.replace('_', ' ')}...`;
    icon.innerHTML = `<svg class="animate-spin" width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin: 0 auto; color: #8b5cf6;"><path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83"></path></svg>`;

    await new Promise(r => setTimeout(r, 2000));
    msg.innerText = 'Verificando fondos y autorización...';
    
    await new Promise(r => setTimeout(r, 1500));
    title.innerText = 'Pago Autorizado';
    msg.innerText = 'Transacción completada con éxito.';
    icon.innerHTML = `<svg width="50" height="50" fill="none" stroke="#10b981" stroke-width="2.5" viewBox="0 0 24 24" style="margin: 0 auto;"><path d="M5 13l4 4L19 7"></path></svg>`;

    await new Promise(r => setTimeout(r, 1000));
    overlay.style.display = 'none';
    return true;
}

async function confirmarFinalizacion(id) {
    if (confirm('¿Deseas marcar esta reservación como FINALIZADA? Esto liberará la habitación inmediatamente.')) {
        try {
            const respuesta = await fetch(`/api/admin/reservas/${id}/finalizar`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            });
            const resultado = await respuesta.json();
            if (resultado.success) {
                alert(resultado.message);
                const filtroActivo = document.querySelector('.filter-btn.active')?.dataset.estado || '';
                obtenerReservas(filtroActivo);
            } else {
                alert('Error: ' + resultado.message);
            }
        } catch (error) {
            console.error('Error al finalizar:', error);
            alert('Ocurrió un error al procesar la finalización.');
        }
    }
}

async function confirmarCancelacion(id) {
    if (confirm('¿Estás seguro de que deseas cancelar esta reservación? Esta acción liberará la habitación automáticamente.')) {
        try {
            const respuesta = await fetch(`/api/admin/reservas/${id}/cancelar`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            });
            const resultado = await respuesta.json();
            if (resultado.success) {
                alert(resultado.message);
                const filtroActivo = document.querySelector('.filter-btn.active')?.dataset.estado || '';
                obtenerReservas(filtroActivo);
            } else {
                alert('Error: ' + resultado.message);
            }
        } catch (error) {
            console.error('Error al cancelar:', error);
            alert('Ocurrió un error al procesar la cancelación.');
        }
    }
}

function formatearFecha(cadenaFecha) {
    if (!cadenaFecha) return 'N/A';
    const fecha = new Date(cadenaFecha);
    const dia = String(fecha.getUTCDate()).padStart(2, '0');
    const mes = String(fecha.getUTCMonth() + 1).padStart(2, '0');
    const anio = String(fecha.getUTCFullYear()).slice(-2);
    return `${dia}/${mes}/${anio}`;
}

document.addEventListener('DOMContentLoaded', () => {
    obtenerReservas();
    cargarDatosFormularioModal();

    async function cargarDatosFormularioModal() {
        try {
            const res = await fetch('/api/admin/reservas/form-data');
            const data = await res.json();
            const selectCliente = document.getElementById('id_cliente');
            if (selectCliente && data.clientes) {
                selectCliente.innerHTML = '<option value="">Seleccione un cliente...</option>' + 
                    data.clientes.map(c => `<option value="${c.id_cliente}">${c.nombre} ${c.ap} (${c.email})</option>`).join('');
            }
        } catch (error) {
            console.error('Error al cargar datos del formulario:', error);
        }
    }

    document.querySelectorAll('.filter-btn').forEach(boton => {
        boton.addEventListener('click', function() {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            obtenerReservas(this.dataset.estado);
        });
    });

    const entradaBusqueda = document.getElementById('searchReserva');
    if (entradaBusqueda) {
        entradaBusqueda.addEventListener('keyup', function() {
            const valor = this.value.toLowerCase();
            const filtrados = reservasActuales.filter(r => 
                String(r.id_reserva).includes(valor) || 
                r.nombre_cliente.toLowerCase().includes(valor) ||
                r.email_cliente.toLowerCase().includes(valor)
            );
            renderizarTabla(filtrados);
        });
    }

    async function calcularCosto() {
        const idHabitacionInput = document.getElementById('id_habitacion');
        const fechaInicioInput = document.getElementById('fecha_inicio');
        const fechaFinInput = document.getElementById('fecha_fin');
        const montoPagoInput = document.getElementById('monto_pago');

        if (!idHabitacionInput || !fechaInicioInput || !fechaFinInput || !montoPagoInput) return;
        const id_habitacion = idHabitacionInput.value;
        const fecha_inicio = fechaInicioInput.value;
        const fecha_fin = fechaFinInput.value;

        if (!id_habitacion || !fecha_inicio || !fecha_fin) {
            montoPagoInput.value = '';
            return;
        }

        try {
            montoPagoInput.value = 'Calculando...';
            const res = await fetch(`/api/admin/reservas/calcular-costo?id_habitacion=${id_habitacion}&fecha_inicio=${fecha_inicio}&fecha_fin=${fecha_fin}`);
            const data = await res.json();
            if (data.success && data.costo !== null) {
                montoPagoInput.value = parseFloat(data.costo).toFixed(2);
            } else {
                montoPagoInput.value = '';
            }
        } catch (error) {
            console.error('Error calculando costo:', error);
        }
    }

    async function actualizarDisponibilidadHabitaciones() {
        const fechaInicio = document.getElementById('fecha_inicio').value;
        const fechaFin = document.getElementById('fecha_fin').value;
        const selectHab = document.getElementById('id_habitacion');
        if (!fechaInicio || !fechaFin || !selectHab) return;

        try {
            selectHab.disabled = true;
            selectHab.innerHTML = '<option value="">Consultando disponibilidad...</option>';
            const res = await fetch(`/api/admin/reservas/disponibilidad?fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`);
            const habitaciones = await res.json();
            if (habitaciones.length === 0) {
                selectHab.innerHTML = '<option value="">No hay habitaciones disponibles</option>';
            } else {
                selectHab.innerHTML = '<option value="">Seleccione una habitación...</option>' + 
                    habitaciones.map(h => `<option value="${h.id_habitacion}" data-precio="${h.precio_base}">Hab. ${h.numero_habitacion} - ${h.nombre_tipo}</option>`).join('');
                selectHab.disabled = false;
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    const inputHabitacion = document.getElementById('id_habitacion');
    const inputFechaInicio = document.getElementById('fecha_inicio');
    const inputFechaFin = document.getElementById('fecha_fin');
    if (inputHabitacion) inputHabitacion.addEventListener('change', calcularCosto);
    if (inputFechaInicio) inputFechaInicio.addEventListener('change', () => { actualizarDisponibilidadHabitaciones(); calcularCosto(); });
    if (inputFechaFin) inputFechaFin.addEventListener('change', () => { actualizarDisponibilidadHabitaciones(); calcularCosto(); });

    const formCrear = document.getElementById('createReservaForm');
    if (formCrear) {
        formCrear.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            const metodo_pago = data.metodo_pago;

            if (metodo_pago && metodo_pago !== 'efectivo') {
                const simulacionExitosa = await iniciarSimulacionPago(metodo_pago);
                if (!simulacionExitosa) return;
            }

            try {
                const button = this.querySelector('button[type="submit"]');
                button.innerHTML = 'Guardando...';
                button.disabled = true;

                const respuesta = await fetch('/api/admin/reservas', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') },
                    body: JSON.stringify(data)
                });
                const resultado = await respuesta.json();
                button.innerHTML = 'Guardar Reserva';
                button.disabled = false;

                if (resultado.success) {
                    alert(resultado.message);
                    cerrarModal('createModal');
                    this.reset();
                    obtenerReservas();
                } else {
                    alert('Error: ' + resultado.message);
                }
            } catch (error) {
                console.error('Error:', error);
            }
        });
    }
});

function abrirModal(id) { document.getElementById(id).style.display = 'block'; document.body.style.overflow = 'hidden'; }
function cerrarModal(id) { document.getElementById(id).style.display = 'none'; document.body.style.overflow = 'auto'; }
window.onclick = function(event) { if (event.target.classList.contains('modal')) cerrarModal(event.target.id); }
