<?php
session_start();
require_once __DIR__ . '/../config.php';

// Vaciar variables de sesión
$_SESSION = [];

// Borrar la cookie de sesión si existe
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// Destruir la sesión
session_destroy();

// Redirigir a login
header('Location: ' . BASE_URL . '/auth/login.php');
exit;
