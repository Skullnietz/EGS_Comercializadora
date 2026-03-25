<?php
if ($_SESSION["perfil"] != "administrador" AND $_SESSION["perfil"] != "vendedor") {
  echo '<script>window.location = "index.php?ruta=ordenes";</script>';
  return;
}
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

  .bm-section {
    display: flex;
    align-items: center;
    gap: 14px;
    margin: 6px 0 16px;
    padding: 0 4px;
  }

  .bm-section-icon {
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

  .bm-section h3 {
    margin: 0;
    font-size: 15px;
    font-weight: 800;
    color: var(--crm-text);
  }

  .bm-section p {
    margin: 2px 0 0;
    font-size: 12px;
    color: var(--crm-muted);
  }

  .bm-card {
    background: var(--crm-surface);
    border: 1px solid var(--crm-border);
    border-radius: var(--crm-radius);
    box-shadow: var(--crm-shadow);
    overflow: hidden;
    transition: box-shadow .2s var(--crm-ease), transform .2s var(--crm-ease);
  }

  .bm-card:hover {
    box-shadow: var(--crm-shadow-lg);
  }

  .bm-card-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding: 16px 20px 14px;
    border-bottom: 1px solid #f1f5f9;
  }

  .bm-card-title {
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
    color: var(--crm-text);
    font-size: 14px;
    font-weight: 700;
    line-height: 1.3;
  }

  .bm-card-title i {
    color: var(--crm-accent);
  }

  .bm-card-subtitle {
    color: var(--crm-muted);
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .5px;
  }

  .bm-card-body {
    padding: 18px 20px 20px;
  }

  .box-header h3 {
    margin: 0;
    font-weight: 700;
  }

  .table-responsive-wrap {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }

  #tablematerial thead th {
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
  }

  /* Oculta indicadores de sorting en toda la tabla, pero mantiene ordenamiento al dar click */
  #tablematerial.dataTable thead .sorting,
  #tablematerial.dataTable thead .sorting_asc,
  #tablematerial.dataTable thead .sorting_desc,
  #tablematerial.dataTable thead .sorting_asc_disabled,
  #tablematerial.dataTable thead .sorting_desc_disabled,
  #tablematerial.dataTable thead .sorting_disabled {
    background-image: none !important;
    padding-right: 8px !important;
  }

  #tablematerial.dataTable thead .sorting::before,
  #tablematerial.dataTable thead .sorting::after,
  #tablematerial.dataTable thead .sorting_asc::before,
  #tablematerial.dataTable thead .sorting_asc::after,
  #tablematerial.dataTable thead .sorting_desc::before,
  #tablematerial.dataTable thead .sorting_desc::after,
  #tablematerial.dataTable thead .sorting_asc_disabled::before,
  #tablematerial.dataTable thead .sorting_asc_disabled::after,
  #tablematerial.dataTable thead .sorting_desc_disabled::before,
  #tablematerial.dataTable thead .sorting_desc_disabled::after,
  #tablematerial.dataTable thead .sorting_disabled::before,
  #tablematerial.dataTable thead .sorting_disabled::after {
    display: none !important;
    content: none !important;
  }

  #tablematerial.dataTable tbody tr td {
    vertical-align: middle;
  }

  .td-info {
    max-width: 360px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .td-orden {
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Courier New", monospace;
  }

  .thumb {
    width: 92px;
    height: 64px;
    object-fit: cover;
    border-radius: .5rem;
    border: 1px solid rgba(0, 0, 0, .08);
    transition: transform .15s ease, box-shadow .15s ease;
    cursor: zoom-in;
  }

  .thumb:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, .12);
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
  .badge-pendiente-aut  { color: #92400e; background: #fffbeb; border-color: #fde68a; }
  .badge-supervision    { color: #6d28d9; background: #f5f3ff; border-color: #ddd6fe; }
  .badge-garantia-acep  { color: #991b1b; background: #fef2f2; border-color: #fecaca; }
  .badge-prob-garantia  { color: #991b1b; background: #fef2f2; border-color: #fecaca; }
  .badge-garantia       { color: #991b1b; background: #fef2f2; border-color: #fecaca; }
  .badge-revision       { color: #b91c1c; background: #fef2f2; border-color: #fca5a5; }
  .badge-terminado      { color: #0e7490; background: #ecfeff; border-color: #a5f3fc; }
  .badge-entreg-asesor  { color: #065f46; background: #ecfdf5; border-color: #a7f3d0; }
  .badge-entreg-pagado  { color: #166534; background: #f0fdf4; border-color: #bbf7d0; }
  .badge-entreg-credito { color: #166534; background: #f0fdf4; border-color: #bbf7d0; }
  .badge-entregado      { color: #166534; background: #f0fdf4; border-color: #bbf7d0; }
  .badge-aceptado       { color: #1e40af; background: #eff6ff; border-color: #bfdbfe; }
  .badge-cancelada      { color: #475569; background: #f1f5f9; border-color: #e2e8f0; }
  .badge-sin-reparacion { color: #64748b; background: #f8fafc; border-color: #e2e8f0; }
  .badge-producto-venta { color: #c2410c; background: #fff7ed; border-color: #fed7aa; }
  .badge-prod-almacen   { color: #57534e; background: #fafaf9; border-color: #d6d3d1; }
  .badge-seguimiento    { color: #0369a1; background: #f0f9ff; border-color: #bae6fd; }
  .badge-otro           { color: #475569; background: #f1f5f9; border-color: #e2e8f0; }
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

  /* Fila de filtros */
  #tablematerial thead tr.filters th {
    background: #fafafa;
    position: sticky;
    top: 42px;
    z-index: 2;
  }

  #tablematerial thead tr.filters input,
  #tablematerial thead tr.filters select {
    width: 100%;
    box-sizing: border-box;
    padding: 6px 8px;
    font-size: 12px;
    border: 1px solid #dbe3ef;
    border-radius: 8px;
    color: var(--crm-text2);
    background: #fff;
  }

  #tablematerial thead tr.filters input:focus,
  #tablematerial thead tr.filters select:focus {
    outline: none;
    border-color: #a5b4fc;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, .12);
  }

  /* Controles superiores DataTables: mostrar registros y buscar */
  #tablematerial_wrapper .dataTables_length,
  #tablematerial_wrapper .dataTables_filter {
    margin-bottom: 12px;
  }

  #tablematerial_wrapper .dataTables_length label,
  #tablematerial_wrapper .dataTables_filter label {
    color: #475569;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .2px;
  }

  #tablematerial_wrapper .dataTables_length select {
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

  #tablematerial_wrapper .dataTables_filter input {
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

  #tablematerial_wrapper .dataTables_length select:focus,
  #tablematerial_wrapper .dataTables_filter input:focus {
    outline: none;
    border-color: #a5b4fc !important;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, .12);
  }

  #tablematerial_wrapper .dataTables_filter input::placeholder {
    color: #94a3b8;
    font-weight: 500;
  }

  .bm-export-btn,
  #tablematerial_wrapper .bm-export-btn {
    margin-right: 8px;
    border-radius: 8px;
    border: 1px solid #15803d;
    background: #16a34a;
    color: #fff;
    font-size: 12px;
    font-weight: 700;
    padding: 7px 12px;
    line-height: 1.2;
    transition: all .15s ease;
  }

  .bm-export-btn:hover,
  .bm-export-btn:focus,
  #tablematerial_wrapper .bm-export-btn:hover,
  #tablematerial_wrapper .bm-export-btn:focus {
    background: #15803d;
    border-color: #166534;
    color: #fff;
    outline: none;
  }

  /* Paginacion DataTables (Bootstrap) - override local para evitar estilo blanco por defecto */
  #tablematerial_wrapper .dataTables_paginate {
    margin-top: 14px;
  }

  /* Estructura real en esta vista: ul.pagination > li.paginate_button > a */
  #tablematerial_wrapper .dataTables_paginate ul.pagination > li.paginate_button > a {
    border-radius: 8px !important;
    border: 1px solid #dbe3ef !important;
    background: #ffffff !important;
    color: #334155 !important;
    margin-left: 6px;
    padding: 6px 12px !important;
    font-weight: 600;
    transition: all .15s ease;
  }

  #tablematerial_wrapper .dataTables_paginate ul.pagination > li.paginate_button > a:hover {
    background: #eef2ff !important;
    border-color: #a5b4fc !important;
    color: #3730a3 !important;
  }

  #tablematerial_wrapper .dataTables_paginate ul.pagination > li.paginate_button.active > a,
  #tablematerial_wrapper .dataTables_paginate ul.pagination > li.paginate_button.active > a:hover,
  #tablematerial_wrapper .dataTables_paginate ul.pagination > li.paginate_button.active > a:focus {
    background: #1a3152 !important;
    border-color: #1a3152 !important;
    color: #ffffff !important;
  }

  #tablematerial_wrapper .dataTables_paginate ul.pagination > li.paginate_button.disabled > a,
  #tablematerial_wrapper .dataTables_paginate ul.pagination > li.paginate_button.disabled > a:hover,
  #tablematerial_wrapper .dataTables_paginate ul.pagination > li.paginate_button.disabled > a:focus {
    background: #f8fafc !important;
    border-color: #e2e8f0 !important;
    color: #94a3b8 !important;
    cursor: not-allowed;
  }

  /* Limpia estilos residuales sobre el <li> para que no tape el boton interno */
  #tablematerial_wrapper .dataTables_paginate ul.pagination > li.paginate_button {
    background: transparent !important;
    border: 0 !important;
    box-shadow: none !important;
  }

  #tablematerial_wrapper .dataTables_paginate .pagination > li > a,
  #tablematerial_wrapper .dataTables_paginate .pagination > li > span {
    border-radius: 8px !important;
    border: 1px solid #dbe3ef !important;
    background: #ffffff !important;
    color: #334155 !important;
    margin-left: 6px;
    padding: 6px 12px;
    font-weight: 600;
    transition: all .15s ease;
  }

  #tablematerial_wrapper .dataTables_paginate .pagination > li > a:hover,
  #tablematerial_wrapper .dataTables_paginate .pagination > li > span:hover {
    background: #eef2ff !important;
    border-color: #a5b4fc !important;
    color: #3730a3 !important;
  }

  #tablematerial_wrapper .dataTables_paginate .pagination > .active > a,
  #tablematerial_wrapper .dataTables_paginate .pagination > .active > span,
  #tablematerial_wrapper .dataTables_paginate .pagination > .active > a:hover,
  #tablematerial_wrapper .dataTables_paginate .pagination > .active > span:hover,
  #tablematerial_wrapper .dataTables_paginate .pagination > .active > a:focus,
  #tablematerial_wrapper .dataTables_paginate .pagination > .active > span:focus {
    background: #1a3152 !important;
    border-color: #1a3152 !important;
    color: #ffffff !important;
  }

  #tablematerial_wrapper .dataTables_paginate .pagination > .disabled > a,
  #tablematerial_wrapper .dataTables_paginate .pagination > .disabled > span,
  #tablematerial_wrapper .dataTables_paginate .pagination > .disabled > a:hover,
  #tablematerial_wrapper .dataTables_paginate .pagination > .disabled > span:hover {
    background: #f8fafc !important;
    border-color: #e2e8f0 !important;
    color: #94a3b8 !important;
    cursor: not-allowed;
  }

  /* Tooltips */
  [data-tooltip] {
    position: relative;
  }

  [data-tooltip]:hover::after {
    content: attr(data-tooltip);
    position: absolute;
    left: 0;
    top: 100%;
    transform: translateY(6px);
    background: rgba(17, 24, 39, .95);
    color: #fff;
    font-size: .75rem;
    line-height: 1.2;
    padding: .4rem .55rem;
    border-radius: .375rem;
    max-width: 520px;
    white-space: normal;
    z-index: 99;
    box-shadow: 0 8px 16px rgba(0, 0, 0, .2);
  }

  /* Modal img */
  .img-modal {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, .7);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
  }

  .img-modal.open {
    display: flex;
  }

  .img-modal img {
    max-width: 92vw;
    max-height: 86vh;
    border-radius: .75rem;
    box-shadow: 0 16px 48px rgba(0, 0, 0, .5);
  }

  .img-modal .close {
    position: absolute;
    top: 12px;
    right: 16px;
    font-size: 28px;
    color: #fff;
    cursor: pointer;
    padding: .25rem .5rem;
    line-height: 1;
    opacity: .9;
  }

  @media (max-width: 767px) {
    .bm-card-head {
      flex-direction: column;
      align-items: flex-start;
    }

    .bm-card-body {
      padding: 12px;
    }

    .content {
      padding: 10px;
    }

    #tablematerial_wrapper .dataTables_length,
    #tablematerial_wrapper .dataTables_filter {
      float: none !important;
      text-align: left !important;
      width: 100%;
    }

    #tablematerial_wrapper .dataTables_filter input {
      width: 100%;
      min-width: 0;
      margin-left: 0;
    }

    #tablematerial_wrapper .bm-export-btn {
      display: inline-block;
      margin: 0 0 8px;
    }
  }
