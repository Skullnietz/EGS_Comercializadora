<?php
/*  ═══════════════════════════════════════════════════
    NOTIFICACIONES — Bell dropdown + Push controlado
    ═══════════════════════════════════════════════════
    Combina (cronológicamente):
    1. Notificaciones de CAMBIO DE ESTADO (nuevas)
    2. Notificaciones de OBSERVACIONES (hoy)
    3. Notificaciones de ATRASO (órdenes con +5 días)
    ═══════════════════════════════════════════════════ */

date_default_timezone_set("America/Mexico_City");

$_noti_ordenes = array();
$_noti_perfil  = $_SESSION["perfil"];
$_noti_limite6m = date("Y-m-d", strtotime("-6 months"));
$_noti_limite1m = date("Y-m-d", strtotime("-1 month"));

// ══════════════════════════════════════
// 1. NOTIFICACIONES DE ATRASO (existentes)
// ══════════════════════════════════════

// Variable para guardar órdenes del técnico (para filtro de observaciones)
$_noti_tecOrdenIds = null;

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
                // Guardar IDs de órdenes del técnico para filtrar observaciones
                $_noti_tecOrdenIds = array();
                foreach ($ordenesDelTecnico as $o) {
                    $_noti_tecOrdenIds[] = intval($o["id"]);
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
    // Si es técnico, solo observaciones de SUS órdenes
    $_noti_obs = controladorObservaciones::ctrObservacionesRecientesNotif(
        isset($_SESSION["id"]) ? intval($_SESSION["id"]) : 0,
        10,
        $_noti_tecOrdenIds // null para admin/vendedor (sin filtro), array de IDs para técnico
    );
    if (!is_array($_noti_obs)) $_noti_obs = array();
    $_noti_totalObs = count($_noti_obs);

    // Enriquecer con datos de la tabla ordenes (conexión distinta)
    if ($_noti_totalObs > 0) {
        $_noti_obs_ids = array();
        foreach ($_noti_obs as $ob) {
            if (isset($ob['id_orden'])) $_noti_obs_ids[] = intval($ob['id_orden']);
        }
        $_noti_obs_ids = array_unique($_noti_obs_ids);
        if (!empty($_noti_obs_ids)) {
            $_noti_obs_ordData = ModeloNotificaciones::mdlDatosOrdenesPorIds($_noti_obs_ids);
        } else {
            $_noti_obs_ordData = array();
        }
    }
} catch (Exception $e) { $_noti_obs = array(); }

// ══════════════════════════════════════
// 4. OBTENER DATOS DE ÓRDENES PARA LINKS
//    (estado + traspaso notifications)
// ══════════════════════════════════════

$_noti_est_ordData = array();
if (!empty($_noti_estado)) {
    $_noti_est_ids = array();
    foreach ($_noti_estado as $ne) {
        if (isset($ne['id_orden'])) $_noti_est_ids[] = intval($ne['id_orden']);
    }
    $_noti_est_ids = array_unique($_noti_est_ids);
    if (!empty($_noti_est_ids)) {
        try {
            $_noti_est_ordData = ModeloNotificaciones::mdlDatosOrdenesPorIds($_noti_est_ids);
        } catch (Exception $e) {}
    }
}

// ══════════════════════════════════════
// 5. COMBINAR TODO CRONOLÓGICAMENTE
// ══════════════════════════════════════

$_noti_combined = array();

// Agregar estado/traspaso
foreach (array_slice($_noti_estado, 0, 15) as $ne) {
    $neOrdId = intval($ne['id_orden']);
    $neOrd = isset($_noti_est_ordData[$neOrdId]) ? $_noti_est_ordData[$neOrdId] : array();
    $neUrl = "index.php?ruta=infoOrden&idOrden=" . $neOrdId
           . "&empresa=" . intval(isset($neOrd['id_empresa']) ? $neOrd['id_empresa'] : (isset($ne['id_empresa']) ? $ne['id_empresa'] : 0))
           . "&asesor=" . intval(isset($neOrd['id_Asesor']) ? $neOrd['id_Asesor'] : (isset($ne['id_asesor']) ? $ne['id_asesor'] : 0))
           . "&cliente=" . intval(isset($neOrd['id_usuario']) ? $neOrd['id_usuario'] : 0)
           . "&tecnico=" . intval(isset($neOrd['id_tecnico']) ? $neOrd['id_tecnico'] : (isset($ne['id_tecnico']) ? $ne['id_tecnico'] : 0))
           . "&pedido=" . intval(isset($neOrd['id_pedido']) ? $neOrd['id_pedido'] : 0);

    $_noti_combined[] = array(
        'source' => isset($ne['tipo']) && $ne['tipo'] === 'traspaso' ? 'traspaso' : 'estado',
        'fecha'  => $ne['fecha'],
        'data'   => $ne,
        'url'    => $neUrl
    );
}

