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

// ── Feedback de envíos (comentario / reseña) ──
$soc_msgComentario = null;   // ok | duplicate | vacio | error
$soc_msgResena     = null;   // ok | duplicate | invalida | error

if (is_array($orden)) {

    $soc_idOrden = intval($orden["id"]);

    // Procesar envíos POST. El id de la orden proviene del token validado,
    // nunca de un campo POST, para evitar manipulación.
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (isset($_POST["comentarioCliente"])) {
            $soc_msgComentario = controladorComentarioCliente::ctrlGuardarComentario($soc_idOrden);
        }
        if (isset($_POST["calificacionResena"])) {
            $soc_msgResena = controladorResenaOrden::ctrlGuardarResena($soc_idOrden);
        }
    }

    // ── Datos a mostrar ──
    $soc_comentarios = controladorComentarioCliente::ctrMostrar($soc_idOrden);
    $soc_resena      = controladorResenaOrden::ctrMostrar($soc_idOrden);
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

/* Estrellas */
.soc-stars { display: inline-flex; flex-direction: row-reverse; gap: 4px; font-size: 34px; }
.soc-stars input { display: none; }
.soc-stars label { color: #cbd5e1; cursor: pointer; transition: color .15s; }
.soc-stars label:hover,
.soc-stars label:hover ~ label,
.soc-stars input:checked ~ label { color: #f59e0b; }
.soc-stars-static { font-size: 28px; color: #f59e0b; letter-spacing: 2px; }
.soc-stars-static .off { color: #cbd5e1; }

.soc-empty { text-align: center; color: #94a3b8; font-size: 13px; padding: 8px 0; }

/* Lightbox simple */
#socLightbox { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.85); z-index: 99999; align-items: center; justify-content: center; }
#socLightbox img { max-width: 92vw; max-height: 88vh; border-radius: 8px; }
#socLightbox .x { position: absolute; top: 16px; right: 20px; color: #fff; font-size: 32px; cursor: pointer; }

@media (max-width: 576px) {
    .soc-info-row { flex-wrap: wrap; }
    .soc-info-row .k { min-width: 100%; }
    .soc-total-row .amt { font-size: 20px; }
    .soc-estado h2 { font-size: 20px; }
    .soc-btn { width: 100%; justify-content: center; }
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
          <div class="soc-info-row"><i class="fa-solid fa-calendar-plus"></i><span class="k">Ingreso</span><span class="v"><?php echo soc_fecha($orden["fecha_ingreso"] ?? "", false); ?></span></div>
          <div class="soc-info-row"><i class="fa-solid fa-dollar-sign"></i><span class="k">Total</span><span class="v">$<?php echo number_format($soc_total, 2); ?></span></div>
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
          <div class="soc-info-row">
            <i class="fa-solid fa-calendar-check"></i>
            <span class="k">Fecha de entrega</span>
            <span class="v"><?php echo soc_fecha($orden["fecha_Salida"] ?? "", false); ?></span>
          </div>
          <?php if (!$entregado): ?>
          <div class="soc-empty" style="text-align:left;margin-top:6px">
            Te avisaremos cuando tu equipo esté listo para recoger.
          </div>
          <?php endif; ?>
        </div>
      </div>

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

      <!-- ═══ 7) Tu experiencia (reseña + calificación) ═══ -->
      <div class="soc-card">
        <div class="soc-card-head"><i class="fa-solid fa-star"></i><h3>Tu experiencia</h3></div>
        <div class="soc-card-body">
          <?php if ($soc_msgResena === "ok"): ?>
            <div class="soc-alert ok"><i class="fa-solid fa-check"></i> ¡Gracias por calificar nuestro servicio!</div>
          <?php elseif ($soc_msgResena === "invalida"): ?>
            <div class="soc-alert warn">Selecciona una calificación de 1 a 5 estrellas.</div>
          <?php elseif ($soc_msgResena === "duplicate"): ?>
            <div class="soc-alert warn">Ya habías enviado tu reseña para esta orden.</div>
          <?php elseif ($soc_msgResena === "error"): ?>
            <div class="soc-alert err">No pudimos guardar tu reseña. Inténtalo de nuevo.</div>
          <?php endif; ?>

          <?php
            // Reseña ya enviada (la recién creada o una previa) → solo lectura.
            $resenaMostrar = $soc_resena;
            if (!$resenaMostrar && $soc_msgResena === "ok") {
                $resenaMostrar = controladorResenaOrden::ctrMostrar($soc_idOrden);
            }
          ?>

          <?php if ($resenaMostrar): $cal = intval($resenaMostrar["calificacion"]); ?>
            <div style="text-align:center">
              <div class="soc-stars-static">
                <?php for ($s = 1; $s <= 5; $s++): ?>
                  <span class="<?php echo $s <= $cal ? '' : 'off'; ?>">&#9733;</span>
                <?php endfor; ?>
              </div>
              <?php if (!empty($resenaMostrar["comentario"])): ?>
                <p style="font-size:13px;color:#1e293b;margin-top:10px;font-style:italic">“<?php echo htmlspecialchars($resenaMostrar["comentario"]); ?>”</p>
              <?php endif; ?>
              <p style="font-size:11px;color:#94a3b8;margin-top:8px">Enviada el <?php echo soc_fecha($resenaMostrar["fecha"]); ?></p>
            </div>
          <?php else: ?>
            <p style="font-size:13px;color:#64748b;margin-bottom:10px;text-align:center">¿Cómo calificarías nuestro servicio?</p>
            <form method="post" class="soc-form" style="text-align:center" onsubmit="return socValidarResena(this);">
              <div class="soc-stars">
                <input type="radio" name="calificacionResena" id="soc-star5" value="5"><label for="soc-star5" title="Excelente">&#9733;</label>
                <input type="radio" name="calificacionResena" id="soc-star4" value="4"><label for="soc-star4" title="Muy bueno">&#9733;</label>
                <input type="radio" name="calificacionResena" id="soc-star3" value="3"><label for="soc-star3" title="Bueno">&#9733;</label>
                <input type="radio" name="calificacionResena" id="soc-star2" value="2"><label for="soc-star2" title="Regular">&#9733;</label>
                <input type="radio" name="calificacionResena" id="soc-star1" value="1"><label for="soc-star1" title="Malo">&#9733;</label>
              </div>
              <textarea name="comentarioResena" maxlength="1000" placeholder="Cuéntanos sobre tu experiencia (opcional)..." style="margin-top:12px"></textarea>
              <div><button type="submit" class="soc-btn"><i class="fa-solid fa-star"></i> Enviar reseña</button></div>
            </form>
          <?php endif; ?>
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
function socValidarResena(form){
  var sel = form.querySelector('input[name=calificacionResena]:checked');
  if(!sel){
    if (window.swal) { swal({type:'warning', title:'Selecciona una calificación', text:'Toca las estrellas para calificar.'}); }
    return false;
  }
  var b = form.querySelector('button[type=submit]'); if(b){ b.disabled = true; }
  return true;
}
</script>
