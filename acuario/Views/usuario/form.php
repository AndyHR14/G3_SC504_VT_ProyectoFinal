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
                <label for="rol">Rol:</label>
                <select id="rol" name="id_rol" required>
                    <option value="">Seleccione un rol</option>
                    <?php
                    // Recorre el array $roles obtenido del controlador
                    if (isset($roles) && is_array($roles)) {
                        foreach ($roles as $rol) {
                            // Marca la opción como seleccionada si coincide con el rol del usuario (en modo edición)
                            $selected = (isset($usuario['ID_ROL']) && $usuario['ID_ROL'] == $rol['ID_ROL']) ? 'selected' : '';
                            echo '<option value="' . htmlspecialchars($rol['ID_ROL']) . '" ' . $selected . '>' . htmlspecialchars($rol['NOMBRE_ROL']) . '</option>';
                        }
                    } else {
                        echo '<option value="">Error: No se pudieron cargar los roles</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="estado">Estado:</label>
                <select id="estado" name="id_estado" required>
                    <option value="">Seleccione un estado</option>
                    <?php
                    // Recorre el array $estados obtenido del controlador
                    if (isset($estados) && is_array($estados)) {
                        foreach ($estados as $estado) {
                            // Marca la opción como seleccionada si coincide con el estado del usuario (en modo edición)
                            $selected = (isset($usuario['ID_ESTADO']) && $usuario['ID_ESTADO'] == $estado['ID_ESTADO']) ? 'selected' : '';
                            echo '<option value="' . htmlspecialchars($estado['ID_ESTADO']) . '" ' . $selected . '>' . htmlspecialchars($estado['NOMBRE_ESTADO']) . '</option>';
                        }
                    } else {
                        echo '<option value="">Error: No se pudieron cargar los estados</option>';
                    }
                    ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Guardar</button>
            <a href="index.php?action=listarUsuarios" class="btn btn-link">Volver</a>
        </form>
    </div>
</body>
</html>
