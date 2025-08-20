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
</head>

<body>
    <header class="topbar">
        <h1>Acuario · <?= h($title ?? '') ?></h1>
        <nav>
            <a class="btn" href="/index.php">Inicio</a>
            <a class="btn primary" href="/index.php?action=create">+ Agregar</a>
        </nav>
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