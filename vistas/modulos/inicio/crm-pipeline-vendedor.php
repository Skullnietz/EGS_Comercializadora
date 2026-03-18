<?php
/*  ═══════════════════════════════════════════════════
    CRM — Pipeline de Ventas con filtro de periodo
    ═══════════════════════════════════════════════════ */

$_pipe_idAsesor = isset($_crm_idAsesor) ? $_crm_idAsesor : 0;

try {
    $_pipe_raw = controladorOrdenes::ctrlMostrarordenesEmpresayPerfil(
        "id_empresa", $_SESSION["empresa"],
        "id_Asesor", $_pipe_idAsesor
    );
    if (!is_array($_pipe_raw)) $_pipe_raw = array();
} catch (Exception $e) { $_pipe_raw = array(); }

// ── Pre-calcular cortes de fecha ──
$_pipe_cortes = array(
    '1m'  => date("Y-m-d", strtotime("-1 month")),
    '3m'  => date("Y-m-d", strtotime("-3 months")),
    '6m'  => date("Y-m-d", strtotime("-6 months")),
    '12m' => date("Y-m-d", strtotime("-12 months")),
);

// ── Clasificar cada orden por estado ──
function _pipeClasificar($est) {
    if (strpos($est, "AUT") !== false) return 'AUT';
    if (strpos($est, "Aceptado") !== false || strpos($est, "ok") !== false) return 'OK';
    if (strpos($est, "Terminada") !== false || strpos($est, "ter") !== false) return 'TER';
    if (strpos($est, "Entregado") !== false || strpos($est, "Ent") !== false) return 'ENT';
    return 'PROC';
}

// ── Generar datos JSON para cada periodo ──
$_pipe_stages_def = array(
    'AUT'  => array('label'=>'Por Autorizar', 'icon'=>'fa-hourglass-half', 'color'=>'#f59e0b'),
    'OK'   => array('label'=>'Aceptadas',     'icon'=>'fa-circle-check',   'color'=>'#3b82f6'),
    'PROC' => array('label'=>'En Proceso',    'icon'=>'fa-gears',          'color'=>'#8b5cf6'),
    'TER'  => array('label'=>'Terminadas',    'icon'=>'fa-flag-checkered', 'color'=>'#06b6d4'),
    'ENT'  => array('label'=>'Entregadas',    'icon'=>'fa-handshake',      'color'=>'#22c55e'),
);

$_pipe_data = array();
foreach ($_pipe_cortes as $periodo => $corte) {
    $_pipe_data[$periodo] = array('AUT'=>0, 'OK'=>0, 'PROC'=>0, 'TER'=>0, 'ENT'=>0, 'total'=>0);
    foreach ($_pipe_raw as $ord) {
        $fi = isset($ord["fecha_ingreso"]) ? substr($ord["fecha_ingreso"], 0, 10) : "";
        if ($fi >= $corte) {
            $est = isset($ord["estado"]) ? $ord["estado"] : "";
            $clave = _pipeClasificar($est);
            $_pipe_data[$periodo][$clave]++;
            $_pipe_data[$periodo]['total']++;
        }
    }
}

// Default: 1 mes
$_pipe_default = $_pipe_data['1m'];
?>

