<?php


require_once 'Models/usuario.php';
require_once 'Models/conexion.php';

class UsuarioController {

    public function index() {
        $usuarioModel = new Usuario();
        $usuarios = $usuarioModel->obtenerUsuarios();
        require 'Views/usuarios_registrados.php';
    }

    public function crear() {
        $conexion_obj = new Conexion(); 
        $oci_conn = $conexion_obj->getConexion(); 

        $roles = [];
        try {
            $sql_roles = "SELECT ID_ROL, NOMBRE_ROL FROM FIDE_ROL_TB ORDER BY NOMBRE_ROL ASC";
            $stmt_roles = oci_parse($oci_conn, $sql_roles);
            oci_execute($stmt_roles);
            while ($row = oci_fetch_assoc($stmt_roles)) {
                $roles[] = $row;
            }
            oci_free_statement($stmt_roles); 
        } catch (Exception $e) {
            error_log("Error al cargar roles en crear(): " . $e->getMessage());
        }

        $estados = [];
        try {
            $sql_estados = "SELECT ID_ESTADO, NOMBRE_ESTADO FROM FIDE_ESTADOS_TB ORDER BY NOMBRE_ESTADO ASC";
            $stmt_estados = oci_parse($oci_conn, $sql_estados);
            oci_execute($stmt_estados);
            while ($row = oci_fetch_assoc($stmt_estados)) {
                $estados[] = $row;
            }
            oci_free_statement($stmt_estados);
        } catch (Exception $e) {
            error_log("Error al cargar estados en crear(): " . $e->getMessage());
        }
        
        $usuario = null;
        $titulo_form = "Registrar Nuevo Usuario";
        $action_form = "guardarUsuario";
        require 'Views/form.php';
    }

    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuarioModel = new Usuario();
            $insertado = $usuarioModel->insertarUsuario(
                $_POST['nombre'] ?? '',
                $_POST['telefono'] ?? '',
                $_POST['correo'] ?? '',
                $_POST['id_rol'] ?? null,
                $_POST['id_estado'] ?? null
            );
            if ($insertado) {
                header('Location: index.php?action=listarUsuarios');
            } else {
                error_log("Fallo al insertar usuario.");
                header('Location: index.php?action=nuevoUsuario&error=insert_failed');
            }
            exit;
        } else {
            header('Location: index.php?action=nuevoUsuario');
            exit;
        }
    }

    public function editar() {
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $id_usuario = $_GET['id'];
            $usuarioModel = new Usuario();
            $usuario = $usuarioModel->obtenerUsuarioPorId($id_usuario);

            if ($usuario) {
                $conexion_obj = new Conexion(); 
                $oci_conn = $conexion_obj->getConexion();

                $roles = [];
                $sql_roles = "SELECT ID_ROL, NOMBRE_ROL FROM FIDE_ROL_TB ORDER BY NOMBRE_ROL ASC";
                $stmt_roles = oci_parse($oci_conn, $sql_roles);
                oci_execute($stmt_roles);
                while ($row = oci_fetch_assoc($stmt_roles)) {
                    $roles[] = $row;
                }

                $estados = [];
                $sql_estados = "SELECT ID_ESTADO, NOMBRE_ESTADO FROM FIDE_ESTADOS_TB ORDER BY NOMBRE_ESTADO ASC";
                $stmt_estados = oci_parse($oci_conn, $sql_estados);
                oci_execute($stmt_estados);
                while ($row = oci_fetch_assoc($stmt_estados)) {
                    $estados[] = $row;
                }

                $titulo_form = "Modificar Usuario";
                $action_form = "actualizarUsuario";
                require 'Views/form.php';
            } else {
                header('Location: index.php?action=listarUsuarios&error=usuario_no_encontrado');
                exit;
            }
        } else {
            header('Location: index.php?action=listarUsuarios&error=id_no_valido');
            exit;
        }
    }

    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_usuario'])) {
            $usuarioModel = new Usuario();
            $actualizado = $usuarioModel->actualizarUsuario(
                $_POST['id_usuario'],
                $_POST['nombre'],
                $_POST['telefono'],
                $_POST['correo'],
                $_POST['id_rol'],
                $_POST['id_estado']
            );

            if ($actualizado) {
                header('Location: index.php?action=listarUsuarios');
            } else {
                error_log("Fallo al actualizar usuario con ID: " . $_POST['id_usuario']);
                header('Location: index.php?action=editarUsuario&id=' . $_POST['id_usuario'] . '&error=update_failed');
            }
            exit;
        } else {
            header('Location: index.php?action=listarUsuarios&error=acceso_invalido_actualizar');
            exit;
        }
    }

    public function cambiarEstado() {
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $id_usuario = $_GET['id'];
            $usuarioModel = new Usuario();
            $usuario = $usuarioModel->obtenerUsuarioPorId($id_usuario);

            if ($usuario) {
                $conexion_obj = new Conexion(); 
                $oci_conn = $conexion_obj->getConexion();

                $estados = [];
                $sql = "SELECT ID_ESTADO, NOMBRE_ESTADO FROM FIDE_ESTADOS_TB ORDER BY NOMBRE_ESTADO ASC";
                $stmt = oci_parse($oci_conn, $sql);
                oci_execute($stmt);
                while ($row = oci_fetch_assoc($stmt)) {
                    $estados[] = $row;
                }

                require 'Views/cambiar_estado.php';
            } else {
                header('Location: index.php?action=listarUsuarios&error=usuario_no_encontrado');
            }
        }
    }

    public function guardarEstado() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_usuario'], $_POST['id_estado'])) {
            $usuarioModel = new Usuario();
            $usuarioModel->actualizarEstadoUsuario($_POST['id_usuario'], $_POST['id_estado']);
        }
        header('Location: index.php?action=listarUsuarios');
        exit;
    }
}

