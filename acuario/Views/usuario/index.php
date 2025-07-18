<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"> <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios Registrados</title>
    <style>
        /* Estilos básicos en línea para que la tabla se vea organizada de inmediato */
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
            color: #333;
        }
        .container {
            background-color: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 1000px;
            margin: 0 auto;
        }
        h1 {
            color: #007bff;
            text-align: center;
            margin-bottom: 25px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #28a745; 
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .btn:hover {
            background-color: #218838;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.05);
        }
        th, td {
            border: 1px solid #e0e0e0;
            padding: 12px 15px;
            text-align: left;
            vertical-align: middle;
        }
        th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 0.9em;
        }
        tbody tr:nth-child(even) {
            background-color: #f8f8f8;
        }
        tbody tr:hover {
            background-color: #e9ecef;
        }
        .actions a {
            margin-right: 15px;
            text-decoration: none;
            color: #007bff;
            font-weight: 600;
            transition: color 0.2s ease;
        }
        .actions a:hover {
            color: #0056b3;
            text-decoration: underline;
        }
        .message {
            padding: 15px;
            background-color: #e9ecef;
            border: 1px solid #ced4da;
            border-radius: 5px;
            text-align: center;
            color: #495057;
            font-size: 1.1em;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Usuarios Registrados</h1>
        <a href="index.php?action=nuevoUsuario" class="btn">Registrar nuevo usuario</a>

        <?php if (!empty($usuarios)): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th> <th>Nombre</th>
                        <th>Fecha Registro</th>
                        <th>Teléfono</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $u): ?>
                        <tr>
                            <td><?= htmlspecialchars($u['ID_USUARIO'] ?? '') ?></td>
                            <td><?= htmlspecialchars($u['NOMBRE'] ?? '') ?></td>
                            <td><?= htmlspecialchars($u['FECHA_REGISTRO'] ?? '') ?></td>
                            <td><?= htmlspecialchars($u['TELEFONO'] ?? '') ?></td>
                            <td><?= htmlspecialchars($u['CORREO'] ?? '') ?></td>
                            <td><?= htmlspecialchars($u['NOMBRE_ROL'] ?? '') ?></td>
                            <td><?= htmlspecialchars($u['NOMBRE_ESTADO'] ?? '') ?></td>
                            <td class="actions">
                                <a href="index.php?action=editarUsuario&id=<?= htmlspecialchars($u['ID_USUARIO'] ?? '') ?>">Modificar</a>
                                </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="message">No hay usuarios registrados en la base de datos o hubo un problema al cargarlos.</p>
        <?php endif; ?>
    </div>
</body>
</html>