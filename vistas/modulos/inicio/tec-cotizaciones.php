<?php
/*  ═══════════════════════════════════════════════════
    TÉCNICO — Widget de Cotizaciones Activas
    Muestra cotizaciones vigentes de la empresa
    ═══════════════════════════════════════════════════ */

$_tcot_lista = array();
$_tcot_vigentes = array();
$_tcot_montoVigente = 0;
$_tcot_porVencer = 0;

try {
    $_tcot_lista = CotizacionesControlador::ctrMostrarCotizaciones(null, null);
    if (!is_array($_tcot_lista)) $_tcot_lista = array();
} catch (Exception $e) { $_tcot_lista = array(); }

foreach ($_tcot_lista as $c) {
    $vigText = isset($c["vigencia"]) ? $c["vigencia"] : "";
    $fecha = isset($c["fecha"]) ? $c["fecha"] : "";
    $expirada = false;
    $restantes = null;

    if (preg_match('/(\d+)/', $vigText, $m) && !empty($fecha)) {
        $d = intval($m[1]);
        $fb = strtotime($fecha);
        if ($fb !== false) {
            $fExp = strtotime("+{$d} days", $fb);
            $expirada = (date('Y-m-d') > date('Y-m-d', $fExp));
            if (!$expirada) {
                $restantes = max(0, intval(($fExp - strtotime(date('Y-m-d'))) / 86400));
            }
        }
    }

    if (!$expirada) {
        $_tcot_vigentes[] = array_merge($c, array('_restantes' => $restantes));
        $_tcot_montoVigente += floatval($c["total"]);
        if ($restantes !== null && $restantes <= 5) $_tcot_porVencer++;
    }
}

$_tcot_totalVigentes = count($_tcot_vigentes);
?>

<div class="crm-card" style="margin-bottom:20px">
  <div class="crm-card-head">
    <h4 class="crm-card-title"><i class="fa-solid fa-file-invoice-dollar"></i> Cotizaciones Activas</h4>
    <div style="display:flex;gap:4px">
      <span class="crm-badge" style="background:#f0fdf4;color:#16a34a"><?php echo $_tcot_totalVigentes; ?> vigentes</span>
      <?php if ($_tcot_porVencer > 0): ?>
        <span class="crm-badge" style="background:#fffbeb;color:#f59e0b"><?php echo $_tcot_porVencer; ?> por vencer</span>
      <?php endif; ?>
    </div>
  </div>

  <?php if (empty($_tcot_vigentes)): ?>
    <div class="crm-card-body">
      <div class="crm-empty">
        <i class="fa-solid fa-file-circle-check" style="font-size:24px;color:#22c55e;opacity:.5"></i>
        <strong>Sin cotizaciones activas</strong>
      </div>
    </div>
  <?php else: ?>

    <!-- Summary strip -->
    <div style="display:flex;border-bottom:1px solid #f1f5f9">
      <div style="flex:1;padding:14px 16px;text-align:center;border-right:1px solid #f1f5f9">
        <div style="font-size:20px;font-weight:800;color:#16a34a">$<?php echo number_format($_tcot_montoVigente, 0); ?></div>
        <div style="font-size:10px;color:#94a3b8;font-weight:500">Monto vigente</div>
      </div>
      <div style="flex:1;padding:14px 16px;text-align:center">
        <div style="font-size:20px;font-weight:800;color:#0f172a"><?php echo $_tcot_totalVigentes; ?></div>
        <div style="font-size:10px;color:#94a3b8;font-weight:500">Activas</div>
      </div>
    </div>

    <!-- Lista -->
    <div class="crm-card-body-flush" style="max-height:280px;overflow-y:auto">
      <?php foreach (array_slice($_tcot_vigentes, 0, 8) as $cot):
        $totalCot = floatval(isset($cot["total"]) ? $cot["total"] : 0);
        $nombreCli = isset($cot["nombre_cliente"]) ? $cot["nombre_cliente"] : "Sin nombre";
        $asunto = isset($cot["asunto"]) ? $cot["asunto"] : "";
        $rest = $cot['_restantes'];

        $restTxt = ""; $restColor = "#64748b"; $restBg = "#f1f5f9";
        if ($rest !== null) {
            $restTxt = $rest . "d";
            if ($rest <= 3) { $restColor = "#dc2626"; $restBg = "#fef2f2"; }
            elseif ($rest <= 7) { $restColor = "#f59e0b"; $restBg = "#fffbeb"; }
            else { $restColor = "#16a34a"; $restBg = "#f0fdf4"; }
        }
      ?>
        <div style="display:flex;align-items:center;gap:10px;padding:10px 16px;border-bottom:1px solid #f8fafc">
          <div style="width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:12px;background:#f0fdf4;color:#22c55e">
            <i class="fa-solid fa-file-circle-check"></i>
          </div>
          <div style="flex:1;min-width:0">
            <div style="font-size:12px;font-weight:600;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
              <?php echo htmlspecialchars($nombreCli); ?>
            </div>
            <div style="font-size:10px;color:#94a3b8">
              <?php echo !empty($asunto) ? htmlspecialchars(mb_substr($asunto, 0, 25)) : 'Sin asunto'; ?>
            </div>
          </div>
          <?php if (!empty($restTxt)): ?>
          <div style="flex-shrink:0;font-size:10px;font-weight:700;color:<?php echo $restColor; ?>;background:<?php echo $restBg; ?>;padding:2px 7px;border-radius:6px">
            <?php echo $restTxt; ?>
          </div>
          <?php endif; ?>
          <div style="font-weight:700;font-size:12px;color:#0f172a;flex-shrink:0">$<?php echo number_format($totalCot, 0); ?></div>
        </div>
      <?php endforeach; ?>
    </div>

    <?php if ($_tcot_totalVigentes > 8): ?>
    <div style="text-align:center;padding:10px;border-top:1px solid #f1f5f9">
      <a href="index.php?ruta=historial-cotizaciones" style="color:#6366f1;font-size:11px;font-weight:600;text-decoration:none">
        Ver todas <i class="fa-solid fa-arrow-right" style="font-size:9px"></i>
      </a>
    </div>
    <?php endif; ?>

  <?php endif; ?>
</div>
