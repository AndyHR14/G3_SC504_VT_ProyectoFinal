<?php
require_once __DIR__ . '/../Models/alimentos_db.php';

class AlimentosController
{
    public function index()
    {
        $m = new AlimentosDB();
        $alimentos = $m->listarAlimentos();
        require __DIR__ . '/../Views/alimentos/list.php';
    }

    public function crear()
    {
        $m = new AlimentosDB();
        $estados = $m->listarEstados();
        $alimento = null;
        $titulo_form = "Registrar Marca de Alimento";
        require __DIR__ . '/../Views/alimentos/form.php';
    }

    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $m = new AlimentosDB();
            $ok = $m->insertarAlimento(
                $_POST['NOMBRE'] ?? '',
                $_POST['DESCRIPCION'] ?? '',
                $_POST['ID_ESTADO'] ?? null
            );
            header('Location: index.php?mod=alimentos&action=listarAlimentos' . ($ok ? '' : '&msg=error'));
            exit;
        }
        header('Location: index.php?mod=alimentos&action=listarAlimentos&msg=error');
        exit;
    }

    public function editar()
    {
        $id = $_GET['id'] ?? null;
        if (!is_numeric($id)) {
            header('Location: index.php?mod=alimentos&action=listarAlimentos&msg=id_no_valido');
            exit;
        }

        $m = new AlimentosDB();
        $alimento = $m->obtenerAlimentoPorId((int)$id);
        if (!$alimento) {
            header('Location: index.php?mod=alimentos&action=listarAlimentos&msg=no_encontrado');
            exit;
        }

        $estados = $m->listarEstados();
        $titulo_form = "Actualizar Marca de Alimento";
        require __DIR__ . '/../Views/alimentos/form.php';
    }

    public function actualizar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ID_MARCA_ALIMENTO'])) {
            $m = new AlimentosDB();
            $ok = $m->actualizarAlimento(
                (int)$_POST['ID_MARCA_ALIMENTO'],
                $_POST['NOMBRE'] ?? '',
                $_POST['DESCRIPCION'] ?? '',
                $_POST['ID_ESTADO'] ?? null
            );
            header('Location: index.php?mod=alimentos&action=listarAlimentos' . ($ok ? '&msg=actualizado' : '&msg=error'));
            exit;
        }
        header('Location: index.php?mod=alimentos&action=listarAlimentos&msg=error');
        exit;
    }

    public function eliminar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ID_MARCA_ALIMENTO'])) {
            $m = new AlimentosDB();
            $ok = $m->eliminarAlimento((int)$_POST['ID_MARCA_ALIMENTO']);
            header('Location: index.php?mod=alimentos&action=listarAlimentos' . ($ok ? '&msg=eliminado' : '&msg=error'));
            exit;
        }
        header('Location: index.php?mod=alimentos&action=listarAlimentos&msg=error');
        exit;
    }
}
