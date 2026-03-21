<?php
/*  ═══════════════════════════════════════════════════
    DASHBOARD ADMIN — Actividad Reciente (Cambios de Estado)
    Muestra las órdenes que tuvieron movimiento hoy
    (ingreso o salida) con su estado actual.
    ═══════════════════════════════════════════════════ */

// Mapeo de estado a info visual
if (!function_exists('_actEstadoInfo')) {
    function _actEstadoInfo($estado) {
        $e = strtolower($estado);
        if (strpos($e, 'entregado') !== false || strpos($e, 'ent') !== false)
            return array('label' => 'Entregado', 'color' => '#22c55e', 'bg' => '#f0fdf4', 'icon' => 'fa-handshake');
        if (strpos($e, 'terminada') !== false || strpos($e, 'ter') !== false)
            return array('label' => 'Terminada', 'color' => '#06b6d4', 'bg' => '#ecfeff', 'icon' => 'fa-flag-checkered');
        if (strpos($e, 'aceptado') !== false || strpos($e, 'ok') !== false)
            return array('label' => 'Aceptado', 'color' => '#3b82f6', 'bg' => '#eff6ff', 'icon' => 'fa-circle-check');
        if (strpos($e, 'revisión') !== false || strpos($e, 'rev') !== false)
            return array('label' => 'En Revisión', 'color' => '#ef4444', 'bg' => '#fef2f2', 'icon' => 'fa-magnifying-glass');
        if (strpos($e, 'autorización') !== false || strpos($e, 'aut') !== false)
            return array('label' => 'Por Autorizar', 'color' => '#f59e0b', 'bg' => '#fffbeb', 'icon' => 'fa-hourglass-half');
        if (strpos($e, 'supervisión') !== false || strpos($e, 'sup') !== false)
            return array('label' => 'Supervisión', 'color' => '#8b5cf6', 'bg' => '#f5f3ff', 'icon' => 'fa-eye');
        if (strpos($e, 'cancel') !== false)
            return array('label' => 'Cancelada', 'color' => '#64748b', 'bg' => '#f1f5f9', 'icon' => 'fa-ban');
        if (strpos($e, 'garantía') !== false || strpos($e, 'garantia') !== false)
            return array('label' => 'Garantía', 'color' => '#dc2626', 'bg' => '#fef2f2', 'icon' => 'fa-rotate-left');
        if (strpos($e, 'sin reparación') !== false || strpos($e, 'sr') !== false)
            return array('label' => 'Sin Reparación', 'color' => '#94a3b8', 'bg' => '#f8fafc', 'icon' => 'fa-xmark');
        return array('label' => $estado, 'color' => '#64748b', 'bg' => '#f1f5f9', 'icon' => 'fa-circle-info');
    }
}

if (!function_exists('_actTiempoRelativo')) {
    function _actTiempoRelativo($fecha) {
        $diff = time() - strtotime($fecha);
        if ($diff < 60) return 'Hace un momento';
        if ($diff < 3600) return 'Hace ' . floor($diff / 60) . ' min';
        if ($diff < 86400) return 'Hace ' . floor($diff / 3600) . 'h';
        if ($diff < 172800) return 'Ayer';
        return date('d/m/Y H:i', strtotime($fecha));
    }
}

// Detectar actividad del día desde las órdenes (ingreso/salida)
// + cambios de estado registrados en notificaciones_estado
$_act_hoy = date('Y-m-d');
$_act_actividad = array();
$_act_ordenesVistos = array(); // evitar duplicados

// Indexar órdenes por ID para lookup rápido
$_act_ordIndex = array();
if (isset($_adm_allOrders) && is_array($_adm_allOrders)) {
    foreach ($_adm_allOrders as $ord) {
        $_act_ordIndex[intval($ord['id'])] = $ord;
    }
}

