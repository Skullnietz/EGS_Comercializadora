<?php
/*  ═══════════════════════════════════════════════════
    NOTIFICACIONES — Bell dropdown + Push controlado
    ═══════════════════════════════════════════════════
    Combina:
    1. Notificaciones de ATRASO (órdenes con +5 días)
    2. Notificaciones de CAMBIO DE ESTADO (nuevas)
    ═══════════════════════════════════════════════════ */

date_default_timezone_set("America/Mexico_City");

$_noti_ordenes = array();
$_noti_perfil  = $_SESSION["perfil"];
$_noti_limite6m = date("Y-m-d", strtotime("-6 months"));
$_noti_limite1m = date("Y-m-d", strtotime("-1 month"));

// ══════════════════════════════════════
// 1. NOTIFICACIONES DE ATRASO (existentes)
// ══════════════════════════════════════

if ($_noti_perfil == "administrador") {
    $_noti_raw = controladorOrdenes::ctrMostrarOrdenes("id_empresa", $_SESSION["empresa"]);
    if (is_array($_noti_raw)) {
        foreach ($_noti_raw as $o) {
            $est = isset($o["estado"]) ? $o["estado"] : "";
            $fi  = isset($o["fecha_ingreso"]) ? $o["fecha_ingreso"] : "";
            if (strpos($est, "Ent") === false && strpos($est, "can") === false
                && stripos($est, "sin reparación") === false && strpos($est, "SR") === false
                && stripos($est, "producto para venta") === false && strpos($est, "PV") === false
                && $fi >= $_noti_limite6m) {
                if (strtotime($fi . "+ 5 days") <= time()) {
                    $_noti_ordenes[] = $o;
                }
            }
        }
    }
} elseif ($_noti_perfil == "vendedor") {
    try {
        $Asesores = Controladorasesores::ctrMostrarAsesoresEleg("correo", $_SESSION["email"]);
        if (is_array($Asesores) && isset($Asesores["id"])) {
            $_noti_raw = controladorOrdenes::ctrMostrarOrdenesDelAsesor($Asesores["id"]);
            if (is_array($_noti_raw)) {
                foreach ($_noti_raw as $o) {
                    $est = isset($o["estado"]) ? $o["estado"] : "";
                    $fi  = isset($o["fecha_ingreso"]) ? $o["fecha_ingreso"] : "";
                    if (strpos($est, "Ent") === false && strpos($est, "can") === false
                        && stripos($est, "sin reparación") === false && strpos($est, "SR") === false
                        && stripos($est, "producto para venta") === false && strpos($est, "PV") === false
                        && $fi >= $_noti_limite6m) {
                        if (strtotime($fi . "+ 5 days") <= time()) {
                            $_noti_ordenes[] = $o;
                        }
                    }
                }
            }
        }
    } catch (Exception $e) {}
} elseif ($_noti_perfil == "tecnico") {
    try {
        $tecnico = ControladorTecnicos::ctrMostrarTecnicos("correo", $_SESSION["email"]);
        if (is_array($tecnico) && isset($tecnico["id"])) {
            $ordenesDelTecnico = controladorOrdenes::ctrMostrarOrdenesDelTecncio($tecnico["id"]);
            if (is_array($ordenesDelTecnico)) {
                foreach ($ordenesDelTecnico as $o) {
                    $est = isset($o["estado"]) ? $o["estado"] : "";
                    $fi  = isset($o["fecha_ingreso"]) ? $o["fecha_ingreso"] : "";
                    if (strpos($est, "Ent") === false
                        && strpos($est, "can") === false
                        && strpos($est, "AUT") === false
                        && stripos($est, "sin reparación") === false && strpos($est, "SR") === false
                        && stripos($est, "producto para venta") === false && strpos($est, "PV") === false
                        && $fi >= $_noti_limite6m
                    ) {
                        if (strtotime($fi . "+ 5 days") <= time()) {
                            $_noti_ordenes[] = $o;
                        }
                    }
                }
            }
        }
    } catch (Exception $e) {}
}

