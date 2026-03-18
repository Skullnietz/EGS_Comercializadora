<!-- ── Estilos del header inyectados aquí para ganar a CUALQUIER skin ── -->
<style>
/* ── Navbar: slate-900 Tailwind, nuclear override ── */
.main-header,
.main-header .navbar,
.main-header nav,
body .main-header .navbar,
body.skin-blue .main-header .navbar,
html body .main-header .navbar {
  background: #1e293b !important;
  background-color: #1e293b !important;
  border-bottom: none !important;
  box-shadow: 0 1px 0 rgba(255,255,255,.05), 0 4px 16px rgba(0,0,0,.25) !important;
}
body .main-header .logo,
body.skin-blue .main-header .logo {
  background: #0f172a !important;
  background-color: #0f172a !important;
  border-right: 1px solid rgba(255,255,255,.06) !important;
  border-bottom: none !important;
  transition: background .15s;
}
body .main-header .logo:hover,
body.skin-blue .main-header .logo:hover { background: #080f1c !important; }

/* Toggle */
.main-header .sidebar-toggle {
  color: #94a3b8 !important;
  font-size: 18px;
  padding: 17px 18px !important;
  border: none !important;
  transition: color .15s, background .15s;
}
.main-header .sidebar-toggle:hover {
  color: #f1f5f9 !important;
  background: rgba(255,255,255,.06) !important;
}

/* Nav icons (bell, ?) */
.navbar-custom-menu .navbar-nav > li > a {
  color: #94a3b8 !important;
  padding: 15px 14px !important;
  transition: color .15s, background .15s;
}
.navbar-custom-menu .navbar-nav > li > a:hover {
  color: #f1f5f9 !important;
  background: rgba(255,255,255,.06) !important;
}
/* Badge notificaciones */
.navbar-custom-menu .label-warning {
  background: #f59e0b !important;
  font-size: 9px !important;
  padding: 2px 5px !important;
  border-radius: 9999px !important;
  font-weight: 700 !important;
}

/* ── Trigger del user-menu ── */
.main-header .user-menu > a.egs-trigger {
  display: flex !important;
  align-items: center !important;
  gap: 9px !important;
  padding: 9px 12px !important;
  margin: 8px 6px !important;
  border-radius: 8px !important;
  color: #e2e8f0 !important;
  background: transparent !important;
  border: 1px solid rgba(255,255,255,.10) !important;
  transition: background .15s, border-color .15s !important;
  text-decoration: none !important;
}
.main-header .user-menu > a.egs-trigger:hover {
  background: rgba(255,255,255,.08) !important;
  border-color: rgba(255,255,255,.18) !important;
}
.egs-trigger .egs-av {
  width: 28px !important;
  height: 28px !important;
  border-radius: 50% !important;
  object-fit: cover;
  border: 1.5px solid rgba(255,255,255,.25) !important;
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

/* ── Dropdown panel Tailwind ── */
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
/* Sección de usuario */
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
/* Status dot */
.egs-drop-user .egs-dot {
  width: 8px; height: 8px;
  border-radius: 50%;
  background: #22c55e;
  flex-shrink: 0;
}
/* Links del menú */
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
/* Divider */
.egs-drop-divider {
  margin: 4px 0 !important;
  border-top: 1px solid #f1f5f9 !important;
}
/* Pie logout */
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

/* ── Sidebar ── */
body .main-sidebar,
body.skin-blue .main-sidebar,
body .main-sidebar .sidebar {
  background: #0f172a !important;
  background-color: #0f172a !important;
}
body.skin-blue .sidebar-menu > li.active > a,
body.skin-blue .sidebar-menu > li.active > a:hover {
  background: rgba(99,102,241,.15) !important;
  border-left-color: #6366f1 !important;
  color: #fff !important;
}
body.skin-blue .sidebar-menu > li > a:hover {
  background: rgba(255,255,255,.05) !important;
  color: #f1f5f9 !important;
  border-left-color: #6366f1 !important;
}
body.skin-blue .treeview-menu {
  background: rgba(0,0,0,.2) !important;
}
</style>

<!-- main-header -->
<header class="main-header">

  <!-- logo -->
  <a href="inicio" class="logo">
    <span class="logo-mini">
      <img src="vistas/img/plantilla/icono.png" class="img-responsive"
           style="padding:8px; filter:brightness(10);">
    </span>
    <span class="logo-lg">
      <img src="vistas/img/plantilla/logo.png" class="img-responsive" id="logo"
           style="padding:8px 28px; filter:brightness(10);">
    </span>
  </a>
  <!-- /logo -->

  <!-- navbar -->
  <nav class="navbar navbar-static-top" role="navigation">

    <!-- Sidebar toggle -->
    <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
      <i class="fa-solid fa-bars"></i>
    </a>

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
  <!-- /navbar -->

</header>
<!-- /main-header -->

<style>
/* Fix logo visibility (se conserva del original) */
#logo { visibility: hidden; }
@media screen and (min-width: 700px) {
  #logo { width:150px; height:75px; margin-top:-11px; visibility:visible; }
}
</style>
