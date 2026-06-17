<?php
/**
 * estado-orden-cliente.php
 *
 * Página PÚBLICA (sin login) a la que el cliente llega escaneando el QR del
 * ticket de su orden: ?ruta=estado-orden-cliente&token=<token_cliente>
 *
 * Muestra: estado del equipo, cotización de la orden, historial de reportes de
 * estado del equipo, comentarios del cliente, resumen de la orden, información
 * de entrega y una sección de experiencia (reseña + calificación, envío único).
 *
 * El acceso se valida por el token opaco de la orden (no por el id secuencial).
 */

$token  = isset($_GET["token"]) ? trim((string) $_GET["token"]) : "";
$orden  = $token !== "" ? controladorOrdenes::ctrMostrarOrdenPorToken($token) : null;

// ── Feedback de envíos (comentario) ──
$soc_msgComentario = null;   // ok | duplicate | vacio | error

if (is_array($orden)) {

    $soc_idOrden = intval($orden["id"]);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (isset($_POST["comentarioCliente"])) {
            $soc_msgComentario = controladorComentarioCliente::ctrlGuardarComentario($soc_idOrden);
        }
    }

    // ── Datos a mostrar ──
    $soc_comentarios = controladorComentarioCliente::ctrMostrar($soc_idOrden);
    $soc_reportes    = controladorReporteEquipo::ctrMostrarReportes($soc_idOrden);
    $soc_fotos       = controladorReporteEquipo::ctrMostrarFotosPorOrden($soc_idOrden);

    // Agrupar fotos por id_reporte
    $soc_fotosPorReporte = array();
    if (is_array($soc_fotos)) {
        foreach ($soc_fotos as $f) {
            $soc_fotosPorReporte[intval($f["id_reporte"])][] = $f["ruta"];
        }
    }

    // Nombre del cliente (best-effort; no es crítico)
    $soc_nombreCliente = "";
    if (!empty($orden["id_usuario"]) && class_exists("ControladorClientes")) {
        try {
            $cli = ControladorClientes::ctrMostrarClientes("id", $orden["id_usuario"]);
            if (is_array($cli) && !empty($cli) && isset($cli[0]["nombre"])) {
                $soc_nombreCliente = $cli[0]["nombre"];
            }
        } catch (Exception $e) {
            $soc_nombreCliente = "";
        }
    }

    // ── Monedero / Recompensas ──
    $soc_monederoOk = false;
    $soc_saldo = 0;
    $soc_entregadas = 0;
    $soc_porcentaje = 1;
    $soc_movimientos = array();
    $soc_nombreCortoMonedero = "";

    if (!empty($orden["id_usuario"]) && class_exists("ControladorRecompensas")) {
        try {
            $soc_idClienteM = intval($orden["id_usuario"]);
            $soc_infoRecomp = ControladorRecompensas::ctrObtenerInfoRecompensas($soc_idClienteM);

            if ($soc_infoRecomp && !empty($soc_infoRecomp["token"])) {
                $soc_monederoData = ControladorRecompensas::ctrObtenerMonederoPorToken($soc_infoRecomp["token"]);

                if ($soc_monederoData) {
                    $soc_monederoOk   = true;
                    $soc_saldo        = $soc_monederoData["saldo"];
                    $soc_entregadas   = $soc_monederoData["entregadas"];
                    $soc_porcentaje   = $soc_monederoData["porcentaje"];
                    $soc_movimientos  = $soc_monederoData["movimientos"];

                    $soc_nombreCortoMonedero = $soc_monederoData["nombre_cliente"];
                    if (mb_strlen($soc_nombreCortoMonedero) > 24) {
                        $partes = explode(' ', trim($soc_nombreCortoMonedero));
                        if (count($partes) >= 2) {
                            $soc_nombreCortoMonedero = $partes[0] . ' ' . $partes[1];
                        }
                        if (mb_strlen($soc_nombreCortoMonedero) > 24) {
                            $soc_nombreCortoMonedero = mb_substr($soc_nombreCortoMonedero, 0, 22) . '...';
                        }
                    }
                }
            }
        } catch (Exception $e) {
            $soc_monederoOk = false;
        }
    }
}

/**
 * Mapa de estado de la orden a información amigable para el cliente.
 */
