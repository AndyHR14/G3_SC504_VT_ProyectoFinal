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
        <th>Fecha ingreso</th>
        <th>Estado (Prod)</th>
        <th>Estado (Inv)</th>
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
          <td><?= h($r['FECHA_INGRESO']) ?></td>
          <td><span class="badge">#<?= h($r['ID_ESTADO_PROD']) ?></span></td>
          <td><span class="badge">#<?= h($r['ID_ESTADO_INV']) ?></span></td>
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
function h($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }

// Helper: color para los badges de estado
if (!function_exists('badgeClase')) {
  function badgeClase($n) {
    switch (trim((string)$n)) {
      case 'Activo':       return 'success';
      case 'En stock':     return 'success';
      case 'Bajo stock':   return 'warning';
      case 'Agotado':      return 'danger';
      case 'Inactivo':     return 'muted';
      default:             return 'muted';
    }
  }
}
?>
<link rel="stylesheet" href="public/styles.css">

<div class="container">
  <div class="toolbar">
    <h1>Inventario</h1>
    <div>
      <a class="btn" href="index.php">← Módulos</a>
      <a class="btn btn--primary" href="index.php?mod=inventario&action=nuevoItem">+ Agregar</a>
    </div>
  </div>

  <?php if (isset($_GET['msg'])): ?>
    <div class="alert"><?= h($_GET['msg']) ?></div>
  <?php endif; ?>

  <div class="card">
    <table class="table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Producto</th>
          <th>Categoría</th>
          <th>Cantidad</th>
          <th>Fecha ingreso</th>
          <th>Estado (Prod)</th>
          <th>Estado (Inv)</th>
          <th style="width:220px">Acciones</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach (($items ?? []) as $r): ?>
        <?php
          // Campos que vienen de la vista FIDE_INVENTARIO_V
          $idProd   = $r['ID_PRODUCTO'] ?? null;
          $prodNom  = $r['NOMBRE_PRODUCTO'] ?? '';
          $catNom   = $r['NOMBRE_CATEGORIA'] ?? '';
          $unidad   = $r['NOMBRE_UNIDAD_MEDIDA'] ?? '';
          $cant     = $r['CANTIDAD'] ?? '';
          $fing     = $r['FECHA_INGRESO'] ?? '';
          $estProd  = $r['ESTADO_PRODUCTO'] ?? 'N/D';
          $estInv   = $r['ESTADO_INVENTARIO'] ?? 'N/D';
        ?>
        <tr>
          <td><?= h($idProd) ?></td>
          <td><?= h($prodNom) ?></td>
          <td><?= h($catNom) ?></td>
          <td><?= h($cant) ?> <?= h($unidad) ?></td>
          <td><?= h($fing) ?></td>
          <td>
            <span class="badge <?= badgeClase($estProd) ?>"><?= h($estProd) ?></span>
          </td>
          <td>
            <span class="badge <?= badgeClase($estInv) ?>"><?= h($estInv) ?></span>
          </td>
          <td>
            <div class="actions">
              <a class="btn btn--sm" href="index.php?mod=inventario&action=editarItem&id=<?= h($idProd) ?>">Modificar</a>
              <form action="index.php?mod=inventario&action=eliminarItem" method="post" style="display:inline"
                    onsubmit="return confirm('¿Eliminar el producto #<?= h($idProd) ?>?');">
                <input type="hidden" name="ID_PRODUCTO" value="<?= h($idProd) ?>">
                <button type="submit" class="btn btn--sm btn--danger">Eliminar</button>
              </form>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>

    <?php if (empty($items)): ?>
      <div class="center" style="padding:20px;color:var(--muted)">Sin registros.</div>
    <?php endif; ?>
  </div>
</div>
