<?php
require_once __DIR__ . '/../Models/colaboradores_db.php';

class ColaboradoresController
{
    public function index()
    {
        $m = new ColaboradoresDB();
        $colaboradores = $m->listarColaboradores();
        require __DIR__ . '/../Views/colaboradores/list.php';
    }

    public function crear()
    {
        $m = new ColaboradoresDB();
        $estados = $m->listarEstados();
        $roles   = $m->listarRoles();
        $col = null;
        $titulo_form = "Registrar Colaborador";
        require __DIR__ . '/../Views/colaboradores/form.php';
    }

    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $m = new ColaboradoresDB();
            $ok = $m->insertarColaborador(
                $_POST['NOMBRE'] ?? '',
                $_POST['FECHA_REGISTRO'] ?? null,
                $_POST['TELEFONO'] ?? '',
                $_POST['CORREO'] ?? '',
                $_POST['ID_ESTADO'] ?? null,
                $_POST['ID_ROL'] ?? null
            );
            header('Location: index.php?mod=colaboradores&action=listarColaboradores' . ($ok ? '' : '&msg=error'));
            exit;
        }
        header('Location: index.php?mod=colaboradores&action=listarColaboradores&msg=error');
        exit;
    }

    public function editar()
    {
        $id = $_GET['id'] ?? null;
        if (!is_numeric($id)) {
            header('Location: index.php?mod=colaboradores&action=listarColaboradores&msg=id_no_valido');
            exit;
        }

        $m = new ColaboradoresDB();
        $col = $m->obtenerColaboradorPorId((int)$id);
        if (!$col) {
            header('Location: index.php?mod=colaboradores&action=listarColaboradores&msg=no_encontrado');
            exit;
        }

        $estados = $m->listarEstados();
        $roles   = $m->listarRoles();
        $titulo_form = "Actualizar Colaborador";
        require __DIR__ . '/../Views/colaboradores/form.php';
    }

    public function actualizar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ID_USUARIO'])) {
            $m = new ColaboradoresDB();
            $ok = $m->actualizarColaborador(
                (int)$_POST['ID_USUARIO'],
                $_POST['NOMBRE'] ?? '',
                $_POST['FECHA_REGISTRO'] ?? null,
                $_POST['TELEFONO'] ?? '',
                $_POST['CORREO'] ?? '',
                $_POST['ID_ESTADO'] ?? null,
                $_POST['ID_ROL'] ?? null
            );
            header('Location: index.php?mod=colaboradores&action=listarColaboradores' . ($ok ? '&msg=actualizado' : '&msg=error'));
            exit;
        }
        header('Location: index.php?mod=colaboradores&action=listarColaboradores&msg=error');
        exit;
    }

    public function eliminar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ID_USUARIO'])) {
            $m = new ColaboradoresDB();
            $ok = $m->eliminarColaborador((int)$_POST['ID_USUARIO']);
            header('Location: index.php?mod=colaboradores&action=listarColaboradores' . ($ok ? '&msg=eliminado' : '&msg=error'));
            exit;
        }
        header('Location: index.php?mod=colaboradores&action=listarColaboradores&msg=error');
        exit;
    }
}
