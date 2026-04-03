<?php
if ($_SESSION["perfil"] != "administrador" && $_SESSION["perfil"] != "vendedor") {
    echo '<script>window.location = "inicio";</script>';
    return;
}

$_hc_idCliente   = isset($_GET["idCliente"]) ? intval($_GET["idCliente"]) : 0;
$_hc_nombreCli   = isset($_GET["nombreCliente"]) ? htmlspecialchars($_GET["nombreCliente"]) : "Cliente";

// ── Datos del cliente ──
$_hc_cliente = null;
try {
    $_hc_cliente = ControladorClientes::ctrMostrarClientes("id", $_hc_idCliente);
} catch (Exception $e) {}

// Usar el nombre completo de la base de datos si está disponible
if (is_array($_hc_cliente) && !empty($_hc_cliente["nombre"])) {
    $_hc_nombreCli = htmlspecialchars($_hc_cliente["nombre"]);
}

$_hc_email = (is_array($_hc_cliente) && isset($_hc_cliente["correo"])) ? $_hc_cliente["correo"] : "";
$_hc_tel1  = (is_array($_hc_cliente) && isset($_hc_cliente["telefono"])) ? trim($_hc_cliente["telefono"]) : "";
$_hc_tel2  = (is_array($_hc_cliente) && isset($_hc_cliente["telefonoDos"])) ? trim($_hc_cliente["telefonoDos"]) : "";

$_hc_t1clean = preg_replace('/\D/', '', $_hc_tel1);
$_hc_t2clean = preg_replace('/\D/', '', $_hc_tel2);
$_hc_t1ok = (strlen($_hc_t1clean) === 10);
$_hc_t2ok = (strlen($_hc_t2clean) === 10) && ($_hc_t2clean !== $_hc_t1clean);
$_hc_emailOk = (!empty($_hc_email) && filter_var($_hc_email, FILTER_VALIDATE_EMAIL) !== false);

// Flags para mostrar datos (sin validar formato, solo existencia)
$_hc_tel1Exists = (!empty($_hc_tel1) && $_hc_tel1 !== "sin Telefono");
$_hc_tel2Exists = (!empty($_hc_tel2) && $_hc_tel2 !== "sin whatsapp");

// ── Órdenes del cliente ──
$_hc_ordenes = array();
try {
    $_hc_ordenes = controladorOrdenes::ctrMostrarHistorial("ordenes", $_hc_idCliente);
    if (!is_array($_hc_ordenes)) $_hc_ordenes = array();
} catch (Exception $e) { $_hc_ordenes = array(); }

// ── Pedidos del cliente ──
$_hc_pedidos = array();
try {
    $_hc_pedidos = ControladorPedidos::ctrMostrarHistorial("pedidos", $_hc_idCliente);
    if (!is_array($_hc_pedidos)) $_hc_pedidos = array();
} catch (Exception $e) { $_hc_pedidos = array(); }

// ── Estadísticas rápidas ──
$_hc_totalGastado = 0;
$_hc_entregadas = 0;
$_hc_canceladas = 0;
$_hc_pendientes = 0;
$_hc_primeraFecha = null;
$_hc_diasRecogida = [];
foreach ($_hc_ordenes as $o) {
    $est = isset($o["estado"]) ? $o["estado"] : "";
    if (strpos($est, "Ent") !== false) {
        $_hc_entregadas++;
        $_hc_totalGastado += floatval(isset($o["total"]) ? $o["total"] : 0);
        // Calcular días de recogida para entregadas con ambas fechas
        $fi = isset($o["fecha_ingreso"]) ? trim($o["fecha_ingreso"]) : "";
        $fs = isset($o["fecha_Salida"]) ? trim($o["fecha_Salida"]) : "";
        if (!empty($fi) && !empty($fs)) {
            try {
                $dIng = new DateTime($fi);
                $dSal = new DateTime($fs);
                $diffDias = $dIng->diff($dSal)->days;
                $_hc_diasRecogida[] = $diffDias;
            } catch (Exception $e) {}
        }
    } elseif (strpos($est, "can") !== false) {
        $_hc_canceladas++;
    } else {
        $_hc_pendientes++;
    }
    $fi2 = isset($o["fecha_ingreso"]) ? $o["fecha_ingreso"] : "";
    if (!empty($fi2) && ($_hc_primeraFecha === null || $fi2 < $_hc_primeraFecha)) $_hc_primeraFecha = $fi2;
}

