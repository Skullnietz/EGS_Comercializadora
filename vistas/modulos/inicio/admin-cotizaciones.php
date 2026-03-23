<?php
/*  ═══════════════════════════════════════════════════
    ADMIN — Widget de Cotizaciones Activas
    ═══════════════════════════════════════════════════ */

$_admCot_lista = array();
$_admCot_vigentes = array();
$_admCot_expiradas = array();
$_admCot_montoVigente = 0;
$_admCot_porVencer = 0;

try {
    $_admCot_lista = CotizacionesControlador::ctrMostrarCotizaciones(null, null);
    if (!is_array($_admCot_lista)) $_admCot_lista = array();
} catch (Exception $e) { $_admCot_lista = array(); }

// Precargar vendedores
$_admCot_vendedores = array();
try {
    $allAse = Controladorasesores::ctrMostrarAsesoresEleg(null, null);
    if (is_array($allAse)) {
        foreach ($allAse as $a) {
            if (isset($a["id"])) $_admCot_vendedores[intval($a["id"])] = $a["nombre"];
        }
    }
} catch (Exception $e) {}

foreach ($_admCot_lista as $c) {
    $vigText = isset($c["vigencia"]) ? $c["vigencia"] : "";
    $fecha = isset($c["fecha"]) ? $c["fecha"] : "";
    $dias = null;
    $expirada = false;
    $restantes = null;

    if (preg_match('/(\d+)/', $vigText, $m) && !empty($fecha)) {
        $dias = intval($m[1]);
        $fechaBase = strtotime($fecha);
        if ($fechaBase !== false) {
            $fechaExp = strtotime("+{$dias} days", $fechaBase);
            $expirada = (date('Y-m-d') > date('Y-m-d', $fechaExp));
            if (!$expirada) {
                $restantes = max(0, intval(($fechaExp - strtotime(date('Y-m-d'))) / 86400));
            }
        }
    }

    if ($expirada) {
        $_admCot_expiradas[] = $c;
    } else {
        $_admCot_vigentes[] = $c;
        $_admCot_montoVigente += floatval($c["total"]);
        if ($restantes !== null && $restantes <= 5) $_admCot_porVencer++;
    }
}

$_admCot_totalVigentes = count($_admCot_vigentes);
$_admCot_totalExpiradas = count($_admCot_expiradas);
?>

