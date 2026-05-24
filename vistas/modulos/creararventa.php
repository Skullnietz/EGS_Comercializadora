<?php

if ($_SESSION["perfil"] != "administrador" AND $_SESSION["perfil"] != "vendedor" AND $_SESSION["perfil"] != "secretaria" AND $_SESSION["perfil"] != "Super-Administrador") {
  echo '<script>window.location = "inicio";</script>';
  return;
}

$asesorSesion = Controladorasesores::ctrMostrarAsesoresEleg("correo", $_SESSION["email"]);
$idAsesorDefault = (is_array($asesorSesion) && isset($asesorSesion["id"])) ? intval($asesorSesion["id"]) : 0;

$itemEmpresa = "id_empresa";
$valorEmpresa = $_SESSION["empresa"];
$asesoresPos = Controladorasesores::ctrMostrarAsesoresEmpresas($itemEmpresa, $valorEmpresa);
if (!is_array($asesoresPos)) {
  $asesoresPos = array();
}

$clientesPos = ControladorClientes::ctrMostrarClientes(null, null);
if (!is_array($clientesPos)) {
  $clientesPos = array();
}

?>
<div class="content-wrapper">

  <section class="content-header">
    <h1>Cajero <small>Gestor de ventas</small></h1>
    <ol class="breadcrumb">
      <li><a href="index.php?ruta=inicio"><i class="fas fa-dashboard"></i> Inicio</a></li>
      <li class="active">Cajero</li>
    </ol>
  </section>

  <section class="content">
    <?php include __DIR__ . '/partials/ventas-cajero-vista.php'; ?>
  </section>

</div>

<?php
  $ventaDinamica = new ControladorVentas();
  $ventaDinamica->ctrCrearVentaDinamica();
?>
