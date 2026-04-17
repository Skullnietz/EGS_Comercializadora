<?php
if($_SESSION["perfil"] != "administrador" AND $_SESSION["perfil"]!= "vendedor" AND $_SESSION["perfil"]!= "Super-Administrador"){
  echo '<script>window.location = "inicio";</script>';
  return;
}
?>

<!-- ══════════════════════════════════════════════════════
     CRM DESIGN SYSTEM — Info Pedido
══════════════════════════════════════════════════════ -->
<style>
/* ─── Tokens (hereda del dashboard) ─── */
:root {
  --crm-bg:       #f8fafc;
  --crm-surface:  #ffffff;
  --crm-border:   #e2e8f0;
  --crm-text:     #0f172a;
  --crm-text2:    #475569;
  --crm-muted:    #94a3b8;
  --crm-accent:   #6366f1;
  --crm-accent2:  #818cf8;
  --crm-radius:   14px;
  --crm-radius-sm:10px;
  --crm-shadow:   0 1px 3px rgba(15,23,42,.06), 0 4px 14px rgba(15,23,42,.04);
  --crm-shadow-lg:0 4px 24px rgba(15,23,42,.10);
  --crm-ease:     cubic-bezier(.4,0,.2,1);
}

/* ─── Page wrapper ─── */
.ped-page { padding: 0 8px; }

/* ─── Cards ─── */
.ped-card {
  background: var(--crm-surface);
  border: 1px solid var(--crm-border);
  border-radius: var(--crm-radius);
  box-shadow: var(--crm-shadow);
  overflow: hidden;
  transition: box-shadow .2s var(--crm-ease);
  margin-bottom: 20px;
}
.ped-card:hover { box-shadow: var(--crm-shadow-lg); }
.ped-card-head {
  display: flex; align-items: center; justify-content: space-between;
  padding: 16px 20px 12px;
  border-bottom: 1px solid #f1f5f9;
}
.ped-card-title {
  display: flex; align-items: center; gap: 10px;
  font-size: 14px; font-weight: 700; color: var(--crm-text);
  margin: 0;
}
.ped-card-title i { font-size: 15px; color: var(--crm-accent); opacity: .85; }
.ped-card-body { padding: 18px 20px; }

/* ─── Section headers ─── */
.ped-section {
  display: flex; align-items: center; gap: 14px;
  margin: 24px 0 14px; padding: 0 4px;
}
.ped-section-icon {
  width: 38px; height: 38px; border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  font-size: 16px; color: #fff; flex-shrink: 0;
}
.ped-section h3 { margin: 0; font-size: 15px; font-weight: 800; color: var(--crm-text); }
.ped-section p  { margin: 2px 0 0; font-size: 12px; color: var(--crm-muted); }

/* ─── Page header ─── */
.ped-page-header {
  background: var(--crm-surface);
  border: 1px solid var(--crm-border);
  border-radius: var(--crm-radius);
  box-shadow: var(--crm-shadow);
  padding: 20px 24px;
  margin-bottom: 20px;
}
.ped-breadcrumb {
  display: flex; align-items: center; gap: 6px;
  list-style: none; margin: 0 0 10px; padding: 0;
  font-size: 12px; font-weight: 500;
}
.ped-breadcrumb li { display: flex; align-items: center; gap: 6px; }
.ped-breadcrumb li a {
  color: var(--crm-muted); text-decoration: none;
  transition: color .15s;
}
.ped-breadcrumb li a:hover { color: var(--crm-accent); }
.ped-breadcrumb li a i { font-size: 11px; }
.ped-breadcrumb-sep { color: var(--crm-border); font-size: 10px; }
.ped-breadcrumb li.active { color: var(--crm-text2); }
.ped-header-row {
  display: flex; align-items: center; justify-content: space-between;
  flex-wrap: wrap; gap: 12px;
}
.ped-header-row h1 {
  font-size: 22px; font-weight: 800; color: var(--crm-text);
  margin: 0; display: flex; align-items: center; gap: 10px;
}

