<?php
/*  ═══════════════════════════════════════════════════
    DASHBOARD ADMIN — Últimos Movimientos
    Muestra las observaciones más recientes de las
    órdenes con autor, resumen y fecha.
    ═══════════════════════════════════════════════════ */

$_mov_obs = array();
try {
    // Si hay IDs de órdenes del asesor, filtrar solo sus observaciones
    if (isset($_crm_orderIds) && !empty($_crm_orderIds)) {
        $_mov_obs = controladorObservaciones::ctrUltimasObservacionesPorOrdenes($_crm_orderIds, 20);
    } else {
        $_mov_obs = controladorObservaciones::ctrUltimasObservaciones(20);
    }
    if (!is_array($_mov_obs)) $_mov_obs = array();
} catch (Exception $e) { $_mov_obs = array(); }

// Mapa de órdenes para construir links directos
$_mov_ordMap = array();
$_mov_ordSource = isset($_crm_allOrders) ? $_crm_allOrders : (isset($_adm_allOrders) ? $_adm_allOrders : array());
if (is_array($_mov_ordSource)) {
    foreach ($_mov_ordSource as $o) {
        if (isset($o['id'])) $_mov_ordMap[$o['id']] = $o;
    }
}
unset($_mov_ordSource);

// Helper: truncar texto sin cortar palabras
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

// Helper: tiempo relativo
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

// Colores por perfil
if (!function_exists('_movColorPerfil')) {
    function _movColorPerfil($perfil) {
        $p = strtolower($perfil);
        if (strpos($p, 'admin') !== false) return array('#6366f1', '#eef2ff', 'fa-shield-halved');
        if (strpos($p, 'vendedor') !== false || strpos($p, 'asesor') !== false) return array('#8b5cf6', '#f5f3ff', 'fa-headset');
        if (strpos($p, 'tecnico') !== false || strpos($p, 'técnico') !== false) return array('#06b6d4', '#ecfeff', 'fa-wrench');
        return array('#64748b', '#f1f5f9', 'fa-user');
    }
}

$_mov_av_grads = array(
    'linear-gradient(135deg,#6366f1,#818cf8)',
    'linear-gradient(135deg,#3b82f6,#60a5fa)',
    'linear-gradient(135deg,#8b5cf6,#a78bfa)',
    'linear-gradient(135deg,#06b6d4,#22d3ee)',
    'linear-gradient(135deg,#22c55e,#4ade80)',
    'linear-gradient(135deg,#f59e0b,#fbbf24)',
    'linear-gradient(135deg,#ef4444,#f87171)',
    'linear-gradient(135deg,#ec4899,#f472b6)',
);
?>

<div class="crm-card" style="margin-bottom:20px">
  <div class="crm-card-head">
    <h4 class="crm-card-title"><i class="fa-solid fa-clock-rotate-left"></i> Últimos Movimientos</h4>
    <div style="display:flex;align-items:center;gap:8px">
      <span class="crm-badge" style="background:#f1f5f9;color:#475569">
        <?php echo count($_mov_obs); ?> recientes
      </span>
      <a href="index.php?ruta=comentarios-hoy"
         style="display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:600;color:#6366f1;background:#eef2ff;border:1px solid #c7d2fe;padding:4px 10px;border-radius:8px;text-decoration:none;transition:background .15s"
         onmouseover="this.style.background='#e0e7ff'" onmouseout="this.style.background='#eef2ff'">
        <i class="fa-solid fa-calendar-day" style="font-size:10px"></i> Ver todos del día
      </a>
    </div>
  </div>
  <div class="crm-card-body-flush">
    <?php if (empty($_mov_obs)): ?>
      <div class="crm-empty">
        <i class="fa-solid fa-comments"></i>
        <strong>Sin movimientos recientes</strong>
        <span style="font-size:12px">Las observaciones de órdenes aparecerán aquí</span>
      </div>
    <?php else: ?>
      <div style="max-height:600px;overflow-y:auto">
        <?php foreach ($_mov_obs as $i => $obs):
          $nombre = isset($obs['creador_nombre']) ? $obs['creador_nombre'] : 'Usuario';
          $perfil = isset($obs['creador_perfil']) ? $obs['creador_perfil'] : '';
          $foto = isset($obs['creador_foto']) ? $obs['creador_foto'] : '';
          $texto = isset($obs['observacion']) ? $obs['observacion'] : '';
          $fecha = isset($obs['fecha']) ? $obs['fecha'] : '';
          $idOrden = isset($obs['id_orden']) ? $obs['id_orden'] : '';
          $resumen = _movTruncar($texto, 90);
          $tiempo = _movTiempoRelativo($fecha);
          $colPerf = _movColorPerfil($perfil);
          $initial = mb_strtoupper(mb_substr($nombre, 0, 1));
          $grad = $_mov_av_grads[$i % count($_mov_av_grads)];
        ?>
          <div style="display:flex;gap:12px;padding:14px 18px;border-bottom:1px solid #f1f5f9;transition:background .12s"
               onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
            <!-- Avatar -->
            <?php if (!empty($foto)): ?>
              <img src="<?php echo htmlspecialchars($foto); ?>"
                   onerror="this.onerror=null;this.style.display='none';this.nextElementSibling.style.display='flex'"
                   style="width:36px;height:36px;border-radius:50%;object-fit:cover;border:2px solid #e2e8f0;flex-shrink:0;margin-top:2px">
              <div style="display:none;width:36px;height:36px;border-radius:50%;background:<?php echo $grad; ?>;align-items:center;justify-content:center;font-size:13px;font-weight:800;color:#fff;flex-shrink:0;margin-top:2px">
                <?php echo $initial; ?>
              </div>
            <?php else: ?>
              <div style="width:36px;height:36px;border-radius:50%;background:<?php echo $grad; ?>;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;color:#fff;flex-shrink:0;margin-top:2px">
                <?php echo $initial; ?>
              </div>
            <?php endif; ?>

            <!-- Contenido -->
            <div style="flex:1;min-width:0">
              <div style="display:flex;align-items:center;gap:8px;margin-bottom:3px;flex-wrap:wrap">
                <span style="font-size:13px;font-weight:700;color:#0f172a"><?php echo htmlspecialchars($nombre); ?></span>
                <span style="display:inline-flex;align-items:center;gap:3px;font-size:10px;font-weight:600;color:<?php echo $colPerf[0]; ?>;background:<?php echo $colPerf[1]; ?>;padding:2px 7px;border-radius:8px">
                  <i class="fa-solid <?php echo $colPerf[2]; ?>" style="font-size:8px"></i>
                  <?php echo htmlspecialchars(ucfirst($perfil)); ?>
                </span>
                <span style="font-size:11px;color:#94a3b8;margin-left:auto;flex-shrink:0"><?php echo $tiempo; ?></span>
              </div>
              <div style="font-size:12px;color:#475569;line-height:1.5;margin-bottom:4px">
                <?php echo htmlspecialchars($resumen); ?>
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
              <a href="<?php echo $movLink; ?>" target="_blank" style="font-size:11px;font-weight:600;color:#6366f1;text-decoration:none;display:inline-flex;align-items:center;gap:4px;transition:color .15s"
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