// 1) Obtener TODOS los cambios de estado de HOY desde notificaciones_estado
try {
    ControladorNotificaciones::ctrCrearTablaEstado();
    $pdo_act = ConexionWP::conectarWP();
    $stmt_act = $pdo_act->prepare(
        "SELECT * FROM notificaciones_estado
         WHERE id_empresa = :empresa AND DATE(fecha) = CURDATE()
         ORDER BY fecha DESC
         LIMIT 50"
    );
    $stmt_act->bindParam(":empresa", $_SESSION["empresa"], PDO::PARAM_INT);
    $stmt_act->execute();
    $_act_notifsHoy = $stmt_act->fetchAll(PDO::FETCH_ASSOC);

    if (is_array($_act_notifsHoy)) {
        foreach ($_act_notifsHoy as $nf) {
            $nfIdOrden = intval($nf['id_orden']);
            $nfTipo = isset($nf['tipo']) ? $nf['tipo'] : 'estado';
            $nfEstado = $nf['estado_nuevo'];
            $eInfo = _actEstadoInfo($nfEstado);

            // Datos de la orden (si disponible)
            $ordData = isset($_act_ordIndex[$nfIdOrden]) ? $_act_ordIndex[$nfIdOrden] : array();

            $tipoEvento = 'cambio_estado';
            if ($nfTipo === 'traspaso') $tipoEvento = 'traspaso';

            $_act_actividad[] = array(
                'id_orden'  => $nfIdOrden,
                'estado'    => $nfEstado,
                'estado_anterior' => isset($nf['estado_anterior']) ? $nf['estado_anterior'] : '',
                'info'      => $eInfo,
                'fecha'     => $nf['fecha'],
                'tipo'      => $tipoEvento,
                'equipo'    => isset($ordData['equipo']) ? $ordData['equipo'] : '',
                'marca'     => isset($ordData['marca']) ? $ordData['marca'] : (isset($ordData['marcaDelEquipo']) ? $ordData['marcaDelEquipo'] : ''),
                'nombre'    => isset($ordData['nombre']) ? $ordData['nombre'] : (isset($nf['nombre_usuario']) ? $nf['nombre_usuario'] : ''),
                'orden'     => !empty($ordData) ? $ordData : array('id' => $nfIdOrden, 'id_empresa' => isset($nf['id_empresa']) ? $nf['id_empresa'] : '', 'id_Asesor' => isset($nf['id_asesor']) ? $nf['id_asesor'] : '', 'id_tecnico' => isset($nf['id_tecnico']) ? $nf['id_tecnico'] : '', 'id_usuario' => '', 'id_pedido' => '', 'id_tecnicoDos' => ''),
                'usuario_accion' => isset($nf['nombre_usuario']) ? $nf['nombre_usuario'] : '',
            );
            $_act_ordenesVistos[$nfIdOrden . '_' . $nf['fecha']] = true;
        }
    }
} catch (Exception $e) {}

// 2) También incluir ingresos/salidas de hoy que no estén en notificaciones
if (isset($_adm_allOrders) && is_array($_adm_allOrders)) {
    foreach ($_adm_allOrders as $ord) {
        $estado = isset($ord['estado']) ? $ord['estado'] : '';
        if (empty($estado)) continue;

        $fi = isset($ord['fecha_ingreso']) ? substr($ord['fecha_ingreso'], 0, 10) : '';
        $fs = !empty($ord['fecha_Salida']) ? substr($ord['fecha_Salida'], 0, 10) : '';

        if ($fi === $_act_hoy) {
            $eInfo = _actEstadoInfo($estado);
            $_act_actividad[] = array(
                'id_orden'  => $ord['id'],
                'estado'    => $estado,
                'info'      => $eInfo,
                'fecha'     => $ord['fecha_ingreso'],
                'tipo'      => 'ingreso',
                'equipo'    => isset($ord['equipo']) ? $ord['equipo'] : '',
                'marca'     => isset($ord['marca']) ? $ord['marca'] : '',
                'nombre'    => isset($ord['nombre']) ? $ord['nombre'] : '',
                'orden'     => $ord,
            );
        }
        if (!empty($fs) && $fs === $_act_hoy && $fi !== $_act_hoy) {
            $eInfo = _actEstadoInfo($estado);
            $_act_actividad[] = array(
                'id_orden'  => $ord['id'],
                'estado'    => $estado,
                'info'      => $eInfo,
                'fecha'     => $ord['fecha_Salida'],
                'tipo'      => 'salida',
                'equipo'    => isset($ord['equipo']) ? $ord['equipo'] : '',
                'marca'     => isset($ord['marca']) ? $ord['marca'] : '',
                'nombre'    => isset($ord['nombre']) ? $ord['nombre'] : '',
                'orden'     => $ord,
            );
        }
    }
}

