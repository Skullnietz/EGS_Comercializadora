<?php
if($_SESSION["perfil"] != "administrador" AND $_SESSION["perfil"]!= "vendedor" AND $_SESSION["perfil"]!= "tecnico" AND $_SESSION["perfil"]!= "secretaria" AND $_SESSION["perfil"]!= "Super-Administrador"){
  echo '<script>window.location = "inicio";</script>';
  return;
}

/* ── Helper: badge de estado ── */
function _reportGetBadgeClass($estadoText) {
    $e = trim(mb_strtolower((string)$estadoText, 'UTF-8'));
    if (strpos($e, 'autorización') !== false || strpos($e, 'autorizacion') !== false || $e === 'aut') return 'badge-pendiente-aut';
    if (strpos($e, 'pendiente') !== false) return 'badge-pendiente-aut';
    if (strpos($e, 'supervisión') !== false || strpos($e, 'supervision') !== false || $e === 'sup') return 'badge-supervision';
    if (strpos($e, 'garantía aceptada') !== false || strpos($e, 'garantia aceptada') !== false || $e === 'ga') return 'badge-garantia-acep';
    if (strpos($e, 'probable garantía') !== false || strpos($e, 'probable garantia') !== false) return 'badge-prob-garantia';
    if (strpos($e, 'garantía') !== false || strpos($e, 'garantia') !== false) return 'badge-garantia';
    if (strpos($e, 'revisión') !== false || strpos($e, 'revision') !== false || $e === 'rev') return 'badge-revision';
    if (strpos($e, 'terminada') !== false || $e === 'ter') return 'badge-terminado';
    if (strpos($e, 'entregado al asesor') !== false) return 'badge-entreg-asesor';
    if (strpos($e, 'entregado/pagado') !== false) return 'badge-entreg-pagado';
    if (strpos($e, 'entregado/credito') !== false || strpos($e, 'entregado/crédito') !== false) return 'badge-entreg-credito';
    if (strpos($e, 'entregado') !== false || strpos($e, 'entregada') !== false) return 'badge-entregado';
    if (strpos($e, 'aceptado') !== false || strpos($e, 'aceptada') !== false || $e === 'ok') return 'badge-aceptado';
    if (strpos($e, 'cancel') !== false) return 'badge-cancelada';
    if (strpos($e, 'sin reparación') !== false || strpos($e, 'sin reparacion') !== false || $e === 'sr') return 'badge-sin-reparacion';
    if (strpos($e, 'producto para venta') !== false || $e === 'pv') return 'badge-producto-venta';
    if (strpos($e, 'producto en almac') !== false) return 'badge-prod-almacen';
    if (strpos($e, 'seguimiento') !== false) return 'badge-seguimiento';
    return 'badge-otro';
}

/* ── Obtener datos (solo si hay rango de fecha o se pidió mostrar todas) ── */
$tieneRango    = isset($_GET["fechaInicial"]);
$mostrarTodas  = isset($_GET["mostrarTodas"]);
$cargarDatos   = $tieneRango || $mostrarTodas;

$fechaInicial = null;
$fechaFinal   = null;
$respuesta    = [];

if($cargarDatos){
    if($tieneRango){
        $fechaInicial = $_GET["fechaInicial"];
        $fechaFinal   = $_GET["fechaFinal"];
    }

    if ($_SESSION["perfil"] == "Super-Administrador"){
        $respuesta = controladorOrdenes::ctrRangoFechasOrdenesSuperAdmin($fechaInicial, $fechaFinal);
    }else{
        $respuesta = controladorOrdenes::ctrRangoFechasOrdenes($fechaInicial, $fechaFinal, "id_empresa", $_SESSION["empresa"]);
    }

    if(!is_array($respuesta)) $respuesta = [];

    usort($respuesta, function($a, $b){
      return intval($a["id"]) <=> intval($b["id"]);
    });
}

/* ── Calcular resumen ── */
$totalOrdenes   = count($respuesta);
$sumaTotal      = 0;
$sumaInversion  = 0;
$conteoEstados  = [];
$estadosUnicos  = [];

foreach($respuesta as $v){
    $sumaTotal     += floatval($v["total"]);
    $sumaInversion += floatval($v["totalInversion"] ?? 0);
    $est = $v["estado"] ?: "Sin estado";
    if(!isset($conteoEstados[$est])) $conteoEstados[$est] = 0;
    $conteoEstados[$est]++;
    $estadosUnicos[$est] = true;
}

