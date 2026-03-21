<?php
/* ═══════════════════════════════════════════════════
   TODAS LAS NOTIFICACIONES — Vista dedicada
   ═══════════════════════════════════════════════════ */

date_default_timezone_set("America/Mexico_City");

$_tn_perfil   = $_SESSION["perfil"];
$_tn_empresa  = isset($_SESSION["empresa"]) ? intval($_SESSION["empresa"]) : 0;
$_tn_idRol    = null;

// ── Determinar ID de rol según perfil ──
if ($_tn_perfil === "vendedor") {
    $Asesores = Controladorasesores::ctrMostrarAsesoresEleg("correo", $_SESSION["email"]);
    if (is_array($Asesores) && isset($Asesores["id"])) {
        $_tn_idRol = intval($Asesores["id"]);
    }
} elseif ($_tn_perfil === "tecnico") {
    $tecnico = ControladorTecnicos::ctrMostrarTecnicos("correo", $_SESSION["email"]);
    if (is_array($tecnico) && isset($tecnico["id"])) {
        $_tn_idRol = intval($tecnico["id"]);
    }
}

// ── Página actual ──
$_tn_pag = isset($_GET["pg"]) ? max(1, intval($_GET["pg"])) : 1;
$_tn_porPagina = 30;
$_tn_offset = ($_tn_pag - 1) * $_tn_porPagina;

// ── Obtener notificaciones de estado/traspaso ──
try {
    ControladorNotificaciones::ctrCrearTablaEstado();
    $_tn_notifs = ControladorNotificaciones::ctrTodasNotificaciones(
        $_tn_perfil, $_tn_empresa, $_tn_idRol,
        $_tn_porPagina + 1,
        $_tn_offset
    );
} catch (Exception $e) { $_tn_notifs = array(); }

if (!is_array($_tn_notifs)) $_tn_notifs = array();
$_tn_hayMas = count($_tn_notifs) > $_tn_porPagina;
if ($_tn_hayMas) array_pop($_tn_notifs);

// ── Obtener observaciones de hoy ──
$_tn_obsHoy = array();
try {
    $_tn_obsHoy = controladorObservaciones::ctrObservacionesRecientesNotif(
        isset($_SESSION["id"]) ? intval($_SESSION["id"]) : 0, 20
    );
    if (!is_array($_tn_obsHoy)) $_tn_obsHoy = array();
} catch (Exception $e) {}

// ── Obtener órdenes con atraso de entrega (misma lógica del bell dropdown) ──
$_tn_atraso = array();
$_tn_limite6m = date("Y-m-d", strtotime("-6 months"));
try {
    $_tn_rawOrdenes = array();
    if ($_tn_perfil === "administrador") {
        $_tn_rawOrdenes = controladorOrdenes::ctrMostrarOrdenes("id_empresa", $_SESSION["empresa"]);
    } elseif ($_tn_perfil === "vendedor" && isset($Asesores) && is_array($Asesores) && isset($Asesores["id"])) {
        $_tn_rawOrdenes = controladorOrdenes::ctrMostrarOrdenesDelAsesor($Asesores["id"]);
    } elseif ($_tn_perfil === "tecnico" && isset($tecnico) && is_array($tecnico) && isset($tecnico["id"])) {
        $_tn_rawOrdenes = controladorOrdenes::ctrMostrarOrdenesDelTecncio($tecnico["id"]);
    }
    if (is_array($_tn_rawOrdenes)) {
        foreach ($_tn_rawOrdenes as $o) {
            $est = isset($o["estado"]) ? $o["estado"] : "";
            $fi  = isset($o["fecha_ingreso"]) ? $o["fecha_ingreso"] : "";
            if (strpos($est, "Ent") === false && strpos($est, "can") === false
                && stripos($est, "sin reparación") === false && strpos($est, "SR") === false
                && stripos($est, "producto para venta") === false && strpos($est, "PV") === false
                && $fi >= $_tn_limite6m) {
                if (strtotime($fi . "+ 5 days") <= time()) {
                    $_tn_atraso[] = $o;
                }
            }
        }
    }
    usort($_tn_atraso, function($a, $b) {
        return strtotime(isset($b["fecha_ingreso"]) ? $b["fecha_ingreso"] : "now")
             - strtotime(isset($a["fecha_ingreso"]) ? $a["fecha_ingreso"] : "now");
    });
} catch (Exception $e) {}

