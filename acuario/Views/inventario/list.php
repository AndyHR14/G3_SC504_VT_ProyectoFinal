<?php function h($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); } ?>
<link rel="stylesheet" href="public/styles.css">

<div class="container">
  <div class="toolbar">
    <h1>Inventario</h1>
    <div>
      <a class="btn" href="index.php">← Módulos</a>
      <a class="btn btn--primary" href="index.php?mod=inventario&action=nuevoItem">+ Agregar</a>
    </div>
  </div>

  <?php if (isset($_GET['msg'])): ?><div class="alert"><?= h($_GET['msg']) ?></div><?php endif; ?>

  <div class="card">
    <table class="table">
      <thead>
      <tr>
        <th>ID</th>
        <th>Producto</th>
        <th>Categoría</th>
        <th>Cantidad</th>
        <th>Unidad de Medida</th>
        <th>Estado</th>
        <th style="width:220px">Acciones</th>
      </tr>
      </thead>
      <tbody>
      <?php foreach (($items ?? []) as $r): ?>
        <tr>
          <td><?= h($r['ID_PRODUCTO']) ?></td>
          <td><?= h($r['NOMBRE_PRODUCTO']) ?></td>
          <td><?= h($r['NOMBRE_CATEGORIA']) ?></td>
          <td><?= h($r['CANTIDAD']) ?></td>
          <td><?= h($r['NOMBRE_UNIDAD_MEDIDA']) ?></td>
          <td><span class="badge">#<?= h($r['ID_ESTADO']) ?></span></td>
          <td>
            <div class="actions">
              <a class="btn btn--sm" href="index.php?mod=inventario&action=editarItem&id=<?= h($r['ID_PRODUCTO']) ?>">Modificar</a>
              <form action="index.php?mod=inventario&action=eliminarItem" method="post" style="display:inline"
                    onsubmit="return confirm('¿Eliminar el producto #<?= h($r['ID_PRODUCTO']) ?>?');">
                <input type="hidden" name="ID_PRODUCTO" value="<?= h($r['ID_PRODUCTO']) ?>">
                <button type="submit" class="btn btn--sm btn--danger">Eliminar</button>
              </form>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    <?php if (empty($items)): ?><div class="center" style="padding:20px;color:var(--muted)">Sin registros.</div><?php endif; ?>
  </div>
</div>
<?php
// Helper: escapar HTML
