<?php
/*  ═══════════════════════════════════════════════════
    DASHBOARD TÉCNICO — Panel de trabajo completo
    ═══════════════════════════════════════════════════ */

// ── Identificar técnico ──
$_tec_id = 0;
$_tec_nombre = $_SESSION["nombre"];
try {
    $_tec_data = ControladorTecnicos::ctrMostrarTecnicos("correo", $_SESSION["email"]);
    if (is_array($_tec_data) && isset($_tec_data["id"])) {
        $_tec_id = intval($_tec_data["id"]);
    }
} catch (Exception $e) {}

// ── Cargar órdenes por estado (método NO static) ──
$_tec_ctrl = new controladorOrdenes();
$_tec_item = "id_empresa";
$_tec_val  = $_SESSION["empresa"];
$_tec_field = "id_tecnico";

$_tec_REV = array();
$_tec_OK  = array();
$_tec_TER = array();
$_tec_ENT = array();
$_tec_AUT = array();

try {
    $r = $_tec_ctrl->ctrlMostrarOrdenesPorEstadoEmpresayTecnico("En revisión (REV)", $_tec_item, $_tec_val, $_tec_field, $_tec_id);
    if (is_array($r)) $_tec_REV = $r;
} catch (Exception $e) {}

try {
    $r = $_tec_ctrl->ctrlMostrarOrdenesPorEstadoEmpresayTecnico("Aceptado (ok)", $_tec_item, $_tec_val, $_tec_field, $_tec_id);
    if (is_array($r)) $_tec_OK = $r;
} catch (Exception $e) {}

try {
    $r = $_tec_ctrl->ctrlMostrarOrdenesPorEstadoEmpresayTecnico("Terminada (ter)", $_tec_item, $_tec_val, $_tec_field, $_tec_id);
    if (is_array($r)) $_tec_TER = $r;
} catch (Exception $e) {}

try {
    $r = $_tec_ctrl->ctrlMostrarOrdenesPorEstadoEmpresayTecnico("Entregado (Ent)", $_tec_item, $_tec_val, $_tec_field, $_tec_id);
    if (is_array($r)) $_tec_ENT = $r;
} catch (Exception $e) {}

try {
    $r = $_tec_ctrl->ctrlMostrarOrdenesPorEstadoEmpresayTecnico("Pendiente de autorización (AUT", $_tec_item, $_tec_val, $_tec_field, $_tec_id);
    if (is_array($r)) $_tec_AUT = $r;
} catch (Exception $e) {}

// ── Filtrar por fecha ──
$_tec_limite = date("Y-m-d", strtotime("-1 month"));
$_tec_limite3m = date("Y-m-d", strtotime("-3 months"));

function _tecFiltrarFecha($arr, $limite) {
    return array_filter($arr, function($o) use ($limite) {
        $fi = isset($o["fecha_ingreso"]) ? substr($o["fecha_ingreso"], 0, 10) : "";
        return $fi >= $limite;
    });
}

// Filtrar órdenes activas (REV/OK) a máximo 3 meses — las más antiguas están congeladas
$_tec_REV = array_values(_tecFiltrarFecha($_tec_REV, $_tec_limite3m));
$_tec_OK  = array_values(_tecFiltrarFecha($_tec_OK, $_tec_limite3m));

$_tec_REV_mes = _tecFiltrarFecha($_tec_REV, $_tec_limite);
$_tec_OK_mes  = _tecFiltrarFecha($_tec_OK, $_tec_limite);
$_tec_TER_mes = _tecFiltrarFecha($_tec_TER, $_tec_limite);
$_tec_ENT_mes = _tecFiltrarFecha($_tec_ENT, $_tec_limite);

// ── Totales (ya filtrados a 3 meses) ──
$_tec_totalActivas = count($_tec_REV) + count($_tec_OK);
$_tec_totalMes = count($_tec_REV_mes) + count($_tec_OK_mes) + count($_tec_TER_mes) + count($_tec_ENT_mes);

// ── Eficiencia: entregadas / total del mes ──
$_tec_eficiencia = $_tec_totalMes > 0
    ? round((count($_tec_ENT_mes) / $_tec_totalMes) * 100, 1) : 0;

if ($_tec_eficiencia >= 70) { $_tec_efGrad = '#059669,#10b981'; }
elseif ($_tec_eficiencia >= 40) { $_tec_efGrad = '#d97706,#f59e0b'; }
else { $_tec_efGrad = '#dc2626,#ef4444'; }

