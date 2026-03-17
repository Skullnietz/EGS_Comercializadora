<!--=====================================
PÁGINA DE INICIO
======================================-->
<style>
/* ── Separadores de sección del Dashboard ── */
.dash-section {
  display: flex;
  align-items: center;
  margin: 24px 0 12px;
  padding: 0 15px;
}
.dash-section-bar {
  width: 4px;
  height: 34px;
  border-radius: 3px;
  margin-right: 12px;
  flex-shrink: 0;
}
.dash-section-text h4 {
  margin: 0 0 1px;
  font-size: 13px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: .6px;
  color: #444;
}
.dash-section-text small {
  color: #aaa;
  font-size: 11px;
  font-weight: 400;
}
.dash-divider {
  border: none;
  border-top: 1px solid #ecf0f1;
  margin: 0 15px 16px;
}
/* ── Iguala alturas en la misma fila ── */
.dash-row-equal [class*="col-"] {
  display: flex;
  flex-direction: column;
}
.dash-row-equal .box {
  flex: 1;
}
</style>

<!-- content-wrapper -->
<div class="content-wrapper">

  <!-- content-header -->
  <section class="content-header">
    <h1>Tablero <small>Panel de Control</small></h1>
    <ol class="breadcrumb">
      <li><a href="index.php?ruta=inicio"><i class="fa-solid fa-gauge"></i> Inicio</a></li>
      <li class="active">Tablero</li>
    </ol>
  </section>

  <!-- content -->
  <section class="content">

    <?php if ($_SESSION["perfil"] == "administrador"): ?>

      <!-- ══════════════════════════════════════════
           SECCIÓN 1 — Resumen General del Mes
      ══════════════════════════════════════════ -->
      <div class="dash-section">
        <div class="dash-section-bar" style="background:#3c8dbc;"></div>
        <div class="dash-section-text">
          <h4><i class="fa-solid fa-chart-pie"></i> &nbsp;Resumen General del Mes</h4>
          <small>Totales acumulados del periodo actual</small>
        </div>
      </div>
      <div class="row">
        <?php include "inicio/superioresAdmin.php"; ?>
      </div>

      <hr class="dash-divider">

      <!-- ══════════════════════════════════════════
           SECCIÓN 2 — Estado del Día
      ══════════════════════════════════════════ -->
      <div class="dash-section">
        <div class="dash-section-bar" style="background:#f39c12;"></div>
        <div class="dash-section-text">
          <h4><i class="fa-solid fa-bolt"></i> &nbsp;Estado del Día</h4>
          <small>Órdenes pendientes, ingresos y eficiencia del mes en curso</small>
        </div>
      </div>

      <?php include "inicio/alertas-criticas.php"; ?>

      <hr class="dash-divider">

      <!-- ══════════════════════════════════════════
           SECCIÓN 3 — Análisis y Rendimiento
      ══════════════════════════════════════════ -->
      <div class="dash-section">
        <div class="dash-section-bar" style="background:#00a65a;"></div>
        <div class="dash-section-text">
          <h4><i class="fa-solid fa-chart-line"></i> &nbsp;Análisis y Rendimiento</h4>
          <small>Histórico de ventas y ranking de técnicos del mes</small>
        </div>
      </div>
      <div class="row dash-row-equal">
        <div class="col-lg-8 col-md-8 col-xs-12">
          <?php include "inicio/grafico-ventas.php"; ?>
        </div>
        <div class="col-lg-4 col-md-4 col-xs-12">
          <?php include "inicio/top-tecnicos.php"; ?>
        </div>
      </div>

      <hr class="dash-divider">

      <!-- ══════════════════════════════════════════
           SECCIÓN 4 — Actividad Reciente
      ══════════════════════════════════════════ -->
      <div class="dash-section">
        <div class="dash-section-bar" style="background:#605ca8;"></div>
        <div class="dash-section-text">
          <h4><i class="fa-solid fa-list-check"></i> &nbsp;Actividad Reciente</h4>
          <small>Últimas órdenes registradas y productos con mayor demanda</small>
        </div>
      </div>
      <div class="row dash-row-equal">
        <div class="col-lg-7 col-md-7 col-xs-12">
          <?php include "inicio/ultimas-ordenes.php"; ?>
        </div>
        <div class="col-lg-5 col-md-5 col-xs-12">
          <?php include "inicio/productos-mas-vendidos.php"; ?>
        </div>
      </div>

      <hr class="dash-divider">

      <!-- ══════════════════════════════════════════
           SECCIÓN 5 — Equipo
      ══════════════════════════════════════════ -->
      <div class="dash-section">
        <div class="dash-section-bar" style="background:#dd4b39;"></div>
        <div class="dash-section-text">
          <h4><i class="fa-solid fa-users"></i> &nbsp;Equipo</h4>
          <small>Asesores y técnicos registrados en la empresa</small>
        </div>
      </div>
      <div class="row dash-row-equal">
        <div class="col-lg-6 col-md-6 col-xs-12">
          <?php include "inicio/asesores-caja.php"; ?>
        </div>
        <div class="col-lg-6 col-md-6 col-xs-12">
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
