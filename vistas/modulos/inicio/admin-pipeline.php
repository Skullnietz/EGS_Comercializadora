<?php
/*  ═══════════════════════════════════════════════════
    DASHBOARD ADMIN — Pipeline general de la empresa
    ═══════════════════════════════════════════════════ */

// ── Cargar TODAS las ordenes de la empresa ──
try {
    $_admpipe_raw = controladorOrdenes::ctrlMostrarordenesEmpresayPerfil(
        "id_empresa", $_SESSION["empresa"],
        null, null
    );
    if (!is_array($_admpipe_raw)) $_admpipe_raw = array();
} catch (Exception $e) { $_admpipe_raw = array(); }

// Si el metodo anterior no funciona sin asesor, intentar alternativa
if (empty($_admpipe_raw)) {
    try {
        $_admpipe_ctrl = new controladorOrdenes();
        $_admpipe_estados = array(
            "En revisión (REV)", "Pendiente de autorización (AUT",
            "Aceptado (ok)", "Terminada (ter)", "Entregado (Ent)"
        );
        $_admpipe_raw = array();
        foreach ($_admpipe_estados as $est) {
            $r = $_admpipe_ctrl->ctrlMostrarOrdenesPorEstadoEmpresayTecnico(
                $est, "id_empresa", $_SESSION["empresa"], null, null
            );
            if (is_array($r)) $_admpipe_raw = array_merge($_admpipe_raw, $r);
        }
    } catch (Exception $e) {}
}

// ── Cortes de fecha ──
$_admpipe_cortes = array(
    '1m'  => date("Y-m-d", strtotime("-1 month")),
    '3m'  => date("Y-m-d", strtotime("-3 months")),
    '6m'  => date("Y-m-d", strtotime("-6 months")),
    '12m' => date("Y-m-d", strtotime("-12 months")),
);

// ── Clasificador de estados ──
function _admPipeClasificar($est) {
    if (strpos($est, "REV") !== false || strpos($est, "revisión") !== false) return 'REV';
    if (strpos($est, "AUT") !== false) return 'AUT';
    if (strpos($est, "Aceptado") !== false || strpos($est, "ok") !== false) return 'OK';
    if (strpos($est, "Terminada") !== false || strpos($est, "ter") !== false) return 'TER';
    if (strpos($est, "Entregado") !== false || strpos($est, "Ent") !== false) return 'ENT';
    return 'REV';
}

$_admpipe_stages_def = array(
    'REV' => array('label'=>'Revision',     'icon'=>'fa-magnifying-glass', 'color'=>'#ef4444'),
    'AUT' => array('label'=>'Por Autorizar', 'icon'=>'fa-hourglass-half',  'color'=>'#8b5cf6'),
    'OK'  => array('label'=>'Aceptadas',     'icon'=>'fa-circle-check',    'color'=>'#3b82f6'),
    'TER' => array('label'=>'Terminadas',    'icon'=>'fa-flag-checkered',  'color'=>'#f59e0b'),
    'ENT' => array('label'=>'Entregadas',    'icon'=>'fa-handshake',       'color'=>'#22c55e'),
);

$_admpipe_data = array();
foreach ($_admpipe_cortes as $periodo => $corte) {
    $_admpipe_data[$periodo] = array('REV'=>0, 'AUT'=>0, 'OK'=>0, 'TER'=>0, 'ENT'=>0, 'total'=>0);
    foreach ($_admpipe_raw as $ord) {
        $fi = isset($ord["fecha_ingreso"]) ? substr($ord["fecha_ingreso"], 0, 10) : "";
        if ($fi >= $corte) {
            $est = isset($ord["estado"]) ? $ord["estado"] : "";
            $clave = _admPipeClasificar($est);
            $_admpipe_data[$periodo][$clave]++;
            $_admpipe_data[$periodo]['total']++;
        }
    }
}

$_admpipe_default = $_admpipe_data['1m'];
?>

