document.addEventListener("DOMContentLoaded", () => {
    const btn = document.getElementById('btnRegistro');
    btn.addEventListener("click", registrarUsuario);
});

async function registrarUsuario() {
    const nombre = document.getElementById('nombre').value.trim();
    const ap = document.getElementById('ap').value.trim();
    const am = document.getElementById('am').value.trim();
    const email = document.getElementById('email').value.trim();
    const telefono = document.getElementById('telefono').value.trim();
    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value.trim();
    const password_confirmation = document.getElementById('password_confirmation').value.trim();
    const btn = document.getElementById('btnRegistro');

    if (!nombre || !ap || !am || !email || !telefono || !username || !password || !password_confirmation) {
        mostrarMensaje("Todos los campos son obligatorios", "error");
        return;
    }

    if (password !== password_confirmation) {
        mostrarMensaje("Las contraseñas no coinciden", "error");
        return;
    }

    try {
        btn.disabled = true;
        btn.textContent = "Registrando...";

        const res = await fetch("/api/registro", {
            method: 'POST',
            headers: {
                "Content-Type": 'application/json',
                "Accept": 'application/json'
            },
            body: JSON.stringify({
                nombre,
                ap,
                am,
                email,
                telefono,
                username,
                password,
                password_confirmation
            })
        });

        const data = await res.json();

        if (!res.ok) {
            if (data.errors) {
                // Manejar errores de validación de Laravel
                const firstError = Object.values(data.errors)[0][0];
                throw new Error(firstError);
            }
            throw new Error(data.mensaje || 'Error al registrarse');
        }

        mostrarMensaje(data.mensaje, "success");

        setTimeout(() => {
            window.location.href = "/login";
        }, 2000);

    } catch (error) {
        mostrarMensaje(error.message, "error");
    } finally {
        btn.disabled = false;
        btn.textContent = "Registrarse";
    }
}

function mostrarMensaje(texto, tipo) {
    const mensaje = document.getElementById("mensaje");
    if (mensaje) {
        mensaje.textContent = texto;
        mensaje.style.color = tipo === "success" ? "#2ecc71" : "#e74c3c";
    }
}
