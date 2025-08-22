<?php
if (!function_exists('h')) {
  function h($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
}
$editing = isset($item) && is_array($item);

$catSel   = $item['ID_CATEGORIA']      ?? '';
$umSel    = $item['ID_UNIDAD_MEDIDA']  ?? '';
$estProd  = $item['ID_ESTADO_PROD']    ?? '';
$estInv   = $item['ID_ESTADO_INV']     ?? '';
?>
<link rel="stylesheet" href="public/styles.css">

<div class="container">
  <div class="toolbar">
    <h1><?= h($titulo_form ?? ($editing ? 'Actualizar' : 'Registrar')) . ($editing ? ' #'.h($item['ID_PRODUCTO']) : '') ?></h1>
    <a class="btn" href="index.php?mod=inventario&action=listarInventario">← Volver</a>
  </div>

  <form method="post"
        action="index.php?mod=inventario&action=<?= $editing ? 'actualizarItem' : 'guardarItem' ?>"
        class="card form">
    <?php if ($editing): ?>
      <input type="hidden" name="ID_PRODUCTO" value="<?= h($item['ID_PRODUCTO']) ?>">
    <?php endif; ?>

    <div class="form-grid">
      <!-- PRODUCTO -->
      <label>Nombre del producto
        <input name="NOMBRE" value="<?= h($item['NOMBRE'] ?? $item['NOMBRE_PRODUCTO'] ?? '') ?>" required>
      </label>

      <label>Categoría
        <select name="ID_CATEGORIA" required>
          <option value="">-- Seleccione --</option>
          <?php foreach (($categorias ?? []) as $c):
            $s = ((string)$catSel === (string)$c['ID']) ? ' selected' : ''; ?>
            <option value="<?= h($c['ID']) ?>"<?= $s ?>><?= h($c['NOMBRE']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>

      <label>Unidad de medida
        <select name="ID_UNIDAD_MEDIDA" required>
          <option value="">-- Seleccione --</option>
          <?php foreach (($unidades ?? []) as $u):
            $s = ((string)$umSel === (string)$u['ID']) ? ' selected' : ''; ?>
            <option value="<?= h($u['ID']) ?>"<?= $s ?>><?= h($u['NOMBRE']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>

      <label>Estado (Producto)
        <select name="ID_ESTADO_PROD" required>
          <option value="">-- Seleccione --</option>
          <?php foreach (($estados ?? []) as $e):
            $s = ((string)$estProd === (string)$e['ID']) ? ' selected' : ''; ?>
            <option value="<?= h($e['ID']) ?>"<?= $s ?>><?= h($e['NOMBRE']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>

 
      <label>Cantidad
        <input type="number" step="1" name="CANTIDAD" value="<?= h($item['CANTIDAD'] ?? '') ?>">
      </label>

      <label>Fecha ingreso
        <input type="date" name="FECHA_INGRESO" value="<?= h($item['FECHA_INGRESO'] ?? '') ?>">
      </label>

      <label>Estado (Inventario)
        <select name="ID_ESTADO_INV">
          <option value="">-- Seleccione --</option>
          <?php foreach (($estados ?? []) as $e):
            $s = ((string)$estInv === (string)$e['ID']) ? ' selected' : ''; ?>
            <option value="<?= h($e['ID']) ?>"<?= $s ?>><?= h($e['NOMBRE']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
    </div>

    <div class="mt-16" style="display:flex;gap:10px">
      <button class="btn btn--primary" type="submit"><?= $editing ? 'Actualizar' : 'Guardar' ?></button>
      <a class="btn btn--ghost" href="index.php?mod=inventario&action=listarInventario">Cancelar</a>
    </div>
  </form>
</div>
