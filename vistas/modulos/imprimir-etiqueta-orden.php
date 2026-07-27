<?php
function egsPrintH($valor) { return htmlspecialchars((string) $valor, ENT_QUOTES, "UTF-8"); }
function egsPrintValor($valor, $vacio = "—") {
    $valor = trim((string) $valor);
    return $valor !== "" ? $valor : $vacio;
}
function egsPrintFecha($valor) {
    $valor = substr(trim((string) $valor), 0, 10);
    $fecha = DateTime::createFromFormat("Y-m-d", $valor);
    return $fecha ? $fecha->format("d/m/Y") : "—";
}
function egsPrintUrlBase() {
    $https = !empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off";
    $host = isset($_SERVER["HTTP_HOST"]) ? $_SERVER["HTTP_HOST"] : "backend.comercializadoraegs.com";
    $script = isset($_SERVER["SCRIPT_NAME"]) ? $_SERVER["SCRIPT_NAME"] : "/index.php";
    $dir = rtrim(str_replace("\\", "/", dirname($script)), "/");
    if ($dir === "." || $dir === "/") $dir = "";
    return ($https ? "https" : "http") . "://" . $host . $dir;
}
function egsPrintTokenCompacto($tokenHex) {
    $bin = @hex2bin((string) $tokenHex);
    return $bin === false ? "" : rtrim(strtr(base64_encode($bin), "+/", "-_"), "=");
}

$tipoEtiqueta = isset($_GET["tipo"]) ? strtolower(trim((string) $_GET["tipo"])) : "";
$idOrdenEtiqueta = isset($_GET["idOrden"]) ? intval($_GET["idOrden"]) : 0;
$perfilEtiqueta = isset($_SESSION["perfil"]) ? (string) $_SESSION["perfil"] : "";
$empresaSesionEtiqueta = isset($_SESSION["empresa"]) ? intval($_SESSION["empresa"]) : 0;
$errorEtiqueta = "";
$ordenEtiqueta = null;
$garantiaEtiqueta = null;
$urlQrEtiqueta = "";

if (!in_array($tipoEtiqueta, array("contacto", "garantia"), true) || $idOrdenEtiqueta < 1) {
    $errorEtiqueta = "La solicitud de impresión está incompleta.";
} else {
    $filasEtiqueta = controladorOrdenes::ctrMostrarordenesParaValidar("id", $idOrdenEtiqueta);
    $ordenEtiqueta = is_array($filasEtiqueta) && !empty($filasEtiqueta) ? $filasEtiqueta[0] : null;
    if (!is_array($ordenEtiqueta)) {
        $errorEtiqueta = "La orden indicada no existe.";
    } elseif (intval($ordenEtiqueta["id_empresa"]) !== $empresaSesionEtiqueta) {
        $errorEtiqueta = "La orden pertenece a otra empresa.";
    }
}

if ($errorEtiqueta === "" && $tipoEtiqueta === "contacto") {
    if ($perfilEtiqueta !== "administrador") {
        $errorEtiqueta = "Solo un administrador puede generar la etiqueta de identificación.";
    } elseif (stripos((string) $ordenEtiqueta["estado"], "(REV)") === false) {
        $errorEtiqueta = "La etiqueta de identificación solo está disponible mientras la orden está en revisión.";
    } else {
        $tokenOrden = controladorOrdenes::ctrAsegurarTokenCliente($idOrdenEtiqueta);
        if (!preg_match('/^[a-f0-9]{32}$/i', (string) $tokenOrden)) {
            $errorEtiqueta = "No fue posible generar el QR de identificación de la orden.";
        } else {
            $urlQrEtiqueta = egsPrintUrlBase() . "/infoOrden?idOrden=" . $idOrdenEtiqueta . "&g=" . rawurlencode($tokenOrden);
        }
    }
}

