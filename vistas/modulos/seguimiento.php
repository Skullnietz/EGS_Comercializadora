<?php
/* ═══════════════════════════════════════════════════
   SEGUIMIENTO DE ORDEN — Timeline de trazabilidad
   Muestra cronológicamente: cambios de estado,
   traspasos de técnico y observaciones
   ═══════════════════════════════════════════════════ */

if (!in_array($_SESSION["perfil"], array("administrador","vendedor","tecnico","secretaria"))) {
  echo '<script>window.location = "index.php?ruta=inicio";</script>';
  return;
}

date_default_timezone_set("America/Mexico_City");

// ── Validar que venga idOrden ──
if (!isset($_GET["idOrden"]) || intval($_GET["idOrden"]) <= 0) {
  echo '<div style="text-align:center;padding:60px 20px;color:#94a3b8">
    <i class="fa-solid fa-triangle-exclamation" style="font-size:48px;margin-bottom:12px;display:block"></i>
    <h3 style="color:#0f172a">No se especificó una orden</h3>
    <p>Selecciona una orden para ver su seguimiento.</p>
    <a href="index.php?ruta=ordenes" class="btn" style="background:#6366f1;color:#fff;border-radius:8px;margin-top:12px">
      <i class="fa-solid fa-arrow-left"></i> Ir a órdenes
    </a>
  </div>';
  return;
}

$_seg_idOrden = intval($_GET["idOrden"]);

// ── Obtener datos de la orden ──
$_seg_ordenes = controladorOrdenes::ctrMostrarordenesParaValidar("id", $_seg_idOrden);
if (!is_array($_seg_ordenes) || empty($_seg_ordenes)) {
  echo '<div style="text-align:center;padding:60px 20px;color:#94a3b8">
    <i class="fa-solid fa-file-circle-xmark" style="font-size:48px;margin-bottom:12px;display:block"></i>
    <h3 style="color:#0f172a">Orden no encontrada</h3>
    <a href="index.php?ruta=ordenes" class="btn" style="background:#6366f1;color:#fff;border-radius:8px;margin-top:12px">
      <i class="fa-solid fa-arrow-left"></i> Ir a órdenes
    </a>
  </div>';
  return;
}

$_seg_orden = $_seg_ordenes[0];
$_seg_estado = isset($_seg_orden["estado"]) ? $_seg_orden["estado"] : "";
$_seg_marca = isset($_seg_orden["marcaDelEquipo"]) ? $_seg_orden["marcaDelEquipo"] : "";
$_seg_modelo = isset($_seg_orden["modeloDelEquipo"]) ? $_seg_orden["modeloDelEquipo"] : "";
$_seg_equipo = trim($_seg_marca . " " . $_seg_modelo);
$_seg_fechaIngreso = isset($_seg_orden["fecha_ingreso"]) ? $_seg_orden["fecha_ingreso"] : "";
$_seg_fechaSalida = isset($_seg_orden["fecha_Salida"]) ? $_seg_orden["fecha_Salida"] : "";
$_seg_portada = isset($_seg_orden["portada"]) ? $_seg_orden["portada"] : "";

// Cliente
$_seg_idCliente = isset($_seg_orden["id_usuario"]) ? $_seg_orden["id_usuario"] : 0;
$_seg_cliente = ControladorClientes::ctrMostrarClientesOrdenes("id", $_seg_idCliente);
$_seg_nombreCliente = is_array($_seg_cliente) && isset($_seg_cliente["nombre"]) ? $_seg_cliente["nombre"] : "Sin cliente";

// Técnico
$_seg_idTec = isset($_seg_orden["id_tecnico"]) ? intval($_seg_orden["id_tecnico"]) : 0;
$_seg_tecnico = null;
if ($_seg_idTec > 0) {
  try { $_seg_tecnico = ControladorTecnicos::ctrMostrarTecnicos("id", $_seg_idTec); } catch (Exception $e) {}
}
$_seg_nombreTec = is_array($_seg_tecnico) && isset($_seg_tecnico["nombre"]) ? $_seg_tecnico["nombre"] : "—";

// ── Obtener historial de cambios de estado ──
$_seg_historial = array();
try {
  ControladorNotificaciones::ctrCrearTablaEstado();
  $_seg_historial = ControladorNotificaciones::ctrHistorialOrden($_seg_idOrden);
  if (!is_array($_seg_historial)) $_seg_historial = array();
} catch (Exception $e) { $_seg_historial = array(); }

