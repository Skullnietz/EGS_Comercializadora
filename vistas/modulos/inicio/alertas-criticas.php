<?php
/* =====================================================
   PANEL DE ALERTAS CRÍTICAS — Dashboard Admin
   ===================================================== */

// Órdenes ingresadas este mes
$entradasMes = ControladorOrdenes::ctrMostrarOrdenesEntrada();
$totalMes    = count($entradasMes);

// Cuántas ingresaron HOY
$hoy      = date('Y-m-d');
$totalHoy = 0;
foreach ($entradasMes as $e) {
    if (substr($e['fecha_ingreso'], 0, 10) === $hoy) $totalHoy++;
}
$pctHoy = $totalMes > 0 ? round($totalHoy * 100 / $totalMes) : 0;

// Órdenes entregadas este mes
$entregasMes  = controladorOrdenes::ctrMostrarOrdenesSuma();
$totalEntMes  = count($entregasMes);

// Cuántas se entregaron HOY
$totalEntHoy = 0;
foreach ($entregasMes as $e) {
    if (substr($e['fecha_Salida'], 0, 10) === $hoy) $totalEntHoy++;
}

// Pendientes del mes = ingresadas - entregadas
$pendientesMes  = max(0, $totalMes - $totalEntMes);
$pctPendientes  = $totalMes > 0 ? round($pendientesMes * 100 / $totalMes) : 0;
$colorPendientes = $pendientesMes === 0 ? 'bg-green' : ($pctPendientes <= 30 ? 'bg-yellow' : 'bg-red');

// % eficiencia del mes: entregadas / ingresadas
$eficiencia = $totalMes > 0 ? round($totalEntMes * 100 / $totalMes) : 0;
$colorEfic  = $eficiencia >= 80 ? 'bg-green' : ($eficiencia >= 50 ? 'bg-yellow' : 'bg-red');
?>

<!--=====================================
ALERTAS CRÍTICAS
======================================-->
<div class="row">

  <!-- Pendientes del mes -->
  <div class="col-lg-3 col-sm-6">
    <div class="info-box <?= $colorPendientes ?>">
      <span class="info-box-icon"><i class="fa fa-clock-o"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Pendientes del Mes</span>
        <span class="info-box-number"><?= number_format($pendientesMes) ?></span>
        <div class="progress"><div class="progress-bar" style="width:<?= $pctPendientes ?>%"></div></div>
        <span class="progress-description">
          <a href="index.php?ruta=ordenes" style="color:#fff;text-decoration:underline">
            <?= $pctPendientes ?>% sin entregar &rarr;
          </a>
        </span>
      </div>
    </div>
  </div>

  <!-- Ingresadas hoy -->
  <div class="col-lg-3 col-sm-6">
    <div class="info-box bg-aqua">
      <span class="info-box-icon"><i class="fa fa-calendar-plus-o"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Ingresadas Hoy</span>
        <span class="info-box-number"><?= $totalHoy ?></span>
        <div class="progress">
          <div class="progress-bar" style="width:<?= $pctHoy ?>%"></div>
        </div>
        <span class="progress-description">
          <?= $pctHoy ?>% del mes &mdash; total mes: <?= $totalMes ?>
        </span>
      </div>
    </div>
  </div>

  <!-- Entregadas hoy -->
  <div class="col-lg-3 col-sm-6">
    <div class="info-box bg-green">
      <span class="info-box-icon"><i class="fa fa-check-circle"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Entregadas Hoy</span>
        <span class="info-box-number"><?= $totalEntHoy ?></span>
        <div class="progress">
          <div class="progress-bar"
               style="width:<?= $totalEntMes > 0 ? round($totalEntHoy*100/$totalEntMes) : 0 ?>%">
          </div>
        </div>
        <span class="progress-description">
          Total entregadas este mes: <?= $totalEntMes ?>
        </span>
      </div>
    </div>
  </div>

  <!-- Eficiencia del mes -->
  <div class="col-lg-3 col-sm-6">
    <div class="info-box <?= $colorEfic ?>">
      <span class="info-box-icon"><i class="fa fa-line-chart"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Eficiencia del Mes</span>
        <span class="info-box-number"><?= $eficiencia ?>%</span>
        <div class="progress">
          <div class="progress-bar" style="width:<?= $eficiencia ?>%"></div>
        </div>
        <span class="progress-description">
          <?= $totalEntMes ?> entregadas de <?= $totalMes ?> ingresadas
        </span>
      </div>
    </div>
  </div>

</div>
<!-- /row alertas -->
