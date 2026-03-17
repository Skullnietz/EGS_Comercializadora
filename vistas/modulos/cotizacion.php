<?php
/* ================== PHP: datos para selects ================== */
$asesorSeleccionadoId = isset($valueOrdenes['id_Asesor']) ? (int) $valueOrdenes['id_Asesor'] : 0;
$clienteSeleccionadoId = isset($valueOrdenes['id_usuario']) ? (int) $valueOrdenes['id_usuario'] : 0;

/* Asesores */
$asesores = [];
try {
  $asesores = Controladorasesores::ctrMostrarAsesoresEleg(null, null);
  if (!is_array($asesores)) {
    $asesores = [];
  }
} catch (Throwable $e) {
  $asesores = [];
}

/* Clientes */
$clientes = [];
try {
  $clientes = ControladorClientes::ctrMostrarClientesOrdenes(null, null);
  if (!is_array($clientes)) {
    $clientes = [];
  }
} catch (Throwable $e) {
  $clientes = [];
}

/* Nombres por id (opcional) */
$NombreAsesor = '';
$NombreUsuario = '';
try {
  if ($asesorSeleccionadoId) {
    $asesor = Controladorasesores::ctrMostrarAsesoresEleg('id', $asesorSeleccionadoId);
    $NombreAsesor = $asesor['nombre'] ?? '';
  }
  if ($clienteSeleccionadoId) {
    $usuario = ControladorClientes::ctrMostrarClientesOrdenes('id', $clienteSeleccionadoId);
    $NombreUsuario = $usuario['nombre'] ?? '';
  }
} catch (Throwable $e) {
}

if (!class_exists('CotizacionesControlador')) {
  $pathCtrl = __DIR__ . '/../../controladores/cotizaciones.controlador.php';
  $pathMdl = __DIR__ . '/../../modelos/cotizacion.modelo.php';
  if (file_exists($pathCtrl))
    include_once $pathCtrl;
  if (file_exists($pathMdl))
    include_once $pathMdl;
}
if (class_exists('CotizacionesControlador')) {
  CotizacionesControlador::ctrCrearCotizacion();
}

// Generar token único para evitar reenvíos
if (!isset($_SESSION['form_token'])) {
  if (session_status() == PHP_SESSION_NONE) {
    session_start();
  }
  $_SESSION['form_token'] = bin2hex(random_bytes(32));
}
$_SESSION['form_token'] = bin2hex(random_bytes(32));

// Generar UUID para el código QR de esta cotización
$codigoQr = bin2hex(random_bytes(16)); // 32 caracteres hex
?>
<!doctype html>
<html lang="es-MX">

