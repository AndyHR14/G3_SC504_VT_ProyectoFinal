<?php

include 'includes/header.php';
include 'db.php';

try {
    $stmt = $pdo->prepare("SELECT * FROM horarios");
    $stmt->execute();
    $horarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<div class="container py-5">
    <h1 class="mb-4 text-center">Gestión de Horarios</h1>
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="table-primary">
                <tr>
                    <th>ID Horario</th>
                    <th>Actividad</th>
                    <th>Hora de Inicio</th>
                    <th>Hora de Fin</th>
                    <th>Día</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($horarios as $horario): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($horario['id']); ?></td>
                        <td><?php echo htmlspecialchars($horario['actividad']); ?></td>
                        <td><?php echo htmlspecialchars($horario['hora_inicio']); ?></td>
                        <td><?php echo htmlspecialchars($horario['hora_fin']); ?></td>
                        <td><?php echo htmlspecialchars($horario['dia']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
include 'includes/footer.php';
?>