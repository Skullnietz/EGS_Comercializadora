<div class="content-wrapper">

  <section class="content-header">
    <h1>Inventario IT <small>Gestión de productos</small></h1>
    <ol class="breadcrumb">
      <li><a href="index.php?ruta=inicio"><i class="fas fa-dashboard"></i> Inicio</a></li>
      <li class="active">Inventario</li>
    </ol>
  </section>

  <section class="content inv-page">
    <?php include __DIR__ . '/crm-styles.php'; ?>
    <style>
      .inv-page.content { background: var(--crm-bg); padding: 14px 15px 24px; }
      .inv-page .content-header h1 { color: var(--crm-text); font-weight: 800; }
      .inv-page .content-header h1 small { color: var(--crm-muted); font-weight: 500; }
      .inv-toolbar { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; margin-bottom: 16px; }
      .inv-scanner-wrap {
        flex: 1; min-width: 240px; display: flex; gap: 8px; align-items: center;
        background: #fff; border: 2px solid var(--crm-accent); border-radius: 12px; padding: 8px 12px;
      }
      .inv-scanner-wrap i { color: var(--crm-accent); font-size: 18px; }
      .inv-scanner-wrap input {
        border: none; outline: none; flex: 1; font-size: 14px; font-weight: 600; background: transparent;
      }
      .inv-filtros { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 14px; }
      .inv-filtro-chip {
        border: 1px solid var(--crm-border); background: #fff; border-radius: 999px;
        padding: 5px 14px; font-size: 12px; font-weight: 600; color: var(--crm-text2); cursor: pointer;
      }
      .inv-filtro-chip.active, .inv-filtro-chip:hover {
        background: var(--crm-accent); border-color: var(--crm-accent); color: #fff;
      }
      .inv-badge { display: inline-block; padding: 4px 10px; border-radius: 999px; font-size: 12px; font-weight: 700; }
      .inv-badge-danger { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
      .inv-badge-warning { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
      .inv-badge-success { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
      .inv-thumb { object-fit: cover; border-radius: 8px; border: 1px solid var(--crm-border); }
      .inv-prod-title { color: var(--crm-text); font-size: 13px; }
      .inv-prod-cat { color: var(--crm-muted); }
      .inv-codigo { cursor: pointer; font-family: monospace; font-weight: 600; color: var(--crm-accent); }
      .inv-codigo:hover { text-decoration: underline; }
      .inv-copy-icon { font-size: 10px; opacity: .6; }
      .inv-usd { color: var(--crm-muted); font-size: 12px; }
      .inv-muted { color: var(--crm-muted); }
      .inv-actions .btn { margin: 1px; border-radius: 6px; }
      .inv-tc-chip {
        display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px;
        background: #eef2ff; border: 1px solid #c7d2fe; border-radius: 10px;
        font-size: 12px; font-weight: 600; color: #4338ca; cursor: pointer; border: none;
      }
      .inv-kbd { border: 1px solid #cbd5e1; border-radius: 4px; padding: 1px 5px; font-size: 10px; background: #fff; }
      .tablaInventarioProductos tbody tr.inv-row-highlight { background: #eef2ff !important; }

      /* DataTables — controles y paginación */
      .tablaInventarioProductos_wrapper { padding: 16px 18px 18px; }
      .tablaInventarioProductos_wrapper .dataTables_length,
      .tablaInventarioProductos_wrapper .dataTables_filter { margin-bottom: 12px; }
      .tablaInventarioProductos_wrapper .dataTables_length label,
      .tablaInventarioProductos_wrapper .dataTables_filter label {
        color: var(--crm-text2); font-size: 12px; font-weight: 700;
      }
      .tablaInventarioProductos_wrapper .dataTables_length select {
        border: 1px solid var(--crm-border) !important; border-radius: 8px;
        background: #fff; color: var(--crm-text); height: 34px;
        padding: 4px 26px 4px 10px; margin: 0 6px; font-size: 12px; font-weight: 600;
      }
      .tablaInventarioProductos_wrapper .dataTables_filter input {
        border: 1px solid var(--crm-border) !important; border-radius: 10px;
        background: #fff; color: var(--crm-text); height: 36px; min-width: 220px;
        padding: 6px 12px; font-size: 12px; font-weight: 600;
      }
      .tablaInventarioProductos_wrapper .dataTables_length select:focus,
      .tablaInventarioProductos_wrapper .dataTables_filter input:focus {
        outline: none; border-color: #a5b4fc !important;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, .12);
      }
      .tablaInventarioProductos_wrapper .dataTables_info {
        color: var(--crm-muted); font-size: 12px; font-weight: 600; padding-top: 14px;
      }
      .tablaInventarioProductos_wrapper .dataTables_paginate { margin-top: 14px; text-align: right; }
      .tablaInventarioProductos_wrapper .dataTables_paginate ul.pagination > li.paginate_button > a,
      .tablaInventarioProductos_wrapper .dataTables_paginate .pagination > li > a,
      .tablaInventarioProductos_wrapper .dataTables_paginate .pagination > li > span {
        border-radius: 8px !important; border: 1px solid #dbe3ef !important;
        background: #fff !important; color: #334155 !important;
        margin-left: 6px; padding: 6px 12px !important; font-weight: 600;
      }
      .tablaInventarioProductos_wrapper .dataTables_paginate ul.pagination > li.paginate_button > a:hover,
      .tablaInventarioProductos_wrapper .dataTables_paginate .pagination > li > a:hover {
        background: #eef2ff !important; border-color: #a5b4fc !important; color: #3730a3 !important;
      }
      .tablaInventarioProductos_wrapper .dataTables_paginate ul.pagination > li.paginate_button.active > a,
      .tablaInventarioProductos_wrapper .dataTables_paginate ul.pagination > li.paginate_button.active > a:hover,
      .tablaInventarioProductos_wrapper .dataTables_paginate ul.pagination > li.paginate_button.active > a:focus,
      .tablaInventarioProductos_wrapper .dataTables_paginate .pagination > .active > a,
      .tablaInventarioProductos_wrapper .dataTables_paginate .pagination > .active > a:hover,
      .tablaInventarioProductos_wrapper .dataTables_paginate .pagination > .active > a:focus {
        background: var(--crm-accent) !important; border-color: var(--crm-accent) !important; color: #fff !important;
      }
      .tablaInventarioProductos_wrapper .dataTables_paginate ul.pagination > li.paginate_button.disabled > a,
      .tablaInventarioProductos_wrapper .dataTables_paginate ul.pagination > li.paginate_button.disabled > a:hover,
      .tablaInventarioProductos_wrapper .dataTables_paginate ul.pagination > li.paginate_button.disabled > a:focus,
      .tablaInventarioProductos_wrapper .dataTables_paginate .pagination > .disabled > a {
        background: #f8fafc !important; border-color: #e2e8f0 !important; color: #94a3b8 !important;
        cursor: not-allowed;
      }
      .tablaInventarioProductos_wrapper .dataTables_paginate ul.pagination > li.paginate_button {
        background: transparent !important; border: 0 !important; box-shadow: none !important;
      }
      .inv-dt-bottom {
        display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between;
        gap: 10px; margin-top: 8px; padding-top: 8px; border-top: 1px solid #f1f5f9;
      }
      @media (max-width: 767px) {
        .tablaInventarioProductos_wrapper .dataTables_length,
        .tablaInventarioProductos_wrapper .dataTables_filter { width: 100%; }
        .tablaInventarioProductos_wrapper .dataTables_filter input { width: 100%; min-width: 0; }
        .inv-dt-bottom { flex-direction: column; align-items: stretch; }
        .tablaInventarioProductos_wrapper .dataTables_paginate { text-align: center; }
      }
    </style>

    <div class="crm-section">
      <div class="crm-section-icon"><i class="fa-solid fa-boxes-stacked"></i></div>
      <div>
        <h3>Inventario de productos informáticos</h3>
        <p>Vista simplificada para mantener stock y precios al día · <span class="inv-kbd">F2</span> escanear · <span class="inv-kbd">Ctrl+N</span> nuevo</p>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-3 col-sm-6" style="margin-bottom:16px">
        <div class="crm-kpi" style="background:linear-gradient(135deg,#6366f1,#818cf8)">
          <i class="fa-solid fa-cubes crm-kpi-icon"></i>
          <div class="crm-kpi-label">Productos activos</div>
          <div class="crm-kpi-value" id="kpiTotalActivos"><?php echo intval($resumenInventario['total_activos'] ?? 0); ?></div>
          <div class="crm-kpi-bar"><span style="width:100%"></span></div>
        </div>
      </div>
      <div class="col-lg-3 col-sm-6" style="margin-bottom:16px">
        <div class="crm-kpi" style="background:linear-gradient(135deg,#f59e0b,#fbbf24)">
          <i class="fa-solid fa-triangle-exclamation crm-kpi-icon"></i>
          <div class="crm-kpi-label">Stock bajo</div>
          <div class="crm-kpi-value" id="kpiStockBajo"><?php echo intval($resumenInventario['stock_bajo'] ?? 0); ?></div>
          <div class="crm-kpi-sub">≤ 15 unidades</div>
        </div>
      </div>
      <div class="col-lg-3 col-sm-6" style="margin-bottom:16px">
        <div class="crm-kpi" style="background:linear-gradient(135deg,#ef4444,#f87171)">
          <i class="fa-solid fa-circle-xmark crm-kpi-icon"></i>
          <div class="crm-kpi-label">Sin stock</div>
          <div class="crm-kpi-value" id="kpiSinStock"><?php echo intval($resumenInventario['sin_stock'] ?? 0); ?></div>
          <div class="crm-kpi-sub">Requieren reposición</div>
        </div>
      </div>
      <div class="col-lg-3 col-sm-6" style="margin-bottom:16px">
        <div class="crm-kpi" style="background:linear-gradient(135deg,#059669,#10b981)">
          <i class="fa-solid fa-sack-dollar crm-kpi-icon"></i>
          <div class="crm-kpi-label">Valor inventario (MXN)</div>
          <div class="crm-kpi-value" id="kpiValorInventario">$<?php echo number_format(floatval($resumenInventario['valor_inventario'] ?? 0), 0); ?></div>
          <div class="crm-kpi-sub">Precio × stock disponible</div>
        </div>
      </div>
    </div>

    <div class="inv-toolbar">
      <div class="inv-scanner-wrap">
        <i class="fa-solid fa-barcode"></i>
        <input type="text" id="invScannerInput" placeholder="Escanear código de barras o escribir SKU..." autocomplete="off">
        <button type="button" class="btn btn-default btn-sm" id="btnEnfocarScanner" title="F2"><i class="fa-solid fa-crosshairs"></i></button>
      </div>
      <button type="button" class="crm-quick crm-quick-primary" data-toggle="modal" data-target="#modalAgregarProducto">
        <span class="crm-quick-icon"><i class="fa-solid fa-plus"></i></span> Agregar producto
      </button>
      <a href="vistas/modulos/descargar-reporte-productos.php?reporte=productos" class="crm-quick">
        <span class="crm-quick-icon"><i class="fa-solid fa-file-excel"></i></span> Exportar Excel
      </a>
      <?php if ($esAdminInventario): ?>
      <button type="button" class="inv-tc-chip" data-toggle="modal" data-target="#modalTipoCambio">
        <i class="fa-solid fa-dollar-sign"></i> TC: <span id="labelTipoCambio"><?php echo number_format($tipoCambioUsd, 2); ?></span> MXN/USD
      </button>
      <?php endif; ?>
    </div>

    <div class="inv-filtros">
      <button type="button" class="inv-filtro-chip active" data-filtro="todos">Todos</button>
      <button type="button" class="inv-filtro-chip" data-filtro="bajo">Stock bajo</button>
      <button type="button" class="inv-filtro-chip" data-filtro="sin">Sin stock</button>
      <button type="button" class="inv-filtro-chip" data-filtro="activos">Activos</button>
      <button type="button" class="inv-filtro-chip" data-filtro="inactivos">Inactivos</button>
    </div>

    <div class="crm-card">
      <div class="crm-card-head">
        <h4 class="crm-card-title"><i class="fa-solid fa-list"></i> Catálogo de inventario</h4>
      </div>
      <div class="crm-card-body-flush">
        <table class="table table-hover crm-table tablaInventarioProductos" width="100%">
          <thead>
            <tr>
              <th>#</th>
              <th>Imagen</th>
              <th>Código</th>
              <th>Producto</th>
              <th>Stock</th>
              <th>Precio MXN</th>
              <th>Ref. USD</th>
              <th>Acciones</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>

    <input type="hidden" id="tipoDePerfil" value="<?php echo htmlspecialchars($_SESSION['perfil']); ?>">
    <input type="hidden" id="id_empresa" value="<?php echo intval($_SESSION['empresa']); ?>">
    <input type="hidden" id="invTipoCambio" value="<?php echo number_format($tipoCambioUsd, 4, '.', ''); ?>">
    <input type="hidden" id="invDeepLinkCodigo" value="<?php echo $codigoDeepLink; ?>">

  </section>
</div>

<!-- Modal ajustar stock -->
<div id="modalAjustarStock" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header" style="background:#6366f1;color:#fff">
        <button type="button" class="close" data-dismiss="modal" style="color:#fff">&times;</button>
        <h4 class="modal-title">Ajustar stock</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" id="stockProductoId">
        <p>Stock actual: <strong id="stockActualLabel">0</strong></p>
        <div class="form-group">
          <label>Ajuste (+/- unidades)</label>
          <input type="number" class="form-control" id="stockAjusteInput" value="0" step="1">
        </div>
        <div class="form-group">
          <label>Motivo (opcional)</label>
          <input type="text" class="form-control" id="stockMotivoInput" placeholder="Entrada, salida, conteo...">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="btnGuardarAjusteStock">Guardar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal QR -->
<div id="modalVerQr" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header" style="background:#6366f1;color:#fff">
        <button type="button" class="close" data-dismiss="modal" style="color:#fff">&times;</button>
        <h4 class="modal-title">Código QR del producto</h4>
      </div>
      <div class="modal-body text-center" id="qrPrintArea">
        <p><strong id="qrProductoTitulo"></strong></p>
        <p class="text-muted" id="qrProductoCodigo"></p>
        <div id="qrCodeContainer" style="display:inline-block;margin:10px auto"></div>
        <p id="qrProductoPrecio"></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" id="btnImprimirQr"><i class="fa-solid fa-print"></i> Imprimir</button>
      </div>
    </div>
  </div>
</div>

<?php if ($esAdminInventario): ?>
<div id="modalTipoCambio" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header" style="background:#6366f1;color:#fff">
        <button type="button" class="close" data-dismiss="modal" style="color:#fff">&times;</button>
        <h4 class="modal-title">Tipo de cambio MXN/USD</h4>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label>Pesos por 1 USD</label>
          <input type="number" class="form-control" id="inputTipoCambio" value="<?php echo number_format($tipoCambioUsd, 2, '.', ''); ?>" min="0.01" step="0.01">
        </div>
        <p class="text-muted" style="font-size:12px">Los precios USD se calculan automáticamente desde el precio MXN.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="btnGuardarTipoCambio">Guardar</button>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
