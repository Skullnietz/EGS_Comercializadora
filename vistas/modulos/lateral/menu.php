<!-- ══ Sidebar: estilos nucleares — gana a skin-blue.min.css ══ -->
<style>
/*
 * "html body.skin-blue" = especificidad 0,1,1 + hijos
 * siempre mayor que ".skin-blue .xxx" (0,1,0 + hijos)
 * + !important como seguro final.
 */

/* ── 1. Fondo sidebar SOLO (NO wrapper) ── */
html body.skin-blue .main-sidebar,
html body.skin-blue .left-side,
html body.skin-blue .main-sidebar .sidebar {
  background-color: #0f172a !important;
  background: #0f172a !important;
}

/* ── 2. User panel ── */
html body.skin-blue .user-panel > .info,
html body.skin-blue .user-panel > .info > a {
  color: #cbd5e1 !important;
  font-size: 11px !important;
}
html body.skin-blue .user-panel > .info > p,
html body.skin-blue .egs-sidebar-user .pull-left.info p {
  color: #f1f5f9 !important;
  font-weight: 600 !important;
  font-size: 13px !important;
  margin-bottom: 2px !important;
}
html body.skin-blue .egs-sidebar-user {
  border-bottom: 1px solid rgba(255,255,255,.10) !important;
  padding: 12px 14px !important;
  margin-bottom: 4px !important;
}
html body.skin-blue .egs-sidebar-user .pull-left.image img {
  width: 38px !important;
  height: 38px !important;
  border-radius: 50% !important;
  border: 2px solid rgba(255,255,255,.25) !important;
  object-fit: cover;
}

/* ── 3. Links globales sidebar ── */
html body.skin-blue .sidebar a {
  color: #cbd5e1 !important;
}
html body.skin-blue .sidebar a:hover {
  text-decoration: none !important;
}

/* ── 4. Headers de sección ── */
html body.skin-blue .sidebar-menu > li.header {
  color: #64748b !important;
  background: transparent !important;
  font-size: 10px !important;
  font-weight: 700 !important;
  letter-spacing: .08em !important;
  text-transform: uppercase !important;
  padding: 12px 16px 4px !important;
}

/* ── 5. Items primer nivel ── */
html body.skin-blue .sidebar-menu > li > a {
  color: #cbd5e1 !important;
  background: transparent !important;
  font-weight: 500 !important;
  font-size: 13px !important;
  border-left: 3px solid transparent !important;
  padding: 10px 14px 10px 16px !important;
  transition: background .15s, color .15s, border-color .15s !important;
}
html body.skin-blue .sidebar-menu > li > a > i,
html body.skin-blue .sidebar-menu > li > a > .fa,
html body.skin-blue .sidebar-menu > li > a > .fas,
html body.skin-blue .sidebar-menu > li > a > .far,
html body.skin-blue .sidebar-menu > li > a > .fab {
  color: #94a3b8 !important;
  width: 20px !important;
  font-size: 14px !important;
  margin-right: 8px !important;
  text-align: center !important;
  transition: color .15s !important;
}

/* ── 6. Hover items ── */
html body.skin-blue .sidebar-menu > li:hover > a,
html body.skin-blue .sidebar-menu > li > a:hover {
  background: rgba(99,102,241,.14) !important;
  color: #fff !important;
  border-left-color: #6366f1 !important;
}
html body.skin-blue .sidebar-menu > li:hover > a > i,
html body.skin-blue .sidebar-menu > li > a:hover > i,
html body.skin-blue .sidebar-menu > li:hover > a > .fa,
html body.skin-blue .sidebar-menu > li:hover > a > .fas,
html body.skin-blue .sidebar-menu > li:hover > a > .far,
html body.skin-blue .sidebar-menu > li:hover > a > .fab {
  color: #c7d2fe !important;
}

/* ── 7. Estado activo ── */
html body.skin-blue .sidebar-menu > li.active > a,
html body.skin-blue .sidebar-menu > li.active > a:hover,
html body.skin-blue .sidebar-menu > li.menu-open > a {
  background: rgba(99,102,241,.22) !important;
  color: #fff !important;
  border-left-color: #6366f1 !important;
  font-weight: 600 !important;
}
html body.skin-blue .sidebar-menu > li.active > a > i,
html body.skin-blue .sidebar-menu > li.menu-open > a > i {
  color: #a5b4fc !important;
}

