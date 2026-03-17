<!--=====================================
USUARIOS — MENÚ MODERNO
======================================-->
<?php
$_fotoUser = (!empty($_SESSION["foto"]))
    ? htmlspecialchars($_SESSION["foto"])
    : "vistas/img/perfiles/default/anonymous.png";
$_nombreUser = htmlspecialchars($_SESSION["nombre"] ?? "Usuario");
$_rolUser    = htmlspecialchars(ucfirst($_SESSION["perfil"] ?? ""));
?>

<!-- user-menu -->
<li class="dropdown user user-menu">

  <!-- ── Trigger ── -->
  <a href="#" class="dropdown-toggle egs-user-trigger" data-toggle="dropdown">
    <span class="egs-avatar-wrap">
      <img src="<?= $_fotoUser ?>" class="user-image egs-avatar-sm" alt="<?= $_nombreUser ?>">
      <span class="egs-online-pulse"></span>
    </span>
    <span class="hidden-xs egs-trigger-name"><?= $_nombreUser ?></span>
    <i class="fa-solid fa-chevron-down hidden-xs egs-chevron"></i>
  </a>

  <!-- ── Dropdown ── -->
  <ul class="dropdown-menu egs-user-dropdown">

    <!-- Cabecera con gradiente -->
    <li class="egs-udrop-header">
      <div class="egs-udrop-avatar-wrap">
        <img src="<?= $_fotoUser ?>" class="egs-udrop-avatar" alt="<?= $_nombreUser ?>">
        <span class="egs-udrop-dot"></span>
      </div>
      <div class="egs-udrop-info">
        <span class="egs-udrop-name"><?= $_nombreUser ?></span>
        <span class="egs-udrop-role"><?= $_rolUser ?></span>
      </div>
    </li>

    <!-- Links rápidos -->
    <li style="padding:0">
      <ul class="egs-udrop-nav">
        <li>
          <a href="index.php?ruta=perfiles">
            <span class="egs-udrop-icon"><i class="fa-solid fa-circle-user"></i></span>
            Mi Cuenta
          </a>
        </li>
        <li>
          <a href="index.php?ruta=preguntas">
            <span class="egs-udrop-icon"><i class="fa-solid fa-circle-question"></i></span>
            Ayuda
          </a>
        </li>
      </ul>
    </li>

    <!-- Pie — Cerrar sesión -->
    <li class="egs-udrop-footer">
      <a href="index.php?ruta=salir" class="egs-btn-logout">
        <i class="fa-solid fa-right-from-bracket"></i>
        Cerrar sesión
      </a>
    </li>

  </ul>

</li>
<!-- /user-menu -->
