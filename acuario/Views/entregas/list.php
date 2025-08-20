<?php function h($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); } ?>
<link rel="stylesheet" href="public/styles.css">

<div class="container">
  <div class="toolbar">
    <h1>Entregas</h1>
    <div>
      <a class="btn" href="index.php">← Módulos</a>
      <a class="btn btn--primary" href="index.php?mod=entregas&action=nuevaEntrega">+ Agregar</a>
    </div>
  </div>

  <?php if (isset($_GET['msg'])): ?><div class="alert"><?= h($_GET['msg']) ?></div><?php endif; ?>

  <div class="card">
    <table class="table">
      <thead>
      <tr>
        <th>ID</th>
        <th>Fecha</th>
        <th>Usuario</th>
        <th>Dirección</th>
        <th>Estado</th>
        <th style="width:220px">Acciones</th>
      </tr>
      </thead>
      <tbody>
      <?php foreach (($entregas ?? []) as $r): ?>
        <tr>
          <td><?= h($r['ID_ENTREGA']) ?></td>
          <td><?= h($r['FECHA']) ?></td>
          <td><?= h($r['NOMBRE_USUARIO']) ?> <span class="muted">#<?= h($r['ID_USUARIO']) ?></span></td>
          <td><?= h($r['DETALLE_DIRECCION']) ?> <span class="muted">#<?= h($r['ID_DIRECCION']) ?></span></td>
          <td><span class="badge"><?= h($r['NOMBRE_ESTADO'] ?? ('#'.$r['ID_ESTADO'])) ?></span></td>
          <td>
            <div class="actions">
              <a class="btn btn--sm" href="index.php?mod=entregas&action=editarEntrega&id=<?= h($r['ID_ENTREGA']) ?>">Modificar</a>
              <form action="index.php?mod=entregas&action=eliminarEntrega" method="post" style="display:inline"
                    onsubmit="return confirm('¿Eliminar la entrega #<?= h($r['ID_ENTREGA']) ?>?');">
                <input type="hidden" name="ID_ENTREGA" value="<?= h($r['ID_ENTREGA']) ?>">
                <button type="submit" class="btn btn--sm btn--danger">Eliminar</button>
              </form>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    <?php if (empty($entregas)): ?><div class="center" style="padding:20px;color:var(--muted)">Sin registros.</div><?php endif; ?>
  </div>
</div>
