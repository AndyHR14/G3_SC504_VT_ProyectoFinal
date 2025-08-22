<?php
if (!function_exists('h')) {
    function h($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
}
$editing = isset($horario) && is_array($horario);
$diaSel  = $horario['DIA'] ?? '';
$usrSel  = $horario['ID_USUARIO'] ?? '';
$estSel  = $horario['ID_ESTADO'] ?? '';
$dias = ['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'];
?>
<link rel="stylesheet" href="public/styles.css">

<div class="container">
  <div class="toolbar">
    <h1><?= h($titulo_form ?? ($editing ? 'Actualizar' : 'Registrar')) . ($editing ? ' #' . h($horario['ID_HORARIO']) : '') ?></h1>
    <a class="btn" href="index.php?mod=horarios&action=listarHorarios">← Volver</a>
  </div>

  <form method="post"
        action="index.php?mod=horarios&action=<?= $editing ? 'actualizarHorario' : 'guardarHorario' ?>"
        class="card form">
    <?php if ($editing): ?>
      <input type="hidden" name="ID_HORARIO" value="<?= h($horario['ID_HORARIO']) ?>">
    <?php endif; ?>

    <div class="form-grid">
      <label>Día
        <select name="DIA" required>
          <option value="">-- Seleccione --</option>
          <?php foreach ($dias as $d): $s = ($diaSel === $d) ? ' selected' : ''; ?>
            <option value="<?= h($d) ?>"<?= $s ?>><?= h($d) ?></option>
          <?php endforeach; ?>
        </select>
      </label>

      <label>Hora inicio
        <input type="time" name="HORA_INICIO" value="<?= h($horario['HORA_INICIO'] ?? '') ?>" required>
      </label>

      <label>Hora final
        <input type="time" name="HORA_FINAL" value="<?= h($horario['HORA_FINAL'] ?? '') ?>" required>
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
      <a class="btn btn--ghost" href="index.php?mod=horarios&action=listarHorarios">Cancelar</a>
    </div>
  </form>
</div>
