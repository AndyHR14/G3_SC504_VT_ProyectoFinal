<?php

session_start();
require_once __DIR__ . '/../config.php'; 

// Si ya hay sesión y es Admin, manda al dashboard
if (!empty($_SESSION['user']) && (int)$_SESSION['user']['rol'] === 1) {
  header('Location: ' . BASE_URL . '/index.php');
  exit;
}

$err = $_GET['err'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Login | Acuario La Casa del Pez</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/styles.css">
  <style>
    body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu; background:#f4fbff;}
    .login-wrap{max-width:420px;margin:8vh auto;padding:28px;background:#fff;border-radius:16px;
                box-shadow:0 10px 30px rgba(0,0,0,.08);}
    h1{margin:0 0 18px; font-size:22px; text-align:center}
    label{display:block;margin:.8rem 0 .4rem;font-weight:600}
    input{width:100%;padding:12px 14px;border:1px solid #dbe7f3;border-radius:10px;outline:none}
    button{margin-top:14px;width:100%;padding:12px 14px;border:0;border-radius:12px;cursor:pointer}
    .primary{background:#0ea5e9;color:#fff;font-weight:700}
    .alert{background:#fee2e2;color:#991b1b;padding:10px 12px;border-radius:10px;margin-bottom:10px}
  </style>
</head>
<body>
  <div class="login-wrap">
    <h1>Acuario La Casa del Pez</h1>
    <?php if ($err === '1'): ?>
      <div class="alert">Acceso denegado. Verifica tu correo o permisos.</div>
    <?php endif; ?>

    <form method="post" action="<?= BASE_URL ?>/auth/login_process.php" autocomplete="off">
      <label>Correo</label>
      <input type="email" name="correo" required placeholder="admin@acuario.com">
      <button class="primary" type="submit">Ingresar</button>
    </form>
  </div>
</body>
</html>
