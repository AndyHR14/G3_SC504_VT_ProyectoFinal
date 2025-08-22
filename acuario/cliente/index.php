<?php
// ACUARIO/cliente/index.php
require_once __DIR__ . '/../auth/auth_check_client.php';
require_once __DIR__ . '/../config.php';
$nombre = $_SESSION['user']['nombre'] ?? 'Cliente';

/* Imágenes y productos (usa tus archivos reales) */
$productos = [
  ['nombre' => 'Pecera 60L',        'img' => BASE_URL . '/public/img/productos/pecera60.jpg'],
  ['nombre' => 'Filtro interno',    'img' => BASE_URL . '/public/img/productos/filtro-interno.jpg'],
  ['nombre' => 'Filtro canister',   'img' => BASE_URL . '/public/img/productos/filtro-canister.jpeg'],
  ['nombre' => 'Alimento Bettas',   'img' => BASE_URL . '/public/img/productos/betta-food.png'],
  ['nombre' => 'Alimento Tropical', 'img' => BASE_URL . '/public/img/productos/tropical-food.jpg'],
  ['nombre' => 'Grava blanca',      'img' => BASE_URL . '/public/img/productos/grava-blanca.jpg'],
  ['nombre' => 'Plantas vivas',     'img' => BASE_URL . '/public/img/productos/plantas.jpg'],
  ['nombre' => 'Calentador 100W',    'img' => BASE_URL . '/public/img/productos/calentador-100w.jpg'],
  ['nombre' => 'Bomba de aire',      'img' => BASE_URL . '/public/img/productos/bomba-aire.jpg'],
  ['nombre' => 'Lámpara LED',        'img' => BASE_URL . '/public/img/productos/lampara-led.jpg'],
  ['nombre' => 'Acondicionador agua','img' => BASE_URL . '/public/img/productos/acondicionador-agua.jpg'],
  ['nombre' => 'Red para peces',     'img' => BASE_URL . '/public/img/productos/red-peces.jpg'],
];

