<?php
require_once '../models/Conexion.php';

$conexion = new Conexion();
$conn = $conexion->getConexion();

$sql = "SELECT * FROM VISTA_INVENTARIO_COMPLETO";
$stid = oci_parse($conn, $sql);
oci_execute($stid);
?>
