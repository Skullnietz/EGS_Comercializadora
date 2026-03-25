<?php
if ($_SESSION["perfil"] != "administrador"
    AND $_SESSION["perfil"] != "vendedor"
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
      <a href="https://www.mercadolibre.com.mx/compras/<?php echo $orderId; ?>/detalle"
         target="_blank"
         class="ml-btn ml-btn-ml">
        <i class="fa-solid fa-external-link-alt"></i> Ver en MercadoLibre
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
              <label>Número de Orden</label>
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

      <!-- ── Vendedor ─────────────────────────────────────────── -->
      <div class="ml-card">
        <div class="ml-card-head">
          <h3 class="ml-card-title"><i class="fa-solid fa-store"></i> Vendedor</h3>
        </div>
        <div class="ml-card-body">
          <div class="ml-info-grid">
            <div class="ml-info-item">
              <label>Nickname</label>
              <div class="valor" id="ml-d-buyer-nick">—</div>
            </div>
            <div class="ml-info-item">
              <label>ID de Vendedor</label>
              <div class="valor" id="ml-d-buyer-id">—</div>
            </div>
            <div class="ml-info-item">
              <label>Ver tienda</label>
              <div class="valor"><a id="ml-d-seller-link" href="#" target="_blank" style="color:#6366f1; font-weight:600;">Abrir en MercadoLibre</a></div>
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
          </div>
        </div>
      </div>

      <!-- ── Acciones ───────────────────────────────────────────── -->
      <div style="display:flex; gap:10px; flex-wrap:wrap; margin-top:6px;">
        <a href="index.php?ruta=pedidos" class="ml-btn ml-btn-back">
          <i class="fa-solid fa-arrow-left"></i> Regresar a Pedidos
        </a>
        <a id="ml-link-ver-ml" href="#" target="_blank" class="ml-btn ml-btn-ml">
          <i class="fa-solid fa-external-link-alt"></i> Ver en MercadoLibre
        </a>
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

      /* ── Info general ── */
      var estado = statusMap[p.status] || { text: p.status || '—', css: 'ml-badge-other' };
      $('#ml-badge-estado').html('<span class="ml-badge ' + estado.css + '">' + estado.text + '</span>');
      $('#ml-d-order-id').text(p.id || ORDER_ID);
      $('#ml-d-fecha-creacion').text(formatFecha(p.date_created));
      $('#ml-d-fecha-update').text(formatFecha(p.last_updated || p.date_last_updated));
      $('#ml-d-total').html('<strong>' + formatMoney(p.total_amount, p.currency_id) + '</strong>');
      $('#ml-d-moneda').text(p.currency_id || '—');
      $('#ml-d-status-detail').text(p.status_detail || '—');

      /* ── Vendedor ── */
      if (p.seller) {
        $('#ml-d-buyer-nick').text(p.seller.nickname || '—');
        $('#ml-d-buyer-id').text(p.seller.id || '—');
        var sellerUrl = 'https://www.mercadolibre.com.mx/perfil/' + (p.seller.nickname || '');
        $('#ml-d-seller-link').attr('href', sellerUrl);
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
      }

      /* ── Link ver en ML ── */
      var urlML = 'https://www.mercadolibre.com.mx/compras/' + ORDER_ID + '/detalle';
      $('#ml-link-ver-ml').attr('href', urlML);

      /* ── Mostrar contenido ── */
      $('#ml-detail-content').show();
    },
    error: function () {
      $('#ml-detail-loading').hide();
      mostrarAlerta('danger',
        '<i class="fa-solid fa-triangle-exclamation"></i> Error de conexión al cargar la orden.');
    }
  });

})();
</script>
