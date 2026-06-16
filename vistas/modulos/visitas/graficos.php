<?php
/** @var array $panorama */
$p = $panorama;
$tendencia = isset($p['tendencia']) ? $p['tendencia'] : array();
$topPaises = isset($p['top_paises']) ? $p['top_paises'] : array();

$labelsT = array();
$dataT = array();
foreach ($tendencia as $row) {
  $labelsT[] = isset($row['dia']) ? $row['dia'] : '';
  $dataT[] = intval(isset($row['total']) ? $row['total'] : 0);
}

$labelsP = array();
$dataP = array();
$colorsP = array('#6366f1','#3b82f6','#22c55e','#f59e0b','#ef4444','#8b5cf6','#06b6d4','#ec4899');
$i = 0;
foreach ($topPaises as $row) {
  $labelsP[] = isset($row['pais']) ? $row['pais'] : 'N/D';
  $dataP[] = intval(isset($row['total']) ? $row['total'] : 0);
  $i++;
}
?>
<script>
window._visitasChartData = {
  tendencia: { labels: <?php echo json_encode($labelsT); ?>, data: <?php echo json_encode($dataT); ?> },
  paises: { labels: <?php echo json_encode($labelsP); ?>, data: <?php echo json_encode($dataP); ?> }
};
</script>

<div class="row">
  <div class="col-md-8" style="margin-bottom:20px">
    <div class="crm-card">
      <div class="crm-card-head">
        <h4 class="crm-card-title"><i class="fa-solid fa-chart-line"></i> Tendencia de visitas (30 días)</h4>
      </div>
      <div class="crm-card-body">
        <div class="visitas-chart-wrap">
          <canvas id="chartVisitasTendencia"></canvas>
        </div>
        <?php if (empty($tendencia)): ?>
          <p style="font-size:12px;color:var(--crm-muted);margin:8px 0 0">Sin datos de tendencia. Activa el tracking local o GA4.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <div class="col-md-4" style="margin-bottom:20px">
    <div class="crm-card">
      <div class="crm-card-head">
        <h4 class="crm-card-title"><i class="fa-solid fa-map"></i> Top países</h4>
      </div>
      <div class="crm-card-body">
        <div class="visitas-chart-wrap">
          <canvas id="chartVisitasPaises"></canvas>
        </div>
        <?php if (empty($topPaises)): ?>
          <p style="font-size:12px;color:var(--crm-muted);margin:8px 0 0">Sin datos geográficos.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-6" style="margin-bottom:20px">
    <div class="crm-card">
      <div class="crm-card-body" style="padding:18px 20px">
        <div style="font-size:11px;font-weight:600;text-transform:uppercase;color:var(--crm-muted)">Engagement</div>
        <div style="font-size:24px;font-weight:800;color:var(--crm-text)">
          <?php echo floatval($p['promedio_ip']['promedio']); ?>
          <span style="font-size:14px;font-weight:600;color:var(--crm-muted)">visitas / IP</span>
        </div>
        <div style="font-size:12px;color:var(--crm-muted);margin-top:6px">
          <?php echo number_format(intval($p['promedio_ip']['ips'])); ?> IPs únicas registradas
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-6" style="margin-bottom:20px">
    <div class="crm-card">
      <div class="crm-card-body" style="padding:18px 20px">
        <div style="font-size:11px;font-weight:600;text-transform:uppercase;color:var(--crm-muted)">Órdenes de servicio (mes)</div>
        <div style="font-size:24px;font-weight:800;color:var(--crm-text)"><?php echo number_format(intval($p['ordenes_ingresadas'])); ?></div>
        <div style="font-size:12px;color:var(--crm-muted);margin-top:6px">
          <a href="index.php?ruta=ordenes">Ver órdenes &rarr;</a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php if (!empty($p['productos_top'])): ?>
<div class="crm-card" style="margin-bottom:20px">
  <div class="crm-card-head">
    <h4 class="crm-card-title"><i class="fa-solid fa-star"></i> Productos más vendidos (tienda)</h4>
  </div>
  <div class="crm-card-body-flush">
    <table class="crm-table">
      <thead>
        <tr><th>Producto</th><th>Ventas</th><th>% del total</th></tr>
      </thead>
      <tbody>
        <?php
        $totalV = floatval($p['total_prod_ventas']);
        foreach (array_slice($p['productos_top'], 0, 5) as $prod):
          $v = intval(isset($prod['ventas']) ? $prod['ventas'] : 0);
          $pct = $totalV > 0 ? round($v * 100 / $totalV, 1) : 0;
          $desc = isset($prod['descripcion']) ? $prod['descripcion'] : (isset($prod['titulo']) ? $prod['titulo'] : '—');
        ?>
        <tr>
          <td><?php echo htmlspecialchars(substr($desc, 0, 60)); ?></td>
          <td><?php echo number_format($v); ?></td>
          <td><?php echo $pct; ?>%</td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>
