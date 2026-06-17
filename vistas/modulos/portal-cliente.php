<?php
/**
 * portal-cliente.php
 *
 * Portal público del cliente (sin login). El token de sesión es el
 * mismo de dinero_electronico.token: un solo identificador opaco por
 * cliente que da acceso a todo su universo de información.
 *
 * URL: ?ruta=portal-cliente&token=<64hex>&tab=<opcional>&orden=<opcional>
 *
 * Tabs: equipos | monedero | historial | ayuda | privacidad
 *
 * Seguridad:
 *  - Token validado con regex /^[a-f0-9]{64}$/
 *  - Todas las queries filtran por id_usuario derivado del token
 *  - Sin id de orden/cliente en POST: siempre se toma del token
 */

$pcToken = isset($_GET["token"]) ? trim((string) $_GET["token"]) : "";
$pcTab   = isset($_GET["tab"])   ? trim((string) $_GET["tab"])   : "equipos";
$pcOrdenSel = isset($_GET["orden"]) ? intval($_GET["orden"]) : 0;

$pcTabsValidas = array("equipos", "monedero", "historial", "ayuda", "privacidad");
if (!in_array($pcTab, $pcTabsValidas, true)) $pcTab = "equipos";

$pcOk = false;
$pcCliente = null;
$pcIdCliente = 0;
$pcMonederoData = null;

if ($pcToken !== "" && preg_match('/^[a-f0-9]{64}$/', $pcToken)) {
    try {
        $pcMonederoData = ControladorRecompensas::ctrObtenerMonederoPorToken($pcToken);
        if (is_array($pcMonederoData) && !empty($pcMonederoData["nombre_cliente"])) {
            $pcOk = true;
            // El controlador no expone id_cliente directo; lo obtenemos del modelo.
            $monederoFila = ModeloRecompensas::mdlObtenerMonederoPorToken($pcToken);
            if (is_array($monederoFila) && !empty($monederoFila["id_cliente"])) {
                $pcIdCliente = intval($monederoFila["id_cliente"]);
            }
        }
    } catch (Exception $e) {
        $pcOk = false;
    }
}

// ── Helpers (mismas funciones que tenía estado-orden-cliente.php) ──
if (!function_exists("pc_estadoInfo")) {
    function pc_estadoInfo($estado)
    {
        $e = strtolower((string) $estado);
        if (strpos($e, "entregad") !== false)  return array("#16a34a", "fa-circle-check",     "Entregado",                  "Tu equipo ya fue entregado. ¡Gracias por tu confianza!");
        if (strpos($e, "termin")   !== false)  return array("#0ea5e9", "fa-flag-checkered",   "Terminada",                  "El servicio terminó y tu equipo está listo para entrega.");
        if (strpos($e, "rev")      !== false)  return array("#f59e0b", "fa-magnifying-glass", "En revisión",                "Estamos revisando tu equipo para definir el servicio.");
        if (strpos($e, "sup")      !== false)  return array("#8b5cf6", "fa-user-gear",        "En supervisión",             "Tu equipo está en supervisión de calidad.");
        if (strpos($e, "aut")      !== false)  return array("#f97316", "fa-clock",            "Pendiente de autorización",  "Esperamos tu autorización para continuar con el servicio.");
        if (strpos($e, "aceptad")  !== false || strpos($e, "(ok)") !== false)
                                                return array("#22c55e", "fa-thumbs-up",        "Aceptada",                   "El servicio fue aceptado y está en proceso.");
        if (strpos($e, "cancel")   !== false)  return array("#ef4444", "fa-circle-xmark",     "Cancelada",                  "Esta orden fue cancelada.");
        if (strpos($e, "sin reparaci") !== false) return array("#64748b", "fa-circle-info",    "Sin reparación",             "Tu equipo no requiere reparación.");
        return array("#64748b", "fa-circle-info", $estado ? $estado : "En proceso", "Tu orden está en proceso.");
    }
}
if (!function_exists("pc_fecha")) {
    function pc_fecha($f, $conHora = true)
    {
        if (empty($f) || $f === "0000-00-00 00:00:00" || $f === "0000-00-00") return "—";
        $ts = strtotime($f);
        if ($ts === false) return "—";
        return date($conHora ? "d/m/Y H:i" : "d/m/Y", $ts);
    }
}
if (!function_exists("pc_preciosVisibles")) {
    function pc_preciosVisibles($estado)
    {
        $e = strtolower((string) $estado);
        return (
            strpos($e, "aceptad") !== false ||
            strpos($e, "termin") !== false ||
            strpos($e, "entregad") !== false ||
            strpos($e, "sin reparaci") !== false
        );
    }
}

// ── POSTs (todos requieren $pcOk y usan $pcIdCliente del token) ──
$pcMsgComentario = null;
$pcMsgPrivacidad = null;
$pcMsgAyuda      = null;

if ($pcOk && $_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["comentarioCliente"]) && isset($_POST["idOrdenComentario"])) {
        // El comentario va atado a una orden; validamos que la orden pertenece al cliente
        $idOrdenCom = intval($_POST["idOrdenComentario"]);
        $ordenesCliente = controladorOrdenes::ctrMostrarHistorial("ordenes", $pcIdCliente);
        $pertenece = false;
        if (is_array($ordenesCliente)) {
            foreach ($ordenesCliente as $oc) {
                if (intval($oc["id"]) === $idOrdenCom) { $pertenece = true; break; }
            }
        }
        if ($pertenece) {
            $pcMsgComentario = controladorComentarioCliente::ctrlGuardarComentario($idOrdenCom);
        } else {
            $pcMsgComentario = "error";
        }
    }
    if (isset($_POST["aceptaPrivacidad"])) {
        $pcMsgPrivacidad = controladorAceptacionPrivacidad::ctrlGuardar($pcIdCliente);
    }
    if (isset($_POST["mensajeAyuda"])) {
        $pcMsgAyuda = controladorSolicitudAyuda::ctrlGuardar($pcIdCliente);
    }
}

// ── Datos cargados según el tab para evitar queries innecesarias ──
$pcOrdenes = array();
$pcEmpresa = null;
$pcAceptacion = null;
$pcSolicitudes = array();

if ($pcOk) {
    if ($pcTab === "equipos" || $pcTab === "historial" || ($pcTab === "ayuda")) {
        $todas = controladorOrdenes::ctrMostrarHistorial("ordenes", $pcIdCliente);
        if (is_array($todas)) $pcOrdenes = $todas;
        // Orden más reciente primero
        usort($pcOrdenes, function($a, $b){
            $fa = isset($a["fecha"]) ? strtotime($a["fecha"]) : 0;
            $fb = isset($b["fecha"]) ? strtotime($b["fecha"]) : 0;
            return $fb <=> $fa;
        });
    }

    if ($pcTab === "ayuda") {
        // Datos de empresa para botones de contacto (primera empresa visible)
        try {
            $idEmpresa = 1;
            if (!empty($pcOrdenes)) $idEmpresa = intval($pcOrdenes[0]["id_empresa"]);
            $pcEmpresa = ControladorVentas::ctrMostrarEmpresasParaTiketimp("id", $idEmpresa);
        } catch (Exception $e) { $pcEmpresa = null; }
        $pcSolicitudes = controladorSolicitudAyuda::ctrListar($pcIdCliente);
    }

    // SIEMPRE cargamos la aceptación de privacidad para mostrar el banner
    // recordatorio en cualquier tab si está pendiente.
    $pcAceptacion = controladorAceptacionPrivacidad::ctrObtener($pcIdCliente);
}

// Flag: ¿el cliente todavía no ha respondido al aviso?
$pcPrivacidadPendiente = $pcOk && !is_array($pcAceptacion);

// Nombre corto para el header
$pcNombre = "";
if ($pcOk && !empty($pcMonederoData["nombre_cliente"])) {
    $pcNombre = $pcMonederoData["nombre_cliente"];
    if (mb_strlen($pcNombre) > 28) {
        $partes = explode(" ", trim($pcNombre));
        if (count($partes) >= 2) $pcNombre = $partes[0] . " " . $partes[1];
        if (mb_strlen($pcNombre) > 28) $pcNombre = mb_substr($pcNombre, 0, 26) . "...";
    }
}

// Helper para construir URLs de tab preservando el token
if (!function_exists("pc_url")) {
    function pc_url($token, $tab, $orden = 0)
    {
        $u = "?ruta=portal-cliente&token=" . urlencode($token) . "&tab=" . urlencode($tab);
        if ($orden > 0) $u .= "&orden=" . intval($orden);
        return $u;
    }
}

