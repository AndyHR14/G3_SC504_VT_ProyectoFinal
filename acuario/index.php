<?php
ini_set('default_charset', 'UTF-8');
mb_internal_encoding('UTF-8');
if (!headers_sent()) {
  header('Content-Type: text/html; charset=UTF-8');
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

register_shutdown_function(function () {
    $e = error_get_last();
    if ($e && in_array($e['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        echo "<pre>FATAL: {$e['message']} in {$e['file']}:{$e['line']}</pre>";
    }
});

// 👇 NUEVO: protege esta página (solo admin general)
require_once __DIR__ . '/auth/auth_check.php';
// (auth_check.php ya hace session_start e importa BASE_URL internamente)

$mod    = $_GET['mod']    ?? 'home';           // animales | alimentos | home
$action = $_GET['action'] ?? null;

switch ($mod) {
    case 'animales':
        require __DIR__ . '/controllers/AnimalesController.php';
        $c = new AnimalesController();
        switch ($action) {
            case 'listarAnimales':   $c->index();      break;
            case 'nuevoAnimal':      $c->crear();      break;
            case 'guardarAnimal':    $c->guardar();    break;
            case 'editarAnimal':     $c->editar();     break;
            case 'actualizarAnimal': $c->actualizar(); break;
            case 'eliminarAnimal':   $c->eliminar();   break;
            default:                 $c->index();      break;
        }
        break;

    case 'alimentos':
        require __DIR__ . '/controllers/AlimentosController.php';
        $c = new AlimentosController();
        switch ($action) {
            case 'listarMarcas':     $c->index();      break;
            case 'nuevaMarca':       $c->crear();      break;
            case 'guardarMarca':     $c->guardar();    break;
            case 'editarMarca':      $c->editar();     break;
            case 'actualizarMarca':  $c->actualizar(); break;
            case 'eliminarMarca':    $c->eliminar();   break;
            default:                 $c->index();      break;
        }
        break;

    case 'colaboradores':
        require __DIR__ . '/controllers/ColaboradoresController.php';
        $c = new ColaboradoresController();
        switch ($action) {
            case 'listarColaboradores':   $c->index();      break;
            case 'nuevoColaborador':      $c->crear();      break;
            case 'guardarColaborador':    $c->guardar();    break;
            case 'editarColaborador':     $c->editar();     break;
            case 'actualizarColaborador': $c->actualizar(); break;
            case 'eliminarColaborador':   $c->eliminar();   break;
            default:                      $c->index();      break;
        }
        break;

    case 'horarios':
        require __DIR__ . '/controllers/HorariosController.php';
        $c = new HorariosController();
        switch ($action) {
            case 'listarHorarios':    $c->index();      break;
            case 'nuevoHorario':      $c->crear();      break;
            case 'guardarHorario':    $c->guardar();    break;
            case 'editarHorario':     $c->editar();     break;
            case 'actualizarHorario': $c->actualizar(); break;
            case 'eliminarHorario':   $c->eliminar();   break;
            default:                  $c->index();      break;
        }
        break;

    case 'inventario':
        require __DIR__ . '/controllers/InventarioController.php';
        $c = new InventarioController();
        switch ($action) {
            case 'listarInventario':  $c->index();      break;
            case 'nuevoItem':         $c->crear();      break;
            case 'guardarItem':       $c->guardar();    break;
            case 'editarItem':        $c->editar();     break;
            case 'actualizarItem':    $c->actualizar(); break;
            case 'eliminarItem':      $c->eliminar();   break;
            default:                  $c->index();      break;
        }
        break;

    case 'proveedores':
        require __DIR__ . '/controllers/ProveedoresController.php';
        $c = new ProveedoresController();
        switch ($action) {
            case 'listarProveedores':   $c->index();      break;
            case 'nuevoProveedor':      $c->crear();      break;
            case 'guardarProveedor':    $c->guardar();    break;
            case 'editarProveedor':     $c->editar();     break;
            case 'actualizarProveedor': $c->actualizar(); break;
            case 'eliminarProveedor':   $c->eliminar();   break;
            default:                    $c->index();      break;
        }
        break;

    case 'clientes':
        require __DIR__ . '/controllers/ClientesController.php';
        $c = new ClientesController();
        switch ($action) {
            case 'listarClientes':    $c->index();      break;
            case 'nuevoCliente':      $c->crear();      break;
            case 'guardarCliente':    $c->guardar();    break;
            case 'editarCliente':     $c->editar();     break;
            case 'actualizarCliente': $c->actualizar(); break;
            case 'eliminarCliente':   $c->eliminar();   break;
            default:                  $c->index();      break;
        }
        break;

    case 'entregas':
        require __DIR__ . '/controllers/EntregasController.php';
        $c = new EntregasController();
        switch ($action) {
            case 'listarEntregas':      $c->index();          break;
            case 'nuevaEntrega':        $c->crear();          break;
            case 'guardarEntrega':      $c->guardar();        break;
            case 'editarEntrega':       $c->editar();         break;
            case 'actualizarEntrega':   $c->actualizar();     break;
            case 'eliminarEntrega':     $c->eliminar();       break;
            case 'guardarDetalle':      $c->guardarDetalle(); break;
            case 'eliminarDetalle':     $c->eliminarDetalle();break;
            default:                    $c->index();          break;
        }
        break;

    case 'facturas':
        require __DIR__ . '/controllers/FacturasController.php';
        $c = new FacturasController();
        switch ($action) {
            case 'listarFacturas':     $c->index();        break;
            case 'nuevaFactura':       $c->crear();        break;
            case 'guardarFactura':     $c->guardar();      break;
            case 'editarFactura':      $c->editar();       break;
            case 'actualizarFactura':  $c->actualizar();   break;
            case 'eliminarFactura':    $c->eliminar();     break;
            case 'guardarDetalle':     $c->guardarDetalle();  break;
            case 'eliminarDetalle':    $c->eliminarDetalle(); break;
            default:                   $c->index();        break;
        }
        break;

    default:
        // ======= HOME (siempre usando el layout) =======
        $title = 'Inicio';
        $view  = 'modules/home';                  // archivo en Views/modules/home.php
        include __DIR__ . '/Views/layout.php';
        exit;
}
