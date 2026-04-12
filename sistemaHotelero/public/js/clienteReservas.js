document.addEventListener('DOMContentLoaded', () => {
    const inputFechaInicio = document.getElementById('fecha_inicio');
    const inputFechaFin = document.getElementById('fecha_fin');
    const selectHab = document.getElementById('id_habitacion');
    const form = document.getElementById('reservaClienteForm');
    const labelTotal = document.getElementById('labelPrecioTotal');
    const btnConfirmar = document.getElementById('btnConfirmar');

    const token = localStorage.getItem('token');

    // Comprobar límite al cargar
    validarLimite();

    async function validarLimite() {
        try {
            const res = await fetch('/api/cliente/dashboard-summary', {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            const data = await res.json();
            
            if (data.limiteAlcanzado) {
                document.getElementById('limitAlert').style.display = 'block';
                document.getElementById('reservaClienteForm').style.opacity = '0.5';
                document.getElementById('reservaClienteForm').style.pointerEvents = 'none';
            }
        } catch (error) { console.error(error); }
    }

    async function actualizarDisponibilidad() {
        const fi = inputFechaInicio.value;
        const ff = inputFechaFin.value;

        if (!fi || !ff) return;

        if (new Date(ff) <= new Date(fi)) {
            selectHab.innerHTML = '<option value="">La fecha de salida debe ser posterior...</option>';
            selectHab.disabled = true;
            return;
        }

        try {
            selectHab.disabled = true;
            selectHab.innerHTML = '<option value="">Buscando habitaciones...</option>';
            
            const res = await fetch(`/api/cliente/reservas/disponibilidad?fecha_inicio=${fi}&fecha_fin=${ff}`, {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            const habitaciones = await res.json();

            if (habitaciones.length === 0) {
                selectHab.innerHTML = '<option value="">No hay habitaciones disponibles para estas fechas.</option>';
            } else {
                selectHab.innerHTML = '<option value="">Elige tu habitación...</option>' + 
                    habitaciones.map(h => `<option value="${h.id_habitacion}" data-precio="${h.precio_base}">Hab. ${h.numero_habitacion} - ${h.nombre_tipo} ($${parseFloat(h.precio_base).toFixed(2)}/noche)</option>`).join('');
                selectHab.disabled = false;
            }
        } catch (error) {
            console.error('Error:', error);
            selectHab.innerHTML = '<option value="">Error al buscar disponibilidad</option>';
        }
    }

    async function calcularCosto() {
        const fi = inputFechaInicio.value;
        const ff = inputFechaFin.value;
        const idHab = selectHab.value;

        if (!fi || !ff || !idHab) {
            labelTotal.textContent = '$0.00';
            btnConfirmar.disabled = true;
            return;
        }

        try {
            labelTotal.textContent = 'Calculando...';
            const res = await fetch(`/api/cliente/reservas/calcular-costo?id_habitacion=${idHab}&fecha_inicio=${fi}&fecha_fin=${ff}`, {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            const data = await res.json();
            
            if (data.success) {
                labelTotal.textContent = `$${parseFloat(data.costo).toLocaleString('en-US', {minimumFractionDigits: 2})}`;
                btnConfirmar.disabled = false;
            } else {
                labelTotal.textContent = '$0.00';
                btnConfirmar.disabled = true;
            }
        } catch (error) {
            labelTotal.textContent = '$0.00';
            btnConfirmar.disabled = true;
        }
    }

    inputFechaInicio.addEventListener('change', () => {
        actualizarDisponibilidad();
        calcularCosto();
    });
    inputFechaFin.addEventListener('change', () => {
        actualizarDisponibilidad();
        calcularCosto();
    });
    selectHab.addEventListener('change', calcularCosto);

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const btn = e.submitter;
        const originalText = btn.textContent;
        const metodo = document.getElementById('metodo_pago').value;
        
        if (!confirm('¿Deseas confirmar tu reservación con estos detalles?')) return;

        try {
            // Mostrar Overlay de Simulación si es Tarjeta o Transferencia
            if (metodo !== 'efectivo') {
                await iniciarSimulacionPago(metodo);
            }

            btn.disabled = true;
            btn.textContent = 'Finalizando...';

            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            const res = await fetch('/api/cliente/reservas', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`,
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(data)
            });

            const result = await res.json();
            
            // Ocultar overlay antes del resultado final
            document.getElementById('paymentOverlay').style.display = 'none';

            if (result.success) {
                alert(result.message);
                window.location.href = '/cliente-dashboard';
            } else {
                let errorFull = result.message || 'Error desconocido';
                if (errorFull.includes('1644')) {
                    errorFull = errorFull.split('1644').pop().replace(/^[\s:]+/, '').split('(Connection:')[0].trim();
                }
                alert('Error: ' + errorFull);
                btn.disabled = false;
                btn.textContent = originalText;
            }
        } catch (error) {
            console.error(error);
            document.getElementById('paymentOverlay').style.display = 'none';
            alert('Ocurrió un error al procesar tu reserva.');
            btn.disabled = false;
            btn.textContent = originalText;
        }
    });

    async function iniciarSimulacionPago(metodo) {
        const overlay = document.getElementById('paymentOverlay');
        const title = document.getElementById('paymentStatusTitle');
        const msg = document.getElementById('paymentStatusMsg');
        
        overlay.style.display = 'flex';
        
        const pasos = [];
        if (metodo.includes('tarjeta')) {
            pasos.push({ t: 'Validando tarjeta...', d: 1500 });
            pasos.push({ t: 'Autorizando transacción...', d: 1500 });
            pasos.push({ t: 'Pago Aprobado', d: 1000 });
        } else if (metodo === 'transferencia') {
            pasos.push({ t: 'Esperando respuesta del banco...', d: 1500 });
            pasos.push({ t: 'Verificando fondos...', d: 1500 });
            pasos.push({ t: 'Transferencia Confirmada', d: 1000 });
        }

        for (const paso of pasos) {
            title.textContent = paso.t;
            await new Promise(r => setTimeout(r, paso.d));
        }
    }
});
