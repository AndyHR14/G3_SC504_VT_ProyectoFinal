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

?>

<div class="container py-5">
    <h1 class="mb-4 text-center">Gestión de Alimentación</h1>
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="table-primary">
                <tr>
                    <th>ID Alimentación</th>
                    <th>Nombre Animal</th>
                    <th>Tipo de Comida</th>
                    <th>Frecuencia</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $pdo->query("SELECT * FROM alimentacion"); 
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['nombre_animal']}</td>
                            <td>{$row['tipo_comida']}</td>
                            <td>{$row['frecuencia']}</td>
                            <td>{$row['estado']}</td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php
include 'includes/footer.php';
?>