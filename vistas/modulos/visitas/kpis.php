<?php
/** @var array $panorama */
/** @var array $insights */
$p = $panorama;
$ga = isset($p['ga4']['metricas']) ? $p['ga4']['metricas'] : array();
$gaActivo = !empty($p['ga4']['activo']);
?>
<div class="row">
  <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12" style="margin-bottom:16px">
    <a href="#tab-detalle" class="visitas-tab-link" data-tab="detalle" style="text-decoration:none;display:block">
      <div class="crm-kpi" style="background:linear-gradient(135deg,#6366f1,#818cf8)">
        <i class="fa-solid fa-globe crm-kpi-icon"></i>
        <div class="crm-kpi-label">Total histórico</div>
        <div class="crm-kpi-value"><?php echo number_format(intval($p['total_historico'])); ?></div>
        <div class="crm-kpi-sub"><i class="fa-solid fa-chart-simple"></i> Visitas acumuladas</div>
        <div class="crm-kpi-bar"><span style="width:<?php echo min(100, intval($p['total_historico']) > 0 ? 70 : 10); ?>%"></span></div>
      </div>
    </a>
  </div>

  <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12" style="margin-bottom:16px">
    <div class="crm-kpi" style="background:linear-gradient(135deg,#3b82f6,#60a5fa)">
      <i class="fa-solid fa-calendar-week crm-kpi-icon"></i>
      <div class="crm-kpi-label">Últimos 7 días</div>
      <div class="crm-kpi-value"><?php echo number_format(intval($p['visitas_7d'])); ?></div>
      <div class="crm-kpi-sub">30d: <?php echo number_format(intval($p['visitas_30d'])); ?></div>
      <div class="crm-kpi-bar"><span style="width:<?php echo min(100, intval($p['visitas_7d']) * 8); ?>%"></span></div>
    </div>
  </div>

  <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12" style="margin-bottom:16px">
    <a href="#tab-detalle" class="visitas-tab-link" data-tab="detalle" style="text-decoration:none;display:block">
      <div class="crm-kpi" style="background:linear-gradient(135deg,#06b6d4,#22d3ee)">
        <i class="fa-solid fa-bell crm-kpi-icon"></i>
        <div class="crm-kpi-label">Nuevas visitas</div>
        <div class="crm-kpi-value"><?php echo number_format(intval($p['nuevas_visitas'])); ?></div>
        <div class="crm-kpi-sub">Sin revisar en campana</div>
        <div class="crm-kpi-bar"><span style="width:<?php echo min(100, intval($p['nuevas_visitas']) * 15); ?>%"></span></div>
      </div>
    </a>
  </div>

  <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12" style="margin-bottom:16px">
    <a href="index.php?ruta=ventas" style="text-decoration:none;display:block">
      <div class="crm-kpi" style="background:linear-gradient(135deg,#22c55e,#4ade80)">
        <i class="fa-solid fa-cart-shopping crm-kpi-icon"></i>
        <div class="crm-kpi-label">Ventas e-commerce</div>
        <div class="crm-kpi-value">$<?php echo number_format(floatval($p['ventas_mes']), 0); ?></div>
        <div class="crm-kpi-sub">Mes actual</div>
        <div class="crm-kpi-bar"><span style="width:<?php echo $p['ventas_mes'] > 0 ? 65 : 15; ?>%"></span></div>
      </div>
    </a>
  </div>

  <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12" style="margin-bottom:16px">
    <a href="index.php?ruta=pedidos" style="text-decoration:none;display:block">
      <div class="crm-kpi" style="background:linear-gradient(135deg,#f59e0b,#fbbf24)">
        <i class="fa-solid fa-box crm-kpi-icon"></i>
        <div class="crm-kpi-label">Pedidos mes</div>
        <div class="crm-kpi-value"><?php echo number_format(floatval($p['pedidos_mes']), 0); ?></div>
        <div class="crm-kpi-sub"><i class="fa-solid fa-truck"></i> Proveedor</div>
        <div class="crm-kpi-bar"><span style="width:50%"></span></div>
      </div>
    </a>
  </div>

  <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12" style="margin-bottom:16px">
    <a href="index.php?ruta=clientes" style="text-decoration:none;display:block">
      <div class="crm-kpi" style="background:linear-gradient(135deg,#8b5cf6,#a78bfa)">
        <i class="fa-solid fa-user-plus crm-kpi-icon"></i>
        <div class="crm-kpi-label">Prospectos</div>
        <div class="crm-kpi-value"><?php echo number_format(intval($p['prospectos_mes'])); ?></div>
        <div class="crm-kpi-sub">Registros del mes</div>
        <div class="crm-kpi-bar"><span style="width:<?php echo min(100, intval($p['prospectos_mes']) * 10); ?>%"></span></div>
      </div>
    </a>
  </div>
</div>

<?php if ($gaActivo): ?>
<div class="visitas-ga4-kpis">
  <div class="visitas-ga4-stat">
    <div class="val"><?php echo number_format(intval(isset($ga['sesiones']) ? $ga['sesiones'] : 0)); ?></div>
    <div class="lbl">Sesiones GA4 (30d)</div>
  </div>
  <div class="visitas-ga4-stat">
    <div class="val"><?php echo number_format(intval(isset($ga['usuarios']) ? $ga['usuarios'] : 0)); ?></div>
    <div class="lbl">Usuarios</div>
  </div>
  <div class="visitas-ga4-stat">
    <div class="val"><?php echo number_format(intval(isset($ga['pageviews']) ? $ga['pageviews'] : 0)); ?></div>
    <div class="lbl">Vistas página</div>
  </div>
  <div class="visitas-ga4-stat">
    <div class="val"><?php echo floatval(isset($ga['rebote']) ? $ga['rebote'] : 0); ?>%</div>
    <div class="lbl">Rebote prom.</div>
  </div>
</div>
<?php endif; ?>
