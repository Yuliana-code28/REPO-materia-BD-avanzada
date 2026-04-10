document.addEventListener("DOMContentLoaded",()=>{
   const btnLogout = document.getElementById("btnLogout");

   btnLogout.addEventListener("click", function(e){
      e.preventDefault();
      logout();
   });
});


async function logout() {
     const token  = localStorage.getItem("token");

     try {
         await fetch('/api/logout',{
            method:'POST',
            headers:{
                "Authorization": "Bearer " + token,
                "Accept": "application/json"
            }
         });

     } catch (error) {
        console.error("Error al cerrar sesión", error);
     }finally{
        localStorage.removeItem("token");
        localStorage.removeItem("user");

        window.location.href = "/login";
     }
}