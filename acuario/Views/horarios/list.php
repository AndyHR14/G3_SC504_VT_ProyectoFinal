<?php function h($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); } ?>
<link rel="stylesheet" href="public/styles.css">

<div class="container">
  <div class="toolbar">
    <h1>Horarios</h1>
    <div>
      <a class="btn" href="index.php">← Módulos</a>
      <a class="btn btn--primary" href="index.php?mod=horarios&action=nuevoHorario">+ Agregar</a>
    </div>
  </div>

  <?php if (isset($_GET['msg'])): ?><div class="alert"><?= h($_GET['msg']) ?></div><?php endif; ?>

  <div class="card">
    <table class="table">
      <thead>
      <tr>
        <th>ID</th>
        <th>Día</th>
        <th>Inicio</th>
        <th>Final</th>
        <th>Usuario</th>
        <th>Estado</th>
        <th style="width:220px">Acciones</th>
      </tr>
      </thead>
      <tbody>
      <?php foreach (($horarios ?? []) as $r): ?>
        <tr>
          <td><?= h($r['ID_HORARIO']) ?></td>
          <td><?= h($r['DIA']) ?></td>
          <td><?= h($r['HORA_INICIO']) ?></td>
          <td><?= h($r['HORA_FINAL']) ?></td>
          <td><?= h($r['NOMBRE'] ?? ('#'.($r['ID_USUARIO'] ?? ''))) ?></td>
          <td><span class="badge"><?= h($r['NOMBRE_ESTADO'] ?? ('#'.($r['ID_ESTADO'] ?? ''))) ?></span></td>
          <td>
            <div class="actions">
              <a class="btn btn--sm" href="index.php?mod=horarios&action=editarHorario&id=<?= h($r['ID_HORARIO']) ?>">Modificar</a>
              <form action="index.php?mod=horarios&action=eliminarHorario" method="post" style="display:inline"
                    onsubmit="return confirm('¿Eliminar el horario #<?= h($r['ID_HORARIO']) ?>?');">
                <input type="hidden" name="ID_HORARIO" value="<?= h($r['ID_HORARIO']) ?>">
                <button type="submit" class="btn btn--sm btn--danger">Eliminar</button>
              </form>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    <?php if (empty($horarios)): ?><div class="center" style="padding:20px;color:var(--muted)">Sin registros.</div><?php endif; ?>
  </div>
</div>