usort($_noti_ordenes, function($a, $b) {
    return strtotime(isset($b["fecha_ingreso"]) ? $b["fecha_ingreso"] : "now")
         - strtotime(isset($a["fecha_ingreso"]) ? $a["fecha_ingreso"] : "now");
});

$_noti_mostrar = array_slice($_noti_ordenes, 0, 20);
$_noti_totalAtraso = count($_noti_ordenes);

// Para push: solo las del último mes, máx 3
$_noti_push = array();
foreach ($_noti_ordenes as $o) {
    $fi = isset($o["fecha_ingreso"]) ? $o["fecha_ingreso"] : "";
    if ($fi >= $_noti_limite1m) {
        $_noti_push[] = $o;
        if (count($_noti_push) >= 3) break;
    }
}

// ══════════════════════════════════════
// 2. NOTIFICACIONES DE CAMBIO DE ESTADO
// ══════════════════════════════════════

$_noti_estado = array();
$_noti_totalEstado = 0;

if ($_noti_perfil === "administrador" || $_noti_perfil === "vendedor" || $_noti_perfil === "tecnico") {
    try {
        // Asegurar que la tabla exista
        ControladorNotificaciones::ctrCrearTablaEstado();

        $_noti_idRol = null;
        if ($_noti_perfil === "vendedor") {
            if (isset($Asesores) && is_array($Asesores) && isset($Asesores["id"])) {
                $_noti_idRol = intval($Asesores["id"]);
            } else {
                $Asesores = Controladorasesores::ctrMostrarAsesoresEleg("correo", $_SESSION["email"]);
                if (is_array($Asesores) && isset($Asesores["id"])) {
                    $_noti_idRol = intval($Asesores["id"]);
                }
            }
        } elseif ($_noti_perfil === "tecnico") {
            if (isset($tecnico) && is_array($tecnico) && isset($tecnico["id"])) {
                $_noti_idRol = intval($tecnico["id"]);
            } else {
                $tecnico = ControladorTecnicos::ctrMostrarTecnicos("correo", $_SESSION["email"]);
                if (is_array($tecnico) && isset($tecnico["id"])) {
                    $_noti_idRol = intval($tecnico["id"]);
                }
            }
        }

        $_noti_estado = ControladorNotificaciones::ctrNotifEstadoNoLeidas(
            $_noti_perfil,
            isset($_SESSION["empresa"]) ? intval($_SESSION["empresa"]) : 0,
            $_noti_idRol
        );

        if (!is_array($_noti_estado)) $_noti_estado = array();
        $_noti_totalEstado = count($_noti_estado);

    } catch (Exception $e) { $_noti_estado = array(); }
}

// ══════════════════════════════════════
// 3. NOTIFICACIONES DE OBSERVACIONES
// ══════════════════════════════════════

$_noti_obs = array();
$_noti_totalObs = 0;

try {
    $_noti_obs = controladorObservaciones::ctrObservacionesRecientesNotif(
        isset($_SESSION["id"]) ? intval($_SESSION["id"]) : 0,
        10
    );
    if (!is_array($_noti_obs)) $_noti_obs = array();
    $_noti_totalObs = count($_noti_obs);
} catch (Exception $e) { $_noti_obs = array(); }

// ── Total combinado ──
$_noti_total = $_noti_totalAtraso + $_noti_totalEstado + $_noti_totalObs;

