<?php
/*  ═══════════════════════════════════════════════════
    VISTA: Cambios de Estado del Día
    Muestra todas las órdenes que tuvieron actividad
    hoy (ingreso o salida) con su estado actual.
    ═══════════════════════════════════════════════════ */

// Control de acceso
$_perfilesPermitidos = array("administrador", "vendedor", "tecnico");
if (!isset($_SESSION["perfil"]) || !in_array($_SESSION["perfil"], $_perfilesPermitidos)) {
    echo '<script>window.location = "index.php?ruta=inicio";</script>';
    return;
}

// Cargar todas las órdenes
$_ceh_allOrders = array();
try {
    $_ceh_allOrders = controladorOrdenes::ctrlMostrarordenesEmpresayPerfil(
        "id_empresa", $_SESSION["empresa"], null, null
    );
    if (!is_array($_ceh_allOrders)) $_ceh_allOrders = array();
} catch (Exception $e) {}

if (empty($_ceh_allOrders)) {
    try {
        $r = controladorOrdenes::ctrlTraerOrdenesConTope(0, 99999);
        if (is_array($r)) $_ceh_allOrders = $r;
    } catch (Exception $e) {}
}

// Mapeo de estado a info visual
function _cehEstadoInfo($estado) {
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

// Filtrar órdenes con actividad hoy
$_ceh_hoy = date('Y-m-d');
$_ceh_actividad = array();

foreach ($_ceh_allOrders as $ord) {
    $estado = isset($ord['estado']) ? $ord['estado'] : '';
    if (empty($estado)) continue;

    $fi = isset($ord['fecha_ingreso']) ? substr($ord['fecha_ingreso'], 0, 10) : '';
    $fs = !empty($ord['fecha_Salida']) ? substr($ord['fecha_Salida'], 0, 10) : '';

    $fechaActividad = '';
    $tipoEvento = '';

    if (!empty($fs) && $fs === $_ceh_hoy) {
        $fechaActividad = $ord['fecha_Salida'];
        $tipoEvento = 'salida';
    } elseif ($fi === $_ceh_hoy) {
        $fechaActividad = $ord['fecha_ingreso'];
        $tipoEvento = 'ingreso';
    }

    if (!empty($fechaActividad)) {
        $eInfo = _cehEstadoInfo($estado);
        $_ceh_actividad[] = array(
            'id_orden'  => $ord['id'],
            'estado'    => $estado,
            'info'      => $eInfo,
            'fecha'     => $fechaActividad,
            'tipo'      => $tipoEvento,
            'equipo'    => isset($ord['equipo']) ? $ord['equipo'] : '',
            'marca'     => isset($ord['marca']) ? $ord['marca'] : '',
            'nombre'    => isset($ord['nombre']) ? $ord['nombre'] : '',
            'titulo'    => isset($ord['titulo']) ? $ord['titulo'] : '',
            'total'     => isset($ord['total']) ? floatval($ord['total']) : 0,
            'orden'     => $ord,
        );
    }
}

// Ordenar por fecha desc
usort($_ceh_actividad, function($a, $b) {
    return strcmp($b['fecha'], $a['fecha']);
});

// Resumen por estado
$_ceh_resumen = array();
foreach ($_ceh_actividad as $act) {
    $lbl = $act['info']['label'];
    if (!isset($_ceh_resumen[$lbl])) {
        $_ceh_resumen[$lbl] = array('count' => 0, 'info' => $act['info']);
    }
    $_ceh_resumen[$lbl]['count']++;
}
?>

<div class="content-wrapper" style="min-height:100vh">
  <section class="content-header">
    <h1 style="font-weight:800;display:flex;align-items:center;gap:10px">
      <i class="fa-solid fa-arrow-right-arrow-left" style="color:#6366f1"></i>
      Cambios de Estado del Día
      <small style="font-weight:400;color:#94a3b8">
        <?php echo date('d/m/Y'); ?> — <?php echo count($_ceh_actividad); ?> movimientos
      </small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="index.php?ruta=inicio"><i class="fa-solid fa-house"></i> Inicio</a></li>
      <li class="active">Cambios de Estado Hoy</li>
    </ol>
  </section>

  <section class="content" style="padding:15px 15px 30px">

    <!-- Resumen por estado -->
    <?php if (!empty($_ceh_resumen)): ?>
    <div style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:20px">
      <?php foreach ($_ceh_resumen as $lbl => $r): ?>
        <div style="display:flex;align-items:center;gap:8px;padding:10px 16px;background:#fff;border-radius:12px;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,.06)">
          <div style="width:32px;height:32px;border-radius:50%;background:<?php echo $r['info']['bg']; ?>;display:flex;align-items:center;justify-content:center;border:2px solid <?php echo $r['info']['color']; ?>30">
            <i class="fa-solid <?php echo $r['info']['icon']; ?>" style="font-size:13px;color:<?php echo $r['info']['color']; ?>"></i>
          </div>
          <div>
            <div style="font-size:18px;font-weight:800;color:#0f172a;line-height:1"><?php echo $r['count']; ?></div>
            <div style="font-size:11px;color:<?php echo $r['info']['color']; ?>;font-weight:600"><?php echo htmlspecialchars($lbl); ?></div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Tabla de actividad -->
    <div style="background:#fff;border-radius:12px;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,.06);overflow:hidden">
      <div style="padding:16px 20px;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between">
        <h4 style="margin:0;font-size:15px;font-weight:700;color:#0f172a">
          <i class="fa-solid fa-list-check" style="color:#6366f1;margin-right:6px"></i>
          Detalle de movimientos
        </h4>
        <a href="index.php?ruta=inicio" style="font-size:12px;font-weight:600;color:#6366f1;text-decoration:none">
          <i class="fa-solid fa-arrow-left" style="font-size:10px"></i> Volver al Dashboard
        </a>
      </div>

      <?php if (empty($_ceh_actividad)): ?>
        <div style="text-align:center;padding:60px 20px;color:#94a3b8">
          <i class="fa-solid fa-arrow-right-arrow-left" style="font-size:40px;margin-bottom:12px;display:block;opacity:.5"></i>
          <strong style="font-size:16px;color:#475569">Sin movimientos hoy</strong>
          <p style="font-size:13px;margin-top:6px">Aún no se han registrado cambios de estado en órdenes el día de hoy</p>
        </div>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table" style="margin:0;font-size:13px">
            <thead>
              <tr style="background:#f8fafc">
                <th style="padding:10px 16px;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;border-bottom:2px solid #e2e8f0">Orden</th>
                <th style="padding:10px 16px;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;border-bottom:2px solid #e2e8f0">Estado</th>
                <th style="padding:10px 16px;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;border-bottom:2px solid #e2e8f0">Cliente</th>
                <th style="padding:10px 16px;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;border-bottom:2px solid #e2e8f0">Equipo</th>
                <th style="padding:10px 16px;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;border-bottom:2px solid #e2e8f0">Tipo</th>
                <th style="padding:10px 16px;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;border-bottom:2px solid #e2e8f0">Hora</th>
                <th style="padding:10px 16px;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;border-bottom:2px solid #e2e8f0"></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($_ceh_actividad as $act):
                $eInfo = $act['info'];
                $hora = date('h:i A', strtotime($act['fecha']));
                $equipoStr = '';
                if (!empty($act['equipo'])) $equipoStr = $act['equipo'];
                if (!empty($act['marca'])) $equipoStr .= ' ' . $act['marca'];
                if (empty($equipoStr)) $equipoStr = '—';

                $movLink = 'index.php?ruta=ordenes';
                $o = $act['orden'];
                if (!empty($act['id_orden'])) {
                    $movLink = 'index.php?ruta=infoOrden&idOrden=' . $act['id_orden']
                        . '&empresa=' . (isset($o['id_empresa']) ? $o['id_empresa'] : '')
                        . '&asesor=' . (isset($o['id_Asesor']) ? $o['id_Asesor'] : '')
                        . '&cliente=' . (isset($o['id_usuario']) ? $o['id_usuario'] : '')
                        . '&tecnico=' . (isset($o['id_tecnico']) ? $o['id_tecnico'] : '')
                        . '&tecnicodos=' . (isset($o['id_tecnicoDos']) ? $o['id_tecnicoDos'] : '')
                        . '&pedido=' . (isset($o['id_pedido']) ? $o['id_pedido'] : '');
                }
              ?>
              <tr style="border-bottom:1px solid #f1f5f9;transition:background .1s"
                  onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                <td style="padding:10px 16px;font-weight:700;color:#0f172a;vertical-align:middle">
                  #<?php echo htmlspecialchars($act['id_orden']); ?>
                </td>
                <td style="padding:10px 16px;vertical-align:middle">
                  <span style="display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;color:<?php echo $eInfo['color']; ?>;background:<?php echo $eInfo['bg']; ?>;padding:4px 10px;border-radius:8px">
                    <i class="fa-solid <?php echo $eInfo['icon']; ?>" style="font-size:9px"></i>
                    <?php echo htmlspecialchars($eInfo['label']); ?>
                  </span>
                </td>
                <td style="padding:10px 16px;color:#475569;vertical-align:middle">
                  <?php echo htmlspecialchars(!empty($act['nombre']) ? $act['nombre'] : '—'); ?>
                </td>
                <td style="padding:10px 16px;color:#475569;vertical-align:middle">
                  <?php echo htmlspecialchars($equipoStr); ?>
                </td>
                <td style="padding:10px 16px;vertical-align:middle">
                  <?php if ($act['tipo'] === 'ingreso'): ?>
                    <span style="font-size:10px;font-weight:600;color:#3b82f6;background:#eff6ff;padding:3px 8px;border-radius:6px">
                      <i class="fa-solid fa-arrow-right-to-bracket" style="font-size:8px"></i> Ingreso
                    </span>
                  <?php else: ?>
                    <span style="font-size:10px;font-weight:600;color:#22c55e;background:#f0fdf4;padding:3px 8px;border-radius:6px">
                      <i class="fa-solid fa-arrow-right-from-bracket" style="font-size:8px"></i> Salida
                    </span>
                  <?php endif; ?>
                </td>
                <td style="padding:10px 16px;font-size:12px;color:#94a3b8;vertical-align:middle">
                  <?php echo $hora; ?>
                </td>
                <td style="padding:10px 16px;vertical-align:middle">
                  <a href="<?php echo $movLink; ?>" target="_blank"
                     style="font-size:11px;font-weight:600;color:#6366f1;text-decoration:none;display:inline-flex;align-items:center;gap:3px"
                     onmouseover="this.style.color='#4f46e5'" onmouseout="this.style.color='#6366f1'">
                    Ver <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:8px"></i>
                  </a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

  </section>
</div>
