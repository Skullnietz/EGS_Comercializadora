<!--=====================================
PÁGINA DE INICIO
======================================-->
<!-- content-wrapper -->
<div class="content-wrapper">

  <!-- content-header -->
  <section class="content-header">
    
    <h1>
    Tablero
    <small>Panel de Control</small>
    </h1>

    <ol class="breadcrumb">

      <li><a href="index.php?ruta=inicio"><i class="fas fa-dashboard"></i> Inicio</a></li>
      <li class="active">Tablero</li>

    </ol>

  </section>
  <!-- content-header -->

  <!-- content -->
  <section class="content">

    <?php if ($_SESSION["perfil"] == "administrador"): ?>

      <!-- ── Fila 1: KPIs superiores ── -->
      <div class="row">
        <?php include "inicio/superioresAdmin.php"; ?>
      </div>

      <!-- ── Fila 2: Alertas críticas del mes ── -->
      <?php include "inicio/alertas-criticas.php"; ?>

      <!-- ── Fila 3: Gráfico de ventas + Top Técnicos ── -->
      <div class="row">
        <div class="col-lg-8">
          <?php include "inicio/grafico-ventas.php"; ?>
        </div>
        <div class="col-lg-4">
          <?php include "inicio/top-tecnicos.php"; ?>
        </div>
      </div>

      <!-- ── Fila 4: Últimas órdenes + Productos más vendidos ── -->
      <div class="row">
        <div class="col-lg-8">
          <?php include "inicio/ultimas-ordenes.php"; ?>
        </div>
        <div class="col-lg-4">
          <?php include "inicio/productos-mas-vendidos.php"; ?>
        </div>
      </div>

      <!-- ── Fila 5: Asesores + Técnicos ── -->
      <div class="row">
        <div class="col-lg-6">
          <?php include "inicio/asesores-caja.php"; ?>
        </div>
        <div class="col-lg-6">
          <?php include "inicio/tecnicos-caja.php"; ?>
        </div>
      </div>

    <?php elseif ($_SESSION["perfil"] == "vendedor"): ?>

      <div class="row">
        <?php include "inicio/superiorVendedor.php"; ?>
      </div>
      <div class="row">
        <div class="col-lg-6">
          <?php include "inicio/ordenes-AUT-caja.php"; ?>
        </div>
      </div>

    <?php elseif ($_SESSION["perfil"] == "tecnico"): ?>

      <div class="row">
        <?php include_once "inicio/superiorTecnicos.php"; ?>
      </div>
      <div class="row">
        <div class="col-lg-6">
          <?php include "inicio/ordenes-caja.php"; ?>
        </div>
        <div class="col-lg-6">
          <?php include "inicio/ordenesEnRev.php"; ?>
        </div>
      </div>

    <?php endif; ?>

  </section>
  <!-- content -->

</div>
  