// Orden seleccionada (para detalle expandido)
$pcOrdenDetalle = null;
if ($pcOk && $pcOrdenSel > 0) {
    foreach ($pcOrdenes as $o) {
        if (intval($o["id"]) === $pcOrdenSel) { $pcOrdenDetalle = $o; break; }
    }
}
?>

<style>
/* ═══ Portal del Cliente (pc-) ═══ */
.main-header, .main-sidebar, .left-side, .control-sidebar, .main-footer { display: none !important; }
body.sidebar-mini .content-wrapper, body .content-wrapper { margin-left: 0 !important; }

.pc-wrap { margin-left: 0 !important; background: linear-gradient(135deg,#f8fafc 0%,#e2e8f0 100%); min-height: 100vh; padding: 16px 12px 48px; }
.pc-shell { max-width: 760px; margin: 0 auto; }

.pc-topbar { display: flex; align-items: center; gap: 12px; margin-bottom: 14px; padding: 12px 16px; background: #fff; border-radius: 14px; box-shadow: 0 4px 12px rgba(0,0,0,.05); border: 1px solid #e2e8f0; }
.pc-topbar img { height: 40px; width: auto; }
.pc-topbar .who { flex: 1; min-width: 0; }
.pc-topbar .who .hi { font-size: 11px; color: #64748b; text-transform: uppercase; letter-spacing: 1px; font-weight: 600; }
.pc-topbar .who .nm { font-size: 15px; font-weight: 800; color: #0f172a; line-height: 1.2; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

.pc-tabs { display: flex; gap: 4px; background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 4px; margin-bottom: 14px; overflow-x: auto; box-shadow: 0 4px 12px rgba(0,0,0,.04); }
.pc-tab { flex: 1; min-width: 90px; padding: 9px 8px; border: none; background: transparent; color: #64748b; font-size: 12px; font-weight: 700; border-radius: 8px; text-decoration: none; text-align: center; transition: all .15s; white-space: nowrap; cursor: pointer; }
.pc-tab i { display: block; font-size: 14px; margin-bottom: 3px; }
.pc-tab.active { background: #0f172a; color: #fff; box-shadow: 0 2px 8px rgba(15,23,42,.25); }
.pc-tab:not(.active):hover { background: #f1f5f9; color: #0f172a; }

.pc-card { background: #fff; border-radius: 14px; box-shadow: 0 4px 14px rgba(0,0,0,.06); border: 1px solid #e2e8f0; margin-bottom: 14px; overflow: hidden; }
.pc-card-head { display: flex; align-items: center; gap: 10px; padding: 14px 18px; border-bottom: 1px solid #f1f5f9; }
.pc-card-head i { font-size: 16px; color: #0ea5e9; width: 20px; text-align: center; }
.pc-card-head h3 { margin: 0; font-size: 14px; font-weight: 800; color: #0f172a; }
.pc-card-body { padding: 16px 18px; }

.pc-alert { padding: 10px 14px; border-radius: 10px; font-size: 13px; margin-bottom: 12px; }
.pc-alert.ok   { background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; }
.pc-alert.warn { background: #fffbeb; border: 1px solid #fde68a; color: #92400e; }
.pc-alert.err  { background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; }
.pc-empty { text-align: center; color: #94a3b8; font-size: 13px; padding: 14px 0; }

/* ─ Grid de equipos ─ */
.pc-grid { display: grid; grid-template-columns: 1fr; gap: 12px; }
.pc-eq { display: block; background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px; text-decoration: none; transition: all .15s; }
.pc-eq:hover { transform: translateY(-2px); box-shadow: 0 8px 22px rgba(0,0,0,.08); border-color: #cbd5e1; text-decoration: none; }
.pc-eq-head { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; }
.pc-eq-badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 16px; font-size: 11px; font-weight: 700; color: #fff; }
.pc-eq-num { margin-left: auto; font-size: 11px; color: #94a3b8; font-weight: 600; }
.pc-eq-name { font-size: 14px; font-weight: 800; color: #0f172a; line-height: 1.3; margin-bottom: 6px; }
.pc-eq-meta { display: flex; justify-content: space-between; font-size: 12px; color: #64748b; }
.pc-eq-total { font-weight: 700; color: #0f172a; }

/* ─ Estado banner de la orden seleccionada ─ */
.pc-estado { text-align: center; color: #fff; padding: 22px 16px; }
.pc-estado .ico { width: 56px; height: 56px; border-radius: 50%; background: rgba(255,255,255,.18); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 10px; }
.pc-estado .ico i { font-size: 26px; }
.pc-estado h2 { margin: 0 0 4px; font-size: 18px; font-weight: 800; }
.pc-estado p  { margin: 0; font-size: 12px; opacity: .92; line-height: 1.5; }

/* ─ Tabla cotización ─ */
.pc-table { width: 100%; font-size: 13px; }
.pc-table th { font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 700; padding: 8px 10px; border-bottom: 1px solid #e2e8f0; }
.pc-table td { padding: 9px 10px; border-bottom: 1px solid #f1f5f9; vertical-align: top; }
.pc-table tr:last-child td { border-bottom: none; }
.pc-total { display: flex; justify-content: space-between; align-items: center; margin-top: 14px; padding: 12px 16px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; }
.pc-total .lbl { font-size: 12px; text-transform: uppercase; letter-spacing: .05em; color: #15803d; font-weight: 700; }
.pc-total .amt { font-size: 20px; font-weight: 800; color: #16a34a; }

.pc-info-row { display: flex; align-items: flex-start; gap: 10px; padding: 9px 0; border-bottom: 1px solid #f1f5f9; }
.pc-info-row:last-child { border-bottom: none; }
.pc-info-row i { color: #94a3b8; width: 18px; text-align: center; margin-top: 2px; }
.pc-info-row .k { font-size: 12px; color: #64748b; min-width: 110px; }
.pc-info-row .v { font-size: 13px; color: #0f172a; font-weight: 600; word-break: break-word; }

.pc-rep { border-left: 3px solid #0ea5e9; padding: 0 0 14px 16px; margin-left: 6px; position: relative; }
.pc-rep:last-child { padding-bottom: 0; }
.pc-rep:before { content: ""; position: absolute; left: -8px; top: 2px; width: 13px; height: 13px; border-radius: 50%; background: #0ea5e9; border: 2px solid #fff; }
.pc-rep .fch { font-size: 11px; color: #94a3b8; margin-bottom: 4px; }
.pc-rep .txt { font-size: 13px; color: #1e293b; line-height: 1.5; white-space: pre-line; }
.pc-rep-fotos { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 8px; }
.pc-rep-fotos img { width: 70px; height: 70px; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0; cursor: pointer; }

.pc-back { display: inline-flex; align-items: center; gap: 6px; color: #0f172a; font-weight: 700; font-size: 13px; text-decoration: none; margin-bottom: 10px; }
.pc-back:hover { color: #1e293b; }

.pc-coment { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 10px 12px; margin-bottom: 8px; }
.pc-coment .fch { font-size: 11px; color: #94a3b8; margin-bottom: 3px; }
.pc-coment .txt { font-size: 13px; color: #1e293b; line-height: 1.5; white-space: pre-line; }
.pc-form textarea { width: 100%; border: 1px solid #cbd5e1; border-radius: 10px; padding: 10px 12px; font-size: 13px; resize: vertical; min-height: 70px; box-sizing: border-box; }
.pc-form select, .pc-form input[type=text] { width: 100%; border: 1px solid #cbd5e1; border-radius: 10px; padding: 10px 12px; font-size: 13px; box-sizing: border-box; }
.pc-btn { display: inline-flex; align-items: center; gap: 8px; background: #0f172a; color: #fff; padding: 10px 20px; border-radius: 10px; font-weight: 700; font-size: 13px; border: none; cursor: pointer; margin-top: 8px; transition: background .2s; text-decoration: none; }
.pc-btn:hover { background: #1e293b; color: #fff; text-decoration: none; }
.pc-btn.danger { background: #b91c1c; }
.pc-btn.danger:hover { background: #991b1b; }
.pc-btn.success { background: #15803d; }
.pc-btn.success:hover { background: #166534; }

/* ─ Monedero (reusa estilos del rediseño previo) ─ */
.pc-mc { position: relative; background: linear-gradient(135deg,#1e3a5f 0%,#0d253f 40%,#1a1a2e 100%); border-radius: 14px; padding: 24px 20px 20px; color: #fff; margin: 16px; overflow: hidden; box-shadow: 0 8px 24px rgba(0,0,0,.18); }
.pc-mc::before { content: ''; position: absolute; top: -60%; right: -30%; width: 280px; height: 280px; background: radial-gradient(circle, rgba(99,102,241,.25) 0%, transparent 70%); }
.pc-mc::after  { content: ''; position: absolute; bottom: -40%; left: -20%; width: 240px; height: 240px; background: radial-gradient(circle, rgba(16,185,129,.15) 0%, transparent 70%); }
.pc-mc-top { display: flex; align-items: center; justify-content: space-between; position: relative; z-index: 1; margin-bottom: 18px; }
.pc-mc-top img { width: 42px; height: 42px; border-radius: 10px; object-fit: contain; background: rgba(255,255,255,.12); padding: 4px; }
.pc-mc-brand { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; color: rgba(255,255,255,.55); }
.pc-mc-saldo-lbl { font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 2px; color: rgba(255,255,255,.5); position: relative; z-index: 1; }
.pc-mc-saldo-amt { position: relative; z-index: 1; font-size: 38px; font-weight: 900; letter-spacing: -1px; background: linear-gradient(135deg,#fff 0%,#a5b4fc 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; margin-top: 4px; }
.pc-mc-saldo-amt.zero { background: linear-gradient(135deg,#64748b 0%,#475569 100%); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; }
.pc-mc-saldo-periodo { font-size: 10px; color: rgba(255,255,255,.4); margin-top: 6px; position: relative; z-index: 1; }
.pc-mon-stats { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin: 0 16px 12px; }
.pc-mon-stat { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px 12px; text-align: center; }
.pc-mon-stat .v { font-size: 22px; font-weight: 900; color: #0f172a; }
.pc-mon-stat .l { font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: .8px; color: #64748b; margin-top: 4px; }
.pc-mon-info { margin: 0 16px 14px; padding: 10px 14px; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 10px; font-size: 11px; color: #1e40af; line-height: 1.5; }
.pc-mov { padding: 0 16px 16px; }
.pc-mov-item { display: flex; align-items: center; padding: 10px 0; border-bottom: 1px solid #f1f5f9; }
.pc-mov-item:last-child { border-bottom: none; }
.pc-mov-icon { width: 32px; height: 32px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 13px; margin-right: 10px; flex-shrink: 0; }
.pc-mov-icon.acum { background: #f0fdf4; color: #16a34a; }
.pc-mov-icon.canje { background: #eef2ff; color: #6366f1; }
.pc-mov-icon.exp { background: #fef2f2; color: #ef4444; }
.pc-mov-info { flex: 1; min-width: 0; }
.pc-mov-desc { font-size: 12px; font-weight: 600; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.pc-mov-fecha { font-size: 10px; color: #94a3b8; margin-top: 2px; }
.pc-mov-monto { font-size: 13px; font-weight: 800; white-space: nowrap; margin-left: 10px; }
.pc-mov-monto.pos { color: #16a34a; }
.pc-mov-monto.neg { color: #ef4444; }

/* ─ Ayuda ─ */
.pc-help-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 10px; margin-bottom: 14px; }
.pc-help-btn { display: flex; flex-direction: column; align-items: center; gap: 6px; padding: 14px 8px; background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; text-decoration: none; transition: all .15s; }
.pc-help-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,.08); text-decoration: none; }
.pc-help-btn i { font-size: 22px; }
.pc-help-btn span { font-size: 11px; font-weight: 700; color: #0f172a; }
.pc-help-btn.wa i { color: #25D366; }
.pc-help-btn.call i { color: #0ea5e9; }
.pc-help-btn.mail i { color: #f59e0b; }
.pc-help-info { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 14px; font-size: 12px; color: #475569; line-height: 1.6; margin-bottom: 14px; }
.pc-help-info b { color: #0f172a; }

/* ─ Privacidad ─ */
.pc-priv { font-size: 12px; color: #334155; line-height: 1.6; max-height: 320px; overflow-y: auto; padding: 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; }
.pc-priv b { color: #0f172a; }
.pc-priv-status { display: flex; align-items: center; gap: 10px; padding: 12px 14px; border-radius: 10px; margin-bottom: 14px; font-size: 13px; font-weight: 600; }
.pc-priv-status.ok { background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; }
.pc-priv-status.no { background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; }
.pc-priv-status.pen { background: #fffbeb; border: 1px solid #fde68a; color: #92400e; }
.pc-priv-actions { display: flex; gap: 10px; margin-top: 14px; }

/* Banner recordatorio persistente */
.pc-priv-banner { display: flex; align-items: center; gap: 12px; padding: 12px 14px; margin-bottom: 14px; background: linear-gradient(135deg,#fef3c7 0%,#fde68a 100%); border: 1px solid #f59e0b; border-radius: 12px; box-shadow: 0 4px 14px rgba(245,158,11,.18); }
.pc-priv-banner i { font-size: 20px; color: #b45309; flex-shrink: 0; }
.pc-priv-banner .txt { flex: 1; font-size: 12px; color: #78350f; line-height: 1.4; }
.pc-priv-banner .txt b { color: #422006; }
.pc-priv-banner a { background: #b45309; color: #fff; padding: 8px 14px; border-radius: 8px; font-size: 12px; font-weight: 700; text-decoration: none; white-space: nowrap; }
.pc-priv-banner a:hover { background: #92400e; color: #fff; text-decoration: none; }
.pc-tab .pc-tab-dot { display: inline-block; width: 8px; height: 8px; background: #ef4444; border-radius: 50%; vertical-align: top; margin-left: -2px; box-shadow: 0 0 0 2px #fff; animation: pcPulse 1.6s ease-in-out infinite; }
@keyframes pcPulse { 0%,100% { transform: scale(1); opacity: 1; } 50% { transform: scale(1.3); opacity: .7; } }

/* Canvas de firma */
.pc-firma-box { border: 2px dashed #cbd5e1; border-radius: 10px; background: #fff; padding: 8px; margin-top: 14px; }
.pc-firma-lbl { font-size: 12px; font-weight: 700; color: #0f172a; margin-bottom: 6px; display: flex; align-items: center; justify-content: space-between; }
.pc-firma-lbl small { font-size: 10px; color: #94a3b8; font-weight: 500; }
.pc-firma-canvas { display: block; width: 100%; height: 160px; background: #f8fafc; border-radius: 6px; cursor: crosshair; touch-action: none; }
.pc-firma-actions { display: flex; gap: 8px; margin-top: 8px; }
.pc-firma-clear { background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1; padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer; }
.pc-firma-clear:hover { background: #e2e8f0; }
.pc-firma-img { display: block; max-width: 100%; height: auto; border: 1px solid #e2e8f0; border-radius: 6px; background: #fff; padding: 8px; margin-top: 10px; }

/* ─ Lightbox ─ */
#pcLightbox { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.85); z-index: 99999; align-items: center; justify-content: center; }
#pcLightbox img { max-width: 92vw; max-height: 88vh; border-radius: 8px; }
#pcLightbox .x { position: absolute; top: 16px; right: 20px; color: #fff; font-size: 32px; cursor: pointer; }

/* ─ Google CTA (mantenida) ─ */
.pc-google-box { background: linear-gradient(135deg,#1a73e8 0%,#4285F4 55%,#669df6 100%); padding: 28px 20px; text-align: center; border-radius: 14px; margin-top: 14px; }
.pc-google-logo { display: inline-block; background: #fff; padding: 8px 16px; border-radius: 30px; font-size: 18px; font-weight: 800; box-shadow: 0 4px 12px rgba(0,0,0,.18); margin-bottom: 14px; }
.pc-google-rate { font-size: 24px; letter-spacing: 4px; margin-bottom: 12px; }
.pc-google-h { font-size: 16px; font-weight: 800; color: #fff; margin-bottom: 6px; }
.pc-google-p { font-size: 12px; color: rgba(255,255,255,.85); line-height: 1.5; max-width: 320px; margin: 0 auto 16px; }
.pc-google-cta { display: inline-block; background: #fff; color: #1a73e8; padding: 12px 28px; border-radius: 26px; font-weight: 800; font-size: 13px; text-decoration: none; box-shadow: 0 4px 14px rgba(0,0,0,.2); }
.pc-google-cta:hover { color: #1558c0; text-decoration: none; }

@media (max-width: 576px) {
    .pc-tab span { display: none; }
    .pc-tab i { margin-bottom: 0; font-size: 16px; }
    .pc-info-row { flex-wrap: wrap; }
    .pc-info-row .k { min-width: 100%; }
    .pc-help-grid { grid-template-columns: 1fr; }
    .pc-mc { margin: 12px; padding: 20px 16px 16px; }
    .pc-mc-saldo-amt { font-size: 30px; }
    .pc-mon-stats { margin: 0 12px 10px; }
    .pc-mon-info { margin: 0 12px 12px; }
    .pc-mov { padding: 0 12px 12px; }
}
</style>

<div class="content-wrapper pc-wrap">
  <div class="pc-shell">

    <?php if (!$pcOk): ?>

      <!-- Token inválido -->
      <div class="pc-card">
        <div style="background:linear-gradient(135deg,#475569 0%,#1e293b 100%);text-align:center;color:#fff;padding:30px 18px">
          <img src="vistas/img/plantilla/Captura3.PNG" alt="Logo" style="max-height:50px;margin-bottom:12px">
          <div style="font-size:18px;font-weight:800;margin-bottom:6px">Enlace no v&aacute;lido</div>
          <div style="font-size:13px;opacity:.85">No pudimos cargar tu portal. Escanea el QR de tu ticket o contacta a EGS.</div>
        </div>
      </div>

    <?php else: ?>

      <!-- Header -->
      <div class="pc-topbar">
        <img src="vistas/img/plantilla/Captura3.PNG" alt="EGS">
        <div class="who">
          <div class="hi">Bienvenido</div>
          <div class="nm"><?php echo htmlspecialchars($pcNombre); ?></div>
        </div>
      </div>

      <!-- Tabs -->
      <div class="pc-tabs">
        <a class="pc-tab <?php echo $pcTab==='equipos'?'active':''; ?>" href="<?php echo pc_url($pcToken,'equipos'); ?>"><i class="fa-solid fa-laptop"></i><span>Equipos</span></a>
        <a class="pc-tab <?php echo $pcTab==='monedero'?'active':''; ?>" href="<?php echo pc_url($pcToken,'monedero'); ?>"><i class="fa-solid fa-wallet"></i><span>Monedero</span></a>
        <a class="pc-tab <?php echo $pcTab==='historial'?'active':''; ?>" href="<?php echo pc_url($pcToken,'historial'); ?>"><i class="fa-solid fa-clock-rotate-left"></i><span>Historial</span></a>
        <a class="pc-tab <?php echo $pcTab==='ayuda'?'active':''; ?>" href="<?php echo pc_url($pcToken,'ayuda'); ?>"><i class="fa-solid fa-headset"></i><span>Ayuda</span></a>
        <a class="pc-tab <?php echo $pcTab==='privacidad'?'active':''; ?>" href="<?php echo pc_url($pcToken,'privacidad'); ?>"><i class="fa-solid fa-shield-halved"></i><span>Privacidad</span><?php if ($pcPrivacidadPendiente): ?><span class="pc-tab-dot" title="Pendiente de revisar"></span><?php endif; ?></a>
      </div>

      <?php /* Recordatorio persistente — solo si está pendiente y no estamos ya en el tab */ ?>
      <?php if ($pcPrivacidadPendiente && $pcTab !== 'privacidad'): ?>
        <div class="pc-priv-banner">
          <i class="fa-solid fa-shield-halved"></i>
          <div class="txt">
            <b>Aviso de privacidad pendiente.</b> Por ley necesitamos tu autorizaci&oacute;n y firma para el manejo de tus datos personales.
          </div>
          <a href="<?php echo pc_url($pcToken,'privacidad'); ?>">Revisar ahora</a>
        </div>
      <?php endif; ?>

      <?php /* ═══ TAB: EQUIPOS ═══ */ ?>
      <?php if ($pcTab === 'equipos'): ?>

        <?php if ($pcOrdenDetalle): /* Detalle expandido de una orden */
          list($eC,$eI,$eL,$eD) = pc_estadoInfo($pcOrdenDetalle["estado"]);
          $verPrecios = pc_preciosVisibles($pcOrdenDetalle["estado"]);
          $totalOrd = isset($pcOrdenDetalle["total"]) ? floatval($pcOrdenDetalle["total"]) : 0;
          // Líneas de cotización
          $lineas = array();
          $nombresFijos = array("partidaUno","partidaDos","partidaTres","partidaCuatro","partidaCinco","partidaSeis","partidaSiete","partidaOcho","partidaNueve","partidaDiez");
          $preciosFijos = array("precioUno","precioDos","precioTres","precioCuatro","precioCinco","precioSeis","precioSiete","precioOcho","precioNueve","precioDiez");
          for ($i=0;$i<10;$i++){
              $d = isset($pcOrdenDetalle[$nombresFijos[$i]]) ? trim((string)$pcOrdenDetalle[$nombresFijos[$i]]) : "";
              $p = isset($pcOrdenDetalle[$preciosFijos[$i]]) ? floatval($pcOrdenDetalle[$preciosFijos[$i]]) : 0;
              if ($d!=="" || $p>0) $lineas[] = array("descripcion"=>$d!==""?$d:"Partida","precio"=>$p);
          }
          $partidasJson = isset($pcOrdenDetalle["partidas"]) ? json_decode($pcOrdenDetalle["partidas"], true) : null;
          if (is_array($partidasJson)) {
              foreach ($partidasJson as $pp) {
                  $d = isset($pp["descripcion"]) ? trim((string)$pp["descripcion"]) : "";
                  $p = isset($pp["precioPartida"]) ? floatval($pp["precioPartida"]) : 0;
                  if ($d!=="" || $p>0) $lineas[] = array("descripcion"=>$d!==""?$d:"Partida adicional","precio"=>$p);
              }
          }
          // Reportes y comentarios de esta orden
          $idOrdDet = intval($pcOrdenDetalle["id"]);
          $reportes = controladorReporteEquipo::ctrMostrarReportes($idOrdDet);
          $fotosRep = controladorReporteEquipo::ctrMostrarFotosPorOrden($idOrdDet);
          $fotosPorRep = array();
          if (is_array($fotosRep)) foreach ($fotosRep as $f) $fotosPorRep[intval($f["id_reporte"])][] = $f["ruta"];
          $comentsOrd = controladorComentarioCliente::ctrMostrar($idOrdDet);
          $entregado = (stripos((string)$pcOrdenDetalle["estado"], "entregad") !== false);
        ?>

          <a href="<?php echo pc_url($pcToken,'equipos'); ?>" class="pc-back"><i class="fa-solid fa-arrow-left"></i> Volver a mis equipos</a>

          <div class="pc-card">
            <div class="pc-estado" style="background:linear-gradient(135deg,<?php echo $eC; ?> 0%, <?php echo $eC; ?>cc 100%)">
              <div class="ico"><i class="fa-solid <?php echo $eI; ?>"></i></div>
              <h2><?php echo htmlspecialchars($eL); ?></h2>
              <p><?php echo htmlspecialchars($eD); ?></p>
            </div>
            <div class="pc-card-body" style="text-align:center;padding:12px">
              <span style="font-size:11px;color:#64748b">Orden</span>
              <strong style="font-size:16px;color:#0f172a;display:block">#<?php echo $idOrdDet; ?></strong>
            </div>
          </div>

          <?php if ($verPrecios): ?>
          <div class="pc-card">
            <div class="pc-card-head"><i class="fa-solid fa-file-invoice-dollar"></i><h3>Cotización</h3></div>
            <div class="pc-card-body">
              <?php if (!empty($lineas)): ?>
                <table class="pc-table">
                  <thead><tr><th>Concepto</th><th style="text-align:right">Precio</th></tr></thead>
                  <tbody>
                  <?php foreach ($lineas as $ln): ?>
                    <tr>
                      <td><?php echo nl2br(htmlspecialchars($ln["descripcion"])); ?></td>
                      <td style="text-align:right;font-weight:700;white-space:nowrap">$<?php echo number_format($ln["precio"],2); ?></td>
                    </tr>
                  <?php endforeach; ?>
                  </tbody>
                </table>
              <?php else: ?>
                <div class="pc-empty">Aún no hay conceptos cotizados.</div>
              <?php endif; ?>
              <div class="pc-total"><span class="lbl">Total</span><span class="amt">$<?php echo number_format($totalOrd,2); ?></span></div>
            </div>
          </div>
          <?php endif; ?>

          <div class="pc-card">
            <div class="pc-card-head"><i class="fa-solid fa-clipboard-list"></i><h3>Reportes del equipo</h3></div>
            <div class="pc-card-body">
              <?php if (is_array($reportes) && !empty($reportes)): ?>
                <?php foreach ($reportes as $rep):
                  $rid = intval($rep["id"]);
                  $rf = isset($fotosPorRep[$rid]) ? $fotosPorRep[$rid] : array(); ?>
                  <div class="pc-rep">
                    <div class="fch"><i class="fa-regular fa-clock"></i> <?php echo pc_fecha($rep["fecha"]); ?></div>
                    <div class="txt"><?php echo htmlspecialchars($rep["descripcion"]); ?></div>
                    <?php if (!empty($rf)): ?>
                      <div class="pc-rep-fotos">
                        <?php foreach ($rf as $u): ?>
                          <img src="<?php echo htmlspecialchars($u); ?>" loading="lazy" onclick="pcAbrirFoto(this.src)" alt="">
                        <?php endforeach; ?>
                      </div>
                    <?php endif; ?>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <div class="pc-empty">Aún no hay reportes para este equipo.</div>
              <?php endif; ?>
            </div>
          </div>

          <div class="pc-card">
            <div class="pc-card-head"><i class="fa-solid fa-receipt"></i><h3>Resumen</h3></div>
            <div class="pc-card-body">
              <?php if (!empty($pcOrdenDetalle["titulo"])): ?>
                <div class="pc-info-row"><i class="fa-solid fa-tag"></i><span class="k">Título</span><span class="v"><?php echo htmlspecialchars($pcOrdenDetalle["titulo"]); ?></span></div>
              <?php endif; ?>
              <?php if (!empty($pcOrdenDetalle["marcaDelEquipo"]) || !empty($pcOrdenDetalle["modeloDelEquipo"])): ?>
                <div class="pc-info-row"><i class="fa-solid fa-laptop"></i><span class="k">Equipo</span><span class="v"><?php echo htmlspecialchars(trim(($pcOrdenDetalle["marcaDelEquipo"] ?? "")." ".($pcOrdenDetalle["modeloDelEquipo"] ?? ""))); ?></span></div>
              <?php endif; ?>
              <?php if (!empty($pcOrdenDetalle["numeroDeSerieDelEquipo"])): ?>
                <div class="pc-info-row"><i class="fa-solid fa-barcode"></i><span class="k">No. de serie</span><span class="v"><?php echo htmlspecialchars($pcOrdenDetalle["numeroDeSerieDelEquipo"]); ?></span></div>
              <?php endif; ?>
              <?php if (!empty($pcOrdenDetalle["fecha_ingreso"]) && $pcOrdenDetalle["fecha_ingreso"] !== "0000-00-00" && $pcOrdenDetalle["fecha_ingreso"] !== "0000-00-00 00:00:00"): ?>
                <div class="pc-info-row"><i class="fa-solid fa-calendar-plus"></i><span class="k">Ingreso</span><span class="v"><?php echo pc_fecha($pcOrdenDetalle["fecha_ingreso"], false); ?></span></div>
              <?php endif; ?>
              <?php if ($verPrecios): ?>
                <div class="pc-info-row"><i class="fa-solid fa-dollar-sign"></i><span class="k">Total</span><span class="v">$<?php echo number_format($totalOrd,2); ?></span></div>
              <?php endif; ?>
            </div>
          </div>

          <div class="pc-card">
            <div class="pc-card-head"><i class="fa-solid fa-truck"></i><h3>Entrega</h3></div>
            <div class="pc-card-body">
              <div class="pc-info-row">
                <i class="fa-solid fa-circle-<?php echo $entregado?'check':'half-stroke'; ?>"></i>
                <span class="k">Estado</span>
                <span class="v" style="color:<?php echo $entregado?'#16a34a':'#f59e0b'; ?>"><?php echo $entregado?'Entregado':'Pendiente de entrega'; ?></span>
              </div>
              <?php $fSal = $pcOrdenDetalle["fecha_Salida"] ?? ""; if (!empty($fSal) && $fSal !== "0000-00-00" && $fSal !== "0000-00-00 00:00:00"): ?>
                <div class="pc-info-row"><i class="fa-solid fa-calendar-check"></i><span class="k">Fecha entrega</span><span class="v"><?php echo pc_fecha($fSal, false); ?></span></div>
              <?php endif; ?>
            </div>
          </div>

          <div class="pc-card">
            <div class="pc-card-head"><i class="fa-solid fa-comments"></i><h3>Comentarios</h3></div>
            <div class="pc-card-body">
              <?php if ($pcMsgComentario === "ok"): ?>
                <div class="pc-alert ok"><i class="fa-solid fa-check"></i> ¡Gracias! Tu comentario fue enviado.</div>
              <?php elseif ($pcMsgComentario === "vacio"): ?>
                <div class="pc-alert warn">Escribe un comentario antes de enviar.</div>
              <?php elseif ($pcMsgComentario === "duplicate"): ?>
                <div class="pc-alert warn">Ese comentario ya fue registrado hace unos segundos.</div>
              <?php elseif ($pcMsgComentario === "error"): ?>
                <div class="pc-alert err">No pudimos guardar tu comentario.</div>
              <?php endif; ?>

              <?php if (is_array($comentsOrd) && !empty($comentsOrd)): ?>
                <?php foreach ($comentsOrd as $c): ?>
                  <div class="pc-coment">
                    <div class="fch"><i class="fa-regular fa-clock"></i> <?php echo pc_fecha($c["fecha"]); ?></div>
                    <div class="txt"><?php echo htmlspecialchars($c["comentario"]); ?></div>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <div class="pc-empty">Aún no has dejado comentarios para esta orden.</div>
              <?php endif; ?>

              <form method="post" class="pc-form" style="margin-top:10px" onsubmit="return pcValidarComentario(this);">
                <input type="hidden" name="idOrdenComentario" value="<?php echo $idOrdDet; ?>">
                <textarea name="comentarioCliente" maxlength="1000" placeholder="Escribe una duda o comentario sobre esta orden..."></textarea>
                <button type="submit" class="pc-btn"><i class="fa-solid fa-paper-plane"></i> Enviar</button>
              </form>
            </div>
          </div>

          <!-- CTA Google -->
          <div class="pc-google-box">
            <div class="pc-google-logo"><span style="color:#4285F4">G</span><span style="color:#EA4335">o</span><span style="color:#FBBC05">o</span><span style="color:#4285F4">g</span><span style="color:#34A853">l</span><span style="color:#EA4335">e</span></div>
            <div class="pc-google-rate"><span style="color:#EA4335">&#9733;</span><span style="color:#FBBC05">&#9733;</span><span style="color:#34A853">&#9733;</span><span style="color:#fff;opacity:.7">&#9733;</span><span style="color:#fff;opacity:.7">&#9733;</span></div>
            <div class="pc-google-h">&iquest;C&oacute;mo fue tu experiencia?</div>
            <div class="pc-google-p">Tu opini&oacute;n nos ayuda a seguir mejorando.</div>
            <a class="pc-google-cta" href="https://search.google.com/local/writereview?placeid=ChIJQ2fMEuyJzYUR53xcbvJcaAg" target="_blank" rel="noopener">&#9733; Dejar rese&ntilde;a en Google</a>
          </div>

        <?php else: /* Grid de equipos activos */
          $activas = array();
          foreach ($pcOrdenes as $o) {
              $eo = strtolower((string)$o["estado"]);
              if (strpos($eo,"entregad") === false && strpos($eo,"cancel") === false) $activas[] = $o;
          }
        ?>
          <div class="pc-card">
            <div class="pc-card-head"><i class="fa-solid fa-laptop"></i><h3>Tus equipos en servicio</h3></div>
            <div class="pc-card-body">
              <?php if (empty($activas)): ?>
                <div class="pc-empty">No tienes equipos activos en este momento.</div>
              <?php else: ?>
                <div class="pc-grid">
                  <?php foreach ($activas as $o):
                    list($eC,$eI,$eL,$eD) = pc_estadoInfo($o["estado"]);
                    $verP = pc_preciosVisibles($o["estado"]);
                  ?>
                    <a href="<?php echo pc_url($pcToken,'equipos',intval($o["id"])); ?>" class="pc-eq">
                      <div class="pc-eq-head">
                        <span class="pc-eq-badge" style="background:<?php echo $eC; ?>"><i class="fa-solid <?php echo $eI; ?>"></i><?php echo htmlspecialchars($eL); ?></span>
                        <span class="pc-eq-num">#<?php echo intval($o["id"]); ?></span>
                      </div>
                      <div class="pc-eq-name"><?php echo htmlspecialchars(trim(($o["marcaDelEquipo"] ?? "")." ".($o["modeloDelEquipo"] ?? "")) ?: ($o["titulo"] ?? "Equipo")); ?></div>
                      <div class="pc-eq-meta">
                        <span><?php echo pc_fecha($o["fecha_ingreso"] ?? $o["fecha"] ?? "", false); ?></span>
                        <?php if ($verP): ?><span class="pc-eq-total">$<?php echo number_format(floatval($o["total"] ?? 0),2); ?></span><?php endif; ?>
                      </div>
                    </a>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </div>
          </div>
        <?php endif; ?>

      <?php endif; ?>

      <?php /* ═══ TAB: MONEDERO ═══ */ ?>
      <?php if ($pcTab === 'monedero'):
        $saldo  = isset($pcMonederoData["saldo"]) ? floatval($pcMonederoData["saldo"]) : 0;
        $entreg = isset($pcMonederoData["entregadas"]) ? intval($pcMonederoData["entregadas"]) : 0;
        $pct    = isset($pcMonederoData["porcentaje"]) ? intval($pcMonederoData["porcentaje"]) : 1;
        $movs   = isset($pcMonederoData["movimientos"]) ? $pcMonederoData["movimientos"] : array();
      ?>
        <div class="pc-card">
          <div class="pc-card-head"><i class="fa-solid fa-wallet"></i><h3>Tu Monedero EGS</h3></div>
          <div class="pc-card-body" style="padding:0">
            <div class="pc-mc">
              <div class="pc-mc-top"><img src="vistas/img/plantilla/Captura3.PNG" alt=""><span class="pc-mc-brand">Monedero EGS</span></div>
              <div class="pc-mc-saldo-lbl">Saldo disponible</div>
              <div class="pc-mc-saldo-amt <?php echo $saldo<=0?'zero':''; ?>">$<?php echo number_format($saldo,2); ?></div>
              <div class="pc-mc-saldo-periodo">Últimos 6 meses</div>
            </div>
            <div class="pc-mon-stats">
              <div class="pc-mon-stat"><div class="v"><?php echo $entreg; ?></div><div class="l">Órdenes entregadas</div></div>
              <div class="pc-mon-stat"><div class="v"><?php echo $pct; ?>%</div><div class="l">Recompensa vigente</div></div>
            </div>
            <div class="pc-mon-info">
              <i class="fa-solid fa-circle-info"></i>
              Tu saldo corresponde a recompensas acumuladas en los <strong>últimos 6 meses</strong>. El dinero electrónico vence 6 meses después de generarse.
            </div>
            <div class="pc-mov">
              <div style="font-size:11px;text-transform:uppercase;letter-spacing:1px;color:#64748b;font-weight:700;margin-bottom:10px">Últimos movimientos</div>
              <?php if (empty($movs)): ?>
                <div class="pc-empty">Cuando se entregue tu primera orden, aquí verás tu dinero acumulado.</div>
              <?php else: foreach ($movs as $m): ?>
                <div class="pc-mov-item">
                  <div class="pc-mov-icon <?php echo $m["tipo"]=='acumulacion'?'acum':($m["tipo"]=='canje'?'canje':'exp'); ?>">
                    <i class="fa-solid <?php echo $m["tipo"]=='acumulacion'?'fa-arrow-up':($m["tipo"]=='canje'?'fa-arrow-down':'fa-rotate'); ?>"></i>
                  </div>
                  <div class="pc-mov-info">
                    <div class="pc-mov-desc"><?php echo htmlspecialchars($m["descripcion"]); ?></div>
                    <div class="pc-mov-fecha"><?php echo date('d/m/Y H:i', strtotime($m["fecha"])); ?></div>
                  </div>
                  <div class="pc-mov-monto <?php echo floatval($m["monto"])>=0?'pos':'neg'; ?>"><?php echo floatval($m["monto"])>=0?'+':''; ?>$<?php echo number_format(abs(floatval($m["monto"])),2); ?></div>
                </div>
              <?php endforeach; endif; ?>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <?php /* ═══ TAB: HISTORIAL ═══ */ ?>
      <?php if ($pcTab === 'historial'): ?>
        <div class="pc-card">
          <div class="pc-card-head"><i class="fa-solid fa-clock-rotate-left"></i><h3>Historial de órdenes</h3></div>
          <div class="pc-card-body">
            <?php if (empty($pcOrdenes)): ?>
              <div class="pc-empty">Sin órdenes registradas.</div>
            <?php else: ?>
              <div class="pc-grid">
                <?php foreach ($pcOrdenes as $o):
                  list($eC,$eI,$eL,$eD) = pc_estadoInfo($o["estado"]);
                  $verP = pc_preciosVisibles($o["estado"]);
                ?>
                  <a href="<?php echo pc_url($pcToken,'equipos',intval($o["id"])); ?>" class="pc-eq">
                    <div class="pc-eq-head">
                      <span class="pc-eq-badge" style="background:<?php echo $eC; ?>"><i class="fa-solid <?php echo $eI; ?>"></i><?php echo htmlspecialchars($eL); ?></span>
                      <span class="pc-eq-num">#<?php echo intval($o["id"]); ?></span>
                    </div>
                    <div class="pc-eq-name"><?php echo htmlspecialchars(trim(($o["marcaDelEquipo"] ?? "")." ".($o["modeloDelEquipo"] ?? "")) ?: ($o["titulo"] ?? "Equipo")); ?></div>
                    <div class="pc-eq-meta">
                      <span><?php echo pc_fecha($o["fecha_ingreso"] ?? $o["fecha"] ?? "", false); ?></span>
                      <?php if ($verP): ?><span class="pc-eq-total">$<?php echo number_format(floatval($o["total"] ?? 0),2); ?></span><?php endif; ?>
                    </div>
                  </a>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      <?php endif; ?>

      <?php /* ═══ TAB: AYUDA ═══ */ ?>
      <?php if ($pcTab === 'ayuda'):
        $tel  = $pcEmpresa["telefono"]    ?? "";
        $tel2 = $pcEmpresa["telefonoDos"] ?? "";  // WhatsApp
        $email= $pcEmpresa["correo"]      ?? "";
        $dir  = $pcEmpresa["direccion"]   ?? "";
        $hor  = $pcEmpresa["Horario"]     ?? "L-V 10:00-14:00 y 16:00-18:30 / Sáb 9:00-14:30";
        $sitio= $pcEmpresa["Sitio"]       ?? "comercializadoraegs.com";

        $telLimpio  = preg_replace('/\D/','',(string)$tel);
        $tel2Limpio = preg_replace('/\D/','',(string)$tel2);
        $waNum      = $tel2Limpio ?: $telLimpio;
        $waPrefix   = '';
        if ($waNum !== '' && strlen($waNum) <= 10) $waPrefix = '52'; // MX
        $waUrl      = $waNum !== '' ? "https://wa.me/{$waPrefix}{$waNum}?text=".urlencode("Hola, soy ".$pcNombre.". Necesito ayuda con mi orden de servicio.") : "#";
      ?>
        <div class="pc-card">
          <div class="pc-card-head"><i class="fa-solid fa-headset"></i><h3>Centro de ayuda</h3></div>
          <div class="pc-card-body">
            <div class="pc-help-grid">
              <a class="pc-help-btn wa" href="<?php echo htmlspecialchars($waUrl); ?>" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp"></i><span>WhatsApp</span></a>
              <a class="pc-help-btn call" href="<?php echo $telLimpio?'tel:'.$telLimpio:'#'; ?>"><i class="fa-solid fa-phone"></i><span>Llamar</span></a>
              <a class="pc-help-btn mail" href="<?php echo $email?'mailto:'.htmlspecialchars($email):'#'; ?>"><i class="fa-solid fa-envelope"></i><span>Email</span></a>
            </div>

            <div class="pc-help-info">
              <div><i class="fa-solid fa-clock" style="color:#94a3b8;margin-right:6px"></i><b>Horario:</b> <?php echo htmlspecialchars($hor); ?></div>
              <?php if ($dir): ?><div style="margin-top:6px"><i class="fa-solid fa-location-dot" style="color:#94a3b8;margin-right:6px"></i><b>Dirección:</b> <?php echo htmlspecialchars($dir); ?></div><?php endif; ?>
              <?php if ($sitio): ?><div style="margin-top:6px"><i class="fa-solid fa-globe" style="color:#94a3b8;margin-right:6px"></i><a href="https://<?php echo htmlspecialchars(preg_replace('#^https?://#','',$sitio)); ?>" target="_blank" rel="noopener"><?php echo htmlspecialchars($sitio); ?></a></div><?php endif; ?>
            </div>

            <?php if ($pcMsgAyuda === "ok"): ?>
              <div class="pc-alert ok"><i class="fa-solid fa-check"></i> Recibimos tu solicitud. Te contactaremos pronto.</div>
            <?php elseif ($pcMsgAyuda === "vacio"): ?>
              <div class="pc-alert warn">Escribe un mensaje antes de enviar.</div>
            <?php elseif ($pcMsgAyuda === "duplicate"): ?>
              <div class="pc-alert warn">Ya enviaste esa solicitud hace unos segundos.</div>
            <?php elseif ($pcMsgAyuda === "error"): ?>
              <div class="pc-alert err">No pudimos guardar tu solicitud, inténtalo de nuevo.</div>
            <?php endif; ?>

            <div style="font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.8px;margin:14px 0 8px">Solicitar ayuda</div>
            <form method="post" class="pc-form" onsubmit="return pcValidarAyuda(this);">
              <?php if (!empty($pcOrdenes)): ?>
                <select name="idOrdenAyuda" style="margin-bottom:10px">
                  <option value="">Sin orden específica</option>
                  <?php foreach ($pcOrdenes as $o): ?>
                    <option value="<?php echo intval($o["id"]); ?>" <?php echo $pcOrdenSel===intval($o["id"])?'selected':''; ?>>
                      Orden #<?php echo intval($o["id"]); ?> — <?php echo htmlspecialchars(trim(($o["marcaDelEquipo"] ?? "")." ".($o["modeloDelEquipo"] ?? "")) ?: "Equipo"); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              <?php endif; ?>
              <textarea name="mensajeAyuda" maxlength="2000" placeholder="Cuéntanos en qué podemos ayudarte..."></textarea>
              <button type="submit" class="pc-btn"><i class="fa-solid fa-paper-plane"></i> Enviar solicitud</button>
            </form>

            <?php if (!empty($pcSolicitudes)): ?>
              <div style="font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.8px;margin:18px 0 8px">Tus solicitudes anteriores</div>
              <?php foreach ($pcSolicitudes as $s):
                $estTxt = ucfirst(str_replace('_',' ',$s["estado"]));
                $estCol = $s["estado"]==='resuelta'?'#16a34a':($s["estado"]==='en_proceso'?'#f59e0b':'#64748b');
              ?>
                <div class="pc-coment">
                  <div class="fch">
                    <i class="fa-regular fa-clock"></i> <?php echo pc_fecha($s["fecha"]); ?>
                    <span style="float:right;color:<?php echo $estCol; ?>;font-weight:700"><?php echo htmlspecialchars($estTxt); ?></span>
                  </div>
                  <div class="txt"><?php echo htmlspecialchars($s["mensaje"]); ?></div>
                  <?php if (!empty($s["notas_admin"])): ?>
                    <div style="font-size:12px;color:#1e40af;margin-top:6px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:8px 10px"><b>Respuesta:</b> <?php echo htmlspecialchars($s["notas_admin"]); ?></div>
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
      <?php endif; ?>

      <?php /* ═══ TAB: PRIVACIDAD ═══ */ ?>
      <?php if ($pcTab === 'privacidad'): ?>
        <div class="pc-card">
          <div class="pc-card-head"><i class="fa-solid fa-shield-halved"></i><h3>Aviso de Privacidad</h3></div>
          <div class="pc-card-body">

            <?php if ($pcMsgPrivacidad === "ok"): ?>
              <div class="pc-alert ok"><i class="fa-solid fa-check"></i> Tu decisión fue registrada.</div>
            <?php elseif ($pcMsgPrivacidad === "invalida"): ?>
              <div class="pc-alert warn">Opción no válida.</div>
            <?php elseif ($pcMsgPrivacidad === "sin_firma"): ?>
              <div class="pc-alert warn"><i class="fa-solid fa-pen-nib"></i> Para aceptar necesitamos tu firma. Trázala en el recuadro antes de confirmar.</div>
            <?php elseif ($pcMsgPrivacidad === "error"): ?>
              <div class="pc-alert err">No pudimos guardar tu decisión.</div>
            <?php endif; ?>

            <?php if (is_array($pcAceptacion)): ?>
              <?php if (intval($pcAceptacion["aceptado"]) === 1): ?>
                <div class="pc-priv-status ok"><i class="fa-solid fa-circle-check"></i> Aceptaste el aviso el <?php echo pc_fecha($pcAceptacion["fecha"]); ?></div>
              <?php else: ?>
                <div class="pc-priv-status no"><i class="fa-solid fa-circle-xmark"></i> No aceptaste el aviso el <?php echo pc_fecha($pcAceptacion["fecha"]); ?></div>
              <?php endif; ?>
            <?php else: ?>
              <div class="pc-priv-status pen"><i class="fa-solid fa-circle-exclamation"></i> Pendiente de revisar</div>
            <?php endif; ?>

            <div class="pc-priv">
              <div style="text-align:center;font-weight:900;font-size:13px;margin-bottom:10px">AVISO Y POL&Iacute;TICA DE PRIVACIDAD PARA EL MANEJO DE DATOS PERSONALES — COMERCIALIZADORA EGS</div>
              <p><b>Asunto:</b> Confidencialidad y Autorizaci&oacute;n de Mensajes Promocionales.</p>
              <p>Estimado/a cliente <b><?php echo htmlspecialchars($pcNombre); ?></b>:</p>
              <p>En COMERCIALIZADORA EGS valoramos la confianza que depositas en nosotros. Para proteger tu informaci&oacute;n, nos comprometemos a mantener la confidencialidad de los datos que compartas con nosotros.</p>
              <p>Adem&aacute;s, nos gustar&iacute;a mantenerte al tanto de nuestras ofertas y novedades. Si deseas recibir mensajes promocionales por WhatsApp, presiona <b>ACEPTO</b>. Si prefieres no recibirlos, presiona <b>NO ACEPTO</b>.</p>
              <p>Esta autorizaci&oacute;n se fundamenta en las siguientes leyes:</p>
              <p><b>1. Constituci&oacute;n Pol&iacute;tica de los Estados Unidos Mexicanos</b> — Art&iacute;culo 16 (Segundo p&aacute;rrafo): Protege el derecho a la protecci&oacute;n de datos personales, el acceso, rectificaci&oacute;n, cancelaci&oacute;n y oposici&oacute;n (derechos ARCO), as&iacute; como la privacidad de las comunicaciones.</p>
              <p><b>2. Ley Federal de Protecci&oacute;n de Datos Personales en Posesi&oacute;n de los Particulares (LFPDPPP)</b>:</p>
              <p>- <b>Art&iacute;culo 6:</b> Establece que los responsables del tratamiento de datos (la empresa) deben garantizar la confidencialidad.<br>- <b>Art&iacute;culos 14 y 15:</b> Obligan a que el tratamiento de datos se limite a las finalidades acordadas y se proteja contra el uso indebido.<br>- <b>Art&iacute;culo 21:</b> Obliga a los terceros que reciban datos a mantener la confidencialidad.</p>
              <p>Tu privacidad es importante. Puedes revocar este permiso en cualquier momento volviendo a esta pantalla.</p>
              <p style="text-align:center;margin-top:14px"><b>COMERCIALIZADORA EGS</b></p>
            </div>

            <?php /* Mostrar la firma guardada si existe */ ?>
            <?php if (is_array($pcAceptacion) && intval($pcAceptacion["aceptado"]) === 1 && !empty($pcAceptacion["firma"])): ?>
              <div style="margin-top:14px">
                <div class="pc-firma-lbl"><span><i class="fa-solid fa-pen-nib" style="color:#16a34a;margin-right:6px"></i>Firma registrada</span><small><?php echo pc_fecha($pcAceptacion["fecha"]); ?></small></div>
                <img class="pc-firma-img" src="<?php echo htmlspecialchars($pcAceptacion["firma"]); ?>" alt="Firma">
              </div>
            <?php endif; ?>

            <?php if (!is_array($pcAceptacion)): /* Pendiente: mostrar formulario con canvas */ ?>
              <form method="post" id="pcPrivForm" onsubmit="return pcPrivSubmit(this);">
                <input type="hidden" name="aceptaPrivacidad" id="pcPrivAccion" value="">
                <input type="hidden" name="firmaPrivacidad" id="pcPrivFirma" value="">

                <div class="pc-firma-box" id="pcFirmaBox">
                  <div class="pc-firma-lbl">
                    <span><i class="fa-solid fa-pen-nib" style="color:#0ea5e9;margin-right:6px"></i>Firma aqu&iacute; con tu dedo o el mouse</span>
                    <small>requerida para aceptar</small>
                  </div>
                  <canvas id="pcFirmaCanvas" class="pc-firma-canvas"></canvas>
                  <div class="pc-firma-actions">
                    <button type="button" class="pc-firma-clear" onclick="pcFirmaLimpiar()"><i class="fa-solid fa-eraser"></i> Limpiar</button>
                  </div>
                </div>

                <div class="pc-priv-actions">
                  <button type="button" class="pc-btn success" style="flex:1;justify-content:center" onclick="pcPrivConfirmar(1)"><i class="fa-solid fa-check"></i> Acepto y firmo</button>
                  <button type="button" class="pc-btn danger"  style="flex:1;justify-content:center" onclick="pcPrivConfirmar(0)"><i class="fa-solid fa-xmark"></i> No acepto</button>
                </div>
              </form>
            <?php else: /* Ya hay decisión: botón para cambiarla (vuelve a pedir firma si pasa a aceptado) */ ?>
              <div class="pc-priv-actions">
                <form method="post" id="pcPrivChangeForm" style="flex:1;display:flex" onsubmit="return pcPrivChangeSubmit(this);">
                  <input type="hidden" name="aceptaPrivacidad" value="<?php echo intval($pcAceptacion["aceptado"])===1?'0':'1'; ?>">
                  <?php if (intval($pcAceptacion["aceptado"]) === 0): ?>
                    <input type="hidden" name="firmaPrivacidad" id="pcPrivFirmaCambio" value="">
                  <?php endif; ?>
                  <button type="submit" class="pc-btn" style="flex:1;justify-content:center;background:#475569"><i class="fa-solid fa-rotate"></i> Cambiar decisi&oacute;n</button>
                </form>
              </div>

              <?php if (intval($pcAceptacion["aceptado"]) === 0): /* Estaba rechazado: si quiere cambiar a aceptar, pedimos firma */ ?>
                <div class="pc-firma-box" id="pcFirmaBoxCambio" style="display:none">
                  <div class="pc-firma-lbl"><span><i class="fa-solid fa-pen-nib" style="color:#0ea5e9;margin-right:6px"></i>Firma aqu&iacute; para confirmar</span><small>requerida</small></div>
                  <canvas id="pcFirmaCanvasCambio" class="pc-firma-canvas"></canvas>
                  <div class="pc-firma-actions"><button type="button" class="pc-firma-clear" onclick="pcFirmaLimpiarCambio()"><i class="fa-solid fa-eraser"></i> Limpiar</button></div>
                </div>
              <?php endif; ?>
            <?php endif; ?>

          </div>
        </div>
      <?php endif; ?>

      <p style="text-align:center;font-size:11px;color:#94a3b8;margin-top:10px">
        Comercializadora EGS · Equipo de cómputo y software
      </p>

    <?php endif; ?>

  </div>
</div>

<div id="pcLightbox" onclick="this.style.display='none'">
  <span class="x">&times;</span>
  <img id="pcLightboxImg" src="" alt="">
</div>

<script>
function pcAbrirFoto(src){
  document.getElementById('pcLightboxImg').src = src;
  document.getElementById('pcLightbox').style.display = 'flex';
}
function pcValidarComentario(form){
  var t = form.comentarioCliente.value.trim();
  if(!t){ return false; }
  var b = form.querySelector('button[type=submit]'); if(b) b.disabled = true;
  return true;
}
function pcValidarAyuda(form){
  var t = form.mensajeAyuda.value.trim();
  if(!t){ return false; }
  var b = form.querySelector('button[type=submit]'); if(b) b.disabled = true;
  return true;
}

/* ──────── Firma digital (canvas) ──────── */
function pcInitFirma(canvas){
  if(!canvas) return null;
  var ctx = canvas.getContext('2d');
  // Ajuste a alta resolución
  function resize(){
    var rect = canvas.getBoundingClientRect();
    var dpr = window.devicePixelRatio || 1;
    canvas.width = rect.width * dpr;
    canvas.height = rect.height * dpr;
    ctx.setTransform(1,0,0,1,0,0);
    ctx.scale(dpr, dpr);
    ctx.lineWidth = 2.4;
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';
    ctx.strokeStyle = '#0f172a';
  }
  resize();
  window.addEventListener('resize', resize);

  var drawing = false, hasInk = false, lastX = 0, lastY = 0;
  function pos(e){
    var r = canvas.getBoundingClientRect();
    var p = (e.touches && e.touches[0]) ? e.touches[0] : e;
    return { x: p.clientX - r.left, y: p.clientY - r.top };
  }
  function start(e){ e.preventDefault(); drawing = true; var p = pos(e); lastX = p.x; lastY = p.y; }
  function move(e){
    if(!drawing) return;
    e.preventDefault();
    var p = pos(e);
    ctx.beginPath();
    ctx.moveTo(lastX, lastY);
    ctx.lineTo(p.x, p.y);
    ctx.stroke();
    lastX = p.x; lastY = p.y; hasInk = true;
  }
  function end(){ drawing = false; }

  canvas.addEventListener('mousedown', start);
  canvas.addEventListener('mousemove', move);
  window.addEventListener('mouseup', end);
  canvas.addEventListener('touchstart', start, {passive:false});
  canvas.addEventListener('touchmove', move, {passive:false});
  canvas.addEventListener('touchend', end);

  return {
    hasInk: function(){ return hasInk; },
    clear: function(){ ctx.clearRect(0,0,canvas.width,canvas.height); hasInk = false; },
    dataUrl: function(){ return canvas.toDataURL('image/png'); }
  };
}

var pcFirma = null;
var pcFirmaCambio = null;

document.addEventListener('DOMContentLoaded', function(){
  var c1 = document.getElementById('pcFirmaCanvas');
  if(c1) pcFirma = pcInitFirma(c1);
  var c2 = document.getElementById('pcFirmaCanvasCambio');
  if(c2) pcFirmaCambio = pcInitFirma(c2);
});

function pcFirmaLimpiar(){ if(pcFirma) pcFirma.clear(); }
function pcFirmaLimpiarCambio(){ if(pcFirmaCambio) pcFirmaCambio.clear(); }

function pcPrivConfirmar(valor){
  var form = document.getElementById('pcPrivForm');
  if(!form) return;
  document.getElementById('pcPrivAccion').value = String(valor);
  if(valor === 1){
    if(!pcFirma || !pcFirma.hasInk()){
      alert('Por favor traza tu firma antes de aceptar.');
      return;
    }
    document.getElementById('pcPrivFirma').value = pcFirma.dataUrl();
  } else {
    document.getElementById('pcPrivFirma').value = '';
  }
  form.submit();
}

function pcPrivSubmit(form){
  // submit programático ya validó; deshabilita botones
  form.querySelectorAll('button').forEach(function(b){ b.disabled = true; });
  return true;
}

function pcPrivChangeSubmit(form){
  // Si está cambiando de RECHAZO → ACEPTO, exigir firma en el canvas de cambio
  var hidden = form.querySelector('input[name=aceptaPrivacidad]');
  if(hidden && hidden.value === '1'){
    // Mostrar canvas si está oculto
    var box = document.getElementById('pcFirmaBoxCambio');
    if(box && box.style.display === 'none'){
      box.style.display = 'block';
      // Re-init canvas ahora que es visible
      if(!pcFirmaCambio){
        var c2 = document.getElementById('pcFirmaCanvasCambio');
        if(c2) pcFirmaCambio = pcInitFirma(c2);
      }
      alert('Para aceptar el aviso, firma en el recuadro y vuelve a presionar.');
      return false;
    }
    if(!pcFirmaCambio || !pcFirmaCambio.hasInk()){
      alert('Por favor traza tu firma antes de aceptar.');
      return false;
    }
    var inp = document.getElementById('pcPrivFirmaCambio');
    if(inp) inp.value = pcFirmaCambio.dataUrl();
  }
  form.querySelectorAll('button').forEach(function(b){ b.disabled = true; });
  return true;
}
</script>