// ── Helper: obtener imagen de orden ──
function _tecGetImg($ord) {
    if (!empty($ord["multimedia"])) {
        $album = json_decode($ord["multimedia"], true);
        if (is_array($album)) {
            foreach ($album as $img) {
                if (isset($img["foto"]) && !empty($img["foto"])) return $img["foto"];
            }
        }
    }
    return "";
}

// ── Helper: link de orden ──
function _tecLink($o) {
    return 'index.php?ruta=infoOrden&idOrden='.$o["id"]
        .'&empresa='.(isset($o["id_empresa"]) ? $o["id_empresa"] : '')
        .'&asesor='.(isset($o["id_Asesor"]) ? $o["id_Asesor"] : '')
        .'&cliente='.(isset($o["id_usuario"]) ? $o["id_usuario"] : '')
        .'&tecnico='.(isset($o["id_tecnico"]) ? $o["id_tecnico"] : '')
        .'&tecnicodos='.(isset($o["id_tecnicoDos"]) ? $o["id_tecnicoDos"] : '')
        .'&pedido='.(isset($o["id_pedido"]) ? $o["id_pedido"] : '');
}

// ── Helper: días desde fecha ──
function _tecDias($fecha) {
    if (empty($fecha)) return 0;
    try { return max(0, (new DateTime($fecha))->diff(new DateTime())->days); }
    catch (Exception $e) { return 0; }
}

// ── Helper: render imagen con fallback a default ──
function _tecImgTag($src, $w, $h, $radius) {
    $fallback = "this.onerror=null;this.src='vistas/img/default/default.png';";
    $html = '<img src="' . htmlspecialchars($src) . '" onerror="' . $fallback . '" style="width:'.$w.'px;height:'.$h.'px;border-radius:'.$radius.'px;object-fit:cover;flex-shrink:0;border:1px solid #e2e8f0">';
    return $html;
}

function _tecImgPlaceholder($w, $h, $radius) {
    return '<div style="width:'.$w.'px;height:'.$h.'px;border-radius:'.$radius.'px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;color:#cbd5e1;font-size:'.round($w*0.35).'px;flex-shrink:0"><i class="fa-solid fa-screwdriver-wrench"></i></div>';
}

// ══════════════════════════════════════════
// PIPELINE: Pre-calcular datos por periodo
// ══════════════════════════════════════════
$_tec_allOrders = array_merge($_tec_REV, $_tec_OK, $_tec_AUT, $_tec_TER, $_tec_ENT);

$_tec_pipe_cortes = array(
    '1m'  => date("Y-m-d", strtotime("-1 month")),
    '3m'  => date("Y-m-d", strtotime("-3 months")),
    '6m'  => date("Y-m-d", strtotime("-6 months")),
    '12m' => date("Y-m-d", strtotime("-12 months")),
);

function _tecPipeClasificar($est) {
    if (strpos($est, "REV") !== false || strpos($est, "revisión") !== false) return 'REV';
    if (strpos($est, "AUT") !== false) return 'AUT';
    if (strpos($est, "Aceptado") !== false || strpos($est, "ok") !== false) return 'OK';
    if (strpos($est, "Terminada") !== false || strpos($est, "ter") !== false) return 'TER';
    if (strpos($est, "Entregado") !== false || strpos($est, "Ent") !== false) return 'ENT';
    return 'REV';
}

$_tec_pipe_data = array();
foreach ($_tec_pipe_cortes as $periodo => $corte) {
    $_tec_pipe_data[$periodo] = array('REV'=>0, 'OK'=>0, 'AUT'=>0, 'TER'=>0, 'ENT'=>0, 'total'=>0);
    foreach ($_tec_allOrders as $ord) {
        $fi = isset($ord["fecha_ingreso"]) ? substr($ord["fecha_ingreso"], 0, 10) : "";
        if ($fi >= $corte) {
            $est = isset($ord["estado"]) ? $ord["estado"] : "";
            $clave = _tecPipeClasificar($est);
            $_tec_pipe_data[$periodo][$clave]++;
            $_tec_pipe_data[$periodo]['total']++;
        }
    }
}

$_tec_pipe_default = $_tec_pipe_data['1m'];

$_tec_stages_def = array(
    'REV' => array('label'=>'Revisión',    'icon'=>'fa-magnifying-glass', 'color'=>'#ef4444'),
    'OK'  => array('label'=>'Aceptadas',   'icon'=>'fa-circle-check',     'color'=>'#3b82f6'),
    'AUT' => array('label'=>'AUT',         'icon'=>'fa-hourglass-half',   'color'=>'#8b5cf6'),
    'TER' => array('label'=>'Terminadas',  'icon'=>'fa-flag-checkered',   'color'=>'#f59e0b'),
    'ENT' => array('label'=>'Entregadas',  'icon'=>'fa-handshake',        'color'=>'#22c55e'),
);
?>

