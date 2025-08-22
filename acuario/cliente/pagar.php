<?php
require_once __DIR__ . '/../auth/auth_check_client.php';
require_once __DIR__ . '/../config.php';

$catalogo = require __DIR__ . '/catalogo.php';

$id      = (int)($_POST['id'] ?? 0);
$fac     = $_POST['fac'] ?? '';
$qty     = max(1, (int)($_POST['qty'] ?? 1));
$descCli = max(0.0, (float)($_POST['desc'] ?? 0.0));
$metodo  = $_POST['metodo'] ?? 'efectivo';

$METODOS = [
  'efectivo'      => 'Efectivo #1',
  'sinpe'         => 'Sinpe Móvil #4',
  'transferencia' => 'Transferencia Bancaria #3',
  'tarjeta'       => 'Tarjeta #2',
];
$metodoLabel = $METODOS[$metodo] ?? $METODOS['efectivo'];

$precio = isset($catalogo[$id]) ? (float)$catalogo[$id]['precio'] : 0.0;
$nombreProd = $catalogo[$id]['nombre'] ?? 'Producto';

// Recalcular del lado servidor (seguro)
$IVA_RATE = 0.13;
$sub      = $precio * $qty;
$desc     = min($descCli, $sub);               // tope máximo
$base     = $sub - $desc;
$iva      = round($base * $IVA_RATE, 2);
$total    = round($base + $iva, 2);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Pago</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    :root{ --prim:#0ea5e9; --ok:#10b981; --txt:#0f172a; --muted:#6b7280; --bg:#f3f8fc; }
    body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Arial,sans-serif;background:var(--bg);margin:0;color:var(--txt)}
    .wrap{max-width:720px;margin:40px auto;padding:24px}
    .card{background:#fff;border-radius:16px;box-shadow:0 10px 25px rgba(0,0,0,.06);padding:24px;text-align:center}
    h1{margin-top:0}
    .ok{display:inline-block;margin:16px auto 6px;padding:10px 14px;border-radius:12px;background:var(--ok);color:#fff;font-weight:700}
    .muted{color:#6b7280}
    a.btn{display:inline-block;margin-top:16px;padding:.7rem 1.1rem;border-radius:12px;background:#0ea5e9;color:#fff;text-decoration:none;font-weight:600}

    /* Toast */
    .toast{position:fixed; top:22px; right:22px; z-index:9999; display:flex; align-items:center; gap:10px;
      background:#fff; color:#0b3b2e; border-left:6px solid var(--ok); padding:12px 14px; border-radius:14px;
      box-shadow:0 12px 30px rgba(0,0,0,.12); min-width:260px; opacity:0; transform:translateY(-16px);
      pointer-events:none; transition:all .25s ease;}
    .toast.show{opacity:1; transform:translateY(0); pointer-events:auto;}
    .toast .icon{width:26px; height:26px; display:grid; place-items:center; background:rgba(16,185,129,.15); color:var(--ok); border-radius:999px; font-weight:900;}
    .toast .txt{line-height:1.15}
    .toast .txt b{display:block}
    .toast .close{margin-left:auto; background:transparent; border:0; font-size:18px; cursor:pointer; color:#334155;}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="card">
      <h1>Pago exitoso</h1>
      <div class="ok">¡Gracias por tu compra!</div>
      <p class="muted">
        Factura: <?= htmlspecialchars($fac ?: '—') ?><br>
        Producto: <?= htmlspecialchars($nombreProd) ?><br>
        Cantidad: <?= (int)$qty ?><br>
        Método: <?= htmlspecialchars($metodoLabel) ?><br>
        Descuento: ₡<?= number_format($desc, 2) ?><br>
        Total pagado: ₡<?= number_format($total, 2) ?>
      </p>
      <a class="btn" href="<?= BASE_URL ?>/cliente/index.php">Volver al catálogo</a>
    </div>
  </div>

  <!-- Toast -->
  <div id="toast" class="toast" role="status" aria-live="polite" aria-atomic="true">
    <div class="icon">✓</div>
    <div class="txt">
      <b>Pago exitoso</b>
      <small class="muted">
        <?= htmlspecialchars($metodoLabel) ?> · Cant. <?= (int)$qty ?> · ₡<?= number_format($total, 2) ?>
      </small>
    </div>
    <button class="close" aria-label="Cerrar">&times;</button>
  </div>

  <script>
    const toast = document.getElementById('toast');
    const closeBtn = toast.querySelector('.close');
    const REDIRECT_MS = 2600;
    function hideToast(){ toast.classList.remove('show'); }
    function goHome(){ window.location.href = "<?= BASE_URL ?>/cliente/index.php"; }
    document.addEventListener('DOMContentLoaded', () => {
      toast.classList.add('show');
      setTimeout(hideToast, REDIRECT_MS - 300);
      setTimeout(goHome, REDIRECT_MS);
    });
    closeBtn.addEventListener('click', () => { hideToast(); goHome(); });
  </script>
</body>
</html>
