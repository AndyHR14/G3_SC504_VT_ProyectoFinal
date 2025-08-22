<?php
// ACUARIO/auth/login_process.php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Models/conexion.php';

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ' . BASE_URL . '/auth/login.php?err=1');
  exit;
}

// Validar input
$correo = trim($_POST['correo'] ?? '');
if ($correo === '') {
  header('Location: ' . BASE_URL . '/auth/login.php?err=1');
  exit;
}

try {
  // Conexión
  $conexionObj = new Conexion();
  $conn = $conexionObj->getConexion();
  if (!$conn) {
    throw new Exception('Sin conexión a Oracle');
  }

  // Verificar existencia (activo) y rol permitido (1=Admin, 16=Cliente)
  $sqlCount = "
    SELECT COUNT(*) AS CNT
    FROM FIDE_USUARIO_TB u
    WHERE UPPER(u.CORREO) = UPPER(:correo)
      AND u.ID_ESTADO = 1
      AND u.ID_ROL IN (1, 16)
  ";
  $stCount = oci_parse($conn, $sqlCount);
  oci_bind_by_name($stCount, ':correo', $correo);
  oci_execute($stCount);
  $rowCount = oci_fetch_assoc($stCount);
  oci_free_statement($stCount);

  $cnt = (int)($rowCount['CNT'] ?? 0);
  if ($cnt !== 1) {
    header('Location: ' . BASE_URL . '/auth/login.php?err=1');
    exit;
  }

  // Traer datos del usuario
  $sqlUser = "
    SELECT u.ID_USUARIO, u.NOMBRE, u.CORREO, u.ID_ESTADO, u.ID_ROL
    FROM FIDE_USUARIO_TB u
    WHERE UPPER(u.CORREO) = UPPER(:correo)
      AND u.ID_ESTADO = 1
      AND u.ID_ROL IN (1, 16)
    FETCH FIRST 1 ROWS ONLY
  ";
  $stUser = oci_parse($conn, $sqlUser);
  oci_bind_by_name($stUser, ':correo', $correo);
  oci_execute($stUser);
  $user = oci_fetch_assoc($stUser);
  oci_free_statement($stUser);

  if (!$user) {
    header('Location: ' . BASE_URL . '/auth/login.php?err=1');
    exit;
  }

  // Crear sesión y redirigir según rol
  session_regenerate_id(true);
  $_SESSION['user'] = [
    'id'     => (int)$user['ID_USUARIO'],
    'nombre' => $user['NOMBRE'],
    'correo' => $user['CORREO'],
    'rol'    => (int)$user['ID_ROL'],
  ];

  if ((int)$user['ID_ROL'] === 1) {
    // Admin
    header('Location: ' . BASE_URL . '/index.php');
  } else {
    // Cliente
    header('Location: ' . BASE_URL . '/cliente/index.php');
  }
  exit;

} catch (Throwable $e) {
  header('Location: ' . BASE_URL . '/auth/login.php?err=1');
  exit;
}
