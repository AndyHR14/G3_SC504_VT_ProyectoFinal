<?php
if (!function_exists('h')) {
  function h($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
}
$editing = isset($cliente) && is_array($cliente);

$rolSel  = $cliente['ID_ROL']       ?? '';
$estSel  = $cliente['ID_ESTADO']    ?? '';
$dirSel  = $cliente['ID_DIRECCION'] ?? '';
?>
<link rel="stylesheet" href="public/styles.css">

<div class="container">
  <div class="toolbar">
    <h1><?= h($titulo_form ?? ($editing ? 'Actualizar' : 'Registrar')) . ($editing ? ' #'.h($cliente['ID_USUARIO']) : '') ?></h1>
    <a class="btn" href="index.php?mod=clientes&action=listarClientes">← Volver</a>
  </div>

  <form method="post"
        action="index.php?mod=clientes&action=<?= $editing ? 'actualizarCliente' : 'guardarCliente' ?>"
        class="card form">
    <?php if ($editing): ?>
      <input type="hidden" name="ID_USUARIO" value="<?= h($cliente['ID_USUARIO']) ?>">
    <?php endif; ?>

    <div class="form-grid">
      <label>Nombre
        <input name="NOMBRE" value="<?= h($cliente['NOMBRE'] ?? '') ?>" required>
      </label>

      <label>Fecha de registro
        <input type="date" name="FECHA_REGISTRO" value="<?= h($cliente['FECHA_REGISTRO'] ?? '') ?>">
      </label>

      <label>Teléfono
        <input name="TELEFONO" value="<?= h($cliente['TELEFONO'] ?? '') ?>">
      </label>

      <label>Correo
        <input type="email" name="CORREO" value="<?= h($cliente['CORREO'] ?? '') ?>">
      </label>

      <label>Rol
        <select name="ID_ROL">
          <option value="">-- Seleccione --</option>
          <?php foreach (($roles ?? []) as $r):
            $s = ((string)$rolSel === (string)$r['ID']) ? ' selected' : ''; ?>
            <option value="<?= h($r['ID']) ?>"<?= $s ?>><?= h($r['NOMBRE']) ?></option>
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

      <label>Dirección
        <select name="ID_DIRECCION">
          <option value="">-- Seleccione --</option>
          <?php foreach (($direcciones ?? []) as $d):
            $s = ((string)$dirSel === (string)$d['ID']) ? ' selected' : ''; ?>
            <option value="<?= h($d['ID']) ?>"<?= $s ?>><?= h($d['NOMBRE']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
    </div>

    <div class="mt-16" style="display:flex;gap:10px">
      <button class="btn btn--primary" type="submit"><?= $editing ? 'Actualizar' : 'Guardar' ?></button>
      <a class="btn btn--ghost" href="index.php?mod=clientes&action=listarClientes">Cancelar</a>
    </div>
  </form>
</div>
