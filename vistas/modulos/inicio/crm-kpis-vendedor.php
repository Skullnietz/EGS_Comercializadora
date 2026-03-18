<?php
/*  ═══════════════════════════════════════════════════
    CRM — KPIs principales del vendedor
    ═══════════════════════════════════════════════════ */

// ── Identificar asesor en sesión ──
$_crm_idAsesor = 0;
try {
    $_crm_asesor = Controladorasesores::ctrMostrarAsesoresEleg("correo", $_SESSION["email"]);
    if (is_array($_crm_asesor) && isset($_crm_asesor["id"])) {
        $_crm_idAsesor = intval($_crm_asesor["id"]);
    }
} catch (Exception $e) { $_crm_idAsesor = 0; }

// ── Ventas del mes (entregadas) ──
$_crm_totalEntregado = 0;
$_crm_numEntregadas = 0;
try {
    $_crm_ordEntregadas = controladorOrdenes::ctrMostrarOrdenesSumaAsesor($_crm_idAsesor);
    if (is_array($_crm_ordEntregadas)) {
        foreach ($_crm_ordEntregadas as $o) {
            $_crm_totalEntregado += floatval(isset($o["total"]) ? $o["total"] : 0);
        }
    }
} catch (Exception $e) {}

try {
    $_crm_cntEntregadas = controladorOrdenes::ctrListarOrdenesAsesor($_crm_idAsesor);
    if (is_array($_crm_cntEntregadas)) {
        foreach ($_crm_cntEntregadas as $v) {
            $_crm_numEntregadas = intval($v[0]);
        }
    }
} catch (Exception $e) {}

// ── Órdenes pendientes de autorización (método NO static) ──
$_crm_ordAUT = array();
$_crm_numAUT = 0;
try {
    $_crm_ctrlOrd = new controladorOrdenes();
    $_crm_ordAUT = $_crm_ctrlOrd->ctrlMostrarOrdenesPorEstadoEmpresayTecnico(
        "Pendiente de autorización (AUT",
        "id_empresa", $_SESSION["empresa"],
        "id_Asesor", $_crm_idAsesor
    );
    if (is_array($_crm_ordAUT)) {
        $_crm_numAUT = count($_crm_ordAUT);
    } else {
        $_crm_ordAUT = array();
    }
} catch (Exception $e) { $_crm_ordAUT = array(); }

// ── Clientes nuevos del mes ──
$_crm_numClientes = 0;
try {
    $_crm_clientes = ControladorUsuarios::ctrMostrarTotalUsuariosMesAsesor("id", $_crm_idAsesor);
    $_crm_numClientes = is_array($_crm_clientes) ? count($_crm_clientes) : 0;
} catch (Exception $e) {}

// ── Entradas del mes ──
$_crm_numEntradas = 0;
try {
    $_crm_entradas = controladorOrdenes::ctrMostrarOrdenesEntradaAsesor($_crm_idAsesor);
    $_crm_numEntradas = is_array($_crm_entradas) ? count($_crm_entradas) : 0;
} catch (Exception $e) {}

// ── Cotizaciones del vendedor ──
$_crm_cotizaciones = array();
try {
    $_crm_cotizaciones = CotizacionesControlador::ctrMostrarCotizaciones("id_vendedor", $_crm_idAsesor);
    if (!is_array($_crm_cotizaciones)) $_crm_cotizaciones = array();
} catch (Exception $e) { $_crm_cotizaciones = array(); }
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