// Helper: info visual de estado
if (!function_exists('_notiEstadoColor')) {
    function _notiEstadoColor($estado) {
        $e = strtolower($estado);
        if (strpos($e, 'entregado') !== false || strpos($e, 'ent') !== false) return array('#22c55e', '#f0fdf4', 'fa-handshake');
        if (strpos($e, 'terminada') !== false || strpos($e, 'ter') !== false) return array('#06b6d4', '#ecfeff', 'fa-flag-checkered');
        if (strpos($e, 'aceptado') !== false || strpos($e, 'ok') !== false)   return array('#3b82f6', '#eff6ff', 'fa-circle-check');
        if (strpos($e, 'revisión') !== false || strpos($e, 'rev') !== false)  return array('#ef4444', '#fef2f2', 'fa-magnifying-glass');
        if (strpos($e, 'autorización') !== false || strpos($e, 'aut') !== false) return array('#f59e0b', '#fffbeb', 'fa-hourglass-half');
        if (strpos($e, 'supervisión') !== false || strpos($e, 'sup') !== false)  return array('#8b5cf6', '#f5f3ff', 'fa-eye');
        if (strpos($e, 'garantía') !== false || strpos($e, 'garantia') !== false) return array('#dc2626', '#fef2f2', 'fa-rotate-left');
        return array('#64748b', '#f1f5f9', 'fa-circle-info');
    }
}
if (!function_exists('_notiTiempoRel')) {
    function _notiTiempoRel($fecha) {
        $diff = time() - strtotime($fecha);
        if ($diff < 60) return 'Ahora';
        if ($diff < 3600) return floor($diff / 60) . 'min';
        if ($diff < 86400) return floor($diff / 3600) . 'h';
        if ($diff < 172800) return 'Ayer';
        return date('d/m', strtotime($fecha));
    }
}
?>

