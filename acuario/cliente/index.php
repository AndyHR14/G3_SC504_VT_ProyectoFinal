<?php
require_once __DIR__ . '/../auth/auth_check_client.php'; // protege esta página
require_once __DIR__ . '/../config.php';
$nombre = $_SESSION['user']['nombre'] ?? 'Cliente';

// Productos de ejemplo (luego los podemos leer de la BD)
$productos = [
  ['nombre' => 'Pecera 60L',        'img' => BASE_URL . '/public/img/productos/pecera60.jpg'],
  ['nombre' => 'Filtro interno',    'img' => BASE_URL . '/public/img/productos/filtro-interno.jpg'],
  ['nombre' => 'Filtro canister',   'img' => BASE_URL . '/public/img/productos/filtro-canister.jpeg'], // .jpeg
  ['nombre' => 'Alimento Bettas',   'img' => BASE_URL . '/public/img/productos/betta-food.png'],       // .png
  ['nombre' => 'Alimento Tropical', 'img' => BASE_URL . '/public/img/productos/tropical-food.jpg'],
  ['nombre' => 'Grava blanca',      'img' => BASE_URL . '/public/img/productos/grava-blanca.jpg'],
  ['nombre' => 'Plantas vivas',     'img' => BASE_URL . '/public/img/productos/plantas.jpg'],
];

// Galería rápida
$galeria = [
  BASE_URL . '/public/img/galeria/hero.jpg',
  BASE_URL . '/public/img/galeria/1.jpeg', // .jpeg
  BASE_URL . '/public/img/galeria/2.jpg',
  BASE_URL . '/public/img/galeria/3.jpg',
  BASE_URL . '/public/img/galeria/4.jpeg', // .jpeg
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Cliente | Acuario La Casa del Pez</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/styles.css">
  <style>
    :root{
      --prim:#0ea5e9; --prim2:#06b6d4; --txt:#0f172a; --muted:#64748b; --bg:#f6fbff;
    }
    *{box-sizing:border-box}
    body{margin:0;font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu;background:var(--bg);color:var(--txt)}
    a{text-decoration:none;color:inherit}
    .topbar{background:linear-gradient(90deg,var(--prim),var(--prim2));color:#fff;padding:18px 0}
    .container{max-width:1100px;margin:0 auto;padding:0 16px}
    .topbar .row{display:flex;align-items:center;justify-content:space-between}
    .btn{display:inline-block;background:#fff;color:var(--prim);padding:10px 14px;border-radius:12px;font-weight:700}
    .hero{padding:36px 0 10px}
    .hero h1{font-size:32px;margin:0 0 8px}
    .hero p{color:var(--muted);max-width:800px}
    .section{padding:26px 0}
    .section h2{margin:0 0 12px;font-size:26px}
    .about{display:grid;grid-template-columns:1.2fr .8fr;gap:22px}
    .about p{line-height:1.6}
    .about .img{border-radius:16px;overflow:hidden;box-shadow:0 10px 28px rgba(2,132,199,.18)}
    .about img{width:100%;height:100%;object-fit:cover;display:block}
    .grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:18px}
    .card{background:#fff;border-radius:16px;box-shadow:0 10px 30px rgba(2,132,199,.12);overflow:hidden}
    .card img{width:100%;height:160px;object-fit:cover;display:block;background:#ebfaff}
    .card .body{padding:14px}
    .tag{display:inline-block;background:#e0f2fe;color:#075985;padding:4px 8px;border-radius:999px;font-size:12px;font-weight:700}
    .gallery{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px}
    .gallery img{width:100%;height:180px;object-fit:cover;border-radius:14px;display:block}
    footer{padding:22px 0 40px;color:#475569}
    .muted{color:var(--muted)}
    .cta{display:flex;gap:12px;flex-wrap:wrap;margin-top:12px}
    .cta .outline{background:transparent;border:2px solid #fff;color:#fff}
  </style>
</head>
<body>

  <!-- Topbar -->
  <header class="topbar">
    <div class="container">
      <div class="row">
        <div><strong>Acuario La Casa del Pez</strong></div>
        <div>
          <span style="margin-right:10px">Hola, <?= htmlspecialchars($nombre,ENT_QUOTES,'UTF-8') ?></span>
          <a class="btn" href="<?= BASE_URL ?>/auth/logout.php">Cerrar sesión</a>
        </div>
      </div>
    </div>
  </header>

  <!-- Hero -->
  <section class="hero">
    <div class="container">
      <h1>Bienvenido al área de clientes</h1>
      <p>En <strong>La Casa del Pez</strong> te ayudamos a crear y mantener acuarios saludables. Aquí encontrarás
         información del acuario, nuestros productos y un vistazo a lo que más nos apasiona: el mundo acuático.</p>
      <div class="cta">
        <a class="btn" href="#productos">Ver productos</a>
        <a class="btn outline" href="#sobre">Sobre nosotros</a>
      </div>
    </div>
  </section>

  <!-- Sobre nosotros -->
  <section id="sobre" class="section">
    <div class="container about">
      <div>
        <h2>Sobre nosotros</h2>
        <p>
          Somos un equipo de apasionados por la acuariofilia. En <em>La Casa del Pez</em> combinamos
          experiencia, ciencia y cariño por la vida acuática para ofrecerte asesoría, productos y
          mantenimiento de acuarios domésticos y profesionales.
        </p>
        <p class="muted">
          Nuestro compromiso es el bienestar de tus peces y plantas: trabajamos con marcas confiables,
          alimentos de calidad, filtros eficientes y accesorios que facilitan el cuidado del ecosistema.
        </p>
      </div>
      <div class="img">
        <img src="<?= BASE_URL ?>/public/img/galeria/hero.jpg" alt="Acuario La Casa del Pez" onerror="this.src='<?= BASE_URL ?>/public/img/placeholder.png'">
      </div>
    </div>
  </section>

  <!-- Productos -->
  <section id="productos" class="section" style="background:#f0fbff">
    <div class="container">
      <h2>Productos destacados</h2>
      <div class="grid">
        <?php foreach ($productos as $p): ?>
          <article class="card">
            <img src="<?= htmlspecialchars($p['img']) ?>" alt="<?= htmlspecialchars($p['nombre']) ?>" onerror="this.src='<?= BASE_URL ?>/public/img/placeholder.png'">
            <div class="body">
              <div class="tag">Disponible</div>
              <h3 style="margin:8px 0 0; font-size:18px;"><?= htmlspecialchars($p['nombre']) ?></h3>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- Galería -->
  <section class="section">
    <div class="container">
      <h2>Galería</h2>
      <div class="gallery">
        <?php foreach ($galeria as $img): ?>
          <img src="<?= htmlspecialchars($img) ?>" alt="Galería" onerror="this.src='<?= BASE_URL ?>/public/img/placeholder.png'">
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer>
    <div class="container">
      <div class="muted">
        © <?= date('Y') ?> La Casa del Pez · Atención al cliente:
        <a href="mailto:infolacasadelpez@acuario.com">infolacasadelpez@acuario.com</a> · Tel. 555-5555
      </div>
    </div>
  </footer>

</body>
</html>
