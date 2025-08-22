<?php
require_once __DIR__ . '/../Models/proveedores_db.php';

class ProveedoresController
{
    public function index()
    {
        $m = new ProveedoresDB();
        $proveedores = $m->listar();
        require __DIR__ . '/../Views/proveedores/list.php';
    }

    public function crear()
    {
        $m = new ProveedoresDB();
        $direcciones = $m->listarDirecciones();
        $estados     = $m->listarEstados();
        $prov = null;
        $titulo_form = "Registrar Proveedor";
        require __DIR__ . '/../Views/proveedores/form.php';
    }

    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $m = new ProveedoresDB();
            $ok = $m->insertar(
                $_POST['NOMBRE_EMPRESA'] ?? '',
                $_POST['TELEFONO'] ?? null,
                $_POST['CORREO'] ?? null,
                $_POST['ID_DIRECCION'] ?? null,
                $_POST['ID_ESTADO'] ?? null
            );
            header('Location: index.php?mod=proveedores&action=listarProveedores' . ($ok ? '' : '&msg=error'));
            exit;
        }
        header('Location: index.php?mod=proveedores&action=listarProveedores&msg=error'); exit;
    }

    public function editar()
    {
        $id = $_GET['id'] ?? null;
        if (!is_numeric($id)) {
            header('Location: index.php?mod=proveedores&action=listarProveedores&msg=id_no_valido'); exit;
        }
        $m    = new ProveedoresDB();
        $prov = $m->obtenerPorId((int)$id);
        if (!$prov) {
            header('Location: index.php?mod=proveedores&action=listarProveedores&msg=no_encontrado'); exit;
        }
        $direcciones = $m->listarDirecciones();
        $estados     = $m->listarEstados();
        $titulo_form = "Actualizar Proveedor";
        require __DIR__ . '/../Views/proveedores/form.php';
    }

    public function actualizar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ID_EMPRESA'])) {
            $m = new ProveedoresDB();
            $ok = $m->actualizar(
                (int)$_POST['ID_EMPRESA'],
                $_POST['NOMBRE_EMPRESA'] ?? '',
                $_POST['TELEFONO'] ?? null,
                $_POST['CORREO'] ?? null,
                $_POST['ID_DIRECCION'] ?? null,
                $_POST['ID_ESTADO'] ?? null
            );
            header('Location: index.php?mod=proveedores&action=listarProveedores' . ($ok ? '&msg=actualizado' : '&msg=error'));
            exit;
        }
        header('Location: index.php?mod=proveedores&action=listarProveedores&msg=error'); exit;
    }

    public function eliminar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ID_EMPRESA'])) {
            $m = new ProveedoresDB();
            $ok = $m->eliminar((int)$_POST['ID_EMPRESA']);
            header('Location: index.php?mod=proveedores&action=listarProveedores' . ($ok ? '&msg=eliminado' : '&msg=error'));
            exit;
        }
        header('Location: index.php?mod=proveedores&action=listarProveedores&msg=error'); exit;
    }
}