/* ── 8. Flecha treeview ── */
html body.skin-blue .sidebar-menu > li > a > .pull-right-container .fa-angle-left,
html body.skin-blue .sidebar-menu > li > a > .pull-right-container > .pull-right {
  color: #64748b !important;
  transition: color .15s, transform .2s !important;
}
html body.skin-blue .sidebar-menu > li:hover > a > .pull-right-container .fa-angle-left,
html body.skin-blue .sidebar-menu > li:hover > a > .pull-right-container > .pull-right {
  color: #94a3b8 !important;
}

/* ── 9. Submenú treeview ── */
html body.skin-blue .sidebar-menu > li > .treeview-menu {
  margin: 0 1px !important;
  background: rgba(0,0,0,.28) !important;
  padding: 4px 0 !important;
  list-style: none !important;
}
/* Cuando el treeview está abierto (.menu-open), forzar display */
html body.skin-blue .sidebar-menu > li.menu-open > .treeview-menu {
  display: block !important;
}
html body.skin-blue .sidebar-menu .treeview-menu > li > a {
  color: #94a3b8 !important;
  font-size: 13px !important;
  font-weight: 500 !important;
  padding: 8px 14px 8px 42px !important;
  border-left: 3px solid transparent !important;
  background: transparent !important;
  display: block !important;
  transition: background .12s, color .12s, border-color .12s !important;
}
html body.skin-blue .sidebar-menu .treeview-menu > li > a > i,
html body.skin-blue .sidebar-menu .treeview-menu > li > a > .fa,
html body.skin-blue .sidebar-menu .treeview-menu > li > a > .fas,
html body.skin-blue .sidebar-menu .treeview-menu > li > a > .far {
  color: #64748b !important;
  font-size: 11px !important;
  margin-right: 8px !important;
  width: 16px !important;
  text-align: center !important;
}
html body.skin-blue .sidebar-menu .treeview-menu > li > a:hover {
  background: rgba(99,102,241,.12) !important;
  color: #e2e8f0 !important;
  border-left-color: #818cf8 !important;
}
html body.skin-blue .sidebar-menu .treeview-menu > li.active > a,
html body.skin-blue .sidebar-menu .treeview-menu > li.active > a:hover {
  color: #c7d2fe !important;
  background: rgba(99,102,241,.10) !important;
  border-left-color: #818cf8 !important;
}

/* ── 10. Sidebar-mini COLLAPSED: flyout treeview ── */
@media (min-width: 768px) {
  html body.skin-blue.sidebar-mini.sidebar-collapse .sidebar-menu > li:hover > .treeview-menu {
    display: block !important;
    position: absolute !important;
    left: 50px !important;
    top: 44px !important;
    width: 200px !important;
    background: #1e293b !important;
    border-radius: 0 6px 6px 0 !important;
    box-shadow: 3px 3px 10px rgba(0,0,0,.3) !important;
    padding: 6px 0 !important;
    z-index: 9999 !important;
  }
  html body.skin-blue.sidebar-mini.sidebar-collapse .sidebar-menu > li:hover > .treeview-menu > li > a {
    padding: 8px 14px !important;
  }
  /* Span flyout (label del item al hover en collapsed) */
  html body.skin-blue.sidebar-mini.sidebar-collapse .sidebar-menu > li:hover > a > span:not(.pull-right) {
    background-color: #1e293b !important;
    color: #e2e8f0 !important;
  }
}

/* ── 11. Scrollbar ── */
html body.skin-blue .main-sidebar .sidebar::-webkit-scrollbar { width: 4px; }
html body.skin-blue .main-sidebar .sidebar::-webkit-scrollbar-track { background: transparent; }
html body.skin-blue .main-sidebar .sidebar::-webkit-scrollbar-thumb {
  background: rgba(99,102,241,.30);
  border-radius: 9999px;
}

/* ── 12. Sidebar-mini collapsed user panel ── */
html body.skin-blue.sidebar-mini.sidebar-collapse .egs-sidebar-user .pull-left.info {
  display: none !important;
}
html body.skin-blue.sidebar-mini.sidebar-collapse .egs-sidebar-user {
  padding: 10px 8px !important;
  text-align: center;
}
html body.skin-blue.sidebar-mini.sidebar-collapse .egs-sidebar-user .pull-left.image {
  float: none !important;
  display: block;
}

