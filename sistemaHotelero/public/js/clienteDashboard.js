document.addEventListener('DOMContentLoaded', () => {
    const user = JSON.parse(localStorage.getItem('user'));
    if (user) {
        document.getElementById('welcomeMessage').textContent = `¡Hola, ${user.username}!`;
        document.getElementById('infoUsername').textContent = user.username;
        document.getElementById('infoRol').textContent = user.rol;
    }

    cargarDatosDashboard();
});

async function cargarDatosDashboard() {
    const token = localStorage.getItem('token');
    if (!token) return;

    try {
        const res = await fetch('/api/cliente/dashboard-summary', {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        const data = await res.json();

        renderizarResumen(data);
        cargarHistorial();
    } catch (error) {
        console.error('Error al cargar dashboard:', error);
    }
}

function renderizarResumen(data) {
    document.getElementById('statTotalReservas').textContent = data.totalReservas || 0;
    document.getElementById('statTotalInvertido').textContent = `$${parseFloat(data.totalGastado || 0).toLocaleString('en-US', {minimumFractionDigits: 2})}`;

    // Info personal
    const user = JSON.parse(localStorage.getItem('user'));
    if (user) {
        document.getElementById('infoIdCliente').textContent = `#${String(data.activa?.id_cliente || data.proxima?.id_cliente || '000').padStart(4, '0')}`;
    }

    const container = document.getElementById('estanciaContainer');
    const reserva = data.activa || data.proxima;

    if (!reserva) {
        container.innerHTML = `
            <div style="text-align: center;">
                <p style="color: var(--text-muted); margin-bottom: 1rem;">No tienes estancias activas o próximas.</p>
                <a href="/cliente/reservas" class="btn-primary" style="display: inline-block;">¡Reservar ahora!</a>
            </div>
        `;
        renderizarConsumos([]); // Limpiar consumos
        return;
    }

    const estadoTexto = reserva.estado === 'activa' ? 'Estancia Actual' : 'Próxima Llegada';
    const colorEstado = reserva.estado === 'activa' ? 'var(--success)' : 'var(--primary)';

    container.innerHTML = `
        <div style="width: 100%;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                <span style="background: ${colorEstado}; color: white; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.7rem; font-weight: 600; text-transform: uppercase;">${estadoTexto}</span>
                <span style="font-weight: 600; font-size: 1.1rem; color: var(--primary);">#${String(reserva.id_reserva).padStart(4, '0')}</span>
            </div>
            <div style="background: var(--bg); padding: 1rem; border-radius: 12px; border: 1px solid var(--border);">
                <div style="font-weight: 600; margin-bottom: 0.5rem; color: var(--text-main);">
                    Habitación: ${reserva.habitaciones?.[0]?.numero_habitacion || 'Asignando...'}
                </div>
                <div style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 0.5rem;">
                    ${formatearFecha(reserva.detalle_reservas?.[0]?.fecha_inicio)} - ${formatearFecha(reserva.detalle_reservas?.[0]?.fecha_fin)}
                </div>
                ${reserva.estado === 'activa' ? `
                    <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px dashed var(--border); display: flex; justify-content: space-between; font-size: 0.85rem;">
                        <span style="color: var(--text-muted);">Costo Estadía:</span>
                        <span style="font-weight: 600;">$${parseFloat(reserva.pagos?.[0]?.monto || 0).toLocaleString('en-US', {minimumFractionDigits: 2})}</span>
                    </div>
                ` : ''}
            </div>
        </div>
    `;

    renderizarConsumos(data.consumos || [], data.totalServicios || 0);
}

function renderizarConsumos(consumos, total) {
    const tbody = document.getElementById('listaConsumosBody');
    const totalCont = document.getElementById('totalServiciosContainer');
    const labelTotal = document.getElementById('labelTotalServicios');

    if (!consumos || consumos.length === 0) {
        tbody.innerHTML = '<tr><td colspan="3" style="text-align: center; color: var(--text-muted); padding: 2rem;">No has registrado consumos en esta estancia.</td></tr>';
        totalCont.style.display = 'none';
        return;
    }

    tbody.innerHTML = consumos.map(c => `
        <tr style="border-bottom: 1px solid rgba(0,0,0,0.03);">
            <td style="padding: 0.75rem 0;">
                <div style="font-weight: 500; font-size: 0.9rem; color: var(--text-main);">${c.nombre_servicio}</div>
                <div style="font-size: 0.75rem; color: var(--text-muted); display: flex; align-items: center; gap: 0.5rem;">
                    <span>Hab. ${c.numero_habitacion}</span> • <span>${formatearFecha(c.fecha_consumo)}</span>
                </div>
            </td>
            <td style="padding: 0.75rem 0; text-align: center; font-size: 0.9rem;">x${c.cantidad}</td>
            <td style="padding: 0.75rem 0; text-align: right; font-weight: 600; font-size: 0.9rem; color: var(--text-dark);">$${parseFloat(c.precio * c.cantidad).toFixed(2)}</td>
        </tr>
    `).join('');

    totalCont.style.display = 'block';
    labelTotal.textContent = `$${parseFloat(total).toLocaleString('en-US', {minimumFractionDigits: 2})}`;
}

async function cargarHistorial() {
    const token = localStorage.getItem('token');
    try {
        const res = await fetch('/api/cliente/reservas', {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        const reservas = await res.json();
        renderizarHistorial(reservas);
    } catch (error) {
        console.error('Error al cargar historial:', error);
    }
}

function renderizarHistorial(reservas) {
    const tbody = document.getElementById('historialReservasBody');
    if (reservas.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align: center;">Aún no has realizado ninguna reserva.</td></tr>';
        return;
    }

    tbody.innerHTML = reservas.map(res => `
        <tr>
            <td style="font-weight: 600;">#${String(res.id_reserva).padStart(4, '0')}</td>
            <td>${res.habitaciones?.[0]?.numero_habitacion || 'Varias'}</td>
            <td>${formatearFecha(res.fecha_inicio || res.detalle_reservas?.[0]?.fecha_inicio)}</td>
            <td>${formatearFecha(res.fecha_fin || res.detalle_reservas?.[0]?.fecha_fin)}</td>
            <td>
                <span class="status-badge badge-${res.estado.toLowerCase()}">${res.estado}</span>
            </td>
            <td style="font-weight: 600;">$${parseFloat(res.pagos?.[0]?.monto || 0).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
        </tr>
    `).join('');
}

function formatearFecha(cadenaFecha) {
    if (!cadenaFecha) return 'N/A';
    const fecha = new Date(cadenaFecha);
    // Ajuste por zona horaria
    const dia = String(fecha.getUTCDate()).padStart(2, '0');
    const mes = String(fecha.getUTCMonth() + 1).padStart(2, '0');
    const anio = String(fecha.getUTCFullYear());
    return `${dia}/${mes}/${anio}`;
}
