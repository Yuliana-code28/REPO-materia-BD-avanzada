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
                <div style="display: flex; gap: 0.5rem;">
                    <button class="action-btn" title="Ver Detalle" style="color: var(--primary); background: none; border: none; cursor: pointer;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    </button>
                    ${reserva.estado === 'activa' ? `
                        <button class="action-btn" title="Cancelar" onclick="confirmarCancelacion(${reserva.id_reserva})" style="color: var(--warning); background: none; border: none; cursor: pointer;">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    ` : ''}
                </div>
            </td>
        </tr>
    `).join('');
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
    // Carga inicial
    obtenerReservas();

    // Filtros
    document.querySelectorAll('.filter-btn').forEach(boton => {
        boton.addEventListener('click', function() {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            obtenerReservas(this.dataset.estado);
        });
    });

    // Búsqueda en el cliente
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
});