// Agregar observaciones
foreach (array_slice($_noti_obs, 0, 8) as $nob) {
    $nobOrdId = intval($nob['id_orden']);
    $nobOrd = (isset($_noti_obs_ordData) && isset($_noti_obs_ordData[$nobOrdId])) ? $_noti_obs_ordData[$nobOrdId] : array();
    $nobUrl = "index.php?ruta=infoOrden&idOrden=" . $nobOrdId
            . "&empresa=" . intval(isset($nobOrd['id_empresa']) ? $nobOrd['id_empresa'] : 0)
            . "&asesor=" . intval(isset($nobOrd['id_Asesor']) ? $nobOrd['id_Asesor'] : 0)
            . "&cliente=" . intval(isset($nobOrd['id_usuario']) ? $nobOrd['id_usuario'] : 0)
            . "&tecnico=" . intval(isset($nobOrd['id_tecnico']) ? $nobOrd['id_tecnico'] : 0)
            . "&pedido=" . intval(isset($nobOrd['id_pedido']) ? $nobOrd['id_pedido'] : 0);

    $_noti_combined[] = array(
        'source' => 'obs',
        'fecha'  => $nob['fecha'],
        'data'   => $nob,
        'url'    => $nobUrl
    );
}

// Agregar atrasos (usan fecha_ingreso como referencia, pero los colocamos al final cronológicamente)
foreach ($_noti_mostrar as $o) {
    $urlOrden = "index.php?ruta=infoOrden&idOrden=" . intval($o["id"])
              . "&empresa=" . intval(isset($o["id_empresa"]) ? $o["id_empresa"] : 0)
              . "&asesor=" . intval(isset($o["id_Asesor"]) ? $o["id_Asesor"] : 0)
              . "&cliente=" . intval(isset($o["id_usuario"]) ? $o["id_usuario"] : 0)
              . "&tecnico=" . intval(isset($o["id_tecnico"]) ? $o["id_tecnico"] : 0)
              . "&pedido=" . intval(isset($o["id_pedido"]) ? $o["id_pedido"] : 0);

    $_noti_combined[] = array(
        'source' => 'atraso',
        'fecha'  => isset($o["fecha_ingreso"]) ? $o["fecha_ingreso"] : "2000-01-01",
        'data'   => $o,
        'url'    => $urlOrden
    );
}

// Ordenar cronológicamente (más reciente primero)
usort($_noti_combined, function($a, $b) {
    return strtotime($b['fecha']) - strtotime($a['fecha']);
});

// Limitar a 25 items en el dropdown
$_noti_combined = array_slice($_noti_combined, 0, 25);

// ── Total combinado ──
$_noti_total = $_noti_totalAtraso + $_noti_totalEstado + $_noti_totalObs;

