<?php function h($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); } ?>
<link rel="stylesheet" href="public/styles.css">

<div class="container">
  <div class="toolbar">
    <h1>Colaboradores</h1>
    <div>
      <a class="btn" href="index.php">← Módulos</a>
      <a class="btn btn--primary" href="index.php?mod=colaboradores&action=nuevoColaborador">+ Agregar</a>
    </div>
  </div>

  <?php if (isset($_GET['msg'])): ?><div class="alert"><?= h($_GET['msg']) ?></div><?php endif; ?>

  <div class="card">
    <table class="table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Fecha registro</th>
          <th>Teléfono</th>
          <th>Correo</th>
          <th>Rol</th>
          <th>Estado</th>
          <th style="width:220px">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach (($colaboradores ?? []) as $r): ?>
          <tr>
            <td><?= h($r['ID_USUARIO']) ?></td>
            <td><?= h($r['NOMBRE']) ?></td>
            <td><?= h($r['FECHA_REGISTRO']) ?></td>
            <td><?= h($r['TELEFONO']) ?></td>
            <td><?= h($r['CORREO']) ?></td>
            <td><?= h($r['NOMBRE_ROL'] ?? ('#'.($r['ID_ROL'] ?? ''))) ?></td>
            <td><span class="badge"><?= h($r['NOMBRE_ESTADO'] ?? ('#'.($r['ID_ESTADO'] ?? ''))) ?></span></td>
            <td>
              <div class="actions">
                <a class="btn btn--sm" href="index.php?mod=colaboradores&action=editarColaborador&id=<?= h($r['ID_USUARIO']) ?>">Modificar</a>
                <form action="index.php?mod=colaboradores&action=eliminarColaborador" method="post" style="display:inline"
                      onsubmit="return confirm('¿Eliminar el colaborador #<?= h($r['ID_USUARIO']) ?>?');">
                  <input type="hidden" name="ID_USUARIO" value="<?= h($r['ID_USUARIO']) ?>">
                  <button type="submit" class="btn btn--sm btn--danger">Eliminar</button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php if (empty($colaboradores)): ?><div class="center" style="padding:20px;color:var(--muted)">Sin registros.</div><?php endif; ?>
  </div>
</div>
