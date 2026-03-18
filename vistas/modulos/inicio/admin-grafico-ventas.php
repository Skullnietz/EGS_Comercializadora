<?php
/*  ═══════════════════════════════════════════════════
    DASHBOARD ADMIN — Gráfico de Ventas con selector
    de periodo: Todo | Año | Mes | Semana
    ═══════════════════════════════════════════════════ */

$_gv_empresa = $_SESSION["empresa"];

// Cargar sin filtro de fechas para tener todo el histórico
$_gv_all = ControladorVentas::ctrRangoFechasVentas(null, null, "id_empresa", $_gv_empresa);
if (!is_array($_gv_all)) $_gv_all = array();

// ── Pre-calcular datos por periodo ──
$_gv_cortes = array(
    'todo'   => '2000-01-01',
    'anio'   => date("Y-01-01"),
    'mes'    => date("Y-m-01"),
    'semana' => date("Y-m-d", strtotime("monday this week")),
);

$_gv_periodos = array();
foreach ($_gv_cortes as $periodo => $corte) {
    $_gv_sum = array();

    foreach ($_gv_all as $v) {
        $fv = isset($v["fecha"]) ? substr($v["fecha"], 0, 10) : "";
        if ($fv < $corte) continue;

        $pago = floatval(isset($v["pago"]) ? $v["pago"] : 0);

        if ($periodo === 'todo' || $periodo === 'anio') {
            $key = substr($fv, 0, 7);
        } else {
            $key = $fv;
        }

        if (!isset($_gv_sum[$key])) $_gv_sum[$key] = 0;
        $_gv_sum[$key] += $pago;
    }

    ksort($_gv_sum);
    $items = array();
    foreach ($_gv_sum as $k => $val) {
        $items[] = array('y' => $k, 'ventas' => round($val, 2));
    }
    if (empty($items)) {
        $items[] = array('y' => date('Y-m'), 'ventas' => 0);
    }
    $_gv_periodos[$periodo] = $items;
}

$_gv_totales = array();
foreach ($_gv_periodos as $p => $items) {
    $t = 0;
    foreach ($items as $it) $t += $it['ventas'];
    $_gv_totales[$p] = $t;
}
?>

<div class="crm-card" style="margin-bottom:20px">
  <div class="crm-card-head">
    <h4 class="crm-card-title"><i class="fa-solid fa-chart-line"></i> Histórico de Ventas</h4>
    <div style="display:flex;align-items:center;gap:6px">
      <span class="crm-badge" id="gvBadgeTotal" style="background:#f0fdf4;color:#16a34a;margin-right:4px">
        $<?php echo number_format($_gv_totales['mes']); ?>
      </span>
      <div style="display:inline-flex;background:#f1f5f9;border-radius:8px;padding:2px;gap:2px" id="gvFilter">
        <button type="button" class="gv-btn" data-period="todo"
                style="padding:4px 10px;border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;transition:all .15s;background:transparent;color:#64748b">
          Todo
        </button>
        <button type="button" class="gv-btn" data-period="anio"
                style="padding:4px 10px;border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;transition:all .15s;background:transparent;color:#64748b">
          Año
        </button>
        <button type="button" class="gv-btn active" data-period="mes"
                style="padding:4px 10px;border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;transition:all .15s;background:#6366f1;color:#fff">
          Mes
        </button>
        <button type="button" class="gv-btn" data-period="semana"
                style="padding:4px 10px;border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;transition:all .15s;background:transparent;color:#64748b">
          Semana
        </button>
      </div>
    </div>
  </div>
  <div class="crm-card-body" style="padding:16px 20px">
    <div id="admin-line-chart-ventas" style="height:260px"></div>
    <div id="gvEmpty" style="display:none">
      <div class="crm-empty" style="padding:60px 20px">
        <i class="fa-solid fa-chart-line"></i>
        <strong>Sin ventas en este periodo</strong>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
  var gvData = <?php echo json_encode($_gv_periodos); ?>;
  var gvTotales = <?php echo json_encode($_gv_totales); ?>;

  function renderChart(period) {
    var data = gvData[period];
    var $el = $('#admin-line-chart-ventas');
    var $empty = $('#gvEmpty');

    var total = gvTotales[period] || 0;
    $('#gvBadgeTotal').text('$' + Math.round(total).toLocaleString('es-MX'));

    if (!data || data.length === 0 || (data.length === 1 && data[0].ventas === 0)) {
      $el.hide();
      $empty.show();
      return;
    }
    $el.show().empty();
    $empty.hide();

    new Morris.Line({
      element          : 'admin-line-chart-ventas',
      resize           : true,
      data             : data,
      xkey             : 'y',
      ykeys            : ['ventas'],
      labels           : ['Ventas'],
      lineColors       : ['#6366f1'],
      lineWidth        : 2,
      hideHover        : 'auto',
      gridTextColor    : '#94a3b8',
      gridStrokeWidth  : 0.3,
      pointSize        : 5,
      pointStrokeColors: ['#6366f1'],
      pointFillColors  : ['#fff'],
      gridLineColor    : '#e2e8f0',
      gridTextFamily   : 'inherit',
      preUnits         : '$',
      gridTextSize     : 11,
      fillOpacity      : 0.08,
      behaveLikeLine   : true,
      parseTime        : false,
      xLabelAngle      : data.length > 12 ? 45 : 0
    });
  }

  renderChart('mes');

  $('#gvFilter').on('click', '.gv-btn', function(){
    var $btn = $(this);
    var period = $btn.data('period');

    $('#gvFilter .gv-btn').css({ background: 'transparent', color: '#64748b' }).removeClass('active');
    $btn.css({ background: '#6366f1', color: '#fff' }).addClass('active');

    renderChart(period);
  });
})();
</script>
