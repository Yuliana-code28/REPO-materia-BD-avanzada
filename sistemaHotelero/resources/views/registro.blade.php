<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - Hotel LUX</title>

    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>

<body>

<div class="login-container">
    <div class="brand">
        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
        </svg>
        Hotel LUX
    </div>
    <h2>Crear cuenta</h2>

    <div class="input-group">
        <span>
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M12 7a4 4 0 100-8 4 4 0 000 8z"></path>
            </svg>
        </span>
        <input type="text" id="nombre" placeholder="Nombre(s)">
    </div>

    <div class="input-group">
        <span>
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M8.5 7a4 4 0 100-8 4 4 0 000 8zM17 11l2 2 4-4"></path>
            </svg>
        </span>
        <input type="text" id="ap" placeholder="Apellido Paterno">
    </div>

    <div class="input-group">
        <span>
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M8.5 7a4 4 0 100-8 4 4 0 000 8zM17 11l2 2 4-4"></path>
            </svg>
        </span>
        <input type="text" id="am" placeholder="Apellido Materno">
    </div>

    <div class="input-group">
        <span>
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                <path d="M22 6l-10 7L2 6"></path>
            </svg>
        </span>
        <input type="email" id="email" placeholder="Correo electrónico">
    </div>

    <div class="input-group">
        <span>
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"></path>
            </svg>
        </span>
        <input type="text" id="telefono" placeholder="Teléfono">
    </div>

    <div class="input-group">
        <span>
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="4"></circle>
                <path d="M16 8v5a3 3 0 006 0v-1a10 10 0 10-3.92 7.94"></path>
            </svg>
        </span>
        <input type="text" id="username" placeholder="Nombre de usuario">
    </div>

    <div class="input-group">
        <span>
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0110 0v4"></path>
            </svg>
        </span>
        <input type="password" id="password" placeholder="Contraseña">
    </div>

    <div class="input-group">
        <span>
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                <path d="M9 12l2 2 4-4"></path>
            </svg>
        </span>
        <input type="password" id="password_confirmation" placeholder="Confirmar contraseña">
    </div>

    <button id="btnRegistro">Registrarse</button>

    <p style="text-align: center; margin-top: 15px; font-size: 14px;">
        ¿Ya tienes cuenta? <a href="/login">Inicia sesión</a>
    </p>

    <p id="mensaje"></p>
</div>
<script src="{{ asset('js/registro.js') }}"></script>

</body>
</html>