<!-- notifications-menu -->
<li class="dropdown notifications-menu">

  <a href="#" class="dropdown-toggle" data-toggle="dropdown" id="egsNotiBell">
    <i class="fa-solid fa-bell"></i>
    <?php if ($_noti_total > 0): ?>
      <span class="label label-warning"><?php echo $_noti_total > 99 ? '99+' : $_noti_total; ?></span>
    <?php endif; ?>
  </a>

  <ul class="dropdown-menu" style="width:360px">

    <li class="header" style="font-size:12px;font-weight:600;display:flex;align-items:center;justify-content:space-between;padding:10px 14px">
      <span>
        <?php if ($_noti_total > 0): ?>
          <?php echo $_noti_total; ?> notificaci<?php echo $_noti_total > 1 ? 'ones' : 'ón'; ?>
        <?php else: ?>
          Sin notificaciones
        <?php endif; ?>
      </span>
      <?php if ($_noti_totalEstado > 0): ?>
        <button type="button" id="egsMarcarLeidas"
                style="border:none;background:#eef2ff;color:#6366f1;font-size:10px;font-weight:600;padding:3px 8px;border-radius:6px;cursor:pointer;transition:background .15s"
                onmouseover="this.style.background='#e0e7ff'" onmouseout="this.style.background='#eef2ff'">
          <i class="fa-solid fa-check-double" style="font-size:9px"></i> Marcar leídas
        </button>
      <?php endif; ?>
    </li>

    <?php if ($_noti_total > 0): ?>
    <li>
      <ul class="menu" style="max-height:400px;overflow-y:auto">

        <?php // ── Cambios de estado (primero, son las nuevas) ──
        if (!empty($_noti_estado)):
          foreach (array_slice($_noti_estado, 0, 15) as $ne):
            $neColor = _notiEstadoColor($ne['estado_nuevo']);
            $neTiempo = _notiTiempoRel($ne['fecha']);
        ?>
        <li>
          <a style="display:flex;align-items:flex-start;gap:10px;padding:10px 14px;border-left:3px solid <?php echo $neColor[0]; ?>;cursor:default;white-space:normal">
            <div style="width:32px;height:32px;border-radius:50%;background:<?php echo $neColor[1]; ?>;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:2px">
              <i class="fa-solid <?php echo $neColor[2]; ?>" style="font-size:12px;color:<?php echo $neColor[0]; ?>"></i>
            </div>
            <div style="flex:1;min-width:0;line-height:1.4">
              <div style="font-size:12px;color:#0f172a">
                <strong>#<?php echo htmlspecialchars($ne['id_orden']); ?></strong>
                cambió a
                <span style="font-weight:700;color:<?php echo $neColor[0]; ?>"><?php echo htmlspecialchars($ne['estado_nuevo']); ?></span>
              </div>
              <div style="font-size:11px;color:#94a3b8;margin-top:2px;display:flex;align-items:center;gap:6px">
                <span><i class="fa-solid fa-user" style="font-size:8px;margin-right:2px"></i><?php echo htmlspecialchars($ne['nombre_usuario']); ?></span>
                <span style="margin-left:auto"><?php echo $neTiempo; ?></span>
              </div>
            </div>
          </a>
        </li>
        <?php endforeach; endif; ?>

        <?php // ── Observaciones nuevas ──
        if (!empty($_noti_obs)):
          foreach (array_slice($_noti_obs, 0, 8) as $nob):
            $nobTiempo = _notiTiempoRel($nob['fecha']);
            $nobTexto = mb_strlen($nob['observacion']) > 60 ? mb_substr($nob['observacion'], 0, 60) . '…' : $nob['observacion'];
            $nobCreador = isset($nob['creador_nombre']) ? $nob['creador_nombre'] : 'Usuario';
        ?>
        <li>
          <a href="index.php?ruta=ordenesnew&idOrden=<?php echo $nob['id_orden']; ?>"
             style="display:flex;align-items:flex-start;gap:10px;padding:10px 14px;border-left:3px solid #f59e0b;white-space:normal">
            <div style="width:32px;height:32px;border-radius:50%;background:#fffbeb;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:2px">
              <i class="fa-solid fa-comment-dots" style="font-size:12px;color:#f59e0b"></i>
            </div>
            <div style="flex:1;min-width:0;line-height:1.4">
              <div style="font-size:12px;color:#0f172a">
                <strong style="color:#f59e0b">#<?php echo htmlspecialchars($nob['id_orden']); ?></strong>
                nueva observación
              </div>
              <div style="font-size:11px;color:#64748b;margin-top:1px;overflow:hidden;text-overflow:ellipsis"><?php echo htmlspecialchars($nobTexto); ?></div>
              <div style="font-size:11px;color:#94a3b8;margin-top:2px;display:flex;align-items:center;gap:6px">
                <span><i class="fa-solid fa-user" style="font-size:8px;margin-right:2px"></i><?php echo htmlspecialchars($nobCreador); ?></span>
                <span style="margin-left:auto"><?php echo $nobTiempo; ?></span>
              </div>
            </div>
          </a>
        </li>
        <?php endforeach; endif; ?>

        <?php // ── Atrasos (después) ──
        if (!empty($_noti_mostrar)):
          foreach ($_noti_mostrar as $o):
            $dias = max(0, floor((time() - strtotime($o["fecha_ingreso"])) / 86400));
            $urgColor = $dias >= 30 ? '#ef4444' : ($dias >= 15 ? '#f59e0b' : '#3b82f6');
        ?>
        <li>
          <a class="btnVerInfoOrden"
             idOrden="<?php echo $o["id"]; ?>"
             cliente="<?php echo isset($o["id_usuario"]) ? $o["id_usuario"] : ''; ?>"
             tecnico="<?php echo isset($o["id_tecnico"]) ? $o["id_tecnico"] : ''; ?>"
             asesor="<?php echo isset($o["id_Asesor"]) ? $o["id_Asesor"] : ''; ?>"
             empresa="<?php echo isset($o["id_empresa"]) ? $o["id_empresa"] : ''; ?>"
             pedido="<?php echo isset($o["id_pedido"]) ? $o["id_pedido"] : ''; ?>"
             tecnicodos="<?php echo isset($o["id_tecnicoDos"]) ? $o["id_tecnicoDos"] : ''; ?>"
             item="nuevasVisitas"
             style="display:flex;align-items:center;gap:8px;padding:8px 12px">
            <span style="width:8px;height:8px;border-radius:50%;background:<?php echo $urgColor; ?>;flex-shrink:0"></span>
            <span style="flex:1">
              <strong>#<?php echo $o["id"]; ?></strong> Atraso de entrega
              <small style="display:block;color:#94a3b8;font-size:11px"><?php echo $dias; ?> días — <?php echo date("d/m/Y", strtotime($o["fecha_ingreso"])); ?></small>
            </span>
          </a>
        </li>
        <?php endforeach; endif; ?>

      </ul>
    </li>
    <?php endif; ?>

    <li class="footer">
      <a href="index.php?ruta=ordenesnew" style="font-size:12px">Ver todas las órdenes</a>
    </li>

  </ul>