if (!function_exists("soc_estadoInfo")) {
    function soc_estadoInfo($estado)
    {
        $e = strtolower((string) $estado);
        // [color, icono, etiqueta, descripción para el cliente]
        if (strpos($e, "entregad") !== false) {
            return array("#16a34a", "fa-circle-check", "Entregado", "Tu equipo ya fue entregado. ¡Gracias por tu confianza!");
        }
        if (strpos($e, "termin") !== false) {
            return array("#0ea5e9", "fa-flag-checkered", "Terminada", "El servicio terminó y tu equipo está listo para entrega.");
        }
        if (strpos($e, "rev") !== false || strpos($e, "revisión") !== false || strpos($e, "revision") !== false) {
            return array("#f59e0b", "fa-magnifying-glass", "En revisión", "Estamos revisando tu equipo para definir el servicio.");
        }
        if (strpos($e, "sup") !== false) {
            return array("#8b5cf6", "fa-user-gear", "En supervisión", "Tu equipo está en supervisión de calidad.");
        }
        if (strpos($e, "aut") !== false) {
            return array("#f97316", "fa-clock", "Pendiente de autorización", "Esperamos tu autorización para continuar con el servicio.");
        }
        if (strpos($e, "aceptad") !== false || strpos($e, "(ok)") !== false) {
            return array("#22c55e", "fa-thumbs-up", "Aceptada", "El servicio fue aceptado y está en proceso.");
        }
        if (strpos($e, "cancel") !== false) {
            return array("#ef4444", "fa-circle-xmark", "Cancelada", "Esta orden fue cancelada.");
        }
        return array("#64748b", "fa-circle-info", $estado ? $estado : "En proceso", "Tu orden está en proceso.");
    }
}

/** Formatea fecha "Y-m-d H:i:s" a "d/m/Y H:i" de forma segura. */
if (!function_exists("soc_fecha")) {
    function soc_fecha($f, $conHora = true)
    {
        if (empty($f) || $f === "0000-00-00 00:00:00" || $f === "0000-00-00") return "—";
        $ts = strtotime($f);
        if ($ts === false) return "—";
        return date($conHora ? "d/m/Y H:i" : "d/m/Y", $ts);
    }
}
?>

<style>
/* ═══ Estado de orden (cliente) — público y responsivo ═══ */
.main-header, .main-sidebar, .left-side,
.control-sidebar, .main-footer { display: none !important; }
body.sidebar-mini .content-wrapper,
body .content-wrapper { margin-left: 0 !important; }

.soc-wrapper {
    margin-left: 0 !important;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    min-height: 100vh;
    padding: 20px 12px 48px;
}
.soc-container { max-width: 680px; margin: 0 auto; }