// ── Helpers ──
if (!function_exists('_tnEstadoInfo')) {
    function _tnEstadoInfo($estado) {
        $e = strtolower($estado);
        if (strpos($e, 'entregado') !== false || strpos($e, 'ent') !== false) return array('#22c55e', '#f0fdf4', 'fa-handshake', 'Entregado');
        if (strpos($e, 'terminada') !== false || strpos($e, 'ter') !== false) return array('#06b6d4', '#ecfeff', 'fa-flag-checkered', 'Terminada');
        if (strpos($e, 'aceptado') !== false || strpos($e, 'ok') !== false)   return array('#3b82f6', '#eff6ff', 'fa-circle-check', 'Aceptado');
        if (strpos($e, 'revisión') !== false || strpos($e, 'rev') !== false)  return array('#ef4444', '#fef2f2', 'fa-magnifying-glass', 'Revisión');
        if (strpos($e, 'autorización') !== false || strpos($e, 'aut') !== false) return array('#f59e0b', '#fffbeb', 'fa-hourglass-half', 'Autorización');
        if (strpos($e, 'supervisión') !== false || strpos($e, 'sup') !== false)  return array('#8b5cf6', '#f5f3ff', 'fa-eye', 'Supervisión');
        if (strpos($e, 'garantía') !== false || strpos($e, 'garantia') !== false) return array('#dc2626', '#fef2f2', 'fa-rotate-left', 'Garantía');
        return array('#64748b', '#f1f5f9', 'fa-circle-info', $estado);
    }
}
if (!function_exists('_tnTiempoRel')) {
    function _tnTiempoRel($fecha) {
        $diff = time() - strtotime($fecha);
        if ($diff < 60) return 'Justo ahora';
        if ($diff < 3600) return 'Hace ' . floor($diff / 60) . ' min';
        if ($diff < 86400) return 'Hace ' . floor($diff / 3600) . 'h';
        if ($diff < 172800) return 'Ayer ' . date('H:i', strtotime($fecha));
        return date('d/m/Y H:i', strtotime($fecha));
    }
}

// Contadores
$_tn_countEstado = 0;
$_tn_countTraspaso = 0;
foreach ($_tn_notifs as $n) {
    $t = isset($n['tipo']) ? $n['tipo'] : 'estado';
    if ($t === 'traspaso') $_tn_countTraspaso++;
    else $_tn_countEstado++;
}
$_tn_countAtraso = count($_tn_atraso);
?>