$galeria = [
  BASE_URL . '/public/img/galeria/hero.jpg',
  BASE_URL . '/public/img/galeria/1.jpeg',
  BASE_URL . '/public/img/galeria/2.jpg',
  BASE_URL . '/public/img/galeria/3.jpg',
  BASE_URL . '/public/img/galeria/4.jpeg',
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
      --band1:#E7F7FF; --band2:#F0FBFF; --band3:#F6F9FF;
    }
    *{box-sizing:border-box}
    body{margin:0;font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu;background:var(--bg);color:var(--txt)}
    a{text-decoration:none;color:inherit}

    /* Topbar (ya es 100% de ancho) */
    .topbar{background:linear-gradient(90deg,var(--prim),var(--prim2));color:#fff;padding:16px 0}
    .topbar .row{display:flex;align-items:center;justify-content:space-between}

    /* Contenido centrado dentro de bandas full-bleed */
    .container{width:min(96vw, 1500px);margin:0 auto;padding:0 22px}
    @media (min-width:1800px){ .container{width:min(92vw, 1700px);} }

    /* Secciones a todo el ancho (“full-bleed”) */
    .bleed{padding:34px 0}
    .hero-band{background:linear-gradient(180deg, rgba(255,255,255,0) 0%, rgba(14,165,233,0.08) 100%)}
    .band-sobre{background:var(--band3)}
    .band-productos{background:var(--band2)}
    .band-galeria{background:var(--band1)}

    /* Hero */
    .hero h1{font-size:34px;margin:0 0 8px}
    .hero p{color:var(--muted);max-width:900px;margin:0}
    .cta{display:flex;gap:10px;flex-wrap:wrap;margin-top:14px}
    .btn{display:inline-block;background:var(--prim);color:#fff;padding:10px 14px;border-radius:12px;font-weight:700}
    .btn.outline{background:transparent;border:2px solid var(--prim);color:var(--prim)}

    /* Sobre nosotros */
    .about{display:grid;grid-template-columns:1.2fr .8fr;gap:26px;align-items:center}
    .about p{line-height:1.7}
    .muted{color:var(--muted)}
    .about .img{border-radius:18px;overflow:hidden;box-shadow:0 14px 34px rgba(2,132,199,.18)}
    .about img{width:100%;height:100%;object-fit:cover;display:block}

    /* Productos */
    .grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:22px}
    .card{background:#fff;border-radius:16px;box-shadow:0 10px 30px rgba(2,132,199,.12);overflow:hidden;transition:transform .15s ease}
    .card:hover{transform:translateY(-3px)}
    .card img{width:100%;height:200px;object-fit:cover;display:block;background:#ebfaff}
    .card .body{padding:14px}
    .tag{display:inline-block;background:#e0f2fe;color:#075985;padding:4px 8px;border-radius:999px;font-size:12px;font-weight:700}

    /* Galería */
    .gallery{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:14px}
    .gallery img{width:100%;height:200px;object-fit:cover;border-radius:14px;display:block;box-shadow:0 8px 22px rgba(2,132,199,.12)}

    footer{padding:26px 0 42px;color:#475569}
  </style>
</head>
<body>

  <!-- Topbar -->
  <header class="topbar">
    <div class="container row">
      <div><strong>Acuario La Casa del Pez</strong></div>
      <div>
        <span style="margin-right:10px">Hola, <?= htmlspecialchars($nombre,ENT_QUOTES,'UTF-8') ?></span>
        <a class="btn" href="<?= BASE_URL ?>/auth/logout.php">Cerrar sesión</a>
      </div>
    </div>
  </header>

  <!-- HERO (banda full-bleed) -->
  <section class="bleed hero-band">
    <div class="container hero">
      <h1>Bienvenido al área de clientes</h1>
      <p>En <strong>La Casa del Pez</strong> te ayudamos a crear y mantener acuarios saludables.
         Aquí encontrarás información del acuario, nuestros productos y un vistazo a lo que más nos apasiona: el mundo acuático.</p>
      <div class="cta">
        <a class="btn" href="#productos">Ver productos</a>
        <a class="btn outline" href="#sobre">Sobre nosotros</a>
      </div>
    </div>
  </section>

  <!-- SOBRE NOSOTROS (banda full-bleed) -->
  <section id="sobre" class="bleed band-sobre">
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
        <img src="<?= BASE_URL ?>/public/img/galeria/hero.jpg" alt="Acuario La Casa del Pez"
             onerror="this.src='<?= BASE_URL ?>/public/img/galeria/1.jpeg'">
      </div>
    </div>
  </section>

  <!-- PRODUCTOS (banda full-bleed) -->
  <section id="productos" class="bleed band-productos">
    <div class="container">
      <h2>Productos destacados</h2>
      <div class="grid">
        <?php foreach ($productos as $p): ?>
          <article class="card">
            <img src="<?= htmlspecialchars($p['img']) ?>" alt="<?= htmlspecialchars($p['nombre']) ?>"
                 onerror="this.src='<?= BASE_URL ?>/public/img/galeria/hero.jpg'">
            <div class="body">
              <div class="tag">Disponible</div>
              <h3 style="margin:8px 0 0; font-size:18px;"><?= htmlspecialchars($p['nombre']) ?></h3>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- GALERÍA (banda full-bleed) -->
  <section class="bleed band-galeria">
    <div class="container">
      <h2>Galería</h2>
      <div class="gallery">
        <?php foreach ($galeria as $img): ?>
          <img src="<?= htmlspecialchars($img) ?>" alt="Galería"
               onerror="this.src='<?= BASE_URL ?>/public/img/galeria/hero.jpg'">
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <footer>
    <div class="container">
      © <?= date('Y') ?> La Casa del Pez · Atención al cliente:
      <a href="mailto:info@acuario.com">info@acuario.com</a> · Tel. 555-5555
    </div>
  </footer>

</body>
</html>