</li>
<!-- /notifications-menu -->

<?php if ($_noti_totalEstado > 0): ?>
<!-- Script: marcar notificaciones de estado como leídas -->
<script>
$(function(){
  $('#egsMarcarLeidas').on('click', function(e){
    e.preventDefault();
    e.stopPropagation();
    var $btn = $(this);
    $btn.prop('disabled', true).text('Listo ✓');
    $.post('ajax/notificaciones.ajax.php', { marcarLeidasEstado: 1 }, function(){
      // Quitar badge y notificaciones de estado del dropdown
      var $badge = $('#egsNotiBell .label');
      var nuevoTotal = <?php echo $_noti_totalAtraso; ?>;
      if (nuevoTotal > 0) {
        $badge.text(nuevoTotal > 99 ? '99+' : nuevoTotal);
      } else {
        $badge.remove();
      }
      // Ocultar las notificaciones de cambio de estado del DOM
      $btn.closest('.header').find('span').first().text(
        nuevoTotal > 0 ? nuevoTotal + ' notificaci' + (nuevoTotal > 1 ? 'ones' : 'ón') : 'Sin notificaciones'
      );
      $btn.fadeOut();
      // Remover items de estado (los que tienen border-left)
      $('.notifications-menu .menu li a[style*="border-left"]').closest('li').slideUp(200);
    });
  });
});
</script>
<?php endif; ?>

<?php if (!empty($_noti_push)): ?>
<!-- Push notifications: SOLO 1 vez por sesión del navegador -->
<script>
(function(){
  var pushKey = 'egs_push_shown_' + <?php echo json_encode(session_id()); ?>;

  if (sessionStorage.getItem(pushKey)) return;

  sessionStorage.setItem(pushKey, '1');

  setTimeout(function(){
    if (typeof Push === 'undefined') return;

    <?php
    $pushOrd = $_noti_push[0];
    $pushImg = '';
    $album = json_decode(isset($pushOrd["multimedia"]) ? $pushOrd["multimedia"] : '[]', true);
    if (is_array($album)) {
        foreach ($album as $img) {
            if (isset($img["foto"])) { $pushImg = $img["foto"]; break; }
        }
    }
    ?>

    try {
      Push.create("Tienes <?php echo count($_noti_push); ?> orden<?php echo count($_noti_push) > 1 ? 'es' : ''; ?> con atraso", {
        body: "Orden #<?php echo $pushOrd['id']; ?><?php echo count($_noti_push) > 1 ? ' y ' . (count($_noti_push) - 1) . ' más' : ''; ?> — Requiere atención",
        <?php if (!empty($pushImg)): ?>
        icon: "<?php echo $pushImg; ?>",
        <?php endif; ?>
        timeout: 8000,
        onClick: function(){
          window.focus();
          window.location = "index.php?ruta=inicio";
          this.close();
        }
      });
    } catch(e) {}

  }, 3000);
})();
</script>
<?php endif; ?>

<?php if ($_noti_totalEstado > 0):
  // Datos para el toast
  $_toast_first = $_noti_estado[0];
  $_toast_color = _notiEstadoColor($_toast_first['estado_nuevo']);
  $_toast_extras = $_noti_totalEstado - 1;
?>
<!-- ══════════════════════════════════════════
     TOAST NOTIFICATION + SONIDO
     Se muestra 1 vez por sesión del navegador
