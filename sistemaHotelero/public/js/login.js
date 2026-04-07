document.addEventListener("DOMContentLoaded", ()=>{
  const btn = document.getElementById('btnLogin');
  btn.addEventListener("click", login);
});

async function login(){
    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value.trim();
    const mensaje = document.getElementById('mensaje');
    const btn = document.getElementById('btnLogin');
    
   if(!username || !password){
      return;
   }

   try {

       btn.disabled = true;
       btn.textContent = "Entrando....";
       
       const res = await fetch("http://127.0.0.1:8000/api/login",{
        method:'POST',
        headers:{
            "Content-Type": 'application/json',
            "Accept": 'application/json'
        },
        body:JSON.stringify({
           username: username,
           password: password
        })
       });
     
       const data = await res.json();
      
       if(!res.ok){
          throw new Error(data.mensaje || 'Error al Inciar Sesión')
       }

       localStorage.setItem("token",data.token);
       localStorage.setItem("user", JSON.stringify(data.usuario));

       mostrarMensaje("Credencailes correctas", "success");
       
       setTimeout(()=>{
          const rol = data.usuario.rol.toLowerCase();
           if (rol === "admin") {
                window.location.href = "/admin-dashboard";
            } else if (rol === "recepcionista") {
                window.location.href = "/recepcionista-dashboard";
            } else {
                window.location.href = "/cliente-dashboard";
            }
       }, 1000);




   } catch (error) {
       mostrarMensaje(error.message, "error");
   }finally {
        btn.disabled = false;
        btn.textContent = "Entrar";
    }
}


function mostrarMensaje(texto, tipo) {
    const mensaje = document.getElementById("mensaje");

    mensaje.textContent = texto;
    mensaje.style.color = tipo === "success" ? "#2ecc71" : "#e74c3c";
}

function limpiarMensaje() {
    document.getElementById("mensaje").textContent = "";
}