// ── Obtener observaciones de la orden ──
$_seg_observaciones = array();
try {
  $_seg_observaciones = controladorObservaciones::ctrMostrarObservaciones($_seg_idOrden);
  if (!is_array($_seg_observaciones)) $_seg_observaciones = array();
} catch (Exception $e) { $_seg_observaciones = array(); }

// ── Unificar en timeline cronológico ──
$_seg_timeline = array();

// Agregar evento de ingreso de la orden
if (!empty($_seg_fechaIngreso)) {
  $_seg_timeline[] = array(
    'tipo'  => 'ingreso',
    'fecha' => $_seg_fechaIngreso,
    'data'  => array('estado' => 'Orden creada')
  );
}

// Agregar cambios de estado y traspasos
foreach ($_seg_historial as $h) {
  $_seg_timeline[] = array(
    'tipo'  => isset($h['tipo']) ? $h['tipo'] : 'estado',
    'fecha' => $h['fecha'],
    'data'  => $h
  );
}

// Agregar observaciones
foreach ($_seg_observaciones as $obs) {
  $_seg_timeline[] = array(
    'tipo'  => 'observacion',
    'fecha' => isset($obs['fecha']) ? $obs['fecha'] : '',
    'data'  => $obs
  );
}

// Agregar evento de entrega si existe
if (!empty($_seg_fechaSalida) && $_seg_fechaSalida !== "0000-00-00" && $_seg_fechaSalida !== "0000-00-00 00:00:00") {
  $_seg_timeline[] = array(
    'tipo'  => 'entrega',
    'fecha' => $_seg_fechaSalida,
    'data'  => array('estado' => 'Entregado')
  );
}

// Ordenar por fecha descendente
usort($_seg_timeline, function ($a, $b) {
  $ta = strtotime($a['fecha']);
  $tb = strtotime($b['fecha']);
  if ($ta === false) $ta = 0;
  if ($tb === false) $tb = 0;
  return $tb - $ta;
});

// ── Helper: color por estado (misma lógica que notificaciones) ──
if (!function_exists('_segEstadoColor')) {
  function _segEstadoColor($estado) {
    $e = strtolower($estado);
    // 1. Garantía
    if (strpos($e, 'garantía') !== false || strpos($e, 'garantia') !== false)
      return array('#dc2626', '#fef2f2', 'fa-rotate-left');
    // 2. Sin reparación (SR)
    if (strpos($e, 'sin reparación') !== false || strpos($e, 'sin reparacion') !== false || strpos($estado, '(SR)') !== false)
      return array('#78716c', '#f5f5f4', 'fa-ban');
    // 3. Cancelada (can)
    if (strpos($e, 'cancelada') !== false || strpos($estado, '(can)') !== false)
      return array('#6b7280', '#f3f4f6', 'fa-circle-xmark');
    // 4. Producto para venta (PV)
    if (strpos($e, 'producto para venta') !== false || strpos($estado, '(PV)') !== false)
      return array('#d97706', '#fffbeb', 'fa-tag');
    // 5. Pendiente de autorización (AUT)
    if (strpos($e, 'autorización') !== false || strpos($e, 'autorizacion') !== false || strpos($estado, 'AUT') !== false)
      return array('#f59e0b', '#fffbeb', 'fa-hourglass-half');
    // 6. Supervisión (SUP)
    if (strpos($e, 'supervisión') !== false || strpos($e, 'supervision') !== false || strpos($estado, 'SUP') !== false)
      return array('#8b5cf6', '#f5f3ff', 'fa-eye');
    // 7. En revisión (REV)
    if (strpos($e, 'revisión') !== false || strpos($e, 'revision') !== false || strpos($estado, 'REV') !== false)
      return array('#ef4444', '#fef2f2', 'fa-magnifying-glass');
    // 8. Aceptado (ok)
    if (strpos($e, 'aceptado') !== false || strpos($estado, '(ok)') !== false)
      return array('#3b82f6', '#eff6ff', 'fa-circle-check');
    // 9. Terminada (ter)
    if (strpos($e, 'terminada') !== false || strpos($estado, '(ter)') !== false)
      return array('#06b6d4', '#ecfeff', 'fa-flag-checkered');
    // 10. Entregado (Ent) — al final para que "ent" no matchee "pendiente"
    if (strpos($e, 'entregado') !== false || strpos($estado, '(Ent)') !== false)
      return array('#22c55e', '#f0fdf4', 'fa-handshake');

    return array('#64748b', '#f1f5f9', 'fa-circle-info');
  }
}

