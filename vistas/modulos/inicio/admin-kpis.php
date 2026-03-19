<?php
/*  ═══════════════════════════════════════════════════
    DASHBOARD ADMIN — KPIs principales (estilo CRM)
    ═══════════════════════════════════════════════════ */

// ── Ventas del mes (órdenes entregadas) ──
$_adm_ordEntregadas = controladorOrdenes::ctrMostrarOrdenesSuma();
$_adm_totalVentas = 0;
$_adm_numEntregadas = 0;
if (is_array($_adm_ordEntregadas)) {
    $_adm_numEntregadas = count($_adm_ordEntregadas);
    foreach ($_adm_ordEntregadas as $o) {
        $_adm_totalVentas += floatval(isset($o["total"]) ? $o["total"] : 0);
    }
}

// ── Órdenes ingresadas este mes ──
$_adm_entradas = ControladorOrdenes::ctrMostrarOrdenesEntrada();
$_adm_numEntradas = is_array($_adm_entradas) ? count($_adm_entradas) : 0;

// ── Ingresadas hoy ──
$_adm_hoy = date('Y-m-d');
$_adm_ingresadasHoy = 0;
$_adm_entregadasHoy = 0;
if (is_array($_adm_entradas)) {
    foreach ($_adm_entradas as $e) {
        if (substr($e['fecha_ingreso'], 0, 10) === $_adm_hoy) $_adm_ingresadasHoy++;
    }
}
if (is_array($_adm_ordEntregadas)) {
    foreach ($_adm_ordEntregadas as $e) {
        if (substr($e['fecha_Salida'], 0, 10) === $_adm_hoy) $_adm_entregadasHoy++;
    }
}

// ── Prospectos nuevos del mes ──
$_adm_usuarios = ControladorUsuarios::ctrMostrarTotalUsuariosMes("id");
$_adm_numProspectos = is_array($_adm_usuarios) ? count($_adm_usuarios) : 0;

// ── Pendientes ──
$_adm_pendientes = max(0, $_adm_numEntradas - $_adm_numEntregadas);

// ── Eficiencia: entregadas / ingresadas ──
$_adm_eficiencia = $_adm_numEntradas > 0
    ? round(($_adm_numEntregadas / $_adm_numEntradas) * 100, 1) : 0;

if ($_adm_eficiencia >= 70)      { $_adm_efGrad = '#059669,#10b981'; }
elseif ($_adm_eficiencia >= 40)  { $_adm_efGrad = '#d97706,#f59e0b'; }
else                             { $_adm_efGrad = '#dc2626,#ef4444'; }

// ── Ventas (punto de venta) ──
$_adm_ventasPV = ControladorVentas::ctrMostrarTotalVentasMes("id");
$_adm_totalVentasPV = is_array($_adm_ventasPV) ? floatval($_adm_ventasPV[0]) : 0;
?>

<div class="row">
  <!-- $ Total Ordenes -->
  <div class="col-lg-3 col-sm-6 col-xs-12" style="margin-bottom:16px">
    <div class="crm-kpi" style="background:linear-gradient(135deg,#6366f1,#818cf8)">
      <i class="fa-solid fa-sack-dollar crm-kpi-icon"></i>
      <div class="crm-kpi-label">Total Ordenes / Mes</div>
      <div class="crm-kpi-value">$<?php echo number_format($_adm_totalVentas); ?></div>
      <div class="crm-kpi-sub">
        <i class="fa-solid fa-receipt"></i>
        <?php echo $_adm_numEntregadas; ?> ordenes entregadas
      </div>
      <div class="crm-kpi-bar"><span style="width:100%"></span></div>
    </div>
  </div>

  <!-- Ingresadas -->
  <div class="col-lg-3 col-sm-6 col-xs-12" style="margin-bottom:16px">
    <div class="crm-kpi" style="background:linear-gradient(135deg,#3b82f6,#60a5fa)">
      <i class="fa-solid fa-file-circle-plus crm-kpi-icon"></i>
      <div class="crm-kpi-label">Ingresadas / Mes</div>
      <div class="crm-kpi-value"><?php echo $_adm_numEntradas; ?></div>
      <div class="crm-kpi-sub">
        <i class="fa-solid fa-calendar-day"></i>
        <?php echo $_adm_ingresadasHoy; ?> hoy &mdash; <?php echo $_adm_pendientes; ?> pendientes
      </div>
      <div class="crm-kpi-bar"><span style="width:<?php echo min($_adm_numEntradas * 2, 100); ?>%"></span></div>
    </div>
  </div>

  <!-- Prospectos -->
  <div class="col-lg-3 col-sm-6 col-xs-12" style="margin-bottom:16px">
    <div class="crm-kpi" style="background:linear-gradient(135deg,#06b6d4,#22d3ee)">
      <i class="fa-solid fa-user-plus crm-kpi-icon"></i>
      <div class="crm-kpi-label">Prospectos / Mes</div>
      <div class="crm-kpi-value"><?php echo $_adm_numProspectos; ?></div>
      <div class="crm-kpi-sub">
        <i class="fa-solid fa-users"></i>
        Nuevos clientes captados
      </div>
      <div class="crm-kpi-bar"><span style="width:<?php echo min($_adm_numProspectos * 5, 100); ?>%"></span></div>
    </div>
  </div>

  <!-- Eficiencia -->
  <div class="col-lg-3 col-sm-6 col-xs-12" style="margin-bottom:16px">
    <div class="crm-kpi" style="background:linear-gradient(135deg,<?php echo $_adm_efGrad; ?>)">
      <i class="fa-solid fa-gauge-high crm-kpi-icon"></i>
      <div class="crm-kpi-label">Eficiencia del Mes</div>
      <div class="crm-kpi-value"><?php echo $_adm_eficiencia; ?>%</div>
      <div class="crm-kpi-sub">
        <i class="fa-solid fa-trophy"></i>
        <?php echo $_adm_numEntregadas; ?> entregadas de <?php echo $_adm_numEntradas; ?> ingresadas
      </div>
      <div class="crm-kpi-bar"><span style="width:<?php echo $_adm_eficiencia; ?>%"></span></div>
    </div>
  </div>
</div>
