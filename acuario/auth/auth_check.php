<?php
session_start();
require_once __DIR__ . '/../config.php';
if (empty($_SESSION['user'])) {
  header('Location: ' . BASE_URL . '/auth/login.php'); exit;
}
if ((int)$_SESSION['user']['rol'] !== 1) {
  header('Location: ' . BASE_URL . '/auth/login.php?err=1'); exit;
}
