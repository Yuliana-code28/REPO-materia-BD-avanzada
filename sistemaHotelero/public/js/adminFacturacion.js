document.addEventListener('DOMContentLoaded', () => {
    cargarDatosFacturacion();
    cargarReportesFinancieros();
});

async function cargarDatosFacturacion() {
    const tbody = document.getElementById('pagosTableBody');
    if (!tbody) return;

    try {
        const res = await fetch('/api/admin/facturacion');
        const pagos = await res.json();

        if (pagos.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 2rem;">No hay registros de transacciones.</td></tr>';
            return;
        }

        tbody.innerHTML = pagos.map(p => `
            <tr>
                <td style="color: var(--text-muted); font-family: monospace;">#PAG-${String(p.id_pago).padStart(4, '0')}</td>
                <td style="font-weight: 600;">${p.cliente}</td>
                <td style="color: var(--primary);">Reserva #${p.id_reserva}</td>
                <td style="font-weight: 700;">$${parseFloat(p.monto).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                <td style="font-size: 0.9rem;">${formatearFechaLarga(p.fecha_pago)}</td>
                <td>
                    <span class="method-badge ${obtenerClaseMetodo(p.metodo_pago)}">
                        ${p.metodo_pago}
                    </span>
                </td>
            </tr>
        `).join('');

        document.getElementById('statConteo').innerText = pagos.length;

    } catch (error) {
        console.error('Error cargando facturación:', error);
        tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; color: var(--warning);">Error al cargar historial.</td></tr>';
    }
}

async function cargarReportesFinancieros() {
    try {
        const res = await fetch('/api/admin/facturacion/reportes');
        const data = await res.json();

        // Actualizar Tarjetas
        document.getElementById('statHistorico').innerText = `$${parseFloat(data.total_historico).toLocaleString('en-US', {minimumFractionDigits: 2})}`;
        document.getElementById('statPromedio').innerText = `$${parseFloat(data.pago_promedio).toLocaleString('en-US', {minimumFractionDigits: 2})}`;

        // Reporte Mensual
        const mensualBody = document.getElementById('reporteMensualBody');
        mensualBody.innerHTML = data.mensual.map(m => `
            <tr>
                <td style="font-weight: 500;">${m.periodo}</td>
                <td style="text-align: right; font-weight: 600; color: var(--primary);">$${parseFloat(m.ingresos_totales).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
            </tr>
        `).join('');

        // Reporte Anual
        const anualBody = document.getElementById('reporteAnualBody');
        anualBody.innerHTML = data.anual.map(a => `
            <tr>
                <td style="font-weight: 500;">Año ${a.anio}</td>
                <td style="text-align: right; font-weight: 600; color: #10b981;">$${parseFloat(a.total_anual).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
            </tr>
        `).join('');

        // Inicializar Gráficas
        renderizarGraficas(data);

    } catch (error) {
        console.error('Error cargando reportes:', error);
    }
}

function renderizarGraficas(data) {
    const ctxMensual = document.getElementById('monthlyChart').getContext('2d');
    const ctxAnual = document.getElementById('yearlyChart').getContext('2d');

    // Invertir datos para que el orden cronológico sea de izquierda a derecha
    const mensualLabels = [...data.mensual].reverse().map(m => m.periodo);
    const mensualValues = [...data.mensual].reverse().map(m => parseFloat(m.ingresos_totales));

    new Chart(ctxMensual, {
        type: 'bar',
        data: {
            labels: mensualLabels,
            datasets: [{
                label: 'Ingreso Mensual ($)',
                data: mensualValues,
                backgroundColor: 'rgba(99, 102, 241, 0.6)',
                borderColor: '#6366f1',
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { font: { size: 10 } } },
                x: { ticks: { font: { size: 10 } } }
            }
        }
    });

    const anualLabels = [...data.anual].reverse().map(a => `Año ${a.anio}`);
    const anualValues = [...data.anual].reverse().map(a => parseFloat(a.total_anual));

    new Chart(ctxAnual, {
        type: 'line',
        data: {
            labels: anualLabels,
            datasets: [{
                label: 'Ingreso Anual ($)',
                data: anualValues,
                fill: true,
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                borderColor: '#10b981',
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#10b981'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { font: { size: 10 } } },
                x: { ticks: { font: { size: 10 } } }
            }
        }
    });
}

function obtenerClaseMetodo(metodo) {
    const m = metodo.toLowerCase();
    if (m.includes('efectivo')) return 'method-efectivo';
    if (m.includes('tarjeta')) return 'method-tarjeta';
    return 'method-transferencia';
}

function formatearFechaLarga(cadena) {
    const fecha = new Date(cadena);
    return fecha.toLocaleDateString('es-MX', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}