if ($errorEtiqueta === "" && $tipoEtiqueta === "garantia") {
    $perfilesSalida = array("administrador", "vendedor", "secretaria");
    if (!in_array($perfilEtiqueta, $perfilesSalida, true)) {
        $errorEtiqueta = "Tu perfil no puede generar etiquetas de garantía.";
    } elseif (stripos((string) $ordenEtiqueta["estado"], "(Ent)") === false) {
        $errorEtiqueta = "La garantía se genera al imprimir el ticket de una orden entregada.";
    } else {
        try {
            $existente = ModeloEtiquetas::mdlGarantiaPorOrden($idOrdenEtiqueta);
            $clienteEtiqueta = ControladorClientes::ctrMostrarClientesOrdenes("id", intval($ordenEtiqueta["id_usuario"]));
            $tecnicoEtiqueta = ControladorTecnicos::ctrMostrarTecnicos("id", intval($ordenEtiqueta["id_tecnico"]));
            $fechaEntrega = !empty($existente["fecha_entrega"]) ? $existente["fecha_entrega"] : substr((string) $ordenEtiqueta["fecha_Salida"], 0, 10);
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaEntrega)) $fechaEntrega = date("Y-m-d");
            $fechaVencimiento = !empty($existente["fecha_vencimiento"]) ? $existente["fecha_vencimiento"] : date("Y-m-d", strtotime($fechaEntrega . " +3 months"));
            $equipoOrden = trim((string) $ordenEtiqueta["marcaDelEquipo"] . " " . (string) $ordenEtiqueta["modeloDelEquipo"]);
            $datosGarantia = array(
                "id_orden" => $idOrdenEtiqueta,
                "id_empresa" => intval($ordenEtiqueta["id_empresa"]),
                "fac_rem" => !empty($existente["fac_rem"]) ? $existente["fac_rem"] : "",
                "tecnico" => !empty($existente["tecnico"]) ? $existente["tecnico"] : (isset($tecnicoEtiqueta["nombre"]) ? $tecnicoEtiqueta["nombre"] : ""),
                "clave_cliente" => !empty($existente["clave_cliente"]) ? $existente["clave_cliente"] : (string) intval($ordenEtiqueta["id_usuario"]),
                "nombre_cliente" => !empty($existente["nombre_cliente"]) ? $existente["nombre_cliente"] : (isset($clienteEtiqueta["nombre"]) ? $clienteEtiqueta["nombre"] : ""),
                "equipo" => !empty($existente["equipo"]) ? $existente["equipo"] : $equipoOrden,
                "numero_serie" => !empty($existente["numero_serie"]) ? $existente["numero_serie"] : $ordenEtiqueta["numeroDeSerieDelEquipo"],
                "fecha_entrega" => $fechaEntrega,
                "fecha_vencimiento" => $fechaVencimiento,
                "proximo_servicio" => !empty($existente["proximo_servicio"]) ? $existente["proximo_servicio"] : "",
                "creado_por" => isset($_SESSION["id"]) ? intval($_SESSION["id"]) : 0
            );
            $tokenGarantia = ModeloEtiquetas::mdlGuardarGarantia($datosGarantia);
            $garantiaEtiqueta = array_merge($datosGarantia, array("token" => $tokenGarantia));
            $urlQrEtiqueta = egsPrintUrlBase() . "/infoOrden?idOrden=" . $idOrdenEtiqueta . "&g=" . rawurlencode(egsPrintTokenCompacto($tokenGarantia));
        } catch (Exception $e) {
            $errorEtiqueta = "No fue posible preparar la garantía: " . $e->getMessage();
        }
    }
}

$configEtiqueta = ModeloEtiquetas::mdlObtenerConfiguracion(is_array($ordenEtiqueta) ? intval($ordenEtiqueta["id_empresa"]) : $empresaSesionEtiqueta);
$clienteContacto = is_array($ordenEtiqueta) ? ControladorClientes::ctrMostrarClientesOrdenes("id", intval($ordenEtiqueta["id_usuario"])) : array();
$equipoContacto = is_array($ordenEtiqueta) ? trim((string) $ordenEtiqueta["marcaDelEquipo"] . " " . (string) $ordenEtiqueta["modeloDelEquipo"]) : "";
$telefonosEtiqueta = array_values(array_filter(array($configEtiqueta["whatsapp"], $configEtiqueta["telefono_1"], $configEtiqueta["telefono_2"], $configEtiqueta["telefono_3"])));
?>

<div class="egs-print-overlay">
<?php if ($errorEtiqueta !== ""): ?>
  <div class="egs-print-error"><i class="fa-solid fa-triangle-exclamation"></i><h2>No se puede imprimir</h2><p><?= egsPrintH($errorEtiqueta) ?></p><button onclick="window.close()">Cerrar</button></div>