<!-- ══════════════════════════════════════════
     KPIs
══════════════════════════════════════════ -->
<div class="row">
  <!-- En Revisión -->
  <div class="col-lg-3 col-sm-6 col-xs-12" style="margin-bottom:16px">
    <div class="crm-kpi" style="background:linear-gradient(135deg,#ef4444,#f87171)">
      <i class="fa-solid fa-magnifying-glass crm-kpi-icon"></i>
      <div class="crm-kpi-label">En Revisión</div>
      <div class="crm-kpi-value"><?php echo count($_tec_REV); ?></div>
      <div class="crm-kpi-sub"><i class="fa-solid fa-clock"></i> Pendientes de diagnosticar</div>
      <div class="crm-kpi-bar"><span style="width:<?php echo min(count($_tec_REV) * 10, 100); ?>%"></span></div>
    </div>
  </div>

  <!-- Aceptadas -->
  <div class="col-lg-3 col-sm-6 col-xs-12" style="margin-bottom:16px">
    <div class="crm-kpi" style="background:linear-gradient(135deg,#3b82f6,#60a5fa)">
      <i class="fa-solid fa-circle-check crm-kpi-icon"></i>
      <div class="crm-kpi-label">Aceptadas</div>
      <div class="crm-kpi-value"><?php echo count($_tec_OK); ?></div>
      <div class="crm-kpi-sub"><i class="fa-solid fa-gears"></i> En proceso de reparación</div>
      <div class="crm-kpi-bar"><span style="width:<?php echo min(count($_tec_OK) * 10, 100); ?>%"></span></div>
    </div>
  </div>

  <!-- Terminadas -->
  <div class="col-lg-3 col-sm-6 col-xs-12" style="margin-bottom:16px">
    <div class="crm-kpi" style="background:linear-gradient(135deg,#f59e0b,#fbbf24)">
      <i class="fa-solid fa-flag-checkered crm-kpi-icon"></i>
      <div class="crm-kpi-label">Terminadas</div>
      <div class="crm-kpi-value"><?php echo count($_tec_TER); ?></div>
      <div class="crm-kpi-sub"><i class="fa-solid fa-hourglass-half"></i> Listas para entregar</div>
      <div class="crm-kpi-bar"><span style="width:<?php echo min(count($_tec_TER) * 10, 100); ?>%"></span></div>
    </div>
  </div>

  <!-- Eficiencia -->
  <div class="col-lg-3 col-sm-6 col-xs-12" style="margin-bottom:16px">
    <div class="crm-kpi" style="background:linear-gradient(135deg,<?php echo $_tec_efGrad; ?>)">
      <i class="fa-solid fa-gauge-high crm-kpi-icon"></i>
      <div class="crm-kpi-label">Eficiencia del Mes</div>
      <div class="crm-kpi-value"><?php echo $_tec_eficiencia; ?>%</div>
      <div class="crm-kpi-sub"><i class="fa-solid fa-trophy"></i> <?php echo count($_tec_ENT_mes); ?> entregadas de <?php echo $_tec_totalMes; ?></div>
      <div class="crm-kpi-bar"><span style="width:<?php echo $_tec_eficiencia; ?>%"></span></div>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════
     PIPELINE con filtro de periodo
══════════════════════════════════════════ -->
<div class="crm-section">
  <div class="crm-section-icon" style="background:linear-gradient(135deg,#3b82f6,#6366f1)">
    <i class="fa-solid fa-diagram-project"></i>
  </div>
  <div>
    <h3>Mi Flujo de Trabajo</h3>
    <p>Estado actual de tus órdenes asignadas</p>
  </div>
</div>

