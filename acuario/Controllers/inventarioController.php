<?php
require_once __DIR__ . '/../Models/inventario_db.php';

class InventarioController
{
    public function index()
    {
        $m = new InventarioDB();
        $items = $m->listarInventario();
        require __DIR__ . '/../Views/inventario/list.php';
    }

    public function crear()
    {
        $m = new InventarioDB();
        $categorias = $m->listarCategorias();
        $unidades   = $m->listarUnidades();
        $estados    = $m->listarEstados(); 
        $item = null;
        $titulo_form = "Registrar Producto / Inventario";
        require __DIR__ . '/../Views/inventario/form.php';
    }

    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $m = new InventarioDB();
            $ok = $m->insertar(
                $_POST['NOMBRE'] ?? '',
                $_POST['ID_CATEGORIA'] ?? null,
                $_POST['ID_UNIDAD_MEDIDA'] ?? null,
                $_POST['ID_ESTADO_PROD'] ?? null,
                $_POST['CANTIDAD'] ?? null,
                $_POST['FECHA_INGRESO'] ?? null,
                $_POST['ID_ESTADO_INV'] ?? null
            );
            header('Location: index.php?mod=inventario&action=listarInventario' . ($ok ? '' : '&msg=error'));
            exit;
        }
        header('Location: index.php?mod=inventario&action=listarInventario&msg=error'); exit;
    }

    public function editar()
    {
        $id = $_GET['id'] ?? null;
        if (!is_numeric($id)) {
            header('Location: index.php?mod=inventario&action=listarInventario&msg=id_no_valido'); exit;
        }
        $m = new InventarioDB();
        $item = $m->obtenerPorId((int)$id);
        if (!$item) {
            header('Location: index.php?mod=inventario&action=listarInventario&msg=no_encontrado'); exit;
        }
        $categorias = $m->listarCategorias();
        $unidades   = $m->listarUnidades();
        $estados    = $m->listarEstados();
        $titulo_form = "Actualizar Producto / Inventario";
        require __DIR__ . '/../Views/inventario/form.php';
    }

    public function actualizar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ID_PRODUCTO'])) {
            $m = new InventarioDB();
            $ok = $m->actualizar(
                (int)$_POST['ID_PRODUCTO'],
                $_POST['NOMBRE'] ?? '',
                $_POST['ID_CATEGORIA'] ?? null,
                $_POST['ID_UNIDAD_MEDIDA'] ?? null,
                $_POST['ID_ESTADO_PROD'] ?? null,
                $_POST['CANTIDAD'] ?? null,
                $_POST['FECHA_INGRESO'] ?? null,
                $_POST['ID_ESTADO_INV'] ?? null
            );
            header('Location: index.php?mod=inventario&action=listarInventario' . ($ok ? '&msg=actualizado' : '&msg=error'));
            exit;
        }
        header('Location: index.php?mod=inventario&action=listarInventario&msg=error'); exit;
    }

    public function eliminar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ID_PRODUCTO'])) {
            $m = new InventarioDB();
            $ok = $m->eliminar((int)$_POST['ID_PRODUCTO']);
            header('Location: index.php?mod=inventario&action=listarInventario' . ($ok ? '&msg=eliminado' : '&msg=error'));
            exit;
        }
        header('Location: index.php?mod=inventario&action=listarInventario&msg=error'); exit;
    }
}
