<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($titulo_form) ? $titulo_form : 'Formulario de Usuario' ?></title>
    <link rel="stylesheet" href="public/estilos.css">
</head>
<body>
    <div class="container">
        <h1><?= isset($titulo_form) ? $titulo_form : 'Formulario de Usuario' ?></h1>
        
        <form method="POST" action="index.php?action=<?= isset($action_form) ? $action_form : 'guardarUsuario' ?>">

            <?php if (isset($usuario['ID_USUARIO'])): ?>
                <input type="hidden" name="id_usuario" value="<?= htmlspecialchars($usuario['ID_USUARIO']) ?>">
            <?php endif; ?>

            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required
                       value="<?= isset($usuario['NOMBRE']) ? htmlspecialchars($usuario['NOMBRE']) : '' ?>">
            </div>

            <div class="form-group">
                <label for="telefono">Teléfono:</label>
                <input type="text" id="telefono" name="telefono" required
                       value="<?= isset($usuario['TELEFONO']) ? htmlspecialchars($usuario['TELEFONO']) : '' ?>">
            </div>

            <div class="form-group">
                <label for="correo">Correo:</label>
                <input type="email" id="correo" name="correo" required
                       value="<?= isset($usuario['CORREO']) ? htmlspecialchars($usuario['CORREO']) : '' ?>">
            </div>

<div class="form-group">
    <label for="estado">Estado:</label>
    <select id="estado" name="id_estado" required>
        <option value="">Seleccione un estado</option>
        <?php
        if (isset($estados) && is_array($estados)) {
            foreach ($estados as $est) {
                $selected = (isset($usuario['ID_ESTADO']) && $usuario['ID_ESTADO'] == $est['ID_ESTADO']) ? 'selected' : '';
                echo '<option value="' . htmlspecialchars($est['ID_ESTADO']) . '" ' . $selected . '>' . htmlspecialchars($est['NOMBRE_ESTADO']) . '</option>';
            }
        } else {
            echo '<option value="">Error: No se pudieron cargar los estados</option>';
        }
        ?>
    </select>
</div>


            

            <button type="submit" class="btn btn-primary">Guardar</button>
            <a href="usuario_registrados.php?action=listarUsuarios" class="btn btn-link">Volver</a>
        </form>
    </div>
</body>
</html>