<div class="crm-card" style="margin-bottom:20px">
  <div class="crm-card-head">
    <h4 class="crm-card-title"><i class="fa-solid fa-diagram-project"></i> Pipeline General</h4>
    <div style="display:flex;align-items:center;gap:6px">
      <span class="crm-badge" id="admPipeBadge" style="background:#f1f5f9;color:#475569;margin-right:4px">
        <?php echo $_admpipe_default['total']; ?> ordenes
      </span>
      <div style="display:inline-flex;background:#f1f5f9;border-radius:8px;padding:2px;gap:2px" id="admPipeFilter">
        <button type="button" class="adm-pipe-btn active" data-period="1m"
                style="padding:4px 10px;border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;transition:all .15s;background:#6366f1;color:#fff">
          Mes
        </button>
        <button type="button" class="adm-pipe-btn" data-period="3m"
                style="padding:4px 10px;border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;transition:all .15s;background:transparent;color:#64748b">
          3M
        </button>
        <button type="button" class="adm-pipe-btn" data-period="6m"
                style="padding:4px 10px;border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;transition:all .15s;background:transparent;color:#64748b">
          6M
        </button>
        <button type="button" class="adm-pipe-btn" data-period="12m"
                style="padding:4px 10px;border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;transition:all .15s;background:transparent;color:#64748b">
          Ano
        </button>
      </div>
    </div>
  </div>
  <div class="crm-card-body">

    <!-- Track bar -->
    <div class="crm-pipe-track" id="admPipeTrack">
      <?php foreach ($_admpipe_stages_def as $k => $s):
        $cnt = $_admpipe_default[$k];
        $pct = $_admpipe_default['total'] > 0 ? max(4, round(($cnt / $_admpipe_default['total']) * 100)) : 0;
      ?>
        <div id="admTrack_<?php echo $k; ?>" style="background:<?php echo $s['color']; ?>;width:<?php echo $cnt > 0 ? $pct : 0; ?>%;transition:width .4s cubic-bezier(.4,0,.2,1)"></div>
      <?php endforeach; ?>
    </div>

    <!-- Stage cards -->
    <div class="crm-pipe-stages" id="admPipeStages">
      <?php foreach ($_admpipe_stages_def as $k => $s):
        $cnt = $_admpipe_default[$k];
        $pct = $_admpipe_default['total'] > 0 ? round(($cnt / $_admpipe_default['total']) * 100, 1) : 0;
      ?>
        <div class="crm-pipe-stage" id="admStage_<?php echo $k; ?>">
          <div class="crm-pipe-stage-icon" style="background:<?php echo $s['color']; ?>">
            <i class="fa-solid <?php echo $s['icon']; ?>"></i>
          </div>
          <div class="crm-pipe-stage-num" id="admNum_<?php echo $k; ?>"><?php echo $cnt; ?></div>
          <div class="crm-pipe-stage-lbl"><?php echo $s['label']; ?></div>
          <div class="crm-pipe-stage-pct" id="admPct_<?php echo $k; ?>" style="font-size:10px;color:#94a3b8;margin-top:2px"><?php echo $pct; ?>%</div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Empty state -->
    <div id="admPipeEmpty" style="display:none">
      <div class="crm-empty">
        <i class="fa-solid fa-inbox"></i>
        <strong>Sin ordenes en este periodo</strong>
        <span style="font-size:12px">Prueba con un rango mas amplio</span>
      </div>
    </div>

  </div>
</div>

<script>
(function(){
  var admPipeData = <?php echo json_encode($_admpipe_data); ?>;
  var stages = ['REV','AUT','OK','TER','ENT'];

  $('#admPipeFilter').on('click', '.adm-pipe-btn', function(){
    var $btn   = $(this);
    var period = $btn.data('period');
    var data   = admPipeData[period];
    if (!data) return;

    $('#admPipeFilter .adm-pipe-btn').css({ background: 'transparent', color: '#64748b' }).removeClass('active');
    $btn.css({ background: '#6366f1', color: '#fff' }).addClass('active');

    var total = data.total;
    $('#admPipeBadge').text(total + ' ordenes');

    if (total === 0) {
      $('#admPipeTrack, #admPipeStages').hide();
      $('#admPipeEmpty').show();
      return;
    } else {
      $('#admPipeTrack, #admPipeStages').show();
      $('#admPipeEmpty').hide();
    }

    for (var i = 0; i < stages.length; i++) {
      var k   = stages[i];
      var cnt = data[k] || 0;
      var pct = total > 0 ? Math.round((cnt / total) * 1000) / 10 : 0;
      var tw  = cnt > 0 ? Math.max(4, Math.round((cnt / total) * 100)) : 0;

      $('#admNum_' + k).text(cnt);
      $('#admPct_' + k).text(pct + '%');
      $('#admTrack_' + k).css('width', tw + '%');
    }
  });
})();
</script>
