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

// ── Obtener IDs de órdenes del técnico (para filtrar observaciones) ──
$_tn_tecOrdenIds = null;
if ($_tn_perfil === "tecnico" && $_tn_idRol !== null) {
    try {
        $_tn_tecOrdenes = controladorOrdenes::ctrMostrarOrdenesDelTecncio($_tn_idRol);
        if (is_array($_tn_tecOrdenes)) {
            $_tn_tecOrdenIds = array();
            foreach ($_tn_tecOrdenes as $_tn_to) {
                $_tn_tecOrdenIds[] = intval($_tn_to["id"]);
            }
        }
    } catch (Exception $e) {}
}

// ── Obtener observaciones de hoy ──
$_tn_obsHoy = array();
try {
    $_tn_obsHoy = controladorObservaciones::ctrObservacionesRecientesNotif(
        isset($_SESSION["id"]) ? intval($_SESSION["id"]) : 0, 20,
        $_tn_tecOrdenIds // null para admin/vendedor, array de IDs para técnico
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
        // Orden de prioridad: estados con "ent" en su nombre van DESPUÉS de pendiente/autorización
        if (strpos($e, 'autorización') !== false || strpos($e, 'autorizacion') !== false || strpos($e, 'pendiente') !== false || $e === 'aut') return array('#f59e0b', '#fffbeb', 'fa-hourglass-half', 'Autorización');
        if (strpos($e, 'supervisión') !== false || strpos($e, 'supervision') !== false || $e === 'sup') return array('#8b5cf6', '#f5f3ff', 'fa-eye', 'Supervisión');
        if (strpos($e, 'garantía aceptada') !== false || strpos($e, 'garantia aceptada') !== false || $e === 'ga') return array('#dc2626', '#fef2f2', 'fa-rotate-left', 'Garantía Aceptada');
        if (strpos($e, 'probable garantía') !== false || strpos($e, 'probable garantia') !== false) return array('#dc2626', '#fef2f2', 'fa-triangle-exclamation', 'Probable Garantía');
        if (strpos($e, 'garantía') !== false || strpos($e, 'garantia') !== false) return array('#dc2626', '#fef2f2', 'fa-rotate-left', 'Garantía');
        if (strpos($e, 'revisión') !== false || strpos($e, 'revision') !== false || $e === 'rev') return array('#ef4444', '#fef2f2', 'fa-magnifying-glass', 'En Revisión');
        if (strpos($e, 'terminada') !== false || $e === 'ter') return array('#06b6d4', '#ecfeff', 'fa-flag-checkered', 'Terminada');
        if (strpos($e, 'entregado al asesor') !== false) return array('#10b981', '#ecfdf5', 'fa-user-check', 'Entregado al Asesor');
        if (strpos($e, 'entregado/pagado') !== false) return array('#22c55e', '#f0fdf4', 'fa-money-check-dollar', 'Entregado/Pagado');
        if (strpos($e, 'entregado/credito') !== false || strpos($e, 'entregado/crédito') !== false) return array('#22c55e', '#f0fdf4', 'fa-credit-card', 'Entregado/Crédito');
        if (strpos($e, 'entregado') !== false || strpos($e, 'entregada') !== false) return array('#22c55e', '#f0fdf4', 'fa-handshake', 'Entregado');
        if (strpos($e, 'aceptado') !== false || strpos($e, 'aceptada') !== false || $e === 'ok') return array('#3b82f6', '#eff6ff', 'fa-circle-check', 'Aceptado');
        if (strpos($e, 'cancel') !== false) return array('#64748b', '#f1f5f9', 'fa-ban', 'Cancelada');
        if (strpos($e, 'sin reparación') !== false || strpos($e, 'sin reparacion') !== false || $e === 'sr') return array('#94a3b8', '#f8fafc', 'fa-xmark', 'Sin Reparación');
        if (strpos($e, 'producto para venta') !== false || $e === 'pv') return array('#f97316', '#fff7ed', 'fa-tags', 'Producto para Venta');
        if (strpos($e, 'producto en almacen') !== false || strpos($e, 'producto en almacén') !== false) return array('#78716c', '#fafaf9', 'fa-warehouse', 'Producto en Almacén');
        if (strpos($e, 'seguimiento') !== false) return array('#0ea5e9', '#f0f9ff', 'fa-chart-line', 'Seguimiento');
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

// ── Batch-fetch datos de órdenes (marca, modelo, cliente) ──
$_tn_ordenIds = array();
foreach ($_tn_notifs as $n) {
    $_tn_ordenIds[intval($n['id_orden'])] = intval($n['id_orden']);
}
// También incluir observaciones
foreach ($_tn_obsHoy as $ob) {
    $_tn_ordenIds[intval($ob['id_orden'])] = intval($ob['id_orden']);
}

$_tn_ordenData = array();
$_tn_clienteNames = array();
if (!empty($_tn_ordenIds)) {
    try {
        $_tn_ordenData = ModeloNotificaciones::mdlDatosOrdenesPorIds(array_values($_tn_ordenIds));
    } catch (Exception $e) { $_tn_ordenData = array(); }

    // Recopilar id_usuario únicos para obtener nombres de clientes
    $_tn_clienteIds = array();
    foreach ($_tn_ordenData as $od) {
        if (!empty($od['id_usuario'])) {
            $_tn_clienteIds[intval($od['id_usuario'])] = intval($od['id_usuario']);
        }
    }
    // Fetch client names from sistema DB
    if (!empty($_tn_clienteIds)) {
        try {
            $pdoSis = Conexion::conectar();
            $phCli = implode(',', array_fill(0, count($_tn_clienteIds), '?'));
            $stmtCli = $pdoSis->prepare("SELECT id, nombre FROM clientesTienda WHERE id IN ($phCli)");
            $stmtCli->execute(array_values($_tn_clienteIds));
            $rowsCli = $stmtCli->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rowsCli as $rc) {
                $_tn_clienteNames[intval($rc['id'])] = $rc['nombre'];
            }
        } catch (Exception $e) {}
    }
}

// Helper para obtener info de orden
if (!function_exists('_tnOrdenInfo')) {
    function _tnOrdenInfo($idOrden, $ordenData, $clienteNames) {
        $id = intval($idOrden);
        $info = array('marca' => '', 'modelo' => '', 'cliente' => '', 'url_params' => '');
        if (isset($ordenData[$id])) {
            $od = $ordenData[$id];
            $info['marca']  = isset($od['marcaDelEquipo']) ? $od['marcaDelEquipo'] : '';
            $info['modelo'] = isset($od['modeloDelEquipo']) ? $od['modeloDelEquipo'] : '';
            $idCli = isset($od['id_usuario']) ? intval($od['id_usuario']) : 0;
            if ($idCli > 0 && isset($clienteNames[$idCli])) {
                $info['cliente'] = $clienteNames[$idCli];
            }
            $info['url_params'] = '&empresa=' . (isset($od['id_empresa']) ? $od['id_empresa'] : '')
                . '&asesor=' . (isset($od['id_Asesor']) ? $od['id_Asesor'] : '')
                . '&cliente=' . (isset($od['id_usuario']) ? $od['id_usuario'] : '')
                . '&tecnico=' . (isset($od['id_tecnico']) ? $od['id_tecnico'] : '')
                . '&tecnicodos=' . (isset($od['id_tecnicoDos']) ? $od['id_tecnicoDos'] : '')
                . '&pedido=' . (isset($od['id_pedido']) ? $od['id_pedido'] : '0');
        }
        return $info;
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

    <?php
    // ══════════════════════════════════════════════════
    // CONSTRUIR LISTA UNIFICADA CRONOLÓGICA
    // ══════════════════════════════════════════════════
    $_tn_allItems = array();

    // Agregar estado/traspaso
    foreach ($_tn_notifs as $n) {
        $nTipo = isset($n['tipo']) ? $n['tipo'] : 'estado';
        $_tn_oi = _tnOrdenInfo($n['id_orden'], $_tn_ordenData, $_tn_clienteNames);
        $_tn_allItems[] = array(
            'source' => $nTipo,
            'fecha'  => $n['fecha'],
            'data'   => $n,
            'oi'     => $_tn_oi
        );
    }

    // Agregar observaciones
    foreach ($_tn_obsHoy as $ob) {
        $_tn_obOi = _tnOrdenInfo($ob['id_orden'], $_tn_ordenData, $_tn_clienteNames);
        $_tn_allItems[] = array(
            'source' => 'observaciones',
            'fecha'  => $ob['fecha'],
            'data'   => $ob,
            'oi'     => $_tn_obOi
        );
    }

    // Agregar atrasos
    foreach ($_tn_atraso as $oa) {
        $oaCliId = isset($oa["id_usuario"]) ? intval($oa["id_usuario"]) : 0;
        if ($oaCliId > 0 && !isset($_tn_clienteNames[$oaCliId])) {
            try {
                $cliFetch = ControladorClientes::ctrMostrarClientes("id", $oaCliId);
                if (is_array($cliFetch) && isset($cliFetch["nombre"])) {
                    $_tn_clienteNames[$oaCliId] = $cliFetch["nombre"];
                }
            } catch (Exception $e) {}
        }
        $_tn_allItems[] = array(
            'source' => 'atraso',
            'fecha'  => isset($oa["fecha_ingreso"]) ? $oa["fecha_ingreso"] : "2000-01-01",
            'data'   => $oa,
            'oi'     => array()
        );
    }

    // Ordenar cronológicamente (más reciente primero)
    usort($_tn_allItems, function($a, $b) {
        return strtotime($b['fecha']) - strtotime($a['fecha']);
    });
    ?>

    <!-- ══ Lista unificada ══ -->
    <ul class="tn-list" id="tnListAll">
    <?php
      $lastDate = '';
      if (empty($_tn_allItems)):
    ?>
      <div class="tn-empty">
        <i class="fa-solid fa-check-circle"></i>
        <p>No hay notificaciones</p>
      </div>
    <?php else:
      foreach ($_tn_allItems as $item):
        $itemSource = $item['source'];
        $itemFecha  = $item['fecha'];
        $itemData   = $item['data'];
        $itemOi     = $item['oi'];
        $itemDate   = date('Y-m-d', strtotime($itemFecha));
        $itemDateLabel = ($itemDate === date('Y-m-d')) ? 'Hoy' : (($itemDate === date('Y-m-d', strtotime('-1 day'))) ? 'Ayer' : date('d \d\e F, Y', strtotime($itemFecha)));

        if ($itemDate !== $lastDate):
          $lastDate = $itemDate;
    ?>
      <li class="tn-date-sep"><?php echo $itemDateLabel; ?></li>
    <?php endif; ?>

    <?php if ($itemSource === 'traspaso'):
        $_tn_equipoTxt = '';
        if (!empty($itemOi['marca']) || !empty($itemOi['modelo'])) {
            $_tn_equipoTxt = trim($itemOi['marca'] . ' ' . $itemOi['modelo']);
        } elseif (!empty($itemData['titulo_orden'])) {
            $_tn_equipoTxt = $itemData['titulo_orden'];
        }
        $_tn_ordenUrl = 'index.php?ruta=infoOrden&idOrden=' . $itemData['id_orden'] . $itemOi['url_params'];
    ?>
      <li class="tn-item" data-ntype="traspaso">
        <div class="tn-item-icon" style="background:#f5f3ff">
          <i class="fa-solid fa-people-arrows" style="color:#8b5cf6"></i>
        </div>
        <div class="tn-item-body">
          <div class="tn-item-title">
            <a href="<?php echo $_tn_ordenUrl; ?>" style="color:#6366f1;font-weight:700;text-decoration:none" title="Ver orden #<?php echo $itemData['id_orden']; ?>">#<?php echo htmlspecialchars($itemData['id_orden']); ?></a>
            — Traspaso de equipo:
            <?php echo htmlspecialchars($itemData['estado_anterior']); ?>
            <i class="fa-solid fa-arrow-right" style="font-size:10px;color:#8b5cf6;margin:0 3px"></i>
            <strong style="color:#8b5cf6"><?php echo htmlspecialchars($itemData['estado_nuevo']); ?></strong>
          </div>
          <?php if (!empty($_tn_equipoTxt)): ?>
            <div style="font-size:12px;color:#64748b;margin-top:2px">
              <i class="fa-solid fa-laptop" style="font-size:9px;margin-right:3px;color:#94a3b8"></i><?php echo htmlspecialchars($_tn_equipoTxt); ?>
              <?php if (!empty($itemOi['cliente'])): ?>
                <span style="margin-left:8px;color:#94a3b8">|</span>
                <span style="margin-left:8px"><i class="fa-solid fa-user-tag" style="font-size:9px;margin-right:3px;color:#94a3b8"></i><?php echo htmlspecialchars($itemOi['cliente']); ?></span>
              <?php endif; ?>
            </div>
          <?php endif; ?>
          <div class="tn-item-meta">
            <span class="tn-badge-tipo" style="background:#f5f3ff;color:#8b5cf6">
              <i class="fa-solid fa-people-arrows" style="font-size:8px"></i> Traspaso
            </span>
            <span><i class="fa-solid fa-user" style="font-size:8px;margin-right:3px"></i><?php echo htmlspecialchars($itemData['nombre_usuario']); ?></span>
            <span><i class="fa-regular fa-clock" style="font-size:8px;margin-right:3px"></i><?php echo _tnTiempoRel($itemFecha); ?></span>
          </div>
        </div>
      </li>

    <?php elseif ($itemSource === 'estado'):
          $ei = _tnEstadoInfo($itemData['estado_nuevo']);
          $_tn_equipoTxt = '';
          if (!empty($itemOi['marca']) || !empty($itemOi['modelo'])) {
              $_tn_equipoTxt = trim($itemOi['marca'] . ' ' . $itemOi['modelo']);
          } elseif (!empty($itemData['titulo_orden'])) {
              $_tn_equipoTxt = $itemData['titulo_orden'];
          }
          $_tn_ordenUrl = 'index.php?ruta=infoOrden&idOrden=' . $itemData['id_orden'] . $itemOi['url_params'];
    ?>
      <li class="tn-item" data-ntype="estado">
        <div class="tn-item-icon" style="background:<?php echo $ei[1]; ?>">
          <i class="fa-solid <?php echo $ei[2]; ?>" style="color:<?php echo $ei[0]; ?>"></i>
        </div>
        <div class="tn-item-body">
          <div class="tn-item-title">
            <a href="<?php echo $_tn_ordenUrl; ?>" style="color:#6366f1;font-weight:700;text-decoration:none" title="Ver orden #<?php echo $itemData['id_orden']; ?>">#<?php echo htmlspecialchars($itemData['id_orden']); ?></a>
            cambió de
            <span style="color:#94a3b8"><?php echo htmlspecialchars($itemData['estado_anterior']); ?></span>
            a
            <strong style="color:<?php echo $ei[0]; ?>"><?php echo htmlspecialchars($itemData['estado_nuevo']); ?></strong>
          </div>
          <?php if (!empty($_tn_equipoTxt)): ?>
            <div style="font-size:12px;color:#64748b;margin-top:2px">
              <i class="fa-solid fa-laptop" style="font-size:9px;margin-right:3px;color:#94a3b8"></i><?php echo htmlspecialchars($_tn_equipoTxt); ?>
              <?php if (!empty($itemOi['cliente'])): ?>
                <span style="margin-left:8px;color:#94a3b8">|</span>
                <span style="margin-left:8px"><i class="fa-solid fa-user-tag" style="font-size:9px;margin-right:3px;color:#94a3b8"></i><?php echo htmlspecialchars($itemOi['cliente']); ?></span>
              <?php endif; ?>
            </div>
          <?php endif; ?>
          <div class="tn-item-meta">
            <span class="tn-badge-tipo" style="background:<?php echo $ei[1]; ?>;color:<?php echo $ei[0]; ?>">
              <i class="fa-solid <?php echo $ei[2]; ?>" style="font-size:8px"></i> <?php echo $ei[3]; ?>
            </span>
            <span><i class="fa-solid fa-user" style="font-size:8px;margin-right:3px"></i><?php echo htmlspecialchars($itemData['nombre_usuario']); ?></span>
            <span><i class="fa-regular fa-clock" style="font-size:8px;margin-right:3px"></i><?php echo _tnTiempoRel($itemFecha); ?></span>
          </div>
        </div>
      </li>

    <?php elseif ($itemSource === 'observaciones'):
          $obTexto = mb_strlen($itemData['observacion']) > 120 ? mb_substr($itemData['observacion'], 0, 120) . '…' : $itemData['observacion'];
          $obCreador = isset($itemData['creador_nombre']) ? $itemData['creador_nombre'] : 'Usuario';
          $obPerfil  = isset($itemData['creador_perfil']) ? $itemData['creador_perfil'] : '';
          $_tn_obEquipo = '';
          if (!empty($itemOi['marca']) || !empty($itemOi['modelo'])) {
              $_tn_obEquipo = trim($itemOi['marca'] . ' ' . $itemOi['modelo']);
          }
          $_tn_obUrl = 'index.php?ruta=infoOrden&idOrden=' . $itemData['id_orden'] . $itemOi['url_params'];
    ?>
      <li class="tn-item" data-ntype="observaciones">
        <div class="tn-item-icon" style="background:#fffbeb">
          <i class="fa-solid fa-comment-dots" style="color:#f59e0b"></i>
        </div>
        <div class="tn-item-body">
          <div class="tn-item-title">
            <a href="<?php echo $_tn_obUrl; ?>" style="color:#6366f1;font-weight:700;text-decoration:none" title="Ver orden #<?php echo $itemData['id_orden']; ?>">#<?php echo htmlspecialchars($itemData['id_orden']); ?></a>
            — <?php echo htmlspecialchars($obTexto); ?>
          </div>
          <?php if (!empty($_tn_obEquipo) || !empty($itemOi['cliente'])): ?>
            <div style="font-size:12px;color:#64748b;margin-top:2px">
              <?php if (!empty($_tn_obEquipo)): ?>
                <i class="fa-solid fa-laptop" style="font-size:9px;margin-right:3px;color:#94a3b8"></i><?php echo htmlspecialchars($_tn_obEquipo); ?>
              <?php endif; ?>
              <?php if (!empty($itemOi['cliente'])): ?>
                <?php if (!empty($_tn_obEquipo)): ?><span style="margin-left:8px;color:#94a3b8">|</span><?php endif; ?>
                <span style="margin-left:8px"><i class="fa-solid fa-user-tag" style="font-size:9px;margin-right:3px;color:#94a3b8"></i><?php echo htmlspecialchars($itemOi['cliente']); ?></span>
              <?php endif; ?>
            </div>
          <?php endif; ?>
          <div class="tn-item-meta">
            <span class="tn-badge-tipo" style="background:#fffbeb;color:#f59e0b">
              <i class="fa-solid fa-comment-dots" style="font-size:8px"></i> Observación
            </span>
            <span><i class="fa-solid fa-user" style="font-size:8px;margin-right:3px"></i><?php echo htmlspecialchars($obCreador); ?></span>
            <?php if (!empty($obPerfil)): ?>
              <span style="text-transform:capitalize"><?php echo htmlspecialchars($obPerfil); ?></span>
            <?php endif; ?>
            <span><i class="fa-regular fa-clock" style="font-size:8px;margin-right:3px"></i><?php echo _tnTiempoRel($itemFecha); ?></span>
          </div>
        </div>
      </li>

    <?php elseif ($itemSource === 'atraso'):
          $oaDias = max(0, floor((time() - strtotime($itemData["fecha_ingreso"])) / 86400));
          $oaColor = $oaDias >= 30 ? '#ef4444' : ($oaDias >= 15 ? '#f59e0b' : '#3b82f6');
          $oaEstado = isset($itemData["estado"]) ? $itemData["estado"] : "";
          $oaMarca  = isset($itemData["marcaDelEquipo"]) ? $itemData["marcaDelEquipo"] : "";
          $oaModelo = isset($itemData["modeloDelEquipo"]) ? $itemData["modeloDelEquipo"] : "";
          $oaEquipo = trim($oaMarca . ' ' . $oaModelo);
          if (empty($oaEquipo) && isset($itemData["titulo"])) $oaEquipo = $itemData["titulo"];
          $oaClienteNom = '';
          $oaCliId = isset($itemData["id_usuario"]) ? intval($itemData["id_usuario"]) : 0;
          if ($oaCliId > 0 && isset($_tn_clienteNames[$oaCliId])) $oaClienteNom = $_tn_clienteNames[$oaCliId];
          $oaUrl = 'index.php?ruta=infoOrden&idOrden=' . $itemData["id"]
                 . '&empresa=' . (isset($itemData["id_empresa"]) ? $itemData["id_empresa"] : '')
                 . '&asesor=' . (isset($itemData["id_Asesor"]) ? $itemData["id_Asesor"] : '')
                 . '&cliente=' . (isset($itemData["id_usuario"]) ? $itemData["id_usuario"] : '')
                 . '&tecnico=' . (isset($itemData["id_tecnico"]) ? $itemData["id_tecnico"] : '')
                 . '&tecnicodos=' . (isset($itemData["id_tecnicoDos"]) ? $itemData["id_tecnicoDos"] : '')
                 . '&pedido=' . (isset($itemData["id_pedido"]) ? $itemData["id_pedido"] : '0');
    ?>
      <li class="tn-item" data-ntype="atraso">
        <div class="tn-item-icon" style="background:<?php echo $oaDias >= 30 ? '#fef2f2' : ($oaDias >= 15 ? '#fffbeb' : '#eff6ff'); ?>">
          <i class="fa-solid fa-clock-rotate-left" style="color:<?php echo $oaColor; ?>"></i>
        </div>
        <div class="tn-item-body">
          <div class="tn-item-title">
            <a href="<?php echo $oaUrl; ?>" style="color:#6366f1;font-weight:700;text-decoration:none" title="Ver orden #<?php echo $itemData['id']; ?>">#<?php echo $itemData["id"]; ?></a>
            — Atraso de entrega:
            <strong style="color:<?php echo $oaColor; ?>"><?php echo $oaDias; ?> días</strong>
          </div>
          <?php if (!empty($oaEquipo)): ?>
            <div style="font-size:12px;color:#64748b;margin-top:2px">
              <i class="fa-solid fa-laptop" style="font-size:9px;margin-right:3px;color:#94a3b8"></i><?php echo htmlspecialchars($oaEquipo); ?>
              <?php if (!empty($oaClienteNom)): ?>
                <span style="margin-left:8px;color:#94a3b8">|</span>
                <span style="margin-left:8px"><i class="fa-solid fa-user-tag" style="font-size:9px;margin-right:3px;color:#94a3b8"></i><?php echo htmlspecialchars($oaClienteNom); ?></span>
              <?php endif; ?>
            </div>
          <?php endif; ?>
          <div class="tn-item-meta">
            <span class="tn-badge-tipo" style="background:<?php echo $oaDias >= 30 ? '#fef2f2' : ($oaDias >= 15 ? '#fffbeb' : '#eff6ff'); ?>;color:<?php echo $oaColor; ?>">
              <i class="fa-solid fa-clock-rotate-left" style="font-size:8px"></i>
              <?php echo $oaDias >= 30 ? 'Crítico' : ($oaDias >= 15 ? 'Urgente' : 'Atención'); ?>
            </span>
            <?php if (!empty($oaEstado)): ?>
              <span style="color:#64748b"><?php echo htmlspecialchars($oaEstado); ?></span>
            <?php endif; ?>
            <span><i class="fa-regular fa-calendar" style="font-size:8px;margin-right:3px"></i>Ingreso: <?php echo date("d/m/Y", strtotime($itemData["fecha_ingreso"])); ?></span>
          </div>
        </div>
      </li>
    <?php endif; endforeach; endif; ?>
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
  var tabs = $('.tn-tab');
  var pag = $('#tnPag');

  tabs.on('click', function(){
    tabs.removeClass('active');
    $(this).addClass('active');
    var tab = $(this).data('tab');

    var $list = $('#tnListAll');

    if (tab === 'todas') {
      // Show all items and date separators
      $list.find('.tn-item, .tn-date-sep, .tn-empty').show();
      pag.show();

    } else {
      // Filter by type
      pag.toggle(tab === 'estado' || tab === 'traspaso');

      $list.find('.tn-item').each(function(){
        $(this).toggle($(this).data('ntype') === tab);
      });
      $list.find('.tn-empty').hide();

      // Show date separators only if they have visible item siblings after them
      $list.find('.tn-date-sep').each(function(){
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
