<?php
if (!function_exists('h')) {
  function h($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
}
$editing = isset($prov) && is_array($prov);
$dirSel  = $prov['ID_DIRECCION'] ?? '';
$estSel  = $prov['ID_ESTADO']    ?? '';
?>
<link rel="stylesheet" href="public/styles.css">

<div class="container">
  <div class="toolbar">
    <h1><?= h($titulo_form ?? ($editing ? 'Actualizar' : 'Registrar')) . ($editing ? ' #'.h($prov['ID_EMPRESA']) : '') ?></h1>
    <a class="btn" href="index.php?mod=proveedores&action=listarProveedores">← Volver</a>
  </div>

  <form method="post"
        action="index.php?mod=proveedores&action=<?= $editing ? 'actualizarProveedor' : 'guardarProveedor' ?>"
        class="card form">
    <?php if ($editing): ?>
      <input type="hidden" name="ID_EMPRESA" value="<?= h($prov['ID_EMPRESA']) ?>">
    <?php endif; ?>

    <div class="form-grid">
      <label>Nombre del proveedor
        <input name="NOMBRE_EMPRESA" value="<?= h($prov['NOMBRE_EMPRESA'] ?? '') ?>" required>
      </label>

      <label>Teléfono
        <input name="TELEFONO" value="<?= h($prov['TELEFONO'] ?? '') ?>">
      </label>

      <label>Correo
        <input type="email" name="CORREO" value="<?= h($prov['CORREO'] ?? '') ?>">
      </label>

      <label>Dirección
        <select name="ID_DIRECCION">
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
      <a class="btn btn--ghost" href="index.php?mod=proveedores&action=listarProveedores">Cancelar</a>
    </div>
  </form>
</div>