<div class="content-wrapper" style="min-height:100vh">

  <section class="content-header">
    <h1 style="font-weight:800;display:flex;align-items:center;gap:10px">
      <i class="fa-solid fa-bell" style="color:#6366f1"></i>
      Notificaciones
      <small style="font-weight:400;color:#94a3b8">Historial completo</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="index.php?ruta=inicio"><i class="fa-solid fa-house"></i> Inicio</a></li>
      <li class="active">Notificaciones</li>
    </ol>
  </section>

  <section class="content" style="padding:15px 15px 30px">

    <style>
    .tn-stats {
        display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap;
    }
    .tn-stat {
        flex: 1; min-width: 130px;
        background: #fff; border: 1px solid #e2e8f0; border-radius: 12px;
        padding: 14px 16px; display: flex; align-items: center; gap: 12px;
    }
    .tn-stat-icon {
        width: 38px; height: 38px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .tn-stat-icon i { font-size: 14px; }
    .tn-stat-num { font-size: 20px; font-weight: 700; color: #0f172a; }
    .tn-stat-label { font-size: 11px; color: #94a3b8; }

    .tn-tabs {
        display: flex; gap: 4px; margin-bottom: 20px;
        background: #f1f5f9; border-radius: 10px; padding: 4px;
    }
    .tn-tab {
        flex: 1; text-align: center; padding: 8px 12px; border-radius: 8px;
        font-size: 12px; font-weight: 600; cursor: pointer; color: #64748b;
        border: none; background: none; transition: all .2s;
    }
    .tn-tab.active {
        background: #fff; color: #0f172a; box-shadow: 0 1px 3px rgba(0,0,0,.08);
    }
    .tn-tab:hover:not(.active) { color: #0f172a; }

    .tn-list { list-style: none; padding: 0; margin: 0; }
    .tn-item {
        background: #fff; border: 1px solid #e2e8f0; border-radius: 12px;
        margin-bottom: 8px; padding: 14px 16px;
        display: flex; align-items: flex-start; gap: 14px;
        transition: box-shadow .15s, border-color .15s;
    }
    .tn-item:hover { box-shadow: 0 2px 8px rgba(0,0,0,.06); border-color: #cbd5e1; }
    .tn-item-icon {
        width: 40px; height: 40px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; margin-top: 2px;
    }
    .tn-item-icon i { font-size: 14px; }
    .tn-item-body { flex: 1; min-width: 0; }
    .tn-item-title { font-size: 13px; color: #0f172a; line-height: 1.5; }
    .tn-item-meta {
        font-size: 11px; color: #94a3b8; margin-top: 4px;
        display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
    }
    .tn-badge-tipo {
        font-size: 10px; font-weight: 600; padding: 2px 8px;
        border-radius: 6px; display: inline-flex; align-items: center; gap: 4px;
    }
    .tn-date-sep {
        font-size: 11px; font-weight: 700; color: #94a3b8;
        text-transform: uppercase; letter-spacing: .5px;
        padding: 10px 0 6px; border-bottom: 1px solid #f1f5f9; margin-bottom: 8px;
    }
    .tn-pag {
        display: flex; justify-content: center; gap: 8px; margin-top: 20px;
    }
    .tn-pag a {
        padding: 8px 16px; border: 1px solid #e2e8f0; border-radius: 8px;
        font-size: 12px; font-weight: 600; color: #6366f1; text-decoration: none;
        transition: all .15s;
    }
    .tn-pag a:hover { background: #eef2ff; border-color: #c7d2fe; }
    .tn-pag span { padding: 8px 16px; font-size: 12px; color: #94a3b8; }
    .tn-empty {
        text-align: center; padding: 60px 20px; color: #94a3b8;
    }
    .tn-empty i { font-size: 40px; margin-bottom: 12px; display: block; color: #cbd5e1; }
    .tn-empty p { font-size: 14px; }
    </style>

    <!-- ══ Stats ══ -->
    <div class="tn-stats">
      <div class="tn-stat">
        <div class="tn-stat-icon" style="background:#eff6ff">
          <i class="fa-solid fa-arrow-right-arrow-left" style="color:#3b82f6"></i>
        </div>
        <div>
          <div class="tn-stat-num"><?php echo $_tn_countEstado; ?></div>
          <div class="tn-stat-label">Cambios de estado</div>
        </div>
      </div>
      <div class="tn-stat">
        <div class="tn-stat-icon" style="background:#f5f3ff">
          <i class="fa-solid fa-people-arrows" style="color:#8b5cf6"></i>
        </div>
        <div>
          <div class="tn-stat-num"><?php echo $_tn_countTraspaso; ?></div>
          <div class="tn-stat-label">Traspasos</div>
        </div>
      </div>
      <div class="tn-stat">
        <div class="tn-stat-icon" style="background:#fef2f2">
          <i class="fa-solid fa-clock-rotate-left" style="color:#ef4444"></i>
        </div>
        <div>
          <div class="tn-stat-num"><?php echo $_tn_countAtraso; ?></div>
          <div class="tn-stat-label">Atrasos de entrega</div>
        </div>
      </div>
      <div class="tn-stat">
        <div class="tn-stat-icon" style="background:#fffbeb">
          <i class="fa-solid fa-comment-dots" style="color:#f59e0b"></i>
        </div>
        <div>
          <div class="tn-stat-num"><?php echo count($_tn_obsHoy); ?></div>
          <div class="tn-stat-label">Observaciones hoy</div>
        </div>
      </div>
    </div>

    <!-- ══ Tabs ══ -->
    <div class="tn-tabs">
      <button class="tn-tab active" data-tab="todas">Todas</button>
      <button class="tn-tab" data-tab="estado">Estados</button>
      <button class="tn-tab" data-tab="traspaso">Traspasos</button>
      <button class="tn-tab" data-tab="atraso">Atrasos</button>
      <button class="tn-tab" data-tab="observaciones">Observaciones</button>
    </div>

    <!-- ══ Lista principal (estado + traspaso) ══ -->
    <ul class="tn-list tn-panel" id="tnListEstado">
    <?php
      $lastDate = '';
      foreach ($_tn_notifs as $n):
        $nTipo  = isset($n['tipo']) ? $n['tipo'] : 'estado';
        $nFecha = $n['fecha'];
        $nDate  = date('Y-m-d', strtotime($nFecha));
        $nDateLabel = ($nDate === date('Y-m-d')) ? 'Hoy' : (($nDate === date('Y-m-d', strtotime('-1 day'))) ? 'Ayer' : date('d \d\e F, Y', strtotime($nFecha)));

        if ($nDate !== $lastDate):
          $lastDate = $nDate;
    ?>
      <li class="tn-date-sep" data-ntype="<?php echo $nTipo; ?>"><?php echo $nDateLabel; ?></li>
    <?php endif; ?>

    <?php if ($nTipo === 'traspaso'): ?>
      <li class="tn-item" data-ntype="traspaso">
        <div class="tn-item-icon" style="background:#f5f3ff">
          <i class="fa-solid fa-people-arrows" style="color:#8b5cf6"></i>
        </div>
        <div class="tn-item-body">
          <div class="tn-item-title">
            <strong>#<?php echo htmlspecialchars($n['id_orden']); ?></strong>
            — Traspaso de equipo:
            <?php echo htmlspecialchars($n['estado_anterior']); ?>
            <i class="fa-solid fa-arrow-right" style="font-size:10px;color:#8b5cf6;margin:0 3px"></i>
            <strong style="color:#8b5cf6"><?php echo htmlspecialchars($n['estado_nuevo']); ?></strong>
          </div>
          <?php if (!empty($n['titulo_orden'])): ?>
            <div style="font-size:12px;color:#64748b;margin-top:2px"><?php echo htmlspecialchars($n['titulo_orden']); ?></div>
          <?php endif; ?>
          <div class="tn-item-meta">
            <span class="tn-badge-tipo" style="background:#f5f3ff;color:#8b5cf6">
              <i class="fa-solid fa-people-arrows" style="font-size:8px"></i> Traspaso
            </span>
            <span><i class="fa-solid fa-user" style="font-size:8px;margin-right:3px"></i><?php echo htmlspecialchars($n['nombre_usuario']); ?></span>
            <span><i class="fa-regular fa-clock" style="font-size:8px;margin-right:3px"></i><?php echo _tnTiempoRel($nFecha); ?></span>
          </div>
        </div>
      </li>

    <?php else:
          $ei = _tnEstadoInfo($n['estado_nuevo']);
    ?>
      <li class="tn-item" data-ntype="estado">
        <div class="tn-item-icon" style="background:<?php echo $ei[1]; ?>">
          <i class="fa-solid <?php echo $ei[2]; ?>" style="color:<?php echo $ei[0]; ?>"></i>
        </div>
        <div class="tn-item-body">
          <div class="tn-item-title">
            <strong>#<?php echo htmlspecialchars($n['id_orden']); ?></strong>
            cambió de
            <span style="color:#94a3b8"><?php echo htmlspecialchars($n['estado_anterior']); ?></span>
            a
            <strong style="color:<?php echo $ei[0]; ?>"><?php echo htmlspecialchars($n['estado_nuevo']); ?></strong>
          </div>
          <?php if (!empty($n['titulo_orden'])): ?>
            <div style="font-size:12px;color:#64748b;margin-top:2px"><?php echo htmlspecialchars($n['titulo_orden']); ?></div>
          <?php endif; ?>
          <div class="tn-item-meta">
            <span class="tn-badge-tipo" style="background:<?php echo $ei[1]; ?>;color:<?php echo $ei[0]; ?>">
              <i class="fa-solid <?php echo $ei[2]; ?>" style="font-size:8px"></i> <?php echo $ei[3]; ?>
            </span>
            <span><i class="fa-solid fa-user" style="font-size:8px;margin-right:3px"></i><?php echo htmlspecialchars($n['nombre_usuario']); ?></span>
            <span><i class="fa-regular fa-clock" style="font-size:8px;margin-right:3px"></i><?php echo _tnTiempoRel($nFecha); ?></span>
          </div>
        </div>
      </li>
    <?php endif; endforeach; ?>

    <?php if (empty($_tn_notifs)): ?>
      <div class="tn-empty" data-ntype="estado">
        <i class="fa-solid fa-check-circle"></i>
        <p>No hay notificaciones de estado o traspaso</p>
      </div>
    <?php endif; ?>
    </ul>

    <!-- ══ Lista de atrasos ══ -->
    <ul class="tn-list tn-panel" id="tnListAtraso" style="display:none">
    <?php if (empty($_tn_atraso)): ?>
      <div class="tn-empty">
        <i class="fa-solid fa-check-circle" style="color:#22c55e"></i>
        <p>No hay órdenes con atraso de entrega</p>
      </div>
    <?php else:
      foreach ($_tn_atraso as $oa):
        $oaDias = max(0, floor((time() - strtotime($oa["fecha_ingreso"])) / 86400));
        $oaColor = $oaDias >= 30 ? '#ef4444' : ($oaDias >= 15 ? '#f59e0b' : '#3b82f6');
        $oaEstado = isset($oa["estado"]) ? $oa["estado"] : "";
        $oaTitulo = isset($oa["titulo"]) ? $oa["titulo"] : "";
    ?>
      <li class="tn-item" data-ntype="atraso">
        <div class="tn-item-icon" style="background:<?php echo $oaDias >= 30 ? '#fef2f2' : ($oaDias >= 15 ? '#fffbeb' : '#eff6ff'); ?>">
          <i class="fa-solid fa-clock-rotate-left" style="color:<?php echo $oaColor; ?>"></i>
        </div>
        <div class="tn-item-body">
          <div class="tn-item-title">
            <strong>#<?php echo $oa["id"]; ?></strong>
            — Atraso de entrega:
            <strong style="color:<?php echo $oaColor; ?>"><?php echo $oaDias; ?> días</strong>
          </div>
          <?php if (!empty($oaTitulo)): ?>
            <div style="font-size:12px;color:#64748b;margin-top:2px"><?php echo htmlspecialchars($oaTitulo); ?></div>
          <?php endif; ?>
          <div class="tn-item-meta">
            <span class="tn-badge-tipo" style="background:<?php echo $oaDias >= 30 ? '#fef2f2' : ($oaDias >= 15 ? '#fffbeb' : '#eff6ff'); ?>;color:<?php echo $oaColor; ?>">
              <i class="fa-solid fa-clock-rotate-left" style="font-size:8px"></i>
              <?php echo $oaDias >= 30 ? 'Crítico' : ($oaDias >= 15 ? 'Urgente' : 'Atención'); ?>
            </span>
            <?php if (!empty($oaEstado)): ?>
              <span style="color:#64748b"><?php echo htmlspecialchars($oaEstado); ?></span>
            <?php endif; ?>
            <span><i class="fa-regular fa-calendar" style="font-size:8px;margin-right:3px"></i>Ingreso: <?php echo date("d/m/Y", strtotime($oa["fecha_ingreso"])); ?></span>
            <a href="index.php?ruta=ordenesnew&idOrden=<?php echo $oa["id"]; ?>&cliente=<?php echo isset($oa["id_usuario"]) ? $oa["id_usuario"] : ''; ?>&tecnico=<?php echo isset($oa["id_tecnico"]) ? $oa["id_tecnico"] : ''; ?>&asesor=<?php echo isset($oa["id_Asesor"]) ? $oa["id_Asesor"] : ''; ?>&empresa=<?php echo isset($oa["id_empresa"]) ? $oa["id_empresa"] : ''; ?>&pedido=<?php echo isset($oa["id_pedido"]) ? $oa["id_pedido"] : ''; ?>&tecnicodos=<?php echo isset($oa["id_tecnicoDos"]) ? $oa["id_tecnicoDos"] : ''; ?>"
               style="color:#6366f1;font-weight:600;text-decoration:none;margin-left:auto">
              <i class="fa-solid fa-eye" style="font-size:9px"></i> Ver orden
            </a>
          </div>
        </div>
      </li>
    <?php endforeach; endif; ?>
    </ul>

    <!-- ══ Lista de observaciones ══ -->
    <ul class="tn-list tn-panel" id="tnListObs" style="display:none">
    <?php if (empty($_tn_obsHoy)): ?>
      <div class="tn-empty">
        <i class="fa-solid fa-comment-dots"></i>
        <p>No hay observaciones nuevas hoy</p>
      </div>
    <?php else:
      foreach ($_tn_obsHoy as $ob):
        $obTexto = mb_strlen($ob['observacion']) > 120 ? mb_substr($ob['observacion'], 0, 120) . '…' : $ob['observacion'];
        $obCreador = isset($ob['creador_nombre']) ? $ob['creador_nombre'] : 'Usuario';
        $obPerfil  = isset($ob['creador_perfil']) ? $ob['creador_perfil'] : '';
    ?>
      <li class="tn-item" data-ntype="observaciones">
        <div class="tn-item-icon" style="background:#fffbeb">
          <i class="fa-solid fa-comment-dots" style="color:#f59e0b"></i>
        </div>
        <div class="tn-item-body">
          <div class="tn-item-title">
            <strong style="color:#f59e0b">#<?php echo htmlspecialchars($ob['id_orden']); ?></strong>
            — <?php echo htmlspecialchars($obTexto); ?>
          </div>
          <div class="tn-item-meta">
            <span class="tn-badge-tipo" style="background:#fffbeb;color:#f59e0b">
              <i class="fa-solid fa-comment-dots" style="font-size:8px"></i> Observación
            </span>
            <span><i class="fa-solid fa-user" style="font-size:8px;margin-right:3px"></i><?php echo htmlspecialchars($obCreador); ?></span>
            <?php if (!empty($obPerfil)): ?>
              <span style="text-transform:capitalize"><?php echo htmlspecialchars($obPerfil); ?></span>
            <?php endif; ?>
            <span><i class="fa-regular fa-clock" style="font-size:8px;margin-right:3px"></i><?php echo _tnTiempoRel($ob['fecha']); ?></span>
            <a href="index.php?ruta=ordenesnew&idOrden=<?php echo $ob['id_orden']; ?>"
               style="color:#6366f1;font-weight:600;text-decoration:none;margin-left:auto">
              <i class="fa-solid fa-eye" style="font-size:9px"></i> Ver orden
            </a>
          </div>
        </div>
      </li>
    <?php endforeach; endif; ?>
    </ul>

    <!-- ── Paginación ── -->
    <div class="tn-pag" id="tnPag">
      <?php if ($_tn_pag > 1): ?>
        <a href="index.php?ruta=todas-notificaciones&pg=<?php echo $_tn_pag - 1; ?>">
          <i class="fa-solid fa-chevron-left" style="font-size:10px"></i> Anterior
        </a>
      <?php endif; ?>
      <span>Página <?php echo $_tn_pag; ?></span>
      <?php if ($_tn_hayMas): ?>
        <a href="index.php?ruta=todas-notificaciones&pg=<?php echo $_tn_pag + 1; ?>">
          Siguiente <i class="fa-solid fa-chevron-right" style="font-size:10px"></i>
        </a>
      <?php endif; ?>
    </div>

  </section>
</div>

<!-- ══ Tab switching ══ -->
<script>
$(function(){
  var tabs    = $('.tn-tab');
  var panels  = {
    'estado':        $('#tnListEstado'),
    'traspaso':      $('#tnListEstado'),   // same panel, filtered
    'atraso':        $('#tnListAtraso'),
    'observaciones': $('#tnListObs')
  };
  var pag = $('#tnPag');

  tabs.on('click', function(){
    tabs.removeClass('active');
    $(this).addClass('active');
    var tab = $(this).data('tab');

    // Hide all panels first
    $('.tn-panel').hide();
    pag.hide();

    if (tab === 'todas') {
      // Show estado list (all items visible) + atrasos below it
      $('#tnListEstado').show().find('.tn-item, .tn-date-sep, .tn-empty').show();
      pag.show();

    } else if (tab === 'atraso') {
      $('#tnListAtraso').show();

    } else if (tab === 'observaciones') {
      $('#tnListObs').show();

    } else {
      // estado or traspaso filter inside tnListEstado
      $('#tnListEstado').show();
      pag.show();

      $('#tnListEstado').find('.tn-item, .tn-date-sep').each(function(){
        var ntype = $(this).data('ntype');
        $(this).toggle(ntype === tab);
      });
      // Show empty message or hide
      $('#tnListEstado .tn-empty').hide();

      // Show date separators only if they have visible siblings
      $('#tnListEstado .tn-date-sep').each(function(){
        var $sep = $(this);
        var hasVisible = false;
        $sep.nextUntil('.tn-date-sep').each(function(){
          if ($(this).is(':visible') && $(this).hasClass('tn-item')) hasVisible = true;
        });
        $sep.toggle(hasVisible);
      });
    }
  });
});
</script>
