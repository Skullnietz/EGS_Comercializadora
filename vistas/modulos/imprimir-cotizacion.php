<?php
/* ================================================================
   IMPRIMIR COTIZACIÓN — Vista read-only del formato profesional
   Carga datos desde BD y renderiza el mismo layout de cotizacion.php
   ================================================================ */

$_ic_id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;
$_ic_cot = null;

if ($_ic_id > 0) {
    $result = CotizacionesControlador::ctrMostrarCotizaciones("id", $_ic_id);
    if (is_array($result) && !empty($result)) {
        // ctrMostrarCotizaciones devuelve fetchAll(), tomar el primer elemento
        $_ic_cot = isset($result["id"]) ? $result : (isset($result[0]) ? $result[0] : null);
    }
}

if (!$_ic_cot) {
    echo '<div class="content-wrapper"><section class="content" style="padding:40px;text-align:center">
      <h3 style="color:#666"><i class="fa fa-exclamation-triangle"></i> Cotización no encontrada</h3>
      <a href="index.php?ruta=historial-cotizaciones" class="btn btn-default" style="margin-top:16px">Volver al historial</a>
    </section></div>';
    return;
}

// Datos
$_ic_productos = json_decode($_ic_cot["productos"], true);
if (!is_array($_ic_productos)) $_ic_productos = array();
$_ic_fecha = !empty($_ic_cot["fecha"]) ? date("d/m/Y", strtotime($_ic_cot["fecha"])) : date("d/m/Y");
$_ic_empresa = isset($_ic_cot["empresa"]) ? $_ic_cot["empresa"] : "egs";

// Vendedor nombre
$_ic_vendedorNombre = "VENDEDOR GENERAL";
if (!empty($_ic_cot["id_vendedor"])) {
    try {
        $asesor = Controladorasesores::ctrMostrarAsesoresEleg("id", intval($_ic_cot["id_vendedor"]));
        if (is_array($asesor) && isset($asesor["nombre"])) {
            $_ic_vendedorNombre = $asesor["nombre"];
        }
    } catch (Exception $e) {}
}

// Empresa config
$_ic_empresas = array(
    "egs" => array(
        "nombre" => "COMERCIALIZADORA EGS",
        "logo" => "vistas/img/plantilla/Captura3.PNG",
        "direccion" => "Pino Suárez 308 Colonia Santa Clara 50090, Toluca México",
        "telefonos" => "729-1339897 | 722-2144416"
    ),
    "loce" => array(
        "nombre" => "COMERCIALIZADORA LOCE",
        "logo" => "vistas/img/plantilla/LOCE.png",
        "direccion" => "CALLE LIBERTAD S/N, JUNTA LOCAL DE CAMINOS, TOLUCA, ESTADO DE MÉXICO, C.P. 50285, MÉXICO.",
        "telefonos" => ""
    ),
    "toluca" => array(
        "nombre" => "TOLUCA GRUPO DE VENTA S.A. DE C.V.",
        "logo" => "vistas/img/plantilla/logo tgv.jpg",
        "direccion" => "ANDADOR NICOLAS ROMERO, 308, COLONIA CENTRO, C. P. 50000, TOLUCA, ESTADO DE MÉXICO",
        "telefonos" => ""
    ),
    "cartuchos" => array(
        "nombre" => "Cartuchos Para Impresoras",
        "logo" => "vistas/img/plantilla/CPI.png",
        "direccion" => "Miguel Hidalgo Oriente 502-A Col. Santa Clara, Toluca, Estado de México. C.P. 50090, México",
        "telefonos" => ""
    )
);

$_ic_emp = isset($_ic_empresas[$_ic_empresa]) ? $_ic_empresas[$_ic_empresa] : $_ic_empresas["egs"];

// Vigencia evaluación
$_ic_expirada = false;
$vigText = isset($_ic_cot["vigencia"]) ? $_ic_cot["vigencia"] : "";
$fechaCot = isset($_ic_cot["fecha"]) ? $_ic_cot["fecha"] : "";
if (preg_match('/(\d+)/', $vigText, $m) && !empty($fechaCot)) {
    $dias = intval($m[1]);
    $fechaBase = strtotime($fechaCot);
    if ($fechaBase !== false) {
        $_ic_expirada = (date('Y-m-d') > date('Y-m-d', strtotime("+{$dias} days", $fechaBase)));
    }
}