<div class="crm-card" style="margin-bottom:20px">
  <div class="crm-card-head">
    <h4 class="crm-card-title"><i class="fa-solid fa-diagram-project"></i> Pipeline</h4>
    <div style="display:flex;align-items:center;gap:6px">
      <span class="crm-badge" id="tecPipeBadge" style="background:#f1f5f9;color:#475569;margin-right:4px">
        <?php echo $_tec_pipe_default['total']; ?> órdenes
      </span>
      <!-- Selector de periodo -->
      <div style="display:inline-flex;background:#f1f5f9;border-radius:8px;padding:2px;gap:2px" id="tecPipeFilter">
        <button type="button" class="tec-pipe-btn active" data-period="1m"
                style="padding:4px 10px;border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;transition:all .15s;background:#6366f1;color:#fff">
          Mes
        </button>
        <button type="button" class="tec-pipe-btn" data-period="3m"
                style="padding:4px 10px;border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;transition:all .15s;background:transparent;color:#64748b">
          3M
        </button>
        <button type="button" class="tec-pipe-btn" data-period="6m"
                style="padding:4px 10px;border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;transition:all .15s;background:transparent;color:#64748b">
          6M
        </button>
        <button type="button" class="tec-pipe-btn" data-period="12m"
                style="padding:4px 10px;border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;transition:all .15s;background:transparent;color:#64748b">
          Año
        </button>
      </div>
    </div>
  </div>
  <div class="crm-card-body">

    <!-- Track bar -->
    <div class="crm-pipe-track" id="tecPipeTrack">
      <?php foreach ($_tec_stages_def as $k => $s):
        $cnt = $_tec_pipe_default[$k];
        $pct = $_tec_pipe_default['total'] > 0 ? max(4, round(($cnt / $_tec_pipe_default['total']) * 100)) : 0;
      ?>
        <div id="tecTrack_<?php echo $k; ?>" style="background:<?php echo $s['color']; ?>;width:<?php echo $cnt > 0 ? $pct : 0; ?>%;transition:width .4s cubic-bezier(.4,0,.2,1)"></div>
      <?php endforeach; ?>
    </div>

    <!-- Stage cards -->
    <div class="crm-pipe-stages" id="tecPipeStages">
      <?php foreach ($_tec_stages_def as $k => $s):
        $cnt = $_tec_pipe_default[$k];
        $pct = $_tec_pipe_default['total'] > 0 ? round(($cnt / $_tec_pipe_default['total']) * 100, 1) : 0;
      ?>
        <div class="crm-pipe-stage" id="tecStage_<?php echo $k; ?>">
          <div class="crm-pipe-stage-icon" style="background:<?php echo $s['color']; ?>">
            <i class="fa-solid <?php echo $s['icon']; ?>"></i>
          </div>
          <div class="crm-pipe-stage-num" id="tecNum_<?php echo $k; ?>"><?php echo $cnt; ?></div>
          <div class="crm-pipe-stage-lbl"><?php echo $s['label']; ?></div>
          <div class="crm-pipe-stage-pct" id="tecPct_<?php echo $k; ?>" style="font-size:10px;color:#94a3b8;margin-top:2px"><?php echo $pct; ?>%</div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Empty state -->
    <div id="tecPipeEmpty" style="display:none">
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
  var tecPipeData = <?php echo json_encode($_tec_pipe_data); ?>;
  var stages = ['REV','OK','AUT','TER','ENT'];

  $('#tecPipeFilter').on('click', '.tec-pipe-btn', function(){
    var $btn   = $(this);
    var period = $btn.data('period');
    var data   = tecPipeData[period];
    if (!data) return;

    // Activar botón
    $('#tecPipeFilter .tec-pipe-btn').css({ background: 'transparent', color: '#64748b' }).removeClass('active');
    $btn.css({ background: '#6366f1', color: '#fff' }).addClass('active');

    var total = data.total;
    $('#tecPipeBadge').text(total + ' órdenes');

    if (total === 0) {
      $('#tecPipeTrack, #tecPipeStages').hide();
      $('#tecPipeEmpty').show();
      return;
    } else {
      $('#tecPipeTrack, #tecPipeStages').show();
      $('#tecPipeEmpty').hide();
    }

    for (var i = 0; i < stages.length; i++) {
      var k   = stages[i];
      var cnt = data[k] || 0;
      var pct = total > 0 ? Math.round((cnt / total) * 1000) / 10 : 0;
      var tw  = cnt > 0 ? Math.max(4, Math.round((cnt / total) * 100)) : 0;

      $('#tecNum_' + k).text(cnt);
      $('#tecPct_' + k).text(pct + '%');
      $('#tecTrack_' + k).css('width', tw + '%');
    }
  });
})();
</script>

<!-- ══════════════════════════════════════════
     ÓRDENES EN REVISIÓN (prioridad máxima)
══════════════════════════════════════════ -->
<div class="crm-section">
  <div class="crm-section-icon" style="background:linear-gradient(135deg,#ef4444,#f59e0b)">
    <i class="fa-solid fa-magnifying-glass"></i>
  </div>
  <div>
    <h3>Por Diagnosticar</h3>
    <p>Órdenes en revisión que necesitan tu diagnóstico</p>
  </div>
</div>

