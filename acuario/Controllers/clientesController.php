<?php
require_once __DIR__ . '/../Models/clientes_db.php';

class ClientesController
{
    public function index()
    {
        $m = new ClientesDB();
        $clientes = $m->listar();
        require __DIR__ . '/../Views/clientes/list.php';
    }

    public function crear()
    {
        $m = new ClientesDB();
        $estados     = $m->listarEstados();
        $roles       = $m->listarRoles();
        $direcciones = $m->listarDirecciones();
        $cliente = null;
        $titulo_form = "Registrar Cliente";
        require __DIR__ . '/../Views/clientes/form.php';
    }

    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $m = new ClientesDB();
            $ok = $m->insertar(
                $_POST['NOMBRE'] ?? '',
                $_POST['FECHA_REGISTRO'] ?? null,
                $_POST['TELEFONO'] ?? null,
                $_POST['CORREO'] ?? null,
                $_POST['ID_ESTADO'] ?? null,
                $_POST['ID_ROL'] ?? null,
                $_POST['ID_DIRECCION'] ?? null
            );
            header('Location: index.php?mod=clientes&action=listarClientes' . ($ok ? '' : '&msg=error'));
            exit;
        }
        header('Location: index.php?mod=clientes&action=listarClientes&msg=error'); exit;
    }

    public function editar()
    {
        $id = $_GET['id'] ?? null;
        if (!is_numeric($id)) {
            header('Location: index.php?mod=clientes&action=listarClientes&msg=id_no_valido'); exit;
        }
        $m = new ClientesDB();
        $cliente = $m->obtenerPorId((int)$id);
        if (!$cliente) {
            header('Location: index.php?mod=clientes&action=listarClientes&msg=no_encontrado'); exit;
        }
        $estados     = $m->listarEstados();
        $roles       = $m->listarRoles();
        $direcciones = $m->listarDirecciones();
        $titulo_form = "Actualizar Cliente";
        require __DIR__ . '/../Views/clientes/form.php';
    }

    public function actualizar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ID_USUARIO'])) {
            $m = new ClientesDB();
            $ok = $m->actualizar(
                (int)$_POST['ID_USUARIO'],
                $_POST['NOMBRE'] ?? '',
                $_POST['FECHA_REGISTRO'] ?? null,
                $_POST['TELEFONO'] ?? null,
                $_POST['CORREO'] ?? null,
                $_POST['ID_ESTADO'] ?? null,
                $_POST['ID_ROL'] ?? null,
                $_POST['ID_DIRECCION'] ?? null
            );
            header('Location: index.php?mod=clientes&action=listarClientes' . ($ok ? '&msg=actualizado' : '&msg=error'));
            exit;
        }
        header('Location: index.php?mod=clientes&action=listarClientes&msg=error'); exit;
    }

    public function eliminar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ID_USUARIO'])) {
            $m = new ClientesDB();
            $ok = $m->eliminar((int)$_POST['ID_USUARIO']);
            header('Location: index.php?mod=clientes&action=listarClientes' . ($ok ? '&msg=eliminado' : '&msg=error'));
            exit;
        }
        header('Location: index.php?mod=clientes&action=listarClientes&msg=error'); exit;
    }
}