<?php else: ?>
  <div class="egs-print-toolbar"><b><?= $tipoEtiqueta === "contacto" ? "Entrada / contacto · 58 × 40 mm" : "Garantía · 58 × 40 mm" ?></b><span>Escala 100% · sin márgenes</span><button type="button" onclick="window.print()"><i class="fa fa-print"></i> Imprimir</button></div>

  <?php if ($tipoEtiqueta === "contacto"): ?>
  <article class="egs-order-label egs-contact-print">
    <i class="egs-triangle top"></i><i class="egs-triangle bottom"></i>
    <section class="egs-contact-copy">
      <header class="egs-contact-header"><span class="egs-brand-mark">EGS</span><div class="egs-order-chip">ENTRADA · ORDEN #<?= intval($ordenEtiqueta["id"]) ?></div></header>
      <b class="egs-client-name"><?= egsPrintH(egsPrintValor(isset($clienteContacto["nombre"]) ? $clienteContacto["nombre"] : "", "CLIENTE")) ?></b>
      <p class="egs-equipment"><?= egsPrintH(egsPrintValor($equipoContacto, "EQUIPO")) ?><?= trim((string) $ordenEtiqueta["numeroDeSerieDelEquipo"]) !== "" ? " · S/N " . egsPrintH($ordenEtiqueta["numeroDeSerieDelEquipo"]) : "" ?></p>
      <b class="egs-contact-title">CONTACTO</b>
      <p class="egs-phones"><?= egsPrintH(implode(" · ", $telefonosEtiqueta)) ?></p>
      <p class="egs-site"><?= egsPrintH(preg_replace('#^https?://#i', '', $configEtiqueta["sitio_web"])) ?></p>
    </section>
    <section class="egs-contact-code"><div class="egs-qr-safe"><div id="egsPrintQr"></div></div><b>ESCANEAR PARA<br>ABRIR ORDEN</b></section>
  </article>
  <?php else: ?>
  <article class="egs-order-label egs-warranty-print">
    <i class="egs-triangle top"></i><i class="egs-triangle bottom"></i>
    <section class="egs-warranty-brand">
      <span class="egs-brand-mark">EGS</span>
      <span class="egs-brand-copy"><b>GARANTÍA DE SERVICIO</b><small><?= egsPrintH(isset($telefonosEtiqueta[0]) ? $telefonosEtiqueta[0] : "") ?></small></span>
    </section>
    <section class="egs-warranty-fields">
      <div><b>ORDEN</b><span>#<?= intval($idOrdenEtiqueta) ?></span></div>
      <div><b>TÉCNICO</b><span><?= egsPrintH(egsPrintValor($garantiaEtiqueta["tecnico"])) ?></span></div>
      <div><b>CLIENTE</b><span><?= egsPrintH(egsPrintValor($garantiaEtiqueta["nombre_cliente"])) ?></span></div>
      <div><b>EQUIPO</b><span><?= egsPrintH(egsPrintValor($garantiaEtiqueta["equipo"])) ?></span></div>
      <div><b>S/N</b><span><?= egsPrintH(egsPrintValor($garantiaEtiqueta["numero_serie"])) ?></span></div>
      <div class="dates"><b>ENTREGA</b><span><?= egsPrintH(egsPrintFecha($garantiaEtiqueta["fecha_entrega"])) ?></span><b>VENCE</b><span><?= egsPrintH(egsPrintFecha($garantiaEtiqueta["fecha_vencimiento"])) ?></span></div>
      <div class="seal"><b>SELLO ALTERADO</b><span>SIN GARANTÍA</span></div>
    </section>
    <section class="egs-warranty-code"><div class="egs-qr-safe"><div id="egsPrintQr"></div></div><b>VALIDAR<br>GARANTÍA</b></section>
  </article>
  <?php endif; ?>
<?php endif; ?>
</div>

