<?php
class Conexion {
    private $conexion;

    public function __construct() {
        $usuario = "Proyecto_Final";
        $contrasena = "123";
        $host = "localhost/XE"; 
        
       
        $charset = "AL32UTF8"; 

        
        $this->conexion = oci_connect($usuario, $contrasena, $host, $charset); 

        if (!$this->conexion) {
            $e = oci_error();
            die("Error de conexión a la base de datos Oracle: " . $e['message']);
        }
    }

    public function getConexion() {
        return $this->conexion;
    }
}
?>