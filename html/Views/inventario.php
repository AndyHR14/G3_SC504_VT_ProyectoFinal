<?php include 'menu.php'; ?>
<div class="container py-5">
    <h1 class="mb-4 text-center">Gestión de Inventario</h1>
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-primary">
                <tr>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th>Cantidad</th>
                    <th>Fecha de Ingreso</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php
                include_once __DIR__ . '/../Controllers/inventarioController.php';

                while ($row = oci_fetch_assoc($stid)) {
                    echo "<tr>";
                    echo "<td>{$row['NOMBRE_PRODUCTO']}</td>";
                    echo "<td>{$row['NOMBRE_CATEGORIA']}</td>";
                    echo "<td>{$row['CANTIDAD']}</td>";
                    echo "<td>{$row['FECHA_INGRESO']}</td>";
                    echo "<td>{$row['ESTADO']}</td>";
                    echo "</tr>";
                }

                oci_free_statement($stid);
                oci_close($conn);
                ?>
            </tbody>
        </table>

        <svg viewBox="0 0 1440 320" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="grad1" x1="0" y1="1" x2="1" y2="0">
                    <stop offset="0%" stop-color="#e0f7fa"/>
                    <stop offset="100%" stop-color="#0288d1"/>
                </linearGradient>
            </defs>
            <path fill="url(#grad1)" d="M0,160L60,170.7C120,181,240,203,360,197.3C480,192,600,160,720,144C840,128,960,128,1080,144C1200,160,1320,192,1380,208L1440,224L1440,0L1380,0C1320,0,1200,0,1080,0C960,0,840,0,720,0C600,0,480,0,360,0C240,0,120,0,60,0L0,0Z" />
        </svg>
    </div>
</div>
<?php include 'footer.php'; ?>