<div class="crm-card" style="margin-bottom:20px">
  <div class="crm-card-head">
    <h4 class="crm-card-title"><i class="fa-solid fa-diagram-project"></i> Flujo Comercial</h4>
    <div style="display:flex;align-items:center;gap:6px">
      <span class="crm-badge" id="pipeBadgeTotal" style="background:#f1f5f9;color:#475569;margin-right:4px">
        <?php echo $_pipe_default['total']; ?> órdenes
      </span>
      <!-- Selector de periodo -->
      <div style="display:inline-flex;background:#f1f5f9;border-radius:8px;padding:2px;gap:2px" id="pipeFilter">
        <button type="button" class="pipe-btn active" data-period="1m"
                style="padding:4px 10px;border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;transition:all .15s;background:#6366f1;color:#fff">
          Mes
        </button>
        <button type="button" class="pipe-btn" data-period="3m"
                style="padding:4px 10px;border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;transition:all .15s;background:transparent;color:#64748b">
          3M
        </button>
        <button type="button" class="pipe-btn" data-period="6m"
                style="padding:4px 10px;border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;transition:all .15s;background:transparent;color:#64748b">
          6M
        </button>
        <button type="button" class="pipe-btn" data-period="12m"
                style="padding:4px 10px;border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;transition:all .15s;background:transparent;color:#64748b">
          Año
        </button>
      </div>
    </div>
  </div>
  <div class="crm-card-body">

    <!-- Track bar -->
    <div class="crm-pipe-track" id="pipeTrack">
      <?php foreach ($_pipe_stages_def as $k => $s):
        $cnt = $_pipe_default[$k];
        $pct = $_pipe_default['total'] > 0 ? max(4, round(($cnt / $_pipe_default['total']) * 100)) : 0;
      ?>
        <div id="pipeTrack_<?php echo $k; ?>" style="background:<?php echo $s['color']; ?>;width:<?php echo $cnt > 0 ? $pct : 0; ?>%;transition:width .4s cubic-bezier(.4,0,.2,1)"></div>
      <?php endforeach; ?>
    </div>

    <!-- Stage cards -->
    <div class="crm-pipe-stages">
      <?php foreach ($_pipe_stages_def as $k => $s):
        $cnt = $_pipe_default[$k];
        $pct = $_pipe_default['total'] > 0 ? round(($cnt / $_pipe_default['total']) * 100, 1) : 0;
      ?>
        <div class="crm-pipe-stage" id="pipeStage_<?php echo $k; ?>">
          <div class="crm-pipe-stage-icon" style="background:<?php echo $s['color']; ?>">
            <i class="fa-solid <?php echo $s['icon']; ?>"></i>
          </div>
          <div class="crm-pipe-stage-num" id="pipeNum_<?php echo $k; ?>"><?php echo $cnt; ?></div>
          <div class="crm-pipe-stage-lbl"><?php echo $s['label']; ?></div>
          <div class="crm-pipe-stage-pct" id="pipePct_<?php echo $k; ?>" style="font-size:10px;color:#94a3b8;margin-top:2px"><?php echo $pct; ?>%</div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Empty state (hidden by default) -->
    <div id="pipeEmpty" style="display:none">
      <div class="crm-empty">
        <i class="fa-solid fa-inbox"></i>
        <strong>Sin órdenes en este periodo</strong>
        <span style="font-size:12px">Prueba con un rango más amplio</span>
      </div>
    </div>

  </div>
</div>

<script>
(function(){
  // Datos precalculados desde PHP
  var pipeData = <?php echo json_encode($_pipe_data); ?>;
  var stages   = ['AUT','OK','PROC','TER','ENT'];

  $('#pipeFilter').on('click', '.pipe-btn', function(){
    var $btn    = $(this);
    var period  = $btn.data('period');
    var data    = pipeData[period];
    if (!data) return;

    // Activar botón
    $('#pipeFilter .pipe-btn').css({ background: 'transparent', color: '#64748b' }).removeClass('active');
    $btn.css({ background: '#6366f1', color: '#fff' }).addClass('active');

    var total = data.total;

    // Badge
    $('#pipeBadgeTotal').text(total + ' órdenes');

    // Mostrar/ocultar
    if (total === 0) {
      $('#pipeTrack, .crm-pipe-stages').hide();
      $('#pipeEmpty').show();
      return;
    } else {
      $('#pipeTrack, .crm-pipe-stages').show();
      $('#pipeEmpty').hide();
    }

    // Actualizar cada stage
    for (var i = 0; i < stages.length; i++) {
      var k   = stages[i];
      var cnt = data[k] || 0;
      var pct = total > 0 ? Math.round((cnt / total) * 1000) / 10 : 0;
      var tw  = cnt > 0 ? Math.max(4, Math.round((cnt / total) * 100)) : 0;

      // Números
      $('#pipeNum_' + k).text(cnt);
      $('#pipePct_' + k).text(pct + '%');

      // Track bar width
      $('#pipeTrack_' + k).css('width', tw + '%');
    }
  });
})();
</script>
