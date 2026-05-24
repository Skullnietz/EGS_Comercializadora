<div id="posCajeroRoot" class="pos-page">
  <?php include __DIR__ . '/crm-styles.php'; ?>
  <style>
    .pos-page.content { background: var(--crm-bg); padding: 14px 15px 24px; }
    .pos-page .content-header h1 { color: var(--crm-text); font-weight: 800; }
    .pos-page .content-header h1 small { color: var(--crm-muted); font-weight: 500; }
    .pos-toolbar {
      display: flex; flex-wrap: wrap; gap: 10px; align-items: center; margin-bottom: 16px;
    }
    .pos-scanner-wrap {
      flex: 1; min-width: 280px; display: flex; gap: 8px; align-items: center;
      background: #fff; border: 2px solid var(--crm-accent); border-radius: 12px; padding: 10px 14px;
      box-shadow: 0 4px 14px rgba(99, 102, 241, .12);
    }
    .pos-scanner-wrap i { color: var(--crm-accent); font-size: 20px; }
    .pos-scanner-wrap input {
      border: none; outline: none; flex: 1; font-size: 16px; font-weight: 600; background: transparent;
    }
    .pos-layout { display: grid; grid-template-columns: 1fr 360px; gap: 16px; align-items: start; }
    @media (max-width: 991px) {
      .pos-layout { grid-template-columns: 1fr; }
    }
    .pos-cart-table { width: 100%; margin: 0; }
    .pos-cart-table th {
      font-size: 11px; text-transform: uppercase; letter-spacing: .04em;
      color: var(--crm-muted); border-bottom: 1px solid var(--crm-border) !important;
    }
    .pos-cart-table td { vertical-align: middle !important; font-size: 13px; }
    .pos-cart-empty {
      text-align: center; padding: 48px 20px; color: var(--crm-muted);
    }
    .pos-cart-empty i { font-size: 42px; opacity: .35; margin-bottom: 10px; display: block; }
    .pos-qty-controls { display: inline-flex; align-items: center; gap: 4px; }
    .pos-qty-controls button {
      width: 28px; height: 28px; border-radius: 6px; border: 1px solid var(--crm-border);
      background: #fff; font-weight: 700; line-height: 1; padding: 0;
    }
    .pos-qty-controls input {
      width: 48px; text-align: center; border: 1px solid var(--crm-border);
      border-radius: 6px; height: 28px; font-weight: 700;
    }
    .pos-checkout { position: sticky; top: 70px; }
    .pos-checkout .form-group { margin-bottom: 12px; }
    .pos-checkout label {
      font-size: 12px; font-weight: 700; color: var(--crm-text2); margin-bottom: 5px; display: block;
    }
    .pos-total-box {
      background: linear-gradient(135deg, #6366f1, #818cf8); color: #fff;
      border-radius: 12px; padding: 16px; margin: 14px 0;
    }
    .pos-total-box .pos-total-label { font-size: 12px; opacity: .85; font-weight: 600; }
    .pos-total-box .pos-total-value { font-size: 32px; font-weight: 800; line-height: 1.1; }
    .pos-line-sub { font-size: 12px; color: var(--crm-muted); }
    .pos-line-code { font-family: monospace; font-size: 11px; color: var(--crm-accent); }
    .pos-monedero {
      display: none; background: #fffbeb; border: 1px solid #fde68a;
      border-radius: 10px; padding: 10px; margin-bottom: 10px;
    }
    .pos-efectivo-row { display: none; gap: 8px; }
    .pos-efectivo-row.active { display: flex; }
    .pos-efectivo-row .form-group { flex: 1; margin-bottom: 0; }
    .pos-kbd {
      border: 1px solid #cbd5e1; border-radius: 4px; padding: 1px 5px;
      font-size: 10px; background: #fff; color: var(--crm-muted);
    }
    .pos-btn-cobrar {
      width: 100%; padding: 14px; font-size: 16px; font-weight: 800;
      border-radius: 10px; border: none; background: #16a34a; color: #fff;
    }
    .pos-btn-cobrar:hover { background: #15803d; color: #fff; }
    .pos-btn-cobrar:disabled { background: #94a3b8; cursor: not-allowed; }
    .pos-nuevo-cliente {
      display: none; background: #eff6ff; border: 1px solid #93c5fd;
      border-radius: 10px; padding: 12px; margin-bottom: 10px;
    }
    .tablaProductosPos_wrapper .dataTables_filter input {
      border-radius: 10px !important; border: 1px solid var(--crm-border) !important;
      min-width: 220px; height: 36px; padding: 6px 12px;
    }
    .pos-modal-catalogo .modal-dialog { width: 92%; max-width: 960px; }
    .pos-modal-catalogo .modal-content {
      border-radius: 14px; border: 1px solid var(--crm-border); overflow: hidden;
    }
    .pos-modal-catalogo .modal-header {
      background: linear-gradient(135deg, #6366f1, #818cf8); color: #fff; border: 0;
    }
    .pos-modal-catalogo .modal-header .close { color: #fff; opacity: .85; }
    .pos-modal-catalogo .modal-body { padding: 16px 18px 12px; background: var(--crm-bg); }
    .pos-modal-hint { font-size: 12px; color: var(--crm-muted); margin-bottom: 12px; }
    .pos-success-banner {
      display: none; align-items: center; flex-wrap: wrap; gap: 10px;
      background: #ecfdf5; border: 1px solid #86efac; border-radius: 12px;
      padding: 12px 16px; margin-bottom: 16px;
    }
    .pos-success-banner.show { display: flex; }
    .pos-success-banner strong { color: #166534; flex: 1; min-width: 180px; }
    .pos-success-actions { display: flex; flex-wrap: wrap; gap: 8px; }
    .pos-success-actions .btn { border-radius: 8px; font-weight: 700; }
    #posToastHost {
      position: fixed; top: 70px; right: 16px; z-index: 10050;
      display: flex; flex-direction: column; gap: 8px; pointer-events: none;
      max-width: min(360px, calc(100vw - 32px));
    }
    .pos-toast {
      pointer-events: auto; display: flex; align-items: flex-start; gap: 10px;
      background: #fff; border: 1px solid var(--crm-border); border-left-width: 4px;
      border-radius: 10px; padding: 12px 14px; box-shadow: var(--crm-shadow-lg);
      animation: posToastIn .28s ease-out;
    }
    .pos-toast.pos-toast-success { border-left-color: #16a34a; }
    .pos-toast.pos-toast-error { border-left-color: #dc2626; }
    .pos-toast.pos-toast-warning { border-left-color: #d97706; }
    .pos-toast.pos-toast-info { border-left-color: #6366f1; }
    .pos-toast-icon { font-size: 16px; margin-top: 1px; }
    .pos-toast-success .pos-toast-icon { color: #16a34a; }
    .pos-toast-error .pos-toast-icon { color: #dc2626; }
    .pos-toast-warning .pos-toast-icon { color: #d97706; }
    .pos-toast-info .pos-toast-icon { color: #6366f1; }
    .pos-toast-msg { font-size: 13px; font-weight: 600; color: var(--crm-text); line-height: 1.35; }
    @keyframes posToastIn {
      from { opacity: 0; transform: translateX(12px); }
      to { opacity: 1; transform: translateX(0); }
    }
  </style>

  <div class="crm-section">
    <div class="crm-section-icon"><i class="fa-solid fa-cash-register"></i></div>
    <div>
      <h3>Cajero — venta rápida</h3>
      <p>
        Escanea, busca o agrega productos del catálogo ·
        <span class="pos-kbd">F2</span> escanear ·
        <span class="pos-kbd">Ctrl+B</span> catálogo ·
        <span class="pos-kbd">Ctrl+Enter</span> cobrar
      </p>
    </div>
  </div>

  <div id="posToastHost" aria-live="polite"></div>

  <div id="posVentaExitosa" class="pos-success-banner">
    <strong><i class="fa-solid fa-circle-check"></i> <span id="posVentaExitosaMsg">Venta registrada</span></strong>
    <div class="pos-success-actions">
      <button type="button" class="btn btn-success btn-sm" id="posBtnNuevaVenta"><i class="fa-solid fa-plus"></i> Nueva venta</button>
      <button type="button" class="btn btn-default btn-sm" id="posBtnImprimirTicket"><i class="fa-solid fa-print"></i> Imprimir ticket</button>
      <a href="index.php?ruta=ventasD" class="btn btn-link btn-sm" id="posBtnVerHistorial">Ver historial</a>
    </div>
  </div>

  <form role="form" method="post" class="formularioVenta pos-form" id="posFormVenta" enctype="multipart/form-data">
    <input type="hidden" name="empresa" id="posEmpresa" value="<?php echo intval($_SESSION['empresa']); ?>">
    <input type="hidden" name="nversion" value="0">
    <input type="hidden" name="nuevoImpuestoVenta" value="0">
    <input type="hidden" name="precioNeto" value="0">
    <input type="hidden" id="listaProductos" name="listaProductos" value="">
    <input type="hidden" id="listaMetodoPago" name="listaMetodoPago" value="">
    <input type="hidden" id="totalVenta" name="totalVenta" value="0">
    <input type="hidden" id="seleccionarCliente" name="seleccionarCliente" value="">
    <input type="hidden" id="id_cliente" name="id_cliente" value="0">
    <input type="hidden" id="nombreCliente" name="nombreCliente" value="">
    <input type="hidden" id="correoClienteVenta" name="correo" value="">
    <input type="hidden" id="tipoDePerfil" value="<?php echo htmlspecialchars($_SESSION['perfil']); ?>">
    <input type="hidden" id="id_empresa" value="<?php echo intval($_SESSION['empresa']); ?>">

    <div class="pos-toolbar">
      <div class="pos-scanner-wrap">
        <i class="fa-solid fa-barcode"></i>
        <input type="text" id="posScannerInput" placeholder="Escanear código de barras, QR o escribir SKU..." autocomplete="off">
        <button type="button" class="btn btn-default btn-sm" id="posBtnEnfocarScanner" title="F2"><i class="fa-solid fa-crosshairs"></i></button>
      </div>
      <button type="button" class="crm-quick" id="posBtnAbrirCatalogo"><span class="crm-quick-icon"><i class="fa-solid fa-search"></i></span> Buscar catálogo</button>
      <button type="button" class="crm-quick" id="posBtnVaciarCarrito"><span class="crm-quick-icon"><i class="fa-solid fa-trash"></i></span> Vaciar</button>
    </div>

    <div class="pos-layout">
      <div class="crm-card">
        <div class="crm-card-head">
          <h4 class="crm-card-title"><i class="fa-solid fa-cart-shopping"></i> Carrito <span id="posCartCount" class="label label-primary" style="margin-left:6px">0</span></h4>
        </div>
        <div class="crm-card-body-flush" style="padding:0">
          <div id="posCartEmpty" class="pos-cart-empty">
            <i class="fa-solid fa-cart-plus"></i>
            Escanea un producto o búscalo en el catálogo para comenzar
          </div>
          <div class="table-responsive" id="posCartWrap" style="display:none">
            <table class="table pos-cart-table" id="posCartTable">
              <thead>
                <tr>
                  <th>Producto</th>
                  <th style="width:110px">Cant.</th>
                  <th style="width:90px">Precio</th>
                  <th style="width:90px">Subtotal</th>
                  <th style="width:40px"></th>
                </tr>
              </thead>
              <tbody id="posCartBody"></tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="crm-card pos-checkout">
        <div class="crm-card-head">
          <h4 class="crm-card-title"><i class="fa-solid fa-receipt"></i> Cobro</h4>
        </div>
        <div class="crm-card-body">
          <div class="form-group">
            <label><i class="fas fa-user"></i> Cliente <span style="color:#dc2626">*</span></label>
            <select id="egs_clienteVentaPOS" required>
              <option value="">Buscar cliente...</option>
              <option value="nuevo">+ Agregar nuevo cliente</option>
              <?php
                if (is_array($clientesPos)) {
                  foreach ($clientesPos as $clPos) {
                    $idCl = intval($clPos['id']);
                    $nomCl = htmlspecialchars($clPos['nombre'], ENT_QUOTES, 'UTF-8');
                    echo '<option value="'.$idCl.'" data-nombre="'.$nomCl.'">'.$nomCl.'</option>';
                  }
                }
              ?>
            </select>
          </div>

          <div id="posNuevoClienteSection" class="pos-nuevo-cliente">
            <div style="font-weight:700;color:#1e40af;margin-bottom:8px"><i class="fas fa-user-plus"></i> Nuevo cliente</div>
            <input type="text" class="form-control" id="posNuevoClienteNombre" placeholder="Nombre *" style="margin-bottom:8px">
            <input type="text" class="form-control" id="posNuevoClienteWhatsapp" placeholder="WhatsApp *">
            <small id="posNuevoClienteError" style="display:none;color:#dc2626"></small>
          </div>

          <div class="form-group">
            <label><i class="fas fa-user-tie"></i> Asesor</label>
            <select class="form-control" name="asesor" id="posAsesor" required>
              <option value="">Seleccionar asesor</option>
              <?php
                if (is_array($asesoresPos)) {
                  foreach ($asesoresPos as $asesorPos) {
                    $idAse = intval($asesorPos['id']);
                    $nomAse = htmlspecialchars($asesorPos['nombre'], ENT_QUOTES, 'UTF-8');
                    $sel = ($idAse === intval($idAsesorDefault)) ? ' selected' : '';
                    echo '<option value="'.$idAse.'"'.$sel.'>'.$nomAse.'</option>';
                  }
                }
              ?>
            </select>
          </div>

          <div id="egsMonederoVentaRapida" class="pos-monedero">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px">
              <span style="font-weight:600;color:#b45309;font-size:13px"><i class="fa-solid fa-wallet"></i> Monedero</span>
              <span style="font-size:12px;color:#64748b">Saldo: <b id="egsSaldoMonederoLabel" style="color:#16a34a">$0.00</b></span>
            </div>
            <div class="input-group input-group-sm">
              <span class="input-group-addon"><i class="fa-solid fa-coins"></i></span>
              <input type="number" id="egsMontoCanjeVenta" name="montoCanjeElectronicoVenta" class="form-control" min="0" step="0.01" value="0" placeholder="Monto a aplicar">
              <span class="input-group-btn">
                <button type="button" id="egsAplicarMaxMonedero" class="btn btn-warning btn-sm">Usar todo</button>
              </span>
            </div>
            <small id="egsMonederoMsg" style="color:#64748b;display:block;margin-top:4px"></small>
          </div>

          <div style="display:flex;justify-content:space-between;font-size:13px;color:var(--crm-text2);margin-bottom:4px">
            <span>Subtotal</span><span id="posSubtotalLabel">$0.00</span>
          </div>
          <div style="display:flex;justify-content:space-between;font-size:13px;color:var(--crm-text2);margin-bottom:8px">
            <span>Descuento</span><span id="posDescuentoLabel">$0.00</span>
          </div>

          <div class="form-group" style="margin-bottom:8px">
            <label>Descuento %</label>
            <input type="number" class="form-control" id="posDescuentoPct" name="nuevodescuentoPorcentaje" min="0" max="100" step="0.01" value="0" placeholder="0">
          </div>

          <div class="pos-total-box">
            <div class="pos-total-label">Total a cobrar</div>
            <div class="pos-total-value" id="posTotalDisplay">$0.00</div>
            <input type="hidden" id="nuevoTotalVenta" name="nuevoTotalVenta" value="0" required>
          </div>

          <div class="form-group">
            <label>Método de pago</label>
            <select class="form-control" id="nuevoMetodoPago" name="nuevoMetodoPago" required>
              <option value="">Seleccionar...</option>
              <option value="efectivo">Efectivo</option>
              <option value="tarjetaCredito">Tarjeta crédito</option>
              <option value="tarjetaDebito">Tarjeta débito</option>
              <option value="pagoElectronico">Pago electrónico</option>
              <option value="mercadoPago">Mercado Pago</option>
              <option value="paypal">PayPal</option>
              <option value="cheque">Cheque</option>
            </select>
          </div>

          <div class="pos-efectivo-row" id="posEfectivoRow">
            <div class="form-group">
              <label>Recibido</label>
              <input type="number" class="form-control" id="posEfectivoRecibido" min="0" step="0.01" placeholder="0.00">
            </div>
            <div class="form-group">
              <label>Cambio</label>
              <input type="text" class="form-control" id="posEfectivoCambio" readonly placeholder="0.00">
            </div>
          </div>

          <div class="form-group" id="posTransaccionWrap" style="display:none">
            <label>Referencia / transacción</label>
            <input type="text" class="form-control" id="nuevoCodigoTransaccion" placeholder="Código o referencia">
          </div>

          <button type="submit" class="pos-btn-cobrar" id="posBtnCobrar" disabled>
            <i class="fa-solid fa-check"></i> Cobrar
          </button>
        </div>
      </div>
    </div>
  </form>
</div>

<div class="modal fade pos-modal-catalogo" id="modalPosCatalogo" tabindex="-1" role="dialog" aria-labelledby="modalPosCatalogoLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="modalPosCatalogoLabel"><i class="fa-solid fa-box"></i> Buscar producto</h4>
      </div>
      <div class="modal-body">
        <p class="pos-modal-hint">
          Escribe nombre o código en el buscador · clic en <strong>Agregar</strong> ·
          <span class="pos-kbd">Esc</span> cerrar
        </p>
        <table class="table table-bordered table-striped dt-responsive tablaProductosPos" width="100%">
          <thead>
            <tr>
              <th>#</th>
              <th>Imagen</th>
              <th>Código</th>
              <th>Producto</th>
              <th>Stock</th>
              <th>Acciones</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>
