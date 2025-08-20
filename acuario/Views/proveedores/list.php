<?php function h($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); } ?>
<link rel="stylesheet" href="public/styles.css">

<div class="container">
  <div class="toolbar">
    <h1>Proveedores</h1>
    <div>
      <a class="btn" href="index.php">← Módulos</a>
      <a class="btn btn--primary" href="index.php?mod=proveedores&action=nuevoProveedor">+ Agregar</a>
    </div>
  </div>

  <?php if (isset($_GET['msg'])): ?><div class="alert"><?= h($_GET['msg']) ?></div><?php endif; ?>

  <div class="card">
    <table class="table">
      <thead>
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Teléfono</th>
        <th>Correo</th>
        <th>Dirección</th>
        <th>Estado</th>
        <th style="width:220px">Acciones</th>
      </tr>
      </thead>
      <tbody>
      <?php foreach (($proveedores ?? []) as $r): ?>
        <tr>
          <td><?= h($r['ID_EMPRESA']) ?></td>
          <td><?= h($r['NOMBRE_EMPRESA']) ?></td>
          <td><?= h($r['TELEFONO']) ?></td>
          <td><?= h($r['CORREO']) ?></td>
          <td><?= h($r['DETALLE_DIRECCION']) ?></td>
          <td><span class="badge">#<?= h($r['ID_ESTADO']) ?></span></td>
          <td>
            <div class="actions">
              <a class="btn btn--sm" href="index.php?mod=proveedores&action=editarProveedor&id=<?= h($r['ID_EMPRESA']) ?>">Modificar</a>
              <form action="index.php?mod=proveedores&action=eliminarProveedor" method="post" style="display:inline"
                    onsubmit="return confirm('¿Eliminar el proveedor #<?= h($r['ID_EMPRESA']) ?>?');">
                <input type="hidden" name="ID_EMPRESA" value="<?= h($r['ID_EMPRESA']) ?>">
                <button type="submit" class="btn btn--sm btn--danger">Eliminar</button>
              </form>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    <?php if (empty($proveedores)): ?><div class="center" style="padding:20px;color:var(--muted)">Sin registros.</div><?php endif; ?>
  </div>
</div>
