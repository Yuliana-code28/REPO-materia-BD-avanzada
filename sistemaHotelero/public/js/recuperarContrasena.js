document.getElementById('forgotPasswordForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const email = document.getElementById('email').value.trim();
    const mensaje = document.getElementById('mensaje');

    if(!email){
        mensaje.textContent = "Ingresa tu correo";
        mensaje.style.color = "red";
        return;
    }

    try {
        const res = await fetch("/api/password/recuperar", {
            method: "POST",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify({email})
        });

        const data = await res.json();

        if(!res.ok){
            throw new Error(data.mensaje || "Error al enviar el correo");
        }

        mensaje.textContent = data.mensaje;
        mensaje.className = "mensaje-success";
        mensaje.style.display = "block";

    } catch (error) {
        mensaje.textContent = error.message;
        mensaje.className = "mensaje-error";
        mensaje.style.display = "block";
    }
});