<head>
  <meta charset="utf-8" />
  <title>Cotización - COMERCIALIZADORA EGS</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <!-- Choices.js (selects buscables) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

  <!-- AutoNumeric + Decimal.js -->
  <script src="https://cdn.jsdelivr.net/npm/autonumeric@4.10.5/dist/autoNumeric.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/decimal.js@10.4.3/decimal.min.js"></script>
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- QRCode.js -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

  <style>
    :root {
      --text: #111;
      --muted: #555;
      --border: #cfd6e0;
      --bg: #fff;
    }

    html,
    body {
      background: #1f2937;
      color: var(--text);
      font-family: system-ui, -apple-system, "Segoe UI", Roboto, Arial;
      line-height: 1.34
    }

    .sheet {
      width: 210mm;
      max-width: 100%;
      background: var(--bg);
      margin: 14px auto;
      box-shadow: 0 10px 30px rgba(0, 0, 0, .25);
      border-radius: 10px;
      overflow: hidden
    }

    .toolbar {
      display: flex;
      gap: 8px;
      padding: 8px 12px;
    }

    .btn {
      border: 1px solid #c2cde0;
      background: #fff;
      color: #0b1a33;
      padding: 8px 12px;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 600;
      font-size: 14px
    }

    .btn.primary {
      background: #0a0a0a;
      color: #fff;
      border-color: #0a0a0a
    }

    /* ====== Header B/N ====== */
    .header {
      background: #0a0a0a;
      color: #fff;
      padding: 8mm 10mm 5mm
    }

    .head-grid {
      display: grid;
      grid-template-columns: 1.1fr .9fr;
      gap: 8px
    }

    .brand {
      display: flex;
      align-items: center;
      gap: 10px
    }

    .logo {
      height: 70px;
      max-width: 180px;
      border-radius: 6px;
      overflow: hidden;
      background: #222;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .logo img {
      max-height: 100%;
      max-width: 100%;
      width: auto;
      height: auto;
    }

    .company h1 {
      margin: 0;
      font-size: 22px;
      font-weight: 900;
      letter-spacing: .5px;
      text-transform: uppercase;
    }

    .company .underline {
      margin-top: 2px;
      height: 6px;
      background: linear-gradient(#000, #000) left/100% 3px no-repeat;
      filter: grayscale(100%);
    }

    .addr {
      font-size: 12.6px;
      opacity: .95;
      margin-top: 3px
    }

    .right h2 {
      margin: 0;
      font-size: 18px;
      text-align: right;
      letter-spacing: .3px
    }

    .kv {
      margin-top: 4px;
      display: grid;
      grid-template-columns: 86px 1fr;
      gap: 4px 10px;
      justify-content: end;
      font-size: 12.6px
    }

    .kv label {
      opacity: .9;
      text-align: right
    }

    .bar {
      height: 2px;
      background: #e6e6e6;
      margin-top: 6mm
    }

    /* ====== Choices.js contraste para header oscuro ====== */
    .header .choices {
      width: 100%;
    }

    .header .choices__inner {
      background: #ffffff !important;
      color: #111 !important;
      border: 1px solid #d5dbe6 !important;
      border-radius: 6px !important;
      padding: 6px 8px !important;
      min-height: auto !important;
      box-shadow: none !important;
    }

    .header .choices__list--single .choices__item {
      color: #111 !important;
      opacity: 1 !important;
    }

    .header .choices__placeholder {
      color: #666 !important;
      opacity: 1 !important;
    }

    .header .choices__input {
      background: #ffffff !important;
      color: #111 !important;
    }

    .header .choices__list--dropdown,
    .header .choices__list[aria-expanded] {
      background: #ffffff !important;
      border: 1px solid #d5dbe6 !important;
      box-shadow: 0 6px 18px rgba(0, 0, 0, .12) !important;
    }

    .header .choices__list--dropdown .choices__item {
      color: #111 !important;
    }

    .header .choices.is-open .choices__inner {
      border-color: #9fb2d1 !important;
    }

    .header .choices.is-disabled .choices__inner {
      background: #f3f4f6 !important;
      color: #555 !important;
      opacity: 1 !important;
    }

    .header .choices__list--dropdown .choices__item--selectable.is-highlighted {
      background: #f2f5fb !important;
    }

    /* ====== Contenido ====== */
    .content {
      padding: 7mm 10mm 9mm
    }

    .row2 {
      display: grid;
      grid-template-columns: 2fr 1fr;
      gap: 8px
    }

    .field {
      display: grid;
      gap: 3px
    }

    .label {
      font-size: 12px;
      color: var(--muted);
      letter-spacing: .06em
    }

    input,
    select,
    textarea {
      border: 1px solid var(--border);
      border-radius: 6px;
      padding: 7px 10px;
      font-size: 14px;
      background: #fff;
      width: 100%;
      box-sizing: border-box
    }

    textarea {
      min-height: 60px;
      resize: vertical
    }

    /* ====== Tabla ====== */
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 6px;
      font-size: 13.3px
    }

    thead th {
      background: #0a0a0a;
      color: #fff;
      padding: 6px 8px;
      border: 1px solid #0a0a0a;
      text-align: left
    }

    thead th:nth-child(2),
    thead th:nth-child(3),
    thead th:nth-child(4) {
      text-align: center;
      white-space: nowrap
    }

    thead th:nth-child(1) {
      width: 64%
    }

    thead th:nth-child(2) {
      width: 10%
    }

    thead th:nth-child(3) {
      width: 12%
    }

    thead th:nth-child(4) {
      width: 14%
    }

    tbody td {
      border: 1px solid var(--border);
      padding: 0;
      background: #fff;
      vertical-align: top
    }

    td>input.desc,
    td>input.qty,
    td>input.price {
      display: block;
      width: 100%;
      height: 36px;
      border: 0;
      padding: 0 8px;
      margin: 0;
      font-size: 14px;
      box-sizing: border-box;
    }

    td:nth-child(2)>input.qty,
    td:nth-child(3)>input.price {
      text-align: center;
    }

    td:nth-child(4) {
      padding: 6px 8px;
    }

    .controls {
      display: flex;
      gap: 6px;
      justify-content: flex-end;
      margin-top: 6px
    }

    .btn-sm {
      border: 1px solid #c2cde0;
      background: #fff;
      color: #0b1a33;
      padding: 6px 10px;
      border-radius: 6px;
      cursor: pointer;
      font-size: 12.5px;
      font-weight: 600
    }

    .btn-sm.warn {
      background: #fee2e2;
      border-color: #fecaca;
      color: #991b1b
    }

    .totals {
      margin-top: 6px;
      display: grid;
      grid-template-columns: 1fr 62mm
    }

    .totals table {
      margin: 0 0 0 auto;
      width: 62mm;
      font-size: 13.1px;
      border-collapse: collapse
    }

    .totals td {
      padding: 6px;
      border: 1px solid var(--border);
      background: #fff;
      vertical-align: top
    }

    .totals tr td:first-child {
      text-align: right;
      color: #333
    }

    .totals .grand td {
      font-weight: 800;
      background: #eee
    }

    .disc-note {
      display: none;
      font-size: 11px;
      color: #555;
      line-height: 1;
      margin-top: 2px
    }

    .note {
      margin-top: 6mm;
      font-size: 12.3px;
      color: #444
    }

    .signatures {
      margin-top: 8mm;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 9mm;
      font-size: 12.3px
    }

    .sig-line {
      border-top: 1px solid #888;
      padding-top: 6px;
      text-align: center
    }

    .errorbar {
      display: none;
      margin-bottom: 8px;
      background: #fee2e2;
      color: #7f1d1d;
      border: 1px solid #fecaca;
      padding: 8px 10px;
      border-radius: 8px;
      font-size: 13px
    }

    .invalid {
      outline: 2px solid #fca5a5;
      background: #fff7f7
    }

    .compact thead th {
      padding: 5px
    }

    .compact td>input {
      height: 32px;
      font-size: 13px
    }

    .compact .totals td {
      padding: 5px
    }

    .compact table,
    .compact .totals table {
      font-size: 12.4px
    }

    .compact .content {
      padding-bottom: 7mm
    }

    @page {
      size: letter portrait;
      margin: 9mm;
    }

    @media print {

      html,
      body {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact
      }

      .toolbar,
      .controls,
      .only-screen {
        display: none !important;
      }

      .sheet {
        box-shadow: none;
        margin: 0;
        border-radius: 0
      }

      .header {
        padding: 8mm 9mm 4mm
      }

      .content {
        padding: 6mm 9mm 8mm
      }

      .head-grid {
        gap: 6px
      }

      .row2 {
        gap: 6px
      }

      input,
      select,
      textarea {
        border: 0;
        padding: 0
      }

      #asunto,
      #vigencia {
        text-align: center;
      }

      /* Ocultar UI de Choices al imprimir */
      .choices {
        display: none !important;
      }

      .print-value {
        display: inline
      }

      .print-hide {
        display: none !important;
      }

      table,
      .totals,
      .signatures {
        page-break-inside: avoid;
      }
    }

    /* SweetAlert2 overrides */
    div.swal2-icon {
      width: 3.5em !important;
      height: 3.5em !important;
      margin: 1em auto .5em !important;
    }
  </style>
</head>

<body>

  <div class="only-screen"
    style="padding: 12px; background: #374151; color: white; display: flex; justify-content: center; align-items: center;">
    <label for="empresaSelect" style="margin-right: 10px; font-weight: bold; font-size: 14px;">Empresa:</label>
    <select id="empresaSelect" onchange="cambiarEmpresa()"
      style="width: 320px; padding: 8px 12px; border-radius: 6px; border: 1px solid #6b7280; background: #4b5563; color: white; font-size: 14px;">
      <option value="egs">COMERCIALIZADORA EGS</option>
      <option value="loce">COMERCIALIZADORA LOCE</option>
      <option value="toluca">TOLUCA GRUPO DE VENTA S.A. DE C.V.</option>
      <option value="cartuchos">CARTUCHOS PARA IMPRESORAS</option>
    </select>
  </div>

  <div class="toolbar">
    <button style="margin-left:50px" class="btn success" onclick="printAndSave()">Guardar / Imprimir</button>
  </div>

  <section class="sheet" id="sheet">
    <header class="header">
      <div class="head-grid">
        <div>
          <div class="brand">
            <div class="logo"><img src="vistas/img/plantilla/Captura3.PNG" alt="Logo" /></div>
            <div class="company">
              <h1 contenteditable="true">COMERCIALIZADORA EGS</h1>
              <div class="underline"></div>
            </div>
          </div>
          <div class="addr"><b>Dirección:</b> <span contenteditable="true">Pino Suárez 308 Colonia Santa Clara 50090,
              Toluca México</span></div>
          <div class="tel"><b>Teléfonos:</b> <span contenteditable="true">729-1339897 | 722-2144416</span></div>

        </div>
        <div class="right">
          <h2>COTIZACIÓN</h2>
          <div class="kv">
            <label>FECHA:</label>
            <span id="fechaTxt"></span>

            <label>VENDEDOR:</label>
            <div>
              <select id="vendedorSelect" class="print-hide">
                <option value="">Seleccione…</option>
                <?php if (!empty($asesores) && is_array($asesores)): ?>
                  <?php foreach ($asesores as $a):
                    $id = (int) ($a['id'] ?? 0);
                    $name = htmlspecialchars($a['nombre'] ?? ('Asesor #' . $id), ENT_QUOTES, 'UTF-8');
                    $sel = ($id === $asesorSeleccionadoId) ? 'selected' : '';
                    ?>
                    <option value="<?= $id ?>" <?= $sel ?>><?= $name ?></option>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>
              <span id="vendedorTxt" class="print-value" style="display:none">
                <?php if ($asesorSeleccionadoId && $NombreAsesor)
                  echo htmlspecialchars($NombreAsesor, ENT_QUOTES, 'UTF-8'); ?>
              </span>
            </div>

            <label>CLIENTE:</label>
            <div>
              <div class="row2" style="grid-template-columns:1fr auto;">
                <select id="clienteSelect" class="print-hide">
                  <option value="">Cliente registrado…</option>
                  <?php if (!empty($clientes) && is_array($clientes)): ?>
                    <?php foreach ($clientes as $c):
                      $id = (int) ($c['id'] ?? 0);
                      $name = htmlspecialchars($c['nombre'] ?? ('Cliente #' . $id), ENT_QUOTES, 'UTF-8');
                      $sel = ($id === $clienteSeleccionadoId) ? 'selected' : '';
                      ?>
                      <option value="<?= $id ?>" <?= $sel ?>><?= $name ?></option>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </select>
                <label class="only-screen"
                  style="display:flex;align-items:center;gap:6px;font-size:12px;margin-left:6px">
                  <input type="checkbox" id="chkPaso" <?= $clienteSeleccionadoId ? '' : 'checked' ?> /> de paso
                </label>
              </div>
              <input id="clientePaso" type="text" placeholder="Cliente de paso (texto libre)" class="print-hide"
                style="display:<?= $clienteSeleccionadoId ? 'none' : 'block' ?>; margin-top:6px"
                value="<?= $clienteSeleccionadoId ? '' : htmlspecialchars($NombreUsuario ?: '', ENT_QUOTES, 'UTF-8') ?>">
              <span id="clienteTxt" class="print-value" style="display:none">
                <?php if ($clienteSeleccionadoId && $NombreUsuario)
                  echo htmlspecialchars($NombreUsuario, ENT_QUOTES, 'UTF-8'); ?>
              </span>
            </div>
          </div>
        </div>
      </div>
      <div class="bar"></div>
    </header>

    <main class="content">
      <div id="errorbar" class="errorbar">Hay valores inválidos (resaltados). Corrígelos para recalcular.</div>

      <div class="row2">
        <div class="field">
          <div class="label">Asunto</div>
          <input id="asunto" type="text" placeholder="Cotización de equipos / refacciones / servicio técnico" />
        </div>
        <div class="field">
          <div class="label">Vigencia</div>
          <input id="vigencia" type="text" value="Validez 30 días" />
        </div>
      </div>

      <div class="field" style="margin-top:6px">
        <div class="label">Descripción / Observaciones</div>
        <textarea id="descripcion"
          placeholder="Especifica requisitos, tiempos de entrega, garantías, condiciones de servicio o instalación."></textarea>
      </div>

      <table id="items">
        <thead>
          <tr>
            <th>PRODUCTO / SERVICIO</th>
            <th>CANTIDAD</th>
            <th>PRECIO</th>
            <th>TOTAL</th>
          </tr>
        </thead>
        <tbody id="tbody">
          <tr>
            <td><input class="desc" type="text" placeholder="Nombre del producto o servicio" /></td>
            <td><input class="qty" /></td>
            <td><input class="price" /></td>
            <td class="lineTotal num">$0.00</td>
          </tr>
        </tbody>
      </table>

      <div class="controls">
        <button class="btn-sm" onclick="addRow()">Añadir renglón</button>
        <button class="btn-sm" onclick="dupRow()">Duplicar último</button>
        <button class="btn-sm warn" onclick="delRow()">Eliminar último</button>
      </div>

      <div class="only-screen" style="margin-top:6px">
        <button id="toggleDI" class="btn-sm" type="button">Agregar descuento (%)</button>
      </div>
      <div id="panelDI" class="only-screen" style="display:none; margin-top:6px">
        <div class="label">Descuento (%)</div>
        <input id="discPct" type="number" min="0" max="100" step="1" value="0"
          style="text-align:left; padding-left:4px; padding-right:4px" placeholder="0">
        <input id="ivaFixed" type="hidden" value="16">
      </div>

      <div class="totals">
        <div></div>
        <table>
          <tr>
            <td>Subtotal</td>
            <td class="num" id="sub">$0.00</td>
          </tr>
          <tr id="rowDiscount" style="display:none">
            <td>Descuento <div id="discNote" class="disc-note">(0%)</div>
            </td>
            <td class="num" id="descuento">$0.00</td>
          </tr>
          <tr class="row-iva ">
            <td>IVA (<span id="ivaLbl">16</span>%)</td>
            <td class="num" id="ivaAmt">$0.00</td>
          </tr>
          <tr class="grand">
            <td>Total</td>
            <td class="num" id="grand">$0.00</td>
          </tr>
        </table>
      </div>

      <div class="note">Precios en MXN. Disponibilidad y tiempos sujetos a cambio sin previo aviso. Garantía conforme a
        fabricante o política de servicio. *Esta cotización no incluye conceptos no especificados.</div>

      <div class="signatures">
        <div>
          <div class="sig-line">Firma de Cliente</div>
        </div>
        <div>
          <div class="sig-line">Firma de Vendedor</div>
        </div>
      </div>

      <div
        style="margin-top: 20mm; display: flex; flex-direction: column; align-items: center; gap: 10px; text-align: center;">
        <div id="qrcode-footer"></div>
        <div style="font-size: 12px; color: #555;">
          Escanea para validar cotización<br>
          ID: <strong><?= $codigoQr ?></strong>
        </div>
      </div>
    </main>
  </section>

  <script>
    const formToken = "<?php echo $_SESSION['form_token']; ?>";
    const codigoQrValue = "<?php echo $codigoQr; ?>";
    const validationUrl = window.location.origin + "/validar-cotizacion?codigo=" + codigoQrValue;

    // Generar QR
    new QRCode(document.getElementById("qrcode-footer"), {
      text: validationUrl,
      width: 128,
      height: 128,
      colorDark: "#000000",
      colorLight: "#ffffff",
      correctLevel: QRCode.CorrectLevel.H
    });

    // Fecha
    document.getElementById('fechaTxt').textContent =
      new Date().toLocaleDateString('es-MX', { day: '2-digit', month: '2-digit', year: 'numeric' });

    // Choices.js
    const vendedorChoices = new Choices('#vendedorSelect', {
      searchEnabled: true,
      shouldSort: false,
      removeItemButton: false,
      placeholderValue: 'Seleccione…',
      itemSelectText: ''   // ← quita el "Seleccionar" dentro de cada opción
    });

    const clienteChoices = new Choices('#clienteSelect', {
      searchEnabled: true,
      shouldSort: false,
      removeItemButton: false,
      placeholderValue: 'Cliente registrado…',
      itemSelectText: ''   // ← igual aquí
    });

    // Texto plano para impresión
    const vendedorSelect = document.getElementById('vendedorSelect');
    const vendedorTxt = document.getElementById('vendedorTxt');
    const clienteSelect = document.getElementById('clienteSelect');
    const clientePaso = document.getElementById('clientePaso');
    const clienteTxt = document.getElementById('clienteTxt');
    const chkPaso = document.getElementById('chkPaso');

    function getSelectedText(selectEl) {
      const opt = selectEl && selectEl.selectedOptions ? selectEl.selectedOptions[0] : null;
      return opt ? opt.text : '';
    }
    function refreshPrintTexts() {
      const vendText = getSelectedText(vendedorSelect);
      const vendValue = vendedorSelect.value;
      vendedorTxt.textContent = (vendValue === '') ? 'VENDEDOR GENERAL' : vendText;
      if (chkPaso && chkPaso.checked) {
        clienteTxt.textContent = (clientePaso.value || 'MOSTRADOR');
      } else {
        const cliText = getSelectedText(clienteSelect);
        clienteTxt.textContent = cliText || clienteTxt.textContent || '—';
      }
      vendedorTxt.style.display = 'inline';
      clienteTxt.style.display = 'inline';
    }
    vendedorSelect.addEventListener('change', () => { refreshPrintTexts(); saveDraft(); });
    clienteSelect.addEventListener('change', () => { refreshPrintTexts(); saveDraft(); });

    if (chkPaso) {
      chkPaso.addEventListener('change', () => {
        const usePaso = chkPaso.checked;
        if (usePaso) { clienteChoices.disable(); }
        else { clienteChoices.enable(); }
        clientePaso.style.display = usePaso ? 'block' : 'none';
        refreshPrintTexts(); saveDraft();
      });
      clientePaso.addEventListener('input', () => { refreshPrintTexts(); saveDraft(); });
      if (chkPaso.checked) { clienteChoices.disable(); }
    }

    // Mostrar/Ocultar panel de descuento
    const toggleDI = document.getElementById('toggleDI');
    const panelDI = document.getElementById('panelDI');
    let diVisible = false;
    toggleDI.addEventListener('click', () => {
      diVisible = !diVisible;
      panelDI.style.display = diVisible ? 'block' : 'none';
      toggleDI.textContent = diVisible ? 'Ocultar descuento (%)' : 'Agregar descuento (%)';
    });

    // AutoNumeric: cantidades enteras / precio moneda
    const moneyOpts = { currencySymbol: '$', decimalCharacter: '.', digitGroupSeparator: ',', decimalPlaces: 2, minimumValue: '0', modifyValueOnWheel: false, emptyInputBehavior: 'zero' };
    const qtyOpts = { decimalCharacter: '.', digitGroupSeparator: ',', decimalPlaces: 0, minimumValue: '0', modifyValueOnWheel: false, emptyInputBehavior: 'zero', outputFormat: 'number' };

    [...document.querySelectorAll('#tbody tr')].forEach(initRow);
    function initRow(tr) {
      if (!tr.querySelector('.qty').autoNumeric) { new AutoNumeric(tr.querySelector('.qty'), qtyOpts); }
      if (!tr.querySelector('.price').autoNumeric) { new AutoNumeric(tr.querySelector('.price'), moneyOpts); }
      tr.querySelector('.qty').addEventListener('input', recalc);
      tr.querySelector('.price').addEventListener('input', recalc);
      tr.querySelector('.desc').addEventListener('input', saveDraft);
    }

    function addRow(desc = '', qty = 1, price = 0) {
      const tr = document.createElement('tr');
      tr.innerHTML = `
      <td><input class="desc" type="text" placeholder="Nombre del producto o servicio" value="${desc}"></td>
      <td><input class="qty"></td>
      <td><input class="price"></td>
      <td class="lineTotal num">$0.00</td>`;
      document.getElementById('tbody').appendChild(tr);
      initRow(tr);
      AutoNumeric.getAutoNumericElement(tr.querySelector('.qty')).set(qty);
      AutoNumeric.getAutoNumericElement(tr.querySelector('.price')).set(price);
      recalc();
    }
    function dupRow() {
      const tb = document.getElementById('tbody');
      if (!tb.rows.length) return addRow();
      const last = tb.rows[tb.rows.length - 1];
      const desc = last.querySelector('.desc').value;
      const qty = AutoNumeric.getAutoNumericElement(last.querySelector('.qty')).getNumber();
      const price = AutoNumeric.getAutoNumericElement(last.querySelector('.price')).getNumber();
      addRow(desc, qty, price);
    }
    function delRow() {
      const tb = document.getElementById('tbody');
      if (tb.rows.length) { tb.deleteRow(tb.rows.length - 1); recalc(); saveDraft(); }
    }
    window.addRow = addRow; window.dupRow = dupRow; window.delRow = delRow;

    const fmt = new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN', minimumFractionDigits: 2 });
    function recalc() {
      const rows = [...document.querySelectorAll('#tbody tr')];
      document.querySelector('.sheet').classList.toggle('compact', rows.length > 5);

      let subtotal = new Decimal(0); let hasError = false;
      rows.forEach(tr => {
        const qtyAn = AutoNumeric.getAutoNumericElement(tr.querySelector('.qty'));
        const priceAn = AutoNumeric.getAutoNumericElement(tr.querySelector('.price'));
        const qty = new Decimal(qtyAn.getNumber() || 0);
        const price = new Decimal(priceAn.getNumber() || 0);
        const qtyOk = qty.isFinite() && qty.greaterThanOrEqualTo(0);
        const priceOk = price.isFinite() && price.greaterThanOrEqualTo(0);
        tr.querySelector('.qty').classList.toggle('invalid', !qtyOk);
        tr.querySelector('.price').classList.toggle('invalid', !priceOk);
        if (!qtyOk || !priceOk) { hasError = true; tr.querySelector('.lineTotal').textContent = '$0.00'; return; }
        const line = qty.times(price); subtotal = subtotal.plus(line);
        tr.querySelector('.lineTotal').textContent = fmt.format(line.toNumber());
      });

      const discPct = parseFloat((document.getElementById('discPct')?.value || '0').toString().trim());
      const ivaPct = 16;
      const pctOk = Number.isFinite(discPct) && discPct >= 0 && discPct <= 100;
      if (document.getElementById('discPct')) document.getElementById('discPct').classList.toggle('invalid', !pctOk);
      hasError = hasError || !pctOk;

      const disc = pctOk ? subtotal.times(new Decimal(discPct).div(100)) : new Decimal(0);
      const base = Decimal.max(0, subtotal.minus(disc));
      const iva = base.times(new Decimal(ivaPct).div(100));
      const total = base.plus(iva);

      document.getElementById('sub').textContent = fmt.format(subtotal.toNumber());
      document.getElementById('descuento').textContent = fmt.format(disc.toNumber());
      document.getElementById('ivaLbl').textContent = ivaPct;
      document.getElementById('ivaAmt').textContent = fmt.format(iva.toNumber());
      document.getElementById('grand').textContent = fmt.format(total.toNumber());

      const showDisc = pctOk && discPct > 0;
      document.getElementById('rowDiscount').style.display = showDisc ? 'table-row' : 'none';
      const note = document.getElementById('discNote');
      if (note) {
        note.textContent = `(${Number.isFinite(discPct) ? discPct.toFixed(0) : 0}%)`;
        note.style.display = showDisc ? 'block' : 'none';
      }

      document.getElementById('errorbar').style.display = hasError ? 'block' : 'none';
      refreshPrintTexts(); saveDraft();
    }

    document.getElementById('discPct')?.addEventListener('input', recalc);

    // Persistencia simple
    const DKEY = 'cotizacion_egs_choices_fix_v1';
    function saveDraft() { localStorage.setItem(DKEY, JSON.stringify({ sheet: document.getElementById('sheet').innerHTML })); }
    function loadDraft() { try { const d = JSON.parse(localStorage.getItem(DKEY) || 'null'); if (!d) return; document.getElementById('sheet').innerHTML = d.sheet; window.location.reload(); } catch (e) { } }
    function clearDraft() { localStorage.removeItem(DKEY); location.reload(); }

    // Cambio de empresa
    const empresas = {
      egs: {
        nombre: "COMERCIALIZADORA EGS",
        logo: "vistas/img/plantilla/Captura3.PNG",
        direccion: "Pino Suárez 308 Colonia Santa Clara 50090, Toluca México"
      },
      loce: {
        nombre: "COMERCIALIZADORA LOCE",
        logo: "vistas/img/plantilla/LOCE.png", // Placeholder
        direccion: "CALLE LIBERTAD S/N, JUNTA LOCAL DE CAMINOS, TOLUCA, ESTADO DE MÉXICO, C.P. 50285, MÉXICO." // Placeholder
      },
      toluca: {
        nombre: "TOLUCA GRUPO DE VENTA S.A. DE C.V.",
        logo: "vistas/img/plantilla/logo tgv.jpg", // Placeholder
        direccion: "ANDADOR NICOLAS ROMERO, 308, COLONIA CENTRO, C. P. 50000, TOLUCA, ESTADO DE MÉXICO" // Placeholder
      },
      cartuchos: {
        nombre: "Cartuchos Para Impresoras",
        logo: "vistas/img/plantilla/CPI.png", // Placeholder
        direccion: "Miguel Hidalgo Oriente 502-A Col. Santa Clara, Toluca, Estado de México. C.P. 50090, México" // Placeholder
      }
    };

    function cambiarEmpresa() {
      const select = document.getElementById('empresaSelect');
      const empresaId = select.value;
      const empresaData = empresas[empresaId];

      if (empresaData) {
        const logoImg = document.querySelector('.brand .logo img');

        if (logoImg) {
          // Set up error handler before changing src
          logoImg.onerror = function () {
            console.error('Error loading logo: ' + this.src);
            this.onerror = null; // prevent infinite loops
            this.src = 'vistas/img/plantilla/placeholder.png';
            this.alt = 'Logo no disponible';
          };

          logoImg.src = empresaData.logo;
          logoImg.alt = 'Logo ' + empresaData.nombre;
        }

        document.querySelector('.company h1').textContent = empresaData.nombre;
        document.querySelector('.addr span').textContent = empresaData.direccion;
        document.title = 'Cotización - ' + empresaData.nombre;
      }
    }
    window.cambiarEmpresa = cambiarEmpresa;


    // Init
    document.getElementById('fechaTxt').textContent =
      new Date().toLocaleDateString('es-MX', { day: '2-digit', month: '2-digit', year: 'numeric' });
    refreshPrintTexts(); recalc();

    async function printAndSave() {
      // 1. Imprimir
      window.print();
    }

    window.onafterprint = async function () {
      // 2. Confirmar guardado
      const result = await Swal.fire({
        title: '¿Se imprimió correctamente?',
        text: "Si confirmas, la cotización se guardará en el sistema.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, guardar',
        cancelButtonText: 'No, cancelar'
      });

      if (result.isConfirmed) {
        submitCotizacion();
      }
    };

    function submitCotizacion() {
      // Recopilar datos
      const id_cliente = document.getElementById('clienteSelect').value;
      // Nombre cliente: si es de paso, toma el input, si no, el texto del select
      let nombre_cliente = document.getElementById('clienteTxt').textContent.trim();

      const id_vendedor = document.getElementById('vendedorSelect').value;
      const empresa = document.getElementById('empresaSelect').value;

      // Productos
      const rows = [...document.querySelectorAll('#tbody tr')];
      const productos = rows.map(tr => {
        return {
          descripcion: tr.querySelector('.desc').value,
          cantidad: AutoNumeric.getAutoNumericElement(tr.querySelector('.qty')).getNumber(),
          precio: AutoNumeric.getAutoNumericElement(tr.querySelector('.price')).getNumber(),
          total: tr.querySelector('.lineTotal').textContent.replace(/[$,]/g, '')
        };
      });

      // Totales
      const impuesto = document.getElementById('ivaAmt').textContent.replace(/[$,]/g, '');
      const neto = document.getElementById('sub').textContent.replace(/[$,]/g, '');
      const total = document.getElementById('grand').textContent.replace(/[$,]/g, '');
      const asunto = document.getElementById('asunto').value;
      const vigencia = document.getElementById('vigencia').value;
      const observaciones = document.getElementById('descripcion').value;
      const descuento_porcentaje = document.getElementById('discPct') ? document.getElementById('discPct').value : 0;

      // Crear formulario
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = ''; // Enviar a la misma página

      const fields = {
        form_token: formToken,
        id_cliente,
        nombre_cliente,
        id_vendedor,
        empresa,
        listaProductos: JSON.stringify(productos),
        impuesto,
        neto,
        total,
        asunto,
        vigencia,
        observaciones,
        descuento_porcentaje,
        codigo_qr: codigoQrValue
      };

      for (const key in fields) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = fields[key];
        form.appendChild(input);
      }

      document.body.appendChild(form);
      form.submit();
    }
  </script>
</body>

</html>