// URL de validación para QR
$_ic_qrUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
    . '://' . $_SERVER['HTTP_HOST']
    . dirname($_SERVER['SCRIPT_NAME'])
    . '/index.php?ruta=validar-cotizacion&codigo=' . urlencode($_ic_cot["codigo_qr"]);
$_ic_qrUrl = str_replace('//', '/', $_ic_qrUrl);
$_ic_qrUrl = preg_replace('#^(https?:)/#', '$1//', $_ic_qrUrl);

// Descuento
$_ic_descPct = floatval(isset($_ic_cot["descuento_porcentaje"]) ? $_ic_cot["descuento_porcentaje"] : 0);
$_ic_neto = floatval($_ic_cot["neto"]);
$_ic_impuesto = floatval($_ic_cot["impuesto"]);
$_ic_total = floatval($_ic_cot["total"]);
$_ic_descMonto = $_ic_descPct > 0 ? $_ic_neto * ($_ic_descPct / 100) : 0;
?>
<!doctype html>
<html lang="es-MX">
<head>
<meta charset="utf-8">
<title>Cotización #<?php echo $_ic_cot["id"]; ?> - <?php echo $_ic_emp["nombre"]; ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<style>
/* Ocultar AdminLTE wrapper */
.main-header, .main-sidebar, .left-side, .control-sidebar, .main-footer { display:none !important }
body.sidebar-mini .content-wrapper, body .content-wrapper { margin-left:0 !important; min-height:auto !important; background:#1f2937 !important }
.wrapper { min-height:auto !important }

:root { --text:#111; --muted:#555; --border:#cfd6e0; --bg:#fff; }
html,body { background:#1f2937; color:var(--text); font-family:system-ui,-apple-system,"Segoe UI",Roboto,Arial; line-height:1.34; margin:0 }
.sheet { width:210mm; max-width:100%; background:var(--bg); margin:14px auto; box-shadow:0 10px 30px rgba(0,0,0,.25); border-radius:10px; overflow:hidden }
.toolbar { display:flex; gap:8px; padding:10px 14px; justify-content:center; align-items:center; flex-wrap:wrap }
.btn { border:1px solid #c2cde0; background:#fff; color:#0b1a33; padding:8px 14px; border-radius:6px; cursor:pointer; font-weight:600; font-size:14px; text-decoration:none; display:inline-flex; align-items:center; gap:6px }
.btn.primary { background:#0a0a0a; color:#fff; border-color:#0a0a0a }
.btn.success { background:#16a34a; color:#fff; border-color:#16a34a }
.btn:hover { opacity:.9 }

/* Header */
.header { background:#0a0a0a; color:#fff; padding:8mm 10mm 5mm }
.head-grid { display:grid; grid-template-columns:1.1fr .9fr; gap:8px }
.brand { display:flex; align-items:center; gap:10px }
.logo { height:70px; max-width:180px; border-radius:6px; overflow:hidden; background:#222; display:flex; justify-content:center; align-items:center }
.logo img { max-height:100%; max-width:100%; width:auto; height:auto }
.company h1 { margin:0; font-size:22px; font-weight:900; letter-spacing:.5px; text-transform:uppercase }
.company .underline { margin-top:2px; height:6px; background:linear-gradient(#000,#000) left/100% 3px no-repeat; filter:grayscale(100%) }
.addr,.tel { font-size:12.6px; opacity:.95; margin-top:3px }
.right h2 { margin:0; font-size:18px; text-align:right; letter-spacing:.3px }
.kv { margin-top:4px; display:grid; grid-template-columns:86px 1fr; gap:4px 10px; justify-content:end; font-size:12.6px }
.kv label { opacity:.9; text-align:right }
.kv span { font-weight:500 }
.bar { height:2px; background:#e6e6e6; margin-top:6mm }

/* Content */
.content { padding:7mm 10mm 9mm }
.row2 { display:grid; grid-template-columns:2fr 1fr; gap:8px }
.field { display:grid; gap:3px }
.field-label { font-size:12px; color:var(--muted); letter-spacing:.06em }
.field-value { font-size:14px; padding:7px 0 }

/* Table */
table { width:100%; border-collapse:collapse; margin-top:6px; font-size:13.3px }
thead th { background:#0a0a0a; color:#fff; padding:6px 8px; border:1px solid #0a0a0a; text-align:left }
thead th:nth-child(2),thead th:nth-child(3),thead th:nth-child(4) { text-align:center; white-space:nowrap }
thead th:nth-child(1) { width:64% } thead th:nth-child(2) { width:10% } thead th:nth-child(3) { width:12% } thead th:nth-child(4) { width:14% }
tbody td { border:1px solid var(--border); padding:6px 8px; background:#fff; vertical-align:top }
tbody td:nth-child(2),tbody td:nth-child(3),tbody td:nth-child(4) { text-align:center }

/* Totals */
.totals { margin-top:6px; display:grid; grid-template-columns:1fr 62mm }
.totals table { margin:0 0 0 auto; width:62mm; font-size:13.1px; border-collapse:collapse }
.totals td { padding:6px; border:1px solid var(--border); background:#fff; vertical-align:top }
.totals tr td:first-child { text-align:right; color:#333 }
.totals .grand td { font-weight:800; background:#eee }

.note { margin-top:6mm; font-size:12.3px; color:#444 }
.signatures { margin-top:8mm; display:grid; grid-template-columns:1fr 1fr; gap:9mm; font-size:12.3px }
.sig-line { border-top:1px solid #888; padding-top:6px; text-align:center }
.qr-footer { margin-top:20mm; display:flex; flex-direction:column; align-items:center; gap:10px; text-align:center }
.qr-footer .qr-meta { font-size:12px; color:#555 }

/* Privacy sheet */
.privacy-sheet { padding:0 }
.privacy-content { padding:9mm 10mm 10mm; color:#111; font-size:14px; line-height:1.55 }
.privacy-title { text-align:center; font-weight:900; font-size:22px; letter-spacing:.2px; margin:0 0 6px }
.privacy-subtitle { text-align:center; font-weight:700; font-size:15px; margin:0 0 18px; color:#333 }
.privacy-content p { margin:10px 0 }
.privacy-choices { text-align:center; font-weight:900; margin:22px 0; font-size:17px; letter-spacing:.3px }
.privacy-law { margin:12px 0 }
.privacy-signoff { text-align:center; margin:24px 0 34px }
.privacy-signature { margin-top:46px }
.privacy-signature-line { border-bottom:1px solid #000; width:80%; margin:0 auto }
.privacy-signature-label { font-size:15px; font-weight:700; margin-top:8px; text-transform:uppercase; text-align:center }
.privacy-date { font-size:14px; font-weight:700; margin-top:18px }

/* Expired banner */
.expired-banner { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; padding:10px 16px; border-radius:8px; margin-bottom:12px; display:flex; align-items:center; gap:10px; font-size:13px; font-weight:600 }
.expired-banner i { font-size:18px; color:#dc2626 }

/* Compact */
.compact thead th { padding:5px } .compact tbody td { padding:4px 6px; font-size:12.8px }
.compact .totals td { padding:5px } .compact table,.compact .totals table { font-size:12.4px }

/* Print */
@page { size:letter portrait; margin:9mm }
@media print {
  html,body { -webkit-print-color-adjust:exact; print-color-adjust:exact; background:#fff }
  .toolbar { display:none !important }
  .sheet { box-shadow:none; margin:0; border-radius:0 }
  .header { padding:8mm 9mm 4mm }
  .content { padding:6mm 9mm 8mm }
  .head-grid,.row2 { gap:6px }
  table,.totals,.signatures { page-break-inside:avoid }
  .expired-banner { -webkit-print-color-adjust:exact; print-color-adjust:exact }
    .privacy-sheet { page-break-before:always }
}
</style>
</head>
<body>

<div class="toolbar">
    <a href="index.php?ruta=historial-cotizaciones" class="btn"><i class="fa fa-arrow-left"></i> Volver</a>
    <button class="btn primary" onclick="window.print()"><i class="fa fa-print"></i> Imprimir / PDF</button>
    <?php if ($_ic_expirada): ?>
    <a href="index.php?ruta=cotizacion&recotizar=<?php echo $_ic_cot['id']; ?>" class="btn success"><i class="fa fa-rotate-right"></i> Recotizar</a>
    <?php endif; ?>
</div>

<section class="sheet<?php echo count($_ic_productos) > 5 ? ' compact' : ''; ?>" id="sheet">
    <header class="header">
        <div class="head-grid">
            <div>
                <div class="brand">
                    <div class="logo"><img src="<?php echo $_ic_emp['logo']; ?>" alt="Logo"></div>
                    <div class="company">
                        <h1><?php echo htmlspecialchars($_ic_emp['nombre']); ?></h1>
                        <div class="underline"></div>
                    </div>
                </div>
                <div class="addr"><b>Dirección:</b> <?php echo htmlspecialchars($_ic_emp['direccion']); ?></div>
                <?php if (!empty($_ic_emp['telefonos'])): ?>
                <div class="tel"><b>Teléfonos:</b> <?php echo htmlspecialchars($_ic_emp['telefonos']); ?></div>
                <?php endif; ?>
            </div>
            <div class="right">
                <h2>COTIZACIÓN</h2>
                <div class="kv">
                    <label>FECHA:</label>
                    <span><?php echo $_ic_fecha; ?></span>
                    <label>VENDEDOR:</label>
                    <span><?php echo htmlspecialchars($_ic_vendedorNombre); ?></span>
                    <label>CLIENTE:</label>
                    <span><?php echo htmlspecialchars($_ic_cot['nombre_cliente']); ?></span>
                </div>
            </div>
        </div>
        <div class="bar"></div>
    </header>

    <main class="content">
        <?php if ($_ic_expirada): ?>
        <div class="expired-banner">
            <i class="fa fa-exclamation-triangle"></i>
            <span>Esta cotización ha expirado. Los precios y condiciones ya no son válidos.</span>
        </div>
        <?php endif; ?>

        <div class="row2">
            <div class="field">
                <div class="field-label">Asunto</div>
                <div class="field-value" style="text-align:center"><?php echo htmlspecialchars($_ic_cot['asunto']); ?></div>
            </div>
            <div class="field">
                <div class="field-label">Vigencia</div>
                <div class="field-value" style="text-align:center"><?php echo htmlspecialchars($_ic_cot['vigencia']); ?></div>
            </div>
        </div>

        <?php if (!empty($_ic_cot['observaciones'])): ?>
        <div class="field" style="margin-top:6px">
            <div class="field-label">Descripción / Observaciones</div>
            <div class="field-value"><?php echo nl2br(htmlspecialchars($_ic_cot['observaciones'])); ?></div>
        </div>
        <?php endif; ?>

        <table id="items">
            <thead>
                <tr>
                    <th>PRODUCTO / SERVICIO</th>
                    <th>CANTIDAD</th>
                    <th>PRECIO</th>
                    <th>TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($_ic_productos as $prod):
                    $precio = isset($prod['precio']) ? floatval($prod['precio']) : 0;
                    $cantidad = isset($prod['cantidad']) ? intval($prod['cantidad']) : 1;
                    $totalProd = isset($prod['total']) ? floatval($prod['total']) : ($precio * $cantidad);
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($prod['descripcion']); ?></td>
                    <td><?php echo $cantidad; ?></td>
                    <td>$<?php echo number_format($precio, 2); ?></td>
                    <td>$<?php echo number_format($totalProd, 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="totals">
            <div></div>
            <table>
                <tr>
                    <td>Subtotal</td>
                    <td>$<?php echo number_format($_ic_neto, 2); ?></td>
                </tr>
                <?php if ($_ic_descPct > 0): ?>
                <tr>
                    <td>Descuento (<?php echo number_format($_ic_descPct, 0); ?>%)</td>
                    <td>-$<?php echo number_format($_ic_descMonto, 2); ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td>IVA (16%)</td>
                    <td>$<?php echo number_format($_ic_impuesto, 2); ?></td>
                </tr>
                <tr class="grand">
                    <td>Total</td>
                    <td>$<?php echo number_format($_ic_total, 2); ?></td>
                </tr>
            </table>
        </div>

        <div class="note">Precios en MXN. Disponibilidad y tiempos sujetos a cambio sin previo aviso. Garantía conforme a fabricante o política de servicio. *Esta cotización no incluye conceptos no especificados.</div>

        <div class="signatures">
            <div><div class="sig-line">Firma de Cliente</div></div>
            <div><div class="sig-line">Firma de Vendedor</div></div>
        </div>

        <div class="qr-footer">
            <div id="qrcode-footer"></div>
            <div class="qr-meta">
                Escanea para validar cotización<br>
                ID: <strong><?php echo htmlspecialchars($_ic_cot['codigo_qr']); ?></strong>
            </div>
        </div>
    </main>
</section>

<section class="sheet privacy-sheet">
    <header class="header">
        <div class="head-grid">
            <div>
                <div class="brand">
                    <div class="logo"><img src="<?php echo $_ic_emp['logo']; ?>" alt="Logo"></div>
                    <div class="company">
                        <h1><?php echo htmlspecialchars($_ic_emp['nombre']); ?></h1>
                        <div class="underline"></div>
                    </div>
                </div>
                <div class="addr"><b>Dirección:</b> <?php echo htmlspecialchars($_ic_emp['direccion']); ?></div>
                <?php if (!empty($_ic_emp['telefonos'])): ?>
                <div class="tel"><b>Teléfonos:</b> <?php echo htmlspecialchars($_ic_emp['telefonos']); ?></div>
                <?php endif; ?>
            </div>
            <div class="right">
                <h2>AVISO DE PRIVACIDAD</h2>
                <div class="kv">
                    <label>FECHA:</label>
                    <span><?php echo $_ic_fecha; ?></span>
                    <label>CLIENTE:</label>
                    <span><?php echo htmlspecialchars($_ic_cot['nombre_cliente']); ?></span>
                    <label>DOCUMENTO:</label>
                    <span>Cotización #<?php echo intval($_ic_cot['id']); ?></span>
                </div>
            </div>
        </div>
        <div class="bar"></div>
    </header>

    <main class="privacy-content">
        <h3 class="privacy-title">AVISO Y POLÍTICA DE PRIVACIDAD PARA EL MANEJO DE DATOS PERSONALES</h3>
        <div class="privacy-subtitle">COMERCIALIZADORA EGS (EQUIPO DE CÓMPUTO Y SOFTWARE)</div>

        <p><b>Asunto:</b> Confidencialidad y Autorización de Mensajes Promocionales</p>
        <p>ESTIMADO/A CLIENTE: <b><?php echo htmlspecialchars($_ic_cot['nombre_cliente']); ?></b></p>
        <p>En COMERCIALIZADORA EGS valoramos la confianza que depositas en nosotros. Para proteger tu información, nos comprometemos a mantener la confidencialidad de los datos que compartas con nosotros.</p>
        <p>Además, nos gustaría mantenerte al tanto de nuestras ofertas y novedades. Si deseas recibir mensajes promocionales de COMERCIALIZADORA EGS a través de WhatsApp, por favor, responde a este aviso seleccionando "ACEPTO".</p>

        <div class="privacy-choices">[ &nbsp;&nbsp; ] ACEPTO &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; [ &nbsp;&nbsp; ] NO ACEPTO</div>

        <p>Esta carta o acuerdo de confidencialidad de la empresa COMERCIALIZADORA EGS se fundamenta principalmente en la protección de datos personales. Se apega a los siguientes artículos y leyes fundamentales:</p>

        <div class="privacy-law">
            <b>1. Constitución Política de los Estados Unidos Mexicanos</b><br>
            Artículo 16 (Segundo párrafo): Protege el derecho a la protección de datos personales, el acceso, rectificación, cancelación y oposición (derechos ARCO), así como la privacidad de las comunicaciones.
        </div>

        <div class="privacy-law">
            <b>2. Ley Federal de Protección de Datos Personales en Posesión de los Particulares (LFPDPPP)</b><br>
            Esta es la ley principal para el manejo de información de clientes.<br>
            - <b>Artículo 6:</b> Establece que los responsables del tratamiento de datos (la empresa) deben garantizar la confidencialidad.<br>
            - <b>Artículos 14 y 15:</b> Obligan a que el tratamiento de datos se limite a las finalidades acordadas y se proteja contra el uso indebido.<br>
            - <b>Artículo 21:</b> Obliga a los terceros que reciban datos a mantener la confidencialidad.
        </div>

        <p>Tu privacidad es importante. Puedes revocar este permiso por escrito en cualquier momento.</p>

        <p class="privacy-signoff">Atentamente,<br><b>COMERCIALIZADORA EGS</b></p>

        <div class="privacy-signature">
            <div class="privacy-signature-line"></div>
            <div class="privacy-signature-label">FIRMA DE CONFORMIDAD</div>
            <div class="privacy-date">FECHA: <?php echo $_ic_fecha; ?></div>
        </div>
    </main>
</section>

<script>
new QRCode(document.getElementById("qrcode-footer"), {
    text: <?php echo json_encode($_ic_qrUrl); ?>,
    width: 128,
    height: 128,
    colorDark: "#000000",
    colorLight: "#ffffff",
    correctLevel: QRCode.CorrectLevel.H
});
</script>
</body>
</html>
