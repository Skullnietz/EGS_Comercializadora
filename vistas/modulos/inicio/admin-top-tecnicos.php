<?php
/*  ═══════════════════════════════════════════════════
    DASHBOARD ADMIN — Rendimiento de Técnicos del Mes
    Compara carga de trabajo (REV + OK) vs resultados
    (TER + ENT) para medir eficiencia real.
    ═══════════════════════════════════════════════════ */

$_rt_ctrl = new controladorOrdenes();
$_rt_item = "id_empresa";
$_rt_val  = $_SESSION["empresa"];
$_rt_field = "id_tecnico";

// Cargar lista de técnicos de la empresa
$_rt_tecLista = ControladorTecnicos::ctrMostrarTecnicosDeEmpresas("id_empresa", $_SESSION["empresa"]);
if (!is_array($_rt_tecLista)) $_rt_tecLista = array();

$_rt_limite = date("Y-m-d", strtotime("-1 month"));
$_rt_limite3m = date("Y-m-d", strtotime("-3 months"));

$_rt_ranking = array();

foreach ($_rt_tecLista as $tec) {
    $tid = intval($tec['id']);
    $nombre = isset($tec['nombre']) ? $tec['nombre'] : 'Técnico #' . $tid;

    $rev = 0; $ok = 0; $ter = 0; $ent = 0;

    // REV - En revisión (filtrar a 3 meses, las más antiguas están congeladas)
    try {
        $r = $_rt_ctrl->ctrlMostrarOrdenesPorEstadoEmpresayTecnico(
            "En revisión (REV)", $_rt_item, $_rt_val, $_rt_field, $tid
        );
        if (is_array($r)) {
            foreach ($r as $o) {
                $fi = isset($o["fecha_ingreso"]) ? substr($o["fecha_ingreso"], 0, 10) : "";
                if ($fi >= $_rt_limite3m) $rev++;
            }
        }
    } catch (Exception $e) {}

    // OK - Aceptadas (filtrar a 3 meses, las más antiguas están congeladas)
    try {
        $r = $_rt_ctrl->ctrlMostrarOrdenesPorEstadoEmpresayTecnico(
            "Aceptado (ok)", $_rt_item, $_rt_val, $_rt_field, $tid
        );
        if (is_array($r)) {
            foreach ($r as $o) {
                $fi = isset($o["fecha_ingreso"]) ? substr($o["fecha_ingreso"], 0, 10) : "";
                if ($fi >= $_rt_limite3m) $ok++;
            }
        }
    } catch (Exception $e) {}

    // TER - Terminadas (trabajo completado)
    try {
        $r = $_rt_ctrl->ctrlMostrarOrdenesPorEstadoEmpresayTecnico(
            "Terminada (ter)", $_rt_item, $_rt_val, $_rt_field, $tid
        );
        if (is_array($r)) {
            // Filtrar solo las del mes
            foreach ($r as $o) {
                $fi = isset($o["fecha_ingreso"]) ? substr($o["fecha_ingreso"], 0, 10) : "";
                if ($fi >= $_rt_limite) $ter++;
            }
        }
    } catch (Exception $e) {}

    // ENT - Entregadas (ciclo cerrado)
    try {
        $r = $_rt_ctrl->ctrlMostrarOrdenesPorEstadoEmpresayTecnico(
            "Entregado (Ent)", $_rt_item, $_rt_val, $_rt_field, $tid
        );
        if (is_array($r)) {
            foreach ($r as $o) {
                $fi = isset($o["fecha_ingreso"]) ? substr($o["fecha_ingreso"], 0, 10) : "";
                if ($fi >= $_rt_limite) $ent++;
            }
        }
    } catch (Exception $e) {}

    $pendientes = $rev + $ok;
    $resueltas = $ter + $ent;
    $total = $pendientes + $resueltas;
    $eficiencia = $total > 0 ? round(($resueltas / $total) * 100, 0) : 0;

    // Solo incluir técnicos con actividad
    if ($total > 0) {
        $_rt_ranking[] = array(
            'nombre'     => $nombre,
            'rev'        => $rev,
            'ok'         => $ok,
            'ter'        => $ter,
            'ent'        => $ent,
            'pendientes' => $pendientes,
            'resueltas'  => $resueltas,
            'total'      => $total,
            'eficiencia' => $eficiencia,
        );
    }
}

// Ordenar por eficiencia (de mejor a peor)
usort($_rt_ranking, function($a, $b) {
    if ($b['eficiencia'] !== $a['eficiencia']) return $b['eficiencia'] - $a['eficiencia'];
    return $b['resueltas'] - $a['resueltas'];
});

$_rt_ranking = array_slice($_rt_ranking, 0, 8);
?>

