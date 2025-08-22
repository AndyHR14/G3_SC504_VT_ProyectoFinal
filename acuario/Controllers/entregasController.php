<?php
require_once __DIR__ . '/../Models/entregas_db.php';

class EntregasController
{
    public function index()
    {
        $m = new EntregasDB();
        $entregas = $m->listarEntregas();
        require __DIR__ . '/../Views/entregas/list.php';
    }

    public function crear()
    {
        $m = new EntregasDB();
        $usuarios   = $m->listarUsuarios();
        $direcciones= $m->listarDirecciones();
        $estados    = $m->listarEstados();
        $entrega = null;
        $detalle = null; 
        $titulo_form = "Registrar Entrega";
        require __DIR__ . '/../Views/entregas/form.php';
    }

    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $m = new EntregasDB();
            $ok = $m->insertarEntrega(
                $_POST['FECHA'] ?? null,
                $_POST['ID_DIRECCION'] ?? null,
                $_POST['ID_USUARIO'] ?? null,
                $_POST['ID_ESTADO'] ?? null
            );
            header('Location: index.php?mod=entregas&action=listarEntregas' . ($ok ? '' : '&msg=error'));
            exit;
        }
        header('Location: index.php?mod=entregas&action=listarEntregas&msg=error'); exit;
    }

    public function editar()
    {
        $id = $_GET['id'] ?? null;
        if (!is_numeric($id)) {
            header('Location: index.php?mod=entregas&action=listarEntregas&msg=id_no_valido'); exit;
        }
        $m = new EntregasDB();
        $entrega = $m->obtenerEntregaPorId((int)$id);
        if (!$entrega) {
            header('Location: index.php?mod=entregas&action=listarEntregas&msg=no_encontrado'); exit;
        }
        $detalle     = $m->obtenerDetallePorEntrega((int)$id);
        $usuarios    = $m->listarUsuarios();
        $direcciones = $m->listarDirecciones();
        $estados     = $m->listarEstados();
        $titulo_form = "Actualizar Entrega";
        require __DIR__ . '/../Views/entregas/form.php';
    }

    public function actualizar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ID_ENTREGA'])) {
            $m = new EntregasDB();
            $ok = $m->actualizarEntrega(
                (int)$_POST['ID_ENTREGA'],
                $_POST['FECHA'] ?? null,
                $_POST['ID_DIRECCION'] ?? null,
                $_POST['ID_USUARIO'] ?? null,
                $_POST['ID_ESTADO'] ?? null
            );
            header('Location: index.php?mod=entregas&action=listarEntregas' . ($ok ? '&msg=actualizado' : '&msg=error'));
            exit;
        }
        header('Location: index.php?mod=entregas&action=listarEntregas&msg=error'); exit;
    }

    public function eliminar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ID_ENTREGA'])) {
            $m = new EntregasDB();
            $ok = $m->eliminarEntrega((int)$_POST['ID_ENTREGA']);
            header('Location: index.php?mod=entregas&action=listarEntregas' . ($ok ? '&msg=eliminado' : '&msg=error'));
            exit;
        }
        header('Location: index.php?mod=entregas&action=listarEntregas&msg=error'); exit;
    }

    

    public function guardarDetalle()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ID_ENTREGA'])) {
            $m = new EntregasDB();
            $ok = $m->upsertDetalle(
                (int)$_POST['ID_ENTREGA'],
                $_POST['DESCRIPCION'] ?? '',
                $_POST['CANTIDAD'] ?? null,
                $_POST['ID_ESTADO_DET'] ?? null
            );
            header('Location: index.php?mod=entregas&action=editarEntrega&id='.(int)$_POST['ID_ENTREGA'] . ($ok ? '&msg=detalle_ok' : '&msg=error'));
            exit;
        }
        header('Location: index.php?mod=entregas&action=listarEntregas&msg=error'); exit;
    }

    public function eliminarDetalle()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ID_ENTREGA'])) {
            $m = new EntregasDB();
            $ok = $m->eliminarDetalle((int)$_POST['ID_ENTREGA']);
            header('Location: index.php?mod=entregas&action=editarEntrega&id='.(int)$_POST['ID_ENTREGA'] . ($ok ? '&msg=detalle_borrado' : '&msg=error'));
            exit;
        }
        header('Location: index.php?mod=entregas&action=listarEntregas&msg=error'); exit;
    }
}
