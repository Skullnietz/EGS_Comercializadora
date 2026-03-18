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

/* ── 2. Logo: vacío, solo fondo ── */
body .main-header .logo,
body.skin-blue .main-header .logo,
html body.skin-blue .main-header .logo {
  background: #0f172a !important;
  border-right: 1px solid rgba(255,255,255,.06) !important;
  border-bottom: none !important;
}
/* Ocultar TODA imagen dentro del logo */
.main-header .logo .logo-mini,
.main-header .logo .logo-lg,
.main-header .logo img {
  display: none !important;
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

/* ── Sidebar: estilos en lateral/menu.php ── */

</style>

<!-- main-header -->
<header class="main-header">

  <!-- logo: vacío pero necesario para layout AdminLTE -->
  <a href="inicio" class="logo">
    <span class="logo-mini">&nbsp;</span>
    <span class="logo-lg">&nbsp;</span>
  </a>

  <!-- navbar -->
  <nav class="navbar navbar-static-top" role="navigation">

    <!-- Sidebar toggle (ÚNICO hamburger) -->
    <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
      <i class="fa-solid fa-bars"></i>
      <span class="sr-only">Toggle navigation</span>
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

</header>
<!-- /main-header -->