// Helper: info visual de estado
if (!function_exists('_notiEstadoColor')) {
    function _notiEstadoColor($estado) {
        $e = strtolower($estado);
        // Orden de prioridad: estados con "ent" en su nombre van DESPUÉS de pendiente/autorización
        if (strpos($e, 'autorización') !== false || strpos($e, 'autorizacion') !== false || strpos($e, 'pendiente') !== false || $e === 'aut') return array('#f59e0b', '#fffbeb', 'fa-hourglass-half');
        if (strpos($e, 'supervisión') !== false || strpos($e, 'supervision') !== false || $e === 'sup') return array('#8b5cf6', '#f5f3ff', 'fa-eye');
        if (strpos($e, 'garantía aceptada') !== false || strpos($e, 'garantia aceptada') !== false || $e === 'ga') return array('#dc2626', '#fef2f2', 'fa-rotate-left');
        if (strpos($e, 'probable garantía') !== false || strpos($e, 'probable garantia') !== false) return array('#dc2626', '#fef2f2', 'fa-triangle-exclamation');
        if (strpos($e, 'garantía') !== false || strpos($e, 'garantia') !== false) return array('#dc2626', '#fef2f2', 'fa-rotate-left');
        if (strpos($e, 'revisión') !== false || strpos($e, 'revision') !== false || $e === 'rev') return array('#ef4444', '#fef2f2', 'fa-magnifying-glass');
        if (strpos($e, 'terminada') !== false || $e === 'ter') return array('#06b6d4', '#ecfeff', 'fa-flag-checkered');
        if (strpos($e, 'entregado al asesor') !== false) return array('#10b981', '#ecfdf5', 'fa-user-check');
        if (strpos($e, 'entregado/pagado') !== false) return array('#22c55e', '#f0fdf4', 'fa-money-check-dollar');
        if (strpos($e, 'entregado/credito') !== false || strpos($e, 'entregado/crédito') !== false) return array('#22c55e', '#f0fdf4', 'fa-credit-card');
        if (strpos($e, 'entregado') !== false || strpos($e, 'entregada') !== false) return array('#22c55e', '#f0fdf4', 'fa-handshake');
        if (strpos($e, 'aceptado') !== false || strpos($e, 'aceptada') !== false || $e === 'ok') return array('#3b82f6', '#eff6ff', 'fa-circle-check');
        if (strpos($e, 'cancel') !== false) return array('#64748b', '#f1f5f9', 'fa-ban');
        if (strpos($e, 'sin reparación') !== false || strpos($e, 'sin reparacion') !== false || $e === 'sr') return array('#94a3b8', '#f8fafc', 'fa-xmark');
        if (strpos($e, 'producto para venta') !== false || $e === 'pv') return array('#f97316', '#fff7ed', 'fa-tags');
        if (strpos($e, 'producto en almacen') !== false || strpos($e, 'producto en almacén') !== false) return array('#78716c', '#fafaf9', 'fa-warehouse');
        if (strpos($e, 'seguimiento') !== false || strpos($e, 'seguimiento-de-venta') !== false) return array('#0ea5e9', '#f0f9ff', 'fa-chart-line');
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

        <?php foreach ($_noti_combined as $ci):
          $ciSource = $ci['source'];
          $ciData   = $ci['data'];
          $ciUrl    = $ci['url'];

          if ($ciSource === 'traspaso'):
            // ── Traspaso de técnico ──
            $neTiempo = _notiTiempoRel($ciData['fecha']);
        ?>
        <li>
          <a href="<?php echo $ciUrl; ?>"
             style="display:flex;align-items:flex-start;gap:10px;padding:10px 14px;border-left:3px solid #8b5cf6;white-space:normal;text-decoration:none">
            <div style="width:32px;height:32px;border-radius:50%;background:#f5f3ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:2px">
              <i class="fa-solid fa-people-arrows" style="font-size:12px;color:#8b5cf6"></i>
            </div>
            <div style="flex:1;min-width:0;line-height:1.4">
              <div style="font-size:12px;color:#0f172a">
                <strong>#<?php echo htmlspecialchars($ciData['id_orden']); ?></strong>
                traspaso de equipo
              </div>
              <div style="font-size:11px;color:#64748b;margin-top:1px">
                <?php echo htmlspecialchars($ciData['estado_anterior']); ?>
                <i class="fa-solid fa-arrow-right" style="font-size:8px;color:#8b5cf6;margin:0 3px"></i>
                <strong style="color:#8b5cf6"><?php echo htmlspecialchars($ciData['estado_nuevo']); ?></strong>
              </div>
              <div style="font-size:11px;color:#94a3b8;margin-top:2px;display:flex;align-items:center;gap:6px">
                <span><i class="fa-solid fa-user" style="font-size:8px;margin-right:2px"></i><?php echo htmlspecialchars($ciData['nombre_usuario']); ?></span>
                <span style="margin-left:auto"><?php echo $neTiempo; ?></span>
              </div>
            </div>
          </a>
        </li>

        <?php elseif ($ciSource === 'estado'):
              // ── Cambio de estado normal ──
              $neColor = _notiEstadoColor($ciData['estado_nuevo']);
              $neTiempo = _notiTiempoRel($ciData['fecha']);
        ?>
        <li>
          <a href="<?php echo $ciUrl; ?>"
             style="display:flex;align-items:flex-start;gap:10px;padding:10px 14px;border-left:3px solid <?php echo $neColor[0]; ?>;white-space:normal;text-decoration:none">
            <div style="width:32px;height:32px;border-radius:50%;background:<?php echo $neColor[1]; ?>;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:2px">
              <i class="fa-solid <?php echo $neColor[2]; ?>" style="font-size:12px;color:<?php echo $neColor[0]; ?>"></i>
            </div>
            <div style="flex:1;min-width:0;line-height:1.4">
              <div style="font-size:12px;color:#0f172a">
                <strong>#<?php echo htmlspecialchars($ciData['id_orden']); ?></strong>
                cambió a
                <span style="font-weight:700;color:<?php echo $neColor[0]; ?>"><?php echo htmlspecialchars($ciData['estado_nuevo']); ?></span>
              </div>
              <div style="font-size:11px;color:#94a3b8;margin-top:2px;display:flex;align-items:center;gap:6px">
                <span><i class="fa-solid fa-user" style="font-size:8px;margin-right:2px"></i><?php echo htmlspecialchars($ciData['nombre_usuario']); ?></span>
                <span style="margin-left:auto"><?php echo $neTiempo; ?></span>
              </div>
            </div>
          </a>
        </li>

        <?php elseif ($ciSource === 'obs'):
              // ── Observación ──
              $nobTiempo = _notiTiempoRel($ciData['fecha']);
              $nobTexto = mb_strlen($ciData['observacion']) > 60 ? mb_substr($ciData['observacion'], 0, 60) . '…' : $ciData['observacion'];
              $nobCreador = isset($ciData['creador_nombre']) ? $ciData['creador_nombre'] : 'Usuario';
        ?>
        <li>
          <a href="<?php echo $ciUrl; ?>"
             style="display:flex;align-items:flex-start;gap:10px;padding:10px 14px;border-left:3px solid #f59e0b;white-space:normal;text-decoration:none">
            <div style="width:32px;height:32px;border-radius:50%;background:#fffbeb;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:2px">
              <i class="fa-solid fa-comment-dots" style="font-size:12px;color:#f59e0b"></i>
            </div>
            <div style="flex:1;min-width:0;line-height:1.4">
              <div style="font-size:12px;color:#0f172a">
                <strong style="color:#f59e0b">#<?php echo htmlspecialchars($ciData['id_orden']); ?></strong>
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

        <?php elseif ($ciSource === 'atraso'):
              // ── Atraso de entrega ──
              $dias = max(0, floor((time() - strtotime($ciData["fecha_ingreso"])) / 86400));
              $urgColor = $dias >= 30 ? '#ef4444' : ($dias >= 15 ? '#f59e0b' : '#3b82f6');
        ?>
        <li>
          <a href="<?php echo $ciUrl; ?>"
             style="display:flex;align-items:center;gap:8px;padding:8px 12px;text-decoration:none">
            <span style="width:8px;height:8px;border-radius:50%;background:<?php echo $urgColor; ?>;flex-shrink:0"></span>
            <span style="flex:1">
              <strong>#<?php echo $ciData["id"]; ?></strong> Atraso de entrega
              <small style="display:block;color:#94a3b8;font-size:11px"><?php echo $dias; ?> días — <?php echo date("d/m/Y", strtotime($ciData["fecha_ingreso"])); ?></small>
            </span>
          </a>
        </li>
        <?php endif; endforeach; ?>

      </ul>
    </li>
    <?php endif; ?>

    <li class="footer">
      <a href="index.php?ruta=todas-notificaciones" style="font-size:12px">Ver todas las notificaciones</a>
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
      var nuevoTotal = <?php echo $_noti_totalAtraso + $_noti_totalObs; ?>;
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
      // Remover items de estado (border-left azul o púrpura) pero no observaciones (naranja) ni atrasos
      $('.notifications-menu .menu li a[style*="border-left:3px solid #3b82f6"]').closest('li').slideUp(200);
      $('.notifications-menu .menu li a[style*="border-left:3px solid #8b5cf6"]').closest('li').slideUp(200);
      $('.notifications-menu .menu li a[style*="border-left:3px solid #22c55e"]').closest('li').slideUp(200);
      $('.notifications-menu .menu li a[style*="border-left:3px solid #06b6d4"]').closest('li').slideUp(200);
      $('.notifications-menu .menu li a[style*="border-left:3px solid #ef4444"]').closest('li').slideUp(200);
      $('.notifications-menu .menu li a[style*="border-left:3px solid #f59e0b"]:not(:has(.fa-comment-dots))').closest('li').slideUp(200);
      $('.notifications-menu .menu li a[style*="border-left:3px solid #dc2626"]').closest('li').slideUp(200);
      $('.notifications-menu .menu li a[style*="border-left:3px solid #64748b"]').closest('li').slideUp(200);
    });
  });
});
</script>
<?php endif; ?>

