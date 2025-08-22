<?php
require_once __DIR__ . '/../Models/animales_db.php';

class AnimalesController
{

    public function index()
    {
        $m = new AnimalesDB();
        $animales = $m->obtenerAnimales();
        require __DIR__ . '/../Views/animales/list.php';
    }

    public function crear()
    {
        $m = new AnimalesDB();
        $generos  = $m->listarGeneros();
        $tipos    = $m->listarTipos();
        $habitats = $m->listarHabitats();
        $estados  = $m->listarEstados();
        $rutinas  = $m->listarRutinas();
        $marcas   = $m->listarMarcas();
        $animal = null;
        $titulo_form = "Registrar Animal";
        require __DIR__ . '/../Views/animales/form.php';
    }

    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $m = new AnimalesDB();
            $ok = $m->insertarAnimal(
                $_POST['NOMBRE_ANIMAL'] ?? '',
                $_POST['FECHA_INGRESO'] ?? null,
                $_POST['EDAD'] ?? null,
                $_POST['PESO'] ?? null,
                $_POST['OBSERVACION'] ?? '',
                $_POST['ID_GENERO'] ?? null,
                $_POST['ID_TIPO'] ?? null,
                $_POST['ID_HABITAT'] ?? null,
                $_POST['ID_ESTADO'] ?? null,
                $_POST['ID_RUTINA'] ?? null,
                $_POST['ID_MARCA_ALIMENTO'] ?? null,
                $_POST['USUARIO'] ?? 'WEB'
            );
            header('Location: index.php?mod=animales&action=listarAnimales' . ($ok ? '' : '&msg=error'));
            exit;
        }
        header('Location: index.php?mod=animales&action=listarAnimales&msg=error');
        exit;
    }

    public function editar()
    {
        $id = $_GET['id'] ?? null;
        if (!is_numeric($id)) {
            header('Location: index.php?mod=animales&action=listarAnimales&msg=id_no_valido');
            exit;
        }

        $m = new AnimalesDB();
        $animal = $m->obtenerAnimalPorId((int)$id);
        if (!$animal) {
            header('Location: index.php?mod=animales&action=listarAnimales&msg=no_encontrado');
            exit;
        }

        $generos  = $m->listarGeneros();
        $tipos    = $m->listarTipos();
        $habitats = $m->listarHabitats();
        $estados  = $m->listarEstados();
        $rutinas  = $m->listarRutinas();
        $marcas   = $m->listarMarcas();

        $titulo_form = "Actualizar Animal";
        require __DIR__ . '/../Views/animales/form.php';
    }

    public function actualizar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ID_ANIMAL'])) {
            $m = new AnimalesDB();
            $ok = $m->actualizarAnimal(
                (int)$_POST['ID_ANIMAL'],
                $_POST['NOMBRE_ANIMAL'] ?? '',
                $_POST['FECHA_INGRESO'] ?? null,
                $_POST['EDAD'] ?? null,
                $_POST['PESO'] ?? null,
                $_POST['OBSERVACION'] ?? '',
                $_POST['ID_GENERO'] ?? null,
                $_POST['ID_TIPO'] ?? null,
                $_POST['ID_HABITAT'] ?? null,
                $_POST['ID_ESTADO'] ?? null,
                $_POST['ID_RUTINA'] ?? null,
                $_POST['ID_MARCA_ALIMENTO'] ?? null,
                $_POST['USUARIO'] ?? 'WEB'
            );
            header('Location: index.php?mod=animales&action=listarAnimales' . ($ok ? '&msg=Ha sido actualizado' : '&msg=error'));
            exit;
        }
        header('Location: index.php?mod=animales&action=listarAnimales&msg=error');
        exit;
    }

    public function eliminar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ID_ANIMAL'])) {
            $m = new AnimalesDB();
            $ok = $m->eliminarAnimal((int)$_POST['ID_ANIMAL'], $_POST['USUARIO'] ?? 'WEB');
            header('Location: index.php?mod=animales&action=listarAnimales' . ($ok ? '&msg=inactivado' : '&msg=error'));
            exit;
        }
        header('Location: index.php?mod=animales&action=listarAnimales&msg=error');
        exit;
    }
}
