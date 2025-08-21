<?php
// views/layout.php
function h($s)
{
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title><?= h($title ?? 'Acuario') ?></title>
    <link rel="stylesheet" href="/public/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>


<body>
    <header class="topbar">
  <div class="container">
    <h1>Acuario · <?= h($title ?? '') ?></h1>
    <nav>
      <a class="btn outline" href="<?= BASE_PATH ?>/index.php?mod=dashboard">Dashboard</a>
      <a class="btn" href="<?= BASE_PATH ?>/index.php?mod=home">Módulos</a>
    </nav>
  </div>
</header>


    <main class="container">
        <?php
        // Renderiza la vista solicitada
        $viewFile = __DIR__ . '/' . $view . '.php';
        if (is_file($viewFile)) include $viewFile;
        else echo "<p>Vista no encontrada.</p>";
        ?>
    </main>
</body>

</html>