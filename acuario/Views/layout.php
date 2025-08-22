<?php
function h($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
if (!headers_sent()) header('Content-Type: text/html; charset=utf-8');

$currentMod  = $_GET['mod'] ?? 'home';
$showSidebar = ($currentMod !== 'home');  
$usuario     = $_SESSION['nombre'] ?? 'Administrador';
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/public/styles.css">
  <script>
    (function(){
      const saved = localStorage.getItem('theme');
      if(saved === 'dark') document.documentElement.classList.add('dark');
    })();
  </script>
</head>
<body>

  <header class="topbar">
    <div class="container topbar__inner">
      <div class="topbar__right">
       

       
        <div class="welcome">Bienvenido, <?= h($usuario) ?></div>
      </div>

      
<div class="topbar__right">
 
  </button>

  <div class="avatar-menu" id="userMenu">
  <button class="btn pill" type="button" aria-haspopup="true" aria-expanded="false" id="userMenuBtn">
    <?= h($usuario) ?> ▾
  </button>
  <div class="menu" role="menu" aria-labelledby="userMenuBtn">
    
    <a href="<?= BASE_URL ?>/auth/logout.php" role="menuitem">Cerrar sesión</a>
  </div>
</div>

  </header>


  <div class="shell <?= $showSidebar ? '' : 'no-sidebar' ?>">
    <?php if ($showSidebar): ?>
    <aside class="sidebar" id="sidebar" aria-label="Navegación lateral">
      <nav class="sidebar__nav">
        <a class="nav-item <?= $currentMod===''||$currentMod==='home' ? 'active' : '' ?>" href="/index.php"><span>🏠</span> Inicio</a>
        <a class="nav-item <?= $currentMod==='animales' ? 'active' : '' ?>" href="/index.php?mod=animales&action=listarAnimales"><span>🐠</span> Animales</a>
        <a class="nav-item <?= $currentMod==='alimentos' ? 'active' : '' ?>" href="/index.php?mod=alimentos&action=listarMarcas"><span>🧪</span> Alimentos</a>
        <a class="nav-item <?= $currentMod==='colaboradores' ? 'active' : '' ?>" href="/index.php?mod=colaboradores&action=listarColaboradores"><span>👥</span> Colaboradores</a>
        <a class="nav-item <?= $currentMod==='horarios' ? 'active' : '' ?>" href="/index.php?mod=horarios&action=listarHorarios"><span>⏱️</span> Horarios</a>
        <a class="nav-item <?= $currentMod==='inventario' ? 'active' : '' ?>" href="/index.php?mod=inventario&action=listarProductos"><span>📦</span> Inventario</a>
        <a class="nav-item <?= $currentMod==='proveedores' ? 'active' : '' ?>" href="/index.php?mod=proveedores&action=listarProveedores"><span>🏢</span> Proveedores</a>
        <a class="nav-item <?= $currentMod==='clientes' ? 'active' : '' ?>" href="/index.php?mod=clientes&action=listarClientes"><span>🙋</span> Clientes</a>
        <a class="nav-item <?= $currentMod==='entregas' ? 'active' : '' ?>" href="/index.php?mod=entregas&action=listarEntregas"><span>🚚</span> Entregas</a>
        <a class="nav-item <?= $currentMod==='facturas' ? 'active' : '' ?>" href="/index.php?mod=facturas&action=listarFacturas"><span>🧾</span> Facturación</a>
      </nav>
      <button class="sidebar__toggle" id="sidebarToggle" type="button" title="Colapsar/expandir menú">⇤</button>
    </aside>
    <?php endif; ?>

    <main class="content" role="main">
      <?php
        $viewFile = __DIR__ . '/' . ($view ?? '') . '.php';
        if (!empty($view) && is_file($viewFile)) include $viewFile;
        else echo "<div class='card'><p>Vista no encontrada.</p></div>";
      ?>
    </main>
  </div>


  <div id="toast" class="toast" aria-live="polite"></div>

  <script>

    document.getElementById('themeToggle')?.addEventListener('click', (e) => {
      const root = document.documentElement;
      const dark = root.classList.toggle('dark');
      localStorage.setItem('theme', dark ? 'dark' : 'light');
      e.currentTarget.setAttribute('aria-pressed', dark ? 'true' : 'false');
    });

    (function(){
      const sidebar = document.getElementById('sidebar');
      const toggle  = document.getElementById('sidebarToggle');
      if (!sidebar || !toggle) return;
      const key = 'sidebar-collapsed';
      if(localStorage.getItem(key)==='1'){ sidebar.classList.add('collapsed'); }
      toggle.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
        localStorage.setItem(key, sidebar.classList.contains('collapsed') ? '1' : '0');
      });
    })();

    (function(){
      const menu = document.getElementById('userMenu');
      const btn  = document.getElementById('userMenuBtn');
      const panel = menu?.querySelector('.menu');
      if(!btn || !panel) return;
      function close(){ panel.classList.remove('open'); btn.setAttribute('aria-expanded','false'); }
      function open(){ panel.classList.add('open'); btn.setAttribute('aria-expanded','true'); }
      btn.addEventListener('click', (e)=>{ e.stopPropagation(); panel.classList.contains('open') ? close() : open(); });
      document.addEventListener('click', (e)=>{ if(!menu.contains(e.target)) close(); });
      document.addEventListener('keydown', (e)=>{ if(e.key==='Escape') close(); });
    })();

    window.showToast = (msg) => {
      const t = document.getElementById('toast');
      t.textContent = msg; t.classList.add('show');
      setTimeout(() => t.classList.remove('show'), 2200);
    };
  </script>
</body>
</html>