══════════════════════════════════════════ -->
<style>
@keyframes egsToastIn {
  0%   { transform: translateX(120%); opacity: 0; }
  100% { transform: translateX(0); opacity: 1; }
}
@keyframes egsToastOut {
  0%   { transform: translateX(0); opacity: 1; }
  100% { transform: translateX(120%); opacity: 0; }
}
@keyframes egsToastPulse {
  0%, 100% { box-shadow: 0 8px 32px rgba(0,0,0,.12); }
  50%      { box-shadow: 0 8px 32px rgba(99,102,241,.25); }
}
#egsToast {
  position: fixed;
  bottom: 24px;
  right: 24px;
  z-index: 99999;
  width: 370px;
  max-width: calc(100vw - 32px);
  background: #fff;
  border-radius: 16px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 8px 32px rgba(0,0,0,.12);
  overflow: hidden;
  animation: egsToastIn .5s cubic-bezier(.16,1,.3,1) forwards,
             egsToastPulse 2s ease-in-out 2;
  font-family: inherit;
}
#egsToast.egs-toast-hide {
  animation: egsToastOut .4s cubic-bezier(.7,0,.84,0) forwards;
}
#egsToast .egs-toast-bar {
  height: 3px;
  background: linear-gradient(90deg, <?php echo $_toast_color[0]; ?>, #6366f1);
  animation: egsToastBarShrink 6s linear forwards;
}
@keyframes egsToastBarShrink {
  0%   { width: 100%; }
  100% { width: 0; }
}
</style>

<div id="egsToast" style="display:none">
  <!-- Barra de progreso -->
  <div class="egs-toast-bar"></div>

  <!-- Contenido -->
  <div style="padding:14px 16px;display:flex;align-items:flex-start;gap:12px">

    <!-- Icono -->
    <div style="width:40px;height:40px;border-radius:12px;background:<?php echo $_toast_color[1]; ?>;display:flex;align-items:center;justify-content:center;flex-shrink:0;border:2px solid <?php echo $_toast_color[0]; ?>25">
      <i class="fa-solid <?php echo $_toast_color[2]; ?>" style="font-size:16px;color:<?php echo $_toast_color[0]; ?>"></i>
    </div>

    <!-- Texto -->
    <div style="flex:1;min-width:0">
      <div style="font-size:13px;font-weight:700;color:#0f172a;margin-bottom:3px">
        <?php if ($_noti_perfil === 'tecnico'): ?>
          Orden lista para trabajar
        <?php else: ?>
          Cambio de estado
        <?php endif; ?>
      </div>
      <div style="font-size:12px;color:#475569;line-height:1.5">
        <strong style="color:<?php echo $_toast_color[0]; ?>">#<?php echo htmlspecialchars($_toast_first['id_orden']); ?></strong>
        cambió a
        <strong style="color:<?php echo $_toast_color[0]; ?>"><?php echo htmlspecialchars($_toast_first['estado_nuevo']); ?></strong>
        <?php if (!empty($_toast_first['nombre_usuario'])): ?>
          <span style="color:#94a3b8">— <?php echo htmlspecialchars($_toast_first['nombre_usuario']); ?></span>
        <?php endif; ?>
      </div>
      <?php if ($_toast_extras > 0): ?>
        <div style="font-size:11px;color:#6366f1;font-weight:600;margin-top:4px">
          <i class="fa-solid fa-bell" style="font-size:9px"></i>
          y <?php echo $_toast_extras; ?> notificaci<?php echo $_toast_extras > 1 ? 'ones' : 'ón'; ?> más
        </div>
      <?php endif; ?>
    </div>

    <!-- Cerrar -->
    <button type="button" onclick="document.getElementById('egsToast').classList.add('egs-toast-hide');setTimeout(function(){document.getElementById('egsToast').style.display='none'},400)"
            style="border:none;background:none;color:#94a3b8;font-size:14px;cursor:pointer;padding:0;line-height:1;flex-shrink:0;margin-top:-2px"
            title="Cerrar">
      <i class="fa-solid fa-xmark"></i>
    </button>

  </div>
</div>

