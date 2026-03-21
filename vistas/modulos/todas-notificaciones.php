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

// ── Obtener notificaciones ──
try {
    ControladorNotificaciones::ctrCrearTablaEstado();
    $_tn_notifs = ControladorNotificaciones::ctrTodasNotificaciones(
        $_tn_perfil, $_tn_empresa, $_tn_idRol,
        $_tn_porPagina + 1, // +1 para saber si hay más páginas
        $_tn_offset
    );
} catch (Exception $e) { $_tn_notifs = array(); }

if (!is_array($_tn_notifs)) $_tn_notifs = array();
$_tn_hayMas = count($_tn_notifs) > $_tn_porPagina;
if ($_tn_hayMas) array_pop($_tn_notifs); // quitar el extra

// ── Obtener observaciones de hoy ──
$_tn_obsHoy = array();
try {
    $_tn_obsHoy = controladorObservaciones::ctrObservacionesRecientesNotif(
        isset($_SESSION["id"]) ? intval($_SESSION["id"]) : 0, 20
    );
    if (!is_array($_tn_obsHoy)) $_tn_obsHoy = array();
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
?>

<style>
.tn-page {
    max-width: 900px;
    margin: 0 auto;
    padding: 20px 15px;
}
.tn-header {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 24px;
}
.tn-header-icon {
    width: 48px; height: 48px;
    border-radius: 14px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.tn-header-icon i { font-size: 20px; color: #fff; }
.tn-header h2 { font-size: 22px; font-weight: 700; color: #0f172a; margin: 0; }
.tn-header p  { font-size: 13px; color: #94a3b8; margin: 2px 0 0; }

/* ── Stats ── */
.tn-stats {
    display: flex;
    gap: 12px;
    margin-bottom: 24px;
    flex-wrap: wrap;
}
.tn-stat {
    flex: 1;
    min-width: 140px;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 14px 16px;
    display: flex;
    align-items: center;
    gap: 12px;
}
.tn-stat-icon {
    width: 38px; height: 38px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.tn-stat-icon i { font-size: 14px; }
.tn-stat-num { font-size: 20px; font-weight: 700; color: #0f172a; }
.tn-stat-label { font-size: 11px; color: #94a3b8; }

/* ── Tabs ── */
.tn-tabs {
    display: flex;
    gap: 4px;
    margin-bottom: 20px;
    background: #f1f5f9;
    border-radius: 10px;
    padding: 4px;
}
.tn-tab {
    flex: 1;
    text-align: center;
    padding: 8px 14px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    color: #64748b;
    border: none;
    background: none;
    transition: all .2s;
}
.tn-tab.active {
    background: #fff;
    color: #0f172a;
    box-shadow: 0 1px 3px rgba(0,0,0,.08);
}
.tn-tab:hover:not(.active) { color: #0f172a; }

/* ── List ── */
.tn-list { list-style: none; padding: 0; margin: 0; }
.tn-item {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    margin-bottom: 8px;
    padding: 14px 16px;
    display: flex;
    align-items: flex-start;
    gap: 14px;
    transition: box-shadow .15s, border-color .15s;
}
.tn-item:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,.06);
    border-color: #cbd5e1;
}
.tn-item-icon {
    width: 40px; height: 40px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    margin-top: 2px;
}
.tn-item-icon i { font-size: 14px; }
.tn-item-body { flex: 1; min-width: 0; }
.tn-item-title { font-size: 13px; color: #0f172a; line-height: 1.5; }
.tn-item-meta {
    font-size: 11px;
    color: #94a3b8;
    margin-top: 4px;
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}
.tn-badge-tipo {
    font-size: 10px;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

/* ── Pagination ── */
.tn-pag {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-top: 20px;
}
.tn-pag a {
    padding: 8px 16px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    color: #6366f1;
    text-decoration: none;
    transition: all .15s;
}
.tn-pag a:hover {
    background: #eef2ff;
    border-color: #c7d2fe;
}
.tn-pag span {
    padding: 8px 16px;
    font-size: 12px;
    color: #94a3b8;
}

/* ── Empty ── */
.tn-empty {
    text-align: center;
    padding: 60px 20px;
    color: #94a3b8;
}
.tn-empty i { font-size: 40px; margin-bottom: 12px; display: block; color: #cbd5e1; }
.tn-empty p { font-size: 14px; }

/* ── Date separator ── */
.tn-date-sep {
    font-size: 11px;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: .5px;
    padding: 10px 0 6px;
    border-bottom: 1px solid #f1f5f9;
    margin-bottom: 8px;
}
</style>

<div class="tn-page">

  <!-- ══ Header ══ -->
  <div class="tn-header">
    <div class="tn-header-icon">
      <i class="fa-solid fa-bell"></i>
    </div>
    <div>
      <h2>Notificaciones</h2>
      <p>Historial de cambios de estado, traspasos y observaciones</p>
    </div>
  </div>

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
    <button class="tn-tab" data-tab="estado">Cambios de estado</button>
    <button class="tn-tab" data-tab="traspaso">Traspasos</button>
    <button class="tn-tab" data-tab="observaciones">Observaciones</button>
  </div>

  <!-- ══ Lista combinada ══ -->
  <div id="tnTabContent">

    <?php if (empty($_tn_notifs) && empty($_tn_obsHoy)): ?>
      <div class="tn-empty">
        <i class="fa-solid fa-bell-slash"></i>
        <p>No hay notificaciones</p>
      </div>
    <?php else: ?>

      <ul class="tn-list" id="tnListEstado">
      <?php
        $lastDate = '';
        foreach ($_tn_notifs as $n):
          $nTipo   = isset($n['tipo']) ? $n['tipo'] : 'estado';
          $nFecha  = $n['fecha'];
          $nDate   = date('Y-m-d', strtotime($nFecha));
          $nDateLabel = ($nDate === date('Y-m-d')) ? 'Hoy' : (($nDate === date('Y-m-d', strtotime('-1 day'))) ? 'Ayer' : date('d \d\e F, Y', strtotime($nFecha)));

          // Date separator
          if ($nDate !== $lastDate):
            $lastDate = $nDate;
      ?>
        <li class="tn-date-sep" data-ntype="<?php echo $nTipo; ?>"><?php echo $nDateLabel; ?></li>
      <?php endif; ?>

      <?php if ($nTipo === 'traspaso'):
            // ── Traspaso ──
      ?>
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
            // ── Cambio de estado ──
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
      </ul>

      <!-- ── Observaciones (tab separado) ── -->
      <ul class="tn-list" id="tnListObs" style="display:none">
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

      <!-- ── Paginación (solo para estado/traspaso) ── -->
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

    <?php endif; ?>

  </div>

</div>

<!-- ══ Tab switching ══ -->
<script>
$(function(){
  var tabs = $('.tn-tab');
  var listEstado = $('#tnListEstado');
  var listObs    = $('#tnListObs');
  var pag        = $('#tnPag');

  tabs.on('click', function(){
    tabs.removeClass('active');
    $(this).addClass('active');
    var tab = $(this).data('tab');

    if (tab === 'observaciones') {
      listEstado.hide();
      listObs.show();
      pag.hide();
    } else {
      listObs.hide();
      listEstado.show();
      pag.show();

      // Filtrar items
      listEstado.find('.tn-item, .tn-date-sep').each(function(){
        var ntype = $(this).data('ntype');
        if (tab === 'todas') {
          $(this).show();
        } else {
          $(this).toggle(ntype === tab);
        }
      });

      // Mostrar date separators solo si tienen items visibles después
      if (tab !== 'todas') {
        listEstado.find('.tn-date-sep').each(function(){
          var $sep = $(this);
          var hasVisible = false;
          $sep.nextUntil('.tn-date-sep').each(function(){
            if ($(this).is(':visible')) hasVisible = true;
          });
          $sep.toggle(hasVisible);
        });
      }
    }
  });
});
</script>