</style>

<div class="content-wrapper">
  <section class="content-header">
    <h1>Búsqueda de Material <small>Panel de control</small></h1>
    <ol class="breadcrumb">
      <li><a href="index.php?ruta=inicio"><i class="fa-solid fa-gauge"></i> Inicio</a></li>
      <li class="active">Búsqueda de Material</li>
    </ol>
  </section>

  <div class="content">
    <div class="bm-section">
      <div class="bm-section-icon"><i class="fa-solid fa-magnifying-glass"></i></div>
      <div>
        <h3>Explorador de equipos y órdenes</h3>
        <p>Filtra por técnico, estado, total y fecha para encontrar materiales más rápido.</p>
      </div>
    </div>

    <div class="row" id="ORDEN">
      <div class="col-12">
        <div class="bm-card">
          <div class="bm-card-head">
            <h3 class="bm-card-title"><i class="fa-solid fa-table"></i> Búsqueda de Producto</h3>
            <div>
              <button type="button" id="btnExportExcelMaterial" class="bm-export-btn"><i class="fa-solid fa-file-excel"></i> Excel</button>
              <span class="bm-card-subtitle">Vista avanzada de resultados</span>
            </div>
          </div>
          <div class="bm-card-body">
            <div class="table-responsive-wrap">

              <table id="tablematerial"
                class="table stripe ordenes order-table display compact cell-border hover row-border"
                style="width:100%">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Información</th>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>Orden</th>
                    <th>Técnico</th>
                    <th>Imagen</th>
                    <th>Estado</th>
                    <th>Total</th>
                    <th>Ingreso</th>
                    <th></th>
                  </tr>
                  <!-- Filtros por columna -->
                  <tr class="filters">
                    <th></th>
                    <th><input type="text" placeholder="Buscar información"></th>
                    <th><input type="text" placeholder="Buscar marca"></th>
                    <th><input type="text" placeholder="Buscar modelo"></th>
                    <th><input type="text" placeholder="Buscar orden"></th>
                    <th>
                      <select id="tecnicoFilter">
                        <option value="">Todos</option>
                        <!-- Se llena dinámicamente -->
                      </select>
                    </th>
                    <th></th>
                    <th>
                      <select id="estadoFilter">
                        <option value="">Todos</option>
                        <!-- Se llena dinámicamente -->
                      </select>
                    </th>
                    <th>
                      <div style="display:flex; gap:.25rem;">
                        <input type="number" step="0.01" id="minTotal" placeholder="Mín" style="width:50%;">
                        <input type="number" step="0.01" id="maxTotal" placeholder="Máx" style="width:50%;">
                      </div>
                    </th>
                    <th>
                      <div style="display:flex; gap:.25rem;">
                        <input type="date" id="minFecha" style="width:50%;">
                        <input type="date" id="maxFecha" style="width:50%;">
                      </div>
                    </th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $ordenes = ControladorOrdenes::ctrMostrarOrdenesMaterial();
                  foreach ($ordenes as $key => $value) {

                    // Técnico
                    $item = "id";
                    $valor = $value["id_tecnico"];
                    $tecnico = ControladorTecnicos::ctrMostrarTecnicos($item, $valor);
                    $NombreTecnico = $tecnico["nombre"];

                    // Enlace Info Orden
                    $InfoOrdenes = "<a href='index.php?ruta=infoOrden&idOrden=" . $value["id"] . "&empresa=" . $value["id_empresa"] . "&asesor=" . $value["id_Asesor"] . "&cliente=" . $value["id_usuario"] . "&tecnico=" . $value["id_tecnico"] . "&pedido=" . $value["id_pedido"] . "' class='btn btn-warning btnVerInfoOrden' idOrden='" . $value["id"] . "' cliente='" . $value["id_usuario"] . "'  tecnico='" . $value["id_tecnico"] . "' asesor='" . $value["id_Asesor"] . "' empresa='" . $value["id_empresa"] . "' pedido='" . $value["id_pedido"] . "' data-toggle='modal' target='_blank' title='Ver detalles de la orden'><i class='fas fa-eye'></i></a>";

                    // Marca / Modelo
                    $marca = isset($value["marcaDelEquipo"]) ? htmlspecialchars($value["marcaDelEquipo"]) : "";
                    $modelo = isset($value["modeloDelEquipo"]) ? htmlspecialchars($value["modeloDelEquipo"]) : "";

                    // Estado -> badge (estandarizado)
                    $estadoText = (string) $value["estado"];
                    $estadoRaw = trim(mb_strtolower($estadoText, 'UTF-8'));
                    // Reusar helper de ordenes.php si existe, sino inline
                    if (function_exists('_ordGetBadgeClass')) {
                      $estadoClass = _ordGetBadgeClass($estadoText);
                    } else {
                      $estadoClass = 'badge-otro';
                      if (strpos($estadoRaw, 'autorización') !== false || strpos($estadoRaw, 'autorizacion') !== false || $estadoRaw === 'aut') $estadoClass = 'badge-pendiente-aut';
                      elseif (strpos($estadoRaw, 'pendiente') !== false) $estadoClass = 'badge-pendiente-aut';
                      elseif (strpos($estadoRaw, 'supervisión') !== false || strpos($estadoRaw, 'supervision') !== false || $estadoRaw === 'sup') $estadoClass = 'badge-supervision';
                      elseif (strpos($estadoRaw, 'garantía aceptada') !== false || strpos($estadoRaw, 'garantia aceptada') !== false || $estadoRaw === 'ga') $estadoClass = 'badge-garantia-acep';
                      elseif (strpos($estadoRaw, 'probable garantía') !== false || strpos($estadoRaw, 'probable garantia') !== false) $estadoClass = 'badge-prob-garantia';
                      elseif (strpos($estadoRaw, 'garantía') !== false || strpos($estadoRaw, 'garantia') !== false) $estadoClass = 'badge-garantia';
                      elseif (strpos($estadoRaw, 'revisión') !== false || strpos($estadoRaw, 'revision') !== false || $estadoRaw === 'rev') $estadoClass = 'badge-revision';
                      elseif (strpos($estadoRaw, 'terminada') !== false || $estadoRaw === 'ter') $estadoClass = 'badge-terminado';
                      elseif (strpos($estadoRaw, 'entregado al asesor') !== false) $estadoClass = 'badge-entreg-asesor';
                      elseif (strpos($estadoRaw, 'entregado/pagado') !== false) $estadoClass = 'badge-entreg-pagado';
                      elseif (strpos($estadoRaw, 'entregado/credito') !== false || strpos($estadoRaw, 'entregado/crédito') !== false) $estadoClass = 'badge-entreg-credito';
                      elseif (strpos($estadoRaw, 'entregado') !== false || strpos($estadoRaw, 'entregada') !== false) $estadoClass = 'badge-entregado';
                      elseif (strpos($estadoRaw, 'aceptado') !== false || strpos($estadoRaw, 'aceptada') !== false || $estadoRaw === 'ok') $estadoClass = 'badge-aceptado';
                      elseif (strpos($estadoRaw, 'cancel') !== false) $estadoClass = 'badge-cancelada';
                      elseif (strpos($estadoRaw, 'sin reparación') !== false || strpos($estadoRaw, 'sin reparacion') !== false || $estadoRaw === 'sr') $estadoClass = 'badge-sin-reparacion';
                      elseif (strpos($estadoRaw, 'producto para venta') !== false || $estadoRaw === 'pv') $estadoClass = 'badge-producto-venta';
                      elseif (strpos($estadoRaw, 'producto en almac') !== false) $estadoClass = 'badge-prod-almacen';
                      elseif (strpos($estadoRaw, 'seguimiento') !== false) $estadoClass = 'badge-seguimiento';
                    }

                    // Total + Fecha
                    $totalNum = (float) $value["total"];
                    $totalFmt = number_format($totalNum, 2, '.', ',');
                    $fechaIngresoRaw = isset($value["fecha_ingreso"]) ? trim((string)$value["fecha_ingreso"]) : '';
                    $fechaTs = strtotime($fechaIngresoRaw);
                    $fechaIngresoIso = $fechaTs ? date('Y-m-d', $fechaTs) : '';
                    $fechaIngreso = htmlspecialchars($fechaIngresoRaw);
                  
                    // Primer thumbnail
                    $thumbHtml = '';
                    $AlbumDeImagenes = json_decode($value["multimedia"], true);
                    if (is_array($AlbumDeImagenes)) {
                      foreach ($AlbumDeImagenes as $k => $valueImagenes) {
                        if (!empty($valueImagenes["foto"])) {
                          $src = htmlspecialchars($valueImagenes["foto"]);
                          $thumbHtml = '<img class="thumb" src="' . $src . '" alt="Foto" data-full="' . $src . '" onclick="openImageModal(this)">';
                          break;
                        }
                      }
                    }

                    echo '<tr>';
                    echo '  <td>' . ($key + 1) . '</td>';
                    $info = htmlspecialchars($value["partidaUno"]);
                    echo '  <td class="td-info" data-tooltip="' . htmlspecialchars($value["partidaUno"]) . '">' . $info . '</td>';
                    echo '  <td>' . $marca . '</td>';
                    echo '  <td>' . $modelo . '</td>';
                    echo '  <td class="td-orden" data-orden="' . htmlspecialchars($value["id"]) . '">' . htmlspecialchars($value["id"]) . '</td>';
                    echo '  <td>' . htmlspecialchars($NombreTecnico) . '</td>';
                    echo '  <td>' . $thumbHtml . '</td>';
                    echo '  <td class="td-estado" data-estado="' . htmlspecialchars($estadoText) . '"><span class="badge ' . $estadoClass . '">' . htmlspecialchars($estadoText) . '</span></td>';
                    echo '  <td data-total="' . $totalNum . '">$ ' . $totalFmt . '</td>';
                    echo '  <td data-fecha="' . htmlspecialchars($fechaIngresoIso) . '">' . $fechaIngreso . '</td>';
                    echo '  <td class="td-actions">' . $InfoOrdenes . '</td>';
                    echo '</tr>';
                  }
                  ?>
                </tbody>
              </table>

              <!-- Modal imagen -->
              <div class="img-modal" id="imgModal" aria-hidden="true"
                onclick="if(event.target === this) closeImageModal()">
                <span class="close" id="imgModalClose" aria-label="Cerrar" onclick="closeImageModal()">&times;</span>
                <img id="imgModalImg" alt="Imagen">
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- jQuery (carga condicional) -->
<script>
  if (!window.jQuery) {
    var s = document.createElement('script');
    s.src = "https://code.jquery.com/jquery-3.7.1.min.js";
    document.head.appendChild(s);
  }