<script>
(function(){
  var toastKey = 'egs_toast_estado_' + <?php echo json_encode(session_id()); ?>;

  // Solo mostrar 1 vez por sesión
  if (sessionStorage.getItem(toastKey)) return;
  sessionStorage.setItem(toastKey, '1');

  // Esperar a que cargue la página
  setTimeout(function(){

    // ── Sonido de notificación (Web Audio API) ──
    try {
      var ctx = new (window.AudioContext || window.webkitAudioContext)();

      // Nota 1: tono suave
      var osc1 = ctx.createOscillator();
      var gain1 = ctx.createGain();
      osc1.type = 'sine';
      osc1.frequency.setValueAtTime(880, ctx.currentTime);       // A5
      osc1.frequency.setValueAtTime(1108.7, ctx.currentTime + .1); // C#6
      gain1.gain.setValueAtTime(0.15, ctx.currentTime);
      gain1.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + .4);
      osc1.connect(gain1);
      gain1.connect(ctx.destination);
      osc1.start(ctx.currentTime);
      osc1.stop(ctx.currentTime + .4);

      // Nota 2: segundo toque (tipo "ding-dong")
      var osc2 = ctx.createOscillator();
      var gain2 = ctx.createGain();
      osc2.type = 'sine';
      osc2.frequency.setValueAtTime(1318.5, ctx.currentTime + .15); // E6
      gain2.gain.setValueAtTime(0, ctx.currentTime);
      gain2.gain.setValueAtTime(0.12, ctx.currentTime + .15);
      gain2.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + .6);
      osc2.connect(gain2);
      gain2.connect(ctx.destination);
      osc2.start(ctx.currentTime + .15);
      osc2.stop(ctx.currentTime + .6);
    } catch(e) {}

    // ── Mostrar toast ──
    var toast = document.getElementById('egsToast');
    if (toast) {
      toast.style.display = 'block';

      // Auto-ocultar después de 6 segundos
      setTimeout(function(){
        if (toast.style.display !== 'none') {
          toast.classList.add('egs-toast-hide');
          setTimeout(function(){ toast.style.display = 'none'; }, 400);
        }
      }, 6000);
    }

  }, 1500);
})();
</script>
<?php endif; ?>

<?php if ($_noti_totalObs > 0):
  // Datos para el toast de observaciones
  $_toastObs_first = $_noti_obs[0];
  $_toastObs_extras = $_noti_totalObs - 1;
  $_toastObs_creador = isset($_toastObs_first['creador_nombre']) ? $_toastObs_first['creador_nombre'] : 'Usuario';
  $_toastObs_texto = mb_strlen($_toastObs_first['observacion']) > 70
      ? mb_substr($_toastObs_first['observacion'], 0, 70) . '…'
      : $_toastObs_first['observacion'];
?>
<!-- ══════════════════════════════════════════
     TOAST OBSERVACIONES + SONIDO
     Se muestra 1 vez por sesión del navegador
══════════════════════════════════════════ -->
<style>
#egsToastObs {
  position: fixed;
  bottom: <?php echo ($_noti_totalEstado > 0) ? '110px' : '24px'; ?>;
  right: 24px;
  z-index: 99998;
  width: 370px;
  max-width: calc(100vw - 32px);
  background: #fff;
  border-radius: 16px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 8px 32px rgba(0,0,0,.12);
  overflow: hidden;
  animation: egsToastIn .5s cubic-bezier(.16,1,.3,1) forwards,
             egsToastPulse 2s ease-in-out 2;
  font-family: inherit;
}
#egsToastObs.egs-toast-hide {
  animation: egsToastOut .4s cubic-bezier(.7,0,.84,0) forwards;
}
#egsToastObs .egs-toast-bar-obs {
  height: 3px;
  background: linear-gradient(90deg, #f59e0b, #ef4444);
  animation: egsToastBarShrink 6s linear forwards;
}
</style>

