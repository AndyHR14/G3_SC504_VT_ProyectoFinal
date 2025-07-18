<?php
// Controllers/usuarioController.php

// Incluye los modelos necesarios para interactuar con la base de datos
require_once 'Models/usuario.php';
require_once 'Models/conexion.php'; // Necesario para obtener roles y estados directamente en el controlador

class UsuarioController {

    /**
     * Muestra la lista de usuarios.
     * Obtiene los usuarios del modelo y pasa los datos a la vista.
     */
    public function index() {
        $usuarioModel = new Usuario();
        $usuarios = $usuarioModel->obtenerUsuarios(); // Llama al método del modelo para obtener los usuarios
        
        // --- CÓDIGO DE DEPURACIÓN (eliminar o comentar en producción) ---
        // Puedes descomentar las siguientes líneas temporalmente para ver si $usuarios trae datos
        /*
        echo "<pre>";
        echo "Contenido de \$usuarios en UsuarioController->index():\n";
        print_r($usuarios);
        echo "</pre>";
        */
        // --- FIN CÓDIGO DE DEPURACIÓN ---

        // Carga la vista para mostrar la lista de usuarios
        require 'Views/usuario/index.php';
    }

    /**
     * Muestra el formulario para crear un nuevo usuario.
     * Obtiene los roles y estados disponibles de la base de datos para los selectbox.
     */
    public function crear() {
        // Se crea una nueva conexión para obtener roles y estados,
        // ya que el modelo Usuario se enfoca solo en la tabla de usuarios.
        $conexion_obj = new Conexion(); 
        $oci_conn = $conexion_obj->getConexion(); // Obtiene el recurso de conexión OCI

        $roles = [];
        try {
            $sql_roles = "SELECT ID_ROL, NOMBRE_ROL FROM FIDE_ROL_TB ORDER BY NOMBRE_ROL ASC";
            $stmt_roles = oci_parse($oci_conn, $sql_roles);
            oci_execute($stmt_roles);
            
            while ($row = oci_fetch_assoc($stmt_roles)) {
                $roles[] = $row;
            }
            oci_free_statement($stmt_roles); // Liberar el statement
            
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
            oci_free_statement($stmt_estados); // Liberar el statement

        } catch (Exception $e) {
            error_log("Error al cargar estados en crear(): " . $e->getMessage());
        }
        
        $usuario = null; // Para indicar que es un nuevo usuario (no edición)
        $titulo_form = "Registrar Nuevo Usuario";
        $action_form = "guardarUsuario"; // Acción a la que enviará el formulario
        
        require 'Views/usuario/form.php'; // Carga la vista del formulario
    }

    /**
     * Procesa la solicitud POST para guardar un nuevo usuario.
     */
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
                // Manejar error de inserción, quizás mostrar un mensaje al usuario
                error_log("Fallo al insertar usuario.");
                header('Location: index.php?action=nuevoUsuario&error=insert_failed');
            }
            exit; // Siempre llama a exit después de un header Location
        } else {
            header('Location: index.php?action=nuevoUsuario'); // Redirige si no es POST
            exit;
        }
    }

    /**
     * Muestra el formulario para editar un usuario existente.
     * Requiere un ID de usuario en la URL.
     */
    public function editar() {
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $id_usuario = $_GET['id'];
            $usuarioModel = new Usuario();
            $usuario = $usuarioModel->obtenerUsuarioPorId($id_usuario); // Obtiene los datos del usuario

            if ($usuario) {
                // Similar a crear(), obtenemos roles y estados para los selectbox
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
                    error_log("Error al cargar roles en editar(): " . $e->getMessage());
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
                    error_log("Error al cargar estados en editar(): " . $e->getMessage());
                }

                $titulo_form = "Modificar Usuario";
                $action_form = "actualizarUsuario"; // Acción a la que enviará el formulario de edición
                
                require 'Views/usuario/form.php'; // Carga la vista del formulario
            } else {
                header('Location: index.php?action=listarUsuarios&error=usuario_no_encontrado');
                exit;
            }
        } else {
            header('Location: index.php?action=listarUsuarios&error=id_no_valido');
            exit;
        }
    }

    /**
     * Procesa la solicitud POST para actualizar un usuario existente.
     */
    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_usuario'])) {
            $usuarioModel = new Usuario();
            $actualizado = $usuarioModel->actualizarUsuario(
                $_POST['id_usuario'] ?? null,
                $_POST['nombre'] ?? '',
                $_POST['telefono'] ?? '',
                $_POST['correo'] ?? '',
                $_POST['id_rol'] ?? null,
                $_POST['id_estado'] ?? null
            );

            if ($actualizado) {
                header('Location: index.php?action=listarUsuarios');
            } else {
                // Manejar error de actualización
                error_log("Fallo al actualizar usuario con ID: " . ($_POST['id_usuario'] ?? 'N/A'));
                header('Location: index.php?action=editarUsuario&id=' . ($_POST['id_usuario'] ?? '') . '&error=update_failed');
            }
            exit;
        } else {
            header('Location: index.php?action=listarUsuarios&error=acceso_invalido_actualizar');
            exit;
        }
    }

    
}
?>