// ── Promedio de recogida ──
$_hc_promedioRecogida = null;
$_hc_recLabel = "Sin datos";
$_hc_recColor = "#64748b";
$_hc_recBg    = "#f1f5f9";
$_hc_recIcon  = "fa-clock";
if (count($_hc_diasRecogida) >= 3) {
    $_hc_promedioRecogida = round(array_sum($_hc_diasRecogida) / count($_hc_diasRecogida), 1);
    $diasTxt = ($_hc_promedioRecogida == 1) ? "día" : "días";
    $_hc_recLabel = "~" . $_hc_promedioRecogida . " " . $diasTxt;
    if ($_hc_promedioRecogida <= 7)      { $_hc_recColor = "#16a34a"; $_hc_recBg = "#f0fdf4"; $_hc_recIcon = "fa-bolt"; }
    elseif ($_hc_promedioRecogida <= 14)  { $_hc_recColor = "#2563eb"; $_hc_recBg = "#eff6ff"; $_hc_recIcon = "fa-clock"; }
    elseif ($_hc_promedioRecogida <= 30) { $_hc_recColor = "#d97706"; $_hc_recBg = "#fffbeb"; $_hc_recIcon = "fa-hourglass-half"; }
    else                                 { $_hc_recColor = "#dc2626"; $_hc_recBg = "#fef2f2"; $_hc_recIcon = "fa-hourglass-end"; }
}

// ── Calificación del cliente ──
$_hc_califLabel = "Sin calificar";
$_hc_califColor = "#64748b";
$_hc_califBg    = "#f1f5f9";
$_hc_califIcon  = "fa-circle-question";
$_hc_califPct   = "";
if (count($_hc_ordenes) >= 3 && ($_hc_entregadas + $_hc_canceladas) > 0) {
    $ratio = $_hc_entregadas / ($_hc_entregadas + $_hc_canceladas) * 100;
    $_hc_califPct = round($ratio) . "%";
    if ($ratio >= 90)     { $_hc_califLabel = "Excelente"; $_hc_califColor = "#16a34a"; $_hc_califBg = "#f0fdf4"; $_hc_califIcon = "fa-star"; }
    elseif ($ratio >= 70) { $_hc_califLabel = "Bueno";     $_hc_califColor = "#2563eb"; $_hc_califBg = "#eff6ff"; $_hc_califIcon = "fa-thumbs-up"; }
    elseif ($ratio >= 50) { $_hc_califLabel = "Regular";   $_hc_califColor = "#d97706"; $_hc_califBg = "#fffbeb"; $_hc_califIcon = "fa-minus-circle"; }
    else                  { $_hc_califLabel = "Malo";      $_hc_califColor = "#dc2626"; $_hc_califBg = "#fef2f2"; $_hc_califIcon = "fa-thumbs-down"; }
}
$_hc_antiguedad = "";
if ($_hc_primeraFecha) {
    try {
        $diff = (new DateTime($_hc_primeraFecha))->diff(new DateTime());
        if ($diff->y > 0) $_hc_antiguedad = $diff->y . " año" . ($diff->y > 1 ? "s" : "");
        elseif ($diff->m > 0) $_hc_antiguedad = $diff->m . " mes" . ($diff->m > 1 ? "es" : "");
        else $_hc_antiguedad = $diff->d . " día" . ($diff->d > 1 ? "s" : "");
    } catch (Exception $e) {}
}

// ── Cache de técnicos y asesores para el historial ──
$_hc_tecnicosCache = [];
$_hc_asesoresCache = [];

