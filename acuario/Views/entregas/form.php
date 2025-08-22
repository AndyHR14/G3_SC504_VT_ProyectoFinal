<?php
if (!function_exists('h')) {
  function h($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
}
$editing = isset($entrega) && is_array($entrega);
$usrSel  = $entrega['ID_USUARIO']   ?? '';
$dirSel  = $entrega['ID_DIRECCION'] ?? '';
$estSel  = $entrega['ID_ESTADO']    ?? '';
?>
<link rel="stylesheet" href="public/styles.css">

<div class="container">
  <div class="toolbar">
    <h1><?= h($titulo_form ?? ($editing ? 'Actualizar' : 'Registrar')) . ($editing ? ' #'.h($entrega['ID_ENTREGA']) : '') ?></h1>
    <a class="btn" href="index.php?mod=entregas&action=listarEntregas">← Volver</a>
  </div>

  <form method="post"
        action="index.php?mod=entregas&action=<?= $editing ? 'actualizarEntrega' : 'guardarEntrega' ?>"
        class="card form">
    <?php if ($editing): ?>
      <input type="hidden" name="ID_ENTREGA" value="<?= h($entrega['ID_ENTREGA']) ?>">
    <?php endif; ?>

    <div class="form-grid">
      <label>Fecha
        <input type="date" name="FECHA" value="<?= h($entrega['FECHA'] ?? '') ?>">
      </label>

      <label>Usuario
        <select name="ID_USUARIO" required>
          <option value="">-- Seleccione --</option>
          <?php foreach (($usuarios ?? []) as $u):
            $s = ((string)$usrSel === (string)$u['ID']) ? ' selected' : ''; ?>
            <option value="<?= h($u['ID']) ?>"<?= $s ?>><?= h($u['NOMBRE']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>

      <label>Dirección
        <select name="ID_DIRECCION" required>
          <option value="">-- Seleccione --</option>
          <?php foreach (($direcciones ?? []) as $d):
            $s = ((string)$dirSel === (string)$d['ID']) ? ' selected' : ''; ?>
            <option value="<?= h($d['ID']) ?>"<?= $s ?>><?= h($d['NOMBRE']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>

      <label>Estado
        <select name="ID_ESTADO" required>
          <option value="">-- Seleccione --</option>
          <?php foreach (($estados ?? []) as $e):
            $s = ((string)$estSel === (string)$e['ID']) ? ' selected' : ''; ?>
            <option value="<?= h($e['ID']) ?>"<?= $s ?>><?= h($e['NOMBRE']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
    </div>

    <div class="mt-16" style="display:flex;gap:10px">
      <button class="btn btn--primary" type="submit"><?= $editing ? 'Actualizar' : 'Guardar' ?></button>
      <a class="btn btn--ghost" href="index.php?mod=entregas&action=listarEntregas">Cancelar</a>
    </div>
  </form>

  <?php if ($editing): ?>
    <div class="card" style="margin-top:20px">
      <h2 style="margin:0 0 12px">Detalle de la entrega</h2>

      <?php if (isset($_GET['msg'])): ?><div class="alert"><?= h($_GET['msg']) ?></div><?php endif; ?>

      <?php if (!empty($detalle)): ?>
        <div class="muted" style="margin-bottom:10px">
          <strong>Estado detalle:</strong> <?= h($detalle['NOMBRE_ESTADO'] ?? ('#'.$detalle['ID_ESTADO'])) ?>
        </div>
      <?php endif; ?>

      <form method="post" action="index.php?mod=entregas&action=guardarDetalle" class="form">
        <input type="hidden" name="ID_ENTREGA" value="<?= h($entrega['ID_ENTREGA']) ?>">
        <div class="form-grid">
          <label>Descripción
            <input name="DESCRIPCION" value="<?= h($detalle['DESCRIPCION'] ?? '') ?>" required>
          </label>
          <label>Cantidad
            <input type="number" step="1" name="CANTIDAD" value="<?= h($detalle['CANTIDAD'] ?? '') ?>">
          </label>
          <label>Estado (detalle)
            <select name="ID_ESTADO_DET" required>
              <option value="">-- Seleccione --</option>
              <?php
              $detSel = $detalle['ID_ESTADO'] ?? '';
              foreach (($estados ?? []) as $e):
                $s = ((string)$detSel === (string)$e['ID']) ? ' selected' : ''; ?>
                <option value="<?= h($e['ID']) ?>"<?= $s ?>><?= h($e['NOMBRE']) ?></option>
              <?php endforeach; ?>
            </select>
          </label>
        </div>

        <div class="mt-16" style="display:flex;gap:10px">
          <button class="btn btn--primary" type="submit"><?= empty($detalle) ? 'Agregar detalle' : 'Guardar cambios' ?></button>

          <?php if (!empty($detalle)): ?>
            <form action="index.php?mod=entregas&action=eliminarDetalle" method="post" style="display:inline"
                  onsubmit="return confirm('¿Eliminar el detalle de la entrega #<?= h($entrega['ID_ENTREGA']) ?>?');">
              <input type="hidden" name="ID_ENTREGA" value="<?= h($entrega['ID_ENTREGA']) ?>">
              <button class="btn btn--danger" type="submit">Eliminar detalle</button>
            </form>
          <?php endif; ?>
        </div>
      </form>
    </div>
  <?php endif; ?>
</div>
