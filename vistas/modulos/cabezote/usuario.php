<?php
/* ── datos del usuario ── */
$_av   = !empty($_SESSION["foto"]) ? htmlspecialchars($_SESSION["foto"])
                                   : "vistas/img/perfiles/default/anonymous.png";
$_nom  = htmlspecialchars($_SESSION["nombre"]     ?? "Usuario");
$_rol  = htmlspecialchars(ucfirst($_SESSION["perfil"] ?? ""));
?>
<!-- user-menu -->
<li class="dropdown user user-menu">

  <!-- Trigger: pill style Tailwind -->
  <a href="#" class="dropdown-toggle egs-trigger" data-toggle="dropdown">
    <img src="<?= $_av ?>" class="egs-av" alt="<?= $_nom ?>">
    <span class="egs-name hidden-xs"><?= $_nom ?></span>
    <i class="fa-solid fa-chevron-down egs-caret hidden-xs"></i>
  </a>

  <!-- Panel Tailwind -->
  <ul class="dropdown-menu egs-drop">

    <!-- Sección usuario -->
    <li class="egs-drop-user">
      <img src="<?= $_av ?>" class="egs-av-lg" alt="<?= $_nom ?>">
      <div class="egs-user-info">
        <strong><?= $_nom ?></strong>
        <span><?= $_rol ?></span>
      </div>
      <span class="egs-dot" title="En línea"></span>
    </li>

    <!-- Links -->
    <li style="padding:0">
      <ul class="egs-drop-nav">
        <li>
          <a href="index.php?ruta=perfiles">
            <i class="fa-solid fa-circle-user"></i> Mi Cuenta
          </a>
        </li>
        <li>
          <a href="index.php?ruta=preguntas">
            <i class="fa-regular fa-circle-question"></i> Ayuda
          </a>
        </li>
      </ul>
    </li>

    <li class="egs-drop-divider"></li>

    <!-- Logout -->
    <li class="egs-drop-foot">
      <a href="index.php?ruta=salir" class="egs-drop-logout">
        <i class="fa-solid fa-arrow-right-from-bracket"></i>
        Cerrar sesión
      </a>
    </li>

  </ul>
</li>
<!-- /user-menu -->
