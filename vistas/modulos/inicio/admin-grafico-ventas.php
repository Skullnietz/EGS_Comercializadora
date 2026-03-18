<?php
/*  ═══════════════════════════════════════════════════
    DASHBOARD ADMIN — Grafico de Ventas (estilo CRM)
    ═══════════════════════════════════════════════════ */

if (isset($_GET["fechaInicial"])) {
    $fechaInicial = $_GET["fechaInicial"];
    $fechaFinal = $_GET["fechaFinal"];
} else {
    $fechaInicial = null;
    $fechaFinal = null;
}

$respuesta = ControladorVentas::ctrRangoFechasVentas($fechaInicial, $fechaFinal, "id_empresa", $_SESSION["empresa"]);

$arrayFechas = array();
$sumaPagosMes = array();
$noRepetirFechas = array();

if (is_array($respuesta) && count($respuesta) > 0) {
    $arrayVentas = array();
    foreach ($respuesta as $key => $value) {
        $fecha = substr($value["fecha"], 0, 7);
        array_push($arrayFechas, $fecha);
        $arrayVentas = array($fecha => $value["pago"]);
        foreach ($arrayVentas as $k => $v) {
            if (!isset($sumaPagosMes[$k])) $sumaPagosMes[$k] = 0;
            $sumaPagosMes[$k] += $v;
        }
    }
    $noRepetirFechas = array_unique($arrayFechas);
}
?>

<div class="crm-card" style="margin-bottom:20px">
  <div class="crm-card-head">
    <h4 class="crm-card-title"><i class="fa-solid fa-chart-line"></i> Historico de Ventas</h4>
    <span class="crm-badge" style="background:#f0fdf4;color:#16a34a">
      <i class="fa-solid fa-arrow-trend-up" style="font-size:10px"></i> Mensual
    </span>
  </div>
  <div class="crm-card-body" style="padding:16px 20px">
    <div id="admin-line-chart-ventas" style="height:260px"></div>
  </div>
</div>

<script>
new Morris.Line({
  element          : 'admin-line-chart-ventas',
  resize           : true,
  data             : [
    <?php
    if (!empty($noRepetirFechas)) {
        $items = array();
        foreach ($noRepetirFechas as $key) {
            $items[] = "{ y: '".$key."', ventas: ".$sumaPagosMes[$key]." }";
        }
        echo implode(",\n    ", $items);
    } else {
        echo "{ y: '0', ventas: 0 }";
    }
    ?>
  ],
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
  behaveLikeLine   : true
});
</script>
