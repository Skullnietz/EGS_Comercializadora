<?php
if ($_SESSION["perfil"] !== "administrador" && $_SESSION["perfil"] !== "Super-Administrador") {
    echo '<script>window.location="index.php?ruta=inicio";</script>';
    return;
}

if (empty($_SESSION["csrf_etiquetas"])) {
    $_SESSION["csrf_etiquetas"] = bin2hex(random_bytes(24));
}

function egsEtiquetaTexto($valor, $maximo)
{
    $valor = trim(strip_tags((string) $valor));
    if (function_exists("mb_substr")) return mb_substr($valor, 0, $maximo, "UTF-8");
    return substr($valor, 0, $maximo);
}

function egsEtiquetaFecha($valor, $obligatoria)
{
    $valor = trim((string) $valor);
    if ($valor === "" && !$obligatoria) return "";
    $fecha = DateTime::createFromFormat("Y-m-d", $valor);
    return ($fecha && $fecha->format("Y-m-d") === $valor) ? $valor : "";
}

$mensaje = "";
$tipoMensaje = "success";
$tokenGenerado = "";
$config = ModeloEtiquetas::mdlObtenerConfiguracion(intval($_SESSION["empresa"]));

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $csrf = isset($_POST["csrf_etiquetas"]) ? (string) $_POST["csrf_etiquetas"] : "";
        if (!hash_equals($_SESSION["csrf_etiquetas"], $csrf)) {
            throw new RuntimeException("La sesión del formulario venció. Recarga la página e inténtalo de nuevo.");
        }

        $accion = isset($_POST["accion_etiqueta"]) ? $_POST["accion_etiqueta"] : "";
        if ($accion === "guardar_contacto") {
            $datosConfig = array(
                "id_empresa" => intval($_SESSION["empresa"]),
                "nombre_comercial" => egsEtiquetaTexto($_POST["nombre_comercial"], 120),
                "lema" => egsEtiquetaTexto($_POST["lema"], 180),
                "direccion" => egsEtiquetaTexto($_POST["direccion"], 300),
                "whatsapp" => egsEtiquetaTexto($_POST["whatsapp"], 30),
                "telefono_1" => egsEtiquetaTexto($_POST["telefono_1"], 30),
                "telefono_2" => egsEtiquetaTexto($_POST["telefono_2"], 30),
                "telefono_3" => egsEtiquetaTexto($_POST["telefono_3"], 30),
                "sitio_web" => egsEtiquetaTexto($_POST["sitio_web"], 180),
                "actualizado_por" => isset($_SESSION["id"]) ? intval($_SESSION["id"]) : 0
            );
            if (!preg_match('#^https?://#i', $datosConfig["sitio_web"]) || filter_var($datosConfig["sitio_web"], FILTER_VALIDATE_URL) === false) {
                throw new RuntimeException("El sitio web debe ser una URL completa que comience con http:// o https://.");
            }
            if ($datosConfig["nombre_comercial"] === "" || $datosConfig["direccion"] === "") {
                throw new RuntimeException("El nombre comercial y la dirección son obligatorios.");
            }
            ModeloEtiquetas::mdlGuardarConfiguracion($datosConfig);
            $config = ModeloEtiquetas::mdlObtenerConfiguracion(intval($_SESSION["empresa"]));
            $mensaje = "Los datos de contacto quedaron guardados.";
        }

        if ($accion === "guardar_garantia") {
            $entrega = egsEtiquetaFecha($_POST["fecha_entrega"], true);
            $vencimiento = egsEtiquetaFecha($_POST["fecha_vencimiento"], true);
            $servicio = egsEtiquetaFecha($_POST["proximo_servicio"], false);
            if ($entrega === "" || $vencimiento === "") {
                throw new RuntimeException("Indica fechas válidas de entrega y vencimiento.");
            }
            if ($vencimiento < $entrega) {
                throw new RuntimeException("El vencimiento no puede ser anterior a la entrega.");
            }
            $datosGarantia = array(
                "id_orden" => intval($_POST["id_orden"]),
                "id_empresa" => intval($_SESSION["empresa"]),
                "fac_rem" => egsEtiquetaTexto($_POST["fac_rem"], 80),
                "tecnico" => egsEtiquetaTexto($_POST["tecnico"], 160),
                "clave_cliente" => egsEtiquetaTexto($_POST["clave_cliente"], 100),
                "nombre_cliente" => egsEtiquetaTexto($_POST["nombre_cliente"], 180),
                "equipo" => egsEtiquetaTexto($_POST["equipo"], 220),
                "numero_serie" => egsEtiquetaTexto($_POST["numero_serie"], 160),
                "fecha_entrega" => $entrega,
                "fecha_vencimiento" => $vencimiento,
                "proximo_servicio" => $servicio,
                "creado_por" => isset($_SESSION["id"]) ? intval($_SESSION["id"]) : 0
            );
            if ($datosGarantia["id_orden"] < 1) {
                throw new RuntimeException("Selecciona una orden para generar una garantía validable.");
            }
            $tokenGenerado = ModeloEtiquetas::mdlGuardarGarantia($datosGarantia);
            $mensaje = "Garantía vinculada y QR actualizado. Ya puedes imprimir la etiqueta.";
        }
    } catch (Exception $e) {
        $mensaje = $e->getMessage();
        $tipoMensaje = "danger";
    }
}

