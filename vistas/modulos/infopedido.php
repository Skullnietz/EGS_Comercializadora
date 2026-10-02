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
  if (!$pedidos) {
    echo '<div class="content-wrapper"><div class="alert alert-warning">El pedido ya no existe.</div></div>';
    return;
  }
  foreach ($pedidos as $key => $valuePedidos) {}
  try { PedidosPersistencia::autorizar($valuePedidos); }
  catch (RuntimeException $e) {
    echo '<div class="content-wrapper"><div class="alert alert-warning">'.htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8').'</div></div>';
    return;
  }

  $item = "id";
  $valor = $valuePedidos['id_cliente'];
  $usuario = ControladorClientes::ctrMostrarClientesOrdenes($item, $valor);

  $item = "id";
  $valor = $valuePedidos['id_Asesor'];
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

  // Lo guardado se lee con tolerancia: los datos de versiones anteriores no deben impedir capturar cambios.
  $avisosPedido = [];
  try { $productosPedido = PedidosPersistencia::guardada($valuePedidos["productos"], 'productos'); } catch (RuntimeException $e) { $productosPedido = []; $avisosPedido[] = $e->getMessage(); }
  try { $pagosPedido = PedidosPersistencia::guardada($valuePedidos["pagos"], 'pagos'); } catch (RuntimeException $e) { $pagosPedido = []; $avisosPedido[] = $e->getMessage(); }
  $observaciones = PedidosPersistencia::observacionesGuardadas($valuePedidos["observaciones"]);
  $totalPedido = round(PedidosPersistencia::importe($valuePedidos["total"]), 2);
  $pagadoPedido = PedidosPersistencia::pagado($valuePedidos, $pagosPedido);
  function pedTexto($valor) {
    return is_scalar($valor) ? htmlspecialchars((string)$valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') : '';
  }
  $historialClienteLink = 'index.php?ruta=Historialdecliente&idCliente='.(int)$valuePedidos['id_cliente']
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

    <form role="form" method="post" id="pedidoDetalleForm" onsubmit="return false;" novalidate data-usuario="<?php echo pedTexto((string)($_SESSION['id'] ?? $_SESSION['nombre'] ?? '') . ':' . (string)($_SESSION['empresa'] ?? '')); ?>" data-pagado="<?php echo $pagadoPedido; ?>"<?php echo $puedeEditarPedidoCompleto ? ' data-finanzas="1"' : ''; ?>>
      <input type="hidden" name="idPedido" value="<?php echo (int)$valuePedidos["id"]; ?>">
      <input type="hidden" name="versionPedido" value="<?php echo PedidosPersistencia::version($valuePedidos); ?>">

    <?php foreach ($avisosPedido as $aviso): ?>
      <div class="alert alert-warning" style="border-radius:10px;"><?php echo pedTexto($aviso); ?></div>
    <?php endforeach; ?>

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
                    <select class="ped-select" name="estado" style="margin-top:6px;">
                      <option value="<?php echo pedTexto($valuePedidos["estado"]); ?>"><?php echo pedTexto($valuePedidos["estado"]); ?></option>
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
                  // Productos del formato antiguo (productoUno…ProductoCinco): solo lectura.
                  $productosAnteriores = [
                    ['productoUno', 'cantidaProductoUno', 'precioProductoUno'],
                    ['ProductoDos', 'cantidadProductoDos', 'precioProductoDos'],
                    ['ProductoTres', 'cantidadProductoTres', 'precioProductoTres'],
                    ['ProductoCuatro', 'cantidadProductoCuatro', 'precioProductoCuatro'],
                    ['ProductoCinco', 'cantidadProductoCinco', 'precioProductoCinco'],
                  ];
                  foreach ($productosAnteriores as list($campoProducto, $campoCantidad, $campoPrecio)) {
                    $nombreProducto = isset($valuePedidos[$campoProducto]) ? $valuePedidos[$campoProducto] : null;
                    if ($nombreProducto === null || $nombreProducto === '' || $nombreProducto === 'undefined') continue;
                    $precioAnterior = PedidosPersistencia::importe($valuePedidos[$campoPrecio]);
                    $sub = PedidosPersistencia::importe($valuePedidos[$campoCantidad]) * $precioAnterior;
                    echo '<tr>
                      <td>'.pedTexto($nombreProducto).'</td>
                      <td>'.pedTexto($valuePedidos[$campoCantidad]).'</td>
                      <td>$'.number_format($precioAnterior, 2).'</td>
                      <td>$'.number_format($sub, 2).'</td>
                    </tr>';
                  }

                  // Productos en JSON: "precio" guarda el subtotal de la línea (así lo imprime el ticket); aquí se edita el unitario.
                  // Solo se envían las filas modificadas, identificadas por su posición.
                  $ro = $puedeEditarPedidoCompleto ? '' : ' readonly';
                  foreach ($productosPedido as $indice => $producto) {
                    if (!is_array($producto)) continue;
                    $cantidad = PedidosPersistencia::importe(isset($producto["cantidad"]) ? $producto["cantidad"] : 0);
                    $subtotal = round(PedidosPersistencia::importe(isset($producto["precio"]) ? $producto["precio"] : 0), 2);
                    $unitario = isset($producto["precioUnitario"]) ? PedidosPersistencia::importe($producto["precioUnitario"]) : $subtotal / ($cantidad > 0 ? $cantidad : 1);
                    echo '<tr class="ped-dynamic-product" data-indice="'.(int)$indice.'" data-subtotal="'.$subtotal.'">
                      <td class="ped-input-cell"><input type="text" value="'.pedTexto(isset($producto["Descripcion"]) ? $producto["Descripcion"] : '').'" class="ped-prod-descripcion"'.$ro.'></td>
                      <td class="ped-input-cell"><input type="number" min="0" step="any" value="'.($cantidad > 0 ? $cantidad : '').'" class="ped-prod-cantidad"'.$ro.'></td>
                      <td class="ped-input-cell"><input type="number" min="0" step="any" value="'.round($unitario, 6).'" class="ped-prod-precio"'.$ro.'></td>
                      <td class="ped-line-subtotal">$'.number_format($subtotal, 2).'</td>
                    </tr>';
                  }
                ?>

                <!-- Total row -->
                <tr class="ped-total-row">
                  <td colspan="3" style="text-align:right; padding-right:20px;">
                    <strong>Total del Pedido</strong>
                  </td>
                  <td class="ped-input-cell">
                    <div style="display:flex;align-items:center;gap:6px;">
                      <span style="font-weight:800;color:var(--crm-accent);font-size:16px;">$</span>
                      <input type="number" step="any" min="0" class="form-control ped-total" name="total" value="<?php echo $totalPedido; ?>"<?php echo $puedeEditarPedidoCompleto ? ' title="Se ajusta al editar productos; también puedes corregirlo"' : ' readonly'; ?>
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
              // Pago inicial y abonos del formato antiguo: se muestran y cuentan, pero no se reescriben.
              foreach (PedidosPersistencia::pagosAnteriores($valuePedidos) as $pagoAnterior):
                $esPagoInicial = $pagoAnterior['campo'] === 'pagoPedido';
            ?>
              <div class="ped-payment-item">
                <div class="ped-payment-icon"><i class="fa-solid fa-coins"></i></div>
                <div style="flex:1;">
                  <div class="ped-info-label"><?php echo $esPagoInicial ? 'Pago Inicial' : 'Abono anterior'; ?></div>
                  <div class="ped-payment-amount">$<?php echo number_format($pagoAnterior['pago'], 2); ?></div>
                </div>
                <div class="ped-payment-date" style="font-size:11px; color:var(--crm-muted);"><?php echo $esPagoInicial ? 'Primer pago' : pedTexto($pagoAnterior['fecha']); ?></div>
              </div>
            <?php endforeach; ?>

            <!-- Abonos guardados (solo lectura) y abonos nuevos sin guardar -->
            <div class="ped-payments">
            <?php
              $abonoNum = 0;
              foreach ($pagosPedido as $pago):
                if (!is_array($pago)) continue;
                $abonoNum++;
            ?>
              <div class="ped-payment-item">
                <div class="ped-payment-icon"><i class="fa-solid fa-money-bill-wave"></i></div>
                <div style="flex:1;">
                  <div class="ped-info-label">Abono #<?php echo $abonoNum; ?></div>
                  <div class="ped-payment-amount">$<?php echo number_format(PedidosPersistencia::importe(isset($pago["pago"]) ? $pago["pago"] : 0), 2); ?></div>
                </div>
                <div class="ped-payment-date"><?php echo pedTexto(isset($pago["fecha"]) ? $pago["fecha"] : ''); ?></div>
              </div>
            <?php endforeach; ?>
            </div>

            <!-- New payment inline form -->
            <?php if ($puedeEditarPedidoCompleto): ?>
            <div class="ped-new-payment" id="pedNewPaymentForm">
              <div class="ped-new-payment-title">
                <i class="fa-solid fa-plus-circle"></i> Registrar Nuevo Abono
              </div>
              <div class="ped-new-payment-row">
                <div class="ped-new-payment-field">
                  <label for="pedNuevoAbonoMonto">Monto del abono</label>
                  <input type="number" id="pedNuevoAbonoMonto" placeholder="0.00" min="0" step="any">
                </div>
                <div class="ped-new-payment-field">
                  <label for="pedNuevoAbonoFecha">Fecha del pago</label>
                  <input type="date" id="pedNuevoAbonoFecha" value="<?php echo date('Y-m-d'); ?>">
                </div>
                <button type="button" class="ped-btn ped-btn-primary btnAgregarAbono" style="padding:8px 16px;">
                  <i class="fa-solid fa-plus"></i> Agregar
                </button>
              </div>
              <p style="margin:8px 0 0; font-size:11px; color:var(--crm-muted);">Los abonos agregados se guardan al presionar «Guardar Pedido».</p>
            </div>
            <?php endif; ?>

            <!-- Summary boxes -->
            <div class="ped-summary-box">
              <div class="ped-summary-item total">
                <div class="ped-summary-label">Total Pagado</div>
                <div class="ped-summary-value ped-pagado">$<?php echo number_format($pagadoPedido, 2); ?></div>
              </div>
              <div class="ped-summary-item debt">
                <div class="ped-summary-label">Adeudo</div>
                <div class="ped-summary-value ped-adeudo">$<?php echo number_format(max(0, $totalPedido - $pagadoPedido), 2); ?></div>
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
            <span class="ped-obs-count" style="font-size:12px; color:var(--crm-muted); font-weight:500;">
              <?php echo count($observaciones) . ' comentario' . (count($observaciones) != 1 ? 's' : ''); ?>
            </span>
          </div>
          <div class="ped-card-body">

            <!-- Compose new observation (inline, always visible for authorized users) -->
            <?php if ($puedeAgregarObservaciones): ?>
              <?php
                $sesIni = pedGetInitials($_SESSION["nombre"]);
                $sesGrad = pedGetGrad($_SESSION["nombre"], $_obsGrads);
              ?>
              <div class="ped-obs-compose" id="pedObsCompose">
                <div class="ped-obs-avatar" style="background:<?php echo $sesGrad; ?>;"><?php echo pedTexto($sesIni); ?></div>
                <div class="ped-obs-compose-body">
                  <textarea id="pedNewObsText" class="form-control" placeholder="Escribe una observación..." rows="2" maxlength="5000"></textarea>
                  <div class="ped-obs-compose-actions">
                    <button type="button" class="ped-btn ped-btn-primary btnAgregarObservacionInfoPedido" style="padding:7px 16px; font-size:12px;">
                      <i class="fa-solid fa-paper-plane"></i> Agregar
                    </button>
                  </div>
                </div>
              </div>
            <?php endif; ?>

            <p class="ped-observation-status" role="status" aria-live="polite" style="margin:0 0 8px; font-size:12px; color:var(--crm-muted);"></p>

            <!-- Existing observations -->
            <div class="ped-obs-list">
              <?php foreach ($observaciones as $obs):
                $obsName = isset($obs["creador"]) && is_scalar($obs["creador"]) && $obs["creador"] !== '' ? (string)$obs["creador"] : 'Usuario';
              ?>
                <div class="ped-obs-item">
                  <div class="ped-obs-avatar" style="background:<?php echo pedGetGrad($obsName, $_obsGrads); ?>;"><?php echo pedTexto(pedGetInitials($obsName)); ?></div>
                  <div class="ped-obs-body">
                    <div class="ped-obs-header">
                      <span class="ped-obs-name"><?php echo pedTexto($obsName); ?></span>
                      <span class="ped-obs-date"><?php echo pedTexto(isset($obs["fecha"]) ? $obs["fecha"] : ''); ?></span>
                    </div>
                    <div class="ped-obs-content"><?php echo pedTexto(isset($obs["observacion"]) ? $obs["observacion"] : ''); ?></div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
            <?php if (!$observaciones): ?>
              <div id="pedObsEmpty" style="text-align:center; padding:24px 0; color:var(--crm-muted);">
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

<script src="vistas/js/pedidos.guardado.js?v=<?php echo filemtime(__DIR__ . '/../js/pedidos.guardado.js'); ?>"></script>
<script src="vistas/js/pedidos.detalle.js?v=<?php echo filemtime(__DIR__ . '/../js/pedidos.detalle.js'); ?>"></script>