/* ── 13. Ocultar cualquier toggle duplicado dentro del sidebar ── */
html body .main-sidebar .sidebar-toggle,
html body .main-sidebar a[data-toggle="push-menu"],
html body.skin-blue .main-sidebar .sidebar-toggle,
html body.sidebar-mini .main-sidebar .sidebar-toggle {
  display: none !important;
}

/* ── 14. Font weight texto menú ── */
.sidebar-menu span { font-weight: 600; }

</style>

<?php
$_fotoSidebar = (!empty($_SESSION["foto"]))
    ? htmlspecialchars($_SESSION["foto"])
    : "vistas/img/perfiles/default/anonymous.png";
?>

<!-- ── User Panel ── -->
<div class="user-panel egs-sidebar-user">
  <div class="pull-left image">
    <img src="<?= $_fotoSidebar ?>" class="img-circle" alt="Usuario">
  </div>
  <div class="pull-left info">
    <p><?= htmlspecialchars($_SESSION["nombre"] ?? "Usuario") ?></p>
    <a href="#">
      <i class="fa-solid fa-circle" style="color:#22c55e;font-size:7px;vertical-align:middle"></i>
      &nbsp;En línea
    </a>
  </div>
</div>

<ul class="sidebar-menu" data-widget="tree">

  <?php

  if ($_SESSION["perfil"] == "Super-Administrador") {
    // Secciones comentadas del original
    echo '<!--<li class="active"><a href="index.php?ruta=inicio"><i class="fas fa-home"></i> <span>Inicio</span></a></li>
    <li><a href="index.php?ruta=comercio"><i class="fas fa-files-o"></i> <span>Gestor Comercio</span></a></li>-->';
  }

  if ($_SESSION["perfil"] == "Super-Administrador" || $_SESSION["perfil"] == "administrador" || $_SESSION["perfil"] == "vendedor" || $_SESSION["perfil"] == "secretaria") {
    echo '<!--<li class="treeview">
          <a href="#">
            <i class="fas fa-th"></i>
            <span>Gestor Categorías</span>
            <span class="pull-right-container">
                <i class="fas fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="index.php?ruta=categorias"><i class="far fa-circle"></i> Categorías</a></li>
            <li><a href="index.php?ruta=subcategorias"><i class="far fa-circle"></i> Subcategorías</a></li>
          </ul>
      </li>
      <li class="treeview">
          <a href="#">
             <i class="fas fa-product-hunt"></i>
             <span>Gestor Productos</span>
             <span class="pull-right-container">
                 <i class="fas fa-angle-left pull-right"></i>
             </span>
          </a>
          <ul class="treeview-menu">
             <li><a href="index.php?ruta=productos"><i class="fas fa-product-hunt"></i> <span>Gestor Productos</span></a></li>
          </ul>
      </li>-->';
  }

  // ── Tablero ──
  if ($_SESSION["perfil"] == "tecnico" || $_SESSION["perfil"] == "administrador" || $_SESSION["perfil"] == "vendedor" || $_SESSION["perfil"] == "secretaria" || $_SESSION["perfil"] == "Super-Administrador") {
    echo '<li><a href="index.php?ruta=inicio"><i class="fas fa-home"></i><span> Tablero</span></a></li>';
  }

  // ── Productos + Ventas ──
  if ($_SESSION["perfil"] == "Super-Administrador" || $_SESSION["perfil"] == "administrador" || $_SESSION["perfil"] == "vendedor" || $_SESSION["perfil"] == "secretaria") {
    echo '
      <li class="treeview">
          <a href="#">
            <i class="fab fa-product-hunt"></i>
            <span>Productos</span>
            <span class="pull-right-container">
                <i class="fas fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="index.php?ruta=productos"><i class="fab fa-product-hunt"></i><span> Gestor Productos</span></a></li>
            <li><a href="index.php?ruta=almacenes"><i class="fas fa-warehouse"></i><span> Gestor Almacenes</span></a></li>
          </ul>
      </li>

      <li><a href="index.php?ruta=ventasR"><i class="fas fa-cart-arrow-down"></i><span> Gestor Ventas R</span></a></li>
      <li><a href="index.php?ruta=creararventa" target="_blank"><i class="fa fa-shopping-cart"></i> <span>Gestor Ventas</span></a></li>';
  }

  // ── Pedidos + Órdenes de servicio ──
  if ($_SESSION["perfil"] == "tecnico" || $_SESSION["perfil"] == "administrador" || $_SESSION["perfil"] == "vendedor" || $_SESSION["perfil"] == "secretaria" || $_SESSION["perfil"] == "Super-Administrador") {
    $gestorPedidosItem = '';
    if ($_SESSION["perfil"] != "tecnico") {
      $gestorPedidosItem = '<li><a href="index.php?ruta=pedidos"><i class="fas fa-box-open"></i><span> Gestor pedidos</span></a></li>';
    }

    $estadoServicioItem = '';
    if ($_SESSION["perfil"] == "administrador" || $_SESSION["perfil"] == "Super-Administrador") {
      $estadoServicioItem = '<li><a href="index.php?ruta=reportePorFecheOrdenes"><i class="far fa-circle"></i> Estado del servicio</a></li>';
    }

    echo $gestorPedidosItem . '

      <li class="treeview">
          <a href="#">
            <i class="fas fa-clipboard-list"></i>
            <span>Órdenes de servicio</span>
            <span class="pull-right-container">
                <i class="fas fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="index.php?ruta=ordenes"><i class="far fa-circle"></i> Órdenes</a></li>
            ' . $estadoServicioItem . '
            <li><a href="index.php?ruta=comisiones"><i class="far fa-circle"></i> Comisiones</a></li>
            <li><a href="index.php?ruta=busquedamaterial"><i class="fas fa-search"></i> Busqueda de Material</a></li>';

    if ($_SESSION["perfil"] == "administrador" || $_SESSION["perfil"] == "vendedor" || $_SESSION["perfil"] == "secretaria" || $_SESSION["perfil"] == "Super-Administrador") {
      echo '<li><a href="index.php?ruta=ordenesnew"><i class="fas fa-file-alt"></i> Todas las ordenes</a></li>';
    }

    echo '  </ul>
      </li>';
  }

  // ── Reportes (comentado) ──
  if ($_SESSION["perfil"] == "administrador" || $_SESSION["perfil"] == "Super-Administrador") {
    echo '<!--<li><a href="index.php?ruta=reportes"><i class="fas fa-signal"></i><span> Reportes</span></a></li>-->';
  }

  // ── Empresas ──
  if ($_SESSION["perfil"] == "Super-Administrador") {
    echo '<li><a href="index.php?ruta=empresas"><i class="fas fa-building"></i><span> Empresas</span></a></li>';
  }

  // ── Configuración WhatsApp ──
  if ($_SESSION["perfil"] == "administrador" || $_SESSION["perfil"] == "Super-Administrador") {
    echo '<li><a href="index.php?ruta=config-whatsapp"><i class="fab fa-whatsapp"></i><span> Config WhatsApp</span></a></li>';
  }

  // ── Corte de caja ──
  if ($_SESSION["perfil"] == "administrador" || $_SESSION["perfil"] == "vendedor" || $_SESSION["perfil"] == "secretaria" || $_SESSION["perfil"] == "Super-Administrador") {
    echo '<li><a href="index.php?ruta=CorteTotal"><i class="fas fa-cash-register"></i><span> Corte de caja</span></a></li>';
  }

  // ── Clientes ──
  if ($_SESSION["perfil"] == "administrador" || $_SESSION["perfil"] == "editor" || $_SESSION["perfil"] == "vendedor" || $_SESSION["perfil"] == "Super-Administrador") {
    echo '<li><a href="index.php?ruta=clientes"><i class="fas fa-users"></i> <span>Gestor Clientes</span></a></li>';
  }

  // ── CRM (administrador) ──
  if ($_SESSION["perfil"] == "administrador") {
    echo '
      <li class="treeview">
          <a href="#">
            <i class="fas fa-handshake"></i>
            <span>CRM</span>
            <span class="pull-right-container">
                <i class="fas fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="index.php?ruta=metas"><i class="far fa-circle"></i> Metas</a></li>
            <li><a href="index.php?ruta=createobjetivo"><i class="far fa-circle"></i> Agregar meta</a></li>
            <li><a href="index.php?ruta=objetivos"><i class="far fa-circle"></i> Reporte de metas</a></li>
            <li><a href="index.php?ruta=listaobjetivos"><i class="far fa-circle"></i> Lista de metas</a></li>
          </ul>
      </li>';
  }

  // ── Visitas ──
  if ($_SESSION["perfil"] == "administrador" AND $_SESSION["empresa"] == 1) {
    echo '<li><a href="index.php?ruta=visitas"><i class="fas fa-map-marker"></i><span> Gestor Visitas</span></a></li>';
  }

  // ── Técnico ──
  if ($_SESSION["perfil"] == "tecnico") {
    echo '<li><a href="index.php?ruta=metas"><i class="fas fa-handshake"></i> <span>Mis Metas</span></a></li>
      <li><a href="index.php?ruta=peticionorden"><i class="fas fa-sync"></i> <span>Petición Orden</span></a></li>
      <li><a href="index.php?ruta=peticionmaterial"><i class="fas fa-tools"></i> <span>Peticion de material</span></a></li>';
  }

  // ── CRM (vendedor / secretaria) ──
  if ($_SESSION["perfil"] == "vendedor" || $_SESSION["perfil"] == "secretaria") {
    echo '
      <li class="treeview">
          <a href="#">
            <i class="fas fa-handshake"></i>
            <span>CRM</span>
            <span class="pull-right-container">
                <i class="fas fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="index.php?ruta=crm"><i class="far fa-circle"></i> CRM</a></li>
            <li><a href="index.php?ruta=metas"><i class="far fa-circle"></i> Metas</a></li>
          </ul>
      </li>';
  }

  // ── Personal ──
  if ($_SESSION["perfil"] == "Super-Administrador" || $_SESSION["perfil"] == "administrador") {
    echo '
      <li class="treeview">
          <a href="#">
            <i class="fas fa-users-cog"></i>
            <span>Personal</span>
            <span class="pull-right-container">
                <i class="fas fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="index.php?ruta=perfiles"><i class="fas fa-key"></i> <span>Gestor Perfiles</span></a></li>
            <li><a href="index.php?ruta=asesores"><i class="fas fa-user-tie"></i><span> Asesores de Ventas</span></a></li>
            <li><a href="index.php?ruta=tecnicos"><i class="fas fa-user-cog"></i><span> Técnicos</span></a></li>
          </ul>
      </li>';
  }

  // ── Citas + Cotización + Peticiones (vendedor / administrador) ──
  if ($_SESSION["perfil"] == "vendedor" || $_SESSION["perfil"] == "administrador") {
    echo '
      <li class="treeview">
          <a href="#">
            <i class="far fa-calendar-alt"></i>
            <span>Citas</span>
            <span class="pull-right-container">
                <i class="fas fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="index.php?ruta=pantallacitas"><i class="fas fa-tv"></i> Pantalla</a></li>
            <li><a href="index.php?ruta=listacitas"><i class="far fa-circle"></i> Lista de citas</a></li>
            <li><a href="index.php?ruta=crearcita"><i class="far fa-calendar-plus"></i> Agregar cita</a></li>
          </ul>
      </li>

      <li class="treeview">
          <a href="#">
            <i class="fas fa-file-invoice-dollar"></i>
            <span>Cotización</span>
            <span class="pull-right-container">
                <i class="fas fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="index.php?ruta=cotizacion"><i class="fas fa-file-invoice-dollar"></i> Nueva cotización</a></li>
            <li><a href="index.php?ruta=historial-cotizaciones"><i class="fas fa-hand-holding-usd"></i> Historial cotizaciones</a></li>
          </ul>
      </li>

      <li class="treeview">
          <a href="#">
            <i class="far fa-edit"></i>
            <span>Peticiones</span>
            <span class="pull-right-container">
                <i class="fas fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="index.php?ruta=peticionorden"><i class="fas fa-sync"></i> <span>Petición Orden</span></a></li>
            <li><a href="index.php?ruta=peticionmaterial"><i class="fas fa-tools"></i> <span>Peticion de material</span></a></li>
            <li><a href="index.php?ruta=listaPeticionesM"><i class="far fa-list-alt"></i> <span>Lista Peticion material</span></a></li>
          </ul>
      </li>';
  }
  ?>

</ul>