<!-- Push notifications del navegador desactivadas - se usan solo toasts internos -->

<?php if ($_noti_totalEstado > 0):
  // Datos para el toast
  $_toast_first = $_noti_estado[0];
  $_toast_tipo = isset($_toast_first['tipo']) ? $_toast_first['tipo'] : 'estado';
  $_toast_color = ($_toast_tipo === 'traspaso') ? array('#8b5cf6', '#f5f3ff', 'fa-people-arrows') : _notiEstadoColor($_toast_first['estado_nuevo']);
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
        <?php if ($_toast_tipo === 'traspaso'): ?>
          Traspaso de equipo
        <?php elseif ($_noti_perfil === 'tecnico'): ?>
          Orden lista para trabajar
        <?php else: ?>
          Cambio de estado
        <?php endif; ?>
      </div>
      <div style="font-size:12px;color:#475569;line-height:1.5">
        <?php if ($_toast_tipo === 'traspaso'): ?>
          <strong style="color:<?php echo $_toast_color[0]; ?>">#<?php echo htmlspecialchars($_toast_first['id_orden']); ?></strong>
          — <?php echo htmlspecialchars($_toast_first['estado_anterior']); ?>
          <i class="fa-solid fa-arrow-right" style="font-size:9px;color:#8b5cf6;margin:0 2px"></i>
          <strong style="color:#8b5cf6"><?php echo htmlspecialchars($_toast_first['estado_nuevo']); ?></strong>
        <?php else: ?>
          <strong style="color:<?php echo $_toast_color[0]; ?>">#<?php echo htmlspecialchars($_toast_first['id_orden']); ?></strong>
          cambió a
          <strong style="color:<?php echo $_toast_color[0]; ?>"><?php echo htmlspecialchars($_toast_first['estado_nuevo']); ?></strong>
        <?php endif; ?>
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
  var lastShownId = parseInt(localStorage.getItem('egs_toast_estado_lastId') || '0', 10);
  var currentId = <?php echo intval($_toast_first['id']); ?>;

  // Solo mostrar si hay una notificación más reciente que la última vista
  if (currentId <= lastShownId) return;
  localStorage.setItem('egs_toast_estado_lastId', currentId);

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
  var lastShownObsId = parseInt(localStorage.getItem('egs_toast_obs_lastId') || '0', 10);
  var currentObsId = <?php echo intval($_toastObs_first['id']); ?>;

  // Solo mostrar si hay una observación más reciente que la última vista
  if (currentObsId <= lastShownObsId) return;
  localStorage.setItem('egs_toast_obs_lastId', currentObsId);

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

