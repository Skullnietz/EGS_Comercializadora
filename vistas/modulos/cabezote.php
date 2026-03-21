<!-- ── Estilos del header inyectados aquí para ganar a CUALQUIER skin ── -->
<style>

/* ══════════════════════════════════════════
   NAVBAR — slate/indigo Tailwind
══════════════════════════════════════════ */

/* ── 1. Fondo navbar ── */
.main-header,
.main-header .navbar,
body .main-header .navbar,
body.skin-blue .main-header .navbar,
html body .main-header .navbar {
  background: #1e293b !important;
  border-bottom: none !important;
  box-shadow: 0 1px 0 rgba(255,255,255,.05), 0 4px 16px rgba(0,0,0,.25) !important;
}

/* ── 2. Logo: fondo oscuro + texto ── */
body .main-header .logo,
body.skin-blue .main-header .logo,
html body.skin-blue .main-header .logo {
  background: #0f172a !important;
  border-right: 1px solid rgba(255,255,255,.06) !important;
  border-bottom: none !important;
}
/* Ocultar imágenes viejas del logo pero mantener spans */
.main-header .logo img {
  display: none !important;
}
.main-header .logo .logo-mini,
.main-header .logo .logo-lg {
  display: block !important;
}

/* ── 3. Sidebar toggle (hamburger) ── */
.main-header .sidebar-toggle {
  color: #cbd5e1 !important;
  font-size: 18px !important;
  padding: 15px 18px !important;
  border: none !important;
  background: transparent !important;
  line-height: 20px !important;
  transition: color .15s, background .15s;
}
.main-header .sidebar-toggle:hover {
  color: #fff !important;
  background: rgba(255,255,255,.08) !important;
}

/* ── 4. Iconos navbar derecha: estilo píldora circular ── */
.navbar-custom-menu .navbar-nav > li > a {
  color: #94a3b8 !important;
  padding: 15px 14px !important;
  font-size: 16px !important;
  transition: color .15s, background .15s;
  position: relative;
}
.navbar-custom-menu .navbar-nav > li > a:hover,
.navbar-custom-menu .navbar-nav > li > a:focus {
  color: #e2e8f0 !important;
  background: rgba(255,255,255,.08) !important;
}

/* Ícono de notificación: campana con efecto */
.navbar-custom-menu .notifications-menu > a > .fa-bell,
.navbar-custom-menu .notifications-menu > a > .fas {
  font-size: 16px !important;
}

/* Ícono FAQ: signo de interrogación */
.navbar-custom-menu .faq-menu > a > i {
  font-size: 16px !important;
}

/* Badge de notificaciones */
.navbar-custom-menu .label-warning {
  background: #ef4444 !important;
  font-size: 10px !important;
  padding: 2px 6px !important;
  border-radius: 9999px !important;
  font-weight: 700 !important;
  position: absolute !important;
  top: 8px !important;
  right: 6px !important;
  line-height: 1.2 !important;
  box-shadow: 0 0 0 2px #1e293b !important;
  min-width: 18px !important;
  text-align: center !important;
}

/* Dropdown de notificaciones */
.navbar-custom-menu .notifications-menu .dropdown-menu {
  border: 1px solid #e2e8f0 !important;
  border-radius: 10px !important;
  box-shadow: 0 10px 40px rgba(15,23,42,.16) !important;
  overflow: hidden !important;
}
.navbar-custom-menu .notifications-menu .dropdown-menu > li.header {
  background: #f8fafc !important;
  color: #475569 !important;
  border-bottom: 1px solid #e2e8f0 !important;
  font-size: 12px !important;
  font-weight: 600 !important;
  padding: 10px 14px !important;
}
.navbar-custom-menu .notifications-menu .menu > li > a {
  font-size: 12.5px !important;
  color: #334155 !important;
  padding: 8px 14px !important;
  border-bottom: 1px solid #f1f5f9 !important;
  transition: background .12s !important;
}
.navbar-custom-menu .notifications-menu .menu > li > a:hover {
  background: #f1f5f9 !important;
}
.navbar-custom-menu .notifications-menu .menu > li > a > i {
  color: #6366f1 !important;
  margin-right: 6px !important;
}

