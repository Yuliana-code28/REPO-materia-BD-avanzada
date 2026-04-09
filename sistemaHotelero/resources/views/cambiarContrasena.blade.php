<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña - Hotel LUX</title>
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
        
        <h2>Nueva contraseña</h2>
        <p class="subtitle">Ingresa y confirma tu nueva contraseña para recuperar el acceso.</p>

        <form id="resetPasswordForm">
            <input type="hidden" id="token" value="{{ request('token') }}">
            <input type="hidden" id="email" value="{{ request('email') }}">
            
            <div class="input-group">
                <label for="password">Nueva contraseña</label>
                <input type="password" id="password" placeholder="Mínimo 6 caracteres" required>
            </div>

            <div class="input-group">
                <label for="password_confirmation">Confirmar contraseña</label>
                <input type="password" id="password_confirmation" placeholder="Repite tu contraseña" required>
            </div>
            
            <button type="submit">Restablecer contraseña</button>
            <div id="mensaje"></div>
        </form>

        <a href="/login" class="footer-link">Volver al inicio de sesión</a>
    </div>

    <script src="{{ asset('js/restablecerContrasena.js') }}"></script>
</body>
</html>