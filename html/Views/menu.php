<?php
// Asegurarse de que la variable $p esté disponible
$p = $_GET['p'] ?? 'inicio';
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo $_SERVER['PHP_SELF']; ?>?p=inicio">La Casa del Pez</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link <?php echo ($p == 'inicio') ? 'active' : ''; ?>" href="<?php echo $_SERVER['PHP_SELF']; ?>?p=inicio">Inicio</a></li>
                <li class="nav-item"><a class="nav-link <?php echo ($p == 'animales') ? 'active' : ''; ?>" href="<?php echo $_SERVER['PHP_SELF']; ?>?p=animales">Animales</a></li>
                <li class="nav-item"><a class="nav-link <?php echo ($p == 'alimentacion') ? 'active' : ''; ?>" href="<?php echo $_SERVER['PHP_SELF']; ?>?p=alimentacion">Alimentación</a></li>
                <li class="nav-item"><a class="nav-link <?php echo ($p == 'colaboradores') ? 'active' : ''; ?>" href="<?php echo $_SERVER['PHP_SELF']; ?>?p=colaboradores">Colaboradores</a></li>
                <li class="nav-item"><a class="nav-link <?php echo ($p == 'horarios') ? 'active' : ''; ?>" href="<?php echo $_SERVER['PHP_SELF']; ?>?p=horarios">Horarios</a></li>
                <li class="nav-item"><a class="nav-link <?php echo ($p == 'inventario') ? 'active' : ''; ?>" href="<?php echo $_SERVER['PHP_SELF']; ?>?p=inventario">Inventario</a></li>
            </ul>
        </div>
    </div>
</nav>
