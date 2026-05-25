<?php
require_once __DIR__ . '/../../config/visitasInsights.helper.php';

$panorama = ControladorVisitas::ctrObtenerPanoramaWeb();
$insights = VisitasInsightsHelper::generar($panorama);

$audit = isset($panorama['audit']) ? $panorama['audit'] : array();
$trackingActivo = !empty($audit['tracking_activo']);
$ultima = isset($audit['ultima_fecha']) ? $audit['ultima_fecha'] : null;
?>

<div class="content-wrapper visitas-page">

  <section class="content-header">
    <h1>Inteligencia Web <small style="font-weight:400;color:#94a3b8">Panorama y acciones</small></h1>
    <ol class="breadcrumb">
      <li><a href="inicio"><i class="fa-solid fa-gauge-high"></i> Inicio</a></li>
      <li class="active">Inteligencia Web</li>
    </ol>
  </section>

  <section class="content">

    <?php include __DIR__ . '/partials/crm-styles.php'; ?>

    <?php if (!$trackingActivo): ?>
    <div class="visitas-banner visitas-banner-warning">
      <i class="fa-solid fa-triangle-exclamation"></i>
      <div>
        <strong>Tracking local <?php echo empty($audit['total_registros']) ? 'sin datos' : 'inactivo'; ?></strong><br>
        <?php if ($ultima): ?>
          Última visita registrada: <?php echo htmlspecialchars($ultima); ?>.
        <?php else: ?>
          No hay visitas en la base de datos.
        <?php endif; ?>
        Activa el script en comercializadoraegs.com o configura GA4 (pestaña Configuración).
      </div>
    </div>
    <?php else: ?>
    <div class="visitas-banner visitas-banner-info">
      <i class="fa-solid fa-circle-check"></i>
      <div>
        <strong>Tracking activo</strong> — <?php echo number_format(intval($panorama['visitas_7d'])); ?> visitas en los últimos 7 días.
        Último registro: <?php echo htmlspecialchars($ultima); ?>.
      </div>
    </div>
    <?php endif; ?>

    <?php include __DIR__ . '/visitas/kpis.php'; ?>

    <div class="row">
      <div class="col-lg-8">
        <?php include __DIR__ . '/visitas/recomendaciones.php'; ?>
        <?php include __DIR__ . '/visitas/focos.php'; ?>
      </div>
      <div class="col-lg-4">
        <?php include __DIR__ . '/visitas/acciones-rapidas.php'; ?>
      </div>
    </div>

    <div class="visitas-tabs" role="tablist">
      <button type="button" class="visitas-tab active" data-tab="resumen" id="tab-resumen">Resumen</button>
      <button type="button" class="visitas-tab" data-tab="geografia" id="tab-geografia">Geografía</button>
      <button type="button" class="visitas-tab" data-tab="detalle" id="tab-detalle">Detalle</button>
      <button type="button" class="visitas-tab" data-tab="legacy" id="tab-legacy">SeeTheStats</button>
      <button type="button" class="visitas-tab" data-tab="config-analytics" id="tab-config-analytics">Configuración</button>
    </div>

    <div id="pane-resumen" class="visitas-tab-pane active">
      <?php include __DIR__ . '/visitas/graficos.php'; ?>
    </div>

    <div id="pane-geografia" class="visitas-tab-pane">
      <div class="crm-card" style="margin-bottom:20px">
        <div class="crm-card-head">
          <h4 class="crm-card-title"><i class="fa-solid fa-earth-americas"></i> Distribución por país</h4>
        </div>
        <div class="crm-card-body">
          <?php if (!empty($panorama['top_paises'])): ?>
          <table class="crm-table">
            <thead><tr><th>País</th><th>Visitas</th></tr></thead>
            <tbody>
              <?php foreach ($panorama['top_paises'] as $pa): ?>
              <tr>
                <td><?php echo htmlspecialchars(isset($pa['pais']) ? $pa['pais'] : '—'); ?></td>
                <td><?php echo number_format(intval(isset($pa['total']) ? $pa['total'] : 0)); ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <?php if (intval($panorama['extranjero']['pct']) > 0): ?>
          <p style="margin-top:12px;font-size:12px;color:var(--crm-muted)">
            Tráfico no-México estimado: <strong><?php echo intval($panorama['extranjero']['pct']); ?>%</strong>
          </p>
          <?php endif; ?>
          <?php else: ?>
          <p style="color:var(--crm-muted)">Sin datos de países.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div id="pane-detalle" class="visitas-tab-pane">
      <?php include __DIR__ . '/visitas/detalle-tabla.php'; ?>
    </div>

    <div id="pane-legacy" class="visitas-tab-pane">
      <?php include __DIR__ . '/visitas/analytics-legacy.php'; ?>
    </div>

    <div id="pane-config-analytics" class="visitas-tab-pane">
      <?php include __DIR__ . '/visitas/config-analytics.php'; ?>
    </div>

  </section>
</div>