<div class="crm-card" style="margin-bottom:20px">
  <div class="crm-card-head">
    <h4 class="crm-card-title"><i class="fa-solid fa-ranking-star"></i> Rendimiento Técnicos</h4>
    <a href="index.php?ruta=tecnicos" style="font-size:12px;font-weight:600;color:#6366f1;text-decoration:none">
      Ver todos <i class="fa-solid fa-arrow-right" style="font-size:10px"></i>
    </a>
  </div>
  <div class="crm-card-body-flush" style="max-height:460px;overflow-y:auto">
    <?php if (empty($_rt_ranking)): ?>
      <div class="crm-empty">
        <i class="fa-solid fa-circle-info"></i>
        <strong>Sin actividad este mes</strong>
      </div>
    <?php else: ?>
      <!-- Leyenda -->
      <div style="display:flex;gap:12px;padding:12px 18px;border-bottom:1px solid #f1f5f9;flex-wrap:wrap">
        <span style="font-size:10px;font-weight:600;color:#94a3b8;display:flex;align-items:center;gap:4px">
          <span style="width:8px;height:8px;border-radius:2px;background:#ef4444;display:inline-block"></span> Pendientes (REV+OK)
        </span>
        <span style="font-size:10px;font-weight:600;color:#94a3b8;display:flex;align-items:center;gap:4px">
          <span style="width:8px;height:8px;border-radius:2px;background:#22c55e;display:inline-block"></span> Resueltas (TER+ENT)
        </span>
      </div>

      <?php foreach ($_rt_ranking as $i => $tec):
        $initial = mb_strtoupper(mb_substr($tec['nombre'], 0, 1));

        if ($tec['eficiencia'] >= 70) { $efColor = '#22c55e'; $efBg = '#f0fdf4'; }
        elseif ($tec['eficiencia'] >= 40) { $efColor = '#f59e0b'; $efBg = '#fffbeb'; }
        else { $efColor = '#ef4444'; $efBg = '#fef2f2'; }

        $pctPend = $tec['total'] > 0 ? round($tec['pendientes'] / $tec['total'] * 100) : 0;
        $pctRes = $tec['total'] > 0 ? round($tec['resueltas'] / $tec['total'] * 100) : 0;
      ?>
        <div style="padding:14px 18px;border-bottom:1px solid #f8fafc;transition:background .12s"
             onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
          <div style="display:flex;align-items:center;gap:12px;margin-bottom:8px">
            <!-- Posición -->
            <?php if ($i === 0): ?>
              <div style="width:28px;height:28px;border-radius:50%;background:linear-gradient(135deg,#f59e0b,#fbbf24);display:flex;align-items:center;justify-content:center;font-size:12px;color:#fff;flex-shrink:0">
                <i class="fa-solid fa-crown"></i>
              </div>
            <?php else: ?>
              <div style="width:28px;height:28px;border-radius:50%;background:#f1f5f9;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:800;color:#64748b;flex-shrink:0">
                <?php echo $i + 1; ?>
              </div>
            <?php endif; ?>

            <div style="flex:1;min-width:0">
              <span style="font-size:13px;font-weight:700;color:#0f172a"><?php echo htmlspecialchars($tec['nombre']); ?></span>
            </div>

            <!-- Eficiencia -->
            <div style="display:flex;align-items:center;gap:6px;padding:4px 10px;background:<?php echo $efBg; ?>;border-radius:10px;flex-shrink:0">
              <span style="font-size:15px;font-weight:800;color:<?php echo $efColor; ?>"><?php echo $tec['eficiencia']; ?>%</span>
            </div>
          </div>

          <!-- Barra de comparación -->
          <div style="display:flex;gap:3px;height:8px;border-radius:4px;overflow:hidden;background:#f1f5f9;margin-bottom:6px">
            <?php if ($tec['pendientes'] > 0): ?>
              <div style="width:<?php echo max($pctPend, 4); ?>%;background:#ef4444;border-radius:4px;transition:width .4s"></div>
            <?php endif; ?>
            <?php if ($tec['resueltas'] > 0): ?>
              <div style="width:<?php echo max($pctRes, 4); ?>%;background:#22c55e;border-radius:4px;transition:width .4s"></div>
            <?php endif; ?>
          </div>

          <!-- Detalle numérico -->
          <div style="display:flex;gap:12px;font-size:11px;color:#64748b;flex-wrap:wrap">
            <span title="En revisión"><i class="fa-solid fa-magnifying-glass" style="color:#ef4444;font-size:9px"></i> <?php echo $tec['rev']; ?> REV</span>
            <span title="Aceptadas / En reparación"><i class="fa-solid fa-gears" style="color:#3b82f6;font-size:9px"></i> <?php echo $tec['ok']; ?> OK</span>
            <span style="color:#cbd5e1">|</span>
            <span title="Terminadas"><i class="fa-solid fa-flag-checkered" style="color:#f59e0b;font-size:9px"></i> <?php echo $tec['ter']; ?> TER</span>
            <span title="Entregadas"><i class="fa-solid fa-circle-check" style="color:#22c55e;font-size:9px"></i> <?php echo $tec['ent']; ?> ENT</span>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>