<div id="egsToastObs" style="display:none">
  <div class="egs-toast-bar-obs"></div>
  <div style="padding:14px 16px;display:flex;align-items:flex-start;gap:12px">

    <div style="width:40px;height:40px;border-radius:12px;background:#fffbeb;display:flex;align-items:center;justify-content:center;flex-shrink:0;border:2px solid rgba(245,158,11,.25)">
      <i class="fa-solid fa-comment-dots" style="font-size:16px;color:#f59e0b"></i>
    </div>

    <div style="flex:1;min-width:0">
      <div style="font-size:13px;font-weight:700;color:#0f172a;margin-bottom:3px">
        Nueva observación
      </div>
      <div style="font-size:12px;color:#475569;line-height:1.5">
        <strong style="color:#f59e0b">#<?php echo htmlspecialchars($_toastObs_first['id_orden']); ?></strong>
        — <?php echo htmlspecialchars($_toastObs_texto); ?>
      </div>
      <div style="font-size:11px;color:#94a3b8;margin-top:2px">
        <i class="fa-solid fa-user" style="font-size:8px;margin-right:2px"></i><?php echo htmlspecialchars($_toastObs_creador); ?>
      </div>
      <?php if ($_toastObs_extras > 0): ?>
        <div style="font-size:11px;color:#f59e0b;font-weight:600;margin-top:4px">
          <i class="fa-solid fa-comment-dots" style="font-size:9px"></i>
          y <?php echo $_toastObs_extras; ?> observaci<?php echo $_toastObs_extras > 1 ? 'ones' : 'ón'; ?> más
        </div>
      <?php endif; ?>
    </div>

    <button type="button" onclick="document.getElementById('egsToastObs').classList.add('egs-toast-hide');setTimeout(function(){document.getElementById('egsToastObs').style.display='none'},400)"
            style="border:none;background:none;color:#94a3b8;font-size:14px;cursor:pointer;padding:0;line-height:1;flex-shrink:0;margin-top:-2px"
            title="Cerrar">
      <i class="fa-solid fa-xmark"></i>
    </button>

  </div>
</div>

<script>
(function(){
  var obsKey = 'egs_toast_obs_' + <?php echo json_encode(session_id()); ?>;

  if (sessionStorage.getItem(obsKey)) return;
  sessionStorage.setItem(obsKey, '1');

  // Delay: si hay toast de estado, esperar un poco más
  var delay = <?php echo ($_noti_totalEstado > 0) ? '2500' : '1500'; ?>;

  setTimeout(function(){

    // ── Sonido: tono cálido diferente al de cambios de estado ──
    try {
      var ctx = new (window.AudioContext || window.webkitAudioContext)();

      // Tono cálido tipo "pop" (D5 → F#5)
      var osc1 = ctx.createOscillator();
      var gain1 = ctx.createGain();
      osc1.type = 'triangle';
      osc1.frequency.setValueAtTime(587.3, ctx.currentTime);       // D5
      osc1.frequency.setValueAtTime(740, ctx.currentTime + .08);   // F#5
      gain1.gain.setValueAtTime(0.13, ctx.currentTime);
      gain1.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + .35);
      osc1.connect(gain1);
      gain1.connect(ctx.destination);
      osc1.start(ctx.currentTime);
      osc1.stop(ctx.currentTime + .35);

      // Segundo toque suave (A5)
      var osc2 = ctx.createOscillator();
      var gain2 = ctx.createGain();
      osc2.type = 'triangle';
      osc2.frequency.setValueAtTime(880, ctx.currentTime + .12);
      gain2.gain.setValueAtTime(0, ctx.currentTime);
      gain2.gain.setValueAtTime(0.10, ctx.currentTime + .12);
      gain2.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + .5);
      osc2.connect(gain2);
      gain2.connect(ctx.destination);
      osc2.start(ctx.currentTime + .12);
      osc2.stop(ctx.currentTime + .5);
    } catch(e) {}

    // ── Mostrar toast ──
    var toast = document.getElementById('egsToastObs');
    if (toast) {
      toast.style.display = 'block';

      setTimeout(function(){
        if (toast.style.display !== 'none') {
          toast.classList.add('egs-toast-hide');
          setTimeout(function(){ toast.style.display = 'none'; }, 400);
        }
      }, 6000);
    }

  }, delay);
})();
</script>
<?php endif; ?>
