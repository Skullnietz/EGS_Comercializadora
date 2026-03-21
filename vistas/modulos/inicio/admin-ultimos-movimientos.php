<?php
/*  ═══════════════════════════════════════════════════
    DASHBOARD ADMIN — Actividad Reciente
    Muestra los últimos cambios de estado de órdenes,
    detectados por las observaciones que contienen
    palabras clave de estado. Si no hay observaciones
    de estado, cae a mostrar la actividad general.
    ═══════════════════════════════════════════════════ */

// ── Cargar observaciones recientes ──
$_mov_obs = array();
try {
    $_mov_obs = controladorObservaciones::ctrUltimasObservaciones(50);
    if (!is_array($_mov_obs)) $_mov_obs = array();
} catch (Exception $e) { $_mov_obs = array(); }

// ── Mapa de órdenes para links directos ──
$_mov_ordMap = array();
if (isset($_adm_allOrders) && is_array($_adm_allOrders)) {
    foreach ($_adm_allOrders as $o) {
        if (isset($o['id'])) $_mov_ordMap[$o['id']] = $o;
    }
}

// ── Helpers ──
if (!function_exists('_movTruncar')) {
    function _movTruncar($texto, $max = 80) {
        $texto = trim(preg_replace('/\s+/', ' ', strip_tags($texto)));
        if (mb_strlen($texto) <= $max) return $texto;
        $corte = mb_substr($texto, 0, $max);
        $ultimo = mb_strrpos($corte, ' ');
        if ($ultimo !== false) $corte = mb_substr($corte, 0, $ultimo);
        return $corte . '...';
    }
}

if (!function_exists('_movTiempoRelativo')) {
    function _movTiempoRelativo($fecha) {
        $diff = time() - strtotime($fecha);
        if ($diff < 60) return 'Hace un momento';
        if ($diff < 3600) return 'Hace ' . floor($diff / 60) . ' min';
        if ($diff < 86400) return 'Hace ' . floor($diff / 3600) . 'h';
        if ($diff < 172800) return 'Ayer';
        if ($diff < 604800) return 'Hace ' . floor($diff / 86400) . ' días';
        return date('d/m/Y', strtotime($fecha));
    }
}

// ── Detectar estados a partir de las órdenes del día ──
// Construir actividad de cambio de estado basándose en la información de las órdenes
$_mov_estadoActividad = array();
$_mov_hoy = date('Y-m-d');

// Mapeo de estados a información visual
if (!function_exists('_movEstadoInfo')) {
    function _movEstadoInfo($estado) {
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

// Generar actividad desde las órdenes recientes (basada en estados actuales + fechas)
if (!empty($_adm_allOrders)) {
    foreach ($_adm_allOrders as $ord) {
        $estado = isset($ord['estado']) ? $ord['estado'] : '';
        if (empty($estado)) continue;

        $fi = isset($ord['fecha_ingreso']) ? substr($ord['fecha_ingreso'], 0, 10) : '';
        $fs = !empty($ord['fecha_Salida']) ? substr($ord['fecha_Salida'], 0, 10) : '';

        // Determinar la fecha más reciente relevante
        // Si fue entregada hoy, esa es la fecha de actividad
        // Si fue ingresada hoy, también cuenta
        $fechaActividad = '';
        $tipoEvento = '';

        $eInfo = _movEstadoInfo($estado);

        if (!empty($fs) && $fs === $_mov_hoy) {
            $fechaActividad = $ord['fecha_Salida'];
            $tipoEvento = 'salida';
        } elseif ($fi === $_mov_hoy) {
            $fechaActividad = $ord['fecha_ingreso'];
            $tipoEvento = 'ingreso';
        }

        if (!empty($fechaActividad)) {
            $_mov_estadoActividad[] = array(
                'id_orden'  => $ord['id'],
                'estado'    => $estado,
                'info'      => $eInfo,
                'fecha'     => $fechaActividad,
                'tipo'      => $tipoEvento,
                'equipo'    => isset($ord['equipo']) ? $ord['equipo'] : '',
                'marca'     => isset($ord['marca']) ? $ord['marca'] : '',
                'nombre'    => isset($ord['nombre']) ? $ord['nombre'] : '',
                'orden'     => $ord,
            );
        }
    }

    // Ordenar por fecha descendente
    usort($_mov_estadoActividad, function($a, $b) {
        return strcmp($b['fecha'], $a['fecha']);
    });

    // Limitar a 20
    $_mov_estadoActividad = array_slice($_mov_estadoActividad, 0, 20);
}

// Conteo de actividad del día por tipo de estado
$_mov_resumenHoy = array();
foreach ($_mov_estadoActividad as $act) {
    $lbl = $act['info']['label'];
    if (!isset($_mov_resumenHoy[$lbl])) {
        $_mov_resumenHoy[$lbl] = array('count' => 0, 'info' => $act['info']);
    }
    $_mov_resumenHoy[$lbl]['count']++;
}
?>

<div class="crm-card" style="margin-bottom:20px">
  <div class="crm-card-head">
    <h4 class="crm-card-title"><i class="fa-solid fa-arrow-right-arrow-left"></i> Actividad Reciente</h4>
    <div style="display:flex;align-items:center;gap:8px">
      <span class="crm-badge" style="background:#f1f5f9;color:#475569">
        <?php echo count($_mov_estadoActividad); ?> hoy
      </span>
      <a href="index.php?ruta=cambios-estado-hoy"
         style="display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:600;color:#6366f1;background:#eef2ff;border:1px solid #c7d2fe;padding:4px 10px;border-radius:8px;text-decoration:none;transition:background .15s"
         onmouseover="this.style.background='#e0e7ff'" onmouseout="this.style.background='#eef2ff'">
        <i class="fa-solid fa-calendar-day" style="font-size:10px"></i> Ver todos del día
      </a>
    </div>
  </div>

  <?php if (!empty($_mov_resumenHoy)): ?>
  <!-- Mini resumen de estados del día -->
  <div style="display:flex;flex-wrap:wrap;gap:6px;padding:10px 18px 0;border-bottom:1px solid #f1f5f9;padding-bottom:10px">
    <?php foreach ($_mov_resumenHoy as $lbl => $r): ?>
      <span style="display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:600;color:<?php echo $r['info']['color']; ?>;background:<?php echo $r['info']['bg']; ?>;padding:3px 10px;border-radius:12px;border:1px solid <?php echo $r['info']['color']; ?>20">
        <i class="fa-solid <?php echo $r['info']['icon']; ?>" style="font-size:9px"></i>
        <?php echo $r['count']; ?> <?php echo htmlspecialchars($lbl); ?>
      </span>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <div class="crm-card-body-flush">
    <?php if (empty($_mov_estadoActividad)): ?>
      <div class="crm-empty">
        <i class="fa-solid fa-arrow-right-arrow-left"></i>
        <strong>Sin cambios de estado hoy</strong>
        <span style="font-size:12px">Los cambios de estado de órdenes aparecerán aquí</span>
      </div>
    <?php else: ?>
      <div style="max-height:600px;overflow-y:auto">
        <?php foreach ($_mov_estadoActividad as $i => $act):
          $eInfo = $act['info'];
          $idOrden = $act['id_orden'];
          $tiempo = _movTiempoRelativo($act['fecha']);
          $descripcion = '';
          if ($act['tipo'] === 'ingreso') {
              $descripcion = 'Orden ingresada';
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
                if (!empty($idOrden) && isset($_mov_ordMap[$idOrden])) {
                    $mo = $_mov_ordMap[$idOrden];
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
