<?php
require_once __DIR__ . '/../Models/horarios_db.php';

class HorariosController
{
    public function index()
    {
        $m = new HorariosDB();
        $horarios = $m->listarHorarios();
        require __DIR__ . '/../Views/horarios/list.php';
    }

    public function crear()
    {
        $m = new HorariosDB();
        $estados  = $m->listarEstados();
        $usuarios = $m->listarUsuarios();
        $horario = null;
        $titulo_form = "Registrar Horario";
        require __DIR__ . '/../Views/horarios/form.php';
    }

    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $m = new HorariosDB();
            $ok = $m->insertarHorario(
                $_POST['DIA'] ?? '',
                $_POST['HORA_INICIO'] ?? '',
                $_POST['HORA_FINAL'] ?? '',
                $_POST['ID_USUARIO'] ?? null,
                $_POST['ID_ESTADO'] ?? null
            );
            header('Location: index.php?mod=horarios&action=listarHorarios' . ($ok ? '' : '&msg=error'));
            exit;
        }
        header('Location: index.php?mod=horarios&action=listarHorarios&msg=error'); exit;
    }

    public function editar()
    {
        $id = $_GET['id'] ?? null;
        if (!is_numeric($id)) {
            header('Location: index.php?mod=horarios&action=listarHorarios&msg=id_no_valido'); exit;
        }
        $m = new HorariosDB();
        $horario = $m->obtenerHorarioPorId((int)$id);
        if (!$horario) {
            header('Location: index.php?mod=horarios&action=listarHorarios&msg=no_encontrado'); exit;
        }
        $estados  = $m->listarEstados();
        $usuarios = $m->listarUsuarios();
        $titulo_form = "Actualizar Horario";
        require __DIR__ . '/../Views/horarios/form.php';
    }

    public function actualizar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ID_HORARIO'])) {
            $m = new HorariosDB();
            $ok = $m->actualizarHorario(
                (int)$_POST['ID_HORARIO'],
                $_POST['DIA'] ?? '',
                $_POST['HORA_INICIO'] ?? '',
                $_POST['HORA_FINAL'] ?? '',
                $_POST['ID_USUARIO'] ?? null,
                $_POST['ID_ESTADO'] ?? null
            );
            header('Location: index.php?mod=horarios&action=listarHorarios' . ($ok ? '&msg=actualizado' : '&msg=error'));
            exit;
        }
        header('Location: index.php?mod=horarios&action=listarHorarios&msg=error'); exit;
    }

    public function eliminar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ID_HORARIO'])) {
            $m = new HorariosDB();
            $ok = $m->eliminarHorario((int)$_POST['ID_HORARIO']);
            header('Location: index.php?mod=horarios&action=listarHorarios' . ($ok ? '&msg=eliminado' : '&msg=error'));
            exit;
        }
        header('Location: index.php?mod=horarios&action=listarHorarios&msg=error'); exit;
    }
}