<div class="row">
  <div class="col-lg-8 col-md-7 col-xs-12">
    <div class="crm-card" style="margin-bottom:20px">
      <div class="crm-card-head">
        <h4 class="crm-card-title"><i class="fa-solid fa-magnifying-glass"></i> En Revisión</h4>
        <?php if (count($_tec_REV) > 0): ?>
          <span class="crm-badge" style="background:#fef2f2;color:#dc2626">
            <i class="fa-solid fa-fire" style="font-size:10px"></i>
            <?php echo count($_tec_REV); ?> pendiente<?php echo count($_tec_REV) > 1 ? 's' : ''; ?>
          </span>
        <?php endif; ?>
      </div>
      <div class="crm-card-body-flush">
        <?php if (empty($_tec_REV)): ?>
          <div class="crm-empty">
            <i class="fa-solid fa-circle-check" style="color:#22c55e;opacity:.6"></i>
            <strong>Sin revisiones pendientes</strong>
            <span style="font-size:12px">Todas las órdenes han sido diagnosticadas</span>
          </div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="crm-table">
              <thead><tr>
                <th></th>
                <th>Orden</th>
                <th>Equipo</th>
                <th style="text-align:center">Días</th>
                <th style="text-align:center">Prioridad</th>
                <th></th>
              </tr></thead>
              <tbody>
                <?php
                usort($_tec_REV, function($a, $b) {
                    return strtotime(isset($a["fecha_ingreso"]) ? $a["fecha_ingreso"] : "now")
                         - strtotime(isset($b["fecha_ingreso"]) ? $b["fecha_ingreso"] : "now");
                });
                foreach (array_slice($_tec_REV, 0, 10) as $o):
                  $img = _tecGetImg($o);
                  $dias = _tecDias(isset($o["fecha_ingreso"]) ? $o["fecha_ingreso"] : "");
                  $marca = isset($o["marcaDelEquipo"]) ? $o["marcaDelEquipo"] : "";
                  $modelo = isset($o["modeloDelEquipo"]) ? $o["modeloDelEquipo"] : "";
                  $equipo = trim($marca . " " . $modelo);
                  if (empty($equipo)) $equipo = "Sin datos";

                  if ($dias >= 10)     { $uc='#ef4444'; $ul='Urgente'; }
                  elseif ($dias >= 5)  { $uc='#f59e0b'; $ul='Alto'; }
                  elseif ($dias >= 2)  { $uc='#3b82f6'; $ul='Normal'; }
                  else                 { $uc='#22c55e'; $ul='Nuevo'; }
                ?>
                <tr>
                  <td style="padding:6px 4px;width:44px">
                    <?php if (!empty($img)): ?>
                      <?php echo _tecImgTag($img, 40, 40, 8); ?>
                    <?php else: ?>
                      <?php echo _tecImgPlaceholder(40, 40, 8); ?>
                    <?php endif; ?>
                  </td>
                  <td><span style="font-weight:700;color:#6366f1">#<?php echo $o["id"]; ?></span>
                    <div style="font-size:11px;color:#94a3b8"><?php echo !empty($o["fecha_ingreso"]) ? date("d/m/Y", strtotime($o["fecha_ingreso"])) : ""; ?></div>
                  </td>
                  <td>
                    <div style="font-weight:600;font-size:13px"><?php echo htmlspecialchars($equipo); ?></div>
                    <?php if (!empty($o["numeroDeSerieDelEquipo"])): ?>
                      <div style="font-size:10px;color:#94a3b8">S/N: <?php echo htmlspecialchars($o["numeroDeSerieDelEquipo"]); ?></div>
                    <?php endif; ?>
                  </td>
                  <td style="text-align:center"><span style="font-weight:700;color:<?php echo $uc; ?>"><?php echo $dias; ?>d</span></td>
                  <td style="text-align:center">
                    <span style="display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;color:<?php echo $uc; ?>">
                      <span style="width:8px;height:8px;border-radius:50%;background:<?php echo $uc; ?>;display:inline-block"></span>
                      <?php echo $ul; ?>
                    </span>
                  </td>
                  <td style="text-align:center">
                    <a href="<?php echo _tecLink($o); ?>" target="_blank"
                       style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:8px;background:#6366f1;color:#fff;font-size:12px;text-decoration:none;transition:background .15s"
                       onmouseover="this.style.background='#4f46e5'" onmouseout="this.style.background='#6366f1'">
                      <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </a>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <?php if (count($_tec_REV) > 10): ?>
            <div style="text-align:center;padding:12px;border-top:1px solid #f1f5f9">
              <a href="index.php?ruta=ordenes" style="color:#6366f1;font-size:12px;font-weight:600;text-decoration:none">
                Ver las <?php echo count($_tec_REV); ?> en revisión <i class="fa-solid fa-arrow-right" style="font-size:10px"></i>
              </a>
            </div>
          <?php endif; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Acciones rápidas técnico -->
  <div class="col-lg-4 col-md-5 col-xs-12">
    <div class="crm-card" style="margin-bottom:20px">
      <div class="crm-card-head">
        <h4 class="crm-card-title"><i class="fa-solid fa-rocket"></i> Acciones Rápidas</h4>
      </div>
      <div class="crm-card-body" style="padding:16px 18px">
        <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:16px">
          <a href="index.php?ruta=ordenes" class="crm-quick">
            <div class="crm-quick-icon" style="background:linear-gradient(135deg,#ef4444,#f87171)">
              <i class="fa-solid fa-clipboard-list"></i>
            </div>
            <div class="crm-quick-text">Mis Órdenes<small>Ver todas mis asignaciones</small></div>
          </a>
          <a href="index.php?ruta=ordenesnew" class="crm-quick">
            <div class="crm-quick-icon" style="background:linear-gradient(135deg,#3b82f6,#60a5fa)">
              <i class="fa-solid fa-list-check"></i>
            </div>
            <div class="crm-quick-text">Todas las Órdenes<small>Vista general del taller</small></div>
          </a>
          <a href="index.php?ruta=comisiones" class="crm-quick">
            <div class="crm-quick-icon" style="background:linear-gradient(135deg,#22c55e,#4ade80)">
              <i class="fa-solid fa-coins"></i>
            </div>
            <div class="crm-quick-text">Mis Comisiones<small>Revisar pagos y bonos</small></div>
          </a>
        </div>

        <!-- Resumen rápido -->
        <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:14px">
          <div style="font-size:12px;font-weight:700;color:#0f172a;margin-bottom:10px">
            <i class="fa-solid fa-chart-simple" style="color:#6366f1;margin-right:4px"></i> Resumen de Carga
          </div>
          <div style="display:flex;flex-direction:column;gap:8px">
            <div style="display:flex;align-items:center;justify-content:space-between">
              <span style="font-size:12px;color:#64748b;display:flex;align-items:center;gap:6px">
                <span style="width:8px;height:8px;border-radius:50%;background:#ef4444;display:inline-block"></span> Revisión
              </span>
              <span style="font-size:13px;font-weight:700;color:#0f172a"><?php echo count($_tec_REV); ?></span>
            </div>
            <div style="display:flex;align-items:center;justify-content:space-between">
              <span style="font-size:12px;color:#64748b;display:flex;align-items:center;gap:6px">
                <span style="width:8px;height:8px;border-radius:50%;background:#3b82f6;display:inline-block"></span> Aceptadas
              </span>
              <span style="font-size:13px;font-weight:700;color:#0f172a"><?php echo count($_tec_OK); ?></span>
            </div>
            <div style="display:flex;align-items:center;justify-content:space-between">
              <span style="font-size:12px;color:#64748b;display:flex;align-items:center;gap:6px">
                <span style="width:8px;height:8px;border-radius:50%;background:#8b5cf6;display:inline-block"></span> Pend. Autorización
              </span>
              <span style="font-size:13px;font-weight:700;color:#0f172a"><?php echo count($_tec_AUT); ?></span>
            </div>
            <div style="display:flex;align-items:center;justify-content:space-between">
              <span style="font-size:12px;color:#64748b;display:flex;align-items:center;gap:6px">
                <span style="width:8px;height:8px;border-radius:50%;background:#f59e0b;display:inline-block"></span> Terminadas
              </span>
              <span style="font-size:13px;font-weight:700;color:#0f172a"><?php echo count($_tec_TER); ?></span>
            </div>
            <hr style="border:none;border-top:1px solid #e2e8f0;margin:4px 0">
            <div style="display:flex;align-items:center;justify-content:space-between">
              <span style="font-size:12px;font-weight:700;color:#0f172a">Total activas</span>
              <span style="font-size:15px;font-weight:800;color:#6366f1"><?php echo $_tec_totalActivas; ?></span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════
     ÓRDENES ACEPTADAS (en proceso)
