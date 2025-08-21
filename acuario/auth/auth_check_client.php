<?php
// ACUARIO/auth/auth_check_client.php
session_start();
require_once __DIR__ . '/../config.php';

// Debe haber sesión
if (empty($_SESSION['user'])) {
  header('Location: ' . BASE_URL . '/auth/login.php');
  exit;
}

// Sólo clientes (ID_ROL = 16)
if ((int)$_SESSION['user']['rol'] !== 16) {
  header('Location: ' . BASE_URL . '/auth/login.php?err=1');
  exit;
}
