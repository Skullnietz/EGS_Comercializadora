<?php
/*  ═══════════════════════════════════════════════════
    VISTA: Comentarios del día
    Muestra todos los comentarios/observaciones
    registrados en la fecha de hoy.
    ═══════════════════════════════════════════════════ */

// Verificar sesión y perfil permitido
if (!isset($_SESSION["validarSesionBackend"]) || $_SESSION["validarSesionBackend"] !== "ok") {
    header("Location: index.php");
    exit;
}
$_perfilesPermitidos = array("administrador", "vendedor", "tecnico");
if (!in_array($_SESSION["perfil"], $_perfilesPermitidos)) {
    header("Location: index.php?ruta=inicio");
    exit;
}

// Cargar observaciones del día
$_choy_obs = array();
try {
    $_choy_obs = controladorObservaciones::ctrObservacionesHoy();
    if (!is_array($_choy_obs)) $_choy_obs = array();
} catch (Exception $e) { $_choy_obs = array(); }

// Mapa de órdenes para links directos (solo admin tiene $_adm_allOrders)
$_choy_ordMap = array();
if (isset($_adm_allOrders) && is_array($_adm_allOrders)) {
    foreach ($_adm_allOrders as $o) {
        if (isset($o['id'])) $_choy_ordMap[$o['id']] = $o;
    }
}

// Helpers (con guardas para evitar redeclaración)
if (!function_exists('_choyTruncar'))
function _choyTruncar($texto, $max = 120) {
    $texto = trim(preg_replace('/\s+/', ' ', strip_tags($texto)));
    if (mb_strlen($texto) <= $max) return $texto;
    $corte = mb_substr($texto, 0, $max);
    $ultimo = mb_strrpos($corte, ' ');
    if ($ultimo !== false) $corte = mb_substr($corte, 0, $ultimo);
    return $corte . '…';
}

if (!function_exists('_choyHora'))
function _choyHora($fecha) {
    return date('H:i', strtotime($fecha));
}

if (!function_exists('_choyColorPerfil'))
function _choyColorPerfil($perfil) {
    $p = strtolower($perfil);
    if (strpos($p, 'admin') !== false)    return array('#6366f1', '#eef2ff', 'fa-shield-halved');
    if (strpos($p, 'vendedor') !== false || strpos($p, 'asesor') !== false) return array('#8b5cf6', '#f5f3ff', 'fa-headset');
    if (strpos($p, 'tecnico') !== false || strpos($p, 'técnico') !== false) return array('#06b6d4', '#ecfeff', 'fa-wrench');
    return array('#64748b', '#f1f5f9', 'fa-user');
}

$_choy_grads = array(
    'linear-gradient(135deg,#6366f1,#818cf8)',
    'linear-gradient(135deg,#3b82f6,#60a5fa)',
    'linear-gradient(135deg,#8b5cf6,#a78bfa)',
    'linear-gradient(135deg,#06b6d4,#22d3ee)',
    'linear-gradient(135deg,#22c55e,#4ade80)',
    'linear-gradient(135deg,#f59e0b,#fbbf24)',
    'linear-gradient(135deg,#ef4444,#f87171)',
    'linear-gradient(135deg,#ec4899,#f472b6)',
);

$_choy_fecha = date('d/m/Y');
$_choy_total = count($_choy_obs);
?>