</script>

<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

<script>
  // ---- Modal de imagen (Plain JS) ----
  function openImageModal(imgElement) {
    const modal = document.getElementById('imgModal');
    const modalImg = document.getElementById('imgModalImg');
    if (!modal || !modalImg) return;

    const fullSrc = imgElement.getAttribute('data-full') || imgElement.src;

    modalImg.src = fullSrc;
    modal.classList.add('open');
    modal.setAttribute('aria-hidden', 'false');
  }

  function closeImageModal() {
    const modal = document.getElementById('imgModal');
    const modalImg = document.getElementById('imgModalImg');
    if (!modal || !modalImg) return;

    modal.classList.remove('open');
    modal.setAttribute('aria-hidden', 'true');
    modalImg.src = '';
  }

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      const modal = document.getElementById('imgModal');
      if (modal && modal.classList.contains('open')) {
        closeImageModal();
      }
    }
  });

  const fmtMXN = new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN', minimumFractionDigits: 2 });

  // Normaliza texto para comparaciones de filtros (acentos, espacios y mayúsculas).
  function normalizeText(value) {
    return (value || '')
      .toString()
      .trim()
      .toLowerCase()
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '')
      .replace(/\s+/g, ' ');
  }

  function parseIsoDateOnly(value) {
    const raw = (value || '').toString().trim();
    const m = raw.match(/^(\d{4})-(\d{2})-(\d{2})$/);
    if (!m) return null;
    const d = new Date(raw + 'T00:00:00');
    return isNaN(d.getTime()) ? null : d;
  }

  function textFromCell($cell) {
    const dataState = $cell.attr('data-estado');
    if (dataState) return dataState.toString().trim();
    return $cell.text().replace(/\s+/g, ' ').trim();
  }

  function exportFilteredRowsToExcel(table) {
    if (typeof XLSX === 'undefined') {
      alert('No se pudo cargar la libreria de Excel. Intenta recargar la pagina.');
      return;
    }

    const rows = [];
    table.rows({ search: 'applied' }).every(function () {
      const $row = $(this.node());
      if (!$row.length) return;

      const totalRaw = parseFloat($row.find('td:eq(8)').attr('data-total'));
      rows.push([
        textFromCell($row.find('td:eq(0)')),
        textFromCell($row.find('td:eq(1)')),
        textFromCell($row.find('td:eq(2)')),
        textFromCell($row.find('td:eq(3)')),
        textFromCell($row.find('td:eq(4)')),
        textFromCell($row.find('td:eq(5)')),
        textFromCell($row.find('td:eq(7)')),
        isNaN(totalRaw) ? textFromCell($row.find('td:eq(8)')) : totalRaw,
        textFromCell($row.find('td:eq(9)'))
      ]);
    });

    if (!rows.length) {
      alert('No hay registros filtrados para exportar.');
      return;
    }

    const header = [[
      '#', 'Informacion', 'Marca', 'Modelo', 'Orden', 'Tecnico', 'Estado', 'Total', 'Ingreso'
    ]];
    const ws = XLSX.utils.aoa_to_sheet(header.concat(rows));
    ws['!cols'] = [
      { wch: 6 }, { wch: 45 }, { wch: 20 }, { wch: 20 }, { wch: 12 },
      { wch: 22 }, { wch: 24 }, { wch: 14 }, { wch: 14 }
    ];

    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Busqueda Material');

    const today = new Date();
    const stamp = today.getFullYear().toString()
      + String(today.getMonth() + 1).padStart(2, '0')
      + String(today.getDate()).padStart(2, '0')
      + '_'
      + String(today.getHours()).padStart(2, '0')
      + String(today.getMinutes()).padStart(2, '0');
    XLSX.writeFile(wb, 'busqueda_material_filtrada_' + stamp + '.xlsx');
  }

  // Filtro global extra: Total (rango) + Fecha (rango) + Estado (select)
  $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
    if (!settings || !settings.nTable || settings.nTable.id !== 'tablematerial') {
      return true;
    }

    const rowNode = settings.aoData[dataIndex] && settings.aoData[dataIndex].nTr
      ? settings.aoData[dataIndex].nTr
      : null;
    if (!rowNode) return true;
    const $row = $(rowNode);

    // ----- Total (col 8) -----
    const minTotal = parseFloat($('#minTotal').val());
    const maxTotal = parseFloat($('#maxTotal').val());
    const totalCell = $row.find('td:eq(8)');
    const totalVal = parseFloat(totalCell.attr('data-total'));
    if (!isNaN(minTotal) && (isNaN(totalVal) || totalVal < minTotal)) return false;
    if (!isNaN(maxTotal) && (isNaN(totalVal) || totalVal > maxTotal)) return false;

    // ----- Fecha (col 9) -----
    const minFechaStr = $('#minFecha').val(); // yyyy-mm-dd
    const maxFechaStr = $('#maxFecha').val();
    const fechaCell = $row.find('td:eq(9)');
    const hayFiltroFecha = !!(minFechaStr || maxFechaStr);
    const fechaIso = (fechaCell.attr('data-fecha') || '').trim();
    const f = parseIsoDateOnly(fechaIso);

    // Si se está filtrando por fecha, una fila sin fecha valida no debe pasar.
    if (hayFiltroFecha && !f) return false;

    if (f) {
      if (minFechaStr) {
        const fMin = parseIsoDateOnly(minFechaStr);
        if (fMin && f < fMin) return false;
      }
      if (maxFechaStr) {
        const fMax = parseIsoDateOnly(maxFechaStr);
        if (fMax) {
          fMax.setHours(23, 59, 59, 999);
          if (f > fMax) return false;
        }
      }
    }

    // ----- Estado (col 7) -----
    const estadoSel = normalizeText($('#estadoFilter').val());
    if (estadoSel) {
      const estadoCell = $row.find('td:eq(7)');
      const estadoTxt = normalizeText(estadoCell.attr('data-estado') || estadoCell.text());
      if (estadoTxt !== estadoSel) return false;
    }

    return true;
  });

  $(document).ready(function () {

    var table = $('#tablematerial').DataTable({
      language: { url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" },
      orderCellsTop: true,
      order: [],
      pageLength: 25,
      lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
      autoWidth: false,
      columnDefs: [
        { targets: [0, 6, 10], orderable: false },  // #, Imagen, Acción
        { targets: [6, 10], searchable: false }    // Imagen, Acción
      ]
    });

    $(document).off('click', '#btnExportExcelMaterial').on('click', '#btnExportExcelMaterial', function () {
      exportFilteredRowsToExcel(table);
    });

    // ---- Búsquedas por columna (Información=1, Marca=2, Modelo=3, Orden=4) ----
    $('#tablematerial thead tr.filters th').each(function (i) {
      const $input = $(this).find('input[type="text"]');
      if ($input.length) {
        $input.on('keyup change', function () {
          if (table.column(i).search() !== this.value) {
            table.column(i).search(this.value).draw();
          }
        });
      }
    });

    // ---- Poblar el select de Estado (col 7) ----
    const estadosMap = new Map();
    table.column(7).data().each(function (val) {
      const div = document.createElement('div'); div.innerHTML = val;
      const txt = (div.textContent || div.innerText || '').trim();
      const key = normalizeText(txt);
      if (key && !estadosMap.has(key)) estadosMap.set(key, txt);
    });
    const estados = Array.from(estadosMap.entries()).sort((a, b) => a[1].localeCompare(b[1], 'es'));
    const $estado = $('#estadoFilter');
    estados.forEach(([key, label]) => {
      const opt = document.createElement('option');
      opt.value = key;
      opt.textContent = label;
      $estado.append(opt);
    });

    // ---- Poblar el select de Técnico (col 5) ----
    const tecnicosSet = new Set();
    table.column(5).data().each(function (val) {
      const txt = (val || '').toString().trim();
      if (txt) tecnicosSet.add(txt);
    });
    const tecnicos = Array.from(tecnicosSet).sort((a, b) => a.localeCompare(b, 'es'));
    const $tecnico = $('#tecnicoFilter');
    tecnicos.forEach(t => {
      const opt = document.createElement('option');
      opt.value = t;
      opt.textContent = t;
      $tecnico.append(opt);
    });

    // ---- Listener técnico: filtro exacto por columna 5 ----
    $('#tecnicoFilter').on('change', function () {
      const val = this.value;
      if (val) {
        // Coincidencia exacta (regex) escapando caracteres especiales
        const esc = $.fn.dataTable.util.escapeRegex(val);
        table.column(5).search('^' + esc + '$', true, false).draw();
      } else {
        table.column(5).search('', true, false).draw();
      }
    });

    // ---- Listeners para filtros avanzados ----
    $('#estadoFilter, #minTotal, #maxTotal, #minFecha, #maxFecha').on('change keyup', function () {
      table.draw();
    });

    // ---- Re-formatea "Total" con Intl al dibujar ----
    const fmtMXN = new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN', minimumFractionDigits: 2 });
    table.on('draw', function () {
      $('#tablematerial tbody tr td[data-total]').each(function () {
        var val = parseFloat($(this).attr('data-total'));
        if (!isNaN(val)) $(this).text(fmtMXN.format(val));
      });
    }).trigger('draw');

    // ---- Modal de imagen (ahora en Plain JS global) ----
  });
</script>