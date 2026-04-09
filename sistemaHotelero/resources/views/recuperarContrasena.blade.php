<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - Hotel LUX</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/contrasena.css') }}">
</head>
<body>
    <div class="container">
        <div class="brand">
            <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            Hotel LUX
        </div>
        
        <h2>¿Olvidaste tu contraseña?</h2>
        <p class="subtitle">Ingresa tu correo y te enviaremos un enlace para recuperarla.</p>

        <form id="forgotPasswordForm">
            <div class="input-group">
                <label for="email">Correo electrónico</label>
                <input type="email" id="email" placeholder="ejemplo@hotel.com" required>
            </div>
            
            <button type="submit">Enviar enlace de recuperación</button>
            <div id="mensaje"></div>
        </form>

        <a href="/login" class="footer-link">Volver al inicio de sesión</a>
    </div>

    <script src="{{ asset('js/recuperarContrasena.js') }}"></script>
</body>
</html>