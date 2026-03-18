<?php
/*  ═══════════════════════════════════════════════════
    CRM — Pipeline de Ventas (visual Kanban)
    ═══════════════════════════════════════════════════ */

$_pipe_idAsesor = isset($_crm_idAsesor) ? $_crm_idAsesor : 0;
$_pipe_todas = array();

try {
    $_pipe_todas = controladorOrdenes::ctrlMostrarordenesEmpresayPerfil(
        "id_empresa", $_SESSION["empresa"],
        "id_Asesor", $_pipe_idAsesor
    );
    if (!is_array($_pipe_todas)) $_pipe_todas = array();
} catch (Exception $e) { $_pipe_todas = array(); }

$_pipe_grupos = array(
    'AUT'  => array('label'=>'Por Autorizar', 'icon'=>'fa-hourglass-half', 'color'=>'#f59e0b', 'items'=>array()),
    'OK'   => array('label'=>'Aceptadas',     'icon'=>'fa-circle-check',   'color'=>'#3b82f6', 'items'=>array()),
    'PROC' => array('label'=>'En Proceso',    'icon'=>'fa-gears',          'color'=>'#8b5cf6', 'items'=>array()),
    'TER'  => array('label'=>'Terminadas',    'icon'=>'fa-flag-checkered', 'color'=>'#06b6d4', 'items'=>array()),
    'ENT'  => array('label'=>'Entregadas',    'icon'=>'fa-handshake',      'color'=>'#22c55e', 'items'=>array()),
);

foreach ($_pipe_todas as $ord) {
    $est = isset($ord["estado"]) ? $ord["estado"] : "";
    if (strpos($est, "AUT") !== false)           $_pipe_grupos['AUT']['items'][] = $ord;
    elseif (strpos($est, "Aceptado") !== false || strpos($est, "ok") !== false) $_pipe_grupos['OK']['items'][] = $ord;
    elseif (strpos($est, "Terminada") !== false || strpos($est, "ter") !== false) $_pipe_grupos['TER']['items'][] = $ord;
    elseif (strpos($est, "Entregado") !== false || strpos($est, "Ent") !== false) $_pipe_grupos['ENT']['items'][] = $ord;
    else $_pipe_grupos['PROC']['items'][] = $ord;
}

$_pipe_total = count($_pipe_todas);
?>

<div class="crm-card" style="margin-bottom:20px">
  <div class="crm-card-head">
    <h4 class="crm-card-title"><i class="fa-solid fa-diagram-project"></i> Flujo Comercial</h4>
    <span class="crm-badge" style="background:#f1f5f9;color:var(--crm-text2)">
      <?php echo $_pipe_total; ?> órdenes
    </span>
  </div>
  <div class="crm-card-body">

    <?php if ($_pipe_total === 0): ?>
      <div class="crm-empty">
        <i class="fa-solid fa-inbox"></i>
        <strong>Sin órdenes registradas</strong>
        <span style="font-size:12px">Crea tu primera orden para ver el pipeline</span>
      </div>
    <?php else: ?>

      <!-- Track bar -->
      <div class="crm-pipe-track">
        <?php foreach ($_pipe_grupos as $g):
          $cnt = count($g['items']);
          if ($cnt === 0) continue;
          $pct = max(8, round(($cnt / $_pipe_total) * 100));
        ?>
          <div style="background:<?php echo $g['color']; ?>;width:<?php echo $pct; ?>%" title="<?php echo $g['label'].': '.$cnt; ?>"></div>
        <?php endforeach; ?>
      </div>

      <!-- Stage cards -->
      <div class="crm-pipe-stages">
        <?php foreach ($_pipe_grupos as $g):
          $cnt = count($g['items']);
          $pct = $_pipe_total > 0 ? round(($cnt / $_pipe_total) * 100, 1) : 0;
        ?>
          <div class="crm-pipe-stage">
            <div class="crm-pipe-stage-icon" style="background:<?php echo $g['color']; ?>">
              <i class="fa-solid <?php echo $g['icon']; ?>"></i>
            </div>
            <div class="crm-pipe-stage-num"><?php echo $cnt; ?></div>
            <div class="crm-pipe-stage-lbl"><?php echo $g['label']; ?></div>
            <div style="font-size:10px;color:var(--crm-muted);margin-top:2px"><?php echo $pct; ?>%</div>
          </div>
        <?php endforeach; ?>
      </div>

    <?php endif; ?>

  </div>
</div>