<!-- content-wrapper -->
<div class="content-wrapper">

  <!-- content-header -->
  <section class="content-header">
    <h1>Comentarios del Día <small><?php echo $_choy_fecha; ?></small></h1>
    <ol class="breadcrumb">
      <li><a href="index.php?ruta=inicio"><i class="fa-solid fa-gauge"></i> Inicio</a></li>
      <li class="active">Comentarios de hoy</li>
    </ol>
  </section>

  <!-- content -->
  <section class="content">

    <style>
      .choy-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        box-shadow: 0 1px 3px rgba(15,23,42,.06), 0 4px 14px rgba(15,23,42,.04);
        overflow: hidden;
      }
      .choy-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px 24px 16px;
        border-bottom: 1px solid #f1f5f9;
        flex-wrap: wrap;
        gap: 12px;
      }
      .choy-row {
        display: flex;
        gap: 14px;
        padding: 16px 24px;
        border-bottom: 1px solid #f8fafc;
        transition: background .12s;
      }
      .choy-row:last-child { border-bottom: none; }
      .choy-row:hover { background: #f8fafc; }
      .choy-num {
        font-size: 11px;
        font-weight: 700;
        color: #94a3b8;
        width: 22px;
        flex-shrink: 0;
        margin-top: 10px;
        text-align: right;
      }
      .choy-avatar {
        width: 40px; height: 40px; border-radius: 50%;
        object-fit: cover; border: 2px solid #e2e8f0;
        flex-shrink: 0; margin-top: 2px;
      }
      .choy-avatar-init {
        width: 40px; height: 40px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 14px; font-weight: 800; color: #fff;
        flex-shrink: 0; margin-top: 2px;
      }
      .choy-empty {
        text-align: center;
        padding: 60px 20px;
        color: #94a3b8;
      }
      .choy-empty i { font-size: 40px; margin-bottom: 14px; display: block; }
      .choy-empty strong { display: block; font-size: 15px; color: #475569; margin-bottom: 6px; }
    </style>

    <!-- Cabecera resumen -->
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px">
      <div style="display:flex;align-items:center;gap:14px">
        <div style="width:44px;height:44px;border-radius:12px;background:linear-gradient(135deg,#6366f1,#8b5cf6);display:flex;align-items:center;justify-content:center">
          <i class="fa-solid fa-comments" style="color:#fff;font-size:18px"></i>
        </div>
        <div>
          <h3 style="margin:0;font-size:17px;font-weight:800;color:#0f172a">Todos los comentarios de hoy</h3>
          <p style="margin:2px 0 0;font-size:12px;color:#94a3b8"><?php echo $_choy_fecha; ?> &mdash; <?php echo $_choy_total; ?> comentario<?php echo $_choy_total !== 1 ? 's' : ''; ?> registrado<?php echo $_choy_total !== 1 ? 's' : ''; ?></p>
        </div>
      </div>
      <a href="index.php?ruta=inicio"
         style="display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:600;color:#475569;background:#f1f5f9;border:1px solid #e2e8f0;padding:8px 14px;border-radius:10px;text-decoration:none;transition:background .15s"
         onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
        <i class="fa-solid fa-arrow-left" style="font-size:11px"></i> Volver al tablero
      </a>
    </div>

    <!-- Tabla de comentarios -->
    <div class="choy-card">
      <div class="choy-card-head">
        <div style="display:flex;align-items:center;gap:10px">
          <i class="fa-solid fa-clock-rotate-left" style="color:#6366f1;font-size:15px"></i>
          <span style="font-size:14px;font-weight:700;color:#0f172a">Observaciones registradas hoy</span>
        </div>
        <span style="font-size:11px;font-weight:600;color:#6366f1;background:#eef2ff;padding:4px 12px;border-radius:20px">
          <?php echo $_choy_total; ?> total
        </span>
      </div>

      <?php if (empty($_choy_obs)): ?>
        <div class="choy-empty">
          <i class="fa-solid fa-comment-slash"></i>
          <strong>Sin comentarios hoy</strong>
          <span>Aún no se han registrado observaciones en el día de hoy</span>
        </div>
      <?php else: ?>
        <?php foreach ($_choy_obs as $i => $obs):
          $nombre  = isset($obs['creador_nombre']) ? $obs['creador_nombre'] : 'Usuario';
          $perfil  = isset($obs['creador_perfil']) ? $obs['creador_perfil'] : '';
          $foto    = isset($obs['creador_foto'])   ? $obs['creador_foto']   : '';
          $texto   = isset($obs['observacion'])    ? $obs['observacion']    : '';
          $fecha   = isset($obs['fecha'])          ? $obs['fecha']          : '';
          $idOrden = isset($obs['id_orden'])       ? $obs['id_orden']       : '';
          $hora    = _choyHora($fecha);
          $resumen = _choyTruncar($texto, 200);
          $colPerf = _choyColorPerfil($perfil);
          $initial = mb_strtoupper(mb_substr($nombre, 0, 1));
          $grad    = $_choy_grads[$i % count($_choy_grads)];

          // Construir link a la orden
          $link = 'index.php?ruta=ordenes';
          if (!empty($idOrden) && isset($_choy_ordMap[$idOrden])) {
              $mo = $_choy_ordMap[$idOrden];
              $link = 'index.php?ruta=infoOrden&idOrden=' . $idOrden
                  . '&empresa='    . (isset($mo['id_empresa'])    ? $mo['id_empresa']    : '')
                  . '&asesor='     . (isset($mo['id_Asesor'])     ? $mo['id_Asesor']     : '')
                  . '&cliente='    . (isset($mo['id_usuario'])    ? $mo['id_usuario']    : '')
                  . '&tecnico='    . (isset($mo['id_tecnico'])    ? $mo['id_tecnico']    : '')
                  . '&tecnicodos=' . (isset($mo['id_tecnicoDos']) ? $mo['id_tecnicoDos'] : '')
                  . '&pedido='     . (isset($mo['id_pedido'])     ? $mo['id_pedido']     : '');
          }
        ?>
          <div class="choy-row">
            <!-- Número -->
            <span class="choy-num"><?php echo ($i + 1); ?></span>

            <!-- Avatar -->
            <?php if (!empty($foto)): ?>
              <img src="<?php echo htmlspecialchars($foto); ?>"
                   onerror="this.onerror=null;this.style.display='none';this.nextElementSibling.style.display='flex'"
                   class="choy-avatar">
              <div class="choy-avatar-init" style="display:none;background:<?php echo $grad; ?>">
                <?php echo $initial; ?>
              </div>
            <?php else: ?>
              <div class="choy-avatar-init" style="background:<?php echo $grad; ?>">
                <?php echo $initial; ?>
              </div>
            <?php endif; ?>

            <!-- Contenido -->
            <div style="flex:1;min-width:0">
              <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;flex-wrap:wrap">
                <span style="font-size:13px;font-weight:700;color:#0f172a"><?php echo htmlspecialchars($nombre); ?></span>
                <span style="display:inline-flex;align-items:center;gap:3px;font-size:10px;font-weight:600;color:<?php echo $colPerf[0]; ?>;background:<?php echo $colPerf[1]; ?>;padding:2px 8px;border-radius:8px">
                  <i class="fa-solid <?php echo $colPerf[2]; ?>" style="font-size:8px"></i>
                  <?php echo htmlspecialchars(ucfirst($perfil)); ?>
                </span>
                <span style="font-size:11px;color:#94a3b8;margin-left:auto;flex-shrink:0">
                  <i class="fa-regular fa-clock" style="font-size:9px"></i> <?php echo $hora; ?>
                </span>
              </div>
              <div style="font-size:13px;color:#334155;line-height:1.6;margin-bottom:6px">
                <?php echo htmlspecialchars($resumen); ?>
              </div>
              <?php if (!empty($idOrden)): ?>
              <a href="<?php echo $link; ?>" target="_blank"
                 style="font-size:11px;font-weight:600;color:#6366f1;text-decoration:none;display:inline-flex;align-items:center;gap:4px;transition:color .15s"
                 onmouseover="this.style.color='#4f46e5'" onmouseout="this.style.color='#6366f1'">
                <i class="fa-solid fa-hashtag" style="font-size:9px"></i>Orden <?php echo htmlspecialchars($idOrden); ?>
                <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:8px;margin-left:2px"></i>
              </a>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

  </section>
</div>