══════════════════════════════════════════ -->
<div class="crm-section">
  <div class="crm-section-icon" style="background:linear-gradient(135deg,#3b82f6,#06b6d4)">
    <i class="fa-solid fa-gears"></i>
  </div>
  <div>
    <h3>En Proceso de Reparación</h3>
    <p>Órdenes aceptadas que estás trabajando actualmente</p>
  </div>
</div>

<div class="crm-card" style="margin-bottom:20px">
  <div class="crm-card-head">
    <h4 class="crm-card-title"><i class="fa-solid fa-gears"></i> Aceptadas</h4>
    <span class="crm-badge" style="background:#dbeafe;color:#1d4ed8"><?php echo count($_tec_OK); ?> en proceso</span>
  </div>
  <div class="crm-card-body-flush">
    <?php if (empty($_tec_OK)): ?>
      <div class="crm-empty">
        <i class="fa-solid fa-circle-check" style="color:#22c55e;opacity:.6"></i>
        <strong>Sin órdenes en proceso</strong>
      </div>
    <?php else: ?>
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:12px;padding:16px">
        <?php
        usort($_tec_OK, function($a, $b) {
            return strtotime(isset($a["fecha_ingreso"]) ? $a["fecha_ingreso"] : "now")
                 - strtotime(isset($b["fecha_ingreso"]) ? $b["fecha_ingreso"] : "now");
        });
        foreach (array_slice($_tec_OK, 0, 12) as $o):
          $img = _tecGetImg($o);
          $dias = _tecDias(isset($o["fecha_ingreso"]) ? $o["fecha_ingreso"] : "");
          $marca = isset($o["marcaDelEquipo"]) ? $o["marcaDelEquipo"] : "";
          $modelo = isset($o["modeloDelEquipo"]) ? $o["modeloDelEquipo"] : "";
          $equipo = trim($marca . " " . $modelo);
          if (empty($equipo)) $equipo = "Sin datos";
          $titulo = isset($o["titulo"]) ? $o["titulo"] : "";

          if ($dias >= 10)     $borderColor = '#ef4444';
          elseif ($dias >= 5)  $borderColor = '#f59e0b';
          else                 $borderColor = '#3b82f6';
        ?>
          <a href="<?php echo _tecLink($o); ?>" target="_blank" style="text-decoration:none;display:flex;gap:12px;padding:14px;border-radius:12px;border:1px solid #e2e8f0;border-left:4px solid <?php echo $borderColor; ?>;background:#fff;transition:box-shadow .15s,transform .15s"
             onmouseover="this.style.boxShadow='0 4px 16px rgba(0,0,0,.08)';this.style.transform='translateY(-1px)'"
             onmouseout="this.style.boxShadow='none';this.style.transform='none'">
            <?php if (!empty($img)): ?>
              <?php echo _tecImgTag($img, 52, 52, 8); ?>
            <?php else: ?>
              <?php echo _tecImgPlaceholder(52, 52, 8); ?>
            <?php endif; ?>
            <div style="flex:1;min-width:0">
              <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px">
                <span style="font-weight:700;color:#6366f1;font-size:13px">#<?php echo $o["id"]; ?></span>
                <span style="font-size:11px;font-weight:700;color:<?php echo $borderColor; ?>"><?php echo $dias; ?>d</span>
              </div>
              <div style="font-size:12px;font-weight:600;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?php echo htmlspecialchars($equipo); ?></div>
              <?php if (!empty($titulo)): ?>
                <div style="font-size:11px;color:#94a3b8;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?php echo htmlspecialchars(mb_substr($titulo, 0, 40)); ?></div>
              <?php endif; ?>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
      <?php if (count($_tec_OK) > 12): ?>
        <div style="text-align:center;padding:12px;border-top:1px solid #f1f5f9">
          <a href="index.php?ruta=ordenes" style="color:#6366f1;font-size:12px;font-weight:600;text-decoration:none">
            Ver las <?php echo count($_tec_OK); ?> aceptadas <i class="fa-solid fa-arrow-right" style="font-size:10px"></i>
          </a>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</div>