/* ── 5. Separador vertical entre iconos nav ── */
.navbar-custom-menu .navbar-nav > li {
  border-left: 1px solid rgba(255,255,255,.06);
}
.navbar-custom-menu .navbar-nav > li:first-child {
  border-left: none;
}

/* ══════════════════════════════════════════
   USER MENU — trigger + dropdown
══════════════════════════════════════════ */

/* ── Trigger pill ── */
.main-header .user-menu > a.egs-trigger {
  display: flex !important;
  align-items: center !important;
  gap: 9px !important;
  padding: 7px 12px !important;
  margin: 7px 8px !important;
  border-radius: 8px !important;
  color: #e2e8f0 !important;
  background: rgba(255,255,255,.05) !important;
  border: 1px solid rgba(255,255,255,.10) !important;
  transition: background .15s, border-color .15s !important;
  text-decoration: none !important;
}
.main-header .user-menu > a.egs-trigger:hover {
  background: rgba(255,255,255,.10) !important;
  border-color: rgba(255,255,255,.20) !important;
}
.egs-trigger .egs-av {
  width: 30px !important;
  height: 30px !important;
  border-radius: 50% !important;
  object-fit: cover;
  border: 2px solid rgba(255,255,255,.20) !important;
  display: block;
  flex-shrink: 0;
}
.egs-trigger .egs-name {
  font-size: 13px;
  font-weight: 600;
  color: #e2e8f0;
  max-width: 120px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.egs-trigger .egs-caret {
  font-size: 10px !important;
  color: #64748b;
  transition: transform .2s;
  flex-shrink: 0;
}
.user-menu.open > a.egs-trigger .egs-caret { transform: rotate(180deg); }

/* ── Dropdown panel ── */
.egs-drop {
  width: 240px !important;
  padding: 0 !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 12px !important;
  box-shadow: 0 10px 40px rgba(15,23,42,.18), 0 2px 8px rgba(15,23,42,.08) !important;
  overflow: hidden !important;
  margin-top: 8px !important;
  right: 0 !important;
  left: auto !important;
  background: #fff !important;
}
.egs-drop-user {
  padding: 16px !important;
  background: #f8fafc !important;
  border-bottom: 1px solid #e2e8f0 !important;
  display: flex !important;
  align-items: center !important;
  gap: 12px !important;
  min-height: auto !important;
}
.egs-drop-user .egs-av-lg {
  width: 40px !important;
  height: 40px !important;
  border-radius: 50% !important;
  object-fit: cover;
  border: 2px solid #e2e8f0 !important;
  flex-shrink: 0;
}
.egs-drop-user .egs-user-info { flex: 1; min-width: 0; }
.egs-drop-user .egs-user-info strong {
  display: block;
  font-size: 13px;
  font-weight: 700;
  color: #0f172a;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.egs-drop-user .egs-user-info span {
  display: inline-block;
  margin-top: 3px;
  font-size: 11px;
  font-weight: 500;
  color: #64748b;
  background: #e2e8f0;
  padding: 1px 7px;
  border-radius: 9999px;
  text-transform: capitalize;
}
.egs-drop-user .egs-dot {
  width: 8px; height: 8px;
  border-radius: 50%;
  background: #22c55e;
  flex-shrink: 0;
}
.egs-drop-nav {
  list-style: none !important;
  margin: 0 !important;
  padding: 6px 0 !important;
  background: #fff !important;
}
.egs-drop-nav > li > a {
  display: flex !important;
  align-items: center !important;
  gap: 10px !important;
  padding: 9px 16px !important;
  font-size: 13px !important;
  font-weight: 500 !important;
  color: #334155 !important;
  text-decoration: none !important;
  border: none !important;
  background: transparent !important;
  transition: background .12s !important;
}
.egs-drop-nav > li > a i {
  width: 16px;
  text-align: center;
  font-size: 13px;
  color: #94a3b8;
  flex-shrink: 0;
  transition: color .12s;
}
.egs-drop-nav > li > a:hover {
  background: #f1f5f9 !important;
  color: #0f172a !important;
}
.egs-drop-nav > li > a:hover i { color: #334155; }
.egs-drop-divider {
  margin: 4px 0 !important;
  border-top: 1px solid #f1f5f9 !important;
}
.egs-drop-foot {
  padding: 8px 10px 10px !important;
  background: #fff !important;
  border-top: 1px solid #f1f5f9 !important;
}
.egs-drop-logout {
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  gap: 8px !important;
  width: 100% !important;
  padding: 8px 12px !important;
  border-radius: 8px !important;
  border: 1px solid #fecdd3 !important;
  background: #fff !important;
  color: #e11d48 !important;
  font-size: 13px !important;
  font-weight: 600 !important;
  text-decoration: none !important;
  transition: background .12s, border-color .12s !important;
}
.egs-drop-logout:hover {
  background: #fff1f2 !important;
  border-color: #fda4af !important;
  color: #be123c !important;
  text-decoration: none !important;
}

/* ══════════════════════════════════════════
   RANKING BADGE — corona/medalla sobre avatar
══════════════════════════════════════════ */

/* Wrapper para posicionar badge sobre avatar */
.egs-av-wrap {
  position: relative;
  display: inline-block;
  flex-shrink: 0;
}

/* ── Icono flotante sobre avatar (trigger navbar) ── */
.egs-rank-crown {
  position: absolute;
  top: -8px;
  left: 50%;
  transform: translateX(-50%);
  font-size: 12px;
  filter: drop-shadow(0 1px 3px rgba(0,0,0,.4));
  z-index: 2;
  line-height: 1;
  pointer-events: none;
}
.egs-rank-pos1 { color: #fbbf24; animation: egs-shine-gold 2.5s ease-in-out infinite; }
.egs-rank-pos2 { color: #a8b4c4; animation: egs-shine-silver 2.5s ease-in-out infinite; }
.egs-rank-pos3 { color: #d97706; animation: egs-shine-bronze 2.5s ease-in-out infinite; }

@keyframes egs-shine-gold {
  0%, 100% { color: #fbbf24; filter: drop-shadow(0 1px 3px rgba(0,0,0,.4)); }
  50%      { color: #fde68a; filter: drop-shadow(0 0 8px rgba(251,191,36,.6)); }
}
@keyframes egs-shine-silver {
  0%, 100% { color: #a8b4c4; filter: drop-shadow(0 1px 3px rgba(0,0,0,.4)); }
  50%      { color: #e2e8f0; filter: drop-shadow(0 0 8px rgba(148,163,184,.6)); }
}
@keyframes egs-shine-bronze {
  0%, 100% { color: #d97706; filter: drop-shadow(0 1px 3px rgba(0,0,0,.4)); }
  50%      { color: #fbbf24; filter: drop-shadow(0 0 8px rgba(217,119,6,.6)); }
}

/* ── Número circular sobre avatar (dropdown) ── */
.egs-rank-badge-num {
  position: absolute;
  bottom: -2px;
  right: -4px;
  width: 18px;
  height: 18px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 10px;
  font-weight: 800;
  color: #fff;
  z-index: 3;
  border: 2px solid #f8fafc;
  line-height: 1;
}
.egs-rank-badge-pos1 { background: linear-gradient(135deg, #f59e0b, #eab308); }
.egs-rank-badge-pos2 { background: linear-gradient(135deg, #94a3b8, #64748b); }
.egs-rank-badge-pos3 { background: linear-gradient(135deg, #d97706, #b45309); }

/* ── Sección de logros en dropdown ── */
.egs-drop-achievements {
  padding: 0 !important;
  border-bottom: 1px solid #e2e8f0 !important;
  background: #fff !important;
  list-style: none !important;
}
.egs-ach-header {
  padding: 8px 14px 4px;
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: .5px;
  color: #94a3b8;
  display: flex;
  align-items: center;
  gap: 5px;
}
.egs-ach-header i {
  font-size: 11px;
  color: #c084fc;
}

/* Fila de logro */
.egs-ach-row {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 5px 14px 7px;
  margin: 0 8px 6px;
  border-radius: 8px;
  transition: transform .12s;
}
.egs-ach-row:hover { transform: translateX(2px); }

/* Colores de fondo por tier */
.egs-ach-gold   { background: linear-gradient(135deg, #fffbeb, #fef3c7); }
.egs-ach-silver { background: linear-gradient(135deg, #f8fafc, #e2e8f0); }
.egs-ach-bronze { background: linear-gradient(135deg, #fffbeb, #fed7aa); }

/* Número de posición (circulito) */
.egs-ach-pos {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 10px;
  font-weight: 800;
  color: #fff;
  flex-shrink: 0;
}
.egs-ach-gold .egs-ach-pos   { background: linear-gradient(135deg, #f59e0b, #eab308); }
.egs-ach-silver .egs-ach-pos { background: linear-gradient(135deg, #94a3b8, #64748b); }
.egs-ach-bronze .egs-ach-pos { background: linear-gradient(135deg, #d97706, #b45309); }

/* Icono */
.egs-ach-icon {
  font-size: 13px;
  flex-shrink: 0;
}
.egs-ach-gold .egs-ach-icon   { color: #f59e0b; }
.egs-ach-silver .egs-ach-icon { color: #94a3b8; }
.egs-ach-bronze .egs-ach-icon { color: #d97706; }

/* Texto */
.egs-ach-text {
  font-size: 12px;
  font-weight: 600;
  white-space: nowrap;
}
.egs-ach-gold .egs-ach-text   { color: #92400e; }
.egs-ach-silver .egs-ach-text { color: #475569; }
.egs-ach-bronze .egs-ach-text { color: #7c2d12; }

/* ── Sidebar: estilos en lateral/menu.php ── */

/* ══════════════════════════════════════════
   LOGO TEXTO
══════════════════════════════════════════ */
html body .main-header .logo .egs-logo-text,
body.skin-blue .main-header .logo .egs-logo-text {
  color: #f1f5f9 !important;
  font-size: 22px !important;
  font-weight: 900 !important;
  letter-spacing: 2px !important;
  text-decoration: none !important;
  font-family: 'Segoe UI', system-ui, -apple-system, sans-serif !important;
  text-shadow: 0 1px 2px rgba(0,0,0,.3);
}
html body .main-header .logo .egs-logo-text .egs-logo-dot {
  color: #6366f1;
  font-weight: 900;
}
/* Mini sidebar: logo más compacto */
body.sidebar-collapse .main-header .logo .egs-logo-text {
  font-size: 18px !important;
  letter-spacing: 1px !important;
}

/* ══════════════════════════════════════════
   BARRA DE BÚSQUEDA GLOBAL
══════════════════════════════════════════ */
.egs-navbar-search {
  float: left;
  display: flex;
  align-items: center;
  padding: 10px 0 10px 8px;
  max-width: 380px;
  flex: 1;
}
.egs-navbar-search .egs-search-box {
  display: flex;
  align-items: center;
  gap: 8px;
  width: 100%;
  background: rgba(255,255,255,.07);
  border: 1px solid rgba(255,255,255,.10);
  border-radius: 8px;
  padding: 6px 12px;
  transition: background .15s, border-color .2s, box-shadow .2s;
  cursor: text;
}
.egs-navbar-search .egs-search-box:hover {
  background: rgba(255,255,255,.10);
  border-color: rgba(255,255,255,.15);
}
.egs-navbar-search .egs-search-box:focus-within {
  background: rgba(255,255,255,.12);
  border-color: #6366f1;
  box-shadow: 0 0 0 3px rgba(99,102,241,.15);
}
.egs-navbar-search .egs-search-icon {
  color: #64748b;
  font-size: 13px;
  flex-shrink: 0;
  transition: color .15s;
}
.egs-navbar-search .egs-search-box:focus-within .egs-search-icon {
  color: #6366f1;
}
.egs-navbar-search .egs-search-input {
  border: none !important;
  outline: none !important;
  background: transparent !important;
  color: #e2e8f0 !important;
  font-size: 13px !important;
  font-weight: 400 !important;
  width: 100% !important;
  padding: 0 !important;
  margin: 0 !important;
  box-shadow: none !important;
  line-height: 1.5 !important;
}
.egs-navbar-search .egs-search-input::placeholder {
  color: #64748b;
  font-weight: 400;
}
.egs-navbar-search .egs-search-kbd {
  display: inline-flex;
  align-items: center;
  gap: 2px;
  padding: 1px 6px;
  border-radius: 4px;
  background: rgba(255,255,255,.08);
  border: 1px solid rgba(255,255,255,.12);
  color: #475569;
  font-size: 10px;
  font-family: inherit;
  font-weight: 600;
  flex-shrink: 0;
  line-height: 1.6;
}

/* ── Search Results Dropdown ── */
.egs-search-results {
  display: none;
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  margin-top: 6px;
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  box-shadow: 0 10px 40px rgba(15,23,42,.16);
  z-index: 9999;
  max-height: 320px;
  overflow-y: auto;
  padding: 6px 0;
}
.egs-search-results.active {
  display: block;
}
.egs-search-results .egs-sr-group {
  padding: 6px 12px 3px;
  font-size: 10px;
  font-weight: 700;
  color: #94a3b8;
  text-transform: uppercase;
  letter-spacing: .05em;
}
.egs-search-results .egs-sr-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 8px 12px;
  color: #334155;
  text-decoration: none;
  font-size: 13px;
  transition: background .1s;
  cursor: pointer;
}
.egs-search-results .egs-sr-item:hover {
  background: #f1f5f9;
}
.egs-search-results .egs-sr-item i {
  color: #94a3b8;
  width: 16px;
  text-align: center;
  font-size: 13px;
}
.egs-search-results .egs-sr-empty {
  padding: 16px;
  text-align: center;
  color: #94a3b8;
  font-size: 13px;
}

/* Responsive: ocultar search en mobile */
@media (max-width: 767px) {
  .egs-navbar-search { display: none; }
}

</style>

<!-- main-header -->
<header class="main-header">

  <!-- Logo texto -->
  <a href="inicio" class="logo">
    <span class="logo-mini"><span class="egs-logo-text">E<span class="egs-logo-dot">.</span></span></span>
    <span class="logo-lg"><span class="egs-logo-text">EGS<span class="egs-logo-dot">.</span></span></span>
  </a>

  <!-- navbar -->
  <nav class="navbar navbar-static-top" role="navigation">

    <!-- Sidebar toggle -->
    <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
      <i class="fa-solid fa-bars"></i>
      <span class="sr-only">Toggle navigation</span>
    </a>

    <!-- Search bar global -->
    <div class="egs-navbar-search" style="position:relative">
      <div class="egs-search-box" id="egsSearchBox">
        <i class="fa-solid fa-magnifying-glass egs-search-icon"></i>
        <input type="text"
               class="egs-search-input"
               id="egsSearchInput"
               placeholder="Buscar órdenes, clientes, productos..."
               autocomplete="off">
        <kbd class="egs-search-kbd hidden-xs">Ctrl K</kbd>
      </div>
      <!-- Resultados -->
      <div class="egs-search-results" id="egsSearchResults"></div>
    </div>

    <!-- Right side -->
    <div class="navbar-custom-menu">
      <ul class="nav navbar-nav">
        <?php
          include "cabezote/faq.php";
          include "cabezote/notificaciones.php";
          include "cabezote/usuario.php";
        ?>
      </ul>
    </div>

  </nav>

</header>
<!-- /main-header -->

<!-- ═══ JS: Búsqueda global del navbar ═══ -->
<script>
(function(){
  var $input   = $('#egsSearchInput');
  var $results = $('#egsSearchResults');
  var timer    = null;

  // Atajos rápidos (navegación directa) — no requiere backend
  var quickLinks = [
    { label: 'Tablero',              icon: 'fa-solid fa-home',               url: 'index.php?ruta=inicio',      tags: 'inicio dashboard home tablero' },
    { label: 'Gestor Productos',     icon: 'fab fa-product-hunt',            url: 'index.php?ruta=productos',   tags: 'productos inventario stock' },
    { label: 'Gestor Almacenes',     icon: 'fa-solid fa-warehouse',          url: 'index.php?ruta=almacenes',   tags: 'almacenes bodega almacen' },
    { label: 'Gestor Ventas',        icon: 'fa-solid fa-shopping-cart',      url: 'index.php?ruta=creararventa',tags: 'ventas vender factura' },
    { label: 'Gestor Ventas R',      icon: 'fa-solid fa-cart-arrow-down',    url: 'index.php?ruta=ventasR',     tags: 'ventas reporte' },
    { label: 'Gestor Pedidos',       icon: 'fa-solid fa-box-open',           url: 'index.php?ruta=pedidos',     tags: 'pedidos envios paquetes' },
    { label: 'Órdenes de Servicio',  icon: 'fa-solid fa-clipboard-list',     url: 'index.php?ruta=ordenes',     tags: 'ordenes servicio reparacion' },
    { label: 'Todas las Órdenes',    icon: 'fa-solid fa-file-alt',           url: 'index.php?ruta=ordenesnew',  tags: 'ordenes todas lista' },
    { label: 'Comisiones',           icon: 'fa-solid fa-coins',              url: 'index.php?ruta=comisiones',  tags: 'comisiones pago tecnicos' },
    { label: 'Clientes',             icon: 'fa-solid fa-users',              url: 'index.php?ruta=clientes',    tags: 'clientes contactos personas' },
    { label: 'Corte de Caja',        icon: 'fa-solid fa-cash-register',      url: 'index.php?ruta=CorteTotal',  tags: 'corte caja efectivo dinero cierre' },
    { label: 'Cotización',           icon: 'fa-solid fa-file-invoice-dollar', url: 'index.php?ruta=cotizacion', tags: 'cotizacion presupuesto precio' },
    { label: 'Citas',                icon: 'fa-regular fa-calendar-alt',     url: 'index.php?ruta=listacitas',  tags: 'citas agenda calendario' },
    { label: 'Técnicos',             icon: 'fa-solid fa-user-cog',           url: 'index.php?ruta=tecnicos',    tags: 'tecnicos personal equipo' },
    { label: 'Asesores de Ventas',   icon: 'fa-solid fa-user-tie',           url: 'index.php?ruta=asesores',    tags: 'asesores vendedores personal' },
    { label: 'Perfiles',             icon: 'fa-solid fa-key',                url: 'index.php?ruta=perfiles',    tags: 'perfiles cuenta usuario configuracion' },
    { label: 'Empresas',             icon: 'fa-solid fa-building',           url: 'index.php?ruta=empresas',    tags: 'empresas compañia' },
    { label: 'Metas / CRM',          icon: 'fa-solid fa-handshake',          url: 'index.php?ruta=metas',       tags: 'metas crm objetivos' },
    { label: 'Ayuda',                icon: 'fa-regular fa-circle-question',  url: 'index.php?ruta=preguntas',   tags: 'ayuda faq preguntas soporte' }
  ];

  function search(query) {
    if (!query || query.length < 2) {
      $results.removeClass('active').empty();
      return;
    }
    var q = query.toLowerCase();
    var matches = quickLinks.filter(function(item) {
      return item.label.toLowerCase().indexOf(q) > -1 ||
             item.tags.indexOf(q) > -1;
    });

    var html = '';
    if (matches.length) {
      html += '<div class="egs-sr-group">Ir a</div>';
      for (var i = 0; i < Math.min(matches.length, 8); i++) {
        html += '<a class="egs-sr-item" href="' + matches[i].url + '">' +
                '<i class="' + matches[i].icon + '"></i>' +
                matches[i].label + '</a>';
      }
    } else {
      html = '<div class="egs-sr-empty"><i class="fa-solid fa-magnifying-glass" style="margin-right:6px;opacity:.5"></i>Sin resultados para "' + query + '"</div>';
    }
    $results.html(html).addClass('active');
  }

  // Eventos
  $input.on('input', function() {
    clearTimeout(timer);
    var val = $(this).val().trim();
    timer = setTimeout(function() { search(val); }, 150);
  });

  $input.on('focus', function() {
    if ($(this).val().trim().length >= 2) search($(this).val().trim());
  });

  // Cerrar al click fuera
  $(document).on('click', function(e) {
    if (!$(e.target).closest('.egs-navbar-search').length) {
      $results.removeClass('active');
    }
  });

  // Atajo Ctrl+K
  $(document).on('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
      e.preventDefault();
      $input.focus();
    }
    if (e.key === 'Escape') {
      $results.removeClass('active');
      $input.blur();
    }
  });

})();
</script>

<?php if (isset($_SESSION['perfil']) && $_SESSION['perfil'] === 'administrador'): ?>
<!-- ══════════════════════════════════════════════════════
     MODAL: Confirmar eliminación de orden con contraseña
     Solo se muestra a administradores
══════════════════════════════════════════════════════ -->
<div class="modal fade" id="egsModalEliminarOrden" tabindex="-1" role="dialog" aria-labelledby="egsModalEliminarLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm" role="document" style="margin-top:120px">
    <div class="modal-content" style="border-radius:12px;overflow:hidden;border:none;box-shadow:0 20px 60px rgba(0,0,0,.3)">

      <!-- Header -->
      <div class="modal-header" style="background:linear-gradient(135deg,#ef4444,#b91c1c);border:none;padding:16px 20px">
        <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:.9;font-size:22px;margin-top:-4px">&times;</button>
        <h4 class="modal-title" id="egsModalEliminarLabel" style="color:#fff;font-weight:700;font-size:15px">
          <i class="fa-solid fa-triangle-exclamation" style="margin-right:6px"></i>
          Eliminar Orden
        </h4>
      </div>

      <!-- Body -->
      <div class="modal-body" style="padding:20px">
        <div style="text-align:center;margin-bottom:14px">
          <div style="width:56px;height:56px;border-radius:50%;background:#fef2f2;display:flex;align-items:center;justify-content:center;margin:0 auto 10px;border:2px solid #fecaca">
            <i class="fa-solid fa-trash" style="font-size:22px;color:#ef4444"></i>
          </div>
          <p style="font-size:13px;color:#374151;margin:0">
            Esta acción es <strong>irreversible</strong>.<br>
            Orden: <strong id="egsDelOrdenId" style="color:#ef4444">#—</strong>
          </p>
        </div>

        <div class="form-group" style="margin-bottom:10px">
          <label style="font-size:12px;font-weight:600;color:#374151;margin-bottom:5px;display:block">
            <i class="fa-solid fa-lock" style="margin-right:4px;color:#6b7280"></i>
            Contraseña de administrador
          </label>
          <div style="position:relative">
            <input type="password"
                   id="egsDelPassword"
                   class="form-control"
                   placeholder="Tu contraseña"
                   style="padding-right:40px;border-radius:8px;border:1px solid #d1d5db;font-size:13px"
                   autocomplete="off">
            <i class="fa-regular fa-eye" id="egsDelTogglePass"
               style="position:absolute;right:12px;top:50%;transform:translateY(-50%);cursor:pointer;color:#9ca3af;font-size:13px"></i>
          </div>
        </div>

        <div id="egsDelMensaje" style="display:none;font-size:12px;padding:7px 10px;border-radius:6px;margin-top:8px"></div>
      </div>

      <!-- Footer -->
      <div class="modal-footer" style="border-top:1px solid #f3f4f6;padding:12px 20px;display:flex;justify-content:space-between">
        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-danger btn-sm" id="egsDelConfirmar" style="border-radius:6px;font-weight:600">
          <i class="fa-solid fa-trash" style="margin-right:4px"></i>Eliminar
        </button>
      </div>

    </div>
  </div>
</div>

<script>
/* ── Modal de eliminación con contraseña ── */
(function(){
  var _idOrden = 0, _imgP = '', _imgPort = '';

  // Toggle ver/ocultar contraseña
  $('#egsDelTogglePass').on('click', function(){
    var $inp = $('#egsDelPassword');
    var tipo = $inp.attr('type') === 'password' ? 'text' : 'password';
    $inp.attr('type', tipo);
    $(this).toggleClass('fa-eye fa-eye-slash');
  });

  // Función global para abrir el modal
  window.egsEliminarOrden = function(idOrden, imgPrincipal, imgPortada) {
    _idOrden  = idOrden;
    _imgP     = imgPrincipal  || '';
    _imgPort  = imgPortada    || '';
    $('#egsDelOrdenId').text('#' + idOrden);
    $('#egsDelPassword').val('');
    $('#egsDelMensaje').hide().text('');
    $('#egsDelConfirmar').prop('disabled', false).html('<i class="fa-solid fa-trash" style="margin-right:4px"></i>Eliminar');
    $('#egsModalEliminarOrden').modal('show');
    setTimeout(function(){ $('#egsDelPassword').focus(); }, 400);
  };

  // Confirmar con Enter
  $('#egsDelPassword').on('keydown', function(e){
    if (e.key === 'Enter') $('#egsDelConfirmar').click();
  });

  // Botón Eliminar
  $('#egsDelConfirmar').on('click', function(){
    var pass = $('#egsDelPassword').val().trim();
    if (!pass) {
      mostrarMsg('Ingresa tu contraseña.', 'warning');
      return;
    }

    var $btn = $(this);
    $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin" style="margin-right:4px"></i>Verificando...');

    var fd = new FormData();
    fd.append('egsAdminPass',    pass);
    fd.append('egsOrdenId',      _idOrden);
    fd.append('egsImgPrincipal', _imgP);
    fd.append('egsImgPortada',   _imgPort);

    $.ajax({
      url:         'ajax/verificarAdminPassword.ajax.php',
      method:      'POST',
      data:        fd,
      contentType: false,
      processData: false,
      dataType:    'json',
      success: function(r) {
        if (r && r.ok) {
          $btn.html('<i class="fa-solid fa-check" style="margin-right:4px"></i>Verificado…');
          mostrarMsg('Contraseña correcta. Eliminando…', 'success');
          setTimeout(function(){ window.location = r.redirect; }, 700);
        } else {
          mostrarMsg(r.msg || 'Contraseña incorrecta.', 'danger');
          $btn.prop('disabled', false).html('<i class="fa-solid fa-trash" style="margin-right:4px"></i>Eliminar');
          $('#egsDelPassword').val('').focus();
        }
      },
      error: function() {
        mostrarMsg('Error de conexión. Intenta de nuevo.', 'danger');
        $btn.prop('disabled', false).html('<i class="fa-solid fa-trash" style="margin-right:4px"></i>Eliminar');
      }
    });
  });

  function mostrarMsg(texto, tipo) {
    var colores = {
      success: { bg: '#f0fdf4', color: '#166534', border: '#bbf7d0' },
      danger:  { bg: '#fef2f2', color: '#991b1b', border: '#fecaca' },
      warning: { bg: '#fffbeb', color: '#92400e', border: '#fde68a' }
    };
    var c = colores[tipo] || colores.warning;
    $('#egsDelMensaje')
      .css({ background: c.bg, color: c.color, border: '1px solid ' + c.border })
      .text(texto)
      .show();
  }

})();
</script>
<?php endif; ?>