<div class="crm-card" style="margin-bottom:20px">
  <div class="crm-card-head">
    <h4 class="crm-card-title"><i class="fa-solid fa-file-invoice-dollar"></i> Cotizaciones Activas</h4>
    <div style="display:flex;gap:4px">
      <span class="crm-badge" style="background:#f0fdf4;color:#16a34a"><?php echo $_admCot_totalVigentes; ?> vigentes</span>
      <?php if ($_admCot_porVencer > 0): ?>
        <span class="crm-badge" style="background:#fffbeb;color:#f59e0b"><?php echo $_admCot_porVencer; ?> por vencer</span>
      <?php endif; ?>
      <?php if ($_admCot_totalExpiradas > 0): ?>
        <span class="crm-badge" style="background:#fef2f2;color:#dc2626"><?php echo $_admCot_totalExpiradas; ?> expiradas</span>
      <?php endif; ?>
    </div>
  </div>

  <?php if (empty($_admCot_vigentes)): ?>
    <div class="crm-card-body">
      <div class="crm-empty">
        <i class="fa-solid fa-file-circle-plus" style="font-size:28px"></i>
        <strong>Sin cotizaciones activas</strong>
        <a href="index.php?ruta=cotizacion" style="font-size:12px;color:#6366f1;font-weight:600;text-decoration:none">
          <i class="fa-solid fa-plus"></i> Crear cotización
        </a>
      </div>
    </div>
  <?php else: ?>

    <!-- Summary strip -->
    <div style="display:flex;border-bottom:1px solid #f1f5f9">
      <div style="flex:1;padding:16px 20px;text-align:center;border-right:1px solid #f1f5f9">
        <div style="font-size:22px;font-weight:800;color:#16a34a;letter-spacing:-.02em">
          $<?php echo number_format($_admCot_montoVigente, 0); ?>
        </div>
        <div style="font-size:11px;color:#94a3b8;font-weight:500">Monto vigente</div>
      </div>
      <div style="flex:1;padding:16px 20px;text-align:center;border-right:1px solid #f1f5f9">
        <div style="font-size:22px;font-weight:800;color:#0f172a;letter-spacing:-.02em">
          <?php echo $_admCot_totalVigentes; ?>
        </div>
        <div style="font-size:11px;color:#94a3b8;font-weight:500">Activas</div>
      </div>
      <div style="flex:1;padding:16px 20px;text-align:center">
        <div style="font-size:22px;font-weight:800;color:#0f172a;letter-spacing:-.02em">
          <?php echo count($_admCot_lista); ?>
        </div>
        <div style="font-size:11px;color:#94a3b8;font-weight:500">Total historial</div>
      </div>
    </div>

    <!-- Lista de cotizaciones vigentes -->
    <div class="crm-card-body-flush" style="max-height:360px;overflow-y:auto">
      <?php foreach (array_slice($_admCot_vigentes, 0, 10) as $cot):
        $totalCot = floatval(isset($cot["total"]) ? $cot["total"] : 0);
        $nombreCli = isset($cot["nombre_cliente"]) ? $cot["nombre_cliente"] : "Sin nombre";
        $asunto = isset($cot["asunto"]) ? $cot["asunto"] : "";
        $vendedorId = intval($cot["id_vendedor"]);
        $vendedorNom = isset($_admCot_vendedores[$vendedorId]) ? $_admCot_vendedores[$vendedorId] : "";

        // Calcular días restantes
        $diasRestTxt = "";
        $diasRestColor = "#64748b";
        $vigText = isset($cot["vigencia"]) ? $cot["vigencia"] : "";
        $fecha = isset($cot["fecha"]) ? $cot["fecha"] : "";
        if (preg_match('/(\d+)/', $vigText, $m) && !empty($fecha)) {
            $d = intval($m[1]);
            $fb = strtotime($fecha);
            if ($fb !== false) {
                $rest = max(0, intval((strtotime("+{$d} days", $fb) - strtotime(date('Y-m-d'))) / 86400));
                if ($rest <= 3) { $diasRestTxt = $rest . "d"; $diasRestColor = "#dc2626"; }
                elseif ($rest <= 7) { $diasRestTxt = $rest . "d"; $diasRestColor = "#f59e0b"; }
                else { $diasRestTxt = $rest . "d"; $diasRestColor = "#16a34a"; }
            }
        }
      ?>
        <a href="index.php?ruta=imprimir-cotizacion&id=<?php echo $cot['id']; ?>" target="_blank"
           style="display:flex;align-items:center;gap:12px;padding:10px 16px;border-bottom:1px solid #f8fafc;text-decoration:none;transition:background .12s"
           onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
          <div style="width:36px;height:36px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:13px;background:#f0fdf4;color:#22c55e">
            <i class="fa-solid fa-file-circle-check"></i>
          </div>
          <div style="flex:1;min-width:0">
            <div style="font-size:13px;font-weight:600;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
              <?php echo htmlspecialchars($nombreCli); ?>
            </div>
            <div style="font-size:11px;color:#94a3b8">
              <?php echo !empty($asunto) ? htmlspecialchars(mb_substr($asunto, 0, 30)) : 'Sin asunto'; ?>
              <?php if (!empty($vendedorNom)): ?>
                &middot; <span style="color:#6366f1"><?php echo htmlspecialchars($vendedorNom); ?></span>
              <?php endif; ?>
            </div>
          </div>
          <?php if (!empty($diasRestTxt)): ?>
          <div style="flex-shrink:0;font-size:11px;font-weight:700;color:<?php echo $diasRestColor; ?>;background:<?php echo $diasRestColor === '#dc2626' ? '#fef2f2' : ($diasRestColor === '#f59e0b' ? '#fffbeb' : '#f0fdf4'); ?>;padding:3px 8px;border-radius:8px">
            <?php echo $diasRestTxt; ?>
          </div>
          <?php endif; ?>
          <div style="font-weight:700;font-size:13px;color:#0f172a;flex-shrink:0">
            $<?php echo number_format($totalCot, 0); ?>
          </div>
        </a>
      <?php endforeach; ?>
    </div>

    <!-- Footer link -->
    <div style="text-align:center;padding:12px;border-top:1px solid #f1f5f9">
      <a href="index.php?ruta=historial-cotizaciones" style="color:#6366f1;font-size:12px;font-weight:600;text-decoration:none">
        Ver todas las cotizaciones <i class="fa-solid fa-arrow-right" style="font-size:10px"></i>
      </a>
    </div>

  <?php endif; ?>
</div>