$ordenesEtiqueta = ModeloEtiquetas::mdlOrdenesRecientes(intval($_SESSION["empresa"]), 200);
$esHttps = (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off");
$esquema = $esHttps ? "https" : "http";
$host = isset($_SERVER["HTTP_HOST"]) ? $_SERVER["HTTP_HOST"] : "backend.egsequipodecomputo.com";
$script = isset($_SERVER["SCRIPT_NAME"]) ? $_SERVER["SCRIPT_NAME"] : "/index.php";
$directorio = rtrim(str_replace("\\", "/", dirname($script)), "/");
if ($directorio === "." || $directorio === "/") $directorio = "";
$urlOrdenQrBase = $esquema . "://" . $host . $directorio . "/infoOrden?idOrden=";

function egsH($valor) { return htmlspecialchars((string) $valor, ENT_QUOTES, "UTF-8"); }
function egsSitioVisible($valor) {
    return preg_replace('#^https?://#i', '', rtrim((string) $valor, '/'));
}
?>

<div class="content-wrapper egs-label-page">
  <section class="content-header">
    <h1>Etiquetas <small>Contacto y garantía</small></h1>
    <ol class="breadcrumb"><li><a href="index.php?ruta=inicio"><i class="fa fa-home"></i> Inicio</a></li><li class="active">Etiquetas</li></ol>
  </section>

  <section class="content">
    <?php if ($mensaje !== ""): ?>
      <div class="alert alert-<?= egsH($tipoMensaje) ?> alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button><?= egsH($mensaje) ?></div>
    <?php endif; ?>

    <div class="egs-label-intro">
      <div class="egs-label-intro-icon"><i class="fa-solid fa-tags"></i></div>
      <div><h2>Generador de etiquetas</h2><p>Selecciona papel de 58 × 40 mm e imprime a escala 100%, sin márgenes ni encabezados. Para garantía recomendamos vincular una orden y generar su QR.</p></div>
      <span><i class="fa-solid fa-ruler-combined"></i> Medidas exactas</span>
    </div>

    <form method="post" id="formEtiquetas" autocomplete="off">
      <input type="hidden" name="csrf_etiquetas" value="<?= egsH($_SESSION["csrf_etiquetas"]) ?>">
      <div class="row">
        <div class="col-lg-5">
          <div class="box box-success egs-editor-box">
            <div class="box-header"><h3 class="box-title"><i class="fa-solid fa-address-card"></i> Datos de contacto</h3><span class="egs-size-pill">58 × 40 mm</span></div>
            <div class="box-body">
              <p class="help-block egs-help">Estos datos se conservan para las siguientes impresiones.</p>
              <div class="row">
                <div class="col-sm-7 form-group"><label>Nombre comercial</label><input class="form-control egs-live" name="nombre_comercial" data-target="contactName" value="<?= egsH($config["nombre_comercial"]) ?>" maxlength="120" required></div>
                <div class="col-sm-5 form-group"><label>Lema</label><input class="form-control egs-live" name="lema" data-target="contactTagline" value="<?= egsH($config["lema"]) ?>" maxlength="180"></div>
              </div>
              <div class="form-group"><label>Dirección</label><textarea class="form-control egs-live" name="direccion" data-target="contactAddress" rows="2" maxlength="300" required><?= egsH($config["direccion"]) ?></textarea></div>
              <div class="row">
                <div class="col-sm-6 form-group"><label>WhatsApp</label><input class="form-control egs-phone" name="whatsapp" value="<?= egsH($config["whatsapp"]) ?>" maxlength="30"></div>
                <div class="col-sm-6 form-group"><label>Teléfono 1</label><input class="form-control egs-phone" name="telefono_1" value="<?= egsH($config["telefono_1"]) ?>" maxlength="30"></div>
                <div class="col-sm-6 form-group"><label>Teléfono 2</label><input class="form-control egs-phone" name="telefono_2" value="<?= egsH($config["telefono_2"]) ?>" maxlength="30"></div>
                <div class="col-sm-6 form-group"><label>Teléfono 3</label><input class="form-control egs-phone" name="telefono_3" value="<?= egsH($config["telefono_3"]) ?>" maxlength="30"></div>
              </div>
              <div class="form-group"><label>Sitio web</label><input class="form-control egs-live" name="sitio_web" data-target="contactWebsite" value="<?= egsH($config["sitio_web"]) ?>" maxlength="180"></div>
              <div class="egs-actions"><button class="btn btn-default" type="submit" name="accion_etiqueta" value="guardar_contacto"><i class="fa fa-save"></i> Guardar datos</button><button class="btn btn-success" type="button" onclick="egsPrintLabel('contact')"><i class="fa fa-print"></i> Imprimir contacto</button></div>
            </div>
          </div>

          <div class="box box-success egs-editor-box">
            <div class="box-header"><h3 class="box-title"><i class="fa-solid fa-shield-halved"></i> Datos de garantía</h3><span class="egs-size-pill">58 × 40 mm</span></div>
            <div class="box-body">
              <div class="egs-mode-choice">
                <label class="active"><input type="radio" name="modo_etiqueta" value="orden" checked> <span><b>Con orden vinculada</b><small>Recomendado · incluye QR de validación</small></span></label>
                <label><input type="radio" name="modo_etiqueta" value="vacia"> <span><b>Vacía</b><small>Para llenar a mano, sin QR</small></span></label>
              </div>
              <div class="form-group" id="orderPicker"><label>Orden</label><select class="form-control" name="id_orden" id="idOrdenEtiqueta"><option value="">Selecciona una orden…</option><?php foreach ($ordenesEtiqueta as $orden): ?><option value="<?= intval($orden["id"]) ?>">#<?= intval($orden["id"]) ?> · <?= egsH(trim($orden["cliente_nombre"] . " · " . $orden["marcaDelEquipo"] . " " . $orden["modeloDelEquipo"], " ·")) ?></option><?php endforeach; ?></select><small class="help-block">Se muestran las 200 órdenes más recientes de tu empresa.</small></div>
              <div class="row">
                <div class="col-sm-6 form-group"><label>Orden</label><input class="form-control" id="ordenVisual" readonly placeholder="Automático"></div>
                <input type="hidden" name="fac_rem" value="">
                <div class="col-sm-6 form-group"><label>Técnico</label><input class="form-control egs-warranty-live" name="tecnico" data-target="wTech" maxlength="160"></div>
                <div class="col-sm-6 form-group"><label>Clave del cliente</label><input class="form-control egs-warranty-live" name="clave_cliente" data-target="wKey" maxlength="100"></div>
                <div class="col-sm-12 form-group"><label>Nombre del cliente</label><input class="form-control egs-warranty-live" name="nombre_cliente" data-target="wClient" maxlength="180"></div>
                <div class="col-sm-7 form-group"><label>Equipo</label><input class="form-control egs-warranty-live" name="equipo" data-target="wEquipment" maxlength="220"></div>
                <div class="col-sm-5 form-group"><label>Núm. de serie</label><input class="form-control egs-warranty-live" name="numero_serie" data-target="wSerial" maxlength="160"></div>
                <div class="col-sm-5 form-group"><label>Fecha de entrega</label><input type="date" class="form-control egs-warranty-live" name="fecha_entrega" data-target="wDelivery" required></div>
                <div class="col-sm-2 form-group"><label>Meses</label><input type="number" class="form-control" id="mesesGarantia" value="3" min="1" max="60"></div>
                <div class="col-sm-5 form-group"><label>Vencimiento</label><input type="date" class="form-control egs-warranty-live" name="fecha_vencimiento" data-target="wExpiry" required></div>
                <div class="col-sm-6 form-group"><label>Próximo servicio</label><input type="date" class="form-control egs-warranty-live" name="proximo_servicio" data-target="wService"></div>
              </div>
              <div class="egs-qr-url" id="qrUrlBox"><i class="fa-solid fa-link"></i><span id="qrUrlText">El enlace aparecerá al guardar la orden.</span></div>
              <div class="egs-actions"><button class="btn btn-success" id="saveWarranty" type="submit" name="accion_etiqueta" value="guardar_garantia"><i class="fa-solid fa-qrcode"></i> Guardar y generar QR</button><button class="btn btn-primary" type="button" onclick="egsPrintLabel('warranty')"><i class="fa fa-print"></i> Imprimir garantía</button></div>
            </div>
          </div>
        </div>

        <div class="col-lg-7">
          <div class="egs-preview-panel">
            <div class="egs-preview-heading"><div><h3>Vista previa</h3><p>La impresión sale en las medidas indicadas, aunque aquí se amplía para facilitar la revisión.</p></div><span><i class="fa fa-circle"></i> Actualización en vivo</span></div>

            <div class="egs-preview-section"><div class="egs-preview-title"><b>Etiqueta de entrada / contacto</b><span>58 × 40 mm</span></div><div class="egs-label-stage contact-stage">
              <div class="egs-print-label egs-contact-label" id="contactLabel">
                <i class="egs-corner egs-corner-tl"></i><i class="egs-corner egs-corner-br"></i>
                <div class="egs-contact-brand"><span class="egs-brand-mark">EGS</span><span class="egs-brand-copy"><b id="contactName"><?= egsH($config["nombre_comercial"]) ?></b><small id="contactTagline"><?= egsH($config["lema"]) ?></small></span></div>
                <div class="egs-contact-info"><b>DIRECCIÓN</b><p id="contactAddress"><?= egsH($config["direccion"]) ?></p><b>CONTACTO</b><p class="egs-phone-line" id="contactPhones"></p><p class="egs-site-line"><span id="contactWebsite"><?= egsH(egsSitioVisible($config["sitio_web"])) ?></span></p></div>
                <div id="contactQr" class="egs-contact-qr is-empty"><span>ORDEN <b id="contactOrder"></b></span><div class="egs-qr-safe"><div id="contactQrCode" class="egs-qr-code egs-contact-qr-code"></div></div><small>ABRIR ORDEN</small></div>
              </div>
            </div></div>

            <div class="egs-preview-section"><div class="egs-preview-title"><b>Etiqueta de garantía</b><span>58 × 40 mm</span></div><div class="egs-label-stage warranty-stage">
              <div class="egs-print-label egs-warranty-label" id="warrantyLabel">
                <i class="egs-corner egs-corner-tl"></i><i class="egs-corner egs-corner-br"></i>
                <div class="egs-warranty-contact"><span class="egs-brand-mark">EGS</span><span class="egs-brand-copy"><b>GARANTÍA DE SERVICIO</b><small class="wc-phones"></small></span></div>
                <div class="egs-warranty-data">
                  <div class="egs-w-row"><b>ORDEN</b><span id="wOrder"></span></div>
                  <div class="egs-w-row"><b>TÉCNICO</b><span id="wTech"></span></div>
                  <div class="egs-w-row"><b>CLIENTE</b><span id="wClient"></span></div>
                  <div class="egs-w-row"><b>EQUIPO</b><span id="wEquipment"></span></div>
                  <div class="egs-w-row"><b>S/N</b><span id="wSerial"></span></div>
                  <div class="egs-w-row egs-date-row"><b>ENTREGA</b><span id="wDelivery"></span><b>VENCE</b><span id="wExpiry"></span></div>
                  <div class="egs-validity-strip"><b>SELLO ALTERADO</b><span>SIN GARANTÍA</span></div>
                </div>
                <div id="warrantyQr" class="egs-label-qr is-empty"><span>ORDEN <b id="wWarrantyOrder"></b></span><div class="egs-qr-safe"><div id="warrantyQrCode" class="egs-qr-code"></div></div><small>VALIDAR<br>GARANTÍA</small></div>
              </div>
            </div></div>
          </div>
        </div>
      </div>
    </form>
  </section>
</div>

<style id="egsLabelStyles">
.egs-label-page{background:#f7f9fc}.egs-label-intro{display:flex;align-items:center;gap:15px;background:linear-gradient(115deg,#f0fdf4,#fff);border:1px solid #bbf7d0;border-radius:14px;padding:16px 20px;margin-bottom:18px;box-shadow:0 2px 12px rgba(22,101,52,.06)}.egs-label-intro-icon{width:48px;height:48px;border-radius:12px;background:#166534;color:#fff;display:flex;align-items:center;justify-content:center;font-size:20px}.egs-label-intro h2{font-size:18px;margin:0 0 3px;font-weight:800}.egs-label-intro p{margin:0;color:#64748b}.egs-label-intro>span{margin-left:auto;color:#166534;background:#dcfce7;border-radius:999px;padding:6px 12px;font-size:11px;font-weight:700;white-space:nowrap}.egs-editor-box{border-radius:12px;border-top:3px solid #15803d;box-shadow:0 2px 10px rgba(15,23,42,.06);overflow:hidden}.egs-editor-box .box-header{padding:13px 15px;border-bottom:1px solid #eef2f7}.egs-editor-box .box-title{font-size:15px;font-weight:700}.egs-editor-box .box-title i{color:#15803d;margin-right:7px}.egs-size-pill{float:right;background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;border-radius:999px;padding:3px 9px;font-size:11px;font-weight:700}.egs-help{margin-top:0}.egs-actions{display:flex;justify-content:flex-end;gap:8px;padding-top:5px;border-top:1px solid #f1f5f9}.egs-mode-choice{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:15px}.egs-mode-choice label{display:flex;align-items:flex-start;gap:7px;border:1px solid #dbe3ec;border-radius:10px;padding:10px;cursor:pointer;background:#fff}.egs-mode-choice label.active{border-color:#22c55e;background:#f0fdf4;box-shadow:0 0 0 1px #22c55e}.egs-mode-choice b,.egs-mode-choice small{display:block}.egs-mode-choice small{font-weight:400;color:#64748b;font-size:10px;margin-top:2px}.egs-qr-url{display:flex;gap:8px;align-items:center;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:8px 10px;margin-bottom:10px;font-size:10px;color:#64748b;word-break:break-all}.egs-qr-url i{color:#15803d}.egs-preview-panel{position:sticky;top:12px;background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:18px;box-shadow:0 4px 20px rgba(15,23,42,.07)}.egs-preview-heading{display:flex;justify-content:space-between;border-bottom:1px solid #edf1f5;padding-bottom:12px}.egs-preview-heading h3{font-size:16px;font-weight:800;margin:0 0 2px}.egs-preview-heading p{font-size:11px;color:#94a3b8;margin:0}.egs-preview-heading>span{font-size:10px;color:#16a34a}.egs-preview-heading .fa-circle{font-size:6px}.egs-preview-section{margin-top:18px}.egs-preview-title{display:flex;justify-content:space-between;margin-bottom:8px;font-size:12px;color:#475569}.egs-preview-title span{font-size:10px;color:#15803d;font-weight:700}.egs-label-stage{display:flex;align-items:center;justify-content:center;min-height:220px;border:1px dashed #cbd5e1;border-radius:12px;background-color:#f8fafc;background-image:linear-gradient(#e9eef4 1px,transparent 1px),linear-gradient(90deg,#e9eef4 1px,transparent 1px);background-size:18.9px 18.9px;overflow:auto}.egs-label-stage .egs-print-label{zoom:2.1;box-shadow:0 3px 10px rgba(0,0,0,.18)}
.egs-print-label{position:relative;background:#fff;color:#080b0a;font-family:Arial,Helvetica,sans-serif;box-sizing:border-box;overflow:hidden;-webkit-print-color-adjust:exact;print-color-adjust:exact}.egs-corner{position:absolute;width:0;height:0;border-style:solid;z-index:4}.egs-corner-tl{top:1.5mm;left:1.5mm;border-width:3.2mm 3.2mm 0 0;border-color:#167533 transparent transparent transparent}.egs-corner-br{right:1.4mm;bottom:1.4mm;border-width:0 0 3.2mm 3.2mm;border-color:transparent transparent #167533 transparent}
.egs-contact-label{width:58mm;height:40mm;display:grid;grid-template-columns:14mm .8mm 1fr;padding:1.4mm}.egs-contact-brand{display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;min-width:0;padding:.5mm}.egs-contact-brand img{width:12.5mm;height:5mm;object-fit:contain;margin-bottom:.4mm}.egs-contact-brand b{font-size:3.6pt;line-height:1.05;text-transform:uppercase;max-width:13mm}.egs-contact-brand small{font-size:2.8pt;line-height:1.12;margin-top:.5mm;max-width:12mm}.egs-green-rule{width:.45mm;background:#18763a;border-radius:1mm;margin:.4mm auto}.egs-contact-info{padding:.8mm .2mm 0 1mm;min-width:0}.egs-contact-info>b{display:block;font-size:5pt;line-height:1;margin-bottom:.35mm;letter-spacing:.05mm}.egs-contact-info p{margin:0 0 .55mm;font-size:3.25pt;font-weight:600;line-height:1.15;overflow:hidden}.egs-contact-info .egs-phone-line{font-size:3.45pt;line-height:1.28}.egs-site-line{position:absolute;left:16.5mm;right:1.7mm;bottom:1.5mm;white-space:nowrap;text-overflow:ellipsis;font-size:3pt!important}.egs-site-line i{color:#16803b;font-style:normal}.egs-contact-label .egs-corner-br{right:1.2mm;bottom:1.2mm}
.egs-warranty-label{width:58mm;height:40mm;display:grid;grid-template-columns:20mm .8mm 1fr;padding:1.4mm}.egs-warranty-contact{padding:.6mm .7mm .3mm;display:flex;flex-direction:column;align-items:flex-start;overflow:hidden}.egs-warranty-contact img{width:14mm;height:5mm;object-fit:contain;align-self:center;margin-bottom:.15mm}.egs-warranty-contact .wc-name{align-self:center;font-size:4pt;line-height:1;text-transform:uppercase}.egs-warranty-contact .wc-tagline{align-self:center;font-size:2.6pt;margin:.25mm 0 .65mm;line-height:1.1;text-align:center}.egs-warranty-contact strong{font-size:4.4pt;line-height:1;margin:.4mm 0 .25mm}.egs-warranty-contact p{font-size:3.05pt;font-weight:600;line-height:1.16;margin:0}.egs-warranty-contact .wc-phones{font-size:3.15pt;line-height:1.22}.egs-warranty-contact .wc-site{font-size:2.7pt;font-weight:700;margin-top:.55mm;max-width:100%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.egs-warranty-rule{width:.5mm;background:#18763a;border-radius:1mm;margin:.2mm auto}.egs-warranty-data{position:relative;padding:.7mm .3mm 0 1mm;min-width:0}.egs-w-row{display:grid;grid-template-columns:14mm 1fr;align-items:end;height:4mm;font-size:4.5pt}.egs-w-row b{font-size:4.7pt;white-space:nowrap}.egs-w-row span{display:block;border-bottom:.2mm solid #222;height:3mm;line-height:2.8mm;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;padding-left:.5mm}.egs-w-row:first-child{grid-template-columns:7mm 1fr 9mm 1fr;gap:.5mm}.egs-w-row:first-child .fac-label{text-align:right}.egs-date-row{grid-template-columns:7mm 1fr 6mm 1fr;gap:.5mm}.egs-date-row b:nth-of-type(2){text-align:right}.egs-validity-strip{height:3.3mm;background:#18763a;color:#fff;font-size:3.25pt;font-weight:700;line-height:3.3mm;padding:0 .8mm;white-space:nowrap;overflow:hidden;margin-top:.6mm;margin-right:12mm;letter-spacing:.03mm}.egs-next-service{position:absolute;left:1mm;right:12.5mm;bottom:1.4mm;display:grid;grid-template-columns:17mm 1fr;align-items:end;font-size:4.5pt}.egs-next-service span{border-bottom:.2mm solid #222;height:2.8mm;text-align:center}.egs-label-qr{position:absolute;right:.3mm;bottom:.25mm;width:11.2mm;height:11.8mm;background:#fff;border:.3mm solid #18763a;border-radius:.7mm;padding:.45mm;box-sizing:border-box;display:flex;flex-direction:column;align-items:center;justify-content:center}.egs-label-qr.is-empty{display:none}.egs-qr-code{width:9.3mm;height:9.3mm}.egs-qr-code canvas{width:100%!important;height:100%!important;display:block!important}.egs-qr-code img{display:none!important}.egs-qr-code img.egs-qr-print{width:100%!important;height:100%!important;display:block!important}.egs-label-qr small{font-size:2.25pt;font-weight:800;line-height:1;color:#166534;letter-spacing:.12mm;margin-top:.15mm}.egs-warranty-label.has-qr .egs-corner-br{display:none}
/* Equilibrio visual: bloques centrados y QR físico de 15 mm con zona de seguridad. */
.egs-w-row:first-child{grid-template-columns:7mm minmax(0,1fr)}
.egs-contact-info{padding:.5mm .2mm .5mm 1mm;display:flex;flex-direction:column;justify-content:center}.egs-contact-info .egs-site-line{position:static;left:auto;right:auto;bottom:auto;align-self:stretch;margin:.45mm 0 0!important;padding:.45mm .6mm;background:#edf8f0;border-left:.45mm solid #18763a;border-radius:.55mm;color:#124e29}.egs-contact-info .egs-site-line i{color:#18763a}.egs-warranty-contact{justify-content:center}.egs-warranty-contact .wc-site{align-self:stretch;padding:.35mm .45mm;background:#edf8f0;border-radius:.45mm;color:#124e29}.egs-validity-strip{margin-right:0}.egs-next-service{right:1mm}.egs-label-qr{width:18.5mm;height:19.3mm;padding:1.15mm;right:.2mm;bottom:.2mm}.egs-qr-code{width:15mm;height:15mm;flex:none}.egs-label-qr small{font-size:2.4pt;margin-top:.2mm}.egs-warranty-label.has-qr .egs-w-row:not(.egs-date-row){height:2.95mm}.egs-warranty-label.has-qr .egs-w-row:not(.egs-date-row) span{height:2.5mm;line-height:2.3mm}.egs-warranty-label.has-qr .egs-date-row{width:18mm;height:6.5mm;grid-template-columns:7mm 11mm;grid-template-rows:3.25mm 3.25mm;gap:0}.egs-warranty-label.has-qr .egs-date-row b:nth-of-type(2){text-align:left}.egs-warranty-label.has-qr .egs-validity-strip{width:18mm;margin-right:0;padding:0 .45mm;font-size:2.55pt;letter-spacing:0}.egs-warranty-label.has-qr .egs-next-service{right:auto;width:18mm;grid-template-columns:11.5mm 1fr;font-size:3.55pt;bottom:.7mm}
/* Legibilidad a tamaño de impresión. */
.egs-contact-info>b{font-size:5.25pt}.egs-contact-info p{font-size:3.6pt;line-height:1.18}.egs-contact-info .egs-phone-line{font-size:3.8pt;line-height:1.25}.egs-contact-info .egs-site-line{font-size:3.15pt!important}.egs-warranty-contact .wc-tagline{font-size:2.8pt}.egs-warranty-contact strong{font-size:4.75pt}.egs-warranty-contact .wc-address,.egs-warranty-contact .wc-phones{font-size:3.4pt;line-height:1.18}.egs-warranty-contact .wc-site{font-size:3pt;letter-spacing:-.03mm}.egs-validity-strip{display:flex;align-items:center;justify-content:center;gap:.7mm;white-space:normal}.egs-validity-strip b,.egs-validity-strip span{font:inherit}.egs-warranty-label.has-qr .egs-date-row{grid-template-columns:8mm 10mm}.egs-warranty-label.has-qr .egs-date-row b{font-size:4.2pt}.egs-warranty-label.has-qr .egs-date-row span{font-size:4pt;padding-left:.25mm;text-align:right}.egs-warranty-label.has-qr .egs-validity-strip{height:7mm;flex-direction:column;gap:.2mm;line-height:1.05;margin-top:.45mm}.egs-warranty-label.has-qr .egs-validity-strip b{font-size:3.8pt}.egs-warranty-label.has-qr .egs-validity-strip span{font-size:3.4pt;letter-spacing:.08mm}
/* Contacto con orden: identificación compacta y QR físico de 12 mm. */
.egs-contact-qr{display:none}.egs-contact-label.has-qr{grid-template-columns:minmax(0,1fr) 14.2mm;grid-template-rows:5.6mm 1fr;column-gap:.8mm;padding:1.2mm}.egs-contact-label.has-qr .egs-corner{display:none}.egs-contact-label.has-qr .egs-contact-brand{grid-column:1;grid-row:1;flex-direction:row;justify-content:flex-start;gap:.6mm;padding:0;text-align:left}.egs-contact-label.has-qr .egs-contact-brand img{width:8.5mm;height:3.8mm;margin:0;flex:none}.egs-contact-label.has-qr .egs-contact-brand b{font-size:3.25pt;max-width:11mm}.egs-contact-label.has-qr .egs-contact-brand small,.egs-contact-label.has-qr .egs-green-rule{display:none}.egs-contact-label.has-qr .egs-contact-info{grid-column:1;grid-row:2;padding:.15mm 0 0;justify-content:flex-start}.egs-contact-label.has-qr .egs-contact-info>b{font-size:4.2pt;margin-bottom:.2mm}.egs-contact-label.has-qr .egs-contact-info>b:nth-of-type(2){margin-top:.25mm}.egs-contact-label.has-qr .egs-contact-info p{font-size:3.15pt;line-height:1.12;margin-bottom:.25mm}.egs-contact-label.has-qr .egs-contact-info .egs-phone-line{font-size:3.2pt;line-height:1.16}.egs-contact-label.has-qr .egs-contact-info .egs-site-line{font-size:2.7pt!important;margin-top:.15mm!important;padding:.25mm .35mm}.egs-contact-label.has-qr .egs-contact-qr:not(.is-empty){grid-column:2;grid-row:1/3;display:flex;flex-direction:column;align-items:center;justify-content:center;min-width:0;border:.28mm solid #18763a;border-radius:.7mm;padding:.55mm;box-sizing:border-box;background:#fff}.egs-contact-qr>span{font-size:3.45pt;font-weight:700;line-height:1;color:#111;margin-bottom:.35mm;white-space:nowrap}.egs-contact-qr>span b{font-size:4.1pt}.egs-contact-qr-code{width:12mm;height:12mm;flex:none}.egs-contact-qr small{font-size:2.15pt;font-weight:800;line-height:1;color:#166534;letter-spacing:.08mm;margin-top:.25mm}
/* Etiqueta térmica real: 58 × 40 mm, monocromática y legible a 203 dpi. */
.egs-print-label{width:58mm!important;height:40mm!important;color:#000!important;background:#fff!important;font-family:Arial,Helvetica,sans-serif!important;font-synthesis:none;print-color-adjust:exact;-webkit-print-color-adjust:exact}
.egs-print-label .egs-corner,.egs-green-rule,.egs-warranty-rule{display:none!important}
.egs-brand-mark{display:inline-flex;align-items:center;justify-content:center;flex:none;min-width:8mm;height:5.5mm;border:.45mm solid #000;border-radius:.6mm;font-size:8pt;line-height:1;font-weight:900;letter-spacing:.1mm;color:#000;background:#fff}
.egs-brand-copy{display:flex;flex-direction:column;min-width:0}
.egs-brand-copy b{font-size:5pt!important;line-height:1.05!important;font-weight:900!important;letter-spacing:-.07mm;white-space:nowrap;overflow:hidden;text-overflow:clip}
.egs-brand-copy small{font-size:5pt!important;line-height:1.1!important;font-weight:700!important;white-space:nowrap;overflow:hidden;text-overflow:clip}
.egs-contact-label,.egs-contact-label.has-qr{display:grid!important;grid-template-columns:minmax(0,1fr) 23mm!important;grid-template-rows:7mm minmax(0,1fr)!important;column-gap:0!important;padding:2mm!important}
.egs-contact-label:not(.has-qr){grid-template-columns:1fr!important}
.egs-contact-brand,.egs-contact-label.has-qr .egs-contact-brand{grid-column:1!important;grid-row:1!important;display:flex!important;flex-direction:row!important;align-items:center!important;justify-content:flex-start!important;gap:.6mm!important;padding:0!important;text-align:left!important;border-bottom:.35mm solid #000}
.egs-contact-info,.egs-contact-label.has-qr .egs-contact-info{grid-column:1!important;grid-row:2!important;display:grid!important;grid-template-columns:10mm minmax(0,1fr)!important;align-content:start!important;gap:.55mm .8mm!important;padding:1.1mm 0 0!important;min-width:0!important}
.egs-contact-info>b,.egs-contact-label.has-qr .egs-contact-info>b{font-size:6pt!important;line-height:1.15!important;font-weight:900!important;margin:0!important}
.egs-contact-info p,.egs-contact-label.has-qr .egs-contact-info p{font-size:6pt!important;line-height:1.16!important;font-weight:700!important;margin:0!important;max-height:7.2mm!important;overflow:hidden!important;overflow-wrap:anywhere}
.egs-contact-info .egs-phone-line,.egs-contact-label.has-qr .egs-contact-info .egs-phone-line{font-size:6.4pt!important;line-height:1.15!important;font-weight:900!important}
.egs-contact-info .egs-site-line,.egs-contact-label.has-qr .egs-contact-info .egs-site-line{grid-column:1/3!important;position:static!important;margin:.5mm 0 0!important;padding:.6mm .8mm!important;border:0!important;border-top:.35mm solid #000!important;border-radius:0!important;background:#fff!important;color:#000!important;font-size:5.8pt!important;line-height:1.1!important;font-weight:800!important;white-space:nowrap!important;overflow:hidden!important;text-overflow:ellipsis!important}
.egs-contact-qr,.egs-contact-label.has-qr .egs-contact-qr:not(.is-empty){grid-column:2!important;grid-row:1/3!important;display:flex!important;position:static!important;width:23mm!important;height:auto!important;min-width:23mm!important;align-items:center!important;justify-content:center!important;flex-direction:column!important;padding:0!important;border:0!important;border-radius:0!important;background:#fff!important;color:#000!important}
.egs-label-qr{grid-column:2!important;grid-row:1/3!important;display:flex!important;position:static!important;width:22.2mm!important;height:auto!important;min-width:22.2mm!important;align-items:center!important;justify-content:center!important;flex-direction:column!important;padding:0!important;border:0!important;border-radius:0!important;background:#fff!important;color:#000!important}
.egs-contact-qr.is-empty,.egs-label-qr.is-empty{display:none!important}
.egs-contact-qr>span,.egs-label-qr>span{font-size:6pt!important;line-height:1!important;font-weight:900!important;margin:0 0 .6mm!important;color:#000!important;white-space:nowrap}
.egs-contact-qr>span b,.egs-label-qr>span b{font-size:6.5pt!important}
.egs-qr-safe{display:flex;flex:0 0 22.2mm;width:22.2mm;height:22.2mm;align-items:center;justify-content:center;background:#fff}
.egs-qr-code,.egs-contact-qr-code{width:22.2mm!important;height:22.2mm!important;flex:none!important}
.egs-qr-code svg{display:block!important;width:22.2mm!important;height:22.2mm!important;shape-rendering:crispEdges}
.egs-qr-code canvas,.egs-qr-code img.egs-qr-print{display:block!important;width:18.8mm!important;height:18.8mm!important;margin:1.7mm!important;image-rendering:pixelated}
.egs-contact-qr small,.egs-label-qr small{max-width:22.2mm!important;overflow:visible!important;font-size:4.8pt!important;line-height:1.05!important;font-weight:900!important;letter-spacing:0!important;margin:.7mm 0 0!important;color:#000!important;text-align:center!important;white-space:normal!important}
.egs-contact-qr .egs-qr-safe{flex-basis:23mm;width:23mm;height:23mm}
.egs-contact-qr-code{width:23mm!important;height:23mm!important}
.egs-contact-qr-code svg{width:23mm!important;height:23mm!important}
.egs-contact-qr-code canvas,.egs-contact-qr-code img.egs-qr-print{width:19.4mm!important;height:19.4mm!important;margin:1.8mm!important}
.egs-contact-qr small{max-width:23mm!important}
.egs-warranty-label{display:grid!important;grid-template-columns:minmax(0,1fr) 22.2mm!important;grid-template-rows:7mm minmax(0,1fr)!important;column-gap:0!important;padding:2mm!important}
.egs-warranty-label:not(.has-qr){grid-template-columns:1fr!important}
.egs-warranty-contact{grid-column:1!important;grid-row:1!important;display:flex!important;flex-direction:row!important;align-items:center!important;justify-content:flex-start!important;gap:.6mm!important;padding:0!important;border-bottom:.35mm solid #000!important;overflow:hidden!important}
.egs-warranty-data{grid-column:1!important;grid-row:2!important;position:static!important;padding:.7mm 0 0!important;min-width:0!important;overflow:hidden!important}
.egs-w-row,.egs-warranty-label.has-qr .egs-w-row:not(.egs-date-row){display:grid!important;grid-template-columns:9mm minmax(0,1fr)!important;align-items:center!important;height:3.2mm!important;min-height:3.2mm!important;gap:1mm!important;border-bottom:.25mm solid #000!important}
.egs-w-row b,.egs-warranty-label.has-qr .egs-w-row b{font-size:5.1pt!important;line-height:1!important;font-weight:900!important;letter-spacing:-.04mm!important;white-space:nowrap!important}
.egs-w-row span,.egs-warranty-label.has-qr .egs-w-row span{height:auto!important;line-height:1.05!important;border:0!important;padding:0!important;font-size:5.4pt!important;font-weight:700!important;white-space:nowrap!important;overflow:hidden!important;text-overflow:ellipsis!important}
.egs-date-row,.egs-warranty-label.has-qr .egs-date-row{display:grid!important;width:auto!important;height:5.8mm!important;grid-template-columns:9mm minmax(0,1fr)!important;grid-template-rows:2.9mm 2.9mm!important;gap:0 1mm!important}
.egs-date-row span,.egs-warranty-label.has-qr .egs-date-row span{padding-left:.4mm!important}
.egs-date-row b:nth-of-type(2){text-align:left!important}
.egs-validity-strip,.egs-warranty-label.has-qr .egs-validity-strip{display:flex!important;height:4.3mm!important;width:auto!important;flex-direction:row!important;align-items:center!important;justify-content:center!important;gap:.7mm!important;margin:.45mm 0 0!important;padding:.35mm .6mm!important;background:#000!important;color:#fff!important;white-space:nowrap!important}
.egs-validity-strip b,.egs-validity-strip span,.egs-warranty-label.has-qr .egs-validity-strip b,.egs-warranty-label.has-qr .egs-validity-strip span{max-width:none!important;overflow:visible!important;text-overflow:clip!important;font-size:4.8pt!important;line-height:1!important;font-weight:900!important;letter-spacing:-.03mm!important;color:#fff!important;white-space:nowrap!important}
.egs-next-service{display:none!important}
@media(max-width:991px){.egs-preview-panel{position:static}.egs-label-stage .egs-print-label{zoom:1.8}}@media(max-width:480px){.egs-label-intro>span{display:none}.egs-mode-choice{grid-template-columns:1fr}.egs-label-stage .egs-print-label{zoom:1.4}}
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
(function(){
  var orders = <?= json_encode($ordenesEtiqueta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
  var orderQrBase = <?= json_encode($urlOrdenQrBase, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
  var generatedToken = <?= json_encode($tokenGenerado, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
  var byId = {};
  orders.forEach(function(order){ byId[String(order.id)] = order; });

  function val(name){ var el=document.querySelector('[name="'+name+'"]'); return el ? el.value.trim() : ''; }
  function set(name,value){ var el=document.querySelector('[name="'+name+'"]'); if(el){el.value=value||'';el.dispatchEvent(new Event('input',{bubbles:true}));} }
  function text(id,value){ var el=document.getElementById(id); if(el) el.textContent=value||''; }
  function displaySite(value){ return (value||'').replace(/^https?:\/\//i,'').replace(/\/$/,''); }
  function compactToken(token){
    if(!/^[a-f0-9]{64}$/i.test(token||'')) return token||'';
    var binary='';
    for(var i=0;i<token.length;i+=2) binary+=String.fromCharCode(parseInt(token.substr(i,2),16));
    return btoa(binary).replace(/\+/g,'-').replace(/\//g,'_').replace(/=+$/,'');
  }
  function prettyDate(value){ if(!value)return ''; var p=value.slice(0,10).split('-'); return p.length===3 ? p[2]+'/'+p[1]+'/'+p[0] : value; }
  function cleanDate(value){ if(!value || !/^\d{4}-\d{2}-\d{2}/.test(value))return ''; var year=parseInt(value.slice(0,4),10); return year>=2000 ? value.slice(0,10) : ''; }
  function todayLocal(){ var d=new Date(); return d.getFullYear()+'-'+String(d.getMonth()+1).padStart(2,'0')+'-'+String(d.getDate()).padStart(2,'0'); }
  function addMonths(dateValue,months){ if(!dateValue)return ''; var p=dateValue.split('-'),d=new Date(+p[0],+p[1]-1,+p[2]); var day=d.getDate(); d.setDate(1); d.setMonth(d.getMonth()+parseInt(months||0,10)); var last=new Date(d.getFullYear(),d.getMonth()+1,0).getDate(); d.setDate(Math.min(day,last)); return d.getFullYear()+'-'+String(d.getMonth()+1).padStart(2,'0')+'-'+String(d.getDate()).padStart(2,'0'); }
  function syncContact(){
    var phones=[val('whatsapp'),val('telefono_1'),val('telefono_2'),val('telefono_3')].filter(Boolean);
    text('contactPhones',phones.slice(0,2).join(' · '));
    text('contactWebsite',displaySite(val('sitio_web')));
    document.querySelectorAll('.wc-name').forEach(function(e){e.textContent=val('nombre_comercial')});
    document.querySelectorAll('.wc-tagline').forEach(function(e){e.textContent=val('lema')});
    document.querySelectorAll('.wc-address').forEach(function(e){e.textContent=val('direccion')});
    document.querySelectorAll('.wc-phones').forEach(function(e){e.textContent=phones[0]||''});
    document.querySelectorAll('.wc-site').forEach(function(e){e.textContent=displaySite(val('sitio_web'))});
  }
  function renderThermalQr(box,url){
    box.innerHTML='';
    var qr=new QRCode(box,{text:url,width:320,height:320,colorDark:'#000000',colorLight:'#ffffff',correctLevel:QRCode.CorrectLevel.M});
    try{
      var model=qr._oQRCode, count=model&&model.getModuleCount?model.getModuleCount():0;
      if(!count)return;
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
    }catch(error){
      /* Conserva el canvas de qrcodejs como respaldo. */
    }
  }
  document.querySelectorAll('.egs-live').forEach(function(el){el.addEventListener('input',function(){text(el.dataset.target,el.value);syncContact();});});
  document.querySelectorAll('.egs-phone').forEach(function(el){el.addEventListener('input',syncContact);});
  document.querySelectorAll('.egs-warranty-live').forEach(function(el){el.addEventListener('input',function(){text(el.dataset.target,el.type==='date'?prettyDate(el.value):el.value);});});

  function renderQr(token){
    var orderId=document.getElementById('idOrdenEtiqueta').value;
    var url=token && orderId ? orderQrBase+encodeURIComponent(orderId)+'&g='+encodeURIComponent(compactToken(token)) : '';
    var targets=[['warrantyQr','warrantyQrCode','warrantyLabel'],['contactQr','contactQrCode','contactLabel']];
    targets.forEach(function(ids){
      var wrap=document.getElementById(ids[0]),box=document.getElementById(ids[1]),label=document.getElementById(ids[2]);
      box.innerHTML=''; wrap.classList.toggle('is-empty',!url); label.classList.toggle('has-qr',!!url);
      if(url && typeof QRCode!=='undefined') renderThermalQr(box,url);
    });
    text('contactOrder',url && orderId ? '#'+orderId : '');
    text('wWarrantyOrder',url && orderId ? '#'+orderId : '');
    text('qrUrlText',url||'Guarda la garantía vinculada para generar el enlace.');
  }
  function selectOrder(){
    var id=document.getElementById('idOrdenEtiqueta').value, o=byId[id];
    document.getElementById('ordenVisual').value=id ? '#'+id : '';
    text('wOrder',id ? '#'+id : '');
    if(!o){renderQr('');return;}
    var saved=!!o.garantia_token;
    set('fac_rem',saved?o.garantia_fac_rem:'');
    set('tecnico',saved?o.garantia_tecnico:o.tecnico_nombre);
    set('clave_cliente',saved?o.garantia_clave_cliente:String(o.id_usuario||''));
    set('nombre_cliente',saved?o.garantia_nombre_cliente:o.cliente_nombre);
    var device=[o.marcaDelEquipo,o.modeloDelEquipo].filter(Boolean).join(' ');
    set('equipo',saved?o.garantia_equipo:device);
    set('numero_serie',saved?o.garantia_numero_serie:o.numeroDeSerieDelEquipo);
    var delivery=saved?o.garantia_fecha_entrega:(cleanDate(o.fecha_Salida)||todayLocal());
    set('fecha_entrega',delivery);
    set('fecha_vencimiento',saved?o.garantia_fecha_vencimiento:addMonths(delivery,document.getElementById('mesesGarantia').value));
    set('proximo_servicio',saved?o.garantia_proximo_servicio:'');
    renderQr(saved?o.garantia_token:'');
  }
  document.getElementById('idOrdenEtiqueta').addEventListener('change',selectOrder);
  document.getElementById('mesesGarantia').addEventListener('input',function(){set('fecha_vencimiento',addMonths(val('fecha_entrega'),this.value));});
  document.querySelector('[name="fecha_entrega"]').addEventListener('change',function(){set('fecha_vencimiento',addMonths(this.value,document.getElementById('mesesGarantia').value));});
  document.querySelectorAll('[name="modo_etiqueta"]').forEach(function(radio){radio.addEventListener('change',function(){
    document.querySelectorAll('.egs-mode-choice label').forEach(function(label){label.classList.toggle('active',label.querySelector('input').checked);});
    var empty=this.value==='vacia' && this.checked;
    document.getElementById('orderPicker').style.display=empty?'none':'';
    document.getElementById('saveWarranty').disabled=empty;
    if(empty){document.getElementById('idOrdenEtiqueta').value='';document.getElementById('ordenVisual').value='';['fac_rem','tecnico','clave_cliente','nombre_cliente','equipo','numero_serie','fecha_entrega','fecha_vencimiento','proximo_servicio'].forEach(function(n){set(n,'')});text('wOrder','');renderQr('');}
  });});
  syncContact();
  if(generatedToken){
    var postedId=<?= json_encode(isset($_POST["id_orden"]) ? (string) $_POST["id_orden"] : "") ?>;
    if(postedId){document.getElementById('idOrdenEtiqueta').value=postedId;selectOrder();renderQr(generatedToken);}
  }

  window.egsPrintLabel=function(type){
    var source=document.getElementById(type==='contact'?'contactLabel':'warrantyLabel');
    if(type==='warranty' && document.querySelector('[name="modo_etiqueta"]:checked').value==='orden' && !document.querySelector('#warrantyQrCode canvas')){
      Swal.fire({icon:'info',title:'Primero genera el QR',text:'Guarda la garantía vinculada antes de imprimirla.'}); return;
    }
    var clone=source.cloneNode(true), canvas=source.querySelector('.egs-qr-code canvas'), cloneQr=clone.querySelector('.egs-qr-code');
    if(canvas&&cloneQr) cloneQr.innerHTML='<img class="egs-qr-print" src="'+canvas.toDataURL('image/png')+'" alt="QR">';
    var size='58mm 40mm';
    var win=window.open('','_blank','width=520,height=420');
    if(!win){Swal.fire({icon:'warning',title:'Permite ventanas emergentes',text:'Se necesita abrir la vista de impresión.'});return;}
    win.document.write('<!doctype html><html><head><meta charset="utf-8"><title>Etiqueta EGS</title><style>'+document.getElementById('egsLabelStyles').textContent+'@page{size:'+size+';margin:0}html,body{margin:0!important;padding:0!important;width:'+size.split(' ')[0]+';height:'+size.split(' ')[1]+';overflow:hidden}.egs-print-label{box-shadow:none!important;zoom:1!important}</style></head><body>'+clone.outerHTML+'</body></html>');
    win.document.close(); win.focus(); setTimeout(function(){win.print();},650);
  };
})();
</script>
