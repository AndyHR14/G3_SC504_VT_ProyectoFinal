<?php

require_once 'db.php';

$query = "SELECT * FROM colaboradores"; 
$stmt = $pdo->prepare($query);
$stmt->execute();
$colaboradores = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<div class="container py-5">
    <h1 class="mb-4 text-center">Gestión de Colaboradores</h1>
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="table-primary">
                <tr>
                    <th>ID Colaborador</th>
                    <th>Nombre</th>
                    <th>Rol</th>
                    <th>Contacto</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($colaboradores as $colaborador): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($colaborador['id']); ?></td>
                        <td><?php echo htmlspecialchars($colaborador['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($colaborador['rol']); ?></td>
                        <td><?php echo htmlspecialchars($colaborador['contacto']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
include 'includes/footer.php';
?>