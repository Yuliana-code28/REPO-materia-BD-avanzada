<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>

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
    <h2>Iniciar sesión</h2>

    <div class="input-group">
        <span>👤</span>
        <input type="text" id="username" placeholder="Usuario">
    </div>

    <div class="input-group">
        <span>🔒</span>
        <input type="password" id="password" placeholder="Contraseña">
    </div>

    <div class="options">
        <label>
            <input type="checkbox"> Recordarme
        </label>
        <span><a href="/contrasena-recuperar">¿Olvidaste tu contraseña?</a></span>
    </div>

    <button id="btnLogin">Entrar</button>

    <p id="mensaje"></p>
</div>
<script src="{{ asset('js/login.js') }}"></script>

</body>
</html>