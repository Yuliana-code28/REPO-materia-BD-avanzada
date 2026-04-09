document.getElementById('resetPasswordForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const email = document.getElementById('email').value;
    const token = document.getElementById('token').value;
    const password = document.getElementById('password').value;
    const password_confirmation = document.getElementById('password_confirmation').value;
    const mensaje = document.getElementById('mensaje');
    
    if( password === password_confirmation){
        try {
        const res = await fetch("/api/password/restablecer", {
            method: "POST",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify({email, token, password, password_confirmation})
        });

        const data = await res.json();

        if(!res.ok) throw new Error(data.mensaje || "Error al restablecer");

        mensaje.textContent = data.mensaje + ". Redirigiendo al login...";
        mensaje.className = "mensaje-success";
        mensaje.style.display = "block";

    
        setTimeout(() => {
            window.location.href = "/login";
        }, 2000);

    } catch (error) {
        mensaje.textContent = error.message;
        mensaje.className = "mensaje-error";
        mensaje.style.display = "block";
    }
    }else{
        mensaje.textContent = "la contraseña no coincide";
        mensaje.className = "mensaje-success";
        mensaje.style.display = "block";
    }
   
});