document.addEventListener('DOMContentLoaded', cargarHabitaciones);

let habitaciones = [];

async function cargarHabitaciones() {
    const grid = document.getElementById('roomGrid');
    if(!grid) return;

    try {
        const res = await fetch('/api/admin/habitaciones');
        habitaciones = await res.json();
        renderizarGrilla(habitaciones);
    } catch (e) {
        console.error('Error al cargar habitaciones:', e);
        grid.innerHTML = '<div style="grid-column: 1/-1; text-align: center; color: var(--warning); padding: 2rem;">Error al cargar datos.</div>';
    }
}

function renderizarGrilla(datos) {
    const grid = document.getElementById('roomGrid');
    if(!grid) return;

    if (datos.length === 0) {
        grid.innerHTML = '<div style="grid-column: 1/-1; text-align: center; color: var(--text-muted); padding: 4rem;">No se encontraron habitaciones.</div>';
        return;
    }

    grid.innerHTML = datos.map(h => {
        let typeIcon = '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>';
        const typeLower = (h.tipo?.nombre_tipo || '').toLowerCase();
        
        if (typeLower.includes('sencilla')) {
            typeIcon = '<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>';
        } else if (typeLower.includes('doble') || typeLower.includes('triple')) {
            typeIcon = '<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 12l-8 8-4-4M16 8l-4 4-2-2"></path></svg>';
        } else if (typeLower.includes('suite')) {
            typeIcon = '<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-7.714 2.143L11 21l-2.286-6.857L1 12l7.714-2.143L11 3z"></path></svg>';
        }

        return `
            <div class="room-card" data-estado="${h.estado}">
                <div class="room-number">#${h.numero_habitacion}</div>
                <span class="room-type">${typeIcon} ${h.tipo?.nombre_tipo || 'N/A'}</span>
                
                <div class="room-badge badge-${h.estado}">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: currentColor;"></span>
                    ${h.estado}
                </div>

                <div class="room-actions">
                    ${h.estado === 'disponible' ? `
                        <button class="btn-action" style="background: rgba(245, 158, 11, 0.05); color: var(--warning); border-color: rgba(245, 158, 11, 0.3);" onclick="cambiarEstadoHabitacion(${h.id_habitacion}, 'mantenimiento')">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            Mantenimiento
                        </button>
                    ` : ''}
                    ${h.estado === 'mantenimiento' ? `
                        <button class="btn-action" style="background: #f9fafb; color: #374151; border-color: #d1d5db;" onclick="cambiarEstadoHabitacion(${h.id_habitacion}, 'disponible')">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            Habilitar
                        </button>
                    ` : ''}
                </div>
            </div>
        `;
    }).join('');
}

function filtrarHabitaciones(filtro, element) {
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    element.classList.add('active');
    
    if(filtro === 'todas') {
        renderizarGrilla(habitaciones);
    } else {
        renderizarGrilla(habitaciones.filter(h => h.estado === filtro));
    }
}

async function cambiarEstadoHabitacion(id, nuevoEstado) {
    if(!confirm(`¿Cambiar estado de la habitación a ${nuevoEstado}?`)) return;
    
    try {
        const res = await fetch(`/api/admin/habitaciones/${id}`, {
            method: 'PUT',
            headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content 
            },
            body: JSON.stringify({ estado: nuevoEstado })
        });
        if(res.ok) cargarHabitaciones();
    } catch(e) { console.error(e); }
}