usort($_act_actividad, function($a, $b) {
    return strcmp($b['fecha'], $a['fecha']);
});

$_act_actividad = array_slice($_act_actividad, 0, 20);

// Resumen por estado
$_act_resumen = array();
foreach ($_act_actividad as $act) {
    $lbl = $act['info']['label'];
    if (!isset($_act_resumen[$lbl])) {
        $_act_resumen[$lbl] = array('count' => 0, 'info' => $act['info']);
    }
    $_act_resumen[$lbl]['count']++;
}
?>

<div class="crm-card" style="margin-bottom:20px">
  <div class="crm-card-head">
    <h4 class="crm-card-title"><i class="fa-solid fa-arrow-right-arrow-left"></i> Actividad Reciente</h4>
    <div style="display:flex;align-items:center;gap:8px">
      <span class="crm-badge" style="background:#f1f5f9;color:#475569">
        <?php echo count($_act_actividad); ?> hoy
      </span>
      <a href="index.php?ruta=cambios-estado-hoy"
         style="display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:600;color:#6366f1;background:#eef2ff;border:1px solid #c7d2fe;padding:4px 10px;border-radius:8px;text-decoration:none;transition:background .15s"
         onmouseover="this.style.background='#e0e7ff'" onmouseout="this.style.background='#eef2ff'">
        <i class="fa-solid fa-calendar-day" style="font-size:10px"></i> Ver todos del día
      </a>
    </div>
  </div>

  <?php if (!empty($_act_resumen)): ?>
  <!-- Mini resumen de estados -->
  <div style="display:flex;flex-wrap:wrap;gap:6px;padding:10px 18px;border-bottom:1px solid #f1f5f9">
    <?php foreach ($_act_resumen as $lbl => $r): ?>
      <span style="display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:600;color:<?php echo $r['info']['color']; ?>;background:<?php echo $r['info']['bg']; ?>;padding:3px 10px;border-radius:12px;border:1px solid <?php echo $r['info']['color']; ?>20">
        <i class="fa-solid <?php echo $r['info']['icon']; ?>" style="font-size:9px"></i>
        <?php echo $r['count']; ?> <?php echo htmlspecialchars($lbl); ?>
      </span>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <div class="crm-card-body-flush">
    <?php if (empty($_act_actividad)): ?>
      <div class="crm-empty">
        <i class="fa-solid fa-arrow-right-arrow-left"></i>
        <strong>Sin cambios de estado hoy</strong>
        <span style="font-size:12px">Los movimientos de órdenes del día aparecerán aquí</span>
      </div>
    <?php else: ?>
      <div style="max-height:500px;overflow-y:auto">
        <?php foreach ($_act_actividad as $i => $act):
          $eInfo = $act['info'];
          $idOrden = $act['id_orden'];
          $tiempo = _actTiempoRelativo($act['fecha']);
          $descripcion = '';
          if ($act['tipo'] === 'ingreso') {
              $descripcion = 'Orden ingresada';
          } elseif ($act['tipo'] === 'traspaso') {
              $descripcion = 'Traspaso: ' . (isset($act['estado_anterior']) ? $act['estado_anterior'] : '') . ' → ' . $act['estado'];
          } elseif ($act['tipo'] === 'cambio_estado' && !empty($act['estado_anterior'])) {
              $descripcion = $act['estado_anterior'] . ' → ' . $eInfo['label'];
          } else {
              $descripcion = 'Estado: ' . $eInfo['label'];
          }
          if (!empty($act['equipo'])) $descripcion .= ' — ' . $act['equipo'];
          if (!empty($act['marca'])) $descripcion .= ' ' . $act['marca'];
        ?>
          <div style="display:flex;gap:12px;padding:12px 18px;border-bottom:1px solid #f1f5f9;transition:background .12s;align-items:center"
               onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">

            <!-- Icono de estado -->
            <div style="width:36px;height:36px;border-radius:50%;background:<?php echo $eInfo['bg']; ?>;display:flex;align-items:center;justify-content:center;flex-shrink:0;border:2px solid <?php echo $eInfo['color']; ?>30">
              <i class="fa-solid <?php echo $eInfo['icon']; ?>" style="font-size:14px;color:<?php echo $eInfo['color']; ?>"></i>
            </div>

            <!-- Contenido -->
            <div style="flex:1;min-width:0">
              <div style="display:flex;align-items:center;gap:8px;margin-bottom:2px;flex-wrap:wrap">
                <span style="display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:700;color:<?php echo $eInfo['color']; ?>;background:<?php echo $eInfo['bg']; ?>;padding:2px 8px;border-radius:8px">
                  <i class="fa-solid <?php echo $eInfo['icon']; ?>" style="font-size:8px"></i>
                  <?php echo htmlspecialchars($eInfo['label']); ?>
                </span>
                <?php if ($act['tipo'] === 'ingreso'): ?>
                  <span style="font-size:10px;font-weight:600;color:#3b82f6;background:#eff6ff;padding:2px 6px;border-radius:6px">
                    <i class="fa-solid fa-arrow-right-to-bracket" style="font-size:7px"></i> Ingreso
                  </span>
                <?php elseif ($act['tipo'] === 'traspaso'): ?>
                  <span style="font-size:10px;font-weight:600;color:#8b5cf6;background:#f5f3ff;padding:2px 6px;border-radius:6px">
                    <i class="fa-solid fa-people-arrows" style="font-size:7px"></i> Traspaso
                  </span>
                <?php elseif ($act['tipo'] === 'cambio_estado'): ?>
                  <span style="font-size:10px;font-weight:600;color:#f59e0b;background:#fffbeb;padding:2px 6px;border-radius:6px">
                    <i class="fa-solid fa-arrow-right-arrow-left" style="font-size:7px"></i> Cambio
                  </span>
                <?php else: ?>
                  <span style="font-size:10px;font-weight:600;color:#22c55e;background:#f0fdf4;padding:2px 6px;border-radius:6px">
                    <i class="fa-solid fa-arrow-right-from-bracket" style="font-size:7px"></i> Salida
                  </span>
                <?php endif; ?>
                <?php if (!empty($act['nombre'])): ?>
                  <span style="font-size:12px;color:#475569"><?php echo htmlspecialchars($act['nombre']); ?></span>
                <?php endif; ?>
                <span style="font-size:11px;color:#94a3b8;margin-left:auto;flex-shrink:0"><?php echo $tiempo; ?></span>
              </div>
              <div style="font-size:12px;color:#64748b;line-height:1.4">
                <?php echo htmlspecialchars($descripcion); ?>
              </div>
              <?php
                $movLink = 'index.php?ruta=ordenes';
                if (!empty($idOrden) && isset($_adm_allOrders)) {
                    $mo = $act['orden'];
                    $movLink = 'index.php?ruta=infoOrden&idOrden=' . $idOrden
                        . '&empresa=' . (isset($mo['id_empresa']) ? $mo['id_empresa'] : '')
                        . '&asesor=' . (isset($mo['id_Asesor']) ? $mo['id_Asesor'] : '')
                        . '&cliente=' . (isset($mo['id_usuario']) ? $mo['id_usuario'] : '')
                        . '&tecnico=' . (isset($mo['id_tecnico']) ? $mo['id_tecnico'] : '')
                        . '&tecnicodos=' . (isset($mo['id_tecnicoDos']) ? $mo['id_tecnicoDos'] : '')
                        . '&pedido=' . (isset($mo['id_pedido']) ? $mo['id_pedido'] : '');
                }
              ?>
              <a href="<?php echo $movLink; ?>" target="_blank" style="font-size:11px;font-weight:600;color:#6366f1;text-decoration:none;display:inline-flex;align-items:center;gap:4px;margin-top:2px;transition:color .15s"
                 onmouseover="this.style.color='#4f46e5'" onmouseout="this.style.color='#6366f1'">
                <i class="fa-solid fa-hashtag" style="font-size:9px"></i>Orden <?php echo htmlspecialchars($idOrden); ?>
                <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:8px;margin-left:2px"></i>
              </a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>
