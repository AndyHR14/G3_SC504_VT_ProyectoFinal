<?php
// Enrutamiento simple con el parámetro "p"
$p = $_GET['p'] ?? 'inicio';

// Ruta a las vistas
$ruta_vistas = __DIR__ . '/Views/';

// Archivo a cargar
switch ($p) {
    case 'inicio':
        $vista = 'index.php';
        break;
    case 'animales':
        $vista = 'animales.php';
        break;
    case 'alimentacion':
        $vista = 'alimentacion.php';
        break;
    case 'colaboradores':
        $vista = 'colaboradores.php';
        break;
    case 'horarios':
        $vista = 'horarios.php';
        break;
    case 'inventario':
        $vista = 'inventario.php';
        break;
    default:
        $vista = 'index.php'; // Fallback
        break;
}

$archivo = $ruta_vistas . $vista;
if (file_exists($archivo)) {
    include $archivo;
} else {
    echo "<h1>404 - Página no encontrada</h1>";
}
