<?php
if (!function_exists('h')) {
    function h($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
}
$editing = isset($col) && is_array($col);
?>
<link rel="stylesheet" href="public/styles.css">

<div class="container">
  <div class="toolbar">
    <h1><?= h($titulo_form ?? ($editing ? 'Actualizar' : 'Registrar')) . ($editing ? ' #' . h($col['ID_USUARIO']) : '') ?></h1>
    <a class="btn" href="index.php?mod=colaboradores&action=listarColaboradores">← Volver</a>
  </div>

  <form method="post"
        action="index.php?mod=colaboradores&action=<?= $editing ? 'actualizarColaborador' : 'guardarColaborador' ?>"
        class="card form">
    <?php if ($editing): ?>
      <input type="hidden" name="ID_USUARIO" value="<?= h($col['ID_USUARIO']) ?>">
    <?php endif; ?>

    <div class="form-grid">
      <label>Nombre
        <input name="NOMBRE" value="<?= h($col['NOMBRE'] ?? '') ?>" required>
      </label>

      <label>Fecha registro
        <input type="date" name="FECHA_REGISTRO" value="<?= h($col['FECHA_REGISTRO'] ?? '') ?>">
      </label>

      <label>Teléfono
        <input name="TELEFONO" value="<?= h($col['TELEFONO'] ?? '') ?>">
      </label>

      <label>Correo
        <input type="email" name="CORREO" value="<?= h($col['CORREO'] ?? '') ?>">
      </label>

      <label>Rol
        <select name="ID_ROL" required>
          <option value="">-- Seleccione --</option>
          <?php
          $selRol = $col['ID_ROL'] ?? '';
          foreach (($roles ?? []) as $r) {
            $s = ((string)$selRol === (string)$r['ID']) ? ' selected' : '';
            echo '<option value="'.h($r['ID']).'"'.$s.'>'.h($r['NOMBRE']).'</option>';
          }
          ?>
        </select>
      </label>

      <label>Estado
        <select name="ID_ESTADO" required>
          <option value="">-- Seleccione --</option>
          <?php
          $selEst = $col['ID_ESTADO'] ?? '';
          foreach (($estados ?? []) as $e) {
            $s = ((string)$selEst === (string)$e['ID']) ? ' selected' : '';
            echo '<option value="'.h($e['ID']).'"'.$s.'>'.h($e['NOMBRE']).'</option>';
          }
          ?>
        </select>
      </label>
    </div>

    <div class="mt-16" style="display:flex;gap:10px">
      <button class="btn btn--primary" type="submit"><?= $editing ? 'Actualizar' : 'Guardar' ?></button>
      <a class="btn btn--ghost" href="index.php?mod=colaboradores&action=listarColaboradores">Cancelar</a>
    </div>
  </form>
</div>