.soc-topbar { text-align: center; margin-bottom: 18px; }
.soc-topbar img { max-height: 64px; width: auto; }
.soc-topbar h4 { margin: 8px 0 0; font-weight: 800; color: #0f172a; font-size: 16px; letter-spacing: .02em; }
.soc-topbar p { margin: 2px 0 0; font-size: 12px; color: #64748b; }

.soc-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 6px 24px rgba(0,0,0,.08);
    border: 1px solid #e2e8f0;
    margin-bottom: 16px;
    overflow: hidden;
}
.soc-card-head {
    display: flex; align-items: center; gap: 10px;
    padding: 14px 18px;
    border-bottom: 1px solid #f1f5f9;
}
.soc-card-head i { font-size: 16px; color: #0ea5e9; width: 20px; text-align: center; }
.soc-card-head h3 { margin: 0; font-size: 15px; font-weight: 800; color: #0f172a; }
.soc-card-body { padding: 18px; }

/* Estado grande */
.soc-estado {
    text-align: center; color: #fff; padding: 26px 18px;
}
.soc-estado .ico {
    width: 64px; height: 64px; border-radius: 50%;
    background: rgba(255,255,255,.18);
    display: inline-flex; align-items: center; justify-content: center;
    margin-bottom: 12px;
}
.soc-estado .ico i { font-size: 30px; }
.soc-estado h2 { margin: 0 0 4px; font-size: 22px; font-weight: 800; }
.soc-estado p  { margin: 0; font-size: 13px; opacity: .92; line-height: 1.5; }

/* Tabla cotización */
.soc-table { width: 100%; font-size: 13px; }
.soc-table th { font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 700; padding: 8px 10px; border-bottom: 1px solid #e2e8f0; }
.soc-table td { padding: 9px 10px; border-bottom: 1px solid #f1f5f9; vertical-align: top; }
.soc-table tr:last-child td { border-bottom: none; }
.soc-total-row { display: flex; justify-content: space-between; align-items: center; margin-top: 14px; padding: 14px 16px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; }
.soc-total-row .lbl { font-size: 12px; text-transform: uppercase; letter-spacing: .05em; color: #15803d; font-weight: 700; }
.soc-total-row .amt { font-size: 24px; font-weight: 800; color: #16a34a; }

/* Filas info (resumen / entrega) */
.soc-info-row { display: flex; align-items: flex-start; gap: 10px; padding: 9px 0; border-bottom: 1px solid #f1f5f9; }
.soc-info-row:last-child { border-bottom: none; }
.soc-info-row i { color: #94a3b8; width: 18px; text-align: center; margin-top: 2px; }
.soc-info-row .k { font-size: 12px; color: #64748b; min-width: 110px; }
.soc-info-row .v { font-size: 13px; color: #0f172a; font-weight: 600; word-break: break-word; }

/* Reportes (timeline) */
.soc-rep { border-left: 3px solid #0ea5e9; padding: 0 0 16px 16px; margin-left: 6px; position: relative; }
.soc-rep:last-child { padding-bottom: 0; }
.soc-rep:before { content: ""; position: absolute; left: -8px; top: 2px; width: 13px; height: 13px; border-radius: 50%; background: #0ea5e9; border: 2px solid #fff; }
.soc-rep .fch { font-size: 11px; color: #94a3b8; margin-bottom: 4px; }
.soc-rep .txt { font-size: 13px; color: #1e293b; line-height: 1.5; white-space: pre-line; }
.soc-rep-fotos { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px; }
.soc-rep-fotos img { width: 72px; height: 72px; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0; cursor: pointer; }

/* Comentarios */
.soc-coment { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 10px 12px; margin-bottom: 8px; }
.soc-coment .fch { font-size: 11px; color: #94a3b8; margin-bottom: 3px; }
.soc-coment .txt { font-size: 13px; color: #1e293b; line-height: 1.5; white-space: pre-line; }

.soc-form textarea { width: 100%; border: 1px solid #cbd5e1; border-radius: 10px; padding: 10px 12px; font-size: 13px; resize: vertical; min-height: 70px; }
.soc-btn { display: inline-flex; align-items: center; gap: 8px; background: #0f172a; color: #fff; padding: 11px 22px; border-radius: 10px; font-weight: 700; font-size: 13px; border: none; cursor: pointer; margin-top: 10px; transition: background .2s; }
.soc-btn:hover { background: #1e293b; }
.soc-btn[disabled] { opacity: .5; cursor: not-allowed; }

.soc-alert { padding: 10px 14px; border-radius: 10px; font-size: 13px; margin-bottom: 12px; }
.soc-alert.ok   { background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; }
.soc-alert.warn { background: #fffbeb; border: 1px solid #fde68a; color: #92400e; }
.soc-alert.err  { background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; }

.soc-empty { text-align: center; color: #94a3b8; font-size: 13px; padding: 8px 0; }

/* Lightbox simple */
#socLightbox { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.85); z-index: 99999; align-items: center; justify-content: center; }
#socLightbox img { max-width: 92vw; max-height: 88vh; border-radius: 8px; }
#socLightbox .x { position: absolute; top: 16px; right: 20px; color: #fff; font-size: 32px; cursor: pointer; }

/* ═══ Monedero EGS (dentro de soc-card) ═══ */
.soc-mc {
    position: relative;
    background: linear-gradient(135deg, #1e3a5f 0%, #0d253f 40%, #1a1a2e 100%);
    border-radius: 14px;
    padding: 24px 20px 20px;
    color: #fff;
    margin: 16px;
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(0,0,0,.18);
}
.soc-mc::before {
    content: ''; position: absolute; top: -60%; right: -30%; width: 280px; height: 280px;
    background: radial-gradient(circle, rgba(99,102,241,.25) 0%, transparent 70%);
    pointer-events: none;
}
.soc-mc::after {
    content: ''; position: absolute; bottom: -40%; left: -20%; width: 240px; height: 240px;
    background: radial-gradient(circle, rgba(16,185,129,.15) 0%, transparent 70%);
    pointer-events: none;
}
.soc-mc-top { display: flex; align-items: center; justify-content: space-between; position: relative; z-index: 1; margin-bottom: 20px; }
.soc-mc-logo { width: 42px; height: 42px; border-radius: 10px; object-fit: contain; background: rgba(255,255,255,.12); padding: 4px; }
.soc-mc-brand { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 2.5px; color: rgba(255,255,255,.55); }
.soc-mc-chip { position: relative; z-index: 1; margin-bottom: 16px; }
.soc-mc-chip-icon { width: 40px; height: 28px; background: linear-gradient(135deg, #daa520, #f0c060, #daa520); border-radius: 5px; display: flex; align-items: center; justify-content: center; }
.soc-mc-chip-icon::after { content: ''; width: 18px; height: 14px; border: 1.5px solid rgba(139,90,0,.4); border-radius: 3px; }
.soc-mc-titular { position: relative; z-index: 1; margin-bottom: 14px; }
.soc-mc-titular-lbl { font-size: 8px; font-weight: 600; text-transform: uppercase; letter-spacing: 1.5px; color: rgba(255,255,255,.4); margin-bottom: 2px; }
.soc-mc-titular-name { font-size: 16px; font-weight: 800; letter-spacing: .5px; }
.soc-mc-saldo { position: relative; z-index: 1; margin-bottom: 16px; }
.soc-mc-saldo-lbl { font-size: 9px; font-weight: 600; text-transform: uppercase; letter-spacing: 2px; color: rgba(255,255,255,.5); margin-bottom: 4px; }
.soc-mc-saldo-amt {
    font-size: 36px; font-weight: 900; letter-spacing: -1px; line-height: 1;
    background: linear-gradient(135deg, #fff 0%, #a5b4fc 100%);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
}
.soc-mc-saldo-amt.zero { background: linear-gradient(135deg, #64748b 0%, #475569 100%); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; }
.soc-mc-saldo-periodo { font-size: 9px; font-weight: 500; color: rgba(255,255,255,.35); margin-top: 6px; }
.soc-mc-bottom { display: flex; align-items: flex-end; justify-content: space-between; position: relative; z-index: 1; }
.soc-mc-type { font-size: 9px; font-weight: 600; text-transform: uppercase; letter-spacing: 1.5px; color: rgba(255,255,255,.4); }
.soc-mc-contactless { opacity: .3; }
.soc-mc-contactless svg { width: 22px; height: 22px; }

.soc-monedero-stats {
    display: grid; grid-template-columns: 1fr 1fr; gap: 10px;
    margin: 0 16px 12px;
}
.soc-monedero-stat {
    background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px;
    padding: 14px 12px; text-align: center;
}
.soc-monedero-stat .val { font-size: 24px; font-weight: 900; color: #0f172a; line-height: 1; }
.soc-monedero-stat .lbl { font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: .8px; color: #64748b; margin-top: 4px; }

.soc-monedero-info {
    display: flex; align-items: flex-start; gap: 8px;
    margin: 0 16px 14px; padding: 10px 14px;
    background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 10px;
    font-size: 11px; color: #1e40af; line-height: 1.5;
}
.soc-monedero-info i { margin-top: 2px; flex-shrink: 0; }
.soc-monedero-info strong { font-weight: 700; }

.soc-mov-section { padding: 0 16px 16px; }
.soc-mov-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
.soc-mov-title { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #64748b; }
.soc-mov-count { font-size: 10px; color: #94a3b8; font-weight: 500; }

.soc-mov-item { display: flex; align-items: center; padding: 10px 0; border-bottom: 1px solid #f1f5f9; }
.soc-mov-item:last-child { border-bottom: none; }
.soc-mov-icon {
    width: 34px; height: 34px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 14px; margin-right: 10px; flex-shrink: 0;
}
.soc-mov-icon.acum { background: #f0fdf4; color: #16a34a; }
.soc-mov-icon.canje { background: #eef2ff; color: #6366f1; }
.soc-mov-icon.exp { background: #fef2f2; color: #ef4444; }
.soc-mov-info { flex: 1; min-width: 0; }
.soc-mov-desc { font-size: 12px; font-weight: 600; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.soc-mov-fecha { font-size: 10px; color: #94a3b8; margin-top: 2px; }
.soc-mov-vence { display: inline-block; background: #fffbeb; border: 1px solid #fde68a; color: #92400e; padding: 1px 6px; border-radius: 4px; font-size: 8px; font-weight: 600; margin-left: 4px; }
.soc-mov-monto { font-size: 13px; font-weight: 800; white-space: nowrap; margin-left: 10px; }
.soc-mov-monto.pos { color: #16a34a; }
.soc-mov-monto.neg { color: #ef4444; }

/* ═══ Califícanos en Google ═══ */
.soc-google-box {
    background: #4285F4;
    background: linear-gradient(135deg, #1a73e8 0%, #4285F4 55%, #669df6 100%);
    padding: 32px 22px; text-align: center;
}
.soc-google-logo {
    display: inline-block; background: #fff;
    padding: 10px 18px; border-radius: 40px;
    font-size: 22px; font-weight: 800; letter-spacing: .5px;
    box-shadow: 0 4px 14px rgba(0,0,0,.18); margin-bottom: 18px;
}
.soc-google-rate { font-size: 30px; letter-spacing: 5px; margin-bottom: 16px; }
.soc-google-h { font-size: 19px; font-weight: 800; color: #fff; margin-bottom: 8px; }
.soc-google-p { font-size: 13px; color: rgba(255,255,255,.9); line-height: 1.6; max-width: 320px; margin: 0 auto 22px; }
.soc-google-cta {
    display: inline-block; background: #fff; color: #1a73e8;
    padding: 14px 36px; border-radius: 30px;
    font-weight: 800; font-size: 14px; text-decoration: none;
    box-shadow: 0 6px 18px rgba(0,0,0,.22); transition: transform .15s, box-shadow .15s;
}
.soc-google-cta:hover { transform: translateY(-2px); box-shadow: 0 10px 26px rgba(0,0,0,.3); color: #1558c0; }
.soc-google-foot { font-size: 11px; color: rgba(255,255,255,.55); margin-top: 16px; }

@media (max-width: 576px) {
    .soc-info-row { flex-wrap: wrap; }
    .soc-info-row .k { min-width: 100%; }
    .soc-total-row .amt { font-size: 20px; }
    .soc-estado h2 { font-size: 20px; }
    .soc-btn { width: 100%; justify-content: center; }
    .soc-mc { margin: 12px; padding: 20px 16px 16px; }
    .soc-mc-saldo-amt { font-size: 30px; }
    .soc-monedero-stats { margin: 0 12px 10px; }
    .soc-monedero-info { margin: 0 12px 12px; }
    .soc-mov-section { padding: 0 12px 12px; }
    .soc-google-cta { width: 100%; }
}

</style>

<div class="content-wrapper soc-wrapper">
  <div class="soc-container">

    <div class="soc-topbar">
      <img src="vistas/img/plantilla/Captura3.PNG" alt="Logo">
      <h4>COMERCIALIZADORA EGS</h4>
      <p>Estado de tu orden de servicio</p>
    </div>

    <?php if (!is_array($orden)): ?>

      <!-- ═══ Orden no encontrada ═══ -->
      <div class="soc-card">
        <div class="soc-estado" style="background:linear-gradient(135deg,#475569 0%,#1e293b 100%)">
          <div class="ico"><i class="fa-solid fa-circle-xmark"></i></div>
          <h2>Orden no encontrada</h2>
          <p>El código del QR no corresponde a ninguna orden registrada.<br>Verifica el enlace o contáctanos.</p>
        </div>
      </div>

    <?php else:
      list($estColor, $estIcon, $estLabel, $estDesc) = soc_estadoInfo($orden["estado"]);
      $soc_eLC = strtolower((string) $orden["estado"]);
      $soc_mostrarPrecios = (
          strpos($soc_eLC, "aceptad") !== false ||
          strpos($soc_eLC, "termin") !== false ||
          strpos($soc_eLC, "entregad") !== false ||
          strpos($soc_eLC, "sin reparaci") !== false
      );
    ?>

      <!-- ═══ 1) Estado del equipo ═══ -->
      <div class="soc-card">
        <div class="soc-estado" style="background:linear-gradient(135deg,<?php echo $estColor; ?> 0%, <?php echo $estColor; ?>cc 100%)">
          <div class="ico"><i class="fa-solid <?php echo $estIcon; ?>"></i></div>
          <h2><?php echo htmlspecialchars($estLabel); ?></h2>
          <p><?php echo htmlspecialchars($estDesc); ?></p>
        </div>
        <div class="soc-card-body" style="padding-top:14px;padding-bottom:14px;text-align:center">
          <span style="font-size:12px;color:#64748b">Orden</span>
          <strong style="font-size:18px;color:#0f172a;display:block">#<?php echo intval($orden["id"]); ?></strong>
        </div>
      </div>

      <!-- ═══ 2) Cotización de la orden ═══ -->
      <?php if ($soc_mostrarPrecios): ?>
      <?php
        $soc_lineas = array();
        $nombresFijos = array("partidaUno","partidaDos","partidaTres","partidaCuatro","partidaCinco","partidaSeis","partidaSiete","partidaOcho","partidaNueve","partidaDiez");
        $preciosFijos = array("precioUno","precioDos","precioTres","precioCuatro","precioCinco","precioSeis","precioSiete","precioOcho","precioNueve","precioDiez");
        for ($i = 0; $i < 10; $i++) {
            $desc = isset($orden[$nombresFijos[$i]]) ? trim((string) $orden[$nombresFijos[$i]]) : "";
            $prec = isset($orden[$preciosFijos[$i]]) ? floatval($orden[$preciosFijos[$i]]) : 0;
            if ($desc !== "" || $prec > 0) {
                $soc_lineas[] = array("descripcion" => $desc !== "" ? $desc : "Partida", "precio" => $prec);
            }
        }
        // Partidas dinámicas (JSON)
        $partidasJson = isset($orden["partidas"]) ? json_decode($orden["partidas"], true) : null;
        if (is_array($partidasJson)) {
            foreach ($partidasJson as $p) {
                $desc = isset($p["descripcion"]) ? trim((string) $p["descripcion"]) : "";
                $prec = isset($p["precioPartida"]) ? floatval($p["precioPartida"]) : 0;
                if ($desc !== "" || $prec > 0) {
                    $soc_lineas[] = array("descripcion" => $desc !== "" ? $desc : "Partida adicional", "precio" => $prec);
                }
            }
        }
        $soc_total = isset($orden["total"]) ? floatval($orden["total"]) : 0;
      ?>
      <div class="soc-card">
        <div class="soc-card-head"><i class="fa-solid fa-file-invoice-dollar"></i><h3>Cotización de la orden</h3></div>
        <div class="soc-card-body">
          <?php if (!empty($soc_lineas)): ?>
            <table class="soc-table">
              <thead><tr><th>Concepto</th><th style="text-align:right">Precio</th></tr></thead>
              <tbody>
                <?php foreach ($soc_lineas as $ln): ?>
                <tr>
                  <td><?php echo nl2br(htmlspecialchars($ln["descripcion"])); ?></td>
                  <td style="text-align:right;font-weight:700;white-space:nowrap">$<?php echo number_format($ln["precio"], 2); ?></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php else: ?>
            <div class="soc-empty">Aún no hay conceptos cotizados para esta orden.</div>
          <?php endif; ?>
          <div class="soc-total-row">
            <span class="lbl">Total</span>
            <span class="amt">$<?php echo number_format($soc_total, 2); ?></span>
          </div>
        </div>
      </div>
      <?php endif; ?>

      <!-- ═══ 3) Historial de reporte de estado del equipo ═══ -->
      <div class="soc-card">
        <div class="soc-card-head"><i class="fa-solid fa-clipboard-list"></i><h3>Reportes del estado de tu equipo</h3></div>
        <div class="soc-card-body">
          <?php if (is_array($soc_reportes) && !empty($soc_reportes)): ?>
            <?php foreach ($soc_reportes as $rep):
              $rid = intval($rep["id"]);
              $repFotos = isset($soc_fotosPorReporte[$rid]) ? $soc_fotosPorReporte[$rid] : array();
            ?>
            <div class="soc-rep">
              <div class="fch"><i class="fa-regular fa-clock"></i> <?php echo soc_fecha($rep["fecha"]); ?></div>
              <div class="txt"><?php echo htmlspecialchars($rep["descripcion"]); ?></div>
              <?php if (!empty($repFotos)): ?>
              <div class="soc-rep-fotos">
                <?php foreach ($repFotos as $rf): ?>
                  <img src="<?php echo htmlspecialchars($rf); ?>" alt="Foto del reporte" loading="lazy" onclick="socAbrirFoto(this.src)">
                <?php endforeach; ?>
              </div>
              <?php endif; ?>
            </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="soc-empty">Todavía no hay reportes del estado del equipo.</div>
          <?php endif; ?>
        </div>
      </div>

      <!-- ═══ 5) Resumen de la orden ═══ -->
      <div class="soc-card">
        <div class="soc-card-head"><i class="fa-solid fa-receipt"></i><h3>Resumen de la orden</h3></div>
        <div class="soc-card-body">
          <?php if ($soc_nombreCliente !== ""): ?>
          <div class="soc-info-row"><i class="fa-solid fa-user"></i><span class="k">Cliente</span><span class="v"><?php echo htmlspecialchars($soc_nombreCliente); ?></span></div>
          <?php endif; ?>
          <?php if (!empty($orden["titulo"])): ?>
          <div class="soc-info-row"><i class="fa-solid fa-tag"></i><span class="k">Título</span><span class="v"><?php echo htmlspecialchars($orden["titulo"]); ?></span></div>
          <?php endif; ?>
          <?php if (!empty($orden["marcaDelEquipo"]) || !empty($orden["modeloDelEquipo"])): ?>
          <div class="soc-info-row"><i class="fa-solid fa-laptop"></i><span class="k">Equipo</span><span class="v"><?php echo htmlspecialchars(trim(($orden["marcaDelEquipo"] ?? "") . " " . ($orden["modeloDelEquipo"] ?? ""))); ?></span></div>
          <?php endif; ?>
          <?php if (!empty($orden["numeroDeSerieDelEquipo"])): ?>
          <div class="soc-info-row"><i class="fa-solid fa-barcode"></i><span class="k">No. de serie</span><span class="v"><?php echo htmlspecialchars($orden["numeroDeSerieDelEquipo"]); ?></span></div>
          <?php endif; ?>
          <?php if (!empty($orden["fecha_ingreso"]) && ($orden["fecha_ingreso"] ?? "") !== "0000-00-00" && ($orden["fecha_ingreso"] ?? "") !== "0000-00-00 00:00:00"): ?>
          <div class="soc-info-row"><i class="fa-solid fa-calendar-plus"></i><span class="k">Ingreso</span><span class="v"><?php echo soc_fecha($orden["fecha_ingreso"], false); ?></span></div>
          <?php endif; ?>
          <?php if ($soc_mostrarPrecios): ?>
          <div class="soc-info-row"><i class="fa-solid fa-dollar-sign"></i><span class="k">Total</span><span class="v">$<?php echo number_format($soc_total, 2); ?></span></div>
          <?php endif; ?>
        </div>
      </div>

      <!-- ═══ 6) Información de entrega ═══ -->
      <?php $entregado = (stripos((string) $orden["estado"], "entregad") !== false); ?>
      <div class="soc-card">
        <div class="soc-card-head"><i class="fa-solid fa-truck"></i><h3>Información de entrega</h3></div>
        <div class="soc-card-body">
          <div class="soc-info-row">
            <i class="fa-solid fa-circle-<?php echo $entregado ? 'check' : 'half-stroke'; ?>"></i>
            <span class="k">Estado de entrega</span>
            <span class="v" style="color:<?php echo $entregado ? '#16a34a' : '#f59e0b'; ?>">
              <?php echo $entregado ? 'Entregado' : 'Pendiente de entrega'; ?>
            </span>
          </div>
          <?php
            $soc_fechaSalida = $orden["fecha_Salida"] ?? "";
            if (!empty($soc_fechaSalida) && $soc_fechaSalida !== "0000-00-00" && $soc_fechaSalida !== "0000-00-00 00:00:00"):
          ?>
          <div class="soc-info-row">
            <i class="fa-solid fa-calendar-check"></i>
            <span class="k">Fecha de entrega</span>
            <span class="v"><?php echo soc_fecha($soc_fechaSalida, false); ?></span>
          </div>
          <?php endif; ?>
          <?php if (!$entregado): ?>
          <div class="soc-empty" style="text-align:left;margin-top:6px">
            Te avisaremos cuando tu equipo esté listo para recoger.
          </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- ═══ 7) Monedero EGS ═══ -->
      <?php
        $soc_ordenCancelada = (stripos((string) $orden["estado"], "cancel") !== false);
        if ($soc_monederoOk && !$soc_ordenCancelada):
      ?>
      <div class="soc-card">
        <div class="soc-card-head"><i class="fa-solid fa-wallet"></i><h3>Tu Monedero EGS</h3></div>
        <div class="soc-card-body" style="padding:0">

          <!-- Tarjeta digital (acento oscuro) -->
          <div class="soc-mc">
            <div class="soc-mc-top">
              <img src="vistas/img/plantilla/Captura3.PNG" class="soc-mc-logo" alt="EGS">
              <span class="soc-mc-brand">Monedero EGS</span>
            </div>
            <div class="soc-mc-chip"><div class="soc-mc-chip-icon"></div></div>
            <div class="soc-mc-titular">
              <div class="soc-mc-titular-lbl">Titular</div>
              <div class="soc-mc-titular-name"><?php echo htmlspecialchars(mb_strtoupper($soc_nombreCortoMonedero)); ?></div>
            </div>
            <div class="soc-mc-saldo">
              <div class="soc-mc-saldo-lbl">Saldo disponible</div>
              <div class="soc-mc-saldo-amt <?php echo $soc_saldo <= 0 ? 'zero' : ''; ?>">$<?php echo number_format($soc_saldo, 2); ?></div>
              <div class="soc-mc-saldo-periodo">Últimos 6 meses</div>
            </div>
            <div class="soc-mc-bottom">
              <span class="soc-mc-type">Dinero Electrónico</span>
              <span class="soc-mc-contactless"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6.5 17.5a8.5 8.5 0 0 1 0-11"/><path d="M10 15a5 5 0 0 1 0-6"/><path d="M13.5 12.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z" fill="currentColor" stroke="none"/></svg></span>
            </div>
          </div>

          <!-- Stats -->
          <div class="soc-monedero-stats">
            <div class="soc-monedero-stat">
              <div class="val"><?php echo intval($soc_entregadas); ?></div>
              <div class="lbl">Órdenes entregadas</div>
            </div>
            <div class="soc-monedero-stat">
              <div class="val"><?php echo intval($soc_porcentaje); ?>%</div>
              <div class="lbl">Recompensa vigente</div>
            </div>
          </div>

          <!-- Info expiración -->
          <div class="soc-monedero-info">
            <i class="fa-solid fa-circle-info"></i>
            <span>Tu saldo corresponde a las recompensas acumuladas en los <strong>últimos 6 meses</strong>. El dinero electrónico vence 6 meses después de ser generado.</span>
          </div>

          <!-- Movimientos -->
          <div class="soc-mov-section">
            <div class="soc-mov-header">
              <span class="soc-mov-title">Últimos movimientos</span>
              <span class="soc-mov-count"><?php echo count($soc_movimientos); ?> registro<?php echo count($soc_movimientos) != 1 ? 's' : ''; ?></span>
            </div>

            <?php if (empty($soc_movimientos)): ?>
              <div class="soc-empty">Cuando se entregue tu primera orden, aquí verás tu dinero electrónico acumulado.</div>
            <?php else: ?>
              <?php foreach ($soc_movimientos as $mov): ?>
              <div class="soc-mov-item">
                <div class="soc-mov-icon <?php echo $mov["tipo"] == 'acumulacion' ? 'acum' : ($mov["tipo"] == 'canje' ? 'canje' : 'exp'); ?>">
                  <i class="fa-solid <?php echo $mov["tipo"] == 'acumulacion' ? 'fa-arrow-up' : ($mov["tipo"] == 'canje' ? 'fa-arrow-down' : 'fa-rotate'); ?>"></i>
                </div>
                <div class="soc-mov-info">
                  <div class="soc-mov-desc"><?php echo htmlspecialchars($mov["descripcion"]); ?></div>
                  <div class="soc-mov-fecha">
                    <?php echo date('d/m/Y H:i', strtotime($mov["fecha"])); ?>
                    <?php if ($mov["tipo"] == 'acumulacion' && !empty($mov["fecha_expiracion"]) && !$mov["expirado"]): ?>
                      <span class="soc-mov-vence">Vence: <?php echo date('d/m/Y', strtotime($mov["fecha_expiracion"])); ?></span>
                    <?php endif; ?>
                  </div>
                </div>
                <div class="soc-mov-monto <?php echo floatval($mov["monto"]) >= 0 ? 'pos' : 'neg'; ?>">
                  <?php echo floatval($mov["monto"]) >= 0 ? '+' : ''; ?>$<?php echo number_format(abs(floatval($mov["monto"])), 2); ?>
                </div>
              </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>

        </div>
      </div>
      <?php endif; ?>

      <!-- ═══ 4) Comentarios del cliente ═══ -->
      <div class="soc-card">
        <div class="soc-card-head"><i class="fa-solid fa-comments"></i><h3>Tus comentarios</h3></div>
        <div class="soc-card-body">
          <?php if ($soc_msgComentario === "ok"): ?>
            <div class="soc-alert ok"><i class="fa-solid fa-check"></i> ¡Gracias! Tu comentario fue enviado.</div>
          <?php elseif ($soc_msgComentario === "vacio"): ?>
            <div class="soc-alert warn">Escribe un comentario antes de enviar.</div>
          <?php elseif ($soc_msgComentario === "duplicate"): ?>
            <div class="soc-alert warn">Ese comentario ya fue registrado hace unos segundos.</div>
          <?php elseif ($soc_msgComentario === "error"): ?>
            <div class="soc-alert err">No pudimos guardar tu comentario. Inténtalo de nuevo.</div>
          <?php endif; ?>

          <?php if (is_array($soc_comentarios) && !empty($soc_comentarios)): ?>
            <?php foreach ($soc_comentarios as $c): ?>
            <div class="soc-coment">
              <div class="fch"><i class="fa-regular fa-clock"></i> <?php echo soc_fecha($c["fecha"]); ?></div>
              <div class="txt"><?php echo htmlspecialchars($c["comentario"]); ?></div>
            </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="soc-empty">Aún no has dejado comentarios.</div>
          <?php endif; ?>

          <form method="post" class="soc-form" style="margin-top:12px" onsubmit="return socValidarComentario(this);">
            <textarea name="comentarioCliente" maxlength="1000" placeholder="Escribe una duda o comentario sobre tu orden..."></textarea>
            <button type="submit" class="soc-btn"><i class="fa-solid fa-paper-plane"></i> Enviar comentario</button>
          </form>
        </div>
      </div>

      <!-- ═══ 7) Califícanos en Google ═══ -->
      <div class="soc-card" style="overflow:hidden">
        <div class="soc-google-box">
          <div class="soc-google-logo">
            <span style="color:#4285F4">G</span><span style="color:#EA4335">o</span><span style="color:#FBBC05">o</span><span style="color:#4285F4">g</span><span style="color:#34A853">l</span><span style="color:#EA4335">e</span>
          </div>
          <div class="soc-google-rate">
            <span style="color:#EA4335">&#9733;</span><span style="color:#FBBC05">&#9733;</span><span style="color:#34A853">&#9733;</span><span style="color:#fff;opacity:.7">&#9733;</span><span style="color:#fff;opacity:.7">&#9733;</span>
          </div>
          <div class="soc-google-h">&iquest;C&oacute;mo fue tu experiencia?</div>
          <div class="soc-google-p">Tu opini&oacute;n nos ayuda a seguir mejorando. D&eacute;janos una rese&ntilde;a en Google Maps.</div>
          <a class="soc-google-cta" href="https://www.google.com/maps/place/EGS+EQUIPO+DE+C%C3%93MPUTO+Y+SOFTWARE/@19.2933702,-99.6549282,17z/" target="_blank" rel="noopener">&#9733; Dejar rese&ntilde;a en Google</a>
          <div class="soc-google-foot">Se abrir&aacute; Google Maps en una nueva pesta&ntilde;a</div>
        </div>
      </div>

      <p style="text-align:center;font-size:11px;color:#94a3b8;margin-top:4px">
        Comercializadora EGS · Equipo de cómputo y software
      </p>

    <?php endif; ?>

  </div>
</div>

<!-- Lightbox de fotos -->
<div id="socLightbox" onclick="this.style.display='none'">
  <span class="x">&times;</span>
  <img id="socLightboxImg" src="" alt="">
</div>

<script>
function socAbrirFoto(src){
  document.getElementById('socLightboxImg').src = src;
  document.getElementById('socLightbox').style.display = 'flex';
}
function socValidarComentario(form){
  var t = form.comentarioCliente.value.trim();
  if(!t){ return false; }
  // Evitar doble envío
  var b = form.querySelector('button[type=submit]'); if(b){ b.disabled = true; }
  return true;
}

</script>
