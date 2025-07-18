<?php

require_once 'conexion.php'; 

class Usuario {
    private $conn_oracle; 

    public function __construct() {
        // Al crear una instancia de Usuario, automáticamente se conecta a la base de datos
        $conexion_obj = new Conexion();
        $this->conn_oracle = $conexion_obj->getConexion(); // Obtiene el recurso de conexión OCI
    }

    /**
     * Obtiene todos los usuarios de la base de datos,
     * incluyendo el nombre del rol y el estado asociados, usando OCI8.
     * @return array Un array de arrays asociativos con los datos de los usuarios.
     */
    public function obtenerUsuarios() {
        try {
            $sql = "
                SELECT
                    u.ID_USUARIO,
                    u.NOMBRE,
                    TO_CHAR(u.FECHA_REGISTRO, 'DD/MM/YY') AS FECHA_REGISTRO, -- Formatear fecha para visualización
                    u.TELEFONO,
                    u.CORREO,
                    r.NOMBRE_ROL,
                    e.NOMBRE_ESTADO
                FROM
                    FIDE_USUARIO_TB u
                LEFT JOIN
                    FIDE_ROL_TB r ON u.ID_ROL = r.ID_ROL
                LEFT JOIN
                    FIDE_ESTADOS_TB e ON u.ID_ESTADO = e.ID_ESTADO
                ORDER BY
                    u.NOMBRE ASC
            ";
            $stmt = oci_parse($this->conn_oracle, $sql); 
            oci_execute($stmt); 

            $usuarios = [];
            while ($row = oci_fetch_assoc($stmt)) { 
                $usuarios[] = $row;
            }
            oci_free_statement($stmt); 
            return $usuarios;
        } catch (Exception $e) { // OCI8 no lanza PDOException, usa Exception general o maneja oci_error()
            error_log("Error al obtener usuarios (OCI): " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene los detalles de un solo usuario por su ID, usando OCI8.
     * @param int $id_usuario El ID del usuario a buscar.
     * @return array|null Un array asociativo con los datos del usuario, o null si no se encuentra.
     */
    public function obtenerUsuarioPorId($id_usuario) {
        try {
            $sql = "
                SELECT
                    ID_USUARIO,
                    NOMBRE,
                    TELEFONO,
                    CORREO,
                    ID_ROL,
                    ID_ESTADO
                FROM
                    FIDE_USUARIO_TB
                WHERE
                    ID_USUARIO = :id_usuario -- Usar bind variables con nombre para OCI8
            ";
            $stmt = oci_parse($this->conn_oracle, $sql);
            oci_bind_by_name($stmt, ':id_usuario', $id_usuario); 
            oci_execute($stmt);

            $usuario = oci_fetch_assoc($stmt); // Devuelve un solo registro
            oci_free_statement($stmt);
            return $usuario;
        } catch (Exception $e) {
            error_log("Error al obtener usuario por ID (OCI): " . $e->getMessage());
            return null;
        }
    }

    /**
     * Inserta un nuevo usuario en la base de datos, usando OCI8.
     * @param string $nombre
     * @param string $telefono
     * @param string $correo
     * @param int $id_rol
     * @param int $id_estado
     * @return bool True si la inserción fue exitosa, false en caso contrario.
     */
    public function insertarUsuario($id_usuario, $nombre, $telefono, $correo, $id_rol, $id_estado) {
    try {
        // Llamada al procedimiento almacenado FIDE_INSERTAR_USUARIO_SP
        $sql = "BEGIN FIDE_PROYECTO_FINAL_PCK.FIDE_INSERTAR_USUARIO_SP(:id_usuario, :nombre, SYSDATE, :telefono, :correo, :id_estado, :id_rol); END;";
        
        $stmt = oci_parse($this->conn_oracle, $sql);
        
        // Bind de los parámetros de entrada
        oci_bind_by_name($stmt, ':id_usuario', $id_usuario);
        oci_bind_by_name($stmt, ':nombre', $nombre);
        oci_bind_by_name($stmt, ':telefono', $telefono);
        oci_bind_by_name($stmt, ':correo', $correo);
        oci_bind_by_name($stmt, ':id_estado', $id_estado);
        oci_bind_by_name($stmt, ':id_rol', $id_rol);
        
        // Ejecutar el procedimiento
        $success = oci_execute($stmt);
        
        oci_free_statement($stmt);
        
        return $success;
        
    } catch (Exception $e) {
        error_log("Error al insertar usuario mediante el procedimiento almacenado: " . $e->getMessage());
        return false;
    }
    }


    /**
     * Actualiza un usuario existente en la base de datos, usando OCI8.
     * @param int $id_usuario El ID del usuario a actualizar.
     * @param string $nombre
     * @param string $telefono
     * @param string $correo
     * @param int $id_rol
     * @param int $id_estado
     * @return bool True si la actualización fue exitosa, false en caso contrario.
     */
    public function actualizarUsuario($id_usuario, $nombre, $telefono, $correo, $id_rol, $id_estado) {
    try {
        // Llamada al procedimiento almacenado FIDE_MODIFICAR_USUARIO_SP
        $sql = "BEGIN FIDE_PROYECTO_FINAL_PCK.FIDE_MODIFICAR_USUARIO_SP(:id_usuario, :nombre, SYSDATE, :telefono, :correo, :id_estado, :id_rol); END;";
        
        $stmt = oci_parse($this->conn_oracle, $sql);
        
        // Bind de los parámetros de entrada
        oci_bind_by_name($stmt, ':id_usuario', $id_usuario);
        oci_bind_by_name($stmt, ':nombre', $nombre);
        oci_bind_by_name($stmt, ':telefono', $telefono);
        oci_bind_by_name($stmt, ':correo', $correo);
        oci_bind_by_name($stmt, ':id_estado', $id_estado);
        oci_bind_by_name($stmt, ':id_rol', $id_rol);
        
        // Ejecutar el procedimiento
        $result = oci_execute($stmt);
        
        oci_free_statement($stmt);
        return $result;
        
    } catch (Exception $e) {
        error_log("Error al actualizar usuario mediante el procedimiento almacenado: " . $e->getMessage());
        return false;
    }
}

    public function eliminarUsuario($id_usuario) {
    try {
        // Llamada al procedimiento almacenado FIDE_ELIMINAR_USUARIO_SP
        $sql = "BEGIN FIDE_PROYECTO_FINAL_PCK.FIDE_ELIMINAR_USUARIO_SP(:id_usuario); END;";
        
        $stmt = oci_parse($this->conn_oracle, $sql);
        
        // Bind del parámetro de entrada
        oci_bind_by_name($stmt, ':id_usuario', $id_usuario);
        
        // Ejecutar el procedimiento
        $result = oci_execute($stmt);
        
        oci_free_statement($stmt);
        return $result;
        
    } catch (Exception $e) {
        error_log("Error al eliminar usuario mediante el procedimiento almacenado: " . $e->getMessage());
        return false;
    }
}
}