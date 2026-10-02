<?php

if($_SESSION["perfil"] != "administrador" AND $_SESSION["perfil"]!= "vendedor" AND $_SESSION["perfil"]!= "Super-Administrador"){

  echo '<script>

  window.location = "inicio";

  </script>';

  return;

}

$mostrarMercadoLibre = ($_SESSION["perfil"] == "administrador" || $_SESSION["perfil"] == "Super-Administrador");

?>

<style>
  :root {
    --crm-bg: #f8fafc;
    --crm-surface: #ffffff;
    --crm-border: #e2e8f0;
    --crm-text: #0f172a;
    --crm-text2: #475569;
    --crm-muted: #94a3b8;
    --crm-accent: #6366f1;
    --crm-radius: 14px;
    --crm-radius-sm: 10px;
    --crm-shadow: 0 1px 3px rgba(15, 23, 42, .06), 0 4px 14px rgba(15, 23, 42, .04);
    --crm-shadow-lg: 0 4px 24px rgba(15, 23, 42, .10);
    --crm-ease: cubic-bezier(.4, 0, .2, 1);
  }

  .content {
    background: var(--crm-bg);
    padding: 14px 15px 20px;
  }

  .content-header .breadcrumb {
    margin-bottom: 0;
  }

  .content-header h1 {
    margin: 0;
    color: var(--crm-text);
    font-weight: 800;
    font-size: 24px;
    letter-spacing: -.01em;
  }

  .content-header h1 small {
    color: var(--crm-muted);
    font-size: 13px;
    font-weight: 500;
  }

  .ped-section {
    display: flex;
    align-items: center;
    gap: 14px;
    margin: 6px 0 16px;
    padding: 0 4px;
  }

  .ped-section-icon {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    color: #fff;
    background: linear-gradient(135deg, #6366f1, #818cf8);
    flex-shrink: 0;
  }

  .ped-section h3 {
    margin: 0;
    font-size: 15px;
    font-weight: 800;
    color: var(--crm-text);
  }

  .ped-section p {
    margin: 2px 0 0;
    font-size: 12px;
    color: var(--crm-muted);
  }

  .ped-card {
    background: var(--crm-surface);
    border: 1px solid var(--crm-border);
    border-radius: var(--crm-radius);
    box-shadow: var(--crm-shadow);
    overflow: hidden;
    transition: box-shadow .2s var(--crm-ease), transform .2s var(--crm-ease);
  }

  .ped-card:hover {
    box-shadow: var(--crm-shadow-lg);
  }

  .ped-card-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding: 16px 20px 14px;
    border-bottom: 1px solid #f1f5f9;
    flex-wrap: wrap;
  }

  .ped-card-title {
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
    color: var(--crm-text);
    font-size: 14px;
    font-weight: 700;
    line-height: 1.3;
  }

  .ped-card-title i {
    color: var(--crm-accent);
  }

  .ped-card-body {
    padding: 18px 20px 20px;
  }

  .ped-actions-group {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 16px;
  }

  .ped-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    border-radius: 8px;
    border: 1px solid transparent;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    transition: all .15s ease;
    text-decoration: none;
    white-space: nowrap;
  }

  .ped-btn-primary {
    background: #6366f1;
    color: #fff;
    border-color: #4f46e5;
  }

  .ped-btn-primary:hover {
    background: #4f46e5;
    border-color: #4338ca;
  }

  .ped-btn-success {
    background: #16a34a;
    color: #fff;
    border-color: #15803d;
  }

  .ped-btn-success:hover {
    background: #15803d;
    border-color: #166534;
  }

  .table-responsive-wrap {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }

  #tablepedidos thead th {
    position: sticky;
    top: 0;
    background: #f8fafc;
    z-index: 2;
    box-shadow: 0 1px 0 rgba(15, 23, 42, .08);
    white-space: nowrap;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .4px;
    color: var(--crm-text2);
    border-bottom-color: #e8eef5;
    padding: 12px 10px;
  }

  #tablepedidos.dataTable thead .sorting,
  #tablepedidos.dataTable thead .sorting_asc,
  #tablepedidos.dataTable thead .sorting_desc,
  #tablepedidos.dataTable thead .sorting_asc_disabled,
  #tablepedidos.dataTable thead .sorting_desc_disabled,
  #tablepedidos.dataTable thead .sorting_disabled {
    background-image: none !important;
    padding-right: 8px !important;
  }

  #tablepedidos.dataTable thead .sorting::before,
  #tablepedidos.dataTable thead .sorting::after,
  #tablepedidos.dataTable thead .sorting_asc::before,
  #tablepedidos.dataTable thead .sorting_asc::after,
  #tablepedidos.dataTable thead .sorting_desc::before,
  #tablepedidos.dataTable thead .sorting_desc::after,
  #tablepedidos.dataTable thead .sorting_asc_disabled::before,
  #tablepedidos.dataTable thead .sorting_asc_disabled::after,
  #tablepedidos.dataTable thead .sorting_desc_disabled::before,
  #tablepedidos.dataTable thead .sorting_desc_disabled::after,
  #tablepedidos.dataTable thead .sorting_disabled::before,
  #tablepedidos.dataTable thead .sorting_disabled::after {
    display: none !important;
    content: none !important;
  }

  #tablepedidos.dataTable tbody tr td {
    vertical-align: middle;
    padding: 12px 10px;
  }

  .badge {
    display: inline-block;
    padding: .35em .6em;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 600;
    border: 1px solid transparent;
    white-space: nowrap;
    letter-spacing: .2px;
  }

  /* ── Estados estandarizados ── */
  .badge-pedido-pendiente { color: #92400e; background: #fffbeb; border-color: #fde68a; }
  .badge-adquirido { color: #0e7490; background: #ecfeff; border-color: #a5f3fc; }
  .badge-almacen { color: #c2410c; background: #fff7ed; border-color: #fed7aa; }
  .badge-asesor { color: #065f46; background: #ecfdf5; border-color: #a7f3d0; }
  .badge-pagado { color: #166534; background: #f0fdf4; border-color: #bbf7d0; }
  .badge-credito { color: #166534; background: #f0fdf4; border-color: #bbf7d0; }

  .td-actions {
    white-space: nowrap;
    width: 1%;
  }

  table.dataTable.stripe tbody tr.odd,
  table.dataTable.display tbody tr.odd {
    background-color: #fbfdff;
  }

  table.dataTable tbody tr:hover {
    background-color: #f4f7ff !important;
  }

  #tablepedidos_wrapper .dataTables_length,
  #tablepedidos_wrapper .dataTables_filter {
    margin-bottom: 12px;
  }

  #tablepedidos_wrapper .dataTables_length label,
  #tablepedidos_wrapper .dataTables_filter label {
    color: #475569;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .2px;
  }

  #tablepedidos_wrapper .dataTables_length select {
    border: 1px solid #dbe3ef !important;
    border-radius: 8px;
    background: #ffffff;
    color: #334155;
    height: 34px;
    padding: 4px 26px 4px 10px;
    margin: 0 6px;
    font-size: 12px;
    font-weight: 600;
  }

  #tablepedidos_wrapper .dataTables_filter input {
    border: 1px solid #dbe3ef !important;
    border-radius: 10px;
    background: #ffffff;
    color: #334155;
    height: 36px;
    min-width: 220px;
    padding: 6px 12px;
    font-size: 12px;
    font-weight: 600;
    transition: all .15s ease;
  }

  #tablepedidos_wrapper .dataTables_length select:focus,
  #tablepedidos_wrapper .dataTables_filter input:focus {
    outline: none;
    border-color: #a5b4fc !important;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, .12);
  }

  #tablepedidos_wrapper .dataTables_paginate ul.pagination {
    display: flex; flex-wrap: wrap; justify-content: flex-end; align-items: center; gap: 4px;
  }

  #tablepedidos_wrapper .dataTables_paginate ul.pagination > li.paginate_button {
    padding: 0 !important; margin: 0 !important;
    background: transparent !important; border: 0 !important; box-shadow: none !important;
  }

  #tablepedidos_wrapper .dataTables_paginate ul.pagination > li.paginate_button > a {
    border-radius: 8px !important;
    border: 1px solid #dbe3ef !important;
    background: #ffffff !important;
    color: #334155 !important;
    margin-left: 6px;
    padding: 6px 12px !important;
    font-weight: 600;
    transition: all .15s ease;
  }

  #tablepedidos_wrapper .dataTables_paginate ul.pagination > li.paginate_button > a:hover {
    background: #eef2ff !important;
    border-color: #a5b4fc !important;
    color: #3730a3 !important;
  }

  #tablepedidos_wrapper .dataTables_paginate ul.pagination > li.paginate_button.active > a {
    background: #1a3152 !important;
    border-color: #1a3152 !important;
    color: #ffffff !important;
  }

  #tablepedidos_wrapper .dataTables_paginate ul.pagination > li.paginate_button.disabled > a {
    background: #f8fafc !important;
    border-color: #e2e8f0 !important;
    color: #94a3b8 !important;
    cursor: not-allowed;
  }

  #table-ml-pedidos_wrapper .dataTables_paginate ul.pagination {
    display: flex; flex-wrap: wrap; justify-content: flex-end; align-items: center; gap: 4px;
  }

  #table-ml-pedidos_wrapper .dataTables_paginate ul.pagination > li.paginate_button {
    padding: 0 !important; margin: 0 !important;
    background: transparent !important; border: 0 !important; box-shadow: none !important;
  }

  @media (max-width: 767px) {
    .ped-card-head {
      flex-direction: column;
      align-items: flex-start;
    }

    .ped-card-body {
      padding: 12px;
    }

    .content {
      padding: 10px;
    }

    #tablepedidos_wrapper .dataTables_length,
    #tablepedidos_wrapper .dataTables_filter {
      float: none !important;
      text-align: left !important;
      width: 100%;
    }

    #tablepedidos_wrapper .dataTables_filter input {
      width: 100%;
      min-width: 0;
    }

    .ped-actions-group {
      width: 100%;
    }
  }

  /* ── Estilos para modales mejorados ── */
  .modal-header {
    background: linear-gradient(135deg, #6366f1, #818cf8) !important;
    color: #fff !important;
    border: none !important;
    padding: 22px 24px !important;
    position: relative;
  }

  .modal-header .close {
    color: #fff !important;
    opacity: 0.8;
    text-shadow: none;
    font-size: 22px;
    position: absolute;
    right: 20px;
    top: 18px;
  }

  .modal-header .close:hover,
  .modal-header .close:focus {
    opacity: 1;
  }

  .modal-header h2,
  .modal-header h3,
  .modal-header h4 {
    margin: 0;
    font-weight: 700;
  }

  .modal-content {
    border: none;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(15,23,42,.18);
  }

  .modal-body {
    padding: 24px;
    background: #f8fafc;
  }

  .modal-footer {
    background: #fff;
    border-top: 1px solid #e2e8f0;
    padding: 16px 24px;
  }

  .modal-body .form-group {
    margin-bottom: 16px;
  }

  .modal-body .input-group {
    border-radius: 8px;
  }

  .modal-body .form-control {
    border: 1px solid #dbe3ef;
    border-radius: 8px;
    padding: 10px 12px;
    font-size: 14px;
  }

  .modal-body .form-control:focus {
    border-color: #a5b4fc;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, .12);
  }

  .modal-body .input-group-addon {
    background: #f0f4f8;
    border: 1px solid #dbe3ef;
    color: #6366f1;
  }

  /* ── Modal section cards ── */
  .pm-section {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 18px 20px;
    margin-bottom: 16px;
  }
  .pm-section-title {
    font-size: 11px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .5px; color: var(--crm-muted); margin: 0 0 12px;
    display: flex; align-items: center; gap: 6px;
  }
  .pm-section-title i { font-size: 12px; color: var(--crm-accent); }
  .pm-field-label {
    font-size: 12px; font-weight: 600; color: var(--crm-text2);
    margin-bottom: 5px; display: block;
  }
  .pm-row { display: flex; gap: 12px; flex-wrap: wrap; }
  .pm-row > .pm-col { flex: 1; min-width: 180px; }
  .pm-divider { border: none; border-top: 1px dashed #e2e8f0; margin: 4px 0 14px; }

  /* ── Fix selects inside modals ── */
  .pm-section select.form-control,
  .modal-body select.form-control {
    width: 100%;
    max-width: 100%;
    overflow: visible;
    text-overflow: ellipsis;
    white-space: nowrap;
    appearance: auto;
    -webkit-appearance: menulist;
    padding: 8px 28px 8px 10px;
    font-size: 13px;
    height: 38px;
    line-height: 1.4;
  }
  /* Keep Bootstrap 3 table layout for input-group (addon + input) */
  .pm-section .input-group {
    display: table;
    width: 100%;
  }
  .pm-section .input-group .input-group-addon {
    display: table-cell;
    width: 1%;
    white-space: nowrap;
    vertical-align: middle;
    border-radius: 8px 0 0 8px;
  }
  .pm-section .input-group .form-control {
    display: table-cell;
    width: 100%;
    border-radius: 0 8px 8px 0;
  }

  /* ── Modal header subtitle ── */
  .pm-header-sub {
    font-size: 13px; font-weight: 400; opacity: .8; margin-top: 4px;
  }
  .pm-header-id {
    font-size: 26px; font-weight: 800; letter-spacing: -.02em;
  }
  .pm-header-icon {
    display: inline-flex; align-items: center; justify-content: center;
    width: 44px; height: 44px; border-radius: 12px;
    background: rgba(255,255,255,.15); margin-right: 14px;
    font-size: 20px; vertical-align: middle;
  }

  /* ── Tabs de secciones ── */
  .ped-tabs {
    border-bottom: 2px solid #e2e8f0;
    margin: 0 0 0;
    padding: 0 4px;
  }

  .ped-tabs > li > a {
    border: 1px solid transparent;
    border-radius: 8px 8px 0 0;
    color: #64748b;
    font-size: 13px;
    font-weight: 600;
    padding: 10px 18px;
    transition: all .15s ease;
  }

  .ped-tabs > li.active > a,
  .ped-tabs > li.active > a:hover,
  .ped-tabs > li.active > a:focus {
    color: #6366f1;
    background: #fff;
    border-color: #e2e8f0 #e2e8f0 #fff;
  }

  .ped-tabs > li > a:hover {
    color: #4f46e5;
    background: #f0f4ff;
    border-color: transparent;
  }

  .ped-tab-content {
    border: none;
    padding: 16px 0 0;
    background: transparent;
  }

  /* ── Badges de estado ML ── */
  .ml-status-badge {
    display: inline-block;
    padding: .3em .65em;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 600;
    white-space: nowrap;
    border: 1px solid transparent;
  }

  .ml-badge-paid      { color: #166534; background: #f0fdf4; border-color: #bbf7d0; }
  .ml-badge-pending   { color: #92400e; background: #fffbeb; border-color: #fde68a; }
  .ml-badge-cancelled { color: #991b1b; background: #fef2f2; border-color: #fecaca; }
  .ml-badge-partial   { color: #0e7490; background: #ecfeff; border-color: #a5f3fc; }
  .ml-badge-other     { color: #475569; background: #f1f5f9; border-color: #cbd5e1; }

  #table-ml-pedidos thead th {
    white-space: nowrap;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .4px;
    color: var(--crm-text2);
    padding: 12px 10px;
  }

  #table-ml-pedidos tbody tr td {
    vertical-align: middle;
    padding: 11px 10px;
  }

</style>

<div class="content-wrapper">
  
   <section class="content-header">
      
    <h1>
      Gestor de Pedidos <small>Panel de control</small>
    </h1>

    <ol class="breadcrumb">

      <li><a href="inicio"><i class="fas fa-dashboard"></i>Inicio</a></li>

      <li class="active">Gestor de Pedidos</li>
      
    </ol>

  </section>


  <div class="content">

    <div class="ped-section">
      <div class="ped-section-icon"><i class="fa-solid fa-box"></i></div>
      <div>
        <h3>Administrador de Pedidos</h3>
        <p>Organiza, monitorea y gestiona todos tus pedidos en una única vista mejorada.</p>
      </div>
    </div>

    <!-- ── Navegación de tabs ───────────────────────────────────── -->
    <ul class="nav nav-tabs ped-tabs" id="pedidosTabs" role="tablist">
      <li role="presentation" class="active">
        <a href="#tab-pedidos-sistema" aria-controls="tab-pedidos-sistema" role="tab" data-toggle="tab">
          <i class="fa-solid fa-list"></i> Pedidos del Sistema
        </a>
      </li>
      <?php if ($mostrarMercadoLibre) { ?>
      <li role="presentation">
        <a href="#tab-ml" aria-controls="tab-ml" role="tab" data-toggle="tab" id="tab-ml-nav">
          <i class="fa-solid fa-store"></i> MercadoLibre
        </a>
      </li>
      <?php } ?>
    </ul>

    <div class="tab-content ped-tab-content">

    <div class="tab-pane active" id="tab-pedidos-sistema">

    <div class="row" id="PEDIDOS">
      <div class="col-12">
        <div class="ped-card">
          <div class="ped-card-head">
            <h3 class="ped-card-title"><i class="fa-solid fa-table"></i> Listado de Pedidos</h3>
            <div class="ped-actions-group">
              <?php
              if ($_SESSION["perfil"] == "administrador" || $_SESSION["perfil"] == "editor" || $_SESSION["perfil"] == "vendedor") {

                echo '<a href="vistas/modulos/descargar-reporte-pedidos.php?reporte=pedidos&empresa='.$_SESSION["empresa"].'" class="ped-btn ped-btn-success"><i class="fa-solid fa-file-excel"></i> Todos</a>';     
              
                echo '<a href="vistas/modulos/descargar-reporte-pedidos-pendientes.php?reporte=pedidosPendientes&empresa='.$_SESSION["empresa"].'" class="ped-btn ped-btn-success"><i class="fa-solid fa-file-excel"></i> Pendientes</a>';     
              
                echo '<a href="vistas/modulos/descargar-reporte-pedidos-adquiridos.php?reporte=pedidosAdquiridos&empresa='.$_SESSION["empresa"].'" class="ped-btn ped-btn-success"><i class="fa-solid fa-file-excel"></i> Adquiridos</a>';     

                echo '<a href="vistas/modulos/descargar-reporte-pedidos-asesor.php?reporte=pedidosAsesor&empresa='.$_SESSION["empresa"].'" class="ped-btn ped-btn-success"><i class="fa-solid fa-file-excel"></i> Asesor</a>';     

                echo '<a href="vistas/modulos/descargar-reporte-pedidos-pagados.php?reporte=pedidosPagados&empresa='.$_SESSION["empresa"].'" class="ped-btn ped-btn-success"><i class="fa-solid fa-file-excel"></i> Pagados</a>';     

                echo '<a href="vistas/modulos/descargar-reporte-pedidos-credito.php?reporte=pedidosCredito&empresa='.$_SESSION["empresa"].'" class="ped-btn ped-btn-success"><i class="fa-solid fa-file-excel"></i> Crédito</a>';     
                
              }  

              if ($_SESSION["perfil"] == "administrador") {
                echo '<a href="vistas/modulos/descargar-reporte-pedidos-sin-enlace.php?reporte=enlace&empresa='.$_SESSION["empresa"].'" class="ped-btn ped-btn-success"><i class="fa-solid fa-file-excel"></i> Enlace</a>';     
              }

              if ($_SESSION["perfil"] !== "tecnico") {
                echo '<button class="ped-btn ped-btn-primary" data-toggle="modal" data-target="#modalAgregarPedido"><i class="fa-solid fa-plus"></i> Nuevo Pedido</button>';
              }
              ?>
            </div>
          </div>
          <div class="ped-card-body">
            <div class="table-responsive-wrap">
              <table id="tablepedidos" class="table stripe ordenes order-table display compact cell-border hover row-border tablaPedidos" width="100%">
              
                <thead>
                  
                  <tr>
                    
                    <th style="width:40px">#</th>
                    <th>Empresa</th>
                    <th>No. Pedido</th>
                    <th>Cliente</th>
                    <th>Estado</th>
                    <th>Total del pedido</th>
                    <th>Orden asignada</th>
                    <th>Fecha de pedido</th>
                    <th>Fecha de entrega</th>
                    <th>Acciones</th>
                    <th>Detalles</th>

                  </tr>

                </thead> 

                <tbody>
                  
                  <?php
                    echo'

                    <input  type="hidden" id="tipoDePerfil" value="'.$_SESSION["perfil"].'"  placeholder="'.$_SESSION["perfil"].'">
                    <input  type="hidden" id="tipoidperfil" value="'.$_SESSION["id"].'"  placeholder="'.$_SESSION["id"].'">
                    <input  type="hidden" id="id_empresa" value="'.$_SESSION["empresa"].'">';
                  ?>
                
                </tbody>

              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    </div><!-- /tab-pane sistema -->

    <?php if ($mostrarMercadoLibre) { ?>
    <!-- ── Tab MercadoLibre ─────────────────────────────────────── -->
    <div class="tab-pane" id="tab-ml">
      <div class="row">
        <div class="col-12">
          <div class="ped-card">
            <div class="ped-card-head">
              <h3 class="ped-card-title">
                <i class="fa-solid fa-store"></i> Pedidos de MercadoLibre
              </h3>
              <div class="ped-actions-group">
                <button class="ped-btn ped-btn-primary" data-toggle="modal" data-target="#modalMLConfig">
                  <i class="fa-solid fa-gear"></i> Configurar
                </button>
                <button class="ped-btn ped-btn-success" id="btn-ml-sync">
                  <i class="fa-solid fa-rotate"></i> Sincronizar
                </button>
              </div>
            </div>
            <div class="ped-card-body">

              <div id="ml-status-bar" style="display:none;" class="alert">
                <span id="ml-status-msg"></span>
              </div>

              <div id="ml-loading" style="display:none; text-align:center; padding:30px;">
                <i class="fa-solid fa-spinner fa-spin fa-2x" style="color:#6366f1;"></i>
                <p style="margin-top:10px; color:#64748b; font-size:13px;">Cargando pedidos de MercadoLibre...</p>
              </div>

              <div class="table-responsive-wrap">
                <table id="table-ml-pedidos" class="table stripe display compact cell-border hover row-border" width="100%">
                  <thead>
                    <tr>
                      <th style="width:40px">#</th>
                      <th>No. Orden ML</th>
                      <th>Vendedor</th>
                      <th>Estado</th>
                      <th>Artículos</th>
                      <th>Total</th>
                      <th>Fecha</th>
                      <th>Acciones</th>
                    </tr>
                  </thead>
                  <tbody id="ml-tbody">
                    <tr>
                      <td colspan="8" style="text-align:center; color:#94a3b8; padding:30px; font-size:13px;">
                        Haz clic en <strong>Sincronizar</strong> para cargar los pedidos de MercadoLibre.
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <!-- Paginación ML -->
              <div id="ml-paginacion" style="display:none; margin-top:14px; display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                <button class="ped-btn ped-btn-primary" id="btn-ml-anterior">
                  <i class="fa-solid fa-chevron-left"></i> Anterior
                </button>
                <span id="ml-pag-info" style="font-size:12px; color:#475569; flex:1; text-align:center;"></span>
                <button class="ped-btn ped-btn-primary" id="btn-ml-siguiente">
                  Siguiente <i class="fa-solid fa-chevron-right"></i>
                </button>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div><!-- /tab-pane ml -->
    <?php } ?>

    </div><!-- /tab-content -->

  </div>

</div>
<!--=====================================
MODAL EDITAR PEDIDO
======================================-->
<div id="modalEditarPedido" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">

        <!-- Header -->
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <span class="pm-header-icon"><i class="fa-solid fa-pen-to-square"></i></span>
          <span class="pm-header-id">Pedido #<span class="NumeroDePedido"></span></span>
          <p class="pm-header-sub">Revisa y edita la información del pedido</p>
        </div>

        <!-- Body -->
        <div class="modal-body">
          <input type="hidden" class="idPedido">

          <!-- Asesor & Cliente -->
          <div class="pm-section">
            <div class="pm-section-title"><i class="fa-solid fa-user"></i> Información del Contacto</div>
            <div class="pm-row" style="margin-bottom:10px;">
              <div class="pm-col">
                <label class="pm-field-label">Asesor</label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa-solid fa-headset"></i></span>
                  <input type="text" class="form-control asesorDePedido" readonly>
                </div>
              </div>
            </div>
            <div class="pm-row">
              <div class="pm-col">
                <label class="pm-field-label">Cliente</label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa-solid fa-user"></i></span>
                  <input type="text" class="form-control clienteNombre" readonly>
                </div>
              </div>
              <div class="pm-col">
                <label class="pm-field-label">Teléfono</label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa-solid fa-phone"></i></span>
                  <input type="text" class="form-control clienteNumero" readonly>
                </div>
              </div>
              <div class="pm-col">
                <label class="pm-field-label">Orden</label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa-solid fa-hashtag"></i></span>
                  <input type="text" class="form-control clienteOrden" readonly>
                </div>
              </div>
            </div>
          </div>

          <!-- Productos -->
          <div class="pm-section">
            <div class="pm-section-title"><i class="fa-solid fa-box-open"></i> Productos del Pedido</div>

            <!-- Producto 1 -->
            <div class="productoUnoEdicionMostrar" style="display:none;">
              <div class="pm-row" style="margin-bottom:8px;">
                <div class="pm-col" style="flex:2;">
                  <label class="pm-field-label">Producto</label>
                  <input type="text" class="form-control edicionProductoUnoPedido">
                </div>
                <div class="pm-col">
                  <label class="pm-field-label">Precio</label>
                  <input type="number" class="form-control precioProductoPedidoEdicion" readonly>
                </div>
              </div>
              <div class="cantidadProductosUnoPedidoEditados" style="display:none; margin-bottom:10px;">
                <label class="pm-field-label">Cantidad</label>
                <input type="number" class="form-control cantidadDeProductoPedidoEditado" readonly style="max-width:140px;">
              </div>
              <hr class="pm-divider">
            </div>

            <!-- Producto 2 -->
            <div class="productoDosEdicionMostrar" style="display:none;">
              <div class="pm-row" style="margin-bottom:8px;">
                <div class="pm-col" style="flex:2;">
                  <label class="pm-field-label">Producto</label>
                  <input type="text" class="form-control edicionProductoUnoPedidoDos">
                </div>
                <div class="pm-col">
                  <label class="pm-field-label">Precio</label>
                  <input type="number" class="form-control precioProductoPedidoEdicionDos" readonly>
                </div>
              </div>
              <div class="cantidadProductosDosPedidoEditados" style="display:none; margin-bottom:10px;">
                <label class="pm-field-label">Cantidad</label>
                <input type="number" class="form-control cantidadDeProductoPedidoEditadoDos" readonly style="max-width:140px;">
              </div>
              <hr class="pm-divider">
            </div>

            <!-- Producto 3 -->
            <div class="productoTresEdicionMostrar" style="display:none;">
              <div class="pm-row" style="margin-bottom:8px;">
                <div class="pm-col" style="flex:2;">
                  <label class="pm-field-label">Producto</label>
                  <input type="text" class="form-control edicionProductoUnoPedidoTres">
                </div>
                <div class="pm-col">
                  <label class="pm-field-label">Precio</label>
                  <input type="number" class="form-control precioProductoPedidoEdicionTres" readonly>
                </div>
              </div>
              <div class="cantidadProductosTresPedidoEditados" style="display:none; margin-bottom:10px;">
                <label class="pm-field-label">Cantidad</label>
                <input type="number" class="form-control cantidadDeProductoPedidoEditadoTres" readonly style="max-width:140px;">
              </div>
              <hr class="pm-divider">
            </div>

            <!-- Producto 4 -->
            <div class="productoCuatroEdicionMostrar" style="display:none;">
              <div class="pm-row" style="margin-bottom:8px;">
                <div class="pm-col" style="flex:2;">
                  <label class="pm-field-label">Producto</label>
                  <input type="text" class="form-control edicionProductoUnoPedidoCuatro">
                </div>
                <div class="pm-col">
                  <label class="pm-field-label">Precio</label>
                  <input type="number" class="form-control precioProductoPedidoEdicionCuatro" readonly>
                </div>
              </div>
              <div class="cantidadProductosCuatroPedidoEditados" style="display:none; margin-bottom:10px;">
                <label class="pm-field-label">Cantidad</label>
                <input type="number" class="form-control cantidadDeProductoPedidoEditadoCuatro" readonly style="max-width:140px;">
              </div>
              <hr class="pm-divider">
            </div>

            <!-- Producto 5 -->
            <div class="productoCincoEdicionMostrar" style="display:none;">
              <div class="pm-row" style="margin-bottom:8px;">
                <div class="pm-col" style="flex:2;">
                  <label class="pm-field-label">Producto</label>
                  <input type="text" class="form-control edicionProductoUnoPedidoCinco">
                </div>
                <div class="pm-col">
                  <label class="pm-field-label">Precio</label>
                  <input type="number" class="form-control precioProductoPedidoEdicionCinco" readonly>
                </div>
              </div>
              <div class="cantidadProductosCincoPedidoEditados" style="display:none; margin-bottom:10px;">
                <label class="pm-field-label">Cantidad</label>
                <input type="number" class="form-control cantidadDeProductoPedidoEditadoCinco" readonly style="max-width:140px;">
              </div>
            </div>
          </div>

          <!-- Pagos & Totales -->
          <div class="pm-section">
            <div class="pm-section-title"><i class="fa-solid fa-money-bill-wave"></i> Pagos y Totales</div>
            <div class="pm-row" style="margin-bottom:10px;">
              <div class="pm-col">
                <label class="pm-field-label">Pago del Cliente</label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa-solid fa-hand-holding-dollar"></i></span>
                  <input type="number" class="form-control pagoClientePedido" value="0" min="0" step="any" readonly>
                </div>
              </div>
              <div class="pm-col">
                <label class="pm-field-label">Total</label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa-solid fa-coins"></i></span>
                  <input type="number" class="form-control pagoPedidoEdidato" readonly>
                </div>
              </div>
              <div class="pm-col">
                <label class="pm-field-label">Adeudo</label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa-solid fa-scale-unbalanced"></i></span>
                  <input type="number" class="form-control adeudoPedidoEditado" min="0" value="0" step="any" readonly>
                </div>
              </div>
            </div>

            <hr class="pm-divider">

            <!-- Abonos -->
            <div class="pm-row" style="margin-bottom:10px;">
              <div class="pm-col">
                <label class="pm-field-label">Abono Registrado</label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa-solid fa-receipt"></i></span>
                  <input class="form-control abono1Lectura" type="text" readonly>
                </div>
              </div>
              <div class="pm-col">
                <label class="pm-field-label">Fecha del Abono</label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa-solid fa-calendar"></i></span>
                  <input type="date" class="form-control fechaAbono1Lectura" readonly>
                </div>
              </div>
            </div>

            <button type="button" class="ped-btn ped-btn-primary" onclick="AgregarCampoDeaAbonoEditado();" style="font-size:12px; padding:7px 14px;">
              <i class="fa-solid fa-plus"></i> Agregar Abono
            </button>
            <div id="camposAbono"></div>
          </div>


          <!-- Estado & Fecha Entrega -->
          <div class="pm-section">
            <div class="pm-section-title"><i class="fa-solid fa-sliders"></i> Estado y Entrega</div>
            <div class="pm-row">
              <div class="pm-col">
                <label class="pm-field-label">Estado del Pedido</label>
                <select class="form-control EstadoDelPedido">
                  <option class="optionEstadoPedido"></option>
                  <option value="Pedido Pendiente">Pedido Pendiente</option>
                  <option value="Pedido Adquirido">Pedido Adquirido</option>
                  <option value="Producto en Almacen">Producto en Almacén</option>
                  <option value="Entregado al asesor">Entregado al Asesor</option>
                  <option value="Entregado/Pagado">Entregado/Pagado</option>
                  <option value="Entregado/Credito">Entregado/Crédito</option>
                </select>
              </div>
              <div class="pm-col">
                <label class="pm-field-label">Fecha de Entrega</label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa-solid fa-truck"></i></span>
                  <input type="date" class="form-control fechaEntregaPedidoEditado" readonly>
                </div>
              </div>
            </div>
          </div>

        </div>

        <!-- Footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-dismiss="modal" style="border:1px solid #dbe3ef; color:#334155; font-weight:600; border-radius:8px;">Cancelar</button>
          <button type="submit" class="ped-btn ped-btn-primary botonGuardarPedido"><i class="fa-solid fa-floppy-disk"></i> Guardar Pedido</button>
        </div>

      </div>
    </div>
</div>

<?php

  $eliminarPedido = new ControladorPedidos();
  $eliminarPedido -> ctrEliminarPedido();
?>


<!--=====================================
MODAL AGREGAR PEDIDO
======================================-->
<div id="modalAgregarPedido" class="modal fade" role="dialog"> 

  <form role="form" method="post" onsubmit="return false;" class="formularioPedidosDinamicos" id="pedidoNuevoForm" data-usuario="<?php echo htmlspecialchars((string)($_SESSION['id'] ?? $_SESSION['nombre'] ?? '') . ':' . (string)($_SESSION['empresa'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
  
    <div class="modal-dialog modal-lg">
      
      <div class="modal-content">

        <!-- Header -->
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <span class="pm-header-icon"><i class="fa-solid fa-box-open"></i></span>
          <span class="pm-header-id">Nuevo Pedido</span>
          <p class="pm-header-sub">Completa los datos para registrar un pedido</p>
        </div>

        <!-- Body -->
        <div class="modal-body">
          <div class="box-body">

            <?php echo '<input type="hidden" value="'.$_SESSION["empresa"].'" name="empresaPedioDinamico">'; ?>

            <!-- Asesor & Cliente -->
            <div class="pm-section">
              <div class="pm-section-title"><i class="fa-solid fa-user"></i> Asignación</div>
              <div class="pm-row" style="margin-bottom:12px;">
                <div class="pm-col">
                  <label class="pm-field-label">Asesor</label>
                  <select class="form-control asesorPedidoDinamico" name="asesorPedidoDinamico">
                    <option value="">Seleccionar Asesor</option>
                    <?php
                      $item = "id_empresa";
                      $valor = $_SESSION["empresa"];
                      $asesor = Controladorasesores::ctrMostrarAsesoresEmpresas($item,$valor);
                      foreach ($asesor as $key => $value) {
                        echo '<option value="'.$value["id"].'">'.htmlspecialchars($value["nombre"]).'</option>';
                      }
                    ?>
                  </select>
                </div>
                <div class="pm-col">
                  <label class="pm-field-label">Cliente</label>
                  <select class="form-control clientePedidoDinamico" name="clientePedidoDinamico">
                    <option value="">Seleccionar Cliente</option>
                    <?php
                      $item = "id_empresa";
                      $valor = $_SESSION["empresa"];
                      $usuario = ControladorClientes::ctrMostrarClientesTabla($item,$valor);
                      foreach ($usuario as $key => $value) {
                        echo '<option value="'.$value["id"].'">'.htmlspecialchars($value["nombre"]).'</option>';
                      }
                    ?>
                  </select>
                </div>
              </div>
            </div>

            <!-- Productos -->
            <div class="pm-section">
              <div class="pm-section-title"><i class="fa-solid fa-box-open"></i> Productos</div>
              <button type="button" class="ped-btn ped-btn-primary AgregarProductos" style="font-size:12px; padding:7px 14px; margin-bottom:12px;">
                <i class="fa-solid fa-plus"></i> Agregar Producto
              </button>
              <div class="NuevoProductoPedido"></div>
            </div>

            <!-- Pago & Total -->
            <div class="pm-section">
              <div class="pm-section-title"><i class="fa-solid fa-money-bill-wave"></i> Pago</div>
              <div class="pm-row" style="margin-bottom:12px;">
                <div class="pm-col">
                  <label class="pm-field-label">Pago del Cliente</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa-solid fa-hand-holding-dollar"></i></span>
                    <input type="number" min="0" step="0.01" class="form-control PagoClientePedidoDinamico">
                  </div>
                </div>
                <div class="pm-col">
                  <label class="pm-field-label">Restante</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa-solid fa-scale-unbalanced"></i></span>
                    <input type="number" class="form-control cambioClientePedidoDinamico" readonly>
                  </div>
                </div>
              </div>
              <div class="pm-row" style="margin-bottom:12px;">
                <div class="pm-col">
                  <label class="pm-field-label">Fecha de Pago</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa-solid fa-calendar"></i></span>
                    <input type="date" class="form-control fechaPagoVentaModal">
                    <input type="hidden" class="PrimerPagolistado" name="PrimerPagolistado">
                    <input type="hidden" class="PrimerAdeudo" name="PrimerAdeudo">
                  </div>
                </div>
                <div class="pm-col">
                  <label class="pm-field-label">Total del Pedido</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa-solid fa-coins"></i></span>
                    <input type="number" class="form-control TotalPedidoEnOrden monto totales" name="TotalPedidoEnOrden" readonly>
                  </div>
                </div>
              </div>
            </div>

            <!-- Estado & Orden -->
            <div class="pm-section">
              <div class="pm-section-title"><i class="fa-solid fa-sliders"></i> Estado y Orden</div>
              <div class="pm-row">
                <div class="pm-col">
                  <label class="pm-field-label">Estado del Pedido</label>
                  <select class="form-control estadoPedidoDinamico" name="EstadoPedidoDinamico">
                    <option class="Pedido Pendiente">Pedido Pendiente</option>
                    <option value="Pedido Adquirido">Pedido Adquirido</option>
                    <option value="Entregado al asesor">Entregado al Asesor</option>
                    <option value="Entregado/Pagado">Entregado/Pagado</option>
                    <option value="Entregado/Credito">Entregado/Crédito</option>
                  </select>
                </div>
                <div class="pm-col">
                  <label class="pm-field-label">Asignar Orden</label>
                  <select class="form-control seleccionarOrdenPedidoDinamico" name="seleccionarOrdenPedidoDinamico">
                    <option value="0">Sin orden</option>
                    <?php
                      $item = "id_empresa";
                      $valor = $_SESSION["empresa"];
                      $pedido = controladorOrdenes::ctrMostrarOrdenes($item,$valor);
                      foreach ($pedido as $key => $valueOrdenes) {
                        echo '<option value="'.$valueOrdenes["id"].'">#'.$valueOrdenes["id"].'</option>';
                      }
                    ?>
                  </select>
                </div>
              </div>
              <input type="hidden" id="ProductosPedidoListados" name="ProductosPedidoListados">
            </div>

          </div>
        </div>

        <!-- Footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-dismiss="modal" style="border:1px solid #dbe3ef; color:#334155; font-weight:600; border-radius:8px;">Cancelar</button>
          <button type="submit" class="ped-btn ped-btn-primary guardarPedidoDinamico"><i class="fa-solid fa-floppy-disk"></i> Guardar Pedido</button>
        </div>

      </div>
    </div>

    <?php if (isset($_POST['ProductosPedidoListados'])) ControladorPedidos::ctrAvisoPedido('error', 'No se guardó el pedido', 'El formulario debe guardar sin salir de la página. Recarga para cargar el guardado seguro.'); ?>

  </form>

</div>

<?php if ($mostrarMercadoLibre) { ?>
<!--=====================================
MODAL CONFIGURACIÓN MERCADOLIBRE
======================================-->
<div id="modalMLConfig" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><i class="fa-solid fa-store"></i> Configuración de MercadoLibre</h4>
      </div>
      <div class="modal-body">

        <!-- Alerta de estado OAuth (resultado de la redirección) -->
        <div id="ml-oauth-alert" class="alert" style="display:none; font-size:13px;"></div>

        <!-- Paso 1: Client ID + Secret → botón OAuth -->
        <div class="alert alert-info" style="font-size:12px; line-height:1.6;">
          <i class="fa-solid fa-circle-info"></i>
          <strong>Modo recomendado:</strong> ingresa tu <strong>Client ID</strong> y
          <strong>Client Secret</strong> y pulsa <em>Conectar con MercadoLibre</em> para
          obtener los tokens automáticamente.<br>
          O bien, pega el <strong>Access Token</strong> manualmente si lo tienes a la mano.
        </div>

        <!-- Botón OAuth -->
        <div class="form-group" style="margin-bottom:18px;">
          <button type="button" class="btn btn-warning btn-block" id="btn-ml-oauth" style="font-weight:600;">
            <i class="fa-solid fa-link"></i> Conectar con MercadoLibre (OAuth)
          </button>
          <small class="text-muted" id="ml-redirect-uri-hint" style="display:none; word-break:break-all;"></small>
        </div>
        <hr style="margin:10px 0 16px;">

        <div class="form-group">
          <label><i class="fas fa-key" style="color:#6366f1;"></i> Access Token <small class="text-muted">(requerido)</small></label>
          <div class="input-group">
            <span class="input-group-addon"><i class="fas fa-key"></i></span>
            <input type="text" class="form-control" id="ml-cfg-access-token" placeholder="APP_USR-1234567890...">
          </div>
        </div>

        <div class="form-group">
          <label><i class="fas fa-hashtag" style="color:#6366f1;"></i> ID de Usuario ML <small class="text-muted">(requerido)</small></label>
          <div class="input-group">
            <span class="input-group-addon"><i class="fas fa-hashtag"></i></span>
            <input type="text" class="form-control" id="ml-cfg-seller-id" placeholder="123456789">
          </div>
        </div>

        <hr>
        <p style="font-size:12px; color:#64748b; margin-bottom:12px;">
          <i class="fa-solid fa-rotate" style="color:#6366f1;"></i>
          <strong>Renovación automática</strong> (opcional): si configuras estos campos el sistema
          renovará el token automáticamente cuando expire.
        </p>

        <div class="form-group">
          <label>Client ID</label>
          <input type="text" class="form-control" id="ml-cfg-client-id" placeholder="123456789">
        </div>
        <div class="form-group">
          <label>Client Secret</label>
          <input type="password" class="form-control" id="ml-cfg-client-secret" placeholder="••••••••">
        </div>
        <div class="form-group">
          <label>Refresh Token</label>
          <input type="text" class="form-control" id="ml-cfg-refresh-token" placeholder="TG-...">
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="button" class="ped-btn ped-btn-primary" id="btn-guardar-ml-config">
          <i class="fa-solid fa-floppy-disk"></i> Guardar Configuración
        </button>
      </div>
    </div>
  </div>
</div>
<?php } ?>

<script>
/*=============================================
SERIALIZAR PRODUCTOS ANTES DE ENVIAR EL FORMULARIO
=============================================*/
$("#pedidoNuevoForm").on("submit", function(){
  if(typeof listarProductosPedidos === "function"){
    listarProductosPedidos();
  }
  if(typeof listarPrimerPago === "function"){
    listarPrimerPago();
  }
});

    /*=============================================
CARGAR LA TABLA DINÁMICA DE PEDIDOS
=============================================*/
$.ajax({
  url:"ajax/tablapedidos.ajax.php?perfil="+$("#tipoDePerfil").val()+"&empresa="+$("#id_empresa").val(),
 	success:function(respuesta){
    // Carga previa para validar disponibilidad de datos
 	}
 })

$("#tablepedidos").DataTable({
  "ajax": "ajax/tablapedidos.ajax.php?perfil="+$("#tipoDePerfil").val()+"&empresa="+$("#id_empresa").val(),
	 "deferRender": true,
	 "retrieve": true,
	 "processing": true,
	 "pageLength": 25,
	 "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
	 "autoWidth": false,
	 "order": [[0, 'asc']],
	 "columnDefs": [
	   { "targets": [9, 10], "orderable": false },
	   { "targets": [9, 10], "searchable": false }
	 ],
	 "language": {
	 	"sProcessing":     "Procesando...",
		"sLengthMenu":     "Mostrar _MENU_ registros",
		"sZeroRecords":    "No se encontraron resultados",
		"sEmptyTable":     "Ningún dato disponible en esta tabla",
		"sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_",
		"sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0",
		"sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
		"sInfoPostFix":    "",
		"sSearch":         "Buscar:",
		"sUrl":            "",
		"sInfoThousands":  ",",
		"sLoadingRecords": "Cargando...",
		"oPaginate": {
			"sFirst":    "Primero",
			"sLast":     "Último",
			"sNext":     "Siguiente",
			"sPrevious": "Anterior"
		},
		"oAria": {
				"sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
				"sSortDescending": ": Activar para ordenar la columna de manera descendente"
		}

	 }

});
/* Los manejadores de pedidos viven en vistas/js/gestor.pedidos.js (cargado en plantilla.php). */

<?php if ($mostrarMercadoLibre) { ?>
/*=============================================
INTEGRACIÓN MERCADOLIBRE
=============================================*/
var mlOffset   = 0;
var mlTotal    = 0;
var mlLimit    = 50;
var mlLoaded   = false;

/* Activar tab ML → cargar pedidos la primera vez */
$(document).on('shown.bs.tab', 'a[href="#tab-ml"]', function () {
  if (!mlLoaded) {
    verificarConfigML();
  }
});

/* Botón sincronizar */
$(document).on('click', '#btn-ml-sync', function () {
  mlOffset = 0;
  cargarPedidosML();
});

/* Paginación */
$(document).on('click', '#btn-ml-siguiente', function () {
  if (mlOffset + mlLimit < mlTotal) {
    mlOffset += mlLimit;
    cargarPedidosML();
  }
});
$(document).on('click', '#btn-ml-anterior', function () {
  if (mlOffset > 0) {
    mlOffset = Math.max(0, mlOffset - mlLimit);
    cargarPedidosML();
  }
});

/* ── Leer ml_status de la URL (regreso del OAuth) ───────────────────────── */
(function () {
  var params  = new URLSearchParams(window.location.search);
  var status  = params.get('ml_status');
  var msg     = params.get('ml_msg');
  var mlUser  = params.get('ml_user');
  var $alert  = $('#ml-oauth-alert');

  var msgs = {
    success     : '✅ MercadoLibre conectado correctamente. Usuario ID: ' + (mlUser || '—'),
    error       : '❌ ML devolvió un error: ' + (msg || ''),
    token_error : '❌ No se pudo obtener el token: ' + (msg || ''),
    no_code     : '❌ ML no envió el código de autorización.',
    csrf_error  : '❌ Error de seguridad (state inválido). Intenta de nuevo.',
    no_credentials : '❌ Guarda primero el Client ID y Client Secret antes de conectar.',
    no_session  : '❌ Tu sesión ha expirado. Inicia sesión nuevamente.',
  };

  if (status && msgs[status]) {
    var type = status === 'success' ? 'alert-success' : 'alert-danger';
    $alert.removeClass('alert-success alert-danger').addClass(type).html(msgs[status]).show();
    $('#modalMLConfig').modal('show');

    // Limpiar el parámetro de la URL sin recargar
    var clean = window.location.pathname + '?ruta=pedidos';
    window.history.replaceState({}, '', clean);
  }
})();

/* ── Botón Conectar con ML (OAuth) ──────────────────────────────────────── */
$(document).on('click', '#btn-ml-oauth', function () {
  var $btn = $(this);
  $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Generando enlace...');

  // Guardar primero client_id y client_secret si están escritos
  var clientId     = $('#ml-cfg-client-id').val().trim();
  var clientSecret = $('#ml-cfg-client-secret').val().trim();

  if (!clientId || !clientSecret) {
    swal({ type: 'warning', title: 'Faltan datos',
           text: 'Ingresa el Client ID y el Client Secret antes de conectar.',
           showConfirmButton: true });
    $btn.prop('disabled', false).html('<i class="fa-solid fa-link"></i> Conectar con MercadoLibre (OAuth)');
    return;
  }

  // Guardar credenciales primero, luego redirigir
  var datos = new FormData();
  datos.append('accion',        'guardarConfig');
  datos.append('access_token',  $('#ml-cfg-access-token').val().trim());
  datos.append('seller_id',     $('#ml-cfg-seller-id').val().trim());
  datos.append('client_id',     clientId);
  datos.append('client_secret', clientSecret);
  datos.append('refresh_token', $('#ml-cfg-refresh-token').val().trim());

  $.ajax({
    url: 'ajax/mercadolibre.ajax.php', method: 'POST', data: datos,
    cache: false, contentType: false, processData: false, dataType: 'json',
    success: function () {
      // Ahora obtener la URL de OAuth
      var d2 = new FormData();
      d2.append('accion', 'generarURLOAuth');
      $.ajax({
        url: 'ajax/mercadolibre.ajax.php', method: 'POST', data: d2,
        cache: false, contentType: false, processData: false, dataType: 'json',
        success: function (r) {
          if (r.status === 'ok') {
            // Mostrar la redirect_uri exacta antes de redirigir
            $('#ml-redirect-uri-hint')
              .html('<i class="fa-solid fa-circle-info"></i> <strong>Redirect URI que debes tener en ML:</strong><br><code style="word-break:break-all;">' + r.redirect_uri + '</code><br>Redirigiendo a MercadoLibre...')
              .show();
            $btn.html('<i class="fa-solid fa-spinner fa-spin"></i> Redirigiendo...');
            // Pequeña pausa para que el usuario vea la URI
            setTimeout(function () {
              window.location.href = r.url;
            }, 2500);
          } else {
            swal({ type: 'error', title: 'Error', text: r.error || 'No se pudo generar la URL.', showConfirmButton: true });
            $btn.prop('disabled', false).html('<i class="fa-solid fa-link"></i> Conectar con MercadoLibre (OAuth)');
          }
        },
        error: function () {
          swal({ type: 'error', title: 'Error de conexión', showConfirmButton: true });
          $btn.prop('disabled', false).html('<i class="fa-solid fa-link"></i> Conectar con MercadoLibre (OAuth)');
        }
      });
    },
    error: function () {
      swal({ type: 'error', title: 'Error al guardar credenciales', showConfirmButton: true });
      $btn.prop('disabled', false).html('<i class="fa-solid fa-link"></i> Conectar con MercadoLibre (OAuth)');
    }
  });
});

/* Guardar configuración */
$(document).on('click', '#btn-guardar-ml-config', function () {
  var $btn = $(this);
  $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Guardando...');

  var datos = new FormData();
  datos.append('accion',        'guardarConfig');
  datos.append('access_token',  $('#ml-cfg-access-token').val().trim());
  datos.append('seller_id',     $('#ml-cfg-seller-id').val().trim());
  datos.append('client_id',     $('#ml-cfg-client-id').val().trim());
  datos.append('client_secret', $('#ml-cfg-client-secret').val().trim());
  datos.append('refresh_token', $('#ml-cfg-refresh-token').val().trim());

  $.ajax({
    url:         'ajax/mercadolibre.ajax.php',
    method:      'POST',
    data:        datos,
    cache:       false,
    contentType: false,
    processData: false,
    dataType:    'json',
    success: function (resp) {
      $btn.prop('disabled', false).html('<i class="fa-solid fa-floppy-disk"></i> Guardar Configuración');
      if (resp.status === 'ok') {
        $('#modalMLConfig').modal('hide');
        swal({ type: 'success', title: 'Configuración guardada correctamente', showConfirmButton: true, confirmButtonText: 'OK' });
      } else {
        swal({ type: 'error', title: 'Error', text: resp.error || 'No se pudo guardar.', showConfirmButton: true });
      }
    },
    error: function () {
      $btn.prop('disabled', false).html('<i class="fa-solid fa-floppy-disk"></i> Guardar Configuración');
      swal({ type: 'error', title: 'Error de conexión', showConfirmButton: true });
    }
  });
});

function verificarConfigML() {
  var datos = new FormData();
  datos.append('accion', 'verificarConfig');

  $.ajax({
    url:         'ajax/mercadolibre.ajax.php',
    method:      'POST',
    data:        datos,
    cache:       false,
    contentType: false,
    processData: false,
    dataType:    'json',
    success: function (resp) {
      if (resp.configurado) {
        cargarPedidosML();
      } else {
        mostrarStatusML('info',
            '<i class="fa-solid fa-circle-info"></i> MercadoLibre no está configurado aún. ' +
           'Haz clic en <strong>Configurar</strong> para ingresar tu Access Token y tu ID de Usuario ML.');
      }
      /* Pre-llenar campos si hay datos */
      if (resp.seller_id) $('#ml-cfg-seller-id').val(resp.seller_id);
      if (resp.client_id) $('#ml-cfg-client-id').val(resp.client_id);
    }
  });
}

function cargarPedidosML() {
  $('#ml-loading').show();
  $('#ml-status-bar').hide();
  $('#ml-tbody').html(
    '<tr><td colspan="8" style="text-align:center; padding:20px;"><i class="fa-solid fa-spinner fa-spin" style="color:#6366f1;"></i></td></tr>'
  );

  var datos = new FormData();
  datos.append('accion', 'obtenerOrdenes');
  datos.append('offset', mlOffset);

  $.ajax({
    url:         'ajax/mercadolibre.ajax.php',
    method:      'POST',
    data:        datos,
    cache:       false,
    contentType: false,
    processData: false,
    dataType:    'json',
    success: function (resp) {
      $('#ml-loading').hide();

      if (!resp) {
        mostrarStatusML('danger', '<i class="fa-solid fa-triangle-exclamation"></i> Respuesta vacía del servidor.');
        return;
      }

      if (resp.error) {
        if (resp.error === 'config_missing') {
          mostrarStatusML('info',
            '<i class="fa-solid fa-circle-info"></i> ' + resp.message +
            ' Haz clic en <strong>Configurar</strong>.');
        } else {
          mostrarStatusML('danger',
            '<i class="fa-solid fa-triangle-exclamation"></i> Error MercadoLibre: ' +
            (resp.message || resp.error));
        }
        renderizarTablaML([]);
        return;
      }

      if (!resp.results || resp.results.length === 0) {
        mostrarStatusML('warning', '<i class="fa-solid fa-circle-info"></i> No se encontraron pedidos en MercadoLibre.');
        renderizarTablaML([]);
        return;
      }

      mlTotal  = resp.paging ? resp.paging.total : resp.results.length;
      mlLoaded = true;
      renderizarTablaML(resp.results);
      actualizarPaginacionML();
    },
    error: function () {
      $('#ml-loading').hide();
      mostrarStatusML('danger', '<i class="fa-solid fa-triangle-exclamation"></i> Error de conexión al cargar pedidos de MercadoLibre.');
    }
  });
}

function renderizarTablaML(pedidos) {
  var statusMap = {
    'paid':           { text: 'Pagado',              css: 'ml-badge-paid'      },
    'pending':        { text: 'Pendiente',            css: 'ml-badge-pending'   },
    'cancelled':      { text: 'Cancelado',            css: 'ml-badge-cancelled' },
    'partially_paid': { text: 'Pago Parcial',         css: 'ml-badge-partial'   },
    'in_process':     { text: 'En Proceso',           css: 'ml-badge-other'     },
    'on_hold':        { text: 'En Espera',            css: 'ml-badge-other'     },
  };

  if (!pedidos || pedidos.length === 0) {
    $('#ml-tbody').html(
      '<tr><td colspan="8" style="text-align:center; color:#94a3b8; padding:24px; font-size:13px;">Sin resultados</td></tr>'
    );
    return;
  }

  var html = '';

  $.each(pedidos, function (i, p) {
    var num    = mlOffset + i + 1;
    var estado = statusMap[p.status] || { text: p.status || '—', css: 'ml-badge-other' };
    var badge  = '<span class="ml-status-badge ' + estado.css + '">' + estado.text + '</span>';

    var items = '—';
    if (p.order_items && p.order_items.length > 0) {
      items = p.order_items[0].item.title;
      if (p.order_items.length > 1) {
        items += ' <small style="color:#94a3b8;">(+' + (p.order_items.length - 1) + ' más)</small>';
      }
    }

    var total = p.currency_id + ' ' + parseFloat(p.total_amount || 0).toLocaleString('es-MX', { minimumFractionDigits: 2 });

    var fecha = '—';
    if (p.date_created) {
      var d = new Date(p.date_created);
      fecha = d.toLocaleDateString('es-MX', { day: '2-digit', month: '2-digit', year: 'numeric' });
    }

    var sellerName = '—';
    if (p.seller) {
      sellerName = p.seller.nickname || '—';
    }

    html += '<tr>';
    html += '<td>' + num + '</td>';
    html += '<td><code style="font-size:11px; color:#4f46e5;">' + p.id + '</code></td>';
    html += '<td>' + sellerName + '</td>';
    html += '<td>' + badge + '</td>';
    html += '<td style="max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">' + items + '</td>';
    html += '<td><strong>' + total + '</strong></td>';
    html += '<td>' + fecha + '</td>';
    html += '<td style="white-space:nowrap;">';
    html += '<a href="index.php?ruta=infopedidoML&order_id=' + p.id + '" class="btn btn-xs btn-info" title="Ver detalles en el sistema" style="margin-right:4px;"><i class="fas fa-eye"></i></a>';
    html += '</td>';
    html += '</tr>';
  });

  $('#ml-tbody').html(html);
}

function actualizarPaginacionML() {
  if (mlTotal <= mlLimit) {
    $('#ml-paginacion').hide();
    return;
  }
  var desde = mlOffset + 1;
  var hasta = Math.min(mlOffset + mlLimit, mlTotal);
  $('#ml-pag-info').text('Mostrando ' + desde + ' – ' + hasta + ' de ' + mlTotal + ' pedidos');
  $('#btn-ml-anterior').prop('disabled', mlOffset === 0);
  $('#btn-ml-siguiente').prop('disabled', mlOffset + mlLimit >= mlTotal);
  $('#ml-paginacion').css('display', 'flex');
}

function mostrarStatusML(tipo, mensaje) {
  var colores = {
    'info':    'alert-info',
    'warning': 'alert-warning',
    'danger':  'alert-danger',
    'success': 'alert-success'
  };
  $('#ml-status-bar')
    .removeClass('alert-info alert-warning alert-danger alert-success')
    .addClass(colores[tipo] || 'alert-info')
    .html(mensaje)
    .show();
}
  <?php } ?>

</script>
