<?php
/*  ═══════════════════════════════════════════════════
    CRM — Cotizaciones recientes del vendedor
    ═══════════════════════════════════════════════════ */

$_cot_lista = isset($_crm_cotizaciones) ? $_crm_cotizaciones : array();
$_cot_vigentes = array();
$_cot_vencidas = array();
$hoy = date("Y-m-d");

foreach ($_cot_lista as $cot) {
    $vig = isset($cot["vigencia"]) ? $cot["vigencia"] : "";
    if (!empty($vig) && $vig < $hoy) $_cot_vencidas[] = $cot;
    else $_cot_vigentes[] = $cot;
}

$_cot_totalMonto = 0;
foreach ($_cot_lista as $c) {
    $_cot_totalMonto += floatval(isset($c["total"]) ? $c["total"] : 0);
}
?>

<div class="crm-card" style="margin-bottom:20px">
  <div class="crm-card-head">
    <h4 class="crm-card-title"><i class="fa-solid fa-file-invoice-dollar"></i> Cotizaciones</h4>
    <div style="display:flex;gap:4px">
      <span class="crm-badge" style="background:#f0fdf4;color:#16a34a"><?php echo count($_cot_vigentes); ?> vigentes</span>
      <?php if (count($_cot_vencidas) > 0): ?>
        <span class="crm-badge" style="background:#fef2f2;color:#dc2626"><?php echo count($_cot_vencidas); ?> vencidas</span>
      <?php endif; ?>
    </div>
  </div>

  <?php if (empty($_cot_lista)): ?>
    <div class="crm-card-body">
      <div class="crm-empty">
        <i class="fa-solid fa-file-circle-plus"></i>
        <strong>Sin cotizaciones</strong>
        <a href="index.php?ruta=cotizacion" style="font-size:12px;color:var(--crm-accent);font-weight:600;text-decoration:none">
          <i class="fa-solid fa-plus"></i> Crear cotización
        </a>
      </div>
    </div>
  <?php else: ?>

    <!-- Summary strip -->
    <div style="display:flex;border-bottom:1px solid #f1f5f9">
      <div style="flex:1;padding:16px 20px;text-align:center;border-right:1px solid #f1f5f9">
        <div style="font-size:22px;font-weight:800;color:var(--crm-text);letter-spacing:-.02em">
          $<?php echo number_format($_cot_totalMonto, 0); ?>
        </div>
        <div style="font-size:11px;color:var(--crm-muted);font-weight:500">Total cotizado</div>
      </div>
      <div style="flex:1;padding:16px 20px;text-align:center">
        <div style="font-size:22px;font-weight:800;color:var(--crm-text);letter-spacing:-.02em">
          <?php echo count($_cot_lista); ?>
        </div>
        <div style="font-size:11px;color:var(--crm-muted);font-weight:500">Cotizaciones</div>
      </div>
    </div>

    <!-- List -->
    <div class="crm-card-body-flush" style="max-height:320px;overflow-y:auto">
      <?php foreach (array_slice($_cot_lista, 0, 10) as $cot):
        $vigencia = isset($cot["vigencia"]) ? $cot["vigencia"] : "";
        $vencida = (!empty($vigencia) && $vigencia < $hoy);
        $totalCot = floatval(isset($cot["total"]) ? $cot["total"] : 0);
        $nombreCli = isset($cot["nombre_cliente"]) ? $cot["nombre_cliente"] : "Sin nombre";
        $asunto = isset($cot["asunto"]) ? $cot["asunto"] : "";
      ?>
        <div class="crm-cot-row">
          <div style="width:34px;height:34px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:13px;
            <?php echo $vencida ? 'background:#fef2f2;color:#ef4444' : 'background:#f0fdf4;color:#22c55e'; ?>">
            <i class="fa-solid <?php echo $vencida ? 'fa-clock' : 'fa-file-circle-check'; ?>"></i>
          </div>
          <div style="flex:1;min-width:0">
            <div style="font-size:13px;font-weight:600;color:var(--crm-text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
              <?php echo htmlspecialchars($nombreCli); ?>
            </div>
            <div style="font-size:11px;color:var(--crm-muted)">
              <?php echo !empty($asunto) ? htmlspecialchars(mb_substr($asunto, 0, 28)) : 'Sin asunto'; ?>
              <?php if (!empty($vigencia)): ?>
                &middot; <span style="color:<?php echo $vencida ? '#ef4444' : '#64748b'; ?>">
                  <?php echo $vencida ? 'Venció' : 'Vence'; ?> <?php echo date("d/m", strtotime($vigencia)); ?>
                </span>
              <?php endif; ?>
            </div>
          </div>
          <div style="font-weight:700;font-size:13px;color:var(--crm-text);flex-shrink:0">
            $<?php echo number_format($totalCot, 0); ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <?php if (count($_cot_lista) > 10): ?>
      <div style="text-align:center;padding:12px;border-top:1px solid #f1f5f9">
        <a href="index.php?ruta=historial-cotizaciones" style="color:var(--crm-accent);font-size:12px;font-weight:600;text-decoration:none">
          Ver todas <i class="fa-solid fa-arrow-right" style="font-size:10px"></i>
        </a>
      </div>
    <?php endif; ?>

  <?php endif; ?>
</div>
