<?php
require_once __DIR__ . '/../auth/auth_check_client.php';
require_once __DIR__ . '/../config.php';

$catalogo = require __DIR__ . '/catalogo.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0 || !isset($catalogo[$id])) {
  header('Location: ' . BASE_URL . '/cliente/index.php'); exit;
}

$prod     = $catalogo[$id];
$nombre   = $_SESSION['user']['nombre'] ?? 'Cliente';
$precio   = (float)$prod['precio'];
$IVA_RATE = 0.13;         // si quieres, muévelo a config.php

// cantidad inicial y descuento inicial
$qty  = max(1, min(99, (int)($_GET['qty'] ?? 1)));
$desc = max(0.0, (float)($_GET['desc'] ?? 0.0));

$sub    = $precio * $qty;
$desc   = min($desc, $sub);              // límite
$base   = $sub - $desc;
$iva    = round($base * $IVA_RATE, 2);
$total  = round($base + $iva, 2);
$facNum = 'FAC-' . date('Ymd-His') . '-' . sprintf('%04d', $id);

// métodos de pago disponibles (clave => etiqueta)
$METODOS = [
  'efectivo'      => 'Efectivo #1',
  'sinpe'         => 'Sinpe Móvil #4',
  'transferencia' => 'Transferencia Bancaria #3',
  'tarjeta'       => 'Tarjeta #2',
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Factura - <?= htmlspecialchars($prod['nombre']) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Arial,sans-serif;background:#f3f8fc;margin:0}
    .wrap{max-width:960px;margin:24px auto;padding:24px}
    .card{background:#fff;border-radius:16px;box-shadow:0 10px 25px rgba(0,0,0,.06);padding:24px}
    .head{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px}
    h1{font-size:22px;margin:0}
    .muted{color:#6b7280;font-size:14px}
    table{width:100%;border-collapse:collapse;margin-top:12px}
    th,td{padding:12px;border-bottom:1px solid #eef2f7;text-align:left}
    tfoot td{font-weight:700}
    .right{text-align:right}
    .row{display:flex;gap:16px;align-items:flex-start}
    .thumb{width:160px;height:120px;border-radius:12px;object-fit:cover;box-shadow:0 6px 16px rgba(0,0,0,.08)}

    /* Cantidad (+/-) */
    .qty{display:inline-flex;align-items:center;gap:8px}
    .qbtn{
      width:32px;height:32px;border:0;border-radius:10px;cursor:pointer;
      background:#eaf7ff;color:#0e7490;font-size:18px;font-weight:800;line-height:0;
      box-shadow:0 4px 10px rgba(14,116,144,.15)
    }
    .qty input[type="number"]{
      width:62px;padding:6px 8px;border:1px solid #e2e8f0;border-radius:10px;text-align:center;font-weight:700
    }

    /* Controles inferiores (descuento/método) */
    .controls{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:16px}
    .control{background:#f8fbff;border:1px solid #eaf3ff;border-radius:12px;padding:12px}
    .control label{display:block;font-size:13px;color:#334155;margin-bottom:6px}
    .control input[type="number"], .control select{
      width:100%;padding:10px;border:1px solid #e2e8f0;border-radius:10px;font-weight:600
    }

    .actions{margin-top:16px;display:flex;gap:10px;justify-content:flex-end}
    .btn{border:0;border-radius:12px;padding:.7rem 1.1rem;font-weight:600;cursor:pointer}
    .primary{background:#0ea5e9;color:#fff}
    .ghost{background:#eef6ff;color:#0e7490}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="card">
      <div class="head">
        <div>
          <h1>Factura</h1>
          <div class="muted">N.º <?= htmlspecialchars($facNum) ?></div>
        </div>
        <div class="muted">
          Fecha: <?= date('d/m/Y H:i') ?><br>
          Cliente: <?= htmlspecialchars($nombre) ?>
        </div>
      </div>

      <div class="row">
        <img class="thumb" src="<?= htmlspecialchars($prod['img']) ?>" alt="<?= htmlspecialchars($prod['nombre']) ?>"
             onerror="this.style.display='none'">
        <table>
          <thead>
            <tr>
              <th>Producto</th>
              <th class="right">Precio</th>
              <th class="right">Cant.</th>
              <th class="right">Subtotal</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>
                <strong><?= htmlspecialchars($prod['nombre']) ?></strong><br>
                <span class="muted">ID: <?= (int)$prod['id'] ?></span>
              </td>
              <td class="right" id="precioCell">₡<?= number_format($precio, 2) ?></td>
              <td class="right">
                <div class="qty">
                  <button type="button" class="qbtn" id="minusBtn">−</button>
                  <input type="number" id="qty" min="1" max="99" step="1" value="<?= $qty ?>">
                  <button type="button" class="qbtn" id="plusBtn">+</button>
                </div>
              </td>
              <td class="right" id="subCell">₡<?= number_format($sub, 2) ?></td>
            </tr>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="3" class="right">Descuento</td>
              <td class="right" id="descCell">₡<?= number_format($desc, 2) ?></td>
            </tr>
            <tr>
              <td colspan="3" class="right">IVA (<?= (int)($IVA_RATE*100) ?>%)</td>
              <td class="right" id="ivaCell">₡<?= number_format($iva, 2) ?></td>
            </tr>
            <tr>
              <td colspan="3" class="right">Total</td>
              <td class="right" id="totalCell">₡<?= number_format($total, 2) ?></td>
            </tr>
          </tfoot>
        </table>
      </div>

      <!-- Formulario: descuento, método y envío -->
      <form class="controls" action="<?= BASE_URL ?>/cliente/pagar.php" method="post" id="payForm">
        <div class="control">
          <label for="desc">Descuento (₡)</label>
          <input type="number" id="desc" name="desc" step="0.01" min="0" value="<?= number_format($desc, 2, '.', '') ?>">
        </div>
        <div class="control">
          <label for="metodo">Método de pago</label>
          <select id="metodo" name="metodo">
            <?php foreach ($METODOS as $k => $label): ?>
              <option value="<?= htmlspecialchars($k) ?>"><?= htmlspecialchars($label) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- hidden fields -->
        <input type="hidden" name="id"    value="<?= (int)$prod['id'] ?>">
        <input type="hidden" name="fac"   value="<?= htmlspecialchars($facNum) ?>">
        <input type="hidden" name="qty"   id="qtyInput"   value="<?= $qty ?>">
        <input type="hidden" name="total" id="totalInput" value="<?= $total ?>">

        <div class="actions" style="grid-column:1 / -1">
          <button type="button" class="btn ghost" onclick="location.href='<?= BASE_URL ?>/cliente/index.php'">Volver</button>
          <button type="submit" class="btn primary">Pagar</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    // === Recalcular totales ===
    const PRECIO   = <?= json_encode($precio) ?>;
    const IVA_RATE = <?= json_encode($IVA_RATE) ?>;

    const qty      = document.getElementById('qty');
    const minusBtn = document.getElementById('minusBtn');
    const plusBtn  = document.getElementById('plusBtn');
    const descIn   = document.getElementById('desc');

    const subCell  = document.getElementById('subCell');
    const descCell = document.getElementById('descCell');
    const ivaCell  = document.getElementById('ivaCell');
    const totalCell= document.getElementById('totalCell');

    const totalInp = document.getElementById('totalInput');
    const qtyInp   = document.getElementById('qtyInput');

    function fmt(n){ return '₡' + n.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','); }
    function clampQty(v){ v = parseInt(v || 1, 10); if (isNaN(v)) v=1; return Math.min(99, Math.max(1, v)); }
    function clampDesc(v, max){ v = parseFloat(v||0); if (isNaN(v)) v=0; v = Math.max(0, v); return Math.min(v, max); }

    function recalc(){
      const q     = clampQty(qty.value);
      qty.value   = q;
      const sub   = PRECIO * q;
      const d     = clampDesc(descIn.value, sub);
      descIn.value= d.toFixed(2);

      const base  = sub - d;
      const iva   = Math.round(base * IVA_RATE * 100) / 100;
      const total = Math.round((base + iva) * 100) / 100;

      subCell.textContent   = fmt(sub);
      descCell.textContent  = fmt(d);
      ivaCell.textContent   = fmt(iva);
      totalCell.textContent = fmt(total);

      totalInp.value = total;
      qtyInp.value   = q;
    }

    qty.addEventListener('input', recalc);
    descIn.addEventListener('input', recalc);
    minusBtn.addEventListener('click', ()=>{ qty.value = clampQty(qty.value) - 1; recalc(); });
    plusBtn .addEventListener('click', ()=>{ qty.value = clampQty(qty.value) + 1; recalc(); });

    // Inicial
    recalc();
  </script>
</body>
</html>
