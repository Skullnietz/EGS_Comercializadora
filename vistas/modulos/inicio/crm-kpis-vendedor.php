<?php
/*  ═══════════════════════════════════════════════════
    CRM — KPIs principales del vendedor
    Usa datos pre-cargados desde inicio.php:
      $_crm_idAsesor, $_crm_kpis, $_crm_ordAUT,
      $_crm_numAUT, $_crm_numClientes, $_crm_cotizaciones
    ═══════════════════════════════════════════════════ */

// ── Datos desde la pre-carga (1 query SQL con SUM + COUNT) ──
$_crm_totalEntregado = floatval(isset($_crm_kpis['total_entregado']) ? $_crm_kpis['total_entregado'] : 0);
$_crm_numEntregadas  = intval(isset($_crm_kpis['num_entregadas']) ? $_crm_kpis['num_entregadas'] : 0);
$_crm_numEntradas    = intval(isset($_crm_kpis['num_entradas']) ? $_crm_kpis['num_entradas'] : 0);

$_crm_numCotizaciones = count($_crm_cotizaciones);

// ── Tasa de conversión (entregadas / ingresadas) ──
$_crm_tasaConversion = $_crm_numEntradas > 0
    ? round(($_crm_numEntregadas / $_crm_numEntradas) * 100, 1)
    : 0;

// ── Colores dinámicos conversión ──
if ($_crm_tasaConversion >= 70) { $_crm_convGrad = '#059669,#10b981'; }
elseif ($_crm_tasaConversion >= 40) { $_crm_convGrad = '#d97706,#f59e0b'; }
else { $_crm_convGrad = '#dc2626,#ef4444'; }
?>

<div class="row">
  <!-- $ Ventas -->
  <div class="col-lg-3 col-sm-6 col-xs-12" style="margin-bottom:16px">
    <div class="crm-kpi" style="background:linear-gradient(135deg,#6366f1,#818cf8)">
      <i class="fa-solid fa-sack-dollar crm-kpi-icon"></i>
      <div class="crm-kpi-label">Ventas del Mes</div>
      <div class="crm-kpi-value">$<?php echo number_format($_crm_totalEntregado); ?></div>
      <div class="crm-kpi-sub">
        <i class="fa-solid fa-receipt"></i>
        <?php echo $_crm_numEntregadas; ?> órdenes entregadas
      </div>
      <div class="crm-kpi-bar"><span style="width:100%"></span></div>
    </div>
  </div>

  <!-- Pendientes -->
  <div class="col-lg-3 col-sm-6 col-xs-12" style="margin-bottom:16px">
    <div class="crm-kpi" style="background:linear-gradient(135deg,#f59e0b,#fbbf24)">
      <i class="fa-solid fa-hourglass-half crm-kpi-icon"></i>
      <div class="crm-kpi-label">Por Autorizar</div>
      <div class="crm-kpi-value"><?php echo $_crm_numAUT; ?></div>
      <div class="crm-kpi-sub">
        <i class="fa-solid fa-phone"></i>
        Requieren seguimiento
      </div>
      <div class="crm-kpi-bar"><span style="width:<?php echo min($_crm_numAUT * 10, 100); ?>%"></span></div>
    </div>
  </div>

  <!-- Prospectos -->
  <div class="col-lg-3 col-sm-6 col-xs-12" style="margin-bottom:16px">
    <div class="crm-kpi" style="background:linear-gradient(135deg,#06b6d4,#22d3ee)">
      <i class="fa-solid fa-user-plus crm-kpi-icon"></i>
      <div class="crm-kpi-label">Prospectos Nuevos</div>
      <div class="crm-kpi-value"><?php echo $_crm_numClientes; ?></div>
      <div class="crm-kpi-sub">
        <i class="fa-solid fa-calendar-day"></i>
        Captados este mes
      </div>
      <div class="crm-kpi-bar"><span style="width:<?php echo min($_crm_numClientes * 8, 100); ?>%"></span></div>
    </div>
  </div>

  <!-- Conversión -->
  <div class="col-lg-3 col-sm-6 col-xs-12" style="margin-bottom:16px">
    <div class="crm-kpi" style="background:linear-gradient(135deg,<?php echo $_crm_convGrad; ?>)">
      <i class="fa-solid fa-bullseye crm-kpi-icon"></i>
      <div class="crm-kpi-label">Tasa de Conversión</div>
      <div class="crm-kpi-value"><?php echo $_crm_tasaConversion; ?>%</div>
      <div class="crm-kpi-sub">
        <i class="fa-solid fa-arrow-trend-up"></i>
        <?php echo $_crm_numEntregadas; ?> de <?php echo $_crm_numEntradas; ?> ingresadas
      </div>
      <div class="crm-kpi-bar"><span style="width:<?php echo $_crm_tasaConversion; ?>%"></span></div>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════
     ATAJO: MIS COMISIONES DEL MES (asesor)
══════════════════════════════════════════ -->
<?php
require_once __DIR__ . "/../../../config/comisiones.helper.php";

$_crm_comRes = array("confirmado" => 0.0, "ordenes" => 0, "revision" => 0);
if ($_crm_idAsesor > 0) {
    try {
        $_crm_comFiltro = function($o) use ($_crm_idAsesor) {
            return intval(isset($o["id_Asesor"]) ? $o["id_Asesor"] : 0) == $_crm_idAsesor;
        };
        $_crm_comQ1 = array_values(array_filter((array) controladorOrdenes::ctrMostrarComisionesPrimera(), $_crm_comFiltro));
        $_crm_comQ2 = array_values(array_filter(_comFiltrarSegundaQuincena(controladorOrdenes::ctrMostrarComisionesSegunda()), $_crm_comFiltro));
        $_crm_comRes = _comResumenMes($_crm_comQ1, $_crm_comQ2, "asesor", null);
    } catch (Exception $e) {}
}
?>
<div class="row">
  <div class="col-xs-12" style="margin-bottom:16px">
    <a href="index.php?ruta=comisiones" style="text-decoration:none;display:block">
      <div class="crm-kpi" style="background:linear-gradient(135deg,#059669,#10b981);cursor:pointer">
        <i class="fa-solid fa-hand-holding-dollar crm-kpi-icon"></i>
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
          <div>
            <div class="crm-kpi-label">Mis Comisiones del Mes</div>
            <div class="crm-kpi-value">$<?php echo number_format($_crm_comRes["confirmado"], 2); ?></div>
            <div class="crm-kpi-sub">
              <i class="fa-solid fa-receipt"></i>
              <?php echo $_crm_comRes["ordenes"]; ?> órdenes entregadas
              &nbsp;·&nbsp; 4% por orden &nbsp;·&nbsp; Aproximado, sujeto a cambios
            </div>
          </div>
          <div style="background:rgba(255,255,255,.16);border:1px solid rgba(255,255,255,.35);border-radius:10px;padding:10px 20px;color:#fff;font-weight:700;font-size:13px;white-space:nowrap">
            Ver detalle <i class="fa-solid fa-arrow-right"></i>
          </div>
        </div>
      </div>
    </a>
  </div>
</div>
