<?php
/*  ═══════════════════════════════════════════════════
    CRM — Acciones Rápidas + Metas del vendedor
    ═══════════════════════════════════════════════════ */

$_ar_idAsesor = isset($_crm_idAsesor) ? $_crm_idAsesor : 0;

$_ar_metas = ControladorMetas::ctrMostrarMetas("id_perfil", $_ar_idAsesor);
if (!is_array($_ar_metas)) $_ar_metas = array();

$_ar_metasPendientes = 0;
$_ar_metasCompletas = 0;
foreach ($_ar_metas as $m) {
    $est = isset($m["estado"]) ? $m["estado"] : "";
    if (stripos($est, "Completada") !== false) $_ar_metasCompletas++;
    else $_ar_metasPendientes++;
}
$_ar_metasTotal = count($_ar_metas);
$_ar_pctMetas = $_ar_metasTotal > 0 ? round(($_ar_metasCompletas / $_ar_metasTotal) * 100) : 0;

// Circunferencia SVG para ring (radio=23, C=2πr≈144.5)
$_ar_circum = 144.5;
$_ar_offset = $_ar_circum - ($_ar_circum * $_ar_pctMetas / 100);
?>

<div class="crm-card" style="margin-bottom:20px">
  <div class="crm-card-head">
    <h4 class="crm-card-title"><i class="fa-solid fa-rocket"></i> Acciones Rápidas</h4>
  </div>
  <div class="crm-card-body" style="padding:16px 18px">

    <!-- Quick actions grid -->
    <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:20px">
      <a href="index.php?ruta=ordenes" class="crm-quick">
        <div class="crm-quick-icon" style="background:linear-gradient(135deg,#6366f1,#818cf8)">
          <i class="fa-solid fa-plus"></i>
        </div>
        <div class="crm-quick-text">Nueva Orden<small>Crear orden de servicio</small></div>
      </a>
      <a href="index.php?ruta=cotizacion" class="crm-quick">
        <div class="crm-quick-icon" style="background:linear-gradient(135deg,#8b5cf6,#a78bfa)">
          <i class="fa-solid fa-file-invoice-dollar"></i>
        </div>
        <div class="crm-quick-text">Cotizar<small>Generar cotización rápida</small></div>
      </a>
      <a href="index.php?ruta=clientes" class="crm-quick">
        <div class="crm-quick-icon" style="background:linear-gradient(135deg,#06b6d4,#22d3ee)">
          <i class="fa-solid fa-user-plus"></i>
        </div>
        <div class="crm-quick-text">Nuevo Cliente<small>Agregar a tu cartera</small></div>
      </a>
      <a href="index.php?ruta=AgregarPedido" class="crm-quick">
        <div class="crm-quick-icon" style="background:linear-gradient(135deg,#f59e0b,#fbbf24)">
          <i class="fa-solid fa-box"></i>
        </div>
        <div class="crm-quick-text">Pedido<small>Registrar pedido nuevo</small></div>
      </a>
    </div>

    <!-- Metas progress ring -->
    <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:var(--crm-radius-sm);padding:16px;display:flex;align-items:center;gap:16px">
      <?php if ($_ar_metasTotal > 0): ?>
        <div class="crm-ring">
          <svg viewBox="0 0 54 54">
            <circle class="crm-ring-track" cx="27" cy="27" r="23"/>
            <circle class="crm-ring-fill" cx="27" cy="27" r="23"
                    stroke="<?php echo $_ar_pctMetas >= 70 ? '#22c55e' : ($_ar_pctMetas >= 40 ? '#f59e0b' : '#6366f1'); ?>"
                    stroke-dasharray="<?php echo $_ar_circum; ?>"
                    stroke-dashoffset="<?php echo $_ar_offset; ?>"/>
          </svg>
          <div class="crm-ring-label"><?php echo $_ar_pctMetas; ?>%</div>
        </div>
        <div style="flex:1">
          <div style="font-size:13px;font-weight:700;color:var(--crm-text);margin-bottom:2px">Mis Metas</div>
          <div style="font-size:12px;color:var(--crm-muted);margin-bottom:6px">
            <?php echo $_ar_metasCompletas; ?> de <?php echo $_ar_metasTotal; ?> completadas
          </div>
          <a href="index.php?ruta=metas" style="font-size:11px;color:var(--crm-accent);font-weight:600;text-decoration:none">
            Ver detalle <i class="fa-solid fa-arrow-right" style="font-size:9px"></i>
          </a>
        </div>
      <?php else: ?>
        <div style="text-align:center;width:100%;padding:4px 0">
          <i class="fa-solid fa-flag" style="color:#cbd5e1;font-size:18px;margin-bottom:6px;display:block"></i>
          <div style="font-size:12px;color:var(--crm-muted);margin-bottom:4px">Sin metas asignadas</div>
          <a href="index.php?ruta=metas" style="font-size:11px;color:var(--crm-accent);font-weight:600;text-decoration:none">
            Crear meta <i class="fa-solid fa-plus" style="font-size:9px"></i>
          </a>
        </div>
      <?php endif; ?>
    </div>

  </div>
</div>
