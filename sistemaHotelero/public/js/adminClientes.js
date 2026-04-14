let clientesActuales = [];

document.addEventListener('DOMContentLoaded', () => {
    obtenerClientes();

    // Filtros
    document.querySelectorAll('.filter-btn').forEach(boton => {
        boton.addEventListener('click', function() {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            filtrarYRenderizar();
        });
    });

    // Búsqueda
    const entradaBusqueda = document.getElementById('searchCliente');
    if (entradaBusqueda) {
        entradaBusqueda.addEventListener('keyup', filtrarYRenderizar);
    }
});

async function obtenerClientes() {
    const tbody = document.getElementById('clientesTableBody');
    if (!tbody) return;

    try {
        const respuesta = await fetch('/api/admin/clientes');
        clientesActuales = await respuesta.json();
        filtrarYRenderizar();
    } catch (error) {
        console.error('Error al obtener clientes:', error);
        tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; color: var(--warning); padding: 2rem;">Error al ejecutar consultas. Verifique la conexión a Base de Datos.</td></tr>';
    }
}

function filtrarYRenderizar() {
    const btnActivo = document.querySelector('.filter-btn.active');
    const clasificacion = btnActivo ? btnActivo.dataset.clasificacion : '';
    const busqueda = document.getElementById('searchCliente')?.value.toLowerCase() || '';

    let filtrados = clientesActuales;

    // Filtro por tab (Clasificacion o Top Payer)
    if (clasificacion === 'TOP_PAYERS') {
        filtrados = filtrados.filter(c => c.es_top_pagador === true);
    } else if (clasificacion === 'FRECUENTES') {
        filtrados = filtrados.filter(c => c.es_frecuente === true);
    } else if (clasificacion) {
        filtrados = filtrados.filter(c => c.clasificacion === clasificacion);
    }

    // Filtro por texto
    if (busqueda) {
        filtrados = filtrados.filter(c => 
            String(c.id_cliente).includes(busqueda) || 
            c.nombre_completo.toLowerCase().includes(busqueda) ||
            c.email.toLowerCase().includes(busqueda)
        );
    }

    renderizarTabla(filtrados);
}

function renderizarTabla(datos) {
    const tbody = document.getElementById('clientesTableBody');
    if (!tbody) return;

    if (datos.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; color: var(--text-muted); padding: 3rem;">No se encontraron clientes bajo este criterio.</td></tr>';
        return;
    }

    tbody.innerHTML = datos.map(cliente => {
        
        let badgeClass = '';
        if(cliente.clasificacion === 'CLIENTE DIAMANTE') badgeClass = 'badge-diamante';
        else if(cliente.clasificacion === 'CLIENTE PLATINO') badgeClass = 'badge-platino';
        else if(cliente.clasificacion === 'CLIENTE ESTANDAR') badgeClass = 'badge-estandar';
        else badgeClass = 'badge-nuevo';

        const topPayerBadge = cliente.es_top_pagador 
            ? `<span class="top-payer-badge" title="Su inversión supera el promedio general de ingresos del hotel">TOP PAGADOR</span>` 
            : '';

        const frecuenteBadge = cliente.es_frecuente 
            ? `<span class="frecuente-badge" title="Supera el promedio de reservaciones del hotel">FRECUENTE</span>` 
            : '';

        return `
            <tr>
                <td style="font-weight: 500; color: var(--text-muted);">#${String(cliente.id_cliente).padStart(4, '0')}</td>
                <td>
                    <div style="font-weight: bold; color: var(--text-main); display: flex; flex-direction: column; align-items: flex-start; gap: 0.25rem;">
                        <span>${cliente.nombre_completo}</span>
                        <div style="display: flex; gap: 0.25rem;">
                            ${topPayerBadge}
                            ${frecuenteBadge}
                        </div>
                    </div>
                </td>
                <td>
                    <div style="font-size: 0.9rem;">${cliente.email}</div>
                    <div style="font-size: 0.8rem; color: var(--text-muted);">${cliente.telefono}</div>
                </td>
                <td style="text-align: center; font-weight: bold; font-size: 1.1rem; color: var(--primary);">
                    ${cliente.total_reservas}
                </td>
                <td style="font-weight: 500;">
                    $${parseFloat(cliente.total_pagado).toLocaleString('en-US', {minimumFractionDigits: 2})}
                </td>
                <td>
                    <span class="status-badge ${badgeClass}" style="text-transform: uppercase; font-size: 0.70rem;">
                        ${cliente.clasificacion.replace('CLIENTE ', '')}
                    </span>
                </td>
            </tr>
        `;
    }).join('');
}
