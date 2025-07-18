<?php
// Habilitar la visualización de errores para depuración (desactivar en producción)
// Esto te ayudará a ver cualquier problema de PHP directamente en la página.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluye el controlador principal de usuarios.
// Asegúrate de que esta ruta sea correcta desde la raíz del proyecto.
require_once 'Controllers/UsuarioController.php';

// Crea una instancia del controlador de usuarios.
$controller = new UsuarioController();

// Determina la acción a ejecutar.
// Si no se especifica una acción en la URL (ej. index.php),
// por defecto se usará 'listarUsuarios'.
$action = $_GET['action'] ?? 'listarUsuarios';

// Usa una estructura switch para manejar las diferentes acciones.
switch ($action) {
    case 'nuevoUsuario':
        // Muestra el formulario para crear un nuevo usuario.
        $controller->crear();
        break;
    case 'guardarUsuario':
        // Procesa los datos del formulario para guardar un nuevo usuario.
        $controller->guardar();
        break;
    case 'listarUsuarios':
        // Muestra la lista de todos los usuarios registrados.
        $controller->index();
        break;
    case 'editarUsuario':
        // Muestra el formulario para editar un usuario existente.
        
        $controller->editar();
        break;
    case 'actualizarUsuario':
        // Procesa los datos del formulario para actualizar un usuario.
        $controller->actualizar();
        break;

     case 'cambiarEstadoUsuario':
        // Muestra el formulario para cambiar el estado (eliminar lógico).
        $controller->cambiarEstado();
        break;

    case 'guardarEstadoUsuario':
        // Guarda el nuevo estado seleccionado desde el formulario.
        $controller->guardarEstado();
        break;

  
    default:
        // Si la acción no es reconocida, por defecto, se redirige a la lista de usuarios.
        $controller->index();
        break;
}


?>