function _hc_getNombreTecnico($idTec, &$cache) {
    if (empty($idTec) || $idTec == "0") return "Sin asignar";
    if (isset($cache[$idTec])) return $cache[$idTec];
    try {
        $tec = ControladorTecnicos::ctrMostrarTecnicos("id", $idTec);
        $cache[$idTec] = isset($tec["nombre"]) ? $tec["nombre"] : "Sin asignar";
    } catch (Exception $e) {
        $cache[$idTec] = "Sin asignar";
    }
    return $cache[$idTec];
}

function _hc_getNombreAsesor($idAs, &$cache) {
    if (empty($idAs) || $idAs == "0") return "Sin asignar";
    if (isset($cache[$idAs])) return $cache[$idAs];
    try {
        $as = Controladorasesores::ctrMostrarAsesoresEleg("id", $idAs);
        $cache[$idAs] = isset($as["nombre"]) ? $as["nombre"] : "Sin asignar";
    } catch (Exception $e) {
        $cache[$idAs] = "Sin asignar";
    }
    return $cache[$idAs];
}
?>

<style>
.hc-card{background:#fff;border:1px solid #e2e8f0;border-radius:14px;box-shadow:0 1px 3px rgba(15,23,42,.06),0 4px 14px rgba(15,23,42,.04);overflow:hidden;margin-bottom:20px}
.hc-card-head{display:flex;align-items:center;justify-content:space-between;padding:18px 22px 14px;border-bottom:1px solid #f1f5f9}
.hc-card-title{display:flex;align-items:center;gap:10px;font-size:14px;font-weight:700;color:#0f172a;margin:0}
.hc-card-title i{font-size:15px;color:#6366f1;opacity:.85}
.hc-stat{text-align:center;padding:18px 12px;border-right:1px solid #f1f5f9}
.hc-stat:last-child{border-right:none}
.hc-stat-val{font-size:22px;font-weight:800;color:#0f172a;letter-spacing:-.02em}
.hc-stat-lbl{font-size:11px;color:#94a3b8;font-weight:500;margin-top:2px}
.hc-badge{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600}
.hc-tab-bar{display:flex;gap:4px;background:#f1f5f9;border-radius:8px;padding:3px}
.hc-tab{padding:6px 16px;border:none;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;transition:all .15s;background:transparent;color:#64748b}
.hc-tab.active{background:#6366f1;color:#fff}
.hc-table{width:100%;border-collapse:separate;border-spacing:0}
.hc-table thead th{padding:10px 14px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#94a3b8;background:#f8fafc;border-bottom:1px solid #e2e8f0}
.hc-table tbody tr{transition:background .12s}
.hc-table tbody tr:hover{background:#f8fafc}
.hc-table tbody td{padding:11px 14px;font-size:13px;color:#0f172a;border-bottom:1px solid #f1f5f9;vertical-align:middle}
.hc-table tbody tr:last-child td{border-bottom:none}
.hc-empty{text-align:center;padding:40px 20px;color:#94a3b8}
.hc-empty i{font-size:32px;margin-bottom:10px;display:block;opacity:.4}
</style>

<div class="content-wrapper">

  <section class="content-header">
    <h1 style="font-weight:800;font-size:20px;color:#0f172a">
      <i class="fa-solid fa-clock-rotate-left" style="color:#6366f1;margin-right:6px"></i>
      Historial: <?php echo $_hc_nombreCli; ?>
    </h1>
    <ol class="breadcrumb">
      <li><a href="index.php?ruta=inicio"><i class="fa-solid fa-gauge"></i> Inicio</a></li>
      <li><a href="index.php?ruta=clientes">Clientes</a></li>
      <li class="active"><?php echo $_hc_nombreCli; ?></li>
    </ol>
  </section>

  <section class="content">

    <!-- ══ FICHA DEL CLIENTE ══ -->
    <div class="hc-card">
      <div style="padding:22px;display:flex;align-items:center;gap:18px;flex-wrap:wrap">
        <!-- Avatar -->
        <div style="width:56px;height:56px;border-radius:50%;background:linear-gradient(135deg,#6366f1,#818cf8);display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:800;color:#fff;flex-shrink:0;box-shadow:0 4px 12px rgba(99,102,241,.25)">
          <?php echo mb_strtoupper(mb_substr($_hc_nombreCli, 0, 2)); ?>
        </div>
        <!-- Info -->
        <div style="flex:1;min-width:200px">
          <div style="font-size:18px;font-weight:800;color:#0f172a;margin-bottom:4px"><?php echo $_hc_nombreCli; ?></div>
          <div style="display:flex;flex-wrap:wrap;gap:12px;font-size:12px;color:#64748b">
            <?php
              // Mostrar asesor del cliente
              $idAsesorCliente = (is_array($_hc_cliente) && isset($_hc_cliente["id_Asesor"])) ? $_hc_cliente["id_Asesor"] : "";
              $nombreAsesorCliente = _hc_getNombreAsesor($idAsesorCliente, $_hc_asesoresCache);
              if ($nombreAsesorCliente !== "Sin asignar"):
            ?>
              <span><i class="fa-solid fa-headphones" style="margin-right:4px;color:#6366f1"></i>Asesor: <strong><?php echo htmlspecialchars($nombreAsesorCliente); ?></strong></span>
            <?php endif; ?>
            <?php if (!empty($_hc_email)): ?>
              <?php if ($_hc_emailOk): ?>
                <a href="mailto:<?php echo htmlspecialchars($_hc_email); ?>" style="text-decoration:none">
                  <span style="color:#64748b"><i class="fa-solid fa-envelope" style="margin-right:4px;color:#94a3b8"></i><?php echo htmlspecialchars($_hc_email); ?></span>
                </a>
              <?php else: ?>
                <span style="color:#dc2626"><i class="fa-solid fa-envelope" style="margin-right:4px;color:#dc2626"></i><?php echo htmlspecialchars($_hc_email); ?> <i class="fa-solid fa-triangle-exclamation" style="font-size:10px" title="Correo inválido"></i></span>
              <?php endif; ?>
            <?php endif; ?>
            <?php if ($_hc_tel1Exists): ?>
              <?php if ($_hc_t1ok): ?>
                <span><i class="fa-solid fa-phone" style="margin-right:4px;color:#94a3b8"></i><?php echo htmlspecialchars($_hc_tel1); ?></span>
              <?php else: ?>
                <span style="color:#dc2626"><i class="fa-solid fa-phone" style="margin-right:4px;color:#dc2626"></i><?php echo htmlspecialchars($_hc_tel1); ?> <i class="fa-solid fa-triangle-exclamation" style="font-size:10px" title="Teléfono inválido"></i></span>
              <?php endif; ?>
            <?php endif; ?>
            <?php if ($_hc_tel2Exists): ?>
              <?php if ($_hc_t2ok): ?>
                <span><i class="fa-solid fa-phone" style="margin-right:4px;color:#94a3b8"></i><?php echo htmlspecialchars($_hc_tel2); ?></span>
              <?php else: ?>
                <span style="color:#dc2626"><i class="fa-solid fa-phone" style="margin-right:4px;color:#dc2626"></i><?php echo htmlspecialchars($_hc_tel2); ?> <i class="fa-solid fa-triangle-exclamation" style="font-size:10px" title="WhatsApp inválido"></i></span>
              <?php endif; ?>
            <?php endif; ?>
            <?php if (!empty($_hc_antiguedad)): ?>
              <span><i class="fa-solid fa-calendar" style="margin-right:4px;color:#94a3b8"></i>Cliente desde hace <?php echo $_hc_antiguedad; ?></span>
            <?php endif; ?>
          </div>
        </div>
        <!-- Actions -->
        <div style="display:flex;gap:6px;flex-shrink:0">
          <?php if ($_hc_t1ok):
            $waMsg = urlencode("Hola " . $_hc_nombreCli . ", le contactamos de EGS. ¿En qué podemos ayudarle?");
          ?>
            <a href="https://wa.me/52<?php echo $_hc_t1clean; ?>?text=<?php echo $waMsg; ?>" target="_blank"
               style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:10px;background:#25d366;color:#fff;font-size:12px;font-weight:600;text-decoration:none;transition:background .15s">
              <i class="fa-brands fa-whatsapp"></i> WhatsApp
            </a>
          <?php endif; ?>
          <?php if ($_hc_t2ok): ?>
            <a href="https://wa.me/52<?php echo $_hc_t2clean; ?>?text=<?php echo $waMsg; ?>" target="_blank"
               style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:10px;background:#128c7e;color:#fff;font-size:12px;font-weight:600;text-decoration:none;transition:background .15s">
              <i class="fa-brands fa-whatsapp"></i> Tel. 2
            </a>
          <?php endif; ?>
          <a href="index.php?ruta=clientes" style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:10px;background:#f1f5f9;color:#475569;font-size:12px;font-weight:600;text-decoration:none;border:1px solid #e2e8f0">
            <i class="fa-solid fa-arrow-left"></i> Volver
          </a>
        </div>
      </div>

      <!-- Stats strip -->
      <div style="display:flex;border-top:1px solid #f1f5f9">
        <div class="hc-stat" style="flex:1">
          <div style="display:inline-flex;align-items:center;gap:6px;padding:5px 14px;border-radius:20px;background:<?php echo $_hc_califBg; ?>;margin-bottom:4px">
            <i class="fa-solid <?php echo $_hc_califIcon; ?>" style="color:<?php echo $_hc_califColor; ?>;font-size:14px"></i>
            <span style="font-size:14px;font-weight:800;color:<?php echo $_hc_califColor; ?>"><?php echo $_hc_califLabel; ?></span>
            <?php if ($_hc_califPct): ?>
              <span style="font-size:11px;font-weight:600;color:<?php echo $_hc_califColor; ?>;opacity:.7">(<?php echo $_hc_califPct; ?>)</span>
            <?php endif; ?>
          </div>
          <div class="hc-stat-lbl">Calificación</div>
        </div>
        <div class="hc-stat" style="flex:1">
          <div style="display:inline-flex;align-items:center;gap:6px;padding:5px 14px;border-radius:20px;background:<?php echo $_hc_recBg; ?>;margin-bottom:4px">
            <i class="fa-solid <?php echo $_hc_recIcon; ?>" style="color:<?php echo $_hc_recColor; ?>;font-size:14px"></i>
            <span style="font-size:14px;font-weight:800;color:<?php echo $_hc_recColor; ?>"><?php echo $_hc_recLabel; ?></span>
          </div>
          <div class="hc-stat-lbl">Tiempo de recogida</div>
        </div>
        <div class="hc-stat" style="flex:1">
          <div class="hc-stat-val" style="color:#6366f1">$<?php echo number_format($_hc_totalGastado, 0); ?></div>
          <div class="hc-stat-lbl">Total acumulado <span style="font-size:9px;color:#a5b4fc">(entregadas)</span></div>
        </div>
        <div class="hc-stat" style="flex:1">
          <div class="hc-stat-val"><?php echo count($_hc_ordenes); ?></div>
          <div class="hc-stat-lbl">Órdenes</div>
        </div>
        <div class="hc-stat" style="flex:1">
          <div class="hc-stat-val" style="color:#22c55e"><?php echo $_hc_entregadas; ?></div>
          <div class="hc-stat-lbl">Entregadas</div>
        </div>
        <div class="hc-stat" style="flex:1">
          <div class="hc-stat-val" style="color:#dc2626"><?php echo $_hc_canceladas; ?></div>
          <div class="hc-stat-lbl">Canceladas</div>
        </div>
        <div class="hc-stat" style="flex:1">
          <div class="hc-stat-val" style="color:#f59e0b"><?php echo $_hc_pendientes; ?></div>
          <div class="hc-stat-lbl">Pendientes</div>
        </div>
        <div class="hc-stat" style="flex:1">
          <div class="hc-stat-val"><?php echo count($_hc_pedidos); ?></div>
          <div class="hc-stat-lbl">Pedidos</div>
        </div>
      </div>
    </div>

    <!-- ══ TABS: Órdenes / Pedidos ══ -->
    <div class="hc-card">
      <div class="hc-card-head">
        <h4 class="hc-card-title"><i class="fa-solid fa-list-check"></i> Registros</h4>
        <div class="hc-tab-bar" id="hcTabs">
          <button type="button" class="hc-tab active" data-tab="ordenes">
            <i class="fa-solid fa-clipboard-list" style="margin-right:4px"></i>Órdenes (<?php echo count($_hc_ordenes); ?>)
          </button>
          <button type="button" class="hc-tab" data-tab="pedidos">
            <i class="fa-solid fa-box-open" style="margin-right:4px"></i>Pedidos (<?php echo count($_hc_pedidos); ?>)
          </button>
        </div>
      </div>

      <!-- Órdenes Tab -->
      <div id="hcTabOrdenes">
        <?php if (empty($_hc_ordenes)): ?>
          <div class="hc-empty">
            <i class="fa-solid fa-clipboard-list"></i>
            <strong style="display:block;color:#0f172a;font-size:14px">Sin órdenes registradas</strong>
          </div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="hc-table">
              <thead>
                <tr>
                  <th>Orden</th>
                  <th style="width:56px"></th>
                  <th>Equipo</th>
                  <th>Técnico</th>
                  <th>Asesor</th>
                  <th>Estado</th>
                  <th style="text-align:right">Total</th>
                  <th>Ingreso</th>
                  <th style="text-align:center">Antigüedad</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($_hc_ordenes as $o):
                  $est = isset($o["estado"]) ? $o["estado"] : "";
                  $total = floatval(isset($o["total"]) ? $o["total"] : 0);
                  $fi = isset($o["fecha_ingreso"]) ? $o["fecha_ingreso"] : "";
                  $marca = isset($o["marcaDelEquipo"]) ? $o["marcaDelEquipo"] : "";
                  $modelo = isset($o["modeloDelEquipo"]) ? $o["modeloDelEquipo"] : "";
                  $equipo = trim($marca . " " . $modelo);
                  if (empty($equipo)) $equipo = "—";

                  // Imagen del equipo
                  $imgEquipo = "";
                  if (!empty($o["multimedia"])) {
                      $album = json_decode($o["multimedia"], true);
                      if (is_array($album)) {
                          foreach ($album as $img) {
                              if (isset($img["foto"]) && !empty($img["foto"])) {
                                  $imgEquipo = $img["foto"];
                                  break;
                              }
                          }
                      }
                  }

                  // Antigüedad
                  $dias = 0;
                  if (!empty($fi)) {
                      try { $dias = max(0, (new DateTime($fi))->diff(new DateTime())->days); } catch(Exception $e){}
                  }

                  // Badge color
                  if (strpos($est, "Ent") !== false) { $bc='#f0fdf4'; $btc='#16a34a'; $elbl='Entregada'; }
                  elseif (strpos($est, "can") !== false) { $bc='#fef2f2'; $btc='#dc2626'; $elbl='Cancelada'; }
                  elseif (strpos($est, "AUT") !== false) { $bc='#fef3c7'; $btc='#92400e'; $elbl='Pend. Autorización'; }
                  elseif (strpos($est, "ok") !== false) { $bc='#dbeafe'; $btc='#1d4ed8'; $elbl='Aceptada'; }
                  elseif (strpos($est, "Terminada") !== false || strpos($est, "ter") !== false) { $bc='#ecfeff'; $btc='#0e7490'; $elbl='Terminada'; }
                  else { $bc='#f1f5f9'; $btc='#475569'; $elbl = mb_substr($est, 0, 20); }

                  // Link
                  $link = 'index.php?ruta=infoOrden&idOrden='.$o["id"]
                      .'&empresa='.(isset($o["id_empresa"]) ? $o["id_empresa"] : '')
                      .'&asesor='.(isset($o["id_Asesor"]) ? $o["id_Asesor"] : '')
                      .'&cliente='.(isset($o["id_usuario"]) ? $o["id_usuario"] : '')
                      .'&tecnico='.(isset($o["id_tecnico"]) ? $o["id_tecnico"] : '')
                      .'&tecnicodos='.(isset($o["id_tecnicoDos"]) ? $o["id_tecnicoDos"] : '')
                      .'&pedido='.(isset($o["id_pedido"]) ? $o["id_pedido"] : '');

                  // Nombres de técnico y asesor
                  $idTec = isset($o["id_tecnico"]) ? $o["id_tecnico"] : "";
                  $nombreTecnico = _hc_getNombreTecnico($idTec, $_hc_tecnicosCache);
                  $idAsesor = isset($o["id_Asesor"]) ? $o["id_Asesor"] : "";
                  $nombreAsesor = _hc_getNombreAsesor($idAsesor, $_hc_asesoresCache);
                ?>
                <tr>
                  <td><span style="font-weight:700;color:#6366f1">#<?php echo $o["id"]; ?></span></td>
                  <td style="padding:6px 4px">
                    <?php if (!empty($imgEquipo)): ?>
                      <img src="<?php echo htmlspecialchars($imgEquipo); ?>" alt="Equipo"
                           style="width:44px;height:44px;border-radius:8px;object-fit:cover;border:1px solid #e2e8f0;cursor:pointer"
                           onclick="window.open(this.src,'_blank')"
                           onerror="this.onerror=null;this.style.display='none';this.nextElementSibling.style.display='flex';">
                      <div style="display:none;width:44px;height:44px;border-radius:8px;background:#f1f5f9;align-items:center;justify-content:center;color:#cbd5e1;font-size:16px;border:1px solid #e2e8f0">
                        <i class="fa-solid fa-screwdriver-wrench"></i>
                      </div>
                    <?php else: ?>
                      <div style="width:44px;height:44px;border-radius:8px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;color:#cbd5e1;font-size:16px;border:1px solid #e2e8f0">
                        <i class="fa-solid fa-screwdriver-wrench"></i>
                      </div>
                    <?php endif; ?>
                  </td>
                  <td>
                    <div style="font-weight:600"><?php echo htmlspecialchars($equipo); ?></div>
                    <?php if (!empty($o["numeroDeSerieDelEquipo"])): ?>
                      <div style="font-size:10px;color:#94a3b8">S/N: <?php echo htmlspecialchars($o["numeroDeSerieDelEquipo"]); ?></div>
                    <?php endif; ?>
                  </td>
                  <td>
                    <div style="font-size:12px;font-weight:600;color:#0f172a">
                      <i class="fa-solid fa-screwdriver-wrench" style="color:#94a3b8;margin-right:3px;font-size:10px"></i><?php echo htmlspecialchars($nombreTecnico); ?>
                    </div>
                  </td>
                  <td>
                    <div style="font-size:12px;font-weight:600;color:#0f172a">
                      <i class="fa-solid fa-headphones" style="color:#94a3b8;margin-right:3px;font-size:10px"></i><?php echo htmlspecialchars($nombreAsesor); ?>
                    </div>
                  </td>
                  <td><span class="hc-badge" style="background:<?php echo $bc; ?>;color:<?php echo $btc; ?>"><?php echo $elbl; ?></span></td>
                  <td style="text-align:right;font-weight:700">$<?php echo number_format($total, 0); ?></td>
                  <td style="font-size:12px;color:#64748b"><?php echo !empty($fi) ? date("d/m/Y", strtotime($fi)) : "—"; ?></td>
                  <td style="text-align:center">
                    <span style="font-weight:700;font-size:12px;color:<?php echo $dias > 30 ? '#ef4444' : ($dias > 15 ? '#f59e0b' : '#64748b'); ?>">
                      <?php echo $dias; ?>d
                    </span>
                  </td>
                  <td style="text-align:center">
                    <a href="<?php echo $link; ?>" target="_blank"
                       style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:8px;background:#6366f1;color:#fff;font-size:12px;text-decoration:none;transition:background .15s"
                       onmouseover="this.style.background='#4f46e5'" onmouseout="this.style.background='#6366f1'">
                      <i class="fa-solid fa-eye"></i>
                    </a>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>

      <!-- Pedidos Tab -->
      <div id="hcTabPedidos" style="display:none">
        <?php if (empty($_hc_pedidos)): ?>
          <div class="hc-empty">
            <i class="fa-solid fa-box-open"></i>
            <strong style="display:block;color:#0f172a;font-size:14px">Sin pedidos registrados</strong>
          </div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="hc-table">
              <thead>
                <tr>
                  <th>Pedido</th>
                  <th>Orden</th>
                  <th>Producto</th>
                  <th>Estado</th>
                  <th style="text-align:right">Adeudo</th>
                  <th style="text-align:right">Total</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($_hc_pedidos as $p):
                  $pEst = isset($p["estado"]) ? $p["estado"] : "";
                  $pTotal = floatval(isset($p["total"]) ? $p["total"] : 0);
                  $pAdeudo = floatval(isset($p["adeudo"]) ? $p["adeudo"] : 0);
                  $pOrden = isset($p["id_orden"]) ? $p["id_orden"] : "0";

                  $pProd = "—";
                  if (!empty($p["productos"])) {
                      $prods = json_decode($p["productos"], true);
                      if (is_array($prods) && !empty($prods)) {
                          $pProd = isset($prods[0]["Descripcion"]) ? mb_substr($prods[0]["Descripcion"], 0, 35) : "—";
                          if (count($prods) > 1) $pProd .= " +" . (count($prods) - 1);
                      }
                  }

                  $pLink = 'index.php?ruta=infopedido&idPedido='.$p["id"]
                      .'&empresa='.(isset($p["id_empresa"]) ? $p["id_empresa"] : '')
                      .'&asesor='.(isset($p["id_Asesor"]) ? $p["id_Asesor"] : '')
                      .'&cliente='.(isset($p["id_cliente"]) ? $p["id_cliente"] : '');
                ?>
                <tr>
                  <td><span style="font-weight:700;color:#8b5cf6">#<?php echo $p["id"]; ?></span></td>
                  <td>
                    <?php if ($pOrden != "0" && !empty($pOrden)): ?>
                      <span style="font-weight:600">Orden #<?php echo $pOrden; ?></span>
                    <?php else: ?>
                      <span style="color:#94a3b8;font-size:12px">Sin orden</span>
                    <?php endif; ?>
                  </td>
                  <td style="font-size:12px;max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?php echo htmlspecialchars($pProd); ?></td>
                  <td>
                    <span class="hc-badge" style="background:#f1f5f9;color:#475569"><?php echo htmlspecialchars(mb_substr($pEst, 0, 20)); ?></span>
                  </td>
                  <td style="text-align:right;font-weight:600;color:<?php echo $pAdeudo > 0 ? '#ef4444' : '#22c55e'; ?>">
                    $<?php echo number_format($pAdeudo, 0); ?>
                  </td>
                  <td style="text-align:right;font-weight:700">$<?php echo number_format($pTotal, 0); ?></td>
                  <td style="text-align:center">
                    <a href="<?php echo $pLink; ?>" target="_blank"
                       style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:8px;background:#8b5cf6;color:#fff;font-size:12px;text-decoration:none;transition:background .15s"
                       onmouseover="this.style.background='#7c3aed'" onmouseout="this.style.background='#8b5cf6'">
                      <i class="fa-solid fa-eye"></i>
                    </a>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>

    </div>

  </section>
</div>

<script>
$(function(){
  $('#hcTabs').on('click', '.hc-tab', function(){
    var tab = $(this).data('tab');
    $('#hcTabs .hc-tab').removeClass('active').css({background:'transparent',color:'#64748b'});
    $(this).addClass('active').css({background:'#6366f1',color:'#fff'});
    if (tab === 'ordenes') {
      $('#hcTabOrdenes').show();
      $('#hcTabPedidos').hide();
    } else {
      $('#hcTabOrdenes').hide();
      $('#hcTabPedidos').show();
    }
  });
});
</script>
