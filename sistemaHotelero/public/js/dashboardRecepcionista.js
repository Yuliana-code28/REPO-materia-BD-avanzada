document.addEventListener('DOMContentLoaded', () => {
    cargarDashboard();
});

async function cargarDashboard() {
    try {
        const res = await fetch('/api/recepcionista/dashboard-data');
        const data = await res.json();

        renderizarEstadisticas(data.stats);
        renderizarGraficaReservas(data.reservas_stats);
    } catch (error) {
        console.error("Error al cargar dashboard:", error);
    }
}

function renderizarGraficaReservas(stats) {
    const ctx = document.getElementById('reservasChart');
    if (!ctx) return;

    // Colores premium
    const colors = {
        violet: '#8b5cf6',
        emerald: '#10b981',
        amber: '#f59e0b',
        slate: '#64748b'
    };

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Pendientes', 'Activas', 'Finalizadas', 'Canceladas'],
            datasets: [{
                data: [stats.pendientes, stats.activas, stats.finalizadas, stats.canceladas],
                backgroundColor: [colors.amber, colors.emerald, colors.violet, colors.slate],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        color: '#1e293b', // Color oscuro para visibilidad en fondo claro
                        font: { family: 'Inter', size: 12 },
                        padding: 20,
                        usePointStyle: true
                    }
                }
            }
        }
    });
}

function renderizarEstadisticas(stats) {
    const container = document.getElementById('statsGrid');
    if (!container) return;

    container.innerHTML = `
        <div class="stat-card" style="border-top: 4px solid #6366f1;">
            <div class="stat-icon" style="opacity: 1; color: #6366f1;">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
            <div class="stat-title">Habitaciones</div>
            <div class="stat-value">${stats.totales}</div>
        </div>
        <div class="stat-card" style="border-top: 4px solid var(--success);">
            <div class="stat-icon" style="opacity: 1; color: var(--success)">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"></path></svg>
            </div>
            <div class="stat-title">Disponibles</div>
            <div class="stat-value">${stats.disponibles}</div>
        </div>
        <div class="stat-card" style="border-top: 4px solid var(--primary);">
            <div class="stat-icon" style="opacity: 1; color: var(--primary)">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
            </div>
            <div class="stat-title">Activas</div>
            <div class="stat-value">${stats.ocupadas}</div>
        </div>
        <div class="stat-card" style="border-top: 4px solid var(--warning);">
            <div class="stat-icon" style="opacity: 1; color: var(--warning)">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            </div>
            <div class="stat-title">Mantenimiento</div>
            <div class="stat-value">${stats.mantenimiento}</div>
        </div>
    `;
}

async function registrarEntrada(reservaId) {
    try {
        const res = await fetch(`/api/recepcionista/reservas/${reservaId}/check-in`, {
            method: 'POST',
            headers: { 
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        });
        const result = await res.json();
        if(result.success) {
            alert('¡Check-in realizado! La habitación está ahora ocupada.');
            cargarDashboard();
        } else {
            alert('Error: ' + result.message);
        }
    } catch(e) { console.error(e); }
}

async function registrarSalida(reservaId) {
    if(!confirm('¿Confirmar Check-out y cierre de cuenta?')) return;
    
    try {
        const res = await fetch(`/api/admin/reservas/${reservaId}/finalizar`, {
            method: 'PATCH',
            headers: { 
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        });
        const result = await res.json();
        if(result.success) {
            alert('Check-out realizado con éxito');
            cargarDashboard();
        } else {
            alert('Error: ' + result.message);
        }
    } catch(e) { console.error(e); }
}