<style>
.egs-print-overlay{position:fixed;inset:0;z-index:100000;background:#eef2f7;display:flex;align-items:center;justify-content:center;font-family:Arial,sans-serif;color:#050505}.egs-print-toolbar{position:fixed;top:18px;left:50%;transform:translateX(-50%);display:flex;align-items:center;gap:14px;padding:9px 12px;background:#fff;border:1px solid #dbe3ec;border-radius:10px;box-shadow:0 5px 18px rgba(15,23,42,.12);font-size:12px}.egs-print-toolbar span{color:#64748b}.egs-print-toolbar button,.egs-print-error button{border:0;border-radius:7px;background:#166534;color:#fff;padding:7px 12px;font-weight:700}.egs-print-error{max-width:430px;padding:30px;text-align:center;background:#fff;border-radius:16px;box-shadow:0 10px 35px rgba(15,23,42,.12)}.egs-print-error i{font-size:32px;color:#dc2626}.egs-order-label{position:relative;box-sizing:border-box;background:#fff;overflow:hidden;box-shadow:0 8px 28px rgba(15,23,42,.2)}.egs-order-label *{box-sizing:border-box}.egs-order-label .egs-triangle{position:absolute;width:0;height:0;z-index:4}.egs-order-label .egs-triangle.top{left:0;top:0;border-top:5mm solid #15803d;border-right:5mm solid transparent}.egs-order-label .egs-triangle.bottom{right:0;bottom:0;border-bottom:5mm solid #15803d;border-left:5mm solid transparent}.egs-order-label img{display:block;object-fit:contain;object-position:left center}.egs-contact-print{width:58mm;height:40mm;padding:1.8mm;display:grid;grid-template-columns:22mm 14mm;gap:1mm}.egs-contact-copy{min-width:0;padding-left:.3mm}.egs-contact-copy img{width:14mm;height:4.5mm;margin-left:4.2mm;margin-bottom:.4mm}.egs-order-chip{font-size:1.45mm;line-height:1.2;font-weight:900;color:#166534;letter-spacing:.08mm;white-space:nowrap}.egs-client-name{display:block;font-size:2.15mm;line-height:1.05;margin-top:.6mm;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.egs-equipment{font-size:1.55mm;line-height:1.15;height:3.5mm;margin:.4mm 0 .5mm;overflow:hidden}.egs-contact-title{display:block;font-size:1.8mm;line-height:1}.egs-phones{font-size:1.5mm;line-height:1.18;font-weight:800;margin:.35mm 0;max-height:3.7mm;overflow:hidden}.egs-site{font-size:1.35mm;line-height:1.1;font-weight:700;margin:0;white-space:nowrap;overflow:hidden}.egs-contact-code{border-left:.45mm solid #15803d;padding-left:1mm;display:flex;flex-direction:column;align-items:center;justify-content:center}.egs-contact-code #egsPrintQr,.egs-contact-code #egsPrintQr img,.egs-contact-code #egsPrintQr canvas{width:13.2mm!important;height:13.2mm!important}.egs-contact-code b,.egs-warranty-code b{text-align:center;font-size:1.3mm;line-height:1.05;color:#166534;margin-top:.4mm}.egs-warranty-print{width:58mm;height:40mm;padding:2mm;display:grid;grid-template-columns:17mm 25mm 16mm;gap:1mm}.egs-warranty-brand{border-right:.55mm solid #15803d;padding-right:1mm;min-width:0}.egs-warranty-brand img{width:13.5mm;height:5mm;margin:0 0 .4mm 1.8mm}.egs-warranty-brand>b{display:block;font-size:1.75mm;line-height:1.05}.egs-warranty-brand small{display:block;font-size:1.2mm;line-height:1.15;margin:.35mm 0 1.2mm}.egs-warranty-brand strong{display:block;font-size:1.8mm;line-height:1}.egs-warranty-brand p{font-size:1.35mm;line-height:1.22;font-weight:800;margin:.5mm 0;max-height:5mm;overflow:hidden}.egs-warranty-brand .web{font-size:1.1mm;font-weight:700;white-space:nowrap;overflow:hidden}.egs-warranty-fields{padding-top:.4mm;min-width:0}.egs-warranty-fields>div{display:grid;grid-template-columns:7mm 1fr;align-items:end;min-height:3.3mm;border-bottom:.25mm solid #555;gap:.5mm}.egs-warranty-fields>div b{font-size:1.55mm;line-height:1.1}.egs-warranty-fields>div span{font-size:1.45mm;line-height:1.1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.egs-warranty-fields>div:first-child{grid-template-columns:5mm 6mm 6mm 1fr}.egs-warranty-fields .dates{grid-template-columns:6mm 7mm 5mm 1fr}.egs-warranty-fields .seal{display:flex;align-items:center;justify-content:space-between;border:0;background:#15803d;color:#fff;min-height:5.4mm;margin-top:1mm;padding:.7mm 1mm}.egs-warranty-fields .seal b{font-size:1.8mm}.egs-warranty-fields .seal span{font-size:2.1mm;font-weight:900}.egs-warranty-code{display:flex;flex-direction:column;align-items:center;justify-content:center}.egs-warranty-code #egsPrintQr,.egs-warranty-code #egsPrintQr img,.egs-warranty-code #egsPrintQr canvas{width:15.5mm!important;height:15.5mm!important}.egs-warranty-code b{font-size:1.6mm;margin-top:.8mm}.egs-print-error{display:block}@media print{html,body{margin:0!important;padding:0!important;background:#fff!important}.wrapper,.main-header,.main-sidebar,.content-wrapper,.main-footer{visibility:hidden!important}.egs-print-overlay,.egs-print-overlay *{visibility:visible!important}.egs-print-overlay{position:fixed;inset:0;background:#fff;display:block}.egs-print-toolbar{display:none}.egs-order-label{position:absolute;left:0;top:0;box-shadow:none}}
.egs-contact-print{grid-template-columns:minmax(0,1fr) 16mm}.egs-warranty-print{grid-template-columns:15mm minmax(0,1fr) 17mm}.egs-warranty-brand img{margin-left:.4mm}.egs-contact-code #egsPrintQr{width:14mm!important;height:14mm!important;padding:1mm;box-sizing:content-box!important;background:#fff}.egs-contact-code #egsPrintQr img,.egs-contact-code #egsPrintQr canvas{width:14mm!important;height:14mm!important}.egs-warranty-code #egsPrintQr{width:15mm!important;height:15mm!important;padding:1mm;box-sizing:content-box!important;background:#fff}.egs-warranty-code #egsPrintQr img,.egs-warranty-code #egsPrintQr canvas{width:15mm!important;height:15mm!important}
.egs-contact-print{grid-template-columns:minmax(0,1fr) 17mm}
@page{size:58mm 40mm;margin:0}
</style>

<style id="egsLabelSafeAreas">
/* Zonas seguras: ningún texto puede invadir el QR ni salir de la etiqueta. */
.egs-contact-copy,.egs-contact-code,.egs-warranty-brand,.egs-warranty-fields,.egs-warranty-code{min-width:0;overflow:hidden}
.egs-contact-copy>*{max-width:100%}
.egs-contact-copy img{width:13.5mm;height:4.4mm;margin-left:3.8mm}
.egs-order-chip{font-size:1.45mm;overflow:hidden;text-overflow:ellipsis}
.egs-client-name{font-size:2mm;overflow:hidden;text-overflow:ellipsis}
.egs-equipment{font-size:1.42mm;overflow:hidden;overflow-wrap:anywhere}
.egs-phones{font-size:1.34mm;line-height:1.16;max-height:4.1mm;overflow:hidden;overflow-wrap:anywhere}
.egs-site{font-size:1.15mm;overflow:hidden;text-overflow:ellipsis}
.egs-contact-code{padding-left:.55mm}
.egs-contact-code b{max-width:14mm;font-size:1.15mm;overflow:hidden}
.egs-warranty-brand img{width:11mm;height:4.5mm;margin-left:3.5mm}
.egs-warranty-brand>b,.egs-warranty-brand small,.egs-warranty-brand strong,.egs-warranty-brand p{max-width:100%;overflow:hidden;overflow-wrap:anywhere}
.egs-warranty-fields>div{min-width:0;overflow:hidden}
.egs-warranty-fields>div>b,.egs-warranty-fields>div>span{min-width:0;max-width:100%;overflow:hidden;text-overflow:ellipsis}
.egs-warranty-fields>div:first-child{grid-template-columns:7mm minmax(0,1fr)}
.egs-warranty-fields .dates{grid-template-columns:10mm minmax(0,1fr);grid-template-rows:repeat(2,3.15mm);min-height:6.55mm;align-items:center;column-gap:.7mm}
.egs-warranty-fields .dates b{font-size:1.35mm;line-height:1;white-space:nowrap;overflow:visible;text-overflow:clip}
.egs-warranty-fields .dates span{font-size:1.55mm;line-height:1;font-weight:800;white-space:nowrap;overflow:visible;text-overflow:clip}
.egs-warranty-fields .seal{flex-direction:column;justify-content:center;gap:.15mm;text-align:center;padding:.45mm .7mm}
.egs-warranty-fields .seal b{font-size:1.5mm;line-height:1}
.egs-warranty-fields .seal span{font-size:2.2mm;line-height:1;font-weight:900}
.egs-warranty-code{padding-bottom:2mm}
.egs-warranty-code b{max-width:15mm;overflow:hidden}
</style>

<style id="egsThermalLabel58x40">
/* Salida térmica monocromática para etiqueta física de 58 × 40 mm. */
.egs-order-label{width:58mm!important;height:40mm!important;padding:2mm!important;color:#000!important;background:#fff!important;font-family:Arial,Helvetica,sans-serif!important;font-synthesis:none;print-color-adjust:exact;-webkit-print-color-adjust:exact}
.egs-order-label .egs-triangle{display:none!important}
.egs-brand-mark{display:inline-flex;align-items:center;justify-content:center;flex:none;min-width:8mm;height:5.5mm;border:.45mm solid #000;border-radius:.6mm;font-size:8pt;line-height:1;font-weight:900;letter-spacing:.1mm;color:#000;background:#fff}
.egs-contact-print{display:grid!important;grid-template-columns:minmax(0,1fr) 22.2mm!important;gap:0!important}
.egs-contact-copy{min-width:0!important;padding:0 1.3mm 0 0!important;border-right:.35mm solid #000!important;overflow:hidden!important}
.egs-contact-header{display:flex;align-items:center;gap:1.1mm;height:7mm;border-bottom:.35mm solid #000}
.egs-order-chip{font-size:6.4pt!important;line-height:1.05!important;font-weight:900!important;color:#000!important;letter-spacing:0!important;white-space:normal!important}
.egs-client-name{display:block!important;margin:1.2mm 0 .6mm!important;font-size:8pt!important;line-height:1.08!important;font-weight:900!important;white-space:nowrap!important;overflow:hidden!important;text-overflow:ellipsis!important}
.egs-equipment{height:8mm!important;margin:0 0 1mm!important;font-size:6.2pt!important;line-height:1.18!important;font-weight:700!important;overflow:hidden!important;overflow-wrap:anywhere}
.egs-contact-title{display:block!important;font-size:6pt!important;line-height:1!important;font-weight:900!important}
.egs-phones{max-height:6mm!important;margin:.55mm 0!important;font-size:6.5pt!important;line-height:1.15!important;font-weight:900!important;overflow:hidden!important;overflow-wrap:anywhere}
.egs-site{margin:0!important;padding-top:.6mm!important;border-top:.3mm solid #000!important;font-size:5.7pt!important;line-height:1.1!important;font-weight:800!important;white-space:nowrap!important;overflow:hidden!important;text-overflow:ellipsis!important}
.egs-contact-code,.egs-warranty-code{display:flex!important;align-items:center!important;justify-content:center!important;flex-direction:column!important;padding:0!important;border:0!important;color:#000!important}
.egs-qr-safe{display:flex;flex:0 0 22.2mm;width:22.2mm;height:22.2mm;align-items:center;justify-content:center;background:#fff}
.egs-contact-code #egsPrintQr,.egs-warranty-code #egsPrintQr{width:22.2mm!important;height:22.2mm!important;padding:0!important;background:#fff!important;box-sizing:border-box!important}
.egs-contact-code #egsPrintQr svg,.egs-warranty-code #egsPrintQr svg{display:block!important;width:22.2mm!important;height:22.2mm!important;shape-rendering:crispEdges}
.egs-contact-code #egsPrintQr canvas,.egs-warranty-code #egsPrintQr canvas{display:block!important;width:18.8mm!important;height:18.8mm!important;margin:1.7mm!important;image-rendering:pixelated}
.egs-contact-code #egsPrintQr img,.egs-warranty-code #egsPrintQr img{display:none!important}
.egs-contact-code b,.egs-warranty-code b{max-width:22.2mm!important;margin:.8mm 0 0!important;overflow:visible!important;text-overflow:clip!important;color:#000!important;font-size:4.8pt!important;line-height:1.08!important;font-weight:900!important;text-align:center!important;white-space:normal!important}
.egs-warranty-print{display:grid!important;grid-template-columns:minmax(0,1fr) 22.2mm!important;grid-template-rows:7mm minmax(0,1fr)!important;column-gap:0!important}
.egs-warranty-brand{grid-column:1!important;grid-row:1!important;display:flex!important;flex-direction:row!important;align-items:center!important;gap:.6mm!important;min-width:0!important;padding:0!important;border:0!important;border-bottom:.35mm solid #000!important;overflow:hidden!important}
.egs-brand-copy{display:flex;flex-direction:column;min-width:0}
.egs-brand-copy b{font-size:5pt!important;line-height:1.05!important;font-weight:900!important;letter-spacing:-.07mm!important;white-space:nowrap!important;overflow:hidden!important;text-overflow:clip!important}
.egs-brand-copy small{font-size:5pt!important;line-height:1.1!important;font-weight:700!important;white-space:nowrap!important;overflow:hidden!important;text-overflow:clip!important}
.egs-warranty-fields{grid-column:1!important;grid-row:2!important;min-width:0!important;padding:.7mm 0 0!important;overflow:hidden!important}
.egs-warranty-fields>div{display:grid!important;grid-template-columns:9mm minmax(0,1fr)!important;align-items:center!important;min-height:3.2mm!important;height:3.2mm!important;gap:1mm!important;border-bottom:.25mm solid #000!important}
.egs-warranty-fields>div:first-child{grid-template-columns:9mm minmax(0,1fr)!important}
.egs-warranty-fields>div b{font-size:5.1pt!important;line-height:1!important;font-weight:900!important;letter-spacing:-.04mm!important;white-space:nowrap!important}
.egs-warranty-fields>div span{font-size:5.4pt!important;line-height:1.05!important;font-weight:700!important;white-space:nowrap!important;overflow:hidden!important;text-overflow:ellipsis!important}
.egs-warranty-fields .dates{display:grid!important;grid-template-columns:9mm minmax(0,1fr)!important;grid-template-rows:2.9mm 2.9mm!important;height:5.8mm!important;min-height:5.8mm!important;gap:0 1mm!important}
.egs-warranty-fields .dates span{padding-left:.4mm!important}
.egs-warranty-fields .seal{display:flex!important;height:4.3mm!important;min-height:4.3mm!important;flex-direction:row!important;align-items:center!important;justify-content:center!important;gap:.7mm!important;margin:.45mm 0 0!important;padding:.35mm .6mm!important;border:0!important;background:#000!important;color:#fff!important;text-align:center!important}
.egs-warranty-fields .seal b,.egs-warranty-fields .seal span{max-width:none!important;overflow:visible!important;text-overflow:clip!important;font-size:4.8pt!important;line-height:1!important;font-weight:900!important;letter-spacing:-.03mm!important;color:#fff!important;white-space:nowrap!important}
.egs-warranty-code{grid-column:2!important;grid-row:1/3!important;padding:0!important}
@media print{html,body{width:58mm!important;height:40mm!important;overflow:hidden!important}.egs-order-label{left:0!important;top:0!important}}
</style>

<?php if ($errorEtiqueta === ""): ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
(function(){
  var box=document.getElementById('egsPrintQr');
  var url=<?= json_encode($urlQrEtiqueta) ?>;
  if(box && url && typeof QRCode!=='undefined'){
    var qr=new QRCode(box,{text:url,width:320,height:320,colorDark:'#000000',colorLight:'#ffffff',correctLevel:QRCode.CorrectLevel.M});
    try{
      var model=qr._oQRCode, count=model&&model.getModuleCount?model.getModuleCount():0;
      if(count){
        var quiet=4,total=count+(quiet*2),commands=[];
        for(var row=0;row<count;row++){
          for(var column=0;column<count;column++){
            if(model.isDark(row,column))commands.push('M'+(column+quiet)+' '+(row+quiet)+'h1v1h-1z');
          }
        }
        var ns='http://www.w3.org/2000/svg',svg=document.createElementNS(ns,'svg');
        svg.setAttribute('viewBox','0 0 '+total+' '+total);
        svg.setAttribute('preserveAspectRatio','xMidYMid meet');
        svg.setAttribute('shape-rendering','crispEdges');
        svg.setAttribute('aria-label','Código QR');
        var background=document.createElementNS(ns,'rect');
        background.setAttribute('width',total);background.setAttribute('height',total);background.setAttribute('fill','#fff');
        var modules=document.createElementNS(ns,'path');
        modules.setAttribute('d',commands.join(''));modules.setAttribute('fill','#000');
        svg.appendChild(background);svg.appendChild(modules);
        box.innerHTML='';box.appendChild(svg);
      }
    }catch(error){
      /* Conserva el canvas de qrcodejs como respaldo. */
    }
    window.setTimeout(function(){ window.focus(); window.print(); },650);
  }
})();
</script>
<?php endif; ?>
