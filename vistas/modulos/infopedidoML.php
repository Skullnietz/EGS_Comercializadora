<?php
if ($_SESSION["perfil"] != "administrador"
    AND $_SESSION["perfil"] != "tecnico"
    AND $_SESSION["perfil"] != "Super-Administrador") {
    echo '<script>window.location = "inicio";</script>';
    return;
}

$orderId = intval($_GET["order_id"] ?? 0);
if (!$orderId) {
    echo '<script>window.location = "index.php?ruta=pedidos";</script>';
    return;
}
?>

<style>
  :root {
    --ml-yellow: #ffe600;
    --crm-bg:     #f8fafc;
    --crm-surface:#ffffff;
    --crm-border: #e2e8f0;
    --crm-text:   #0f172a;
    --crm-text2:  #475569;
    --crm-muted:  #94a3b8;
    --crm-accent: #6366f1;
    --crm-radius: 14px;
    --crm-shadow: 0 1px 3px rgba(15,23,42,.06), 0 4px 14px rgba(15,23,42,.04);
    --crm-shadow-lg: 0 4px 24px rgba(15,23,42,.10);
  }

  .content { background: var(--crm-bg); padding: 14px 15px 30px; }

  .ml-card {
    background: var(--crm-surface);
    border: 1px solid var(--crm-border);
    border-radius: var(--crm-radius);
    box-shadow: var(--crm-shadow);
    margin-bottom: 20px;
    overflow: hidden;
  }

  .ml-card-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding: 14px 20px;
    border-bottom: 1px solid #f1f5f9;
    flex-wrap: wrap;
  }

  .ml-card-title {
    margin: 0;
    font-size: 14px;
    font-weight: 700;
    color: var(--crm-text);
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .ml-card-title i { color: var(--crm-accent); }

  .ml-card-body { padding: 20px; }

  .ml-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 14px;
    margin-bottom: 0;
  }

  .ml-info-item label {
    display: block;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .4px;
    color: var(--crm-muted);
    font-weight: 700;
    margin-bottom: 4px;
  }

  .ml-info-item .valor {
    font-size: 14px;
    font-weight: 600;
    color: var(--crm-text);
  }

  .ml-badge {
    display: inline-block;
    padding: .35em .7em;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
    border: 1px solid transparent;
  }

  .ml-badge-paid      { color: #166534; background: #f0fdf4; border-color: #bbf7d0; }
  .ml-badge-pending   { color: #92400e; background: #fffbeb; border-color: #fde68a; }
  .ml-badge-cancelled { color: #991b1b; background: #fef2f2; border-color: #fecaca; }
  .ml-badge-partial   { color: #0e7490; background: #ecfeff; border-color: #a5f3fc; }
  .ml-badge-other     { color: #475569; background: #f1f5f9; border-color: #cbd5e1; }

  .ml-items-table th {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .4px;
    color: var(--crm-text2);
    background: #f8fafc;
    padding: 10px 12px;
    border-bottom: 2px solid #e2e8f0;
  }

  .ml-items-table td {
    padding: 12px;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
    font-size: 13px;
  }

  .ml-items-table tr:last-child td { border-bottom: none; }

  .ml-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 9px 16px;
    border-radius: 8px;
    border: 1px solid transparent;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    text-decoration: none;
    transition: all .15s ease;
    white-space: nowrap;
  }

  .ml-btn-back    { background: #f1f5f9; color: #475569; border-color: #e2e8f0; }
  .ml-btn-back:hover { background: #e2e8f0; color: #1e293b; }
  .ml-btn-ml      { background: var(--ml-yellow); color: #1a1a1a; border-color: #d4c100; font-weight: 800; }
  .ml-btn-ml:hover { background: #f0d800; }
  .ml-btn-primary { background: #6366f1; color: #fff; border-color: #4f46e5; }
  .ml-btn-primary:hover { background: #4f46e5; }

  .ml-header-bar {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 18px;
    flex-wrap: wrap;
  }

  .ml-header-bar h2 {
    margin: 0;
    font-size: 20px;
    font-weight: 800;
    color: var(--crm-text);
    flex: 1;
  }

  .ml-header-bar h2 small {
    font-size: 13px;
    color: var(--crm-muted);
    font-weight: 500;
  }

  #ml-detail-loading {
    text-align: center;
    padding: 60px 20px;
  }

  #ml-detail-loading p {
    margin-top: 14px;
    color: var(--crm-muted);
    font-size: 13px;
  }

  .ml-shipping-badge {
    display: inline-block;
    padding: .3em .6em;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
  }

  .ml-section-note {
    margin: 0 0 14px;
    color: var(--crm-text2);
    font-size: 12px;
    line-height: 1.5;
  }

  .ml-json-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 14px;
  }

  .ml-json-box {
    border: 1px solid var(--crm-border);
    border-radius: 12px;
    overflow: hidden;
    background: #f8fafc;
  }

  .ml-json-box h4 {
    margin: 0;
    padding: 12px 14px;
    font-size: 12px;
    font-weight: 800;
    color: var(--crm-text);
    border-bottom: 1px solid #e2e8f0;
    background: #fff;
  }

  .ml-json-box pre {
    margin: 0;
    padding: 14px;
    min-height: 180px;
    max-height: 360px;
    overflow: auto;
    background: #0f172a;
    color: #e2e8f0;
    font-size: 12px;
    line-height: 1.55;
    white-space: pre-wrap;
    word-break: break-word;
  }

  .ml-link-disabled {
    color: var(--crm-muted) !important;
    pointer-events: none;
    text-decoration: none;
  }

  .ml-subsection-title {
    margin: 20px 0 10px;
    font-size: 12px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .4px;
    color: var(--crm-text2);
  }

  .ml-address-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 14px;
  }

  .ml-address-card {
    border: 1px solid var(--crm-border);
    border-radius: 12px;
    background: #f8fafc;
    padding: 14px;
  }

  .ml-address-card h4 {
    margin: 0 0 10px;
    font-size: 13px;
    font-weight: 800;
    color: var(--crm-text);
  }

  .ml-address-card p {
    margin: 0 0 8px;
    font-size: 13px;
    color: var(--crm-text);
    line-height: 1.5;
  }

  .ml-address-card p:last-child { margin-bottom: 0; }

  .ml-address-card strong {
    display: inline-block;
    min-width: 76px;
    color: var(--crm-text2);
  }

  .ml-timeline {
    display: grid;
    gap: 10px;
  }

  .ml-timeline-item {
    border-left: 3px solid #cbd5e1;
    padding: 10px 12px;
    background: #f8fafc;
    border-radius: 0 10px 10px 0;
  }

  .ml-timeline-item strong {
    display: block;
    color: var(--crm-text);
    font-size: 13px;
    margin-bottom: 4px;
  }

  .ml-timeline-item span {
    display: block;
    color: var(--crm-text2);
    font-size: 12px;
    line-height: 1.5;
  }

  .ml-alt-links {
    display: none;
    margin-top: 14px;
    padding: 14px;
    border: 1px dashed #cbd5e1;
    border-radius: 12px;
    background: #f8fafc;
  }

  .ml-alt-links h4 {
    margin: 0 0 8px;
    font-size: 13px;
    font-weight: 800;
    color: var(--crm-text);
  }

  .ml-alt-links p {
    margin: 0 0 10px;
    font-size: 12px;
    color: var(--crm-text2);
  }

  .ml-alt-links-list {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
  }

  @media (max-width: 767px) {
    .ml-header-bar { flex-direction: column; align-items: flex-start; }
    .ml-card-body  { padding: 14px; }
    .content       { padding: 10px; }
  }
</style>

<div class="content-wrapper">

  <section class="content-header">
    <h1>Detalle de Orden <small>MercadoLibre</small></h1>
    <ol class="breadcrumb">
      <li><a href="inicio"><i class="fas fa-dashboard"></i> Inicio</a></li>
      <li><a href="index.php?ruta=pedidos">Pedidos</a></li>
      <li class="active">Orden ML #<?php echo $orderId; ?></li>
    </ol>
  </section>

  <div class="content">

    <!-- Barra de encabezado -->
    <div class="ml-header-bar">
      <a href="index.php?ruta=pedidos" class="ml-btn ml-btn-back">
        <i class="fa-solid fa-arrow-left"></i> Regresar a Pedidos
      </a>
      <h2>
        Orden de MercadoLibre
        <small id="ml-order-id-title">#<?php echo $orderId; ?></small>
      </h2>
      <a href="#" target="_blank" rel="noopener noreferrer" id="ml-header-link-ml" class="ml-btn ml-btn-ml">
        <i class="fa-solid fa-arrow-up-right-from-square"></i> Abrir compra en ML
      </a>
    </div>

    <!-- Estado de alerta -->
    <div id="ml-detail-alert" style="display:none;" class="alert"></div>

    <!-- Loading spinner -->
    <div id="ml-detail-loading">
      <i class="fa-solid fa-spinner fa-spin fa-3x" style="color:#6366f1;"></i>
      <p>Cargando información de la orden desde MercadoLibre...</p>
    </div>

    <!-- Contenido (se llena con JS) -->
    <div id="ml-detail-content" style="display:none;">

      <!-- ── Resumen de la orden ──────────────────────────────── -->
      <div class="ml-card">
        <div class="ml-card-head">
          <h3 class="ml-card-title"><i class="fa-solid fa-circle-info"></i> Información General</h3>
          <span id="ml-badge-estado"></span>
        </div>
        <div class="ml-card-body">
          <div class="ml-info-grid">
            <div class="ml-info-item">
              <label># Compra ML <small style="color:#94a3b8;">(visible en ML)</small></label>
              <div class="valor" id="ml-d-shipping-ref">—</div>
            </div>
            <div class="ml-info-item">
              <label># Orden API <small style="color:#94a3b8;">(ID interno)</small></label>
              <div class="valor" id="ml-d-order-id">—</div>
            </div>
            <div class="ml-info-item">
              <label>Fecha de Creación</label>
              <div class="valor" id="ml-d-fecha-creacion">—</div>
            </div>
            <div class="ml-info-item">
              <label>Última Actualización</label>
              <div class="valor" id="ml-d-fecha-update">—</div>
            </div>
            <div class="ml-info-item">
              <label>Total de la Orden</label>
              <div class="valor" id="ml-d-total" style="font-size:18px; color:#16a34a;">—</div>
            </div>
            <div class="ml-info-item">
              <label>Moneda</label>
              <div class="valor" id="ml-d-moneda">—</div>
            </div>
            <div class="ml-info-item">
              <label>Detalle de estado</label>
              <div class="valor" id="ml-d-status-detail">—</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Datos de buyer y seller -->
      <div class="ml-card">
        <div class="ml-card-head">
          <h3 class="ml-card-title"><i class="fa-solid fa-user"></i> Buyer (Comprador)</h3>
        </div>
        <div class="ml-card-body">
          <p class="ml-section-note">
            Estos datos salen directamente de la orden y, cuando es posible, tambien se complementan con
            la consulta a <code>/users/{id}</code>.
          </p>
          <div class="ml-info-grid">
            <div class="ml-info-item">
              <label>Nickname</label>
              <div class="valor" id="ml-d-buyer-order-nick">—</div>
            </div>
            <div class="ml-info-item">
              <label>ID de Buyer</label>
              <div class="valor" id="ml-d-buyer-order-id">—</div>
            </div>
            <div class="ml-info-item">
              <label>Nombre</label>
              <div class="valor" id="ml-d-buyer-name">—</div>
            </div>
            <div class="ml-info-item">
              <label>Documento</label>
              <div class="valor" id="ml-d-buyer-doc">—</div>
            </div>
            <div class="ml-info-item">
              <label>Perfil ML</label>
              <div class="valor"><a id="ml-d-buyer-link" href="#" target="_blank" class="ml-link-disabled" style="color:#6366f1; font-weight:600;">Sin nickname disponible</a></div>
            </div>
          </div>
        </div>
      </div>

      <div class="ml-card">
        <div class="ml-card-head">
          <h3 class="ml-card-title"><i class="fa-solid fa-store"></i> Seller (Vendedor)</h3>
        </div>
        <div class="ml-card-body">
          <div class="ml-info-grid">
            <div class="ml-info-item">
              <label>Nickname</label>
              <div class="valor" id="ml-d-seller-order-nick">—</div>
            </div>
            <div class="ml-info-item">
              <label>ID de Seller</label>
              <div class="valor" id="ml-d-seller-order-id">—</div>
            </div>
            <div class="ml-info-item">
              <label>Nombre</label>
              <div class="valor" id="ml-d-seller-name">—</div>
            </div>
            <div class="ml-info-item">
              <label>Documento</label>
              <div class="valor" id="ml-d-seller-doc">—</div>
            </div>
            <div class="ml-info-item">
              <label>Perfil ML</label>
              <div class="valor"><a id="ml-d-seller-link" href="#" target="_blank" class="ml-link-disabled" style="color:#6366f1; font-weight:600;">Sin nickname disponible</a></div>
            </div>
          </div>
        </div>
      </div>

      <!-- ── Artículos ─────────────────────────────────────────── -->
      <div class="ml-card">
        <div class="ml-card-head">
          <h3 class="ml-card-title"><i class="fa-solid fa-box"></i> Artículos del Pedido</h3>
        </div>
        <div class="ml-card-body" style="padding:0;">
          <div style="overflow-x:auto;">
            <table class="ml-items-table" style="width:100%; border-collapse:collapse;">
              <thead>
                <tr>
                  <th>ID Artículo</th>
                  <th>Descripción</th>
                  <th>Cantidad</th>
                  <th>Precio Unitario</th>
                  <th>Subtotal</th>
                  <th>Ver en ML</th>
                </tr>
              </thead>
              <tbody id="ml-d-items-body">
                <tr><td colspan="6" style="text-align:center; color:#94a3b8; padding:20px;">Cargando artículos...</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- ── Pagos ──────────────────────────────────────────────── -->
      <div class="ml-card" id="ml-card-pagos">
        <div class="ml-card-head">
          <h3 class="ml-card-title"><i class="fa-solid fa-credit-card"></i> Pagos</h3>
        </div>
        <div class="ml-card-body" style="padding:0;">
          <div style="overflow-x:auto;">
            <table class="ml-items-table" style="width:100%; border-collapse:collapse;">
              <thead>
                <tr>
                  <th>ID Pago</th>
                  <th>Estado</th>
                  <th>Método</th>
                  <th>Monto Pagado</th>
                  <th>Fecha</th>
                </tr>
              </thead>
              <tbody id="ml-d-pagos-body">
                <tr><td colspan="5" style="text-align:center; color:#94a3b8; padding:20px;">Sin información</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- ── Envío ──────────────────────────────────────────────── -->
      <div class="ml-card" id="ml-card-envio" style="display:none;">
        <div class="ml-card-head">
          <h3 class="ml-card-title"><i class="fa-solid fa-truck"></i> Envío</h3>
          <span id="ml-d-envio-badge"></span>
        </div>
        <div class="ml-card-body">
          <div id="ml-envio-loading" style="text-align:center; padding:20px; color:#94a3b8;">
            <i class="fa-solid fa-spinner fa-spin"></i> Cargando datos de envío...
          </div>
            <div id="ml-envio-info" style="display:none;">
              <div class="ml-info-grid">
                <div class="ml-info-item">
                  <label>ID de Envío</label>
                  <div class="valor" id="ml-d-shipping-id">—</div>
              </div>
              <div class="ml-info-item">
                <label>Estado</label>
                <div class="valor" id="ml-d-shipping-status">—</div>
              </div>
              <div class="ml-info-item">
                <label>Subestado</label>
                <div class="valor" id="ml-d-shipping-substatus">—</div>
              </div>
              <div class="ml-info-item">
                <label><i class="fa-solid fa-calendar-check" style="color:#16a34a;"></i> Fecha estimada de entrega</label>
                <div class="valor" id="ml-d-shipping-eta" style="color:#16a34a;">—</div>
              </div>
              <div class="ml-info-item">
                <label>Número de seguimiento</label>
                <div class="valor" id="ml-d-shipping-tracking">—</div>
              </div>
                <div class="ml-info-item">
                  <label>Servicio de envío</label>
                  <div class="valor" id="ml-d-shipping-service">—</div>
                </div>
              </div>

              <div class="ml-subsection-title">Fechas y costos</div>
              <div class="ml-info-grid">
                <div class="ml-info-item">
                  <label>Fecha de creacion</label>
                  <div class="valor" id="ml-d-shipping-created">—</div>
                </div>
                <div class="ml-info-item">
                  <label>Ultima actualizacion</label>
                  <div class="valor" id="ml-d-shipping-updated">—</div>
                </div>
                <div class="ml-info-item">
                  <label>Fecha de impresion</label>
                  <div class="valor" id="ml-d-shipping-printed">—</div>
                </div>
                <div class="ml-info-item">
                  <label>Fecha enviado</label>
                  <div class="valor" id="ml-d-shipping-date-shipped">—</div>
                </div>
                <div class="ml-info-item">
                  <label>Fecha entregado</label>
                  <div class="valor" id="ml-d-shipping-date-delivered">—</div>
                </div>
                <div class="ml-info-item">
                  <label>Tipo logistico</label>
                  <div class="valor" id="ml-d-shipping-logistic">—</div>
                </div>
                <div class="ml-info-item">
                  <label>Modo</label>
                  <div class="valor" id="ml-d-shipping-mode">—</div>
                </div>
                <div class="ml-info-item">
                  <label>Tipo</label>
                  <div class="valor" id="ml-d-shipping-type">—</div>
                </div>
                <div class="ml-info-item">
                  <label>Creado por</label>
                  <div class="valor" id="ml-d-shipping-created-by">—</div>
                </div>
                <div class="ml-info-item">
                  <label>Service ID</label>
                  <div class="valor" id="ml-d-shipping-service-id">—</div>
                </div>
                <div class="ml-info-item">
                  <label>Costo base</label>
                  <div class="valor" id="ml-d-shipping-base-cost">—</div>
                </div>
                <div class="ml-info-item">
                  <label>Costo de orden</label>
                  <div class="valor" id="ml-d-shipping-order-cost">—</div>
                </div>
              </div>

              <div id="ml-tracking-link-wrap" style="margin-top:14px; display:none;">
                <a id="ml-tracking-link" href="#" target="_blank" class="ml-btn ml-btn-primary" style="font-size:12px;">
                  <i class="fa-solid fa-route"></i> Rastrear envío
                </a>
              </div>

              <div class="ml-subsection-title">Direcciones</div>
              <div class="ml-address-grid">
                <div class="ml-address-card">
                  <h4>Destino</h4>
                  <p><strong>Recibe:</strong> <span id="ml-d-receiver-name">—</span></p>
                  <p><strong>Direccion:</strong> <span id="ml-d-receiver-line">—</span></p>
                  <p><strong>Zona:</strong> <span id="ml-d-receiver-zone">—</span></p>
                  <p><strong>Telefono:</strong> <span id="ml-d-receiver-phone">—</span></p>
                  <p><strong>Notas:</strong> <span id="ml-d-receiver-comment">—</span></p>
                </div>
                <div class="ml-address-card">
                  <h4>Origen</h4>
                  <p><strong>Envia:</strong> <span id="ml-d-sender-name">—</span></p>
                  <p><strong>Direccion:</strong> <span id="ml-d-sender-line">—</span></p>
                  <p><strong>Zona:</strong> <span id="ml-d-sender-zone">—</span></p>
                  <p><strong>Nodo:</strong> <span id="ml-d-sender-node">—</span></p>
                  <p><strong>Tipos:</strong> <span id="ml-d-sender-types">—</span></p>
                </div>
              </div>

              <div class="ml-subsection-title">Items del envio</div>
              <div style="overflow-x:auto;">
                <table class="ml-items-table" style="width:100%; border-collapse:collapse;">
                  <thead>
                    <tr>
                      <th>ID Item</th>
                      <th>Descripcion</th>
                      <th>Cantidad</th>
                      <th>Dimensiones</th>
                    </tr>
                  </thead>
                  <tbody id="ml-d-shipping-items-body">
                    <tr><td colspan="4" style="text-align:center; color:#94a3b8; padding:20px;">Sin items de envio</td></tr>
                  </tbody>
                </table>
              </div>

              <div class="ml-subsection-title">Historial del envio</div>
              <div class="ml-timeline" id="ml-d-shipping-history">
                <div class="ml-timeline-item">
                  <strong>Sin historial</strong>
                  <span>El shipment no devolvio eventos todavia.</span>
                </div>
              </div>
            </div>
          </div>
        </div>

      <div class="ml-card">
        <div class="ml-card-head">
          <h3 class="ml-card-title"><i class="fa-solid fa-code"></i> Payload API</h3>
        </div>
        <div class="ml-card-body">
          <p class="ml-section-note">
            Aqui puedes ver exactamente lo que responde Mercado Libre para esta compra.
            Esto te sirve para validar que campos puedes usar del lado buyer.
          </p>
          <div class="ml-json-grid">
            <div class="ml-json-box">
              <h4>Orden completa /orders/{id}</h4>
              <pre id="ml-json-order">Cargando...</pre>
            </div>
            <div class="ml-json-box">
              <h4>Buyer dentro de la orden</h4>
              <pre id="ml-json-buyer">Sin datos</pre>
            </div>
            <div class="ml-json-box">
              <h4>Seller dentro de la orden</h4>
              <pre id="ml-json-seller">Sin datos</pre>
            </div>
            <div class="ml-json-box">
              <h4>Buyer /users/{id}</h4>
              <pre id="ml-json-buyer-user">Sin datos</pre>
            </div>
            <div class="ml-json-box">
              <h4>Seller /users/{id}</h4>
              <pre id="ml-json-seller-user">Sin datos</pre>
            </div>
            <div class="ml-json-box">
              <h4>Envio /shipments/{id}</h4>
              <pre id="ml-json-shipping">Sin datos</pre>
            </div>
          </div>
        </div>
      </div>

      <!-- ── Acciones ───────────────────────────────────────────── -->
      <div style="display:flex; gap:10px; flex-wrap:wrap; margin-top:6px;">
        <a href="index.php?ruta=pedidos" class="ml-btn ml-btn-back">
          <i class="fa-solid fa-arrow-left"></i> Regresar a Pedidos
        </a>
        <a href="#" target="_blank" rel="noopener noreferrer" id="ml-link-ver-ml" class="ml-btn ml-btn-ml">
          <i class="fa-solid fa-arrow-up-right-from-square"></i> Abrir compra en ML
        </a>
      </div>

      <div class="ml-alt-links" id="ml-alt-links">
        <h4>Enlaces alternativos</h4>
        <p>
          Si el boton principal no abre la compra, prueba estas variantes. En tu caso suele funcionar mejor
          la ruta armada con <code>order.id</code> que con <code>shipping.id</code>.
        </p>
        <div class="ml-alt-links-list">
          <a href="#" target="_blank" rel="noopener noreferrer" id="ml-alt-link-order" class="ml-btn ml-btn-back">Ruta por order.id</a>
          <a href="#" target="_blank" rel="noopener noreferrer" id="ml-alt-link-shipping" class="ml-btn ml-btn-back">Ruta por shipping.id</a>
          <a href="#" target="_blank" rel="noopener noreferrer" id="ml-alt-link-simple" class="ml-btn ml-btn-back">Ruta simple</a>
        </div>
      </div>

    </div><!-- /ml-detail-content -->

  </div><!-- /content -->
</div><!-- /content-wrapper -->

<script>
(function () {
  var ORDER_ID = <?php echo $orderId; ?>;

  var statusMap = {
    'paid':           { text: 'Pagado',        css: 'ml-badge-paid'      },
    'pending':        { text: 'Pendiente',      css: 'ml-badge-pending'   },
    'cancelled':      { text: 'Cancelado',      css: 'ml-badge-cancelled' },
    'partially_paid': { text: 'Pago Parcial',   css: 'ml-badge-partial'   },
    'in_process':     { text: 'En Proceso',     css: 'ml-badge-other'     },
    'on_hold':        { text: 'En Espera',      css: 'ml-badge-other'     },
  };

  var pagoStatusMap = {
    'approved':   { text: 'Aprobado',   css: 'ml-badge-paid'      },
    'pending':    { text: 'Pendiente',  css: 'ml-badge-pending'   },
    'rejected':   { text: 'Rechazado', css: 'ml-badge-cancelled' },
    'cancelled':  { text: 'Cancelado', css: 'ml-badge-cancelled' },
    'in_process': { text: 'En Proceso',css: 'ml-badge-other'     },
  };

  function escapeHtml(value) {
    return String(value == null ? '' : value)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }

  function renderJson(selector, data) {
    var isEmptyObject = data && typeof data === 'object' && !Array.isArray(data) && Object.keys(data).length === 0;
    var isEmptyArray = Array.isArray(data) && data.length === 0;

    if (data == null || isEmptyObject || isEmptyArray) {
      $(selector).text('Sin datos');
      return;
    }

    try {
      $(selector).html(escapeHtml(JSON.stringify(data, null, 2)));
    } catch (e) {
      $(selector).text(String(data));
    }
  }

  function textoPlano() {
    for (var i = 0; i < arguments.length; i++) {
      var value = arguments[i];
      if (typeof value === 'string' && value.trim() !== '') return value.trim();
      if (value !== null && value !== undefined && value !== '') return String(value);
    }
    return '—';
  }

  function nombreCompleto(data) {
    if (!data) return '—';
    var fullName = [data.first_name || '', data.last_name || ''].join(' ').trim();
    return textoPlano(fullName, data.registration_name, data.name);
  }

  function documentoTexto(data) {
    if (!data) return '—';

    var identification = data.identification || {};
    var billingInfo = data.billing_info || {};

    if (identification.number) {
      return textoPlano(
        ((identification.type || '') + ' ' + identification.number).trim()
      );
    }

    if (billingInfo.doc_number) {
      return textoPlano(
        ((billingInfo.doc_type || '') + ' ' + billingInfo.doc_number).trim()
      );
    }

    return '—';
  }

  function setPerfilLink(selector, nickname) {
    if (nickname && nickname !== '—') {
      $(selector)
        .attr('href', 'https://www.mercadolibre.com.mx/perfil/' + nickname)
        .removeClass('ml-link-disabled')
        .text('Abrir perfil en MercadoLibre');
      return;
    }

    $(selector)
      .attr('href', '#')
      .addClass('ml-link-disabled')
      .text('Sin nickname disponible');
  }

  function renderParty(role, orderData, userData) {
    var merged = $.extend(true, {}, orderData || {}, userData || {});
    var nickname = textoPlano(merged.nickname);

    $('#ml-d-' + role + '-order-id').text(textoPlano(merged.id));
    $('#ml-d-' + role + '-order-nick').text(nickname);
    $('#ml-d-' + role + '-name').text(nombreCompleto(merged));
    $('#ml-d-' + role + '-doc').text(documentoTexto(merged));
    setPerfilLink('#ml-d-' + role + '-link', nickname === '—' ? '' : nickname);
  }

  function cargarUsuarioMl(userId, role, orderData) {
    renderJson('#ml-json-' + role + '-user', { loading: true, user_id: userId });

    var dU = new FormData();
    dU.append('accion', 'obtenerUsuario');
    dU.append('user_id', userId);

    $.ajax({
      url: 'ajax/mercadolibre.ajax.php',
      method: 'POST',
      data: dU,
      cache: false,
      contentType: false,
      processData: false,
      dataType: 'json',
      success: function (u) {
        renderJson('#ml-json-' + role + '-user', u || {});
        renderParty(role, orderData, u || {});
      },
      error: function () {
        renderJson('#ml-json-' + role + '-user', {
          error: 'No se pudo consultar /users/' + userId
        });
      }
    });
  }

  function construirUrlsCompraML(orderId, shippingId, packId) {
    var base = 'https://myaccount.mercadolibre.com.mx/my_purchases/';
    var urls = {
      byOrder: base + orderId + '/status?orderId=' + orderId,
      byOrderWithPack: base + orderId + '/status?packId=' + packId + '&orderId=' + orderId,
      byShipping: shippingId ? (base + shippingId + '/status?orderId=' + orderId) : '',
      byShippingWithPack: (shippingId && packId) ? (base + shippingId + '/status?packId=' + packId + '&orderId=' + orderId) : '',
      simple: base + orderId + '/status'
    };

    urls.primary = packId ? urls.byOrderWithPack : urls.byOrder;
    return urls;
  }

  function configurarLinksCompraML(orderId, shippingId, packId) {
    var urls = construirUrlsCompraML(orderId, shippingId, packId);

    $('#ml-link-ver-ml').attr('href', urls.primary);
    $('#ml-header-link-ml').attr('href', urls.primary);
    $('#ml-alt-link-order').attr('href', urls.byOrderWithPack || urls.byOrder);
    $('#ml-alt-link-shipping').attr('href', urls.byShippingWithPack || urls.byShipping || urls.byOrder);
    $('#ml-alt-link-simple').attr('href', urls.simple);
    $('#ml-alt-links').show();
  }

  function textoLista(values) {
    if (!values || !values.length) return '—';
    return values.join(', ');
  }

  function direccionLinea(address) {
    if (!address) return '—';
    return textoPlano(
      address.address_line,
      [address.street_name || '', address.street_number || ''].join(' ').trim()
    );
  }

  function direccionZona(address) {
    if (!address) return '—';
    return textoLista([
      address.neighborhood && address.neighborhood.name ? address.neighborhood.name : '',
      address.city && address.city.name ? address.city.name : '',
      address.state && address.state.name ? address.state.name : '',
      address.zip_code || ''
    ].filter(Boolean));
  }

  function traducirEstadoEnvio(value) {
    var map = {
      'ready_to_ship': 'Listo para enviar',
      'shipped': 'En camino',
      'delivered': 'Entregado',
      'not_delivered': 'No entregado',
      'cancelled': 'Cancelado',
      'handling': 'Preparando',
      'in_transit': 'En transito',
      'pending': 'Pendiente'
    };
    return map[value] || value || '—';
  }

  function traducirSubestadoEnvio(value) {
    var map = {
      'shipment_paid': 'Envio pagado',
      'in_warehouse': 'En bodega',
      'ready_to_pack': 'Listo para empaquetar',
      'packed': 'Empacado',
      'in_packing_list': 'En lista de empaque',
      'picked_up': 'Recolectado',
      'out_for_delivery': 'En ruta de entrega',
      'delivered': 'Entregado'
    };
    return map[value] || value || '—';
  }

  function renderShippingItems(items) {
    if (!items || !items.length) {
      $('#ml-d-shipping-items-body').html('<tr><td colspan="4" style="text-align:center; color:#94a3b8; padding:20px;">Sin items de envio</td></tr>');
      return;
    }

    var html = '';
    $.each(items, function (i, item) {
      html += '<tr>';
      html += '<td><code style="font-size:11px;">' + textoPlano(item.id) + '</code></td>';
      html += '<td>' + escapeHtml(textoPlano(item.description)) + '</td>';
      html += '<td style="text-align:center;">' + textoPlano(item.quantity) + '</td>';
      html += '<td>' + escapeHtml(textoPlano(item.dimensions)) + '</td>';
      html += '</tr>';
    });

    $('#ml-d-shipping-items-body').html(html);
  }

  function renderShippingHistory(shipment) {
    var timeline = [];
    var statusHistory = shipment.status_history || {};
    var substatusHistory = shipment.substatus_history || [];

    if (statusHistory.date_ready_to_ship) {
      timeline.push({
        date: statusHistory.date_ready_to_ship,
        title: 'Listo para enviar',
        detail: 'Inicio de preparacion del envio'
      });
    }

    if (statusHistory.date_handling) {
      timeline.push({
        date: statusHistory.date_handling,
        title: 'Handling',
        detail: 'El envio entro a proceso logistico'
      });
    }

    if (statusHistory.date_shipped) {
      timeline.push({
        date: statusHistory.date_shipped,
        title: 'Enviado',
        detail: 'El paquete salio a ruta'
      });
    }

    if (statusHistory.date_first_visit) {
      timeline.push({
        date: statusHistory.date_first_visit,
        title: 'Primer intento de visita',
        detail: 'Primer registro de visita o entrega'
      });
    }

    if (statusHistory.date_delivered) {
      timeline.push({
        date: statusHistory.date_delivered,
        title: 'Entregado',
        detail: 'Entrega confirmada'
      });
    }

    if (statusHistory.date_not_delivered) {
      timeline.push({
        date: statusHistory.date_not_delivered,
        title: 'No entregado',
        detail: 'Hubo un intento sin entrega'
      });
    }

    if (statusHistory.date_cancelled) {
      timeline.push({
        date: statusHistory.date_cancelled,
        title: 'Cancelado',
        detail: 'El envio fue cancelado'
      });
    }

    $.each(substatusHistory, function (i, event) {
      timeline.push({
        date: event.date,
        title: traducirSubestadoEnvio(event.substatus),
        detail: 'Estado: ' + traducirEstadoEnvio(event.status)
      });
    });

    timeline.sort(function (a, b) {
      return new Date(a.date).getTime() - new Date(b.date).getTime();
    });

    if (!timeline.length) {
      $('#ml-d-shipping-history').html(
        '<div class="ml-timeline-item"><strong>Sin historial</strong><span>El shipment no devolvio eventos todavia.</span></div>'
      );
      return;
    }

    var html = '';
    $.each(timeline, function (i, event) {
      html += '<div class="ml-timeline-item">';
      html += '<strong>' + escapeHtml(textoPlano(event.title)) + '</strong>';
      html += '<span>' + escapeHtml(formatFecha(event.date)) + '</span>';
      html += '<span>' + escapeHtml(textoPlano(event.detail)) + '</span>';
      html += '</div>';
    });
    $('#ml-d-shipping-history').html(html);
  }

  function obtenerPrimeraFechaEntrega(shipment) {
    var shippingOption = shipment.shipping_option || {};
    var candidates = [
      shippingOption.estimated_delivery_extended && shippingOption.estimated_delivery_extended.date,
      shippingOption.estimated_delivery_time && shippingOption.estimated_delivery_time.date,
      shippingOption.estimated_delivery_final && shippingOption.estimated_delivery_final.date,
      shippingOption.estimated_delivery_limit && shippingOption.estimated_delivery_limit.date,
      shippingOption.estimated_delivery_time && shippingOption.estimated_delivery_time.to,
      shippingOption.estimated_delivery_time && shippingOption.estimated_delivery_time.from,
      shipment.estimated_delivery_time && shipment.estimated_delivery_time.date,
      shipment.estimated_delivery_final && shipment.estimated_delivery_final.date,
      shipment.estimated_delivery_limit && shipment.estimated_delivery_limit.date,
      shipment.estimated_delivery_time && shipment.estimated_delivery_time.to,
      shipment.estimated_delivery_time && shipment.estimated_delivery_time.from,
      shipment.estimated_handle_time && shipment.estimated_handle_time.date
    ];

    for (var i = 0; i < candidates.length; i++) {
      if (candidates[i]) return candidates[i];
    }

    return null;
  }

  function formatFecha(iso) {
    if (!iso) return '—';
    var d = new Date(iso);
    return d.toLocaleString('es-MX', {
      day: '2-digit', month: '2-digit', year: 'numeric',
      hour: '2-digit', minute: '2-digit'
    });
  }

  function formatMoney(amount, currency) {
    return (currency || 'MXN') + ' ' +
      parseFloat(amount || 0).toLocaleString('es-MX', { minimumFractionDigits: 2 });
  }

  function mostrarAlerta(tipo, msg) {
    var colores = { info:'alert-info', warning:'alert-warning', danger:'alert-danger', success:'alert-success' };
    $('#ml-detail-alert')
      .removeClass('alert-info alert-warning alert-danger alert-success')
      .addClass(colores[tipo] || 'alert-info')
      .html(msg)
      .show();
  }

  /* ── Cargar detalle de la orden ── */
  var datos = new FormData();
  datos.append('accion',   'obtenerOrden');
  datos.append('order_id', ORDER_ID);

  $.ajax({
    url:         'ajax/mercadolibre.ajax.php',
    method:      'POST',
    data:        datos,
    cache:       false,
    contentType: false,
    processData: false,
    dataType:    'json',
    success: function (p) {
      $('#ml-detail-loading').hide();

      if (!p || p.error) {
        mostrarAlerta('danger',
          '<i class="fa-solid fa-triangle-exclamation"></i> ' +
          (p && p.message ? p.message : 'No se pudo obtener la orden de MercadoLibre.'));
        return;
      }

      renderJson('#ml-json-order', p);
      renderJson('#ml-json-buyer', p.buyer || {});
      renderJson('#ml-json-seller', p.seller || {});

      /* ── Info general ── */
      var estado = statusMap[p.status] || { text: p.status || '—', css: 'ml-badge-other' };
      $('#ml-badge-estado').html('<span class="ml-badge ' + estado.css + '">' + estado.text + '</span>');
      $('#ml-d-order-id').text(p.id || ORDER_ID);
      // Número que ML muestra en "detalle de compra" = shipping.id
      var shippingRef = (p.shipping && p.shipping.id) ? p.shipping.id : '—';
      $('#ml-d-shipping-ref').text(shippingRef);
      $('#ml-d-fecha-creacion').text(formatFecha(p.date_created));
      $('#ml-d-fecha-update').text(formatFecha(p.last_updated || p.date_last_updated));
      $('#ml-d-total').html('<strong>' + formatMoney(p.total_amount, p.currency_id) + '</strong>');
      $('#ml-d-moneda').text(p.currency_id || '—');

      // status_detail viene vacío en órdenes completadas — mostrar texto legible
      var statusDetailMap = {
        'confirmed'             : 'Confirmado',
        'payment_required'      : 'Pago requerido',
        'payment_in_process'    : 'Pago en proceso',
        'partially_refunded'    : 'Parcialmente reembolsado',
        'pending_cancel'        : 'Cancelación pendiente',
        'cancelled'             : 'Cancelado',
        'waiting_transfer'      : 'Esperando transferencia',
        'mediating'             : 'En mediación',
        'null'                  : '—',
      };
      var rawDetail = p.status_detail || '';
      var detailText = rawDetail
        ? (statusDetailMap[rawDetail] || rawDetail)
        : (estado.text); // si viene vacío, repetir el estado principal
      $('#ml-d-status-detail').text(detailText);

      /* ── Buyer y Seller ── */
      renderParty('buyer', p.buyer || {}, null);
      renderParty('seller', p.seller || {}, null);

      if (p.buyer && p.buyer.id) {
        cargarUsuarioMl(p.buyer.id, 'buyer', p.buyer || {});
      }

      if (p.seller && p.seller.id) {
        cargarUsuarioMl(p.seller.id, 'seller', p.seller || {});
      }

      /* ── Artículos ── */
      if (p.order_items && p.order_items.length > 0) {
        var itemsHtml = '';
        $.each(p.order_items, function (i, item) {
          var subtotal = parseFloat(item.unit_price || 0) * parseInt(item.quantity || 1);
          var mlItemUrl = 'https://www.mercadolibre.com.mx/p/' + (item.item.id || '');
          itemsHtml += '<tr>';
          itemsHtml += '<td><code style="font-size:11px; color:#4f46e5;">' + (item.item.id || '—') + '</code></td>';
          itemsHtml += '<td>' + (item.item.title || '—') + '</td>';
          itemsHtml += '<td style="text-align:center;">' + (item.quantity || 1) + '</td>';
          itemsHtml += '<td>' + formatMoney(item.unit_price, p.currency_id) + '</td>';
          itemsHtml += '<td><strong>' + formatMoney(subtotal, p.currency_id) + '</strong></td>';
          itemsHtml += '<td><a href="' + mlItemUrl + '" target="_blank" class="btn btn-xs btn-warning" title="Ver artículo en ML"><i class="fas fa-external-link-alt"></i></a></td>';
          itemsHtml += '</tr>';
        });
        $('#ml-d-items-body').html(itemsHtml);
      } else {
        $('#ml-d-items-body').html('<tr><td colspan="6" style="text-align:center; color:#94a3b8;">Sin artículos</td></tr>');
      }

      /* ── Pagos ── */
      if (p.payments && p.payments.length > 0) {
        var pagosHtml = '';
        $.each(p.payments, function (i, pago) {
          var ps = pagoStatusMap[pago.status] || { text: pago.status || '—', css: 'ml-badge-other' };
          pagosHtml += '<tr>';
          pagosHtml += '<td><code style="font-size:11px;">' + (pago.id || '—') + '</code></td>';
          pagosHtml += '<td><span class="ml-badge ' + ps.css + '">' + ps.text + '</span></td>';
          pagosHtml += '<td>' + (pago.payment_type || '—') + '</td>';
          pagosHtml += '<td><strong>' + formatMoney(pago.total_paid_amount || pago.amount, p.currency_id) + '</strong></td>';
          pagosHtml += '<td>' + formatFecha(pago.date_approved || pago.date_created) + '</td>';
          pagosHtml += '</tr>';
        });
        $('#ml-d-pagos-body').html(pagosHtml);
      }

      /* ── Envío ── */
      if (p.shipping && p.shipping.id) {
        $('#ml-d-shipping-id').text(p.shipping.id);
        $('#ml-d-shipping-status').text(p.shipping.status || '—');
        $('#ml-d-shipping-substatus').text(p.shipping.substatus || '—');
        if (p.shipping.status) {
          $('#ml-d-envio-badge').html('<span class="ml-shipping-badge">' + p.shipping.status + '</span>');
        }
        $('#ml-card-envio').show();

        // Consultar shipment completo para fecha de entrega y tracking
        cargarDetalleEnvio(p.shipping.id);
      } else {
        renderJson('#ml-json-shipping', {
          note: 'La orden no incluye shipping.id'
        });
      }

      /* ── Link ver en ML ── */
      // path    = p.shipping.id  (2000012198329309)
      // packId= = p.pack_id      (2000012198329313)
      // orderId= = p.id          (2000015689832592)
      var shippingId = (p.shipping && p.shipping.id) ? p.shipping.id : null;
      var packId     = p.pack_id || null;
      configurarLinksCompraML(ORDER_ID, shippingId, packId);

      /* ── Mostrar contenido ── */
      $('#ml-detail-content').show();
    },
    error: function () {
      $('#ml-detail-loading').hide();
      mostrarAlerta('danger',
        '<i class="fa-solid fa-triangle-exclamation"></i> Error de conexión al cargar la orden.');
    }
  });

  /* ── Cargar detalle completo del envío ── */
  function cargarDetalleEnvio(shippingId) {
    var d = new FormData();
    d.append('accion',      'obtenerEnvio');
    d.append('shipping_id', shippingId);

    $.ajax({
      url: 'ajax/mercadolibre.ajax.php', method: 'POST', data: d,
      cache: false, contentType: false, processData: false, dataType: 'json',
      success: function (s) {
        $('#ml-envio-loading').hide();
        renderJson('#ml-json-shipping', s || {});

        if (!s || s.error) {
          $('#ml-envio-info').html('<p style="color:#94a3b8; font-size:13px;">No se pudo cargar el detalle del envío.</p>').show();
          return;
        }

        // Estado legible
        var statusText = traducirEstadoEnvio(s.status);
        var subText    = traducirSubestadoEnvio(s.substatus);
        $('#ml-d-shipping-status').text(statusText);
        $('#ml-d-shipping-substatus').text(subText);
        $('#ml-d-envio-badge').html('<span class="ml-shipping-badge">' + statusText + '</span>');

        // Fecha estimada de entrega — ML puede devolverla en distintos campos
        var eta = '—';
        var etaDate = obtenerPrimeraFechaEntrega(s);

        if (etaDate) {
          eta = formatFecha(etaDate);
        } else if (s.status === 'delivered') {
          eta = 'Entregado';
        }
        $('#ml-d-shipping-eta').text(eta);

        // Número de seguimiento
        var tracking = '—';
        if (s.tracking_number) {
          tracking = s.tracking_number;
        } else if (s.tracking_method) {
          tracking = s.tracking_method;
        }
        $('#ml-d-shipping-tracking').text(tracking);

        // Servicio de envío
        var servicio = s.shipping_option
                     ? (s.shipping_option.name || s.service_id || '—')
                     : (s.service_id || '—');
        $('#ml-d-shipping-service').text(servicio);
        $('#ml-d-shipping-created').text(formatFecha(s.date_created));
        $('#ml-d-shipping-updated').text(formatFecha(s.last_updated));
        $('#ml-d-shipping-printed').text(formatFecha(s.date_first_printed));
        $('#ml-d-shipping-date-shipped').text(formatFecha(s.status_history && s.status_history.date_shipped));
        $('#ml-d-shipping-date-delivered').text(formatFecha(s.status_history && s.status_history.date_delivered));
        $('#ml-d-shipping-logistic').text(textoPlano(s.logistic_type));
        $('#ml-d-shipping-mode').text(textoPlano(s.mode));
        $('#ml-d-shipping-type').text(textoPlano(s.type));
        $('#ml-d-shipping-created-by').text(textoPlano(s.created_by));
        $('#ml-d-shipping-service-id').text(textoPlano(s.service_id));
        $('#ml-d-shipping-base-cost').text(s.base_cost != null ? formatMoney(s.base_cost, s.shipping_option && s.shipping_option.currency_id) : '—');
        $('#ml-d-shipping-order-cost').text(s.order_cost != null ? formatMoney(s.order_cost, s.shipping_option && s.shipping_option.currency_id) : '—');

        $('#ml-d-receiver-name').text(textoPlano(s.receiver_address && s.receiver_address.receiver_name));
        $('#ml-d-receiver-line').text(direccionLinea(s.receiver_address));
        $('#ml-d-receiver-zone').text(direccionZona(s.receiver_address));
        $('#ml-d-receiver-phone').text(textoPlano(s.receiver_address && s.receiver_address.receiver_phone));
        $('#ml-d-receiver-comment').text(textoPlano(s.receiver_address && s.receiver_address.comment));

        $('#ml-d-sender-name').text(textoPlano(s.sender_id));
        $('#ml-d-sender-line').text(direccionLinea(s.sender_address));
        $('#ml-d-sender-zone').text(direccionZona(s.sender_address));
        $('#ml-d-sender-node').text(textoPlano(
          s.sender_address && s.sender_address.node && (s.sender_address.node.node_id || s.sender_address.node.logistic_center_id)
        ));
        $('#ml-d-sender-types').text(textoLista((s.sender_address && s.sender_address.types) || []));

        renderShippingItems(s.shipping_items || []);
        renderShippingHistory(s);

        // Link de rastreo
        if (s.tracking_url) {
          $('#ml-tracking-link').attr('href', s.tracking_url);
          $('#ml-tracking-link-wrap').show();
        } else if (s.tracking_number) {
          // Enlace genérico a correos/estafeta si no hay URL directa
          var trackUrl = 'https://www.correosdemexico.gob.mx/SSLServicios/ConsultaCP/Seguimiento.aspx?tipo=masivo&numero=' + s.tracking_number;
          $('#ml-tracking-link').attr('href', trackUrl);
          $('#ml-tracking-link-wrap').show();
        }

        $('#ml-envio-info').show();
      },
      error: function () {
        $('#ml-envio-loading').hide();
        renderJson('#ml-json-shipping', {
          error: 'No se pudo consultar el shipment ' + shippingId
        });
        $('#ml-envio-info').html('<p style="color:#94a3b8; font-size:13px;">Error al consultar el envío.</p>').show();
      }
    });
  }

})();
</script>
