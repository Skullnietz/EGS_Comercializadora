<?php
/*  ═══════════════════════════════════════════════════
    DASHBOARD ADMIN — Top Tecnicos (estilo CRM)
    ═══════════════════════════════════════════════════ */

$_admtec_entregadas = isset($_adm_ordEntregadas) ? $_adm_ordEntregadas
                    : controladorOrdenes::ctrMostrarOrdenesSuma();

$_admtec_conteo = array();
if (is_array($_admtec_entregadas)) {
    foreach ($_admtec_entregadas as $o) {
        $tid = isset($o['id_tecnico']) ? $o['id_tecnico'] : null;
        if ($tid) {
            $_admtec_conteo[$tid] = (isset($_admtec_conteo[$tid]) ? $_admtec_conteo[$tid] : 0) + 1;
        }
    }
}

$_admtec_lista = ControladorTecnicos::ctrMostrarTecnicosDeEmpresas("id_empresa", $_SESSION["empresa"]);
$_admtec_mapa = array();
if (is_array($_admtec_lista)) {
    foreach ($_admtec_lista as $t) {
        $_admtec_mapa[$t['id']] = $t['nombre'];
    }
}

$_admtec_ranking = array();
foreach ($_admtec_mapa as $id => $nombre) {
    $_admtec_ranking[] = array(
        'nombre'   => $nombre,
        'entregas' => isset($_admtec_conteo[$id]) ? $_admtec_conteo[$id] : 0,
    );
}
foreach ($_admtec_conteo as $id => $cnt) {
    if (!isset($_admtec_mapa[$id])) {
        $_admtec_ranking[] = array('nombre' => 'Tecnico #' . $id, 'entregas' => $cnt);
    }
}

usort($_admtec_ranking, function($a, $b) { return $b['entregas'] - $a['entregas']; });
$_admtec_ranking = array_slice($_admtec_ranking, 0, 6);
$_admtec_max = count($_admtec_ranking) > 0 && $_admtec_ranking[0]['entregas'] > 0
    ? $_admtec_ranking[0]['entregas'] : 1;

$_admtec_gradients = array(
    'linear-gradient(135deg,#22c55e,#4ade80)',
    'linear-gradient(135deg,#3b82f6,#60a5fa)',
    'linear-gradient(135deg,#f59e0b,#fbbf24)',
    'linear-gradient(135deg,#ef4444,#f87171)',
    'linear-gradient(135deg,#8b5cf6,#a78bfa)',
    'linear-gradient(135deg,#06b6d4,#22d3ee)',
);
$_admtec_colors = array('#22c55e','#3b82f6','#f59e0b','#ef4444','#8b5cf6','#06b6d4');
?>

<div class="crm-card" style="margin-bottom:20px">
  <div class="crm-card-head">
    <h4 class="crm-card-title"><i class="fa-solid fa-trophy"></i> Top Tecnicos del Mes</h4>
    <a href="index.php?ruta=tecnicos" style="font-size:12px;font-weight:600;color:#6366f1;text-decoration:none">
      Ver todos <i class="fa-solid fa-arrow-right" style="font-size:10px"></i>
    </a>
  </div>
  <div class="crm-card-body" style="padding:16px 20px">
    <?php if (empty($_admtec_ranking) || $_admtec_ranking[0]['entregas'] === 0): ?>
      <div class="crm-empty">
        <i class="fa-solid fa-circle-info"></i>
        <strong>Sin entregas este mes</strong>
      </div>
    <?php else: ?>
      <div style="display:flex;flex-direction:column;gap:12px">
        <?php foreach ($_admtec_ranking as $i => $tec):
          $pct = round($tec['entregas'] * 100 / $_admtec_max);
          $color = $_admtec_colors[$i % count($_admtec_colors)];
          $grad = $_admtec_gradients[$i % count($_admtec_gradients)];
          $initial = mb_strtoupper(mb_substr($tec['nombre'], 0, 1));
        ?>
          <div style="display:flex;align-items:center;gap:12px">
            <div style="width:34px;height:34px;border-radius:50%;background:<?php echo $grad; ?>;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;color:#fff;flex-shrink:0;box-shadow:0 2px 6px rgba(0,0,0,.12)">
              <?php if ($i === 0): ?>
                <i class="fa-solid fa-crown" style="font-size:14px"></i>
              <?php else: ?>
                <?php echo $initial; ?>
              <?php endif; ?>
            </div>
            <div style="flex:1;min-width:0">
              <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px">
                <span style="font-size:13px;font-weight:600;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                  <?php echo htmlspecialchars($tec['nombre']); ?>
                </span>
                <span style="font-size:12px;font-weight:700;color:<?php echo $color; ?>;flex-shrink:0;margin-left:8px">
                  <?php echo $tec['entregas']; ?> entrega<?php echo $tec['entregas'] != 1 ? 's' : ''; ?>
                </span>
              </div>
              <div style="height:6px;background:#f1f5f9;border-radius:3px;overflow:hidden">
                <div style="height:100%;width:<?php echo $pct; ?>%;background:<?php echo $grad; ?>;border-radius:3px;transition:width .6s cubic-bezier(.4,0,.2,1)"></div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>
