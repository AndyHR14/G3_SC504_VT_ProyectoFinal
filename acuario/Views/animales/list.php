<?php function h($s)
{
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
} ?>
<link rel="stylesheet" href="public/styles.css">

<div class="container">
    <div class="toolbar">
        <h1>Animales</h1>
        <div>
            <a class="btn" href="index.php">← Módulos</a>
            <a class="btn btn--primary" href="index.php?mod=animales&action=nuevoAnimal">+ Agregar</a>
        </div>
    </div>

    <?php if (isset($_GET['msg'])): ?><div class="alert"><?= h($_GET['msg']) ?></div><?php endif; ?>

    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Fecha ingreso</th>
                    <th>Edad</th>
                    <th>Peso</th>
                    <th>Estado</th>
                    <th style="width:220px">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($animales as $r): ?>
                    <tr>
                        <td><?= h($r['ID_ANIMAL']) ?></td>
                        <td><?= h($r['NOMBRE_ANIMAL']) ?></td>
                        <td><?= h($r['FECHA_INGRESO']) ?></td>
                        <td><?= h($r['EDAD']) ?></td>
                        <td><?= h($r['PESO']) ?></td>
                        <td><span class="badge">#<?= h($r['ID_ESTADO']) ?></span></td>
                        <td>
                            <div class="actions">
                                <a class="btn btn--sm" href="index.php?mod=animales&action=editarAnimal&id=<?= h($r['ID_ANIMAL']) ?>">Modificar</a>
                                <form action="index.php?mod=animales&action=eliminarAnimal" method="post" style="display:inline"
                                    onsubmit="return confirm('¿Eliminar el registro #<?= h($r['ID_ANIMAL']) ?>?');">
                                    <input type="hidden" name="ID_ANIMAL" value="<?= h($r['ID_ANIMAL']) ?>">
                                    <!-- opcional: para bitácora -->
                                    <!-- <input type="hidden" name="USUARIO" value="<?= h($_SESSION['usuario'] ?? 'WEB') ?>"> -->
                                    <button type="submit" class="btn btn--sm btn--danger">Eliminar</button>
                                </form>

                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php if (empty($animales)): ?><div class="center" style="padding:20px;color:var(--muted)">Sin registros.</div><?php endif; ?>
    </div>
</div>