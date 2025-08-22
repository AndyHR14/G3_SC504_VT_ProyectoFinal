<?php
if (!function_exists('h')) {
  function h($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
}
$editing = isset($factura) && is_array($factura);

$usrSel = $factura['ID_USUARIO'] ?? '';
$mpSel  = $factura['ID_METODO_PAGO'] ?? '';
$estSel = $factura['ID_ESTADO'] ?? '';
?>
<link rel="stylesheet" href="public/styles.css">

<div class="container">
  <div class="toolbar">
    <h1><?= h($titulo_form ?? ($editing ? 'Actualizar' : 'Registrar')) . ($editing ? ' #'.h($factura['ID_FACTURA']) : '') ?></h1>
    <a class="btn" href="index.php?mod=facturas&action=listarFacturas">← Volver</a>
  </div>

  <form method="post"
        action="index.php?mod=facturas&action=<?= $editing ? 'actualizarFactura' : 'guardarFactura' ?>"
        class="card form">
    <?php if ($editing): ?>
      <input type="hidden" name="ID_FACTURA" value="<?= h($factura['ID_FACTURA']) ?>">
    <?php endif; ?>

    <div class="form-grid">
      <label>Fecha
        <input type="date" name="FECHA_REGISTRO" value="<?= h($factura['FECHA_REGISTRO'] ?? '') ?>">
      </label>
      <label>Cliente/Usuario
        <select name="ID_USUARIO" required>
          <option value="">-- Seleccione --</option>
          <?php foreach (($usuarios ?? []) as $u):
            $s = ((string)$usrSel === (string)$u['ID']) ? ' selected' : ''; ?>
            <option value="<?= h($u['ID']) ?>"<?= $s ?>><?= h($u['NOMBRE']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Método de pago
        <select name="ID_METODO_PAGO" required>
          <option value="">-- Seleccione --</option>
          <?php foreach (($metodosPago ?? []) as $m):
            $s = ((string)$mpSel === (string)$m['ID']) ? ' selected' : ''; ?>
            <option value="<?= h($m['ID']) ?>"<?= $s ?>><?= h($m['NOMBRE']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>

      <label>Subtotal
        <input type="number" step="0.01" name="SUBTOTAL" value="<?= h($factura['SUBTOTAL'] ?? '') ?>">
      </label>
      <label>IVA
        <input type="number" step="0.01" name="IVA" value="<?= h($factura['IVA'] ?? '') ?>">
      </label>
      <label>Descuento
        <input type="number" step="0.01" name="DESCUENTO" value="<?= h($factura['DESCUENTO'] ?? '') ?>">
      </label>
      <label>Total
        <input type="number" step="0.01" name="MONTO_TOTAL" value="<?= h($factura['MONTO_TOTAL'] ?? '') ?>">
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
      <a class="btn btn--ghost" href="index.php?mod=facturas&action=listarFacturas">Cancelar</a>
    </div>
  </form>

  <?php if ($editing): ?>
    <div class="card" style="margin-top:20px">
      <h2 style="margin:0 0 12px">Detalle de la factura</h2>
      <?php if (isset($_GET['msg'])): ?><div class="alert"><?= h($_GET['msg']) ?></div><?php endif; ?>

      
      <form method="post" action="index.php?mod=facturas&action=guardarDetalle" class="form">
        <input type="hidden" name="ID_FACTURA" value="<?= h($factura['ID_FACTURA']) ?>">

        <div class="form-grid">
          <label>Producto
            <select name="ID_PRODUCTO" required>
              <option value="">-- Seleccione --</option>
              <?php foreach (($productos ?? []) as $p): ?>
                <option value="<?= h($p['ID']) ?>"><?= h($p['NOMBRE']) ?></option>
              <?php endforeach; ?>
            </select>
          </label>

          <label>Cantidad
            <input type="number" step="1" name="CANTIDAD" required>
          </label>
          <label>Precio unitario
            <input type="number" step="0.01" name="PRECIO_UNITARIO" required>
          </label>
          <label>Total
            <input type="number" step="0.01" name="TOTAL" required>
          </label>

          <label>Estado (detalle)
            <select name="ID_ESTADO_DET" required>
              <option value="">-- Seleccione --</option>
              <?php foreach (($estados ?? []) as $e): ?>
                <option value="<?= h($e['ID']) ?>"><?= h($e['NOMBRE']) ?></option>
              <?php endforeach; ?>
            </select>
          </label>
        </div>

        <div class="mt-16" style="display:flex;gap:10px">
          <button class="btn btn--primary" type="submit">Agregar / Guardar detalle</button>
        </div>
      </form>

      <!-- Tabla de detalles existentes -->
      <div style="margin-top:16px;overflow:auto">
        <table class="table">
          <thead>
          <tr>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Precio</th>
            <th>Total</th>
            <th>Estado</th>
            <th style="width:120px">Acciones</th>
          </tr>
          </thead>
          <tbody>
          <?php foreach (($detalles ?? []) as $d): ?>
            <tr>
              <td><?= h($d['NOMBRE_PRODUCTO']) ?> <span class="muted">#<?= h($d['ID_PRODUCTO']) ?></span></td>
              <td><?= h($d['CANTIDAD']) ?></td>
              <td><?= h($d['PRECIO_UNITARIO']) ?></td>
              <td><?= h($d['TOTAL']) ?></td>
              <td><span class="badge"><?= h($d['NOMBRE_ESTADO'] ?? ('#'.$d['ID_ESTADO'])) ?></span></td>
              <td>
                <form action="index.php?mod=facturas&action=eliminarDetalle" method="post" style="display:inline"
                      onsubmit="return confirm('¿Eliminar el producto #<?= h($d['ID_PRODUCTO']) ?> de la factura #<?= h($d['ID_FACTURA']) ?>?');">
                  <input type="hidden" name="ID_FACTURA" value="<?= h($d['ID_FACTURA']) ?>">
                  <input type="hidden" name="ID_PRODUCTO" value="<?= h($d['ID_PRODUCTO']) ?>">
                  <button type="submit" class="btn btn--sm btn--danger">Eliminar</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
        <?php if (empty($detalles)): ?><div class="center" style="padding:12px;color:var(--muted)">Sin detalles.</div><?php endif; ?>
      </div>
    </div>
  <?php endif; ?>
</div>