$utilidad = $sumaTotal - $sumaInversion;

/* ── URL params para descargas ── */
$fechaParams = isset($_GET["fechaInicial"])
    ? "&fechaInicial=".$_GET["fechaInicial"]."&fechaFinal=".$_GET["fechaFinal"]
    : "";
$empParam = "&empresa=".$_SESSION["empresa"];
$perfil   = $_SESSION["perfil"];
$isAdmin  = in_array($perfil, ['administrador','Super-Administrador']);

function _dlLink($file, $reporte, $reporteFecha, $fechaParams, $empParam, $extra = "") {
    if($fechaParams){
        return "vistas/modulos/{$file}?reporte={$reporteFecha}{$fechaParams}{$empParam}{$extra}";
    }
    return "vistas/modulos/{$file}?reporte={$reporte}{$empParam}{$extra}";
}

$rangoTexto = $mostrarTodas ? "Todas las órdenes" : "Selecciona un rango";
if($fechaInicial && $fechaFinal){
    if($fechaInicial === $fechaFinal){
        $rangoTexto = date("d/m/Y", strtotime($fechaInicial));
    } else {
        $rangoTexto = date("d/m/Y", strtotime($fechaInicial)) . " — " . date("d/m/Y", strtotime($fechaFinal));
    }
}

/* ── Índices de columna para JS (dependen del perfil) ── */
$colEstado    = 6;
$colTotal     = 7;
$colInversion = $isAdmin ? 8  : -1;
$colUtilidad  = $isAdmin ? 9  : -1;
$colIngreso   = $isAdmin ? 10 : 8;
$colFecha     = $isAdmin ? 11 : 9;
$totalCols    = $isAdmin ? 12 : 10;
?>

<style>
:root {
  --rpt-bg: #f8fafc;
  --rpt-card: #ffffff;
  --rpt-border: #e2e8f0;
  --rpt-text: #334155;
  --rpt-muted: #94a3b8;
  --rpt-accent: #3b82f6;
}

.rpt-header {
  display: flex; align-items: center; justify-content: space-between;
  flex-wrap: wrap; gap: 12px; margin-bottom: 20px;
}
.rpt-header h1 { font-size: 22px; font-weight: 700; color: var(--rpt-text); margin: 0; }
.rpt-header h1 i { color: var(--rpt-accent); margin-right: 8px; }
.rpt-date-badge {
  display: inline-flex; align-items: center; gap: 6px;
  background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe;
  border-radius: 20px; padding: 6px 14px; font-size: 13px; font-weight: 600;
}