<!-- ══════════════════════════════════════════════════
     POLLING EN TIEMPO REAL (cada 45 seg)
     Actualiza badge + muestra toast si hay nuevas
══════════════════════════════════════════════════ -->
<script>
(function(){
  var POLL_INTERVAL = 45000; // 45 segundos
  // Usar el mayor entre lo que hay en PHP y lo guardado en localStorage (por si otra pestaña ya actualizó)
  var serverEstadoId = <?php echo (!empty($_noti_estado) && isset($_noti_estado[0]['id'])) ? intval($_noti_estado[0]['id']) : 0; ?>;
  var serverObsId    = <?php echo (!empty($_noti_obs) && isset($_noti_obs[0]['id'])) ? intval($_noti_obs[0]['id']) : 0; ?>;
  var lastEstadoId   = Math.max(serverEstadoId, parseInt(localStorage.getItem('egs_toast_estado_lastId') || '0', 10));
  var lastObsId      = Math.max(serverObsId, parseInt(localStorage.getItem('egs_toast_obs_lastId') || '0', 10));
  var currentAtraso  = <?php echo $_noti_totalAtraso; ?>;

  function egsPlayNotifSound(type) {
    try {
      var ctx = new (window.AudioContext || window.webkitAudioContext)();
      var osc1 = ctx.createOscillator();
      var gain1 = ctx.createGain();

      if (type === 'traspaso') {
        osc1.type = 'sine';
        osc1.frequency.setValueAtTime(659.3, ctx.currentTime);
        osc1.frequency.setValueAtTime(880, ctx.currentTime + .1);
        gain1.gain.setValueAtTime(0.12, ctx.currentTime);
        gain1.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + .4);
      } else if (type === 'obs') {
        osc1.type = 'triangle';
        osc1.frequency.setValueAtTime(587.3, ctx.currentTime);
        osc1.frequency.setValueAtTime(740, ctx.currentTime + .08);
        gain1.gain.setValueAtTime(0.11, ctx.currentTime);
        gain1.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + .35);
      } else {
        osc1.type = 'sine';
        osc1.frequency.setValueAtTime(880, ctx.currentTime);
        osc1.frequency.setValueAtTime(1108.7, ctx.currentTime + .1);
        gain1.gain.setValueAtTime(0.13, ctx.currentTime);
        gain1.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + .4);
      }
      osc1.connect(gain1);
      gain1.connect(ctx.destination);
      osc1.start(ctx.currentTime);
      osc1.stop(ctx.currentTime + .4);

      var osc2 = ctx.createOscillator();
      var gain2 = ctx.createGain();
      osc2.type = osc1.type;
      osc2.frequency.setValueAtTime(type === 'obs' ? 880 : 1318.5, ctx.currentTime + .12);
      gain2.gain.setValueAtTime(0, ctx.currentTime);
      gain2.gain.setValueAtTime(0.09, ctx.currentTime + .12);
      gain2.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + .5);
      osc2.connect(gain2);
      gain2.connect(ctx.destination);
      osc2.start(ctx.currentTime + .12);
      osc2.stop(ctx.currentTime + .5);
    } catch(e) {}
  }

  function egsShowLiveToast(data) {
    // Remover toast anterior si existe
    var old = document.getElementById('egsLiveToast');
    if (old) old.remove();

    var icon, color, bg, title, body;

    if (data.type === 'traspaso') {
      icon = 'fa-people-arrows'; color = '#8b5cf6'; bg = '#f5f3ff';
      title = 'Traspaso de equipo';
      body = '<strong style="color:#8b5cf6">#' + data.idOrden + '</strong> — ' +
             data.anterior + ' <i class="fa-solid fa-arrow-right" style="font-size:9px;color:#8b5cf6;margin:0 2px"></i> <strong style="color:#8b5cf6">' + data.nuevo + '</strong>';
    } else if (data.type === 'obs') {
      icon = 'fa-comment-dots'; color = '#f59e0b'; bg = '#fffbeb';
      title = 'Nueva observación';
      body = '<strong style="color:#f59e0b">#' + data.idOrden + '</strong> — ' + data.texto +
             '<br><span style="color:#94a3b8;font-size:11px"><i class="fa-solid fa-user" style="font-size:8px"></i> ' + data.creador + '</span>';
    } else {
      icon = 'fa-arrow-right-arrow-left'; color = '#3b82f6'; bg = '#eff6ff';
      title = 'Cambio de estado';
      body = '<strong style="color:' + color + '">#' + data.idOrden + '</strong> cambió a <strong style="color:' + color + '">' + data.nuevo + '</strong>';
      if (data.usuario) body += ' <span style="color:#94a3b8">— ' + data.usuario + '</span>';
    }

    var html = '<div id="egsLiveToast" style="position:fixed;bottom:24px;right:24px;z-index:99999;width:370px;max-width:calc(100vw - 32px);background:#fff;border-radius:16px;border:1px solid #e2e8f0;box-shadow:0 8px 32px rgba(0,0,0,.12);overflow:hidden;animation:egsToastIn .5s cubic-bezier(.16,1,.3,1) forwards;font-family:inherit">' +
      '<div style="height:3px;background:linear-gradient(90deg,' + color + ',#6366f1);animation:egsToastBarShrink 6s linear forwards"></div>' +
      '<div style="padding:14px 16px;display:flex;align-items:flex-start;gap:12px">' +
        '<div style="width:40px;height:40px;border-radius:12px;background:' + bg + ';display:flex;align-items:center;justify-content:center;flex-shrink:0;border:2px solid ' + color + '25">' +
          '<i class="fa-solid ' + icon + '" style="font-size:16px;color:' + color + '"></i>' +
        '</div>' +
        '<div style="flex:1;min-width:0">' +
          '<div style="font-size:13px;font-weight:700;color:#0f172a;margin-bottom:3px">' + title + '</div>' +
          '<div style="font-size:12px;color:#475569;line-height:1.5">' + body + '</div>' +
        '</div>' +
        '<button type="button" onclick="var t=document.getElementById(\'egsLiveToast\');if(t){t.style.animation=\'egsToastOut .4s forwards\';setTimeout(function(){t.remove()},400)}" style="border:none;background:none;color:#94a3b8;font-size:14px;cursor:pointer;padding:0;line-height:1;flex-shrink:0;margin-top:-2px" title="Cerrar"><i class="fa-solid fa-xmark"></i></button>' +
      '</div></div>';

    document.body.insertAdjacentHTML('beforeend', html);

    setTimeout(function(){
      var t = document.getElementById('egsLiveToast');
      if (t) {
        t.style.animation = 'egsToastOut .4s forwards';
        setTimeout(function(){ if (t.parentNode) t.remove(); }, 400);
      }
    }, 6000);
  }

  // Genera el HTML de un item del dropdown según el tipo
  function egsBuildDropdownItem(data) {
    if (data.type === 'traspaso') {
      return '<li><a href="index.php?ruta=infoOrden&idOrden=' + data.idOrden + '" style="display:flex;align-items:flex-start;gap:10px;padding:10px 14px;border-left:3px solid #8b5cf6;white-space:normal;text-decoration:none">' +
        '<div style="width:32px;height:32px;border-radius:50%;background:#f5f3ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:2px">' +
          '<i class="fa-solid fa-people-arrows" style="font-size:12px;color:#8b5cf6"></i></div>' +
        '<div style="flex:1;min-width:0;line-height:1.4">' +
          '<div style="font-size:12px;color:#0f172a"><strong>#' + data.idOrden + '</strong> traspaso de equipo</div>' +
          '<div style="font-size:11px;color:#64748b;margin-top:1px">' + data.anterior +
            ' <i class="fa-solid fa-arrow-right" style="font-size:8px;color:#8b5cf6;margin:0 3px"></i>' +
            '<strong style="color:#8b5cf6">' + data.nuevo + '</strong></div>' +
          '<div style="font-size:11px;color:#94a3b8;margin-top:2px"><i class="fa-solid fa-user" style="font-size:8px;margin-right:2px"></i>' + (data.usuario || '') + ' <span style="margin-left:auto">Ahora</span></div>' +
        '</div></a></li>';
    } else if (data.type === 'obs') {
      return '<li><a href="index.php?ruta=infoOrden&idOrden=' + data.idOrden + '" style="display:flex;align-items:flex-start;gap:10px;padding:10px 14px;border-left:3px solid #f59e0b;white-space:normal;text-decoration:none">' +
        '<div style="width:32px;height:32px;border-radius:50%;background:#fffbeb;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:2px">' +
          '<i class="fa-solid fa-comment-dots" style="font-size:12px;color:#f59e0b"></i></div>' +
        '<div style="flex:1;min-width:0;line-height:1.4">' +
          '<div style="font-size:12px;color:#0f172a"><strong style="color:#f59e0b">#' + data.idOrden + '</strong> nueva observación</div>' +
          '<div style="font-size:11px;color:#64748b;margin-top:1px;overflow:hidden;text-overflow:ellipsis">' + (data.texto || '') + '</div>' +
          '<div style="font-size:11px;color:#94a3b8;margin-top:2px"><i class="fa-solid fa-user" style="font-size:8px;margin-right:2px"></i>' + (data.creador || '') + ' <span style="margin-left:auto">Ahora</span></div>' +
        '</div></a></li>';
    } else {
      // estado — usar color genérico azul para simplificar
      return '<li><a href="index.php?ruta=infoOrden&idOrden=' + data.idOrden + '" style="display:flex;align-items:flex-start;gap:10px;padding:10px 14px;border-left:3px solid #3b82f6;white-space:normal;text-decoration:none">' +
        '<div style="width:32px;height:32px;border-radius:50%;background:#eff6ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:2px">' +
          '<i class="fa-solid fa-arrow-right-arrow-left" style="font-size:12px;color:#3b82f6"></i></div>' +
        '<div style="flex:1;min-width:0;line-height:1.4">' +
          '<div style="font-size:12px;color:#0f172a"><strong>#' + data.idOrden + '</strong> cambió a <span style="font-weight:700;color:#3b82f6">' + data.nuevo + '</span></div>' +
          '<div style="font-size:11px;color:#94a3b8;margin-top:2px"><i class="fa-solid fa-user" style="font-size:8px;margin-right:2px"></i>' + (data.usuario || '') + ' <span style="margin-left:auto">Ahora</span></div>' +
        '</div></a></li>';
    }
  }

  function egsPollNotificaciones() {
    $.post('ajax/notificaciones.ajax.php', { pollNotificaciones: 1 }, function(r) {
      if (!r || typeof r !== 'object') return;

      // Actualizar badge de la campana
      var total = (r.total || 0) + currentAtraso;
      var $badge = $('#egsNotiBell .label');
      if (total > 0) {
        if ($badge.length) {
          $badge.text(total > 99 ? '99+' : total);
        } else {
          $('#egsNotiBell').append('<span class="label label-warning">' + (total > 99 ? '99+' : total) + '</span>');
        }
      } else {
        $badge.remove();
      }

      // Actualizar texto del header del dropdown
      var $headerSpan = $('.notifications-menu .header span').first();
      if ($headerSpan.length && total > 0) {
        $headerSpan.text(total + ' notificaci' + (total > 1 ? 'ones' : 'ón'));
      }

      // Detectar nueva notificación de estado/traspaso
      if (r.ultimaNotif && r.ultimaNotif.id && r.ultimaNotif.id > lastEstadoId) {
        lastEstadoId = r.ultimaNotif.id;
        // Sincronizar con localStorage para evitar re-mostrar en otras pestañas/navegaciones
        localStorage.setItem('egs_toast_estado_lastId', r.ultimaNotif.id);
        var soundType = r.ultimaNotif.tipo === 'traspaso' ? 'traspaso' : 'estado';
        egsPlayNotifSound(soundType);
        egsShowLiveToast({
          type: r.ultimaNotif.tipo || 'estado',
          idOrden: r.ultimaNotif.idOrden,
          anterior: r.ultimaNotif.anterior,
          nuevo: r.ultimaNotif.nuevo,
          usuario: r.ultimaNotif.usuario
        });

        // Insertar en el dropdown menu
        var $menu = $('.notifications-menu .menu');
        if ($menu.length) {
          var itemHtml = egsBuildDropdownItem({
            type: r.ultimaNotif.tipo || 'estado',
            idOrden: r.ultimaNotif.idOrden,
            anterior: r.ultimaNotif.anterior,
            nuevo: r.ultimaNotif.nuevo,
            usuario: r.ultimaNotif.usuario
          });
          $menu.prepend(itemHtml);
          // Limitar a 20 items en el dropdown
          $menu.find('> li').slice(20).remove();
        } else {
          // No existía el <ul class="menu">, crear la estructura
          var $dropdownUl = $('.notifications-menu .dropdown-menu');
          var $noItems = $dropdownUl.find('li.footer');
          $('<li><ul class="menu" style="max-height:400px;overflow-y:auto"></ul></li>').insertBefore($noItems);
          var $newMenu = $dropdownUl.find('.menu');
          $newMenu.append(egsBuildDropdownItem({
            type: r.ultimaNotif.tipo || 'estado',
            idOrden: r.ultimaNotif.idOrden,
            anterior: r.ultimaNotif.anterior,
            nuevo: r.ultimaNotif.nuevo,
            usuario: r.ultimaNotif.usuario
          }));
        }
      }
      // Detectar nueva observación (independiente del estado)
      if (r.ultimaObs && r.ultimaObs.id && r.ultimaObs.id > lastObsId) {
        lastObsId = r.ultimaObs.id;
        localStorage.setItem('egs_toast_obs_lastId', r.ultimaObs.id);
        egsPlayNotifSound('obs');
        egsShowLiveToast({
          type: 'obs',
          idOrden: r.ultimaObs.idOrden,
          texto: r.ultimaObs.texto,
          creador: r.ultimaObs.creador
        });

        // Insertar en el dropdown menu
        var $menu = $('.notifications-menu .menu');
        if ($menu.length) {
          var itemHtml = egsBuildDropdownItem({
            type: 'obs',
            idOrden: r.ultimaObs.idOrden,
            texto: r.ultimaObs.texto,
            creador: r.ultimaObs.creador
          });
          $menu.prepend(itemHtml);
          $menu.find('> li').slice(20).remove();
        }
      }

    }, 'json');
  }

  // Iniciar polling después de 10 segundos
  setTimeout(function(){
    setInterval(egsPollNotificaciones, POLL_INTERVAL);
  }, 10000);

})();
</script>
