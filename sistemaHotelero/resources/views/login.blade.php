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
        <span>
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M12 7a4 4 0 100-8 4 4 0 000 8z"></path>
            </svg>
        </span>
        <input type="text" id="username" placeholder="Usuario">
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

    <div class="options">
        <label>
            <input type="checkbox"> Recordarme
        </label>
        <span><a href="/contrasena-recuperar">¿Olvidaste tu contraseña?</a></span>
    </div>

    <button id="btnLogin">Entrar</button>

    <p style="text-align: center; margin-top: 15px; font-size: 14px;">
        ¿No tienes cuenta? <a href="/registro">Regístrate aquí</a>
    </p>

    <p id="mensaje"></p>
</div>
<script src="{{ asset('js/login.js') }}"></script>

</body>
</html>