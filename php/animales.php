<?php
$host = 'localhost'; 
$dbname = 'Proyecto_Final'; 
$username = 'Proyecto_Final'; 
$password = '123'; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}

include 'db.php';
include 'includes/header.php';

$query = "SELECT * FROM animales"; 
$stmt = $pdo->prepare($query);
$stmt->execute();
$animales = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container py-5">
    <h1 class="mb-4 text-center">Gestión de Animales</h1>
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="table-primary">
                <tr>
                    <th>ID Animal</th>
                    <th>Nombre</th>
                    <th>Especie</th>
                    <th>Tipo</th>
                    <th>Hábitat</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($animales as $animal): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($animal['id']); ?></td>
                        <td><?php echo htmlspecialchars($animal['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($animal['especie']); ?></td>
                        <td><?php echo htmlspecialchars($animal['tipo']); ?></td>
                        <td><?php echo htmlspecialchars($animal['habitat']); ?></td>
                        <td><?php echo htmlspecialchars($animal['estado']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <svg viewBox="0 0 1440 320" xmlns="http://www.w3.org/2000/svg"><defs><linearGradient id="grad1" x1="0" y1="1" x2="1" y2="0"><stop offset="0%" stop-color="#e0f7fa"/><stop offset="100%" stop-color="#0288d1"/></linearGradient></defs><path fill="url(#grad1)" d="M0,160L60,170.7C120,181,240,203,360,197.3C480,192,600,160,720,144C840,128,960,128,1080,144C1200,160,1320,192,1380,208L1440,224L1440,0L1380,0C1320,0,1200,0,1080,0C960,0,840,0,720,0C600,0,480,0,360,0C240,0,120,0,60,0L0,0Z" /></svg>
    </div>
</div>

<?php include 'includes/footer.php'; ?>