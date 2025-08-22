<?php function h($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); } ?>
<link rel="stylesheet" href="public/styles.css">

<div class="container">
  <div class="toolbar">
    <h1>Facturas</h1>
    <div>
      <a class="btn" href="index.php">← Módulos</a>
      <a class="btn btn--primary" href="index.php?mod=facturas&action=nuevaFactura">+ Agregar</a>
    </div>
  </div>

  <?php if (isset($_GET['msg'])): ?><div class="alert"><?= h($_GET['msg']) ?></div><?php endif; ?>

  <div class="card">
    <table class="table">
      <thead>
      <tr>
        <th>ID</th>
        <th>Fecha</th>
        <th>Cliente/Usuario</th>
        <th>Método de pago</th>
        <th>Subtotal</th>
        <th>IVA</th>
        <th>Descuento</th>
        <th>Total</th>
        <th>Items</th>
        <th>Estado</th>
        <th style="width:220px">Acciones</th>
      </tr>
      </thead>
      <tbody>
      <?php foreach (($facturas ?? []) as $r): ?>
        <tr>
          <td><?= h($r['ID_FACTURA']) ?></td>
          <td><?= h($r['FECHA_REGISTRO']) ?></td>
          <td><?= h($r['NOMBRE']) ?> <span class="muted">#<?= h($r['ID_USUARIO']) ?></span></td>
          <td><?= h($r['NOMBRE_METODO_PAGO']) ?> <span class="muted">#<?= h($r['ID_METODO_PAGO']) ?></span></td>
          <td><?= h($r['SUBTOTAL']) ?></td>
          <td><?= h($r['IVA']) ?></td>
          <td><?= h($r['DESCUENTO']) ?></td>
          <td><strong><?= h($r['MONTO_TOTAL']) ?></strong></td>
          <td><?= h($r['ITEMS']) ?></td>
          <td><span class="badge"><?= h($r['NOMBRE_ESTADO'] ?? ('#'.$r['ID_ESTADO'])) ?></span></td>
          <td>
            <div class="actions">
              <a class="btn btn--sm" href="index.php?mod=facturas&action=editarFactura&id=<?= h($r['ID_FACTURA']) ?>">Modificar</a>
              <form action="index.php?mod=facturas&action=eliminarFactura" method="post" style="display:inline"
                    onsubmit="return confirm('¿Eliminar la factura #<?= h($r['ID_FACTURA']) ?>?');">
                <input type="hidden" name="ID_FACTURA" value="<?= h($r['ID_FACTURA']) ?>">
                <button type="submit" class="btn btn--sm btn--danger">Eliminar</button>
              </form>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    <?php if (empty($facturas)): ?><div class="center" style="padding:20px;color:var(--muted)">Sin registros.</div><?php endif; ?>
  </div>
</div>
