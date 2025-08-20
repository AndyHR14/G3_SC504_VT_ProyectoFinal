<?php

//require_once 'conexion.php'; 
require_once __DIR__ . '/conexion.php';

class Usuario {
    private $conn_oracle; 

    public function __construct() {
        $conexion_obj = new Conexion();
        $this->conn_oracle = $conexion_obj->getConexion();
    }

    public function obtenerUsuarios() {
        try {
            $sql = "
                SELECT
                    u.ID_USUARIO,
                    u.NOMBRE,
                    TO_CHAR(u.FECHA_REGISTRO, 'DD/MM/YY') AS FECHA_REGISTRO,
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
        } catch (Exception $e) {
            error_log("Error al obtener usuarios (OCI): " . $e->getMessage());
            return [];
        }
    }

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
                    ID_USUARIO = :id_usuario
            ";
            $stmt = oci_parse($this->conn_oracle, $sql);
            oci_bind_by_name($stmt, ':id_usuario', $id_usuario); 
            oci_execute($stmt);

            $usuario = oci_fetch_assoc($stmt);
            oci_free_statement($stmt);
            return $usuario;
        } catch (Exception $e) {
            error_log("Error al obtener usuario por ID (OCI): " . $e->getMessage());
            return null;
        }
    }

    public function insertarUsuario($nombre, $telefono, $correo, $id_rol, $id_estado) {
        try {
            $sql = "
                INSERT INTO FIDE_USUARIO_TB (NOMBRE, FECHA_REGISTRO, TELEFONO, CORREO, ID_ROL, ID_ESTADO)
                VALUES (:nombre, SYSDATE, :telefono, :correo, :id_rol, :id_estado)
            ";
            $stmt = oci_parse($this->conn_oracle, $sql);

            oci_bind_by_name($stmt, ':nombre', $nombre);
            oci_bind_by_name($stmt, ':telefono', $telefono);
            oci_bind_by_name($stmt, ':correo', $correo);
            oci_bind_by_name($stmt, ':id_rol', $id_rol);
            oci_bind_by_name($stmt, ':id_estado', $id_estado);

            $result = oci_execute($stmt, OCI_COMMIT_ON_SUCCESS); 
            oci_free_statement($stmt);
            return $result; 
        } catch (Exception $e) {
            error_log("Error al insertar usuario (OCI): " . $e->getMessage());
            return false;
        }
    }

    public function actualizarUsuario($id_usuario, $nombre, $telefono, $correo, $id_rol, $id_estado) {
        try {
            $sql = "
                UPDATE FIDE_USUARIO_TB
                SET
                    NOMBRE = :nombre,
                    TELEFONO = :telefono,
                    CORREO = :correo,
                    ID_ROL = :id_rol,
                    ID_ESTADO = :id_estado
                WHERE
                    ID_USUARIO = :id_usuario
            ";
            $stmt = oci_parse($this->conn_oracle, $sql);

            oci_bind_by_name($stmt, ':nombre', $nombre);
            oci_bind_by_name($stmt, ':telefono', $telefono);
            oci_bind_by_name($stmt, ':correo', $correo);
            oci_bind_by_name($stmt, ':id_rol', $id_rol);
            oci_bind_by_name($stmt, ':id_estado', $id_estado);
            oci_bind_by_name($stmt, ':id_usuario', $id_usuario);

            $result = oci_execute($stmt, OCI_COMMIT_ON_SUCCESS);
            oci_free_statement($stmt);
            return $result;
        } catch (Exception $e) {
            error_log("Error al actualizar usuario (OCI): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza solo el estado del usuario (usado para eliminar lógico).
     */
    public function actualizarEstadoUsuario($id_usuario, $id_estado) {
        try {
            $sql = "
                UPDATE FIDE_USUARIO_TB
                SET ID_ESTADO = :id_estado
                WHERE ID_USUARIO = :id_usuario
            ";
            $stmt = oci_parse($this->conn_oracle, $sql);
            oci_bind_by_name($stmt, ':id_estado', $id_estado);
            oci_bind_by_name($stmt, ':id_usuario', $id_usuario);

            return oci_execute($stmt, OCI_COMMIT_ON_SUCCESS);
        } catch (Exception $e) {
            error_log("Error al actualizar estado del usuario: " . $e->getMessage());
            return false;
        }
    }
}


