<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Usuario</title>
    <link rel="stylesheet" href="public/estilos.css">
</head>
<body>
    <div class="container">
        <h1>Eliminar Usuario</h1>

        <form method="POST" action="usuario_registrados.php?action=guardarEstadoUsuario">
            <input type="hidden" name="id_usuario" value="<?= htmlspecialchars($usuario['ID_USUARIO']) ?>">

            <div class="form-group">
                <label for="estado">Seleccione nuevo estado:</label>
                <select id="estado" name="id_estado" required>
                    <option value="">-- Seleccione un estado --</option>
                    <?php foreach ($estados as $estado): ?>
                        <?php $selected = ($usuario['ID_ESTADO'] == $estado['ID_ESTADO']) ? 'selected' : ''; ?>
                        <option value="<?= htmlspecialchars($estado['ID_ESTADO']) ?>" <?= $selected ?>>
                            <?= htmlspecialchars($estado['NOMBRE_ESTADO']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-danger">Guardar</button>
            <a href="usuario_registrados.php?action=listarUsuarios" class="btn btn-link">Cancelar</a>
        </form>
    </div>
</body>
</html>
