document.addEventListener('DOMContentLoaded', loadData);

async function loadData() {
    const list = document.getElementById('activeReservationsList');
    const select = document.getElementById('id_servicio');
    const badge = document.getElementById('countBadge');
    
    try {
        // Cargar Reservas Activas
        const resReservas = await fetch('/api/recepcionista/reservas-activas');
        const reservas = await resReservas.json();
        
        if (badge) badge.innerText = reservas.length;

        if (reservas.length === 0) {
            list.innerHTML = `
                <div class="empty-state">
                    <div style="font-size: 2.5rem; margin-bottom: 1rem;">🛌</div>
                    <div style="font-weight: 600; font-size: 1rem; color: var(--text-main);">Sin habitaciones ocupadas</div>
                    <div style="font-size: 0.85rem;">No hay huéspedes para cargo en este momento.</div>
                </div>
            `;
        } else {
            list.innerHTML = reservas.map(r => `
                <div class="guest-card" onclick="selectReservaParaCargo(${r.id_reserva}, '${r.habitaciones.map(h => h.numero_habitacion).join(', ')}', '${r.cliente.nombre} ${r.cliente.ap}', this)">
                    <div class="guest-avatar">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <div class="guest-info">
                        <span class="guest-name">${r.cliente.nombre} ${r.cliente.ap}</span>
                        <span class="guest-room">HABITACIÓN ${r.habitaciones.map(h => h.numero_habitacion).join(', ')}</span>
                        <div style="font-size: 0.7rem; color: var(--text-muted); margin-top: 0.2rem;">Reserva #${r.id_reserva}</div>
                    </div>
                    <div style="color: var(--primary); opacity: 0.5;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"></path></svg>
                    </div>
                </div>
            `).join('');
        }

        // Cargar Catálogo de Servicios
        const resServicios = await fetch('/api/recepcionista/servicios');
        const servicios = await resServicios.json();
        select.innerHTML = '<option value="">Busca un servicio...</option>' + 
            servicios.map(s => `<option value="${s.id_servicio}">${s.nombre_servicio.toUpperCase()} — $${parseFloat(s.precio).toFixed(2)}</option>`).join('');

    } catch (e) {
        console.error('Error al cargar datos de servicios:', e);
    }
}

function selectReservaParaCargo(id, room, client, element) {
    document.querySelectorAll('.guest-card').forEach(c => c.classList.remove('selected'));
    element.classList.add('selected');

    document.getElementById('selected_reserva_id').value = id;
    document.getElementById('display_room').value = `HAB: ${room} — ${client}`;
    document.getElementById('btnSubmit').disabled = false;
    
    // Smooth scroll al panel de cargo en móviles si es necesario
    if(window.innerWidth < 768) {
        document.querySelector('.charge-panel').scrollIntoView({ behavior: 'smooth' });
    }
}

const serviceForm = document.getElementById('serviceForm');
if (serviceForm) {
    serviceForm.onsubmit = async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData.entries());
        const btn = document.getElementById('btnSubmit');

        try {
            btn.innerHTML = 'Registrando...';
            btn.disabled = true;

            const res = await fetch('/api/recepcionista/consumos', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content 
                },
                body: JSON.stringify(data)
            });
            const result = await res.json();
            
            btn.innerHTML = 'Registrar Cargo';
            
            if (result.success) {
                alert(result.message);
                e.target.reset();
                document.getElementById('display_room').value = '';
                document.querySelectorAll('.active-reservation-card').forEach(c => c.classList.remove('selected'));
            } else {
                alert('Error: ' + result.message);
                btn.disabled = false;
            }
        } catch(e) { 
            console.error(e);
            alert('Error en conexión');
            btn.disabled = false;
            btn.innerHTML = 'Registrar Cargo';
        }
    };
}