<!-- Cotizaciones removidas del panel técnico -->

<!-- ══════════════════════════════════════════
     TERMINADAS + ENTREGADAS (resumen)
══════════════════════════════════════════ -->
<div class="crm-section">
  <div class="crm-section-icon" style="background:linear-gradient(135deg,#22c55e,#06b6d4)">
    <i class="fa-solid fa-flag-checkered"></i>
  </div>
  <div>
    <h3>Completadas</h3>
    <p>Órdenes terminadas pendientes de entrega y entregadas recientes</p>
  </div>
</div>

<div class="row dash-row-equal">
  <!-- Terminadas -->
  <div class="col-lg-6 col-md-6 col-xs-12">
    <div class="crm-card" style="margin-bottom:20px">
      <div class="crm-card-head">
        <h4 class="crm-card-title"><i class="fa-solid fa-flag-checkered"></i> Terminadas</h4>
        <span class="crm-badge" style="background:#fef3c7;color:#92400e"><?php echo count($_tec_TER); ?> listas</span>
      </div>
      <div class="crm-card-body-flush" style="max-height:320px;overflow-y:auto">
        <?php if (empty($_tec_TER)): ?>
          <div class="crm-empty" style="padding:24px">
            <i class="fa-solid fa-inbox" style="font-size:24px"></i>
            <strong>Sin terminadas</strong>
          </div>
        <?php else: ?>
          <?php foreach (array_slice($_tec_TER, 0, 8) as $o):
            $img = _tecGetImg($o);
            $marca = isset($o["marcaDelEquipo"]) ? $o["marcaDelEquipo"] : "";
            $modelo = isset($o["modeloDelEquipo"]) ? $o["modeloDelEquipo"] : "";
            $equipo = trim($marca . " " . $modelo);
            if (empty($equipo)) $equipo = "Sin datos";
            $total = floatval(isset($o["total"]) ? $o["total"] : 0);
          ?>
            <a href="<?php echo _tecLink($o); ?>" target="_blank" style="display:flex;align-items:center;gap:12px;padding:10px 16px;border-bottom:1px solid #f8fafc;text-decoration:none;transition:background .12s"
               onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
              <?php if (!empty($img)): ?>
                <?php echo _tecImgTag($img, 36, 36, 6); ?>
              <?php else: ?>
                <?php echo _tecImgPlaceholder(36, 36, 6); ?>
              <?php endif; ?>
              <div style="flex:1;min-width:0">
                <div style="font-size:13px;font-weight:700;color:#6366f1">#<?php echo $o["id"]; ?> <span style="font-weight:500;color:#0f172a"><?php echo htmlspecialchars($equipo); ?></span></div>
                <div style="font-size:11px;color:#94a3b8"><?php echo !empty($o["fecha_ingreso"]) ? date("d/m/Y", strtotime($o["fecha_ingreso"])) : ""; ?></div>
              </div>
              <div style="font-weight:700;font-size:12px;color:#0f172a;flex-shrink:0">$<?php echo number_format($total, 0); ?></div>
            </a>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Entregadas recientes -->
  <div class="col-lg-6 col-md-6 col-xs-12">
    <div class="crm-card" style="margin-bottom:20px">
      <div class="crm-card-head">
        <h4 class="crm-card-title"><i class="fa-solid fa-handshake"></i> Entregadas Recientes</h4>
        <span class="crm-badge" style="background:#f0fdf4;color:#16a34a"><?php echo count($_tec_ENT_mes); ?> este mes</span>
      </div>
      <div class="crm-card-body-flush" style="max-height:320px;overflow-y:auto">
        <?php
        $entRecientes = array_slice($_tec_ENT, 0, 8);
        if (empty($entRecientes)): ?>
          <div class="crm-empty" style="padding:24px">
            <i class="fa-solid fa-inbox" style="font-size:24px"></i>
            <strong>Sin entregas recientes</strong>
          </div>
        <?php else: ?>
          <?php foreach ($entRecientes as $o):
            $img = _tecGetImg($o);
            $marca = isset($o["marcaDelEquipo"]) ? $o["marcaDelEquipo"] : "";
            $modelo = isset($o["modeloDelEquipo"]) ? $o["modeloDelEquipo"] : "";
            $equipo = trim($marca . " " . $modelo);
            if (empty($equipo)) $equipo = "Sin datos";
            $total = floatval(isset($o["total"]) ? $o["total"] : 0);
          ?>
            <a href="<?php echo _tecLink($o); ?>" target="_blank" style="display:flex;align-items:center;gap:12px;padding:10px 16px;border-bottom:1px solid #f8fafc;text-decoration:none;transition:background .12s"
               onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
              <?php if (!empty($img)): ?>
                <?php echo _tecImgTag($img, 36, 36, 6); ?>
              <?php else: ?>
                <?php echo _tecImgPlaceholder(36, 36, 6); ?>
              <?php endif; ?>
              <div style="flex:1;min-width:0">
                <div style="font-size:13px;font-weight:700;color:#22c55e">#<?php echo $o["id"]; ?> <span style="font-weight:500;color:#0f172a"><?php echo htmlspecialchars($equipo); ?></span></div>
                <div style="font-size:11px;color:#94a3b8"><?php echo !empty($o["fecha_ingreso"]) ? date("d/m/Y", strtotime($o["fecha_ingreso"])) : ""; ?></div>
              </div>
              <div style="flex-shrink:0">
                <span style="display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:600;color:#16a34a;background:#f0fdf4;padding:3px 8px;border-radius:12px">
                  <i class="fa-solid fa-check" style="font-size:9px"></i> Entregada
                </span>
              </div>
            </a>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php
/* ══ Actividad Reciente — solo órdenes del técnico ══ */
$_adm_allOrders      = $_tec_allOrders;
$_act_filtro_tecnico = $_tec_id;
include __DIR__ . "/admin-actividad-estado.php";
unset($_act_filtro_tecnico, $_act_filtroTec, $_act_filtroAse, $_act_usaFiltro);
?>
