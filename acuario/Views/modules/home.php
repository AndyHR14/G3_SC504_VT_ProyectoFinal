<link rel="stylesheet" href="public/styles.css">
<div class="container">
  <div class="toolbar">
    <h1>Selecciona un módulo</h1>
  </div>

  <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:16px">
    <a class="card btn btn--primary" style="padding:24px;text-decoration:none;display:flex;flex-direction:column;gap:8px"
       href="index.php?mod=animales&action=listarAnimales">
      <span style="font-size:1.2rem;font-weight:700">🐠 Animales</span>
      <span style="font-weight:500">Gestiona los animales del acuario</span>
    </a>

    <a class="card btn" style="padding:24px;text-decoration:none;display:flex;flex-direction:column;gap:8px"
       href="index.php?mod=alimentos&action=listarMarcas">
      <span style="font-size:1.2rem;font-weight:700">🧪 Alimentos</span>
      <span style="font-weight:500">Gestiona las marcas de alimento</span>
    </a>

    <a class="card btn" style="padding:24px;text-decoration:none;display:flex;flex-direction:column;gap:8px"
       href="index.php?mod=colaboradores&action=listarColaboradores">
      <span style="font-size:1.2rem;font-weight:700">👥 Colaboradores</span>
      <span style="font-weight:500">Gestiona los colaboradores/usuarios</span>
    </a>

    <a class="card btn" style="padding:24px;text-decoration:none;display:flex;flex-direction:column;gap:8px"
      href="index.php?mod=horarios&action=listarHorarios">
      <span style="font-size:1.2rem;font-weight:700">🗓️ Horarios</span>
      <span style="font-weight:500">Gestiona los horarios de colaboradores</span>
    </a>
    
    <a class="card btn" style="padding:24px;text-decoration:none;display:flex;flex-direction:column;gap:8px"
      href="index.php?mod=inventario&action=listarInventario">
      <span style="font-size:1.2rem;font-weight:700">📦 Inventario</span>
      <span style="font-weight:500">Productos, categorías y existencias</span>
    </a>

    <a class="card btn" style="padding:24px;text-decoration:none;display:flex;flex-direction:column;gap:8px"
      href="index.php?mod=proveedores&action=listarProveedores">
      <span style="font-size:1.2rem;font-weight:700">🏷️ Proveedores</span>
      <span style="font-weight:500">Empresas proveedoras</span>
    </a>

    <a class="card btn" style="padding:24px;text-decoration:none;display:flex;flex-direction:column;gap:8px"
      href="index.php?mod=clientes&action=listarClientes">
      <span style="font-size:1.2rem;font-weight:700">👤 Clientes</span>
      <span style="font-weight:500">Gestión de clientes</span>
    </a>

    <a class="card btn" style="padding:24px;text-decoration:none;display:flex;flex-direction:column;gap:8px"
      href="index.php?mod=entregas&action=listarEntregas">
      <span style="font-size:1.2rem;font-weight:700">🚚 Entregas</span>
      <span style="font-weight:500">Gestiona entregas y su detalle</span>
    </a>

    <a class="card btn" style="padding:24px;text-decoration:none;display:flex;flex-direction:column;gap:8px"
      href="index.php?mod=facturas&action=listarFacturas">
      <span style="font-size:1.2rem;font-weight:700">🧾 Facturación</span>
      <span style="font-weight:500">Gestiona facturas y sus detalles</span>
    </a>



  </div>
</div>