/* ─── Status badge ─── */
.ped-status {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 5px 14px; border-radius: 20px;
  font-size: 12px; font-weight: 600; line-height: 1.4;
}
.ped-status-pendiente   { background: #fef3c7; color: #92400e; }
.ped-status-adquirido   { background: #dbeafe; color: #1e40af; }
.ped-status-almacen     { background: #e0e7ff; color: #3730a3; }
.ped-status-asesor      { background: #fce7f3; color: #9d174d; }
.ped-status-pagado      { background: #d1fae5; color: #065f46; }
.ped-status-credito     { background: #fff7ed; color: #9a3412; }
.ped-status-cancelado   { background: #fee2e2; color: #991b1b; }

/* ─── Info rows ─── */
.ped-info-row {
  display: flex; align-items: center; gap: 12px;
  padding: 10px 0;
  border-bottom: 1px solid #f1f5f9;
}
.ped-info-row:last-child { border-bottom: none; }
.ped-info-icon {
  width: 36px; height: 36px; border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
  font-size: 14px; flex-shrink: 0;
}
.ped-info-icon.blue   { background: #eff6ff;  color: #3b82f6; }
.ped-info-icon.green  { background: #f0fdf4;  color: #22c55e; }
.ped-info-icon.purple { background: #f5f3ff;  color: #8b5cf6; }
.ped-info-icon.orange { background: #fff7ed;  color: #f97316; }
.ped-info-icon.indigo { background: #eef2ff;  color: #6366f1; }
.ped-info-label { font-size: 11px; color: var(--crm-muted); font-weight: 500; text-transform: uppercase; letter-spacing: .3px; }
.ped-info-value { font-size: 14px; color: var(--crm-text); font-weight: 600; }

/* ─── Products table ─── */
.ped-products-table {
  width: 100%; border-collapse: separate; border-spacing: 0;
}
.ped-products-table thead th {
  font-size: 11px; font-weight: 600; text-transform: uppercase;
  letter-spacing: .5px; color: var(--crm-muted);
  padding: 10px 14px; border-bottom: 2px solid #f1f5f9;
  text-align: left;
}
.ped-products-table tbody td {
  padding: 12px 14px; border-bottom: 1px solid #f8fafc;
  font-size: 13px; color: var(--crm-text); vertical-align: middle;
}
.ped-products-table tbody tr:hover { background: #fafbfc; }
.ped-products-table .ped-input-cell input {
  border: 1px solid var(--crm-border); border-radius: 8px;
  padding: 6px 10px; font-size: 13px; width: 100%;
  transition: border-color .2s;
}
.ped-products-table .ped-input-cell input:focus {
  outline: none; border-color: var(--crm-accent);
  box-shadow: 0 0 0 3px rgba(99,102,241,.1);
}
.ped-products-table .ped-input-cell input[readonly] {
  background: #f8fafc; border-color: transparent; cursor: default;
}
.ped-total-row td { border-top: 2px solid var(--crm-border); font-weight: 700; }

/* ─── Payments ─── */
.ped-payment-item {
  display: flex; align-items: center; gap: 12px;
  padding: 10px 0; border-bottom: 1px solid #f1f5f9;
}
.ped-payment-item:last-child { border-bottom: none; }
.ped-payment-icon {
  width: 32px; height: 32px; border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
  background: #f0fdf4; color: #22c55e; font-size: 13px; flex-shrink: 0;
}
.ped-payment-amount { font-size: 14px; font-weight: 700; color: var(--crm-text); }
.ped-payment-date { font-size: 12px; color: var(--crm-muted); margin-left: auto; }

/* ─── Summary totals ─── */
.ped-summary-box {
  display: flex; gap: 12px; margin-top: 16px;
}
.ped-summary-item {
  flex: 1; padding: 14px 16px; border-radius: var(--crm-radius-sm);
  text-align: center;
}
.ped-summary-item.total   { background: #eff6ff; border: 1px solid #bfdbfe; }
.ped-summary-item.paid    { background: #f0fdf4; border: 1px solid #bbf7d0; }
.ped-summary-item.debt    { background: #fef2f2; border: 1px solid #fecaca; }
.ped-summary-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .4px; margin-bottom: 4px; }
.ped-summary-value { font-size: 20px; font-weight: 800; }
.ped-summary-item.total .ped-summary-label { color: #3b82f6; }
.ped-summary-item.total .ped-summary-value { color: #1e40af; }
.ped-summary-item.paid .ped-summary-label  { color: #22c55e; }
.ped-summary-item.paid .ped-summary-value  { color: #065f46; }
.ped-summary-item.debt .ped-summary-label  { color: #ef4444; }
.ped-summary-item.debt .ped-summary-value  { color: #991b1b; }

/* ─── Observation avatars ─── */
.ped-obs-avatar {
  width: 36px; height: 36px; border-radius: 50%; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center;
  color: #fff; font-size: 12px; font-weight: 800;
  letter-spacing: .3px;
}

/* ─── Observations list ─── */
.ped-obs-list { display: flex; flex-direction: column; gap: 0; }
.ped-obs-item {
  display: flex; gap: 12px; padding: 14px 0;
  border-bottom: 1px solid #f1f5f9;
}
.ped-obs-item:last-child { border-bottom: none; }
.ped-obs-body { flex: 1; min-width: 0; }
.ped-obs-header {
  display: flex; align-items: center; gap: 8px;
  margin-bottom: 4px;
}
.ped-obs-name { font-size: 13px; font-weight: 700; color: var(--crm-text); }
.ped-obs-date { font-size: 11px; color: var(--crm-muted); }
.ped-obs-content {
  font-size: 13px; color: var(--crm-text2); line-height: 1.55;
  white-space: pre-wrap; word-break: break-word;
}
.ped-obs-content textarea.nuevaObservacion {
  width: 100%; border: none; background: transparent;
  padding: 0; font-size: 13px; font-weight: 400;
  color: var(--crm-text2); resize: none; min-height: 20px;
  font-family: inherit; line-height: 1.55;
  overflow: hidden;
}

/* ─── Compose observation ─── */
.ped-obs-compose {
  display: flex; gap: 12px; padding: 16px;
  background: #f8fafc; border-radius: var(--crm-radius-sm);
  border: 1px solid #f1f5f9; margin-bottom: 16px;
}
.ped-obs-compose-body { flex: 1; display: flex; flex-direction: column; gap: 8px; }
.ped-obs-compose textarea {
  width: 100%; border: 1px solid var(--crm-border); border-radius: 8px;
  padding: 10px 14px; font-size: 13px; font-weight: 500;
  color: var(--crm-text); resize: vertical; min-height: 60px;
  font-family: inherit; transition: border-color .2s, box-shadow .2s;
}
.ped-obs-compose textarea:focus {
  outline: none; border-color: var(--crm-accent);
  box-shadow: 0 0 0 3px rgba(99,102,241,.1);
}
.ped-obs-compose-actions {
  display: flex; align-items: center; justify-content: flex-end; gap: 8px;
}

/* Legacy classes kept for JS compatibility */
.agregarcampoobervacionesPedidos { display: none; }

/* ─── Dynamic payment inputs ─── */
.agregarCamposPago .input-group,
.nuevoCampoPagoPedido .input-group {
  margin-bottom: 8px;
}
.agregarCamposPago input.pagoAbonado,
.nuevoCampoPagoPedido input.fechaAbono {
  border-radius: 8px;
}

/* ─── Buttons ─── */
.ped-btn {
  display: inline-flex; align-items: center; gap: 8px;
  padding: 10px 20px; border-radius: 10px;
  font-size: 13px; font-weight: 600; border: none;
  cursor: pointer; transition: all .2s var(--crm-ease);
}
.ped-btn-primary {
  background: var(--crm-accent); color: #fff;
  box-shadow: 0 2px 8px rgba(99,102,241,.25);
}
.ped-btn-primary:hover {
  background: #4f46e5; transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(99,102,241,.35);
  color: #fff; text-decoration: none;
}
.ped-btn-outline {
  background: transparent; color: var(--crm-accent);
  border: 1px solid var(--crm-accent);
}
.ped-btn-outline:hover {
  background: rgba(99,102,241,.06);
  color: var(--crm-accent); text-decoration: none;
}
.ped-btn-success {
  background: #22c55e; color: #fff;
  box-shadow: 0 2px 8px rgba(34,197,94,.25);
}
.ped-btn-success:hover {
  background: #16a34a; transform: translateY(-1px);
  color: #fff; text-decoration: none;
}
.ped-btn-danger {
  background: transparent; color: #ef4444;
  border: 1px solid #fecaca; border-radius: 8px;
  padding: 6px 10px; font-size: 12px;
}
.ped-btn-danger:hover { background: #fef2f2; color: #ef4444; text-decoration: none; }

/* ─── Select styled ─── */
.ped-select {
  width: 100%; border: 1px solid var(--crm-border); border-radius: 10px;
  padding: 10px 14px; font-size: 14px; font-weight: 500;
  color: var(--crm-text); background: var(--crm-surface);
  appearance: auto; cursor: pointer;
  transition: border-color .2s, box-shadow .2s;
}
.ped-select:focus {
  outline: none; border-color: var(--crm-accent);
  box-shadow: 0 0 0 3px rgba(99,102,241,.1);
}

/* ─── Back link ─── */
.ped-back {
  display: inline-flex; align-items: center; gap: 6px;
  font-size: 13px; color: var(--crm-muted); font-weight: 500;
  text-decoration: none; transition: color .2s;
}
.ped-back:hover { color: var(--crm-accent); text-decoration: none; }

/* ─── WhatsApp button ─── */
.ped-wa-btn {
  display: inline-flex; align-items: center; justify-content: center;
  width: 32px; height: 32px; border-radius: 8px;
  background: #25d366; color: #fff; font-size: 14px;
  border: none; cursor: pointer; flex-shrink: 0;
  transition: all .2s var(--crm-ease); text-decoration: none;
}
.ped-wa-btn:hover {
  background: #128c7e; transform: scale(1.08);
  color: #fff; text-decoration: none;
}

/* ─── New payment inline form ─── */
.ped-new-payment {
  background: #f8fafc; border: 2px dashed var(--crm-border);
  border-radius: var(--crm-radius-sm); padding: 14px 16px;
  margin-top: 12px; display: none;
  animation: pedSlideIn .25s var(--crm-ease);
}
.ped-new-payment.active { display: block; }
@keyframes pedSlideIn {
  from { opacity: 0; transform: translateY(-8px); }
  to   { opacity: 1; transform: translateY(0); }
}
.ped-new-payment-title {
  font-size: 12px; font-weight: 700; color: var(--crm-accent);
  text-transform: uppercase; letter-spacing: .4px;
  margin-bottom: 10px; display: flex; align-items: center; gap: 6px;
}
.ped-new-payment-row {
  display: flex; gap: 10px; align-items: flex-end;
}
.ped-new-payment-field {
  flex: 1;
}
.ped-new-payment-field label {
  display: block; font-size: 11px; font-weight: 600;
  color: var(--crm-text2); margin-bottom: 4px;
}
.ped-new-payment-field input {
  width: 100%; border: 1px solid var(--crm-border); border-radius: 8px;
  padding: 8px 12px; font-size: 13px; font-weight: 500;
  color: var(--crm-text); transition: border-color .2s, box-shadow .2s;
}
.ped-new-payment-field input:focus {
  outline: none; border-color: var(--crm-accent);
  box-shadow: 0 0 0 3px rgba(99,102,241,.1);
}

/* ─── Modal enhanced ─── */
.ped-modal-body { padding: 28px; }
.ped-modal-illustration {
  text-align: center; padding: 20px 0 24px;
}
.ped-modal-illustration i {
  font-size: 48px; color: var(--crm-accent); opacity: .15;
}
.ped-modal-section-title {
  font-size: 11px; font-weight: 700; text-transform: uppercase;
  letter-spacing: .5px; color: var(--crm-muted); margin-bottom: 10px;
  display: flex; align-items: center; gap: 6px;
}
.ped-modal-section-title i { font-size: 12px; color: var(--crm-accent); }
.ped-modal-info-box {
  background: #f8fafc; border: 1px solid #f1f5f9;
  border-radius: var(--crm-radius-sm); padding: 14px 16px;
  display: flex; align-items: center; gap: 12px; margin-bottom: 20px;
}
.ped-modal-info-icon {
  width: 40px; height: 40px; border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  background: rgba(99,102,241,.08); color: var(--crm-accent);
  font-size: 16px; flex-shrink: 0;
}
.ped-modal-info-label { font-size: 11px; color: var(--crm-muted); }
.ped-modal-info-value { font-size: 16px; font-weight: 700; color: var(--crm-text); }

/* Choices.js override inside modal */
#modalAsignarPedido .choices {
  margin-bottom: 0;
}
#modalAsignarPedido .choices__inner {
  border: 1px solid var(--crm-border); border-radius: 10px;
  padding: 8px 12px; font-size: 14px; min-height: 44px;
  background: var(--crm-surface);
}
#modalAsignarPedido .choices__inner:focus-within {
  border-color: var(--crm-accent);
  box-shadow: 0 0 0 3px rgba(99,102,241,.1);
}
#modalAsignarPedido .choices__list--dropdown {
  border-radius: 10px; border-color: var(--crm-border);
  box-shadow: var(--crm-shadow-lg);
  z-index: 9999 !important;
}
#modalAsignarPedido .choices__list--dropdown .choices__item--selectable.is-highlighted {
  background: rgba(99,102,241,.08); color: var(--crm-text);
}
#modalAsignarPedido .choices__input {
  font-size: 14px; color: var(--crm-text);
}

/* ─── Responsive ─── */
@media(max-width: 991px) {
  .ped-summary-box { flex-direction: column; }
  .ped-header-row { flex-direction: column; align-items: flex-start; }
  .ped-new-payment-row { flex-direction: column; }
}
</style>

<?php
  $item = "id";
  $valor = $_GET["idPedido"];
  $pedidos = ControladorPedidos::ctrMostrarorpedidosParaValidar($item, $valor);
  foreach ($pedidos as $key => $valuePedidos) {}

  $item = "id";
  $valor = $_GET["cliente"];
  $usuario = ControladorClientes::ctrMostrarClientesOrdenes($item, $valor);

  $item = "id";
  $valor = $_GET["asesor"];
  $asesor = Controladorasesores::ctrMostrarAsesoresEleg($item, $valor);

  $itemOrdenes = "id";
  $valorOrdenes = $valuePedidos["id_orden"];
  $ordenes = controladorOrdenes::ctrMostrarordenesParaValidar($itemOrdenes, $valorOrdenes);
  $valueOrdenes = [];
  if (!empty($ordenes) && is_array($ordenes)) {
    foreach ($ordenes as $key => $valueOrdenes) {}
  }
  // Orden & Técnico: only resolve if order is assigned
  $tieneOrden = !empty($valuePedidos["id_orden"]) && $valuePedidos["id_orden"] != '0';
  $tieneTecnico = false;
  $respuesta = null;
  if ($tieneOrden) {
    $itemTec = "id";
    $valorTec = isset($valueOrdenes["id_tecnico"]) ? $valueOrdenes["id_tecnico"] : 0;
    if (!empty($valorTec)) {
      $respuesta = ControladorTecnicos::ctrMostrarTecnicos($itemTec, $valorTec);
      $tieneTecnico = !empty($respuesta) && !empty($respuesta["nombre"]);
    }
  }

  // Avatar gradient palette (matches dashboard)
  $_obsGrads = [
    'linear-gradient(135deg,#6366f1,#818cf8)',
    'linear-gradient(135deg,#3b82f6,#60a5fa)',
    'linear-gradient(135deg,#06b6d4,#22d3ee)',
    'linear-gradient(135deg,#22c55e,#4ade80)',
    'linear-gradient(135deg,#f59e0b,#fbbf24)',
    'linear-gradient(135deg,#ef4444,#f87171)',
    'linear-gradient(135deg,#8b5cf6,#a78bfa)',
    'linear-gradient(135deg,#ec4899,#f472b6)',
  ];
  function pedGetInitials($name) {
    $parts = array_filter(explode(' ', trim($name)));
    if (count($parts) >= 2) return mb_strtoupper(mb_substr($parts[0],0,1) . mb_substr($parts[1],0,1));
    return mb_strtoupper(mb_substr($name,0,2));
  }
  function pedGetGrad($name, $grads) {
    $hash = crc32($name);
    return $grads[abs($hash) % count($grads)];
  }

  // Determine status class
  $estadoRaw = $valuePedidos["estado"];
  $statusClass = 'ped-status-pendiente';
  if (stripos($estadoRaw, 'Adquirido') !== false) $statusClass = 'ped-status-adquirido';
  elseif (stripos($estadoRaw, 'Almacen') !== false || stripos($estadoRaw, 'Almacén') !== false) $statusClass = 'ped-status-almacen';
  elseif (stripos($estadoRaw, 'asesor') !== false) $statusClass = 'ped-status-asesor';
  elseif (stripos($estadoRaw, 'Pagado') !== false) $statusClass = 'ped-status-pagado';
  elseif (stripos($estadoRaw, 'Credito') !== false || stripos($estadoRaw, 'Crédito') !== false) $statusClass = 'ped-status-credito';
  elseif (stripos($estadoRaw, 'cancelado') !== false) $statusClass = 'ped-status-cancelado';

  $perfilActual = isset($_SESSION["perfil"]) ? $_SESSION["perfil"] : "";
  $puedeEditarPedidoCompleto = ($perfilActual == "administrador" || $perfilActual == "Super-Administrador");
  $puedeAgregarObservaciones = ($puedeEditarPedidoCompleto || $perfilActual == "vendedor");
  $puedeAsignarPedido = $puedeAgregarObservaciones;
  $historialClienteLink = 'index.php?ruta=Historialdecliente&idCliente='.(isset($_GET["cliente"]) ? intval($_GET["cliente"]) : 0)
    .'&nombreCliente='.(isset($usuario["nombre"]) ? urlencode($usuario["nombre"]) : 'Cliente');
?>

<div class="content-wrapper">

  <section class="content ped-page" style="padding-top:20px;">

    <!-- ─── Page Header ─── -->
    <div class="ped-page-header">
      <ul class="ped-breadcrumb">
        <li><a href="index.php?ruta=inicio"><i class="fa-solid fa-gauge"></i> Inicio</a></li>
        <li><span class="ped-breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span></li>
        <li><a href="index.php?ruta=pedidos">Pedidos</a></li>
        <li><span class="ped-breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span></li>
        <li class="active">Pedido #<?php echo htmlspecialchars($_GET["idPedido"]); ?></li>
      </ul>
      <div class="ped-header-row">
        <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
          <a href="index.php?ruta=pedidos" class="ped-back" title="Volver">
            <i class="fa-solid fa-arrow-left"></i>
          </a>
          <h1>Pedido #<?php echo htmlspecialchars($_GET["idPedido"]); ?></h1>
          <span class="ped-status <?php echo $statusClass; ?>"><?php echo htmlspecialchars($estadoRaw); ?></span>
        </div>
        <?php if ($puedeAsignarPedido): ?>
        <button class="ped-btn ped-btn-outline" data-toggle="modal" data-target="#modalAsignarPedido">
          <i class="fa-solid fa-link"></i> Asignar a Orden
        </button>
        <?php endif; ?>
      </div>
    </div>

    <form role="form" method="post">
      <input type="hidden" value="<?php echo htmlspecialchars($_GET["idPedido"]); ?>" name="idPedido">
      <input type="hidden" class="PagosListados" name="PagosListados">
      <input type="hidden" id="listarObservacionesPedidos" name="listarObservacionesPedidos">

    <div class="row">

      <!-- ══════════════════════════════════════
           LEFT COLUMN — Client + Order Info
      ══════════════════════════════════════ -->
      <div class="col-md-5 col-lg-4">

        <!-- Client Card -->
        <div class="ped-card">
          <div class="ped-card-head">
            <h4 class="ped-card-title"><i class="fa-solid fa-user"></i> Información del Cliente</h4>
          </div>
          <div class="ped-card-body">
            <div class="ped-info-row">
              <div class="ped-info-icon blue"><i class="fa-solid fa-user"></i></div>
              <div style="flex:1;">
                <div class="ped-info-label">Nombre</div>
                <div class="ped-info-value"><?php echo htmlspecialchars($usuario["nombre"]); ?></div>
              </div>
              <a href="<?php echo $historialClienteLink; ?>" class="ped-btn ped-btn-outline" style="padding:7px 12px; font-size:12px;" title="Ver historial del cliente">
                <i class="fa-solid fa-clock-rotate-left"></i> Historial
              </a>
            </div>
            <?php
              $correoValid = !empty($usuario["correo"]) && filter_var($usuario["correo"], FILTER_VALIDATE_EMAIL);
            ?>
            <?php if ($correoValid): ?>
            <div class="ped-info-row">
              <div class="ped-info-icon purple"><i class="fa-solid fa-envelope"></i></div>
              <div>
                <div class="ped-info-label">Correo</div>
                <div class="ped-info-value"><?php echo htmlspecialchars($usuario["correo"]); ?></div>
              </div>
            </div>
            <?php endif; ?>
            <?php
              $tel1Raw = isset($usuario["telefono"]) ? preg_replace('/[^0-9]/', '', $usuario["telefono"]) : '';
              $tel2Raw = isset($usuario["telefonoDos"]) ? preg_replace('/[^0-9]/', '', $usuario["telefonoDos"]) : '';
              $tel1Valid = strlen($tel1Raw) >= 10;
              $tel2Valid = strlen($tel2Raw) >= 10;
            ?>
            <?php if ($tel1Valid): ?>
            <div class="ped-info-row">
              <div class="ped-info-icon green"><i class="fa-solid fa-phone"></i></div>
              <div style="flex:1;">
                <div class="ped-info-label">Teléfono</div>
                <div class="ped-info-value"><?php echo htmlspecialchars($usuario["telefono"]); ?></div>
              </div>
              <a href="https://wa.me/52<?php echo $tel1Raw; ?>" target="_blank" class="ped-wa-btn" title="Enviar WhatsApp">
                <i class="fa-brands fa-whatsapp"></i>
              </a>
            </div>
            <?php endif; ?>
            <?php if ($tel2Valid): ?>
            <div class="ped-info-row">
              <div class="ped-info-icon green"><i class="fa-solid fa-phone"></i></div>
              <div style="flex:1;">
                <div class="ped-info-label">Teléfono 2</div>
                <div class="ped-info-value"><?php echo htmlspecialchars($usuario["telefonoDos"]); ?></div>
              </div>
              <a href="https://wa.me/52<?php echo $tel2Raw; ?>" target="_blank" class="ped-wa-btn" title="Enviar WhatsApp">
                <i class="fa-brands fa-whatsapp"></i>
              </a>
            </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Order / Assignment Card -->
        <div class="ped-card">
          <div class="ped-card-head">
            <h4 class="ped-card-title"><i class="fa-solid fa-clipboard-list"></i> Asignación</h4>
          </div>
          <div class="ped-card-body">

            <!-- Estado -->
              <div class="ped-info-row" style="flex-wrap:wrap; gap:8px;">
                <div class="ped-info-icon orange"><i class="fa-solid fa-toggle-on"></i></div>
                <div style="flex:1; min-width:160px;">
                  <div class="ped-info-label">Estado del Pedido</div>
                  <?php if ($puedeEditarPedidoCompleto): ?>
                    <select class="ped-select" name="EstadoPedidoDinamico" style="margin-top:6px;">
                      <option value="<?php echo htmlspecialchars($valuePedidos["estado"]); ?>"><?php echo htmlspecialchars($valuePedidos["estado"]); ?></option>
                      <option value="Pedido Pendiente">Pedido Pendiente</option>
                      <option value="Pedido Adquirido">Pedido Adquirido</option>
                      <option value="Producto en Almacen">Producto en Almacén</option>
                      <option value="Entregado al asesor">Entregado al Asesor</option>
                      <option value="Entregado/Pagado">Entregado/Pagado</option>
                      <option value="Entregado/Credito">Entregado/Crédito</option>
                      <option value="cancelado">Cancelado</option>
                    </select>
                  <?php else: ?>
                    <div class="ped-info-value"><?php echo htmlspecialchars($valuePedidos["estado"]); ?></div>
                  <?php endif; ?>
                </div>
              </div>

            <div class="ped-info-row">
              <div class="ped-info-icon indigo"><i class="fa-solid fa-headset"></i></div>
              <div>
                <div class="ped-info-label">Asesor</div>
                <div class="ped-info-value"><?php echo htmlspecialchars($asesor["nombre"]); ?></div>
              </div>
            </div>
            <?php if ($tieneOrden): ?>
            <div class="ped-info-row">
              <div class="ped-info-icon blue"><i class="fa-solid fa-hashtag"></i></div>
              <div>
                <div class="ped-info-label">Orden Asociada</div>
                <div class="ped-info-value">#<?php echo htmlspecialchars($valuePedidos["id_orden"]); ?></div>
              </div>
            </div>
            <?php endif; ?>
            <?php if ($tieneTecnico): ?>
            <div class="ped-info-row">
              <div class="ped-info-icon purple"><i class="fa-solid fa-screwdriver-wrench"></i></div>
              <div>
                <div class="ped-info-label">Técnico</div>
                <div class="ped-info-value"><?php echo htmlspecialchars($respuesta["nombre"]); ?></div>
              </div>
            </div>
            <?php endif; ?>
          </div>
        </div>

      </div>

      <!-- ══════════════════════════════════════
           RIGHT COLUMN — Products + Observations
      ══════════════════════════════════════ -->
      <div class="col-md-7 col-lg-8">

        <!-- Products Card -->
        <div class="ped-card">
          <div class="ped-card-head">
            <h4 class="ped-card-title"><i class="fa-solid fa-box-open"></i> Productos del Pedido</h4>
          </div>
          <div class="ped-card-body" style="padding:0;">
            <table class="ped-products-table">
              <thead>
                <tr>
                  <th style="width:50%;">Producto</th>
                  <th style="width:15%;">Cantidad</th>
                  <th style="width:20%;">Precio</th>
                  <th style="width:15%;">Subtotal</th>
                </tr>
              </thead>
              <tbody>
                <?php
                  // Legacy products (productoUno ... ProductoCinco)
                  if ($valuePedidos["productoUno"] != "undefined" && $valuePedidos["productoUno"] != null) {
                    $sub = (float)$valuePedidos["cantidaProductoUno"] * (float)$valuePedidos["precioProductoUno"];
                    echo '<tr>
                      <td>'.htmlspecialchars($valuePedidos["productoUno"]).'</td>
                      <td>'.htmlspecialchars($valuePedidos["cantidaProductoUno"]).'</td>
                      <td class="ped-input-cell"><input type="number" value="'.htmlspecialchars($valuePedidos["precioProductoUno"]).'" readonly></td>
                      <td>$'.number_format($sub, 2).'</td>
                    </tr>';
                  }
                  if ($valuePedidos["ProductoDos"] != "undefined" && $valuePedidos["ProductoDos"] != null) {
                    $sub = (float)$valuePedidos["cantidadProductoDos"] * (float)$valuePedidos["precioProductoDos"];
                    echo '<tr>
                      <td>'.htmlspecialchars($valuePedidos["ProductoDos"]).'</td>
                      <td>'.htmlspecialchars($valuePedidos["cantidadProductoDos"]).'</td>
                      <td>$'.number_format((float)$valuePedidos["precioProductoDos"], 2).'</td>
                      <td>$'.number_format($sub, 2).'</td>
                    </tr>';
                  }
                  if ($valuePedidos["ProductoTres"] != "undefined" && $valuePedidos["ProductoTres"] != null) {
                    $sub = (float)$valuePedidos["cantidadProductoTres"] * (float)$valuePedidos["precioProductoTres"];
                    echo '<tr>
                      <td>'.htmlspecialchars($valuePedidos["ProductoTres"]).'</td>
                      <td>'.htmlspecialchars($valuePedidos["cantidadProductoTres"]).'</td>
                      <td>$'.number_format((float)$valuePedidos["precioProductoTres"], 2).'</td>
                      <td>$'.number_format($sub, 2).'</td>
                    </tr>';
                  }
                  if ($valuePedidos["ProductoCuatro"] != "undefined" && $valuePedidos["ProductoCuatro"] != null) {
                    $sub = (float)$valuePedidos["cantidadProductoCuatro"] * (float)$valuePedidos["precioProductoCuatro"];
                    echo '<tr>
                      <td>'.htmlspecialchars($valuePedidos["ProductoCuatro"]).'</td>
                      <td>'.htmlspecialchars($valuePedidos["cantidadProductoCuatro"]).'</td>
                      <td>$'.number_format((float)$valuePedidos["precioProductoCuatro"], 2).'</td>
                      <td>$'.number_format($sub, 2).'</td>
                    </tr>';
                  }
                  if ($valuePedidos["ProductoCinco"] != "undefined" && $valuePedidos["ProductoCinco"] != null) {
                    $sub = (float)$valuePedidos["cantidadProductoCinco"] * (float)$valuePedidos["precioProductoCinco"];
                    echo '<tr>
                      <td>'.htmlspecialchars($valuePedidos["ProductoCinco"]).'</td>
                      <td>'.htmlspecialchars($valuePedidos["cantidadProductoCinco"]).'</td>
                      <td>$'.number_format((float)$valuePedidos["precioProductoCinco"], 2).'</td>
                      <td>$'.number_format($sub, 2).'</td>
                    </tr>';
                  }

                  // Dynamic JSON products
                  $productos = json_decode($valuePedidos["productos"], true);
                  if (is_array($productos)) {
                    foreach ($productos as $key => $valueProductos) {
                      $ro = $puedeEditarPedidoCompleto ? '' : ' readonly';
                      echo '<tr>
                        <td class="ped-input-cell"><input type="text" value="'.htmlspecialchars($valueProductos["Descripcion"]).'" class="descripcioParaListar"'.$ro.'></td>
                        <td class="ped-input-cell"><input type="number" value="'.htmlspecialchars($valueProductos["cantidad"]).'" class="cantidadProductoParaListar"'.$ro.'></td>
                        <td class="ped-input-cell"><input type="number" value="'.htmlspecialchars($valueProductos["precio"]).'" class="precioProductoParaListar"'.$ro.'></td>
                        <td></td>
                      </tr>';
                    }
                  }
                ?>
                <input type="hidden" id="ListarPreciosActualizados" name="ListarPreciosActualizados">
                <input type="hidden" id="listarObservacionesPedidos" name="listarObservacionesPedidos">
                <input type="hidden" class="PagosListados" name="PagosListados">

                <!-- Total row -->
                <tr class="ped-total-row">
                  <td colspan="3" style="text-align:right; padding-right:20px;">
                    <strong>Total del Pedido</strong>
                  </td>
                  <td class="ped-input-cell">
                    <div style="display:flex;align-items:center;gap:6px;">
                      <span style="font-weight:800;color:var(--crm-accent);font-size:16px;">$</span>
                      <input type="number" class="form-control totalPagarPedidoDinamico" name="totalPagarPedidoDinamico" value="<?php echo htmlspecialchars($valuePedidos["total"]); ?>" <?php echo $puedeEditarPedidoCompleto ? '' : 'readonly'; ?>
                        style="border:1px solid var(--crm-border);border-radius:8px;font-weight:800;font-size:16px;color:var(--crm-accent);padding:6px 10px;">
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- ══════════════════════════════════════
             PAYMENTS CARD
        ══════════════════════════════════════ -->
        <div class="ped-card">
          <div class="ped-card-head">
            <h4 class="ped-card-title"><i class="fa-solid fa-credit-card"></i> Pagos y Abonos</h4>
            <?php if ($puedeEditarPedidoCompleto): ?>
            <button type="button" class="ped-btn ped-btn-outline btnToggleNewPayment" style="padding:6px 14px; font-size:12px;">
              <i class="fa-solid fa-plus"></i> Nuevo Pago
            </button>
            <?php endif; ?>
          </div>
          <div class="ped-card-body">

            <?php
              $pagos = json_decode($valuePedidos["pagos"], true);

              // Show initial payment
              if ($valuePedidos["pagoPedido"] != null && $valuePedidos["pagoPedido"] != "" && $valuePedidos["pagoPedido"] != 0):
            ?>
              <div class="ped-payment-item">
                <div class="ped-payment-icon"><i class="fa-solid fa-coins"></i></div>
                <div style="flex:1;">
                  <div class="ped-info-label">Pago Inicial</div>
                  <div class="ped-payment-amount">$<?php echo number_format((float)$valuePedidos["pagoPedido"], 2); ?></div>
                </div>
                <div class="ped-payment-date" style="font-size:11px; color:var(--crm-muted);">Primer pago</div>
              </div>
              <input type="hidden" value="<?php echo htmlspecialchars($valuePedidos["pagoPedido"]); ?>">
            <?php endif; ?>

            <!-- Existing payments list -->
            <div class="agregarCamposPago">
            <?php
              if ($pagos != null && $pagos != ""):
                $abonoNum = 1;
                foreach ($pagos as $key => $valuePagos):
            ?>
              <div class="ped-payment-item">
                <div class="ped-payment-icon"><i class="fa-solid fa-money-bill-wave"></i></div>
                <div style="flex:1;">
                  <div class="ped-info-label">Abono #<?php echo $abonoNum; ?></div>
                  <input type="number" class="form-control pagoAbonado" value="<?php echo htmlspecialchars($valuePagos["pago"]); ?>" readonly style="border:none;background:transparent;font-size:14px;font-weight:700;padding:0;height:auto;color:var(--crm-text);box-shadow:none;">
                </div>
                <div class="ped-payment-date">
                  <input type="date" class="form-control fechaAbono" value="<?php echo htmlspecialchars($valuePagos["fecha"]); ?>" readonly style="border:none;background:transparent;font-size:12px;color:var(--crm-muted);box-shadow:none;text-align:right;">
                </div>
              </div>
            <?php
                  $abonoNum++;
                endforeach;
              endif;
            ?>
            </div>

            <!-- New payment inline form -->
            <?php if ($puedeEditarPedidoCompleto): ?>
            <div class="ped-new-payment" id="pedNewPaymentForm">
              <div class="ped-new-payment-title">
                <i class="fa-solid fa-plus-circle"></i> Registrar Nuevo Abono
              </div>
              <div class="ped-new-payment-row">
                <div class="ped-new-payment-field">
                  <label>Monto del abono</label>
                  <input type="number" class="pagoAbonado" placeholder="$0.00" min="0" step="any">
                </div>
                <div class="ped-new-payment-field">
                  <label>Fecha del pago</label>
                  <input type="date" class="fechaAbono">
                </div>
              </div>
            </div>
            <?php endif; ?>

            <!-- Hidden structure for legacy JS compatibility -->
            <div class="nuevoCampoPagoPedido" style="display:none;"></div>

            <!-- Summary boxes -->
            <div class="ped-summary-box">
              <div class="ped-summary-item total">
                <div class="ped-summary-label">Total Pagado</div>
                <div class="ped-summary-value">
                  <input type="number" class="form-control totalPagosPeiddoDinamico" readonly
                    style="border:none;background:transparent;text-align:center;font-size:18px;font-weight:800;color:#1e40af;box-shadow:none;padding:0;height:auto;">
                </div>
              </div>
              <div class="ped-summary-item debt">
                <div class="ped-summary-label">Adeudo</div>
                <div class="ped-summary-value">
                  <input type="number" class="form-control adeudoPedidoDinamico" name="adeudoPedidoDinamico" readonly value="<?php echo htmlspecialchars($valuePedidos["adeudo"]); ?>"
                    style="border:none;background:transparent;text-align:center;font-size:18px;font-weight:800;color:#991b1b;box-shadow:none;padding:0;height:auto;">
                </div>
              </div>
            </div>

            <?php if (!$puedeEditarPedidoCompleto): ?>
            <div style="margin-top:16px;padding:12px 14px;border-radius:10px;background:#f8fafc;color:var(--crm-text2);font-size:12px;line-height:1.5;">
              Solo el administrador puede editar productos, pagos, abonos, total y estado del pedido.
            </div>
            <?php endif; ?>

            <?php if ($puedeEditarPedidoCompleto): ?>
            <div style="margin-top:16px; text-align:right;">
              <button type="submit" class="ped-btn ped-btn-success">
                <i class="fa-solid fa-floppy-disk"></i> Guardar Pedido
              </button>
            </div>
            <?php endif; ?>

          </div>
        </div>

        <!-- Observations Card -->
        <div class="ped-card">
          <div class="ped-card-head">
            <h4 class="ped-card-title"><i class="fa-solid fa-comments"></i> Observaciones</h4>
            <span style="font-size:12px; color:var(--crm-muted); font-weight:500;">
              <?php
                $observaciones = json_decode($valuePedidos["observaciones"], true);
                $obsCount = is_array($observaciones) ? count($observaciones) : 0;
                echo $obsCount . ' comentario' . ($obsCount != 1 ? 's' : '');
              ?>
            </span>
          </div>
          <div class="ped-card-body">

            <?php echo '<input type="hidden" class="usuarioActualPedido" value="'.htmlspecialchars($_SESSION["nombre"]).'">'; ?>
            <textarea class="form-control input-lg" id="fechaVista" style="display:none;"></textarea>

            <!-- Compose new observation (inline, always visible for authorized users) -->
            <?php if ($puedeAgregarObservaciones): ?>
              <?php
                $sesIni = pedGetInitials($_SESSION["nombre"]);
                $sesGrad = pedGetGrad($_SESSION["nombre"], $_obsGrads);
              ?>
              <div class="ped-obs-compose" id="pedObsCompose">
                <div class="ped-obs-avatar" style="background:<?php echo $sesGrad; ?>;"><?php echo $sesIni; ?></div>
                <div class="ped-obs-compose-body">
                  <textarea id="pedNewObsText" class="form-control" placeholder="Escribe una observación..." rows="2"></textarea>
                  <div class="ped-obs-compose-actions">
                    <button type="button" class="ped-btn ped-btn-primary AgregarCampoDeObservacionPedidos" style="padding:7px 16px; font-size:12px;">
                      <i class="fa-solid fa-paper-plane"></i> Agregar
                    </button>
                  </div>
                </div>
              </div>
            <?php endif; ?>

            <!-- Hidden legacy container for JS serialization -->
            <div class="cajaObervacionesPedidos" style="display:none;">
              <div class="agregarcampoobervacionesPedidos"></div>
            </div>

            <!-- Existing observations -->
            <?php if (is_array($observaciones) && count($observaciones) > 0): ?>
              <div class="ped-obs-list">
                <?php foreach ($observaciones as $key => $valueObservaciones):
                  if ($puedeAgregarObservaciones || $_SESSION["perfil"] == "tecnico"):
                    $obsName = isset($valueObservaciones["creador"]) ? $valueObservaciones["creador"] : 'Usuario';
                    $obsIni  = pedGetInitials($obsName);
                    $obsGrad = pedGetGrad($obsName, $_obsGrads);
                ?>
                  <div class="ped-obs-item">
                    <div class="ped-obs-avatar" style="background:<?php echo $obsGrad; ?>;"><?php echo $obsIni; ?></div>
                    <div class="ped-obs-body">
                      <div class="ped-obs-header">
                        <span class="ped-obs-name"><?php echo htmlspecialchars($obsName); ?></span>
                        <span class="ped-obs-date"><?php echo htmlspecialchars($valueObservaciones["fecha"]); ?></span>
                      </div>
                      <div class="ped-obs-content">
                        <textarea class="nuevaObservacion" readonly data-creador="<?php echo htmlspecialchars($obsName); ?>" fecha="<?php echo htmlspecialchars($valueObservaciones["fecha"]); ?>"><?php echo htmlspecialchars($valueObservaciones["observacion"]); ?></textarea>
                      </div>
                    </div>
                  </div>
                <?php
                  endif;
                endforeach; ?>
              </div>
            <?php else: ?>
              <div style="text-align:center; padding:24px 0; color:var(--crm-muted);">
                <i class="fa-solid fa-message" style="font-size:28px; opacity:.3; margin-bottom:8px;"></i>
                <p style="margin:0; font-size:13px;">No hay observaciones registradas</p>
              </div>
            <?php endif; ?>

            <?php if ($puedeAgregarObservaciones && !$puedeEditarPedidoCompleto): ?>
            <div style="margin-top:16px; text-align:right;">
              <button type="submit" class="ped-btn ped-btn-success">
                <i class="fa-solid fa-floppy-disk"></i> Guardar observaciones
              </button>
            </div>
            <?php endif; ?>

          </div>
        </div>

      </div><!-- /right col -->

    </div><!-- /row -->
    </form>

    <?php
      $editarOrdenDinamica = new ControladorPedidos();
      $editarOrdenDinamica->ctrEditarOrdenDinamica();
    ?>

  </section>

</div><!-- /content-wrapper -->


<!-- ══════════════════════════════════════════════════════
     MODAL — Asignar Pedido a Orden
══════════════════════════════════════════════════════ -->
<div id="modalAsignarPedido" class="modal fade" role="dialog">
  <form role="form" method="post" class="formularioPedidosDinamicos">
    <div class="modal-dialog">
      <div class="modal-content" style="border-radius:var(--crm-radius); overflow:hidden; border:none; box-shadow:var(--crm-shadow-lg);">

        <!-- Header -->
        <div class="modal-header" style="background:linear-gradient(135deg, #6366f1 0%, #818cf8 100%); color:#fff; border:none; padding:22px 28px 18px;">
          <button type="button" class="close" data-dismiss="modal" style="color:#fff; opacity:.7; font-size:22px; margin-top:-4px;">&times;</button>
          <h4 style="margin:0; font-weight:800; font-size:17px; letter-spacing:-.01em;">
            <i class="fa-solid fa-link" style="margin-right:10px; opacity:.8;"></i> Asignar Pedido a Orden
          </h4>
          <p style="margin:4px 0 0; font-size:12px; opacity:.7;">Vincula este pedido con una orden de trabajo existente</p>
        </div>

        <!-- Body -->
        <div class="ped-modal-body">

          <!-- Pedido info box -->
          <div class="ped-modal-section-title">
            <i class="fa-solid fa-file-invoice"></i> Pedido actual
          </div>
          <div class="ped-modal-info-box">
            <div class="ped-modal-info-icon"><i class="fa-solid fa-file-invoice"></i></div>
            <div>
              <div class="ped-modal-info-label">Pedido seleccionado</div>
              <div class="ped-modal-info-value">#<?php echo htmlspecialchars($_GET["idPedido"]); ?></div>
            </div>
            <input type="hidden" name="AsignarPedidoDinamico" value="<?php echo htmlspecialchars($_GET["idPedido"]); ?>">
          </div>

          <!-- Orden selector -->
          <div class="ped-modal-section-title">
            <i class="fa-solid fa-clipboard-list"></i> Seleccionar orden destino
          </div>
          <div style="margin-bottom:8px;">
            <select class="ped-select" id="selectorOrdenChoices" name="AsignarOrdenDinamico">
              <option value="" placeholder>Buscar orden por número...</option>
              <?php
                $orden = controladorOrdenes::ctrMostrarOrdenesSuma();
                foreach ($orden as $key => $valueOrden) {
                  echo '<option value="'.htmlspecialchars($valueOrden["id"]).'">Orden #'.htmlspecialchars($valueOrden["id"]).'</option>';
                }
              ?>
            </select>
          </div>
          <p style="font-size:11px; color:var(--crm-muted); margin:0;"><i class="fa-solid fa-circle-info" style="margin-right:4px;"></i> Escribe el número de orden para filtrar resultados</p>

        </div>

        <!-- Footer -->
        <div class="modal-footer" style="border-top:1px solid var(--crm-border); padding:16px 28px; display:flex; justify-content:flex-end; gap:10px;">
          <button type="button" class="ped-btn ped-btn-outline" data-dismiss="modal">
            <i class="fa-solid fa-xmark"></i> Cancelar
          </button>
          <button type="submit" class="ped-btn ped-btn-primary">
            <i class="fa-solid fa-check"></i> Asignar Orden
          </button>
        </div>

      </div>
    </div>
  </form>
  <?php
    $crearPedido = new ControladorPedidos();
    $crearPedido->ctrAsignarPedidoEnOrden();
  ?>
</div>

<script>
/*=============================================
INIT CHOICES.JS ON MODAL OPEN
=============================================*/
var ordenChoicesInstance = null;
$('#modalAsignarPedido').on('shown.bs.modal', function() {
  if (!ordenChoicesInstance) {
    ordenChoicesInstance = new Choices('#selectorOrdenChoices', {
      searchEnabled: true,
      searchPlaceholderValue: 'Escribe para buscar...',
      placeholderValue: 'Buscar orden...',
      itemSelectText: 'Seleccionar',
      noResultsText: 'Sin resultados',
      noChoicesText: 'No hay opciones',
      shouldSort: false,
      searchResultLimit: 20
    });
  }
});

/*=============================================
AGREGAR CAMPOS DINAMICAMENTE OBSERVACION
=============================================*/
var nextinput = 0;
var nextinputPrecio = 0;
var nextCantidad = 0;
function AgregarCamposPedidos(){
nextinput++;
nextinputPrecio++;
nextCantidad++;
campo = '<div class="form-group row"><div class="col-xs-6"><div class="input-group"><span class="input-group-addon"><i class="fa fa-product-hunt"></i></span><input class="form-control input-lg Producto'+nextinput+'" type="text" placeholder="Nombre Del Producto"></div></div><div class="col-xs-6"><div class="input-group"><input class="form-control input-lg precioProducto precioProducto'+nextinputPrecio+'" type="number" value="0" min="0" step="any" placeholder="Precio" id="precioUno"><span class="input-group-addon"><i class="fa fa-dollar"></i></span></div></div></div><div class="form-group"><div class="input-group"><span class="input-group-addon"><i class="fa fa-arrow-circle-up"></i></span><input type="number" class="form-control input-lg cantidadProducto cantidadProducto'+nextCantidad+'" placeholder="cantidad"></div></div>';
$("#camposProductos").append(campo);
}

/*=============================================
REALIZAR OPERACIONES PRODUCTO UNO
=============================================*/
$(document).on("keyup", function() {


        $(".precioProducto1").each(function() {
            var $cantidadPedidoUno = $(".cantidadProducto1").val();
            var $precioPedidoUno = $(".precioProducto1").val();
            
            var $totalUno = $cantidadPedidoUno * $precioPedidoUno;


            
            $(".pagoPedido").val($totalUno);
            $(".totalPedidoUno").val($totalUno);

        });
});
/*=============================================
REALIZAR OPERACIONES PRODUCTO DOS
=============================================*/
$(document).on("change", function() {


        $(".precioProducto2").each(function() {
            var $cantidadPedidoUno = $(".cantidadProducto1").val();
            var $precioPedidoUno = $(".precioProducto1").val();
            var $cantidadPedidoDos = $(".cantidadProducto2").val();
            var $precioPedidoDos = $(".precioProducto2").val();

            var $totalUno = $cantidadPedidoUno * $precioPedidoUno;
            var $calculoTotalDos = $cantidadPedidoDos * $precioPedidoDos;
            var $totalDos = $totalUno + $calculoTotalDos;
            
            $(".pagoPedido").val($totalDos);
            $(".totalPedidoDos").val($calculoTotalDos);
        });
});
/*=============================================
REALIZAR OPERACIONES PRODUCTO TRES
=============================================*/
$(document).on("change", function() {


        $(".precioProducto3").each(function() {
            var $cantidadPedidoUno = $(".cantidadProducto1").val();
            var $precioPedidoUno = $(".precioProducto1").val();
            var $cantidadPedidoDos = $(".cantidadProducto2").val();
            var $precioPedidoDos = $(".precioProducto2").val();
            var $cantidadPedidoTres = $(".cantidadProducto3").val();
            var $precioPedidoTres = $(".precioProducto3").val();

            var $totalUno = $cantidadPedidoUno * $precioPedidoUno;
            var $calculoTotalDos = $cantidadPedidoDos * $precioPedidoDos;
            var $calculoTotalTres = $cantidadPedidoTres * $precioPedidoTres;
            var $TotalTres = $totalUno + $calculoTotalDos + $calculoTotalTres;
            
            $(".pagoPedido").val($TotalTres);
           $(".totalPedidoTres").val($calculoTotalTres);
        });
});
/*=============================================
REALIZAR OPERACIONES PRODUCTO CUATRO
=============================================*/
$(document).on("change", function() {


        $(".precioProducto3").each(function() {
            var $cantidadPedidoUno = $(".cantidadProducto1").val();
            var $precioPedidoUno = $(".precioProducto1").val();
            var $cantidadPedidoDos = $(".cantidadProducto2").val();
            var $precioPedidoDos = $(".precioProducto2").val();
            var $cantidadPedidoTres = $(".cantidadProducto3").val();
            var $precioPedidoTres = $(".precioProducto3").val();
            var $cantidadPedidoCuatro = $(".cantidadProducto4").val();
            var $precioPedidoCuatro = $(".precioProducto4").val();

            var $totalUno = $cantidadPedidoUno * $precioPedidoUno;
            var $calculoTotalDos = $cantidadPedidoDos * $precioPedidoDos;
            var $calculoTotalTres = $cantidadPedidoTres * $precioPedidoTres;
            var $calculoTotalCuatro = $cantidadPedidoCuatro * $precioPedidoCuatro;
            var $TotalCuatro = $totalUno + $calculoTotalDos + $calculoTotalTres + $calculoTotalCuatro;

            $(".pagoPedido").val($TotalCuatro);
            $(".totalPedidoCuatro").val($calculoTotalCuatro);
        });
});
/*=============================================
REALIZAR OPERACIONES PRODUCTO CINCO
=============================================*/
$(document).on("keyup", function() {


        $(".precioProducto3").each(function() {
            var $cantidadPedidoUno = $(".cantidadProducto1").val();
            var $precioPedidoUno = $(".precioProducto1").val();
            var $cantidadPedidoDos = $(".cantidadProducto2").val();
            var $precioPedidoDos = $(".precioProducto2").val();
            var $cantidadPedidoTres = $(".cantidadProducto3").val();
            var $precioPedidoTres = $(".precioProducto3").val();
			var $cantidadPedidoCuatro = $(".cantidadProducto4").val();
            var $precioPedidoCuatro = $(".precioProducto4").val();
            var $cantidadPedidoCinco = $(".cantidadProducto5").val();
            var $precioPedidoCinco = $(".precioProducto5").val();

            var $totalUno = $cantidadPedidoUno * $precioPedidoUno;
            var $calculoTotalDos = $cantidadPedidoDos * $precioPedidoDos;
            var $calculoTotalTres = $cantidadPedidoTres * $precioPedidoTres;
            var $calculoTotalCuatro = $cantidadPedidoCuatro * $precioPedidoCuatro;
            var $calculoTotalCinco = $cantidadPedidoCinco * $precioPedidoCinco;
            var $TotalCinco = $totalUno + $calculoTotalDos + $calculoTotalTres + $calculoTotalCuatro + $calculoTotalCinco;
            
            $(".pagoPedido").val($TotalCinco);
            $(".totalPedidoCinco").val($TotalCinco);
        });
});
/*=============================================
CALCULO DE ADEUDO
=============================================*/
$(document).on("change", "#pagoClientePedido", function() {


        $("#pagoClientePedido").each(function() {

            var $pagoClientePedido = $("#pagoClientePedido").val();
            var $TotalDelPedido = $("#ResultadoPedido").val();


            var $TotalAdeudo = parseFloat($TotalDelPedido) - parseFloat($pagoClientePedido);
            
            $("#adeudo").val($TotalAdeudo);
        });
});
/*=============================================
GUARDAR EL PEDIDO
=============================================*/


$(".guardarPedido").click(function(){

   
    agregarMiPedido();          

})

function agregarMiPedido(){

        /*=============================================
        ALMACENAMOS TODOS LOS CAMPOS DE PEDIDO
        =============================================*/
       var empresaPedido = $(".empresaPedido").val();
       var AsesorPedido = $(".AsesorPedido").val();
       var clientePeido = $(".clientePeido").val();
       var Producto1 = $(".Producto1").val();
       var precioProducto1 = $(".precioProducto1").val();
       var cantidadProducto1 = $(".cantidadProducto1").val();
       var totalPedidoUno = $(".totalPedidoUno").val();
       var Producto2 = $(".Producto2").val();
       var precioProducto2 = $(".precioProducto2").val();
       var cantidadProducto2 = $(".cantidadProducto2").val();
       var totalPedidoDos = $(".totalPedidoDos").val();
       var Producto3 = $(".Producto3").val();
       var precioProducto3 = $(".precioProducto3").val();
       var cantidadProducto3 = $(".cantidadProducto3").val();
       var totalPedidoTres = $(".totalPedidoTres").val();
       var Producto4 = $(".Producto4").val();
       var precioProducto4 = $(".precioProducto4").val();
       var cantidadProducto4 = $(".cantidadProducto4").val();
       var totalPedidoCuatro = $(".totalPedidoCuatro").val();
       var Producto5 = $(".Producto5").val();
       var precioProducto5 = $(".precioProducto5").val();
       var cantidadProducto5 = $(".cantidadProducto5").val();
       var totalPedidoCinco = $(".totalPedidoCinco").val();
       var metodo = $(".metodo").val();
       var IngresarEstadoDelPedido = $(".IngresarEstadoDelPedido").val();

       var pagoClientePedido = $(".pagoClientePedido").val();
       var pagoPedido = $(".pagoPedido").val();
       var adeudo = $(".adeudo").val();
       var fechaEntrega = $(".fechaEntrega").val();
         
        var datospedido = new FormData();
        datospedido.append("empresaPedido", empresaPedido);
        datospedido.append("AsesorPedido", AsesorPedido);
        datospedido.append("clientePeido", clientePeido);
        datospedido.append("Producto1", Producto1);
        datospedido.append("precioProducto1", precioProducto1);
        datospedido.append("cantidadProducto1", cantidadProducto1);
        datospedido.append("totalPedidoUno", totalPedidoUno);
        datospedido.append("Producto2", Producto2);
        datospedido.append("precioProducto2", precioProducto2);
        datospedido.append("cantidadProducto2", cantidadProducto2);
        datospedido.append("totalPedidoDos", totalPedidoDos);
        datospedido.append("Producto3", Producto3);
        datospedido.append("precioProducto3", precioProducto3);
        datospedido.append("cantidadProducto3", cantidadProducto3);              
        datospedido.append("totalPedidoTres", totalPedidoTres);
        datospedido.append("Producto4", Producto4);
        datospedido.append("precioProducto4", precioProducto4);      
        datospedido.append("cantidadProducto4", cantidadProducto4);
        datospedido.append("totalPedidoCuatro", totalPedidoCuatro);      
        datospedido.append("Producto5", Producto5);
        datospedido.append("precioProducto5", precioProducto5);      
        datospedido.append("cantidadProducto5", cantidadProducto5);
        datospedido.append("totalPedidoCinco", totalPedidoCinco);
        datospedido.append("metodo", metodo);
        datospedido.append("pagoClientePedido", pagoClientePedido);
        datospedido.append("pagoPedido", pagoPedido);
        datospedido.append("adeudo", adeudo);
        datospedido.append("fechaEntrega", fechaEntrega);
        datospedido.append("IngresarEstadoDelPedido", IngresarEstadoDelPedido);

        $.ajax({
                url:"ajax/pedidos.ajax.php",
                method: "POST",
                data: datospedido,
                cache: false,
                contentType: false,
                processData: false,
                success: function(respuesta){
                    
                     //console.log("respuesta", respuesta);

                    if(respuesta == "ok"){

                        swal({
                          type: "success",
                          title: "El pedido ha sido guardado correctamente",
                          showConfirmButton: true,
                          confirmButtonText: "Cerrar"
                          }).then(function(result){
                            if (result.value) {

                            window.location = "pedidos";

                            }
                        })
                    }

                }

        })

}

/*=============================================
IMPRIMIR TICKET DE ORDEN
=============================================*/
$(".tablaPedidos").on("click", ".btnImprimirTicketPedido", function(){
    //console.log("Editar");
  var idPedido = $(this).attr("idPedido");
  //console.log(idPedido);
  var empresa = $(this).attr("empresa");
  var asesor = $(this).attr("asesor");
  var cliente = $(this).attr("cliente");
  var datos = new FormData();
  datos.append("idPedido", idPedido);
  datos.append("empresa", empresa);
  datos.append("asesor", asesor);
  datos.append("cliente", cliente);
  $.ajax({


    url:"ajax/pedidos.ajax.php",
    method: "POST",
    data: datos,
    cache: false,
    contentType: false,
    processData: false,
    dataType: "json",
    success: function(respuesta){ 

         $("#printDetalles").val(respuesta["detalles"]);
         $("#cantidad").val(respuesta["cantidad"]);
         $("#cantidadPagada").val(respuesta["pago"]);
         $("#idPedido").val(respuesta["id"]);
         $("#empresa").val(respuesta["id_empresa"]);
         $("#asesor").val(respuesta["id_Asesor"]);
         $("#cliente").val(respuesta["id_usuario"]);
    
        //console.log("Datos usuario:", respuesta);

    }

  })
  window.open("https://backend.comercializadoraegs.com/extensiones/tcpdf/pdf/ticketpedido.php/?idPedido="+idPedido+"&empresa="+empresa+"&asesor="+asesor+"&cliente="+cliente+"", "_blank");


})

/*=============================================
EDITAR PEDIDO
=============================================*/
$('.tablaPedidos tbody').on("click", ".btnEditarPedido", function(){

  var idPedido = $(this).attr("idPedido");
  var datos = new FormData();
  datos.append("idPedido", idPedido);

  $.ajax({

    url:"ajax/pedidos.ajax.php",
    method: "POST",
    data: datos,
    cache: false,
    contentType: false,
    processData: false,
    dataType: "json",
    success: function(respuesta){ 
        
         $(".idPedido").val(respuesta[0]["id"]); 
         $(".edicionProductoUnoPedido").val(respuesta[0]["productoUno"]);
         $(".precioProductoPedidoEdicion").val(respuesta[0]["precioProductoUno"]);
         $(".cantidadDeProductoPedidoEditado").val(respuesta[0]["cantidaProductoUno"]);
         $(".pagoClientePedido").val(respuesta[0]["pagoPedido"]);
         $(".pagoPedidoEdidato").val(respuesta[0]["total"]);
         $(".adeudoPedidoEditado").val(respuesta[0]["adeudo"]);
         $(".fechaEntregaPedidoEditado").val(respuesta[0]["fechaEntrega"]);
         $(".optionEstadoPedido").html(respuesta[0]["estado"]);
         $(".NumeroDePedido").html(respuesta[0]["id"]);
         /*=============================================
         DATOS PEDIDO DOS
         =============================================*/
         $(".edicionProductoUnoPedidoDos").val(respuesta[0]["ProductoDos"]);
         $(".precioProductoPedidoEdicionDos").val(respuesta[0]["precioProductoDos"]);
         $(".cantidadDeProductoPedidoEditadoDos").val(respuesta[0]["cantidadProductoDos"]);
         //$(".pagoClientePedido").val(respuesta[0]["pagoPedido"]);


         /*=============================================
         DATOS PEDIDO TRES
         =============================================*/
         $(".edicionProductoUnoPedidoTres").val(respuesta[0]["ProductoTres"]);
         $(".precioProductoPedidoEdicionTres").val(respuesta[0]["precioProductoTres"]);
         $(".cantidadDeProductoPedidoEditadoTres").val(respuesta[0]["cantidadProductoTres"]);
         //$(".pagoClientePedido").val(respuesta[0]["pagoPedido"]);

         /*=============================================
         DATOS PEDIDO CUATRO
         =============================================*/
         $(".edicionProductoUnoPedidoCuatro").val(respuesta[0]["ProductoCuatro"]);
         $(".precioProductoPedidoEdicionCuatro").val(respuesta[0]["precioProductoCuatro"]);
         $(".cantidadDeProductoPedidoEditadoCuatro").val(respuesta[0]["cantidadProductoCuatro"]);
         //$(".pagoClientePedido").val(respuesta[0]["pagoPedido"]);

         /*=============================================
         DATOS PEDIDO CINCO
         =============================================*/
         $(".edicionProductoUnoPedidoCinco").val(respuesta[0]["ProductoCinco"]);
         $(".precioProductoPedidoEdicionCinco").val(respuesta[0]["precioProductoCinco"]);
         $(".cantidadDeProductoPedidoEditadoCinco").val(respuesta[0]["cantidadProductoCinco"]);
         //$(".pagoClientePedido").val(respuesta[0]["pagoPedido"]);


        //console.log("Datos pedidos:", respuesta[0]);


        if (respuesta[0]["productoUno"]!= "undefined"){

          $(".productoUnoEdicionMostrar").show();
          $(".cantidadProductosUnoPedidoEditados").show();
          
          //$(".multimediaFisica").hide();
        }

        if (respuesta[0]["ProductoDos"] != "undefined"){

          $(".productoDosEdicionMostrar").show();
          $(".cantidadProductosDosPedidoEditados").show();
          

        }else{


           $(".productoDosEdicionMostrar").hide();
           $(".cantidadProductosDosPedidoEditados").hide();
        }

        if (respuesta[0]["ProductoTres"] != "undefined"){

          $(".productoTresEdicionMostrar").show();
          $(".cantidadProductosTresPedidoEditados").show();
          

        }else{


          $(".productoTresEdicionMostrar").hide();
          $(".cantidadProductosTresPedidoEditados").hide();
        }

        if (respuesta[0]["ProductoCuatro"] != "undefined"){

          $(".productoCuatroEdicionMostrar").show();
          $(".cantidadProductosCuatroPedidoEditados").show();

        }else{

          $(".productoCuatroEdicionMostrar").hide();
          $(".cantidadProductosCuatroPedidoEditados").hide();
        }

        if (respuesta[0]["ProductoCinco"] != "undefined"){

          $(".productoCincoEdicionMostrar").show();
          $(".cantidadProductosCincoPedidoEditados").show();
          
        }else{

          $(".productoCincoEdicionMostrar").hide();
          $(".cantidadProductosCincoPedidoEditados").hide();


        }


         /*=============================================
         DATOS DE ABONO UNO
         =============================================*/
         $(".abono1Lectura").val(respuesta[0]["abonoUno"]);
         $(".fechaAbono1Lectura").val(respuesta[0]["fechaAbonoUno"]);
         
       /*=============================================
      TRAEMOS LOS ASESORES
      =============================================*/
      if (respuesta[0]["id_Asesor"] != 0){

        var datosAsesores = new FormData();
        datosAsesores.append("idAsesor", respuesta[0]["id_Asesor"]);


        $.ajax({

          url:"ajax/Asesores.ajax.php",
          method: "POST",
          data: datosAsesores,
          cache: false,
          contentType: false,
          processData: false,
          dataType: "json",
          success: function(respuesta){

            $(".asesorDePedido").val(respuesta["nombre"]);
    
          }
        })

      }
 /*=============================================
      TRAEMOS LOS DATOS DE CLIENTES
      =============================================*/
      if (respuesta[0]["id_cliente"] != 0){

        var datosAsesores = new FormData();
        datosAsesores.append("idCliente", respuesta[0]["id_cliente"]);


        $.ajax({

          url:"ajax/clientes.ajax.php",
          method: "POST",
          data: datosAsesores,
          cache: false,
          contentType: false,
          processData: false,
          dataType: "json",
          success: function(respuesta){
            //console.log("resp1", respuesta);
          $(".clienteNombre").val(respuesta["nombre"]);
          $(".clienteNumero").val(respuesta["telefono"]);
    
          }
        })

      }
      

    }

  })

})
/*=============================================
AGREGAR CAMPOS DINAMICAMENTE DE ABONO
=============================================*/
var nextinput = 0;
var nextinputPrecio = 0;
var nextFecha = 0;
function AgregarCampoDeaAbonoEditado(){
nextinput++;
nextinputPrecio++;
nextFecha++;
campo = '<div class="form-group row"><div class="col-xs-6"><span><h5><center>Abono</center></h5></span><div class="input-group"><span class="input-group-addon"><i class="fa fa-money"></i></span><input class="form-control input-lg abono'+nextinput+'" type="text" placeholder="Agregar Abono"></div></div><div class="col-xs-6"><span><h5><center>Fecha</center></h5></span><div class="input-group"><span class="input-group-addon"><i class="fa fa-date"></i></span><input type="date" class="form-control input-lg fechaAbono'+nextFecha+'" min="0" value="0" step="any">  </div>  </div></div>';
$("#camposAbono").append(campo);
}

/*=============================================
GUARDAR CAMBIOS DEL PEDIDO
=============================================*/ 
$(".guardarPedidoEditado").click(function(){

 btnEditarMiPedido(); 

})


function btnEditarMiPedido(){

  var idPedido = $("#modalEditarPedido .idPedido").val(); 
  var edicionProductoUnoPedido = $("#modalEditarPedido .edicionProductoUnoPedido").val();
  var abono1 = $("#modalEditarPedido .abono1").val();
  var fechaAbono1 = $("#modalEditarPedido .fechaAbono1").val();
  var edicionProductoUnoPedidoDos = $("#modalEditarPedido .edicionProductoUnoPedidoDos").val()
  var abono2 = $("#modalEditarPedido .abono2").val();
  var fechaAbono2 = $("#modalEditarPedido .fechaAbono2").val();
  var abono3 = $("#modalEditarPedido .abono3").val();
  var fechaAbono3 = $("#modalEditarPedido .fechaAbono3").val();
  var abono4 = $("#modalEditarPedido .abono4").val();
  var fechaAbono4 = $("#modalEditarPedido .fechaAbono4").val();
  var abono5 = $("#modalEditarPedido .abono5").val();
  var fechaAbono5 = $("#modalEditarPedido .fechaAbono5").val();
  var adeudoPedidoEditado = $("#modalEditarPedido .adeudoPedidoEditado").val();
  var EstadoDelPedido = $("#modalEditarPedido .EstadoDelPedido").val();


  var datosPedido = new FormData();
  datosPedido.append("id", idPedido);
  datosPedido.append("abono1", abono1);
  datosPedido.append("edicionProductoUnoPedido", edicionProductoUnoPedido);
  datosPedido.append("fechaAbono1", fechaAbono1);
  datosPedido.append("edicionProductoUnoPedidoDos", edicionProductoUnoPedidoDos);
  datosPedido.append("abono2", abono2);
  datosPedido.append("fechaAbono2", fechaAbono2);
  datosPedido.append("abono3", abono3);
  datosPedido.append("fechaAbono3", fechaAbono3);
  datosPedido.append("abono4", abono4);
  datosPedido.append("fechaAbono4", fechaAbono4);
  datosPedido.append("abono5", abono5);
  datosPedido.append("fechaAbono5", fechaAbono5);
  datosPedido.append("adeudoPedidoEditado", adeudoPedidoEditado);        
  datosPedido.append("EstadoDelPedido", EstadoDelPedido); 

  $.ajax({
      url:"ajax/pedidos.ajax.php",
      method: "POST",
      data: datosPedido,
      cache: false,
      contentType: false,
      processData: false,
      success: function(respuesta){
                  
                     
        if(respuesta == "ok"){

          swal({
            type: "success",
            title: "El abono ha sido agregado correctamente",
            showConfirmButton: true,
            confirmButtonText: "Cerrar"
            }).then(function(result){
            if (result.value) {

            window.location = "pedidos";

            }
          })
        }

      }

  })
  
}

/*=============================================
REALIZAR OPERACIONES DE ABONO UNO 
=============================================*/
$(document).on("change", function() {


        $(".abono1").each(function() {
            var $adeudoDelPedido = $(".adeudoPedidoEditado").val();
            var $primerAbono = $(".abono1").val();

            var $totalNuevoAdeudo = $adeudoDelPedido - $primerAbono;

            $(".adeudoPedidoEditado").val($totalNuevoAdeudo);
        });
});
/*=============================================
REALIZAR OPERACIONES DE ABONO DOS 
=============================================*/
$(document).on("change", function() {


        $(".abono2").each(function() {
            var $pagoTotal = $(".pagoPedidoEdidato").val();
            var $pagoInicial = $(".pagoClientePedido").val();
            var $primerAbono = $(".abono1").val();
            var $segudnoAbono = $(".abono2").val();
            var $totalAbonos = parseFloat($primerAbono) + parseFloat($segudnoAbono);
            var $totalNuevoAdeudoDos =  parseFloat($pagoTotal) - parseFloat($totalAbonos) - parseFloat($pagoInicial);

            $(".adeudoPedidoEditado").val($totalNuevoAdeudoDos);
        });
});
/*=============================================
REALIZAR OPERACIONES DE ABONO TRES 
=============================================*/
$(document).on("change", function() {


        $(".abono3").each(function() {
            var $pagoTotal = $(".pagoPedidoEdidato").val();
            var $pagoInicial = $(".pagoClientePedido").val();
            var $primerAbono = $(".abono1").val();
            var $segudnoAbono = $(".abono2").val();
            var $TercerAbono = $(".abono3").val();
            var $totalAbonos = parseFloat($primerAbono) + parseFloat($segudnoAbono) + parseFloat($TercerAbono);
            var $totalNuevoAdeudoTres =  parseFloat($pagoTotal) - parseFloat($totalAbonos) - parseFloat($pagoInicial);

            $(".adeudoPedidoEditado").val($totalNuevoAdeudoTres);
        });
});
/*=============================================
REALIZAR OPERACIONES DE ABONO CUATRO 
=============================================*/
$(document).on("change", function() {


        $(".abono4").each(function() {
            var $adeudoDelPedidoCuatro = $(".adeudoPedidoEditado").val();
            var $CuartoAbono = $(".abono4").val();

            var $totalNuevoAdeudoCuatro =  $adeudoDelPedidoCuatro - $CuartoAbono;

            $(".adeudoPedidoEditado").val($totalNuevoAdeudoCuatro);
        });
});
/*=============================================
REALIZAR OPERACIONES DE ABONO CINCO 
=============================================*/
$(document).on("change", function() {


        $(".abono5").each(function() {
            var $adeudoDelPedidoCinco = $(".adeudoPedidoEditado").val();
            var $QuintoAbono = $(".abono5").val();

            var $totalNuevoAdeudoCinco =  $adeudoDelPedidoCinco - $QuintoAbono;

            $(".adeudoPedidoEditado").val($totalNuevoAdeudoCinco);
        });
});

/*=============================================
ELIMINAR PEDIDO
=============================================*/

$('.tablaPedidos tbody').on("click", ".btnEliminarPedido", function(){

  var idpedido = $(this).attr("idpedido");


  swal({
    title: '¿Está seguro de borrar el pedido?',
    text: "¡Si no lo está puede cancelar la accíón!",
    type: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      cancelButtonText: 'Cancelar',
      confirmButtonText: 'Si, borrar Pedido!'
  }).then(function(result){

    if(result.value){

      window.location = "index.php?ruta=pedidos&idpedido="+idpedido+"";

    }

  })

})

/*=============================================
SELECTOR DE CLIENTE
=============================================*/
$(document).ready(function(){
      
  $('.clientePeido').select2();
        
});

/*=============================================
VER INFORMACION DEL PEDIDO
=============================================*/
$(".tablaPedidos").on("click", ".btnVerInfoPedido", function(){
    //console.log("Editar");
  var idPedido = $(this).attr("idPedido");
 //console.log(idPedido);
  var empresa = $(this).attr("empresa");
  var asesor = $(this).attr("asesor");
  var cliente = $(this).attr("cliente");
  var datos = new FormData();
  datos.append("idPedido", idPedido);
  datos.append("empresa", empresa);
  datos.append("asesor", asesor);
  datos.append("cliente", cliente);
  
  $.ajax({


    url:"ajax/pedidos.ajax.php",
    method: "POST",
    data: datos,
    cache: false,
    contentType: false,
    processData: false,
    dataType: "json",
    success: function(respuesta){ 

     
    
        //console.log("Datos Orden:", respuesta);

    }

  })
  window.open("index.php?ruta=infopedido&idPedido="+idPedido+"&empresa="+empresa+"&asesor="+asesor+"&cliente="+cliente+"","_self");


})



/*=============================================
TOGGLE NEW PAYMENT FORM
=============================================*/
$('.btnToggleNewPayment').click(function() {
  var $form = $('#pedNewPaymentForm');
  if ($form.hasClass('active')) {
    $form.removeClass('active');
  } else {
    $form.addClass('active');
    $form.find('input[type=number]').focus();
  }
});

/*=============================================
AGREGAR CAMPOS PAGO PEDIDO DINAMICO (legacy class kept)
=============================================*/
$(document).on('change', '#pedNewPaymentForm input', function() {
  var $form = $('#pedNewPaymentForm');
  var monto = $form.find('input.pagoAbonado').val();
  var fecha = $form.find('input.fechaAbono').val();

  if (monto && fecha && parseFloat(monto) > 0) {
    // Move to confirmed payments list
    var numAbono = $('.agregarCamposPago .ped-payment-item').length + 1;
    $(".agregarCamposPago").append(
      '<div class="ped-payment-item" style="animation:pedSlideIn .25s ease;">'+
        '<div class="ped-payment-icon" style="background:#f0fdf4;color:#22c55e;"><i class="fa-solid fa-check"></i></div>'+
        '<div style="flex:1;">'+
          '<div class="ped-info-label">Abono #'+numAbono+'</div>'+
          '<input type="number" class="form-control pagoAbonado" value="'+monto+'" readonly style="border:none;background:transparent;font-size:14px;font-weight:700;padding:0;height:auto;color:var(--crm-text);box-shadow:none;">'+
        '</div>'+
        '<div class="ped-payment-date">'+
          '<input type="date" class="form-control fechaAbono" value="'+fecha+'" readonly style="border:none;background:transparent;font-size:12px;color:var(--crm-muted);box-shadow:none;text-align:right;">'+
        '</div>'+
      '</div>'
    );

    // Reset form
    $form.find('input.pagoAbonado').val('');
    $form.find('input.fechaAbono').val('');
    $form.removeClass('active');

    // Recalculate
    listarPrimerPago();
    listarObservacionesPedidos();
    listarNuevosPreciosDePedido();
    listarProductosPedidoDinamico();

    // Recalc totals
    var sumaDos = 0;
    $(".pagoAbonado").each(function(){ sumaDos += +$(this).val(); });
    $(".totalPagosPeiddoDinamico").val(sumaDos);
    var totalPagar = $(".totalPagarPedidoDinamico").val();
    $(".adeudoPedidoDinamico").val(parseFloat(totalPagar) - parseFloat(sumaDos));
  }
});


$(document).on("change", "input.pagoAbonado", function(){

  listarProductosPedidoDinamico()
  listarNuevosPreciosDePedido()
})
$(document).on("change", "input.fechaAbono", function(){

  listarProductosPedidoDinamico()
  listarNuevosPreciosDePedido()
})

$(document).ready(function(){

  listarProductosPedidoDinamico()
  listarPrimerPago()
  listarObservacionesPedidos()
  listarNuevosPreciosDePedido()

});  
/*=============================================
LISTAR PRODUCTOS DEL PEDIDO
=============================================*/
function listarProductosPedidoDinamico(){

  var listarProductosPedidodianmico = [];

  var pago = $(".pagoAbonado");
  var fecha = $(".fechaAbono");

  for (var i =0; i < pago.length; i++) {

    listarProductosPedidodianmico.push({"pago" : $(pago[i]).val(),
                                        "fecha" : $(fecha[i]).val()})

  }

  $(".PagosListados").val(JSON.stringify(listarProductosPedidodianmico));
} 

/*=============================================
SUMAR TOTAL DE LOS PRECIOS
=============================================*/
$(document).ready(function(){
       var sum = 0;
       $(".pagoAbonado").each(function(){
           sum += +$(this).val();
       });
       $(".totalPagosPeiddoDinamico").val(sum);

       //console.log("suma de los abonos: ", sum);
});
/*=============================================
RESTAR TOTAL DE LOS PRECIOS
=============================================*/
$(document).ready(function(){
      
      var totalPagosPeiddoDinamico = $(".totalPagosPeiddoDinamico").val();

      //console.log(totalPagosPeiddoDinamico);

      var totalPagarPedidoDinamico = $(".totalPagarPedidoDinamico").val();

       //console.log(totalPagarPedidoDinamico);

      var operacion = parseFloat(totalPagarPedidoDinamico) - parseFloat(totalPagosPeiddoDinamico);

      $(".adeudoPedidoDinamico").val(operacion);

       //console.log("adeudo: ", operacion);
});
/*=============================================
RESTAR TOTAL DE LOS PRECIOS
=============================================*/
$(document).on("change", "input.pagoAbonado", function(){

    var sumaDos = 0;
      $(".pagoAbonado").each(function(){
        
        sumaDos += +$(this).val();
      
      });

       //console.log("sma de nuevo abono",sumaDos);

    var totalPagarPedidoDinamico = $(".totalPagarPedidoDinamico").val();

    var restarNuevoPago = parseFloat(totalPagarPedidoDinamico) - parseFloat(sumaDos);

    //console.log("total Nuevo Adeudo", restarNuevoPago);

    $(".adeudoPedidoDinamico").val(restarNuevoPago);
});
/*=============================================
RESTAR CAMBIO A REGRESAR NE VENTANA MODAL
=============================================*/
$(document).on("change", "input.PagoClientePedidoDinamico", function(){

var pagoDelCliente = $(".PagoClientePedidoDinamico").val();

var TotalPedidoEnModal = $(".TotalPedidoEnOrden").val();

var calcularCambio =  parseFloat(TotalPedidoEnModal) - parseFloat(pagoDelCliente);

  $(".cambioClientePedidoDinamico").val(calcularCambio);

  listarPrimerPago()
  listarObservacionesPedidos()
  listarNuevosPreciosDePedido()


});

$(document).on("change", "input.fechaPagoVentaModal", function(){

  listarPrimerPago()
  listarObservacionesPedidos()
  listarNuevosPreciosDePedido()
  

});
/*=============================================
AUTO-RESIZE READONLY OBSERVATION TEXTAREAS
=============================================*/
$(document).ready(function(){
  $('.ped-obs-content textarea.nuevaObservacion[readonly]').each(function(){
    this.style.height = 'auto';
    this.style.height = this.scrollHeight + 'px';
  });
});
/*=============================================
LISTAR PRIMER PAGO
=============================================*/
function listarPrimerPago(){

  var listarPrimerPagoPedido = [];

  var pago = $(".PagoClientePedidoDinamico");
  var fecha = $(".fechaPagoVentaModal");

  for (var i =0; i < pago.length; i++) {

    listarPrimerPagoPedido.push({"pago" : $(pago[i]).val(),
                                  "fecha" : $(fecha[i]).val()})

  }

  $(".PrimerPagolistado").val(JSON.stringify(listarPrimerPagoPedido));

  listarObservacionesPedidos()
  listarNuevosPreciosDePedido()

} 

$(document).on("change", "input.PagoClientePedidoDinamico", function(){

  var pagoParaDeudo = $(".PagoClientePedidoDinamico").val();

  var TotalPedidoEnOrden = $(".TotalPedidoEnOrden").val();


  var operacionPrimerAdeudo = parseFloat(TotalPedidoEnOrden) - parseFloat(pagoParaDeudo);

   $(".PrimerAdeudo").val(operacionPrimerAdeudo);

   //console.log("primer adeudo: " , operacionPrimerAdeudo);

   listarObservacionesPedidos()
   listarNuevosPreciosDePedido()
   

});

  var today = new Date();

  var dd = today.getDate();

  var mm = today.getMonth() + 1; //January is 0!

  var yyyy = today.getFullYear();



  var fecha = mm + '/' + dd + '/' + yyyy;



var valor_sesion = $('.usuarioActualPedido').val();



$("#fechaVista").attr("fecha", fecha);


$('.AgregarCampoDeObservacionPedidos').click(function() {

  var obsText = $('#pedNewObsText').val();
  if (!obsText || !obsText.trim()) {
    $('#pedNewObsText').focus();
    return;
  }

  // Build initials from session name
  var parts = valor_sesion.trim().split(/\s+/);
  var initials = parts.length >= 2 ? (parts[0][0] + parts[1][0]).toUpperCase() : valor_sesion.substring(0,2).toUpperCase();

  // Get gradient based on name
  var obsGrads = [
    'linear-gradient(135deg,#6366f1,#818cf8)',
    'linear-gradient(135deg,#3b82f6,#60a5fa)',
    'linear-gradient(135deg,#06b6d4,#22d3ee)',
    'linear-gradient(135deg,#22c55e,#4ade80)',
    'linear-gradient(135deg,#f59e0b,#fbbf24)',
    'linear-gradient(135deg,#ef4444,#f87171)',
    'linear-gradient(135deg,#8b5cf6,#a78bfa)',
    'linear-gradient(135deg,#ec4899,#f472b6)'
  ];
  var hash = 0;
  for (var c = 0; c < valor_sesion.length; c++) hash = ((hash << 5) - hash) + valor_sesion.charCodeAt(c);
  var grad = obsGrads[Math.abs(hash) % obsGrads.length];

  // Insert visual item at top of the list
  var obsKey = 'obs-' + Date.now() + '-' + Math.floor(Math.random() * 100000);
  var newItem =
    '<div class="ped-obs-item" style="animation:pedSlideIn .25s var(--crm-ease);">'+
      '<div class="ped-obs-avatar" style="background:'+grad+';">'+initials+'</div>'+
      '<div class="ped-obs-body">'+
        '<div class="ped-obs-header">'+
          '<span class="ped-obs-name">'+$('<span>').text(valor_sesion).html()+'</span>'+
          '<span class="ped-obs-date">'+fecha+' <span style="color:#22c55e;font-weight:600;">• Nueva</span></span>'+
        '</div>'+
        '<div class="ped-obs-content">'+$('<span>').text(obsText).html()+'</div>'+
      '</div>'+
      '<button type="button" class="ped-btn-danger quitarObservacion" data-obs-key="'+obsKey+'" style="align-self:flex-start;margin-top:2px;"><i class="fa-solid fa-times"></i></button>'+
    '</div>';

  // Ensure the list exists
  if ($('.ped-obs-list').length === 0) {
    // Remove empty state and create list
    $('#pedObsCompose').after('<div class="ped-obs-list"></div>');
    // Remove "no hay observaciones" placeholder
    $('.ped-card-body').find('[style*="text-align:center"]').filter(function() { return $(this).find('.fa-message').length; }).remove();
  }
  $('.ped-obs-list').prepend(newItem);

  // Add hidden fields for serialization
  $(".cajaObervacionesPedidos").show();
  $(".agregarcampoobervacionesPedidos").prepend(
    '<div class="pedHiddenObs" data-obs-key="'+obsKey+'">'+
      '<textarea class="nuevaObservacion" data-creador="'+$('<span>').text(valor_sesion).html()+'" fecha="'+fecha+'" style="display:none;">'+$('<span>').text(obsText).html()+'</textarea>'+
    '</div>'
  );

  // Clear compose area
  $('#pedNewObsText').val('');

  // Update count
  var totalObs = $('.ped-obs-item').length;
  $('.ped-card-head span').filter(function(){ return $(this).text().match(/comentario/); }).text(totalObs + ' comentario' + (totalObs !== 1 ? 's' : ''));

  listarObservacionesPedidos();
  listarNuevosPreciosDePedido();
});

/* Remove a newly-added observation */
$(document).on("click", ".quitarObservacion", function() {
  var $item = $(this).closest('.ped-obs-item');
  var obsKey = $(this).attr('data-obs-key');
  $('.agregarcampoobervacionesPedidos .pedHiddenObs[data-obs-key="'+obsKey+'"]').remove();
  $item.remove();
  // Update count
  var totalObs = $('.ped-obs-item').length;
  $('.ped-card-head span').filter(function(){ return $(this).text().match(/comentario/); })
    .text(totalObs + ' comentario' + (totalObs !== 1 ? 's' : ''));
  listarObservacionesPedidos();
  listarNuevosPreciosDePedido();
});

$(document).on("change", ".nuevaObservacion", function() {

    listarObservacionesPedidos()
    listarNuevosPreciosDePedido()
    

});
$(document).on("change", ".descripcioParaListar", function() {

    listarObservacionesPedidos()
    listarNuevosPreciosDePedido()
    

});
$(document).on("change", ".cantidadProductoParaListar", function() {

    listarObservacionesPedidos()
    listarNuevosPreciosDePedido()
    

});
$(document).on("change", ".precioProductoParaListar", function() {

    listarObservacionesPedidos()
    listarNuevosPreciosDePedido()
    

});
/*=============================================
SUMAR TOTAL DE LOS PRECIOS DEL PEDIDO
=============================================*/

$(document).on("change", ".precioProductoParaListar", function() {
       var sum = 0;
       $(".precioProductoParaListar").each(function(){
           sum += +$(this).val();
       });
       $(".totalPagarPedidoDinamico").val(sum);
});
$(document).on("change", ".cantidadProductoParaListar", function() {
       var mult = $(".totalPagarPedidoDinamico").val();
       $(".cantidadProductoParaListar").each(function(){
           mult *= +$(this).val();
       });
       $(".totalPagarPedidoDinamico").val(mult);
});
/*=============================================
LISTAR OBERVACIONES
=============================================*/
function listarObservacionesPedidos(){

  var listarnuvasObservacionesPedidos = [];

  var descripcion = $(".nuevaObservacion");

  for (var i =0; i < descripcion.length; i++){

    listarnuvasObservacionesPedidos.push({"observacion" : $(descripcion[i]).val(), 

                     "creador" : $(descripcion[i]).attr("data-creador"),

                     "fecha" : $(descripcion[i]).attr("fecha")})

  }

  $("#listarObservacionesPedidos").val(JSON.stringify(listarnuvasObservacionesPedidos));

}

/*=============================================
LISTAR OBERVACIONES
=============================================*/
function listarNuevosPreciosDePedido(){

  var listarNuevosPrecios = [];

  var Descripcion = $(".descripcioParaListar");

  var cantidad = $(".cantidadProductoParaListar");

  var precio = $(".precioProductoParaListar");

  for (var i =0; i < Descripcion.length; i++){

    listarNuevosPrecios.push({"Descripcion" : $(Descripcion[i]).val(), 

                     "cantidad" : $(cantidad[i]).val(),

                     "precio" : $(precio[i]).val()})

  }

  $("#ListarPreciosActualizados").val(JSON.stringify(listarNuevosPrecios));

}

/*=============================================
SERIALIZAR ANTES DE ENVIAR EL FORMULARIO
=============================================*/
$('form[method="post"]').on('submit', function() {
  listarProductosPedidoDinamico();
  listarPrimerPago();
  listarObservacionesPedidos();
  listarNuevosPreciosDePedido();
});

</script>