// Badge del estado actual
$_seg_estadoColor = _segEstadoColor($_seg_estado);
?>

<style>
  .seg-card { background:#fff; border-radius:14px; box-shadow:0 1px 4px rgba(0,0,0,.06); overflow:hidden; margin-bottom:20px; }
  .seg-header { background:linear-gradient(135deg,#6366f1 0%,#818cf8 100%); padding:20px 24px; color:#fff; }
  .seg-body { padding:24px; }

  /* Timeline */
  .seg-timeline { position:relative; padding-left:40px; }
  .seg-timeline::before {
    content:''; position:absolute; left:15px; top:0; bottom:0; width:2px;
    background:linear-gradient(180deg,#6366f1 0%,#e2e8f0 100%);
  }
  .seg-event { position:relative; margin-bottom:24px; }
  .seg-event:last-child { margin-bottom:0; }
  .seg-dot {
    position:absolute; left:-40px; top:2px;
    width:32px; height:32px; border-radius:50%;
    display:flex; align-items:center; justify-content:center;
    font-size:12px; z-index:1; border:3px solid #fff;
    box-shadow:0 2px 6px rgba(0,0,0,.1);
  }
  .seg-event-card {
    background:#fff; border:1px solid #f1f5f9; border-radius:10px;
    padding:14px 18px; transition:box-shadow .15s;
  }
  .seg-event-card:hover { box-shadow:0 4px 12px rgba(0,0,0,.08); }
  .seg-event-time {
    font-size:11px; color:#94a3b8; font-weight:500;
    display:flex; align-items:center; gap:6px; margin-bottom:6px;
  }
  .seg-event-title { font-size:13px; font-weight:600; color:#0f172a; line-height:1.4; }
  .seg-event-detail { font-size:12px; color:#64748b; margin-top:4px; line-height:1.5; }
  .seg-badge {
    display:inline-flex; align-items:center; gap:4px;
    font-size:11px; font-weight:600; padding:3px 10px;
    border-radius:12px;
  }
  .seg-arrow { font-size:10px; margin:0 4px; opacity:.6; }

  /* Responsive */
  @media (max-width:768px) {
    .seg-timeline { padding-left:32px; }
    .seg-dot { left:-32px; width:26px; height:26px; font-size:10px; }
    .seg-header-grid { flex-direction:column !important; gap:12px !important; }
  }
</style>

<div class="content-wrapper" style="padding:16px">
  <section class="content">

    <!-- ══ Breadcrumb ══ -->
    <div style="margin-bottom:16px;display:flex;align-items:center;gap:8px;flex-wrap:wrap">
      <a href="index.php?ruta=ordenes" style="color:#6366f1;font-size:12px;font-weight:600;text-decoration:none">
        <i class="fa-solid fa-list"></i> Órdenes
      </a>
      <i class="fa-solid fa-chevron-right" style="font-size:8px;color:#cbd5e1"></i>
      <a href="index.php?ruta=infoOrden&idOrden=<?php echo $_seg_idOrden; ?>" style="color:#6366f1;font-size:12px;font-weight:600;text-decoration:none">
        Orden #<?php echo $_seg_idOrden; ?>
      </a>
      <i class="fa-solid fa-chevron-right" style="font-size:8px;color:#cbd5e1"></i>
      <span style="font-size:12px;color:#64748b;font-weight:500">Seguimiento</span>
    </div>

    <!-- ══ Header Card ══ -->
    <div class="seg-card">
      <div class="seg-header">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px" class="seg-header-grid">
          <div style="display:flex;align-items:center;gap:16px">
            <div style="width:56px;height:56px;border-radius:12px;overflow:hidden;background:rgba(255,255,255,.15);flex-shrink:0">
              <img src="<?php echo !empty($_seg_portada) ? htmlspecialchars($_seg_portada) : 'vistas/img/default/default.png'; ?>"
                   onerror="this.onerror=null;this.src='vistas/img/default/default.png'"
                   style="width:100%;height:100%;object-fit:cover">
            </div>
            <div>
              <h2 style="margin:0;font-size:20px;font-weight:800;letter-spacing:-.02em">
                <i class="fa-solid fa-route" style="margin-right:6px;opacity:.7"></i>
                Seguimiento — Orden #<?php echo $_seg_idOrden; ?>
              </h2>
              <p style="margin:4px 0 0;font-size:13px;opacity:.8">
                <?php echo !empty($_seg_equipo) ? htmlspecialchars($_seg_equipo) : 'Sin equipo registrado'; ?>
                &mdash; <?php echo htmlspecialchars($_seg_nombreCliente); ?>
              </p>
            </div>
          </div>
          <div style="display:flex;align-items:center;gap:10px">
            <span class="seg-badge" style="background:<?php echo $_seg_estadoColor[1]; ?>;color:<?php echo $_seg_estadoColor[0]; ?>;font-size:12px;padding:5px 14px">
              <i class="fa-solid <?php echo $_seg_estadoColor[2]; ?>" style="font-size:10px"></i>
              <?php echo htmlspecialchars($_seg_estado); ?>
            </span>
            <a href="index.php?ruta=infoOrden&idOrden=<?php echo $_seg_idOrden; ?>" class="btn btn-sm"
               style="background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.3);border-radius:8px;font-size:12px;font-weight:600">
              <i class="fa-solid fa-pen-to-square"></i> Ver orden
            </a>
          </div>
        </div>
      </div>

      <!-- Info rápida -->
      <div style="display:flex;border-bottom:1px solid #f1f5f9;flex-wrap:wrap">
        <div style="flex:1;min-width:140px;padding:14px 20px;text-align:center;border-right:1px solid #f1f5f9">
          <div style="font-size:18px;font-weight:800;color:#0f172a"><?php echo count($_seg_historial); ?></div>
          <div style="font-size:10px;color:#94a3b8;font-weight:500">Cambios de estado</div>
        </div>
        <div style="flex:1;min-width:140px;padding:14px 20px;text-align:center;border-right:1px solid #f1f5f9">
          <div style="font-size:18px;font-weight:800;color:#0f172a"><?php echo count($_seg_observaciones); ?></div>
          <div style="font-size:10px;color:#94a3b8;font-weight:500">Observaciones</div>
        </div>
        <div style="flex:1;min-width:140px;padding:14px 20px;text-align:center;border-right:1px solid #f1f5f9">
          <div style="font-size:18px;font-weight:800;color:#6366f1"><?php echo htmlspecialchars($_seg_nombreTec); ?></div>
          <div style="font-size:10px;color:#94a3b8;font-weight:500">Técnico asignado</div>
        </div>
        <div style="flex:1;min-width:140px;padding:14px 20px;text-align:center">
          <div style="font-size:18px;font-weight:800;color:#0f172a">
            <?php
              if (!empty($_seg_fechaIngreso)) {
                $dias = max(0, floor((time() - strtotime($_seg_fechaIngreso)) / 86400));
                echo $dias . 'd';
              } else echo '—';
            ?>
          </div>
          <div style="font-size:10px;color:#94a3b8;font-weight:500">Días transcurridos</div>
        </div>
      </div>
    </div>

    <!-- ══ Timeline ══ -->
    <div class="seg-card">
      <div style="padding:18px 24px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between">
        <h4 style="margin:0;font-size:15px;font-weight:700;color:#0f172a">
          <i class="fa-solid fa-timeline" style="color:#6366f1;margin-right:6px"></i>
          Línea de tiempo
        </h4>
        <span style="font-size:11px;color:#94a3b8;font-weight:500">
          <?php echo count($_seg_timeline); ?> eventos
        </span>
      </div>

      <div class="seg-body">
        <?php if (empty($_seg_timeline)): ?>
          <div style="text-align:center;padding:40px 20px;color:#94a3b8">
            <i class="fa-solid fa-clock-rotate-left" style="font-size:36px;margin-bottom:10px;display:block;opacity:.5"></i>
            <strong style="color:#64748b">Sin movimientos registrados</strong>
            <p style="font-size:12px;margin-top:4px">Los cambios de estado y observaciones aparecerán aquí</p>
          </div>
        <?php else: ?>
          <div class="seg-timeline">
            <?php foreach ($_seg_timeline as $ev):
              $evTipo = $ev['tipo'];
              $evFecha = $ev['fecha'];
              $evData = $ev['data'];
              $fechaFmt = !empty($evFecha) ? date('d/m/Y H:i', strtotime($evFecha)) : '';
              $fechaDia = !empty($evFecha) ? date('d M Y', strtotime($evFecha)) : '';
              $fechaHora = !empty($evFecha) ? date('H:i', strtotime($evFecha)) : '';

              if ($evTipo === 'ingreso'):
                // ── Ingreso de la orden ──
            ?>
              <div class="seg-event">
                <div class="seg-dot" style="background:#22c55e;color:#fff"><i class="fa-solid fa-plus"></i></div>
                <div class="seg-event-card" style="border-left:3px solid #22c55e">
                  <div class="seg-event-time">
                    <i class="fa-regular fa-calendar"></i> <?php echo $fechaFmt; ?>
                  </div>
                  <div class="seg-event-title" style="color:#16a34a">
                    <i class="fa-solid fa-file-circle-plus" style="margin-right:4px"></i>
                    Orden ingresada al sistema
                  </div>
                </div>
              </div>

            <?php elseif ($evTipo === 'entrega'):
                // ── Entrega ──
            ?>
              <div class="seg-event">
                <div class="seg-dot" style="background:#22c55e;color:#fff"><i class="fa-solid fa-handshake"></i></div>
                <div class="seg-event-card" style="border-left:3px solid #22c55e">
                  <div class="seg-event-time">
                    <i class="fa-regular fa-calendar"></i> <?php echo $fechaFmt; ?>
                  </div>
                  <div class="seg-event-title" style="color:#16a34a">
                    <i class="fa-solid fa-truck" style="margin-right:4px"></i>
                    Equipo entregado al cliente
                  </div>
                </div>
              </div>

            <?php elseif ($evTipo === 'traspaso'):
                // ── Traspaso de técnico ──
                $usuario = isset($evData['nombre_usuario']) ? $evData['nombre_usuario'] : '';
                $colorTrasAnterior = _segEstadoColor(isset($evData['estado_anterior']) ? $evData['estado_anterior'] : '');
                $colorTrasNuevo = _segEstadoColor(isset($evData['estado_nuevo']) ? $evData['estado_nuevo'] : '');
            ?>
              <div class="seg-event">
                <div class="seg-dot" style="background:#8b5cf6;color:#fff"><i class="fa-solid fa-people-arrows"></i></div>
                <div class="seg-event-card" style="border-left:3px solid #8b5cf6">
                  <div class="seg-event-time">
                    <i class="fa-regular fa-clock"></i> <?php echo $fechaFmt; ?>
                    <?php if (!empty($usuario)): ?>
                      <span style="margin-left:auto;color:#8b5cf6;font-weight:600">
                        <i class="fa-solid fa-user" style="font-size:8px"></i> <?php echo htmlspecialchars($usuario); ?>
                      </span>
                    <?php endif; ?>
                  </div>
                  <div class="seg-event-title">
                    <i class="fa-solid fa-people-arrows" style="color:#8b5cf6;margin-right:4px"></i>
                    Traspaso de técnico
                  </div>
                  <div class="seg-event-detail">
                    <span class="seg-badge" style="background:<?php echo $colorTrasAnterior[1]; ?>;color:<?php echo $colorTrasAnterior[0]; ?>">
                      <i class="fa-solid <?php echo $colorTrasAnterior[2]; ?>" style="font-size:9px"></i>
                      <?php echo htmlspecialchars($evData['estado_anterior']); ?>
                    </span>
                    <i class="fa-solid fa-arrow-right seg-arrow" style="color:#8b5cf6"></i>
                    <span class="seg-badge" style="background:<?php echo $colorTrasNuevo[0]; ?>;color:#fff">
                      <i class="fa-solid <?php echo $colorTrasNuevo[2]; ?>" style="font-size:9px"></i>
                      <?php echo htmlspecialchars($evData['estado_nuevo']); ?>
                    </span>
                  </div>
                </div>
              </div>

            <?php elseif ($evTipo === 'estado'):
                // ── Cambio de estado ──
                $colorAnterior = _segEstadoColor(isset($evData['estado_anterior']) ? $evData['estado_anterior'] : '');
                $colorNuevo = _segEstadoColor(isset($evData['estado_nuevo']) ? $evData['estado_nuevo'] : '');
                $usuario = isset($evData['nombre_usuario']) ? $evData['nombre_usuario'] : '';
            ?>
              <div class="seg-event">
                <div class="seg-dot" style="background:<?php echo $colorNuevo[0]; ?>;color:#fff">
                  <i class="fa-solid <?php echo $colorNuevo[2]; ?>"></i>
                </div>
                <div class="seg-event-card" style="border-left:3px solid <?php echo $colorNuevo[0]; ?>">
                  <div class="seg-event-time">
                    <i class="fa-regular fa-clock"></i> <?php echo $fechaFmt; ?>
                    <?php if (!empty($usuario)): ?>
                      <span style="margin-left:auto;color:<?php echo $colorNuevo[0]; ?>;font-weight:600">
                        <i class="fa-solid fa-user" style="font-size:8px"></i> <?php echo htmlspecialchars($usuario); ?>
                      </span>
                    <?php endif; ?>
                  </div>
                  <div class="seg-event-title">
                    <i class="fa-solid fa-arrow-right-arrow-left" style="color:<?php echo $colorNuevo[0]; ?>;margin-right:4px"></i>
                    Cambio de estado
                  </div>
                  <div class="seg-event-detail">
                    <span class="seg-badge" style="background:<?php echo $colorAnterior[1]; ?>;color:<?php echo $colorAnterior[0]; ?>">
                      <i class="fa-solid <?php echo $colorAnterior[2]; ?>" style="font-size:9px"></i>
                      <?php echo htmlspecialchars($evData['estado_anterior']); ?>
                    </span>
                    <i class="fa-solid fa-arrow-right seg-arrow"></i>
                    <span class="seg-badge" style="background:<?php echo $colorNuevo[0]; ?>;color:#fff">
                      <i class="fa-solid <?php echo $colorNuevo[2]; ?>" style="font-size:9px"></i>
                      <?php echo htmlspecialchars($evData['estado_nuevo']); ?>
                    </span>
                  </div>
                </div>
              </div>

            <?php elseif ($evTipo === 'observacion'):
                // ── Observación ──
                $obsTexto = isset($evData['observacion']) ? $evData['observacion'] : '';
                $obsCreador = isset($evData['creador_nombre']) ? $evData['creador_nombre'] : (isset($evData['nombre']) ? $evData['nombre'] : 'Usuario');
                $obsPerfil = isset($evData['creador_perfil']) ? $evData['creador_perfil'] : '';
                // Color por perfil
                $obsColor = '#f59e0b'; $obsBg = '#fffbeb';
                if ($obsPerfil === 'administrador') { $obsColor = '#6366f1'; $obsBg = '#eef2ff'; }
                elseif ($obsPerfil === 'tecnico') { $obsColor = '#06b6d4'; $obsBg = '#ecfeff'; }
                elseif ($obsPerfil === 'vendedor') { $obsColor = '#8b5cf6'; $obsBg = '#f5f3ff'; }
            ?>
              <div class="seg-event">
                <div class="seg-dot" style="background:<?php echo $obsColor; ?>;color:#fff"><i class="fa-solid fa-comment-dots"></i></div>
                <div class="seg-event-card" style="border-left:3px solid <?php echo $obsColor; ?>">
                  <div class="seg-event-time">
                    <i class="fa-regular fa-clock"></i> <?php echo $fechaFmt; ?>
                    <span style="margin-left:auto;font-weight:600;color:<?php echo $obsColor; ?>">
                      <i class="fa-solid fa-user" style="font-size:8px"></i> <?php echo htmlspecialchars($obsCreador); ?>
                      <?php if (!empty($obsPerfil)): ?>
                        <span class="seg-badge" style="background:<?php echo $obsBg; ?>;color:<?php echo $obsColor; ?>;font-size:9px;padding:1px 6px;margin-left:4px">
                          <?php echo htmlspecialchars($obsPerfil); ?>
                        </span>
                      <?php endif; ?>
                    </span>
                  </div>
                  <div class="seg-event-title">
                    <i class="fa-solid fa-comment-dots" style="color:<?php echo $obsColor; ?>;margin-right:4px"></i>
                    Observación
                  </div>
                  <div class="seg-event-detail" style="background:<?php echo $obsBg; ?>;padding:10px 14px;border-radius:8px;margin-top:6px;font-size:13px;color:#334155">
                    <?php echo nl2br(htmlspecialchars($obsTexto)); ?>
                  </div>
                </div>
              </div>

            <?php endif; ?>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>

  </section>
</div>