/* ── KPI Cards ── */
.rpt-kpi-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 14px; margin-bottom: 20px; }
.rpt-kpi {
  background: var(--rpt-card); border: 1px solid var(--rpt-border); border-radius: 12px;
  padding: 18px 20px; display: flex; align-items: center; gap: 14px;
  transition: transform .15s, box-shadow .15s;
}
.rpt-kpi:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,.07); }
.rpt-kpi-icon {
  width: 46px; height: 46px; border-radius: 12px;
  display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;
}
.rpt-kpi-icon.blue   { background: #eff6ff; color: #3b82f6; }
.rpt-kpi-icon.green  { background: #f0fdf4; color: #22c55e; }
.rpt-kpi-icon.amber  { background: #fffbeb; color: #f59e0b; }
.rpt-kpi-icon.purple { background: #f5f3ff; color: #8b5cf6; }
.rpt-kpi-body h4 { margin: 0 0 2px; font-size: 20px; font-weight: 700; color: var(--rpt-text); }
.rpt-kpi-body small { color: var(--rpt-muted); font-size: 12px; font-weight: 500; }

/* ── Download buttons ── */
.rpt-downloads { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 18px; }
.rpt-dl-btn {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 7px 14px; border-radius: 8px; font-size: 12px; font-weight: 600;
  color: #fff; text-decoration: none; transition: opacity .15s, transform .15s; border: none; cursor: pointer;
}
.rpt-dl-btn:hover { opacity: .88; transform: translateY(-1px); color: #fff; text-decoration: none; }
.rpt-dl-btn.green  { background: #22c55e; }
.rpt-dl-btn.blue   { background: #3b82f6; }
.rpt-dl-btn.amber  { background: #f59e0b; }
.rpt-dl-btn.purple { background: #8b5cf6; }
.rpt-dl-btn.cyan   { background: #06b6d4; }
.rpt-dl-btn.rose   { background: #f43f5e; }
.rpt-dl-btn.slate  { background: #64748b; }

/* ── Date picker button ── */
.rpt-datepicker-btn {
  display: inline-flex; align-items: center; gap: 8px;
  padding: 9px 18px; background: var(--rpt-card); border: 1px solid var(--rpt-border);
  border-radius: 10px; color: var(--rpt-text); font-size: 13px; font-weight: 600;
  cursor: pointer; transition: border-color .15s, box-shadow .15s;
}
.rpt-datepicker-btn:hover { border-color: var(--rpt-accent); box-shadow: 0 0 0 3px rgba(59,130,246,.1); }
.rpt-datepicker-btn i { color: var(--rpt-accent); }

/* ── Status distribution ── */
.rpt-estados-row { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 18px; }
.rpt-estado-chip {
  display: inline-flex; align-items: center; gap: 5px;
  padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;
  border: 1px solid transparent; cursor: pointer; transition: opacity .15s;
}
.rpt-estado-chip:hover { opacity: .8; }
.rpt-estado-chip .count {
  background: rgba(0,0,0,.08); border-radius: 10px; padding: 1px 7px; font-size: 11px; font-weight: 700;
}

/* ── Table ── */
.rpt-table-wrap {
  background: var(--rpt-card); border: 1px solid var(--rpt-border);
  border-radius: 12px; overflow: hidden;
}
.rpt-table-wrap table { margin-bottom: 0; }
.rpt-table-wrap thead th {
  background: #f8fafc; border-bottom: 2px solid var(--rpt-border);
  color: var(--rpt-muted); font-size: 11px; font-weight: 700;
  text-transform: uppercase; letter-spacing: .04em; padding: 10px 12px; white-space: nowrap;
}
.rpt-table-wrap tbody td {
  padding: 10px 12px; font-size: 13px; color: var(--rpt-text);
  vertical-align: middle; border-bottom: 1px solid #f1f5f9;
}
.rpt-table-wrap tbody tr:hover { background: #f8fafc; }
.rpt-table-wrap tbody tr:last-child td { border-bottom: none; }

/* ── Filter row ── */
.rpt-table-wrap thead tr.rpt-filter-row th {
  background: #fff; padding: 6px 8px; border-bottom: 2px solid var(--rpt-border);
}
.rpt-filter-input {
  width: 100%; padding: 5px 8px; border: 1px solid #e2e8f0; border-radius: 6px;
  font-size: 12px; color: var(--rpt-text); background: #f8fafc;
  transition: border-color .15s, box-shadow .15s; outline: none;
}
.rpt-filter-input:focus {
  border-color: var(--rpt-accent); box-shadow: 0 0 0 2px rgba(59,130,246,.12);
  background: #fff;
}
.rpt-filter-select {
  width: 100%; padding: 5px 8px; border: 1px solid #e2e8f0; border-radius: 6px;
  font-size: 12px; color: var(--rpt-text); background: #f8fafc;
  transition: border-color .15s, box-shadow .15s; outline: none;
  cursor: pointer; appearance: auto;
}
.rpt-filter-select:focus {
  border-color: var(--rpt-accent); box-shadow: 0 0 0 2px rgba(59,130,246,.12);
  background: #fff;
}

.rpt-orden-link { font-weight: 700; color: var(--rpt-accent); text-decoration: none; }
.rpt-orden-link:hover { text-decoration: underline; color: #2563eb; }
.rpt-equipo-info { display: flex; flex-direction: column; gap: 1px; }
.rpt-equipo-info .marca { font-weight: 600; color: var(--rpt-text); }
.rpt-equipo-info .modelo { font-size: 11px; color: var(--rpt-muted); }
.rpt-money { font-weight: 600; white-space: nowrap; }
.rpt-money.positive { color: #16a34a; }
.rpt-money.negative { color: #dc2626; }

.rpt-table-wrap .badge {
  display: inline-block; padding: 4px 10px; border-radius: 20px;
  font-size: 11px; font-weight: 600; border: 1px solid transparent; white-space: nowrap;
}

/* DataTables overrides */
.rpt-table-wrap .dataTables_wrapper { padding: 0; }
.rpt-table-wrap .dataTables_wrapper .dataTables_length,
.rpt-table-wrap .dataTables_wrapper .dataTables_filter { display: none; }
.rpt-table-wrap .rpt-dt-bottom {
  display: flex; align-items: center; justify-content: space-between;
  flex-wrap: wrap; gap: 10px;
  padding: 12px 16px; border-top: 1px solid var(--rpt-border);
  background: #f8fafc;
}
.rpt-table-wrap .dataTables_wrapper .dataTables_info {
  padding: 0; font-size: 12px; color: var(--rpt-muted); margin: 0;
}
.rpt-table-wrap .dataTables_wrapper .dataTables_paginate {
  padding: 0; margin: 0;
}
.rpt-table-wrap .dataTables_wrapper .dataTables_paginate .paginate_button {
  padding: 5px 11px !important; border-radius: 6px !important; font-size: 12px !important;
  border: 1px solid var(--rpt-border) !important; margin: 0 2px;
  background: #fff !important; color: var(--rpt-text) !important;
}
.rpt-table-wrap .dataTables_wrapper .dataTables_paginate .paginate_button.current {
  background: var(--rpt-accent) !important; color: #fff !important;
  border-color: var(--rpt-accent) !important;
}
.rpt-table-wrap .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
  background: #eff6ff !important; color: var(--rpt-accent) !important;
  border-color: var(--rpt-accent) !important;
}
.rpt-table-wrap .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
  opacity: .4; cursor: default;
}
.rpt-table-wrap .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
  background: #fff !important; color: var(--rpt-text) !important;
  border-color: var(--rpt-border) !important;
}

/* ── Responsive ── */
@media (max-width: 767px) {
  .rpt-kpi-row { grid-template-columns: repeat(2, 1fr); }
  .rpt-header { flex-direction: column; align-items: flex-start; }
  .rpt-table-wrap { overflow-x: auto; }
}
</style>

<div class="content-wrapper" style="background: var(--rpt-bg);">
  <section class="content" style="padding-top: 20px;">

    <!-- ═══ Header ═══ -->
    <div class="rpt-header">
      <div>
        <h1><i class="fa-solid fa-clipboard-list"></i>Reporte de Órdenes</h1>
      </div>
      <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
        <span class="rpt-date-badge">
          <i class="fa-regular fa-calendar"></i>
          <?= htmlspecialchars($rangoTexto) ?>
        </span>
        <button type="button" class="rpt-datepicker-btn" id="daterange-btnOrdenes">
          <i class="fa-solid fa-calendar-days"></i>
          <span>Cambiar rango</span>
          <i class="fa-solid fa-chevron-down" style="font-size:10px;opacity:.5;"></i>
        </button>
      </div>
    </div>

    <?php if(!$cargarDatos): ?>
    <!-- ═══ Landing / Preview ═══ -->
    <div style="background:var(--rpt-card); border:1px solid var(--rpt-border); border-radius:16px; padding:60px 30px; text-align:center; max-width:600px; margin:40px auto;">
      <i class="fa-solid fa-chart-bar" style="font-size:56px; color:var(--rpt-accent); opacity:.25; display:block; margin-bottom:20px;"></i>
      <h3 style="font-size:20px; font-weight:700; color:var(--rpt-text); margin:0 0 8px;">Selecciona un rango de fechas</h3>
      <p style="font-size:14px; color:var(--rpt-muted); margin:0 0 28px; line-height:1.6;">
        Usa el botón <strong>Cambiar rango</strong> de arriba para filtrar las órdenes por periodo,<br>
        o muestra todas las órdenes del sistema.
      </p>
      <div style="display:flex; justify-content:center; gap:12px; flex-wrap:wrap;">
        <button type="button" class="rpt-datepicker-btn" onclick="$('#daterange-btnOrdenes').click()">
          <i class="fa-solid fa-calendar-days"></i>
          Elegir rango de fechas
        </button>
        <a href="index.php?ruta=reportePorFecheOrdenes&mostrarTodas=1"
           style="display:inline-flex; align-items:center; gap:8px; padding:9px 18px;
                  background:var(--rpt-accent); color:#fff; border-radius:10px; font-size:13px;
                  font-weight:600; text-decoration:none; transition:opacity .15s;"
           onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
          <i class="fa-solid fa-list"></i>
          Mostrar todas las órdenes
        </a>
      </div>
    </div>
    <?php else: ?>

    <!-- ═══ KPI Cards ═══ -->
    <div class="rpt-kpi-row">
      <div class="rpt-kpi">
        <div class="rpt-kpi-icon blue"><i class="fa-solid fa-clipboard-list"></i></div>
        <div class="rpt-kpi-body">
          <h4 id="kpi-ordenes"><?= number_format($totalOrdenes) ?></h4>
          <small>Total Órdenes</small>
        </div>
      </div>
      <div class="rpt-kpi">
        <div class="rpt-kpi-icon green"><i class="fa-solid fa-dollar-sign"></i></div>
        <div class="rpt-kpi-body">
          <h4 id="kpi-ingresos">$<?= number_format($sumaTotal, 2) ?></h4>
          <small>Ingresos</small>
        </div>
      </div>
      <div class="rpt-kpi">
        <div class="rpt-kpi-icon amber"><i class="fa-solid fa-coins"></i></div>
        <div class="rpt-kpi-body">
          <h4 id="kpi-inversion">$<?= number_format($sumaInversion, 2) ?></h4>
          <small>Inversión</small>
        </div>
      </div>
      <div class="rpt-kpi">
        <div class="rpt-kpi-icon purple"><i class="fa-solid fa-chart-line"></i></div>
        <div class="rpt-kpi-body">
          <h4 id="kpi-utilidad">$<?= number_format($utilidad, 2) ?></h4>
          <small>Utilidad</small>
        </div>
      </div>
    </div>

    <!-- ═══ Distribución de estados ═══ -->
    <?php if(!empty($conteoEstados)): ?>
    <div class="rpt-estados-row" id="rpt-estados-row">
      <?php foreach($conteoEstados as $est => $cnt):
        $badgeCls = _reportGetBadgeClass($est);
      ?>
        <span class="rpt-estado-chip <?= $badgeCls ?>" data-estado="<?= htmlspecialchars($est) ?>">
          <?= htmlspecialchars($est) ?>
          <span class="count" data-estado-count="<?= htmlspecialchars($est) ?>"><?= $cnt ?></span>
        </span>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- ═══ Botones de descarga ═══ -->
    <div class="rpt-downloads">
      <?php if($perfil == "administrador"): ?>
        <a class="rpt-dl-btn green" href="<?= _dlLink('descargar-reporte-Ordenes.php','ordenes','reporte',$fechaParams,$empParam) ?>">
          <i class="fa-solid fa-file-excel"></i> Descargar Reporte
        </a>
        <a class="rpt-dl-btn blue" href="<?= _dlLink('descargar-reporte-infoOrden.php','infoordenes','infoordenes','',$empParam) ?>">
          <i class="fa-solid fa-file-excel"></i> Info Órdenes
        </a>
        <a class="rpt-dl-btn cyan" href="<?= _dlLink('descargar-reporte-Ordenes-ingresos.php','ordenesIgresos','reporteIngresos',$fechaParams,$empParam) ?>">
          <i class="fa-solid fa-file-excel"></i> Ingresos
        </a>
      <?php endif; ?>

      <?php if(in_array($perfil, ['administrador','tecnico','vendedor'])): ?>
        <a class="rpt-dl-btn amber" href="<?= _dlLink('descargar-reporte-OrdenesPEN.php','ordenesPEN','reporte',$fechaParams,$empParam) ?>">
          <i class="fa-solid fa-file-excel"></i> REV
        </a>
      <?php endif; ?>

      <?php if(in_array($perfil, ['administrador','vendedor'])): ?>
        <a class="rpt-dl-btn purple" href="<?= _dlLink('descargar-reporte-OrdenesSup.php','ordenesSup','reporte',$fechaParams,$empParam) ?>">
          <i class="fa-solid fa-file-excel"></i> SUP
        </a>
      <?php endif; ?>

      <?php if(in_array($perfil, ['administrador','editor','vendedor'])): ?>
        <a class="rpt-dl-btn slate" href="<?= _dlLink('descargar-reporte-OrdenesAut.php','ordenesAUT','reporte',$fechaParams,$empParam) ?>">
          <i class="fa-solid fa-file-excel"></i> AUT
        </a>
      <?php endif; ?>

      <?php if(in_array($perfil, ['administrador','editor','tecnico','vendedor'])): ?>
        <a class="rpt-dl-btn blue" href="<?= _dlLink('descargar-reporte-OrdenesOK.php','ordenesOk','reporte',$fechaParams,$empParam) ?>">
          <i class="fa-solid fa-file-excel"></i> OK
        </a>
        <a class="rpt-dl-btn cyan" href="<?= _dlLink('descargar-reporte-OrdenesTer.php','ordenesTer','reporte',$fechaParams,$empParam) ?>">
          <i class="fa-solid fa-file-excel"></i> TER
        </a>
      <?php endif; ?>

      <?php if(in_array($perfil, ['administrador','secretaria','vendedor'])): ?>
        <a class="rpt-dl-btn green" href="<?= _dlLink('descargar-reporte-OrdenesEntregadas.php','ordenesENT','reporte',$fechaParams,$empParam) ?>">
          <i class="fa-solid fa-file-excel"></i> ENT
        </a>
        <a class="rpt-dl-btn rose" href="<?= _dlLink('descargar-reporte-OrdenesParaVenta.php','ordenesVenta','reporte',$fechaParams,$empParam) ?>">
          <i class="fa-solid fa-file-excel"></i> Para Venta
        </a>
      <?php endif; ?>

      <?php if($perfil == "administrador"): ?>
        <a class="rpt-dl-btn slate" href="vistas/modulos/descargar-reporte-marca.php?reporte=ordenespormarca<?= $empParam ?>">
          <i class="fa-solid fa-file-excel"></i> Por Marca
        </a>
      <?php endif; ?>

      <?php if($perfil == "tecnico"):
        $tecnicoEnSession = ControladorTecnicos::ctrMostrarTecnicos("correo", $_SESSION["email"]);
        $estadoreporte = "Entregado (Ent)";
      ?>
        <?php if(isset($_GET["fechaInicial"])): ?>
          <a class="rpt-dl-btn green" href="vistas/modulos/descargar-reporte-OrdenesEntregadas.php?reporte=entregadas<?= $fechaParams.$empParam ?>">
            <i class="fa-solid fa-file-excel"></i> ENT
          </a>
        <?php else: ?>
          <a class="rpt-dl-btn green" href="vistas/modulos/descargar-reporte-OrdenesPorEstado.php?reporte=ordenesTER<?= $empParam ?>&estado=<?= urlencode($estadoreporte) ?>&tecnico=<?= $tecnicoEnSession["id"] ?>">
            <i class="fa-solid fa-file-excel"></i> ENT
          </a>
        <?php endif; ?>
      <?php endif; ?>
    </div>

    <!-- ═══ Tabla de órdenes ═══ -->
    <div class="rpt-table-wrap">
      <table id="rptOrdenesTable" class="table dt-responsive" width="100%">
        <thead>
          <!-- Fila de títulos -->
          <tr>
            <th>#</th>
            <th>Orden</th>
            <th>Equipo</th>
            <th>Empresa</th>
            <th>Asesor</th>
            <th>Cliente</th>
            <th>Estado</th>
            <th>Total</th>
            <?php if($isAdmin): ?>
              <th>Inversión</th>
              <th>Utilidad</th>
            <?php endif; ?>
            <th>Ingreso</th>
            <th>Fecha</th>
          </tr>
          <!-- Fila de filtros -->
          <tr class="rpt-filter-row">
            <th></th>
            <th><input type="text" class="rpt-filter-input" placeholder="# orden..." data-col="1"></th>
            <th><input type="text" class="rpt-filter-input" placeholder="Marca, modelo..." data-col="2"></th>
            <th><input type="text" class="rpt-filter-input" placeholder="Empresa..." data-col="3"></th>
            <th><input type="text" class="rpt-filter-input" placeholder="Asesor..." data-col="4"></th>
            <th><input type="text" class="rpt-filter-input" placeholder="Cliente..." data-col="5"></th>
            <th>
              <select class="rpt-filter-select" data-col="6">
                <option value="">Todos</option>
                <?php foreach(array_keys($estadosUnicos) as $est): ?>
                  <option value="<?= htmlspecialchars($est) ?>"><?= htmlspecialchars($est) ?></option>
                <?php endforeach; ?>
              </select>
            </th>
            <th><input type="text" class="rpt-filter-input" placeholder="$..." data-col="7"></th>
            <?php if($isAdmin): ?>
              <th><input type="text" class="rpt-filter-input" placeholder="$..." data-col="8"></th>
              <th><input type="text" class="rpt-filter-input" placeholder="$..." data-col="9"></th>
            <?php endif; ?>
            <th></th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($respuesta as $key => $value):
            $NameEmpresa   = ControladorVentas::ctrMostrarEmpresasParaTiketimp("id", $value["id_empresa"]);
            $NombreEmpresa = $NameEmpresa["empresa"] ?? "—";

            $asesor       = Controladorasesores::ctrMostrarAsesoresEleg("id", $value["id_Asesor"]);
            $NombreAsesor = $asesor["nombre"] ?? "—";

            $usuario        = ControladorClientes::ctrMostrarClientes("id", $value["id_usuario"]);
            $NombreUsuario  = $usuario["nombre"] ?? "—";

            $total     = floatval($value["total"]);
            $inversion = floatval($value["totalInversion"] ?? 0);
            $util      = $total - $inversion;

            $marca  = $value["marcaDelEquipo"] ?? "";
            $modelo = $value["modeloDelEquipo"] ?? "";

            $fechaIngreso = $value["fecha_ingreso"] ?? "";
            $fechaOrden   = $value["fecha"] ?? "";
          ?>
          <tr data-total="<?= $total ?>" data-inversion="<?= $inversion ?>" data-estado="<?= htmlspecialchars($value["estado"]) ?>">
            <td><?= $key + 1 ?></td>
            <td data-order="<?= intval($value["id"]) ?>">
              <a class="rpt-orden-link" href="index.php?ruta=infoOrden&idOrden=<?= $value["id"] ?>">
                #<?= $value["id"] ?>
              </a>
            </td>
            <td>
              <?php if($marca || $modelo): ?>
                <div class="rpt-equipo-info">
                  <span class="marca"><?= htmlspecialchars($marca) ?></span>
                  <?php if($modelo): ?><span class="modelo"><?= htmlspecialchars($modelo) ?></span><?php endif; ?>
                </div>
              <?php else: ?>
                <span style="color:var(--rpt-muted);">—</span>
              <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($NombreEmpresa) ?></td>
            <td><?= htmlspecialchars($NombreAsesor) ?></td>
            <td><?= htmlspecialchars($NombreUsuario) ?></td>
            <td>
              <span class="badge <?= _reportGetBadgeClass($value["estado"]) ?>">
                <?= htmlspecialchars($value["estado"]) ?>
              </span>
            </td>
            <td class="rpt-money">$<?= number_format($total, 2) ?></td>
            <?php if($isAdmin): ?>
              <td class="rpt-money" style="color:#f59e0b;">$<?= number_format($inversion, 2) ?></td>
              <td class="rpt-money <?= $util >= 0 ? 'positive' : 'negative' ?>">$<?= number_format($util, 2) ?></td>
            <?php endif; ?>
            <td style="white-space:nowrap; font-size:12px; color:var(--rpt-muted);">
              <?= $fechaIngreso ? date("d/m/Y", strtotime($fechaIngreso)) : "—" ?>
            </td>
            <td style="white-space:nowrap; font-size:12px;" data-order="<?= $fechaOrden ?>">
              <?= $fechaOrden ? date("d/m/Y H:i", strtotime($fechaOrden)) : "—" ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <?php if(empty($respuesta) && $cargarDatos): ?>
    <div style="text-align:center; padding:60px 20px; color:var(--rpt-muted);">
      <i class="fa-solid fa-inbox" style="font-size:48px; opacity:.3; display:block; margin-bottom:14px;"></i>
      <p style="font-size:15px; font-weight:600;">No hay órdenes para este rango</p>
      <p style="font-size:13px;">Selecciona un rango de fecha diferente</p>
    </div>
    <?php endif; ?>

    <?php endif; /* cierra if($cargarDatos) else */ ?>

  </section>
</div>

<!-- ═══ JS: DataTables + Filtros + Recálculo de KPIs ═══ -->
<?php if($cargarDatos): ?>
<script>
(function(){
  var IS_ADMIN   = <?= $isAdmin ? 'true' : 'false' ?>;
  var COL_ESTADO = <?= $colEstado ?>;
  var COL_TOTAL  = <?= $colTotal ?>;

  // Inicializar DataTables
  var table = $('#rptOrdenesTable').DataTable({
    paging: true,
    pageLength: 50,
    lengthChange: false,
    searching: true,
    info: true,
    ordering: true,
    order: [[1, 'asc']], // Orden ascendente por id de orden
    orderCellsTop: true,  // Click en la fila de títulos para ordenar
    language: {
      info: "Mostrando _START_ a _END_ de _TOTAL_ órdenes",
      infoEmpty: "Sin órdenes",
      infoFiltered: "(filtrado de _MAX_ totales)",
      zeroRecords: '<div style="text-align:center;padding:30px;color:#94a3b8;"><i class="fa-solid fa-filter" style="font-size:28px;opacity:.3;display:block;margin-bottom:10px;"></i>Sin resultados con estos filtros</div>',
      paginate: { first: "«", last: "»", next: "›", previous: "‹" }
    },
    dom: '<"rpt-dt-top"r>t<"rpt-dt-bottom"ip>',
    columnDefs: [
      { orderable: false, targets: 0 } // columna #
    ]
  });

  /* ── Filtros por columna (inputs de texto) ── */
  $('.rpt-filter-input').on('keyup change', function(){
    var col = parseInt($(this).data('col'));
    table.column(col).search(this.value).draw();
  });

  /* ── Filtro selector de estado ── */
  $('.rpt-filter-select').on('change', function(){
    var col = parseInt($(this).data('col'));
    var val = $(this).val();
    // Búsqueda exacta con regex
    if(val){
      table.column(col).search('^' + $.fn.dataTable.util.escapeRegex(val) + '$', true, false).draw();
    } else {
      table.column(col).search('').draw();
    }
  });

  /* ── Click en chips de estado para filtrar rápido ── */
  $(document).on('click', '.rpt-estado-chip', function(){
    var estado = $(this).data('estado');
    var $sel = $('.rpt-filter-select[data-col="' + COL_ESTADO + '"]');
    // Toggle: si ya está seleccionado, limpiar
    if($sel.val() === estado){
      $sel.val('').trigger('change');
    } else {
      $sel.val(estado).trigger('change');
    }
  });

  /* ── Recalcular KPIs cada vez que se filtra/dibuja la tabla ── */
  function recalcKPIs(){
    var totalOrdenes = 0;
    var sumaTotal    = 0;
    var sumaInv      = 0;
    var estados      = {};

    table.rows({ search: 'applied' }).every(function(){
      var $row = $(this.node());
      totalOrdenes++;
      sumaTotal += parseFloat($row.data('total')) || 0;
      sumaInv   += parseFloat($row.data('inversion')) || 0;
      var est = $row.data('estado') || 'Sin estado';
      estados[est] = (estados[est] || 0) + 1;
    });

    var utilidad = sumaTotal - sumaInv;

    $('#kpi-ordenes').text(totalOrdenes.toLocaleString('es-MX'));
    $('#kpi-ingresos').text('$' + sumaTotal.toLocaleString('es-MX', {minimumFractionDigits:2, maximumFractionDigits:2}));
    $('#kpi-inversion').text('$' + sumaInv.toLocaleString('es-MX', {minimumFractionDigits:2, maximumFractionDigits:2}));

    var $util = $('#kpi-utilidad');
    $util.text('$' + utilidad.toLocaleString('es-MX', {minimumFractionDigits:2, maximumFractionDigits:2}));
    $util.removeClass('rpt-money negative positive');
    if(utilidad < 0) $util.addClass('rpt-money negative');

    // Actualizar conteos en chips de estado
    $('[data-estado-count]').each(function(){
      var est = $(this).data('estado-count');
      $(this).text(estados[est] || 0);
    });

    // Renumerar columna #
    var idx = 1;
    table.rows({ search: 'applied' }).every(function(){
      var $row = $(this.node());
      $row.find('td:first').text(idx++);
    });
  }

  table.on('draw', recalcKPIs);

  // Cálculo inicial
  recalcKPIs();

})();
</script>
<?php endif; ?>
