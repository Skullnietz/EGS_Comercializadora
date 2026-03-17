<?php
/* =====================================================
   TOP TÉCNICOS DEL MES — Dashboard Admin
   ===================================================== */

// Órdenes entregadas este mes (reutiliza $entregasMes si ya fue cargada antes)
$_entregasTec = isset($entregasMes) ? $entregasMes
              : controladorOrdenes::ctrMostrarOrdenesSuma();

// Contar entregas por id_tecnico
$conteoPorTec = [];
foreach ($_entregasTec as $o) {
    $tid = $o['id_tecnico'] ?? null;
    if ($tid) {
        $conteoPorTec[$tid] = ($conteoPorTec[$tid] ?? 0) + 1;
    }
}

// Cargar lista de técnicos de la empresa
$_tecList = ControladorTecnicos::ctrMostrarTecnicosDeEmpresas("id_empresa", $_SESSION["empresa"]);

// Construir mapa id => nombre
$mapaTec = [];
foreach ($_tecList as $t) {
    $mapaTec[$t['id']] = $t['nombre'];
}

// Construir ranking: incluir técnicos con 0 entregas también
$ranking = [];
foreach ($mapaTec as $id => $nombre) {
    $ranking[] = [
        'nombre'   => $nombre,
        'entregas' => $conteoPorTec[$id] ?? 0,
    ];
}
// Añadir técnicos de órdenes que quizás no estén en la empresa actual
foreach ($conteoPorTec as $id => $cnt) {
    if (!isset($mapaTec[$id])) {
        $ranking[] = ['nombre' => 'Técnico #' . $id, 'entregas' => $cnt];
    }
}

usort($ranking, fn($a, $b) => $b['entregas'] - $a['entregas']);
$ranking     = array_slice($ranking, 0, 6);
$maxEntregas = max(1, $ranking[0]['entregas'] ?? 1);

$barColors = ['bg-green', 'bg-aqua', 'bg-yellow', 'bg-orange', 'bg-red', 'bg-purple'];
?>

<!--=====================================
TOP TÉCNICOS DEL MES
======================================-->
<div class="box box-solid">

  <div class="box-header with-border" style="background:#3c8dbc;color:#fff;">
    <h3 class="box-title"><i class="fa-solid fa-trophy" style="margin-right:6px"></i>Top Técnicos del Mes</h3>
    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse" style="color:#fff">
        <i class="fa-solid fa-minus"></i>
      </button>
    </div>
  </div>

  <div class="box-body">

    <?php if (empty($ranking) || $ranking[0]['entregas'] === 0): ?>
      <p class="text-muted text-center" style="padding:20px 0">
        <i class="fa-solid fa-circle-info"></i> Sin entregas registradas este mes.
      </p>
    <?php else: ?>
      <?php foreach ($ranking as $i => $tec): ?>
        <?php
          $pct   = round($tec['entregas'] * 100 / $maxEntregas);
          $color = $barColors[$i] ?? 'bg-gray';
        ?>
        <div style="margin-bottom:12px">
          <div style="display:flex;justify-content:space-between;margin-bottom:3px">
            <span>
              <?php if ($i === 0): ?>
                <i class="fa-solid fa-star" style="color:#f39c12"></i>&nbsp;
              <?php endif; ?>
              <strong><?= htmlspecialchars($tec['nombre']) ?></strong>
            </span>
            <span class="badge <?= $color ?>"><?= $tec['entregas'] ?> entrega<?= $tec['entregas'] != 1 ? 's' : '' ?></span>
          </div>
          <div class="progress progress-sm" style="margin:0">
            <div class="progress-bar <?= $color ?>" style="width:<?= $pct ?>%"></div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>

  </div>

  <div class="box-footer text-center">
    <a href="index.php?ruta=tecnicos">Ver todos los técnicos <i class="fa-solid fa-circle-arrow-right"></i></a>
  </div>

</div>
