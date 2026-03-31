<?php

if ($_SESSION["perfil"] != "administrador" AND $_SESSION["perfil"] != "vendedor" AND $_SESSION["perfil"] != "tecnico" AND $_SESSION["perfil"] != "secretaria" AND $_SESSION["perfil"] != "Super-Administrador") {



  echo '<script>



  window.location = "index.php?ruta=ordenes";



  </script>';



  return;

}



?>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.css" />
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.js"></script>

<style>
/* ═══════════════════════════════════════════════════
   ORDENES — CRM DESIGN SYSTEM OVERRIDE
   ═══════════════════════════════════════════════════ */
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

/* ─── Content Header ─── */
.content-wrapper .content-header {
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 60%, #334155 100%);
  padding: 28px 30px 24px;
  margin: 0;
  border-bottom: none;
}
.content-wrapper .content-header h1 {
  color: #fff;
  font-size: 22px;
  font-weight: 800;
  letter-spacing: -.02em;
  margin: 0 0 4px;
}
.content-wrapper .content-header h1 small {
  color: rgba(255,255,255,.5);
  font-size: 13px;
  font-weight: 400;
  display: block;
  margin-top: 4px;
}
.content-wrapper .content-header .breadcrumb {
  background: rgba(255,255,255,.08);
  border-radius: 8px;
  padding: 6px 14px;
  margin: 0;
  top: 28px;
}
.content-wrapper .content-header .breadcrumb > li,
.content-wrapper .content-header .breadcrumb > li > a,
.content-wrapper .content-header .breadcrumb > li.active {
  color: rgba(255,255,255,.6);
  font-size: 12px;
  font-weight: 500;
}
.content-wrapper .content-header .breadcrumb > li > a:hover { color: #fff; }
.content-wrapper .content-header .breadcrumb > .separator,
.content-wrapper .content-header .breadcrumb-item + .breadcrumb-item::before {
  color: rgba(255,255,255,.3);
}

/* ─── Main Content ─── */
.content-wrapper .content { background: var(--crm-bg); padding: 24px; }

/* ─── Box → CRM Card ─── */
.content .box {
  background: var(--crm-surface);
  border: 1px solid var(--crm-border);
  border-radius: var(--crm-radius);
  box-shadow: var(--crm-shadow);
  overflow: visible;
  margin-bottom: 20px;
}
.content .box-header {
  background: transparent;
  border-bottom: 1px solid #f1f5f9;
  padding: 18px 22px 14px;
  border-radius: var(--crm-radius) var(--crm-radius) 0 0;
}
.content .box-header .box-title {
  font-size: 14px;
  font-weight: 700;
  color: var(--crm-text);
}
.content .box-body {
  padding: 22px;
}

/* ─── Toolbar Area ─── */
#ultimaenentrega {
  background: linear-gradient(135deg, #f0f9ff, #eff6ff) !important;
  border-radius: var(--crm-radius-sm) !important;
  border: 1px solid #bfdbfe !important;
  padding: 12px 18px !important;
  margin-bottom: 6px;
}
.columnaultimaentrega { font-size: 13px; }
.columnaultimaentrega h5 { margin: 4px 0; font-size: 13px; color: #1e40af; }

/* ─── Buttons ─── */
.btn-primary {
  background: linear-gradient(135deg, var(--crm-accent), var(--crm-accent2));
  border: none;
  border-radius: 10px;
  padding: 10px 22px;
  font-weight: 600;
  font-size: 13px;
  box-shadow: 0 2px 8px rgba(99,102,241,.25);
  transition: all .2s var(--crm-ease);
}
.btn-primary:hover, .btn-primary:focus {
  background: linear-gradient(135deg, #4f46e5, #6366f1);
  box-shadow: 0 4px 14px rgba(99,102,241,.35);
  transform: translateY(-1px);
}
.btn-warning {
  background: linear-gradient(135deg, #f59e0b, #fbbf24);
  border: none;
  border-radius: 8px;
  color: #fff;
  font-weight: 600;
  font-size: 12px;
  padding: 6px 14px;
  transition: all .15s;
}
.btn-warning:hover { background: linear-gradient(135deg, #d97706, #f59e0b); color: #fff; transform: translateY(-1px); }
.btn-danger {
  background: linear-gradient(135deg, #ef4444, #f87171);
  border: none;
  border-radius: 8px;
  color: #fff;
  font-weight: 600;
  font-size: 12px;
  padding: 6px 14px;
  transition: all .15s;
}
.btn-danger:hover { background: linear-gradient(135deg, #dc2626, #ef4444); transform: translateY(-1px); }
.btn-success {
  background: linear-gradient(135deg, #22c55e, #4ade80);
  border: none;
  border-radius: 8px;
  color: #fff;
  font-weight: 600;
  transition: all .15s;
}
.btn-success:hover { background: linear-gradient(135deg, #16a34a, #22c55e); transform: translateY(-1px); }
.btn-info {
  background: linear-gradient(135deg, #06b6d4, #22d3ee);
  border: none;
  border-radius: 8px;
  color: #fff;
  font-weight: 600;
  transition: all .15s;
}
.btn-info:hover { background: linear-gradient(135deg, #0891b2, #06b6d4); transform: translateY(-1px); }

/* ─── DataTable Modern ─── */
.dataTables_wrapper { font-family: inherit; }
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_paginate {
  padding: 12px 8px;
  font-size: 13px;
  color: var(--crm-text2);
}
.dataTables_wrapper .dataTables_filter input {
  border: 1px solid var(--crm-border);
  border-radius: 8px;
  padding: 6px 14px;
  font-size: 13px;
  transition: border-color .2s, box-shadow .2s;
  outline: none;
}
.dataTables_wrapper .dataTables_filter input:focus {
  border-color: var(--crm-accent);
  box-shadow: 0 0 0 3px rgba(99,102,241,.1);
}
.dataTables_wrapper .dataTables_length select {
  border: 1px solid var(--crm-border);
  border-radius: 6px;
  padding: 4px 8px;
  font-size: 13px;
}
table.dataTable thead th {
  background: #f8fafc !important;
  color: var(--crm-muted) !important;
  font-size: 10px !important;
  font-weight: 700 !important;
  text-transform: uppercase !important;
  letter-spacing: .6px !important;
  padding: 12px 14px !important;
  border-bottom: 2px solid var(--crm-border) !important;
}
table.dataTable tbody td {
  font-size: 13px;
  color: var(--crm-text);
  padding: 12px 14px !important;
  border-bottom: 1px solid #f1f5f9 !important;
  vertical-align: middle;
}
table.dataTable tbody tr:hover {
  background: #f8fafc !important;
}
table.dataTable tbody tr.atraso {
  background: linear-gradient(90deg, #fef2f2, #fff5f5) !important;
  border-left: 3px solid #ef4444;
}
table.dataTable tbody tr.atraso:hover {
  background: #fef2f2 !important;
}
/* Pagination */
.dataTables_wrapper .dataTables_paginate .paginate_button {
  border-radius: 8px !important;
  border: 1px solid var(--crm-border) !important;
  margin: 0 2px;
  padding: 5px 12px !important;
  font-size: 12px;
  font-weight: 600;
  transition: all .15s;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.current {
  background: var(--crm-accent) !important;
  color: #fff !important;
  border-color: var(--crm-accent) !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
  background: #eef2ff !important;
  color: var(--crm-accent) !important;
  border-color: var(--crm-accent) !important;
}

/* ─── Status Badges Modern ─── */
.badge {
  display: inline-flex;
  align-items: center;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 11px;
  font-weight: 600;
  letter-spacing: .02em;
  white-space: nowrap;
  border: 1px solid transparent;
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

/* ─── Modals Modern ─── */
.modal-content {
  border-radius: var(--crm-radius) !important;
  border: none !important;
  box-shadow: 0 25px 60px rgba(0,0,0,.15), 0 0 0 1px rgba(0,0,0,.04) !important;
  overflow: hidden;
}
.modal-header {
  background: linear-gradient(135deg, #0f172a, #1e293b) !important;
  color: #fff !important;
  border-bottom: none !important;
  padding: 20px 24px !important;
  border-radius: 0 !important;
}
.modal-header .modal-title {
  font-size: 17px;
  font-weight: 700;
  color: #fff;
}
.modal-header .close {
  color: rgba(255,255,255,.6) !important;
  opacity: 1 !important;
  font-size: 22px;
  text-shadow: none !important;
  transition: color .15s;
}
.modal-header .close:hover { color: #fff !important; }
.modal-body {
  padding: 24px !important;
  background: var(--crm-bg);
}
.modal-footer {
  background: var(--crm-surface);
  border-top: 1px solid var(--crm-border) !important;
  padding: 16px 24px !important;
}

/* ─── Form Controls Modern ─── */
.modal .form-control {
  border: 1px solid var(--crm-border);
  border-radius: 8px;
  padding: 8px 14px;
  font-size: 13px;
  color: var(--crm-text);
  transition: border-color .2s, box-shadow .2s;
}
.modal .form-control:focus {
  border-color: var(--crm-accent);
  box-shadow: 0 0 0 3px rgba(99,102,241,.1);
  outline: none;
}
.modal .form-group label,
.modal .input-group-addon {
  font-size: 12px;
  font-weight: 600;
  color: var(--crm-text2);
}
.modal .input-group-addon {
  background: #f8fafc;
  border: 1px solid var(--crm-border);
  border-radius: 8px 0 0 8px;
  min-width: 42px;
}

/* ─── Scrollbar ─── */
.modal-body::-webkit-scrollbar { width: 6px; }
.modal-body::-webkit-scrollbar-track { background: transparent; }
.modal-body::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }

/* ─── Order number highlight ─── */
table.dataTable tbody td h2 {
  font-size: 16px;
  font-weight: 800;
  color: var(--crm-accent);
  margin: 0;
}

/* ─── DataTable Fix: evitar columnas sobrepuestas ─── */
.dataTables_scrollHead,
.dataTables_scrollBody { overflow: visible !important; }
table.dataTable {
  width: 100% !important;
  table-layout: auto !important;
}
table.dataTable th,
table.dataTable td {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 220px;
}
.dataTables_wrapper {
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}
table.dataTable thead .sorting,
table.dataTable thead .sorting_asc,
table.dataTable thead .sorting_desc {
  background-image: none !important;
  position: relative;
}
table.dataTable thead .sorting_asc::after { content: ' ▲'; font-size: 8px; color: var(--crm-accent); }
table.dataTable thead .sorting_desc::after { content: ' ▼'; font-size: 8px; color: var(--crm-accent); }
table.dataTable thead .sorting::after { content: ' ⇅'; font-size: 8px; color: #cbd5e1; }
</style>

<?php
// Helper global: mapear estado a clase de badge
function _ordGetBadgeClass($estadoText) {
    $e = trim(mb_strtolower((string)$estadoText, 'UTF-8'));
    // Autorización / Pendiente — ANTES de entregado (evitar match con "ent" en "pendiente")
    if (strpos($e, 'autorización') !== false || strpos($e, 'autorizacion') !== false || $e === 'aut') return 'badge-pendiente-aut';
    if (strpos($e, 'pendiente') !== false) return 'badge-pendiente-aut';
    // Supervisión
    if (strpos($e, 'supervisión') !== false || strpos($e, 'supervision') !== false || $e === 'sup') return 'badge-supervision';
    // Garantías (orden: aceptada > probable > genérica)
    if (strpos($e, 'garantía aceptada') !== false || strpos($e, 'garantia aceptada') !== false || $e === 'ga') return 'badge-garantia-acep';
    if (strpos($e, 'probable garantía') !== false || strpos($e, 'probable garantia') !== false) return 'badge-prob-garantia';
    if (strpos($e, 'garantía') !== false || strpos($e, 'garantia') !== false) return 'badge-garantia';
    // Revisión
    if (strpos($e, 'revisión') !== false || strpos($e, 'revision') !== false || $e === 'rev') return 'badge-revision';
    // Terminada
    if (strpos($e, 'terminada') !== false || $e === 'ter') return 'badge-terminado';
    // Entregados (específicos primero)
    if (strpos($e, 'entregado al asesor') !== false) return 'badge-entreg-asesor';
    if (strpos($e, 'entregado/pagado') !== false) return 'badge-entreg-pagado';
    if (strpos($e, 'entregado/credito') !== false || strpos($e, 'entregado/crédito') !== false) return 'badge-entreg-credito';
    if (strpos($e, 'entregado') !== false || strpos($e, 'entregada') !== false) return 'badge-entregado';
    // Aceptado
    if (strpos($e, 'aceptado') !== false || strpos($e, 'aceptada') !== false || $e === 'ok') return 'badge-aceptado';
    // Cancelada
    if (strpos($e, 'cancel') !== false) return 'badge-cancelada';
    // Sin reparación
    if (strpos($e, 'sin reparación') !== false || strpos($e, 'sin reparacion') !== false || $e === 'sr') return 'badge-sin-reparacion';
    // Producto para venta
    if (strpos($e, 'producto para venta') !== false || $e === 'pv') return 'badge-producto-venta';
    // Producto en almacén
    if (strpos($e, 'producto en almac') !== false) return 'badge-prod-almacen';
    // Seguimiento de venta
    if (strpos($e, 'seguimiento') !== false) return 'badge-seguimiento';
    return 'badge-otro';
}
?>

<script>
  $(document).ready(function () {
    $(".ocultarimagen").hide();

    /* ═══ Auto-generación de título ═══ */
    function _egsStrip(s) {
      return s.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    }
    function _egsDateStamp() {
      var d = new Date();
      var dd = ('0'+d.getDate()).slice(-2);
      var mm = ('0'+(d.getMonth()+1)).slice(-2);
      var aa = String(d.getFullYear()).slice(-2);
      return dd + mm + aa;
    }
    function _egsBuildTitle() {
      var cSel = $('.cliente option:selected');
      var cTxt = (cSel.val() && cSel.val() !== '' &&
                  cSel.text().trim() !== 'Seleccionar cliente')
                 ? cSel.text().trim() : '';
      var m  = ($('#marca').val()  || '').toUpperCase();
      var mo = ($('#modelo').val() || '').toUpperCase();
      var sr = ($('#numeroserial').val() || '').toUpperCase();
      var cClean = _egsStrip(cTxt).toUpperCase().replace(/[^A-Z0-9 ]/g,'').trim();
      var fecha = _egsDateStamp();
      var parts = [cClean, m, mo, sr, fecha].filter(function(p){ return p!==''; });
      var title = parts.join(' ');
      $('.tituloOrden').val(title);
      $('.rutaOrden').val(title.length > 0 ? limpiarUrl(title) : '');
      if (title.length > 0) {
        $('#egs_tituloPreview').html('<i class="fa-solid fa-check-circle" style="color:#22c55e;margin-right:4px"></i>' + title).css('color','#1e293b');
      } else {
        $('#egs_tituloPreview').html('<i class="fa-solid fa-circle-info" style="color:#94a3b8;margin-right:4px"></i>Se genera al completar Cliente + Equipo').css('color','#94a3b8');
      }
      if (cClean && m && mo && sr) { $('.tituloOrden').trigger('change'); }
    }

    /* ═══ Eventos de cambio ═══ */
    $('.cliente').on('change', function(){ _egsBuildTitle(); _egsValidate(); });
    $('#marca').on('keyup', function(){ $(this).val($(this).val().toUpperCase()); _egsBuildTitle(); _egsValidate(); });
    $('#modelo').on('keyup', function(){ $(this).val($(this).val().toUpperCase()); _egsBuildTitle(); _egsValidate(); });
    $('#numeroserial').on('keyup', function(){ $(this).val($(this).val().toUpperCase()); _egsBuildTitle(); _egsValidate(); });
    $('#textareaDetallesInternos').on('keyup', function(){ $(this).val($(this).val().toUpperCase()); });

    /* ═══ Validación + habilitar botón guardar ═══ */
    $("#btncompletarorden").hide();
    $("#spanboton").html("Complete los campos obligatorios");

    function _egsValidate() {
      var mOk  = $('#marca').val().length >= 2;
      var moOk = $('#modelo').val().length >= 4;
      var srOk = $('#numeroserial').val().length === 6;
      var clOk = $('.cliente').val() && $('.cliente').val() !== '' &&
                 $('.cliente option:selected').text().trim() !== 'Seleccionar cliente';
      /* Visual feedback */
      $('#marca').css('border-color', !mOk && $('#marca').val().length>0 ? '#ef4444' : (mOk ? '#22c55e':''));
      $('#modelo').css('border-color', !moOk && $('#modelo').val().length>0 ? '#ef4444' : (moOk ? '#22c55e':''));
      $('#numeroserial').css('border-color', !srOk && $('#numeroserial').val().length>0 ? '#ef4444' : (srOk ? '#22c55e':''));
      /* Mensajes */
      $('#spanmarca').html(!mOk && $('#marca').val().length>0 ? 'Mín. 2 caracteres':'');
      $('#spanmodelo').html(!moOk && $('#modelo').val().length>0 ? 'Mín. 4 caracteres':'');
      $('#spannumeroserie').html(!srOk && $('#numeroserial').val().length>0 ? 'Exactamente 6 dígitos':'');
      /* Botón */
      if (mOk && moOk && srOk && clOk) {
        $('#btncompletarorden').show(); $('#spanboton').html('');
      } else {
        $('#btncompletarorden').hide();
        if (!clOk) $('#spanboton').html('Seleccione un cliente');
        else if (!mOk) $('#spanboton').html('Complete la marca (mín. 2 caracteres)');
        else if (!moOk) $('#spanboton').html('Complete el modelo (mín. 4 caracteres)');
        else $('#spanboton').html('Complete el N° de serie (6 dígitos)');
      }
    }
    $('#datatableordenes').DataTable({
      "order": [[ 0, "desc" ]],
      "scrollX": true,
      "language": {
        "lengthMenu": "Mostrando _MENU_ registros por hoja",
        "zeroRecords": "No se encontraron registros",
        "info": "Mostrando la pagina _PAGE_ de _PAGES_",
        "infoEmpty": "No hay registros disponibles",
        "infoFiltered": "(Se encontraron  _MAX_ registros en total)",
        "search": "Buscar: ",
        "paginate": {
          "previous": "Anterior",
          "next": "Siguiente"
        }
      }
    });
  });
</script>
<div class="content-wrapper">



  <section class="content-header">
    <h1>
      <i class="fa-solid fa-clipboard-list" style="margin-right:10px;opacity:.7"></i>Gestor de Órdenes
      <small>Órdenes de Servicio Activas</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="index.php?ruta=inicio"><i class="fa-solid fa-gauge" style="margin-right:4px"></i> Inicio</a></li>
      <li class="active">Órdenes de Servicio</li>
    </ol>
  </section>



  <section class="content">



    <div class="box">



      <div class="box-header with-border">



        <?php



        //include "inicio/grafico-ventas.php";
        


        ?>



      </div>



      <div class="box-body">



        <div class="box-tools">
          <!-- Toolbar: Agregar + Última Entrega -->
          <div id="ultimaenentrega" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
            <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarOrden">
              <i class="fa-solid fa-plus" style="margin-right:6px"></i> Agregar Orden
            </button>
            <?php
              $UltimaEntregada = controladorOrdenes::ctrUltimaEntrega();
              foreach ($UltimaEntregada as $key => $ultima) {
                echo '<div style="display:flex;align-items:center;gap:10px;background:#fff;border:1px solid #bfdbfe;border-radius:8px;padding:8px 16px">';
                echo '<span style="font-size:12px;font-weight:700;color:#1e40af"><i class="fa-solid fa-truck-fast" style="margin-right:5px"></i> ÚLTIMA ENTREGA</span>';
                echo '<span style="font-size:13px;font-weight:800;color:#0f172a">ORDEN: ' . $ultima["id"] . '</span>';
                if ($_SESSION["perfil"] == "administrador") {
                  echo '<a href="extensiones/tcpdf/pdf/ticketOrden.php/?idOrden=' . $ultima["id"] . '&empresa=' . $ultima["id_empresa"] . '&asesor=' . $ultima["id_Asesor"] . '&cliente=' . $ultima["id_usuario"] . '&tecnico=' . $ultima["id_tecnico"] . '" class="btn btn-success btn-sm" target="_blank" style="padding:4px 10px;font-size:11px"><i class="fa-solid fa-ticket" style="margin-right:4px"></i> Ticket</a>';
                }
                echo '</div>';
              }
            ?>
          </div>




            <br>



            <!-- <div class="form-1-2">



            <label for="buscadroOrdenesLbael">Buscar:</label>

            <input type="text"  class="caja_Busqueda">



          </div>-->


            <table id="datatableordenes"
              class="table stripe ordenes order-table display compact cell-border hover row-border">



              <thead>



                <tr>



                  <th>No. Orden</th>

                  <th>Técnico</th>

                  <th>Asesor</th>

                  <th>Cliente</th>

                  <th>
                    <?php if ($_SESSION["perfil"] == "tecnico") {
                      echo 'Imagen';
                    } else {
                      echo 'TOTAL';
                    } ?>

                  </th>

                  <th>Estado</th>

                  <th>Fecha Entrada</th>

                  <th>Ultima modificación</th>

                  <th>Fecha de Salida</th>

                  <th>Editar</th>

                  <?php

                  if ($_SESSION["perfil"] == "administrador") {



                    echo '<th>Eliminar</th>';



                  }



                  ?>

                  <th>Imprimir Ticket</th>



                </tr>



              </thead>



              <tbody>



                <?php

                //INICIA LA PAGINACION//
                


                //TRAEMOS LA BASE CORRESPONDIENTE A CADA PAGINA
                
                $base = (1 - 1) * 150;

                $tope = 12;



                //$ordenesConTope=controladorOrdenes::ctrlTraerOrdenesConTope($base,$tope);
                


                //var_dump($ordenesConTope);
                


                if ($_SESSION["perfil"] == "Super-Administrador") {



                  $item = null;

                  $valor = null;



                  $ordenes = controladorOrdenes::ctrMostrarordenesParaValidar($item, $valor);



                } else if ($_SESSION["perfil"] == "administrador") {



                  $item = "id_empresa";

                  $valor = $_SESSION["empresa"];



                  $ordenes = controladorOrdenes::ctrMostrarOrdenes($item, $valor);



                } else if ($_SESSION["perfil"] == "secretaria") {



                  $item = "id_empresa";

                  $valor = $_SESSION["empresa"];



                  $ordenes = controladorOrdenes::ctrMostrarOrdenes($item, $valor);



                }



                foreach ($ordenes as $key => $valueOrdenes) {

                  //TRAER EMPRESA
                


                  $item = "id";

                  $valor = $valueOrdenes["id_empresa"];



                  $respuesta = ControladorVentas::ctrMostrarEmpresasParaTiketimp($item, $valor);



                  $NombreEmpresa = $respuesta["empresa"];



                  //TRAER TECNICO
                
                  $item = "id";

                  $valor = $valueOrdenes["id_tecnico"];



                  $tecnico = ControladorTecnicos::ctrMostrarTecnicos($item, $valor);



                  $NombreTecnico = $tecnico["nombre"];



                  //TRAER ASESOR
                


                  $item = "id";

                  $valor = $valueOrdenes["id_Asesor"];



                  $asesor = Controladorasesores::ctrMostrarAsesoresEleg($item, $valor);



                  $NombreAsesor = $asesor["nombre"];



                  //TRAER CLIENTE (USUARIO)
                


                  $item = "id";

                  $valor = $valueOrdenes["id_usuario"];



                  $usuario = ControladorClientes::ctrMostrarClientesOrdenes($item, $valor);



                  $NombreUsuario = $usuario["nombre"];


                  /*LINK DE IMPRESION DE EDITAR ORDEN https://backend.comercializadoraegs.com/index.php?ruta=infoOrden&idOrden=5240&empresa=1&asesor=9&cliente=2726&tecnico=4&pedido=0*/

                  $InfoOrdenes = "<a href='index.php?ruta=infoOrden&idOrden=" . $valueOrdenes["id"] . "&empresa=" . $valueOrdenes["id_empresa"] . "&asesor=" . $valueOrdenes["id_Asesor"] . "&cliente=" . $valueOrdenes["id_usuario"] . "&tecnico=" . $valueOrdenes["id_tecnico"] . "&tecnicodos=" . $valueOrdenes["id_tecnicoDos"] . "&pedido=" . $valueOrdenes["id_pedido"] . "' class='btn btn-warning' target='_blank'><i class='fas fa-eye' ></i></a>";



                  $eliminarOrden = "<button class='btn btn-danger btnEliminarorden' idOrden='" . $valueOrdenes["id"] . "'><i class='fas fa-times'></i></button>";

                  /*LINK DE IMPRESION DE TICKET: extensiones/tcpdf/pdf/ticketOrden.php/?idOrden=5242&empresa=1&asesor=9&cliente=2727&tecnico=2*/

                  $ticket = "<a href='extensiones/tcpdf/pdf/ticketOrden.php/?idOrden=" . $valueOrdenes["id"] . "&empresa=" . $valueOrdenes["id_empresa"] . "&asesor=" . $valueOrdenes["id_Asesor"] . "&cliente=" . $valueOrdenes["id_usuario"] . "&tecnico=" . $valueOrdenes["id_tecnico"] . "' class='btn btn-warning btnImprimirorden' idOrden='" . $valueOrdenes["id"] . "' cliente='" . $valueOrdenes["id_usuario"] . "'  tecnico='" . $valueOrdenes["id_tecnico"] . "' asesor='" . $valueOrdenes["id_Asesor"] . "' empresa='" . $valueOrdenes["id_empresa"] . "' data-toggle='modal' target='_blank'><i class='fas fa-ticket-alt' ></i></a>";



                  $pedido = "<button class='btn btn-info' data-toggle='modal' data-target='#modalAsignarPedido'><i class='fas fa-sort'></i></button>";



                  date_default_timezone_set("America/Mexico_City");



                  $fechaDeIngreso = $valueOrdenes["fecha_ingreso"];



                  $fecha = date("Y-m-d H:i:s", strtotime($fechaDeIngreso . "+ 5 days"));



                  if ($valueOrdenes["fecha_ingreso"] >= $fecha) {



                    echo '<tr class="atraso">

                      


                      <td data-order="' . (int)$valueOrdenes["id"] . '"><span style="font-weight:800;color:#6366f1">#' . $valueOrdenes["id"] . '</span></td>

                      <td>' . $NombreTecnico . '</td>

                      <td>' . $NombreAsesor . '</td>

                      <td>' . $NombreUsuario . '</td>

                      <td>$ ' . number_format($valueOrdenes["total"], 2) . '</td>

                      <td>';
                    $estadoText = (string) $valueOrdenes["estado"];
                    $estadoClass = _ordGetBadgeClass($estadoText);
                    echo '<span class="badge ' . $estadoClass . '">' . htmlspecialchars($estadoText) . '</span>';
                    echo '</td>

                      <td>' . $valueOrdenes["fecha_ingreso"] . '</td>

                      <td>' . $valueOrdenes["fecha"] . '</td>

                      <td>' . $valueOrdenes["fecha_Salida"] . '</td>

                      <td>' . $InfoOrdenes . '</td>';



                    if ($_SESSION["perfil"] == "administrador") {



                      echo ' <td>' . $eliminarOrden . '</td>';

                    }



                    echo '<td>' . $ticket . '</td>



                  </tr>';

                  } else {



                    echo '<tr>

                      


                      <td data-order="' . (int)$valueOrdenes["id"] . '"><span style="font-weight:800;color:#6366f1">#' . $valueOrdenes["id"] . '</span></td>

                      <td>' . $NombreTecnico . '</td>

                      <td>' . $NombreAsesor . '</td>

                      <td>' . $NombreUsuario . '</td>

                      <td>$ ' . number_format($valueOrdenes["total"], 2) . '</td>

                      <td>';
                    $estadoText = (string) $valueOrdenes["estado"];
                    $estadoClass = _ordGetBadgeClass($estadoText);
                    echo '<span class="badge ' . $estadoClass . '">' . htmlspecialchars($estadoText) . '</span>';
                    echo '</td>

                      <td>' . $valueOrdenes["fecha_ingreso"] . '</td>

                      <td>' . $valueOrdenes["fecha"] . '</td>

                      <td>' . $valueOrdenes["fecha_Salida"] . '</td>

                      <td>' . $InfoOrdenes . '</td>';



                    if ($_SESSION["perfil"] == "administrador") {



                      echo ' <td>' . $eliminarOrden . '</td>';

                    }



                    echo '<td>' . $ticket . '</td>



                  </tr>';

                  }





                }





                if ($_SESSION["perfil"] == "vendedor") {





                  //TRAER ORDENES CON ATRASO 
                
                  //$item = "correo";
                
                  //$valor =  $_SESSION["email"];
                


                  //$Asesores = Controladorasesores::ctrMostrarAsesoresEleg($item,$valor);
                


                  //$id_Asesor = $Asesores["id"];
                


                  //echo'<pre>'.$id_Asesor.'</pre>';
                
                  $item = "id_empresa";

                  $valor = $_SESSION["empresa"];

                  $ordenes = controladorOrdenes::ctrMostrarOrdenes($item, $valor);



                  foreach ($ordenes as $key => $valueOrdenes) {

                    //TRAER EMPRESA
                


                    $item = "id";

                    $valor = $valueOrdenes["id_empresa"];



                    $respuesta = ControladorVentas::ctrMostrarEmpresasParaTiketimp($item, $valor);



                    $NombreEmpresa = $respuesta["empresa"];



                    //TRAER TECNICO
                
                    $item = "id";

                    $valor = $valueOrdenes["id_tecnico"];



                    $tecnico = ControladorTecnicos::ctrMostrarTecnicos($item, $valor);



                    $NombreTecnico = $tecnico["nombre"];



                    //TRAER ASESOR
                


                    $item = "id";

                    $valor = $valueOrdenes["id_Asesor"];



                    $asesor = Controladorasesores::ctrMostrarAsesoresEleg($item, $valor);



                    $NombreAsesor = $asesor["nombre"];



                    //TRAER CLIENTE (USUARIO)
                


                    $item = "id";

                    $valor = $valueOrdenes["id_usuario"];



                    $usuario = ControladorClientes::ctrMostrarClientesOrdenes($item, $valor);



                    $NombreUsuario = $usuario["nombre"];





                    /*LINK DE IMPRESION DE EDITAR ORDEN https://backend.comercializadoraegs.com/index.php?ruta=infoOrden&idOrden=5240&empresa=1&asesor=9&cliente=2726&tecnico=4&pedido=0*/

                    $InfoOrdenes = "<a href='index.php?ruta=infoOrden&idOrden=" . $valueOrdenes["id"] . "&empresa=" . $valueOrdenes["id_empresa"] . "&asesor=" . $valueOrdenes["id_Asesor"] . "&cliente=" . $valueOrdenes["id_usuario"] . "&tecnico=" . $valueOrdenes["id_tecnico"] . "&tecnicodos=" . $valueOrdenes["id_tecnicoDos"] . "&pedido=" . $valueOrdenes["id_pedido"] . "' class='btn btn-warning' data-toggle='modal'><i class='fas fa-eye'></i></a>";



                    $eliminarOrden = "<button class='btn btn-danger btnEliminarorden' idOrden='" . $valueOrdenes["id"] . "'><i class='fas fa-times'></i></button>";



                    /*LINK DE IMPRESION DE TICKET: extensiones/tcpdf/pdf/ticketOrden.php/?idOrden=5242&empresa=1&asesor=9&cliente=2727&tecnico=2*/

                    $ticket = "<a href='extensiones/tcpdf/pdf/ticketOrden.php/?idOrden=" . $valueOrdenes["id"] . "&empresa=" . $valueOrdenes["id_empresa"] . "&asesor=" . $valueOrdenes["id_Asesor"] . "&cliente=" . $valueOrdenes["id_usuario"] . "&tecnico=" . $valueOrdenes["id_tecnico"] . "' class='btn btn-warning btnImprimirorden' idOrden='" . $valueOrdenes["id"] . "' cliente='" . $valueOrdenes["id_usuario"] . "'  tecnico='" . $valueOrdenes["id_tecnico"] . "' asesor='" . $valueOrdenes["id_Asesor"] . "' empresa='" . $valueOrdenes["id_empresa"] . "' data-toggle='modal'><i class='fas fa-ticket-alt'></i></a>";



                    $pedido = "<button class='btn btn-info' data-toggle='modal' data-target='#modalAsignarPedido'><i class='fas fa-sort'></i></button>";



                    date_default_timezone_set("America/Mexico_City");



                    $fechaDeIngreso = $valueOrdenes["fecha_ingreso"];



                    $fecha = date("Y-m-d H:i:s", strtotime($fechaDeIngreso . "+ 5 days"));



                    if ($valueOrdenes["fecha_ingreso"] >= $fecha) {



                      echo '<tr class="atraso">

                      


                      <td data-order="' . (int)$valueOrdenes["id"] . '"><span style="font-weight:800;color:#6366f1">#' . $valueOrdenes["id"] . '</span></td>

                      <td>' . $NombreTecnico . '</td>

                      <td>' . $NombreAsesor . '</td>

                      <td>' . $NombreUsuario . '</td>

                      <td>$ ' . number_format($valueOrdenes["total"], 2) . '</td>

                      <td>';
                      $estadoText = (string) $valueOrdenes["estado"];
                      $estadoClass = _ordGetBadgeClass($estadoText);

                      echo '<span class="badge ' . $estadoClass . '">' . htmlspecialchars($estadoText) . '</span>';
                      echo '</td>

                      <td>' . $valueOrdenes["fecha_ingreso"] . '</td>

                      <td>' . $valueOrdenes["fecha"] . '</td>

                      <td>' . $valueOrdenes["fecha_Salida"] . '</td>

                      <td>' . $InfoOrdenes . '</td>';



                      if ($_SESSION["perfil"] == "administrador") {



                        echo ' <td>' . $eliminarOrden . '</td>';

                      }



                      echo '<td>' . $ticket . '</td>



                  </tr>';

                    } else {



                      echo '<tr>

                      


                      <td data-order="' . (int)$valueOrdenes["id"] . '"><span style="font-weight:800;color:#6366f1">#' . $valueOrdenes["id"] . '</span></td>

                      <td>' . $NombreTecnico . '</td>

                      <td>' . $NombreAsesor . '</td>

                      <td>' . $NombreUsuario . '</td>

                      <td>$ ' . number_format($valueOrdenes["total"], 2) . '</td>

                      <td><span class="badge ' . _ordGetBadgeClass($valueOrdenes["estado"]) . '">' . htmlspecialchars($valueOrdenes["estado"]) . '</span></td>

                      <td>' . $valueOrdenes["fecha_ingreso"] . '</td>

                      <td>' . $valueOrdenes["fecha"] . '</td>

                      <td>' . $valueOrdenes["fecha_Salida"] . '</td>

                      <td>' . $InfoOrdenes . '</td>';



                      if ($_SESSION["perfil"] == "administrador") {



                        echo ' <td>' . $eliminarOrden . '</td>';

                      }



                      echo '<td>' . $ticket . '</td>



                  </tr>';

                    }





                  }



                }



                if ($_SESSION["perfil"] == "tecnico") {



                  //TRAER ORDENES CON ATRASO 
                
                  $itemUno = "correo";

                  $valorUno = $_SESSION["email"];



                  $tecnicoEnSession = ControladorTecnicos::ctrMostrarTecnicos($itemUno, $valorUno);



                  $id_tecnico = $tecnicoEnSession["id"];



                  //echo'<pre>'.$id_tecnico.'</pre>';
                


                  $ordenesDelTecnico = controladorOrdenes::ctrMostrarOrdenesDelTecncio($id_tecnico);



                  foreach ($ordenesDelTecnico as $key => $valueOrdeneDelTecnico) {



                    //TRAER EMPRESA
                


                    $item = "id";

                    $valor = $valueOrdeneDelTecnico["id_empresa"];



                    $respuesta = ControladorVentas::ctrMostrarEmpresasParaTiketimp($item, $valor);



                    $NombreEmpresa = $respuesta["empresa"];



                    //TRAER TECNICO
                
                    $item = "id";

                    $valor = $valueOrdeneDelTecnico["id_tecnico"];



                    $tecnico = ControladorTecnicos::ctrMostrarTecnicos($item, $valor);



                    $NombreTecnico = $tecnico["nombre"];



                    //TRAER ASESOR
                


                    $item = "id";

                    $valor = $valueOrdeneDelTecnico["id_Asesor"];



                    $asesor = Controladorasesores::ctrMostrarAsesoresEleg($item, $valor);



                    $NombreAsesor = $asesor["nombre"];



                    //TRAER CLIENTE (USUARIO)
                


                    $item = "id";

                    $valor = $valueOrdeneDelTecnico["id_usuario"];



                    $usuario = ControladorClientes::ctrMostrarClientesOrdenes($item, $valor);



                    $NombreUsuario = $usuario["nombre"];





                    $InfoOrdenes = "<a href='index.php?ruta=infoOrden&idOrden=" . $valueOrdeneDelTecnico["id"] . "&empresa=" . $valueOrdeneDelTecnico["id_empresa"] . "&asesor=" . $valueOrdeneDelTecnico["id_Asesor"] . "&cliente=" . $valueOrdeneDelTecnico["id_usuario"] . "&tecnico=" . $valueOrdeneDelTecnico["id_tecnico"] . "&tecnicodos=" . $valueOrdeneDelTecnico["id_tecnicoDos"] . "&pedido=" . $valueOrdeneDelTecnico["id_pedido"] . "' class='btn btn-warning' data-toggle='modal'><i class='fas fa-eye'></i></a>";



                    $eliminarOrden = "<button class='btn btn-danger btnEliminarorden' idOrden='" . $valueOrdeneDelTecnico["id"] . "'><i class='fas fa-times'></i></button>";



                    $ticket = "<button class='btn btn-warning btnImprimirorden' idOrden='" . $valueOrdeneDelTecnico["id"] . "' cliente='" . $valueOrdeneDelTecnico["id_usuario"] . "'  tecnico='" . $valueOrdeneDelTecnico["id_tecnico"] . "' asesor='" . $valueOrdeneDelTecnico["id_Asesor"] . "' empresa='" . $valueOrdeneDelTecnico["id_empresa"] . "' data-toggle='modal'><i class='fas fa-ticket-alt'></i></button>";



                    $pedido = "<button class='btn btn-info' data-toggle='modal' data-target='#modalAsignarPedido'><i class='fas fa-sort'></i></button>";



                    date_default_timezone_set("America/Mexico_City");



                    $fechaDeIngreso = $valueOrdeneDelTecnico["fecha_ingreso"];



                    $fecha = date("Y-m-d H:i:s", strtotime($fechaDeIngreso . "+ 5 days"));



                    if ($valueOrdeneDelTecnico["fecha_ingreso"] >= $fecha) {





                      echo '<tr class="atraso">

                      


                      <td data-order="' . (int)$valueOrdeneDelTecnico["id"] . '"><span style="font-weight:800;color:#6366f1">#' . $valueOrdeneDelTecnico["id"] . '</span></td>

                      <td>' . $NombreTecnico . '</td>

                      <td>' . $NombreAsesor . '</td>

                      <td>' . $NombreUsuario . '</td>

                      <td>';
                      $AlbumDeImagenes = json_decode($valueOrdeneDelTecnico["multimedia"], true);
                      $i = 0;
                      foreach ($AlbumDeImagenes as $key => $valueImagenes) {
                        echo '<img class="img-thumbnail" width="100px" src="' . $valueImagenes["foto"] . '">';
                        if (++$i == 1)
                          break;
                      }
                      echo '</td>
                      <td><span class="badge ' . _ordGetBadgeClass($valueOrdeneDelTecnico["estado"]) . '">' . htmlspecialchars($valueOrdeneDelTecnico["estado"]) . '</span></td>

                      <td>' . $valueOrdeneDelTecnico["fecha_ingreso"] . '</td>

                      <td>' . $valueOrdeneDelTecnico["fecha"] . '</td>

                      <td>' . $valueOrdeneDelTecnico["fecha_Salida"] . '</td>

                      <td>' . $InfoOrdenes . '</td>';



                      if ($_SESSION["perfil"] == "administrador") {



                        echo ' <td>' . $eliminarOrden . '</td>';

                      }



                      echo '<td>' . $ticket . '</td>



                    </tr>';



                    } else {





                      echo '<tr>

                      


                      <td data-order="' . (int)$valueOrdeneDelTecnico["id"] . '"><span style="font-weight:800;color:#6366f1">#' . $valueOrdeneDelTecnico["id"] . '</span></td>

                      <td>' . $NombreTecnico . '</td>

                      <td>' . $NombreAsesor . '</td>

                      <td>' . $NombreUsuario . '</td>

                      <td>';
                      $AlbumDeImagenes = json_decode($valueOrdeneDelTecnico["multimedia"], true);
                      $i = 0;
                      foreach ($AlbumDeImagenes as $key => $valueImagenes) {
                        echo '<img class="img-thumbnail" width="100px" src="' . $valueImagenes["foto"] . '">';
                        if (++$i == 1)
                          break;
                      }
                      echo '</td>

                      <td><span class="badge ' . _ordGetBadgeClass($valueOrdeneDelTecnico["estado"]) . '">' . htmlspecialchars($valueOrdeneDelTecnico["estado"]) . '</span></td>

                      <td>' . $valueOrdeneDelTecnico["fecha_ingreso"] . '</td>

                      <td>' . $valueOrdeneDelTecnico["fecha"] . '</td>

                      <td>' . $valueOrdeneDelTecnico["fecha_Salida"] . '</td>

                      <td>' . $InfoOrdenes . '</td>';



                      if ($_SESSION["perfil"] == "administrador") {



                        echo ' <td>' . $eliminarOrden . '</td>';

                      }



                      echo '<td>' . $ticket . '</td>



                  </tr>';



                    }



                  }

                }

                ?>





              </tbody>





            </table>





          </div>



        </div>



  </section>



</div>



<!--=====================================

MODAL AGREGAR ORDENES

======================================-->

<style>
/* ── Modal Nueva Orden — Estilos ── */
#modalAgregarOrden .egs-section {
  margin: 0 -24px; padding: 14px 24px 4px;
  border-top: 1px solid #f1f5f9;
}
#modalAgregarOrden .egs-section:first-child { border-top: none; }
#modalAgregarOrden .egs-section-title {
  display: flex; align-items: center; gap: 8px;
  font-size: 12px; font-weight: 700; text-transform: uppercase;
  letter-spacing: .5px; color: #6366f1; margin-bottom: 14px;
}
#modalAgregarOrden .egs-section-title i { font-size: 13px; }
#modalAgregarOrden .egs-lbl {
  display: block; font-size: 11px; font-weight: 600;
  color: #64748b; margin-bottom: 4px; letter-spacing: .02em;
}
#modalAgregarOrden .egs-lbl .egs-req { color: #ef4444; margin-left: 2px; }
#modalAgregarOrden .egs-title-bar {
  background: #f8fafc; border: 1px dashed #e2e8f0; border-radius: 8px;
  padding: 10px 14px; margin-bottom: 16px; font-size: 13px;
  font-weight: 600; color: #94a3b8; transition: all .2s;
}
#modalAgregarOrden .egs-field-hint {
  font-size: 10px; color: #ef4444; margin-top: 2px; min-height: 14px;
}
#modalAgregarOrden .form-control:focus {
  border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.1);
}
#modalAgregarOrden .row.egs-row { margin-left: -8px; margin-right: -8px; }
#modalAgregarOrden .row.egs-row > [class*="col-"] { padding-left: 8px; padding-right: 8px; }
</style>

<div id="modalAgregarOrden" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- HEADER -->
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><i class="fa-solid fa-clipboard-list" style="margin-right:8px;opacity:.8"></i>Nueva Orden de Servicio</h4>
      </div>

      <!-- BODY -->
      <div class="modal-body" style="padding:20px 24px">
        <div class="box-body" style="padding:0">

          <!-- Hidden fields -->
          <?php echo '<input type="hidden" value="'.$_SESSION["empresa"].'" class="empresa">'; ?>
          <input type="hidden" class="validarOrden tituloOrden nce" value="">
          <input type="hidden" class="rutaOrden" value="">

          <!-- Título auto-generado (preview) -->
          <div class="egs-title-bar">
            <span style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#94a3b8;display:block;margin-bottom:4px">
              <i class="fa-solid fa-tag" style="margin-right:4px"></i>Título de la orden
            </span>
            <div id="egs_tituloPreview" style="font-size:13px">
              <i class="fa-solid fa-circle-info" style="color:#94a3b8;margin-right:4px"></i>Se genera al completar Cliente + Equipo
            </div>
          </div>

          <!-- SECCION: Asignacion -->
          <div class="egs-section">
            <div class="egs-section-title"><i class="fa-solid fa-users"></i> Asignaci&oacute;n</div>
            <div class="row egs-row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="egs-lbl"><i class="fa-solid fa-user" style="margin-right:4px"></i>Cliente<span class="egs-req">*</span></label>
                  <select class="form-control cliente" style="width:100%" required>
                    <option>Seleccionar cliente</option>
                    <?php
                    $item = "id_empresa"; $valor = $_SESSION["empresa"];
                    $usuario = ControladorClientes::ctrMostrarClientesTabla($item, $valor);
                    $_cli_ordenesMap = []; $_cli_estadoMap = []; $_cli_recogidaMap = [];
                    try { $_cli_ordenesMap = ControladorClientes::ctrContarOrdenesClientesBulk(); } catch(Exception $e) {}
                    try { $_cli_estadoMap = ControladorClientes::ctrContarOrdenesEstadoBulk(); } catch(Exception $e) {}
                    try { $_cli_recogidaMap = ControladorClientes::ctrPromedioRecogidaBulk(); } catch(Exception $e) {}
                    foreach ($usuario as $key => $value) {
                      $cId = intval($value["id"]);
                      $cOrd = isset($_cli_ordenesMap[$cId]) ? $_cli_ordenesMap[$cId] : 0;
                      $cEnt = isset($_cli_estadoMap[$cId]) ? $_cli_estadoMap[$cId]["entregadas"] : 0;
                      $cCan = isset($_cli_estadoMap[$cId]) ? $_cli_estadoMap[$cId]["canceladas"] : 0;
                      $cp = [];
                      if ($cOrd >= 3 && ($cEnt + $cCan) > 0) {
                        $r = $cEnt / ($cEnt + $cCan) * 100;
                        if ($r >= 90) $cp["c"] = ["Excelente","#16a34a","#f0fdf4"];
                        elseif ($r >= 70) $cp["c"] = ["Bueno","#2563eb","#eff6ff"];
                        elseif ($r >= 50) $cp["c"] = ["Regular","#d97706","#fffbeb"];
                        else $cp["c"] = ["Malo","#dc2626","#fef2f2"];
                      }
                      if (isset($_cli_recogidaMap[$cId])) {
                        $d = $_cli_recogidaMap[$cId];
                        $dt = "~".$d." días";
                        if ($d <= 7) $cp["r"] = [$dt,"#16a34a","#f0fdf4"];
                        elseif ($d <= 14) $cp["r"] = [$dt,"#2563eb","#eff6ff"];
                        elseif ($d <= 30) $cp["r"] = [$dt,"#d97706","#fffbeb"];
                        else $cp["r"] = [$dt,"#dc2626","#fef2f2"];
                      }
                      $cpAttr = !empty($cp) ? " data-custom-properties='" . htmlspecialchars(json_encode($cp, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') . "'" : "";
                      echo '<option value="' . $cId . '"' . $cpAttr . '>' . htmlspecialchars($value["nombre"]) . '</option>';
                    }
                    ?>
                  </select>
                  <div class="cliente-badges" style="display:none;gap:6px;flex-wrap:wrap;margin-top:6px"></div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="egs-lbl"><i class="fa-solid fa-screwdriver-wrench" style="margin-right:4px"></i>T&eacute;cnico<span class="egs-req">*</span></label>
                  <select class="form-control form-control-sm tecnico" required>
                    <option value="">Seleccionar t&eacute;cnico</option>
                    <?php
                    $item = "id_empresa"; $valor = $_SESSION["empresa"];
                    $tecnico = ControladorTecnicos::ctrMostrarTecnicosDeEmpresas($item, $valor);
                    foreach ($tecnico as $key => $valueTecnicoActivo) {
                      echo '<option value="' . $valueTecnicoActivo["id"] . '" class="text-uppercase">' . $valueTecnicoActivo["nombre"] . '</option>';
                    }
                    ?>
                  </select>
                </div>
              </div>
            </div>
            <div class="row egs-row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="egs-lbl"><i class="fa-solid fa-user-tie" style="margin-right:4px"></i>Asesor</label>
                  <select class="form-control form-control-sm asesor" required>
                    <option>Seleccionar asesor</option>
                    <?php
                    $itemUno = "id_empresa"; $valorDos = $_SESSION["empresa"];
                    $asesorParaSelect = Controladorasesores::ctrMostrarAsesoresEmpresas($itemUno, $valorDos);
                    foreach ($asesorParaSelect as $key => $valueAsesoresActivos) {
                      echo '<option value="' . $valueAsesoresActivos["id"] . '" idAsesor=' . $valueAsesoresActivos["id"] . ' class="seleccionarElAsesor text-uppercase">' . $valueAsesoresActivos["nombre"] . '</option>';
                    }
                    ?>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="egs-lbl"><i class="fa-solid fa-flag" style="margin-right:4px"></i>Estado<span class="egs-req">*</span></label>
                  <select class="form-control form-control-sm status" required>
                    <?php
                    if ($_SESSION["perfil"] == "tecnico") {
                      echo '<option value="En revisión (REV)">En revisión (REV)</option>
                            <option value="Supervisión (SUP)">Supervisión (SUP)</option>
                            <option class="garantia" value="En revisión probable garantía">En revisión probable garantía</option>';
                    }
                    if ($_SESSION["perfil"] == "editor") {
                      echo '<option value="En revisión (REV)">En revisión (REV)</option>
                            <option value="Aceptado (ok)">Aceptado (ok)</option>
                            <option value="Terminada (ter)">Terminada (ter)</option>
                            <option class="garantia" value="En revisión probable garantía">En revisión probable garantía</option>';
                    }
                    if ($_SESSION["perfil"] == "vendedor") {
                      echo '<option value="En revisión (REV)">En revisión (REV)</option>
                            <option value="Supervisión (SUP)">Supervisión (SUP)</option>
                            <option value="Pendiente de autorización (AUT)">Pendiente de autorización (AUT)</option>
                            <option value="Terminada (ter)">Terminada (ter)</option>
                            <option class="garantia" value="En revisión probable garantía">En revisión probable garantía</option>';
                    }
                    if ($_SESSION["perfil"] == "administrador") {
                      echo '<option value="En revisión (REV)">En revisión (REV)</option>
                            <option value="Supervisión (SUP)">Supervisión (SUP)</option>
                            <option value="Pendiente de autorización (AUT)">Pendiente de autorización (AUT)</option>
                            <option value="Aceptado (ok)">Aceptado (ok)</option>
                            <option value="Terminada (ter)">Terminada (ter)</option>
                            <option value="Cancelada (can)">Cancelada (can)</option>
                            <option value="Entregado (Ent)">Entregado (Ent)</option>
                            <option value="Sin reparación (SR)">Sin reparación (SR)</option>
                            <option class="garantia" value="En revisión probable garantía">En revisión probable garantía</option>
                            <option class="garantia" value="Garantía aceptada (GA)">Garantía aceptada (GA)</option>';
                    }
                    ?>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <!-- SECCION: Equipo -->
          <div class="egs-section">
            <div class="egs-section-title"><i class="fa-solid fa-laptop"></i> Datos del Equipo</div>
            <div class="row egs-row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="egs-lbl"><i class="far fa-copyright" style="margin-right:4px"></i>Marca<span class="egs-req">*</span></label>
                  <input id="marca" class="form-control marcaDelEquipo" type="text" placeholder="Marca del equipo" name="marcaDelEquipo" style="text-transform:uppercase">
                  <div class="egs-field-hint" id="spanmarca"></div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="egs-lbl"><i class="fa-solid fa-microchip" style="margin-right:4px"></i>Modelo<span class="egs-req">*</span></label>
                  <input id="modelo" class="form-control modeloDelEquipo" type="text" placeholder="Modelo del equipo" name="modeloDelEquipo" style="text-transform:uppercase">
                  <div class="egs-field-hint" id="spanmodelo"></div>
                </div>
              </div>
            </div>
            <div class="row egs-row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="egs-lbl"><i class="fa-solid fa-barcode" style="margin-right:4px"></i>N&uacute;mero de Serie<span class="egs-req">*</span> <span style="font-weight:400;color:#94a3b8">(últimos 6 dígitos)</span></label>
                  <input id="numeroserial" class="form-control numeroDeSerieDelEquipo" type="text" placeholder="Numero Serial" name="numeroDeSerieDelEquipo" maxlength="6" style="text-transform:uppercase;letter-spacing:2px;font-weight:700">
                  <div class="egs-field-hint" id="spannumeroserie"></div>
                </div>
              </div>
            </div>
          </div>

          <!-- SECCION: Evidencia -->
          <div class="egs-section">
            <div class="egs-section-title"><i class="fa-solid fa-camera"></i> Evidencia Fotogr&aacute;fica</div>
            <div class="form-group agregarMultimedia">
              <div class="multimediaOrden needsclick dz-clickable">
                <div class="dz-message needsclick cuadro multimedia" style="border:2px dashed #e2e8f0;border-radius:10px;padding:24px;text-align:center;background:#fafbfc;transition:all .2s;cursor:pointer">
                  <i class="fa-solid fa-cloud-arrow-up" style="font-size:32px;color:#94a3b8;display:block;margin-bottom:8px"></i>
                  <span style="font-size:13px;font-weight:600;color:#64748b">Arrastrar o dar clic para subir im&aacute;genes</span>
                  <br><span style="font-size:11px;color:#94a3b8">JPG o PNG &bull; m&aacute;x. 2MB por imagen &bull; hasta 10 fotos</span>
                </div>
              </div>
            </div>

            <!-- Fotos ocultas (portada/principal) -->
            <div class="form-group row ocultarimagen">
              <div class="col col-lg-6">
                <div class="panel"><h3>SUBIR FOTO PORTADA</h3></div>
                <input type="file" class="fotoPortada form-control-file">
                <input type="hidden" class="antiguaFotoPortada">
                <p class="help-block">Tamaño recomendado 1280px * 720px <br> Peso máximo de la foto 2MB</p>
              </div>
              <div class="col col-lg-6">
                <img loading="lazy" src="vistas/img/default/default.png" class="img-thumbnail previsualizarPortada" style="width=200px;">
              </div>
            </div>
            <div class="form-group row ocultarimagen">
              <div class="col col-lg-6">
                <div class="panel"><h3>SUBIR FOTO PRINCIPAL DEL PRODUCTO</h3></div>
                <input type="file" class="fotoPrincipal form-control-file">
                <p class="help-block">Tamaño recomendado 400px * 450px <br> Peso máximo de la foto 2MB</p>
              </div>
              <div class="col col-lg-6">
                <img loading="lazy" src="vistas/img/default/default.png" class="img-thumbnail previsualizarPrincipal" style="width=200px;">
              </div>
            </div>
          </div>

          <!-- SECCION: Detalles Internos -->
          <div class="egs-section">
            <div class="egs-section-title"><i class="fa-solid fa-file-lines"></i> Detalles Internos</div>
            <div class="form-group">
              <label class="egs-lbl"><i class="fa-solid fa-lock" style="margin-right:4px"></i>Observaciones internas <span style="font-weight:400;color:#94a3b8">(no visible para el cliente)</span></label>
              <textarea rows="3" class="form-control form-control-sm descripcionOrden" id="textareaDetallesInternos" placeholder="Ingresar detalles internos" style="text-transform:uppercase"></textarea>
            </div>
          </div>

          <!-- SECCION: Partidas -->
          <div class="egs-section">
            <div class="egs-section-title"><i class="fa-solid fa-receipt"></i> Partidas <span style="font-weight:400;font-size:10px;color:#94a3b8;text-transform:none">(visible en el ticket del cliente)</span></div>
            <div id="campos">
              <!-- Partidas dinámicas se agregan aquí -->
            </div>
            <div style="margin:8px 0 16px">
              <a href="#" onclick="AgregarCampos(); return false;" style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border:1px dashed #cbd5e1;border-radius:8px;color:#6366f1;font-size:12px;font-weight:600;text-decoration:none;transition:all .15s" onmouseover="this.style.borderColor='#6366f1';this.style.background='#f5f3ff'" onmouseout="this.style.borderColor='#cbd5e1';this.style.background=''">
                <i class="fa-solid fa-plus"></i> Agregar Partida
              </a>
            </div>

            <!-- Total -->
            <div class="row egs-row">
              <div class="col-md-6 col-md-offset-6">
                <div style="background:#f8fafc;border-radius:10px;padding:14px 18px;border:1px solid #e2e8f0">
                  <label class="egs-lbl" style="margin-bottom:6px"><i class="fa-solid fa-calculator" style="margin-right:4px"></i>TOTAL</label>
                  <div class="input-group">
                    <span class="input-group-addon" style="background:#6366f1;color:#fff;border-color:#6366f1;font-weight:700">$</span>
                    <input type="number" class="form-control input-lg totalOrden" min="0" value="0" step="any" readonly style="font-size:18px;font-weight:800;color:#1e293b">
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>

      <!-- PIE DEL MODAL -->
      <div class="modal-footer" style="display:flex;align-items:center;justify-content:space-between">
        <div class="preload"></div>
        <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa-solid fa-xmark" style="margin-right:4px"></i>Salir</button>
        <div style="display:flex;align-items:center;gap:12px">
          <span style="color:#ef4444;font-size:12px;font-weight:600" id="spanboton"></span>
          <button type="button" id="btncompletarorden" class="btn btn-primary guardarOrden" style="padding:8px 24px;font-weight:700">
            <i class="fa-solid fa-floppy-disk" style="margin-right:6px"></i>Guardar Orden
          </button>
        </div>
      </div>

      </form>
    </div>
  </div>
</div>

    <!--=====================================

MODAL AGREGAR OBSERVACION

======================================



<div id="modalAgregarDetalle" class="modal fade" role="dialog">

  

  <div class="modal-dialog">



    <div class="modal-content">-->



    <!--=====================================

        CABEZA DEL MODAL

        ======================================



        <div class="modal-header" style="background:#138a1e; color:white">



          <button type="button" class="close" data-dismiss="modal">&times;</button>



          <h4 class="modal-title">Agregar Observacion</h4>



        </div>-->



    <!--=====================================

        CUERPO DEL MODAL

        ======================================



        <div class="modal-body">



            <div class="box-body">



              <div class="form-group">

                

                  <div class="panel">AGREGAR NUEVA OBSERVACION</div>



                     <a href="#" onclick="AgregarObservacion();">

                          <div id="camposObservacion">

                           

                           <input type="button" class="btn btn-primary " value="Agregar observación"/></br></br>

                          <?php



                          // echo'<input type="hidden" class="creador" value="'.$_SESSION["id"].'">
                          
                          //  <input type="hidden" class="idOrdenObservacion">';
                          


                          ?>

                          </a>e

                    </div>

            </div>-->



    <!--=====================================

            OBSERVACION UNO

            ======================================

             <div class="form-group row">

              

               <div class="col-xs-6">



                <div class="input-group">

                  <input class="form-control input-lg fecha" type="text"  readonly>

                  </div>

              </div>



              <div class="col-xs-6">

              

                <div class="input-group">



                <textarea type="text"  rows="3" class="form-control input-lg observacionUno" placeholder="Ingresar detalles internos" readonly></textarea>



                <span class="input-group-addon"><i class="fa fa-edit"></i></span> 



              </div>

            </div>



          </div>

  

          </div>



        </div>-->



    <!--=====================================

        PIE DEL MODAL

        ======================================

  

        <div class="modal-footer">



          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>



          <button type="submit" class="btn btn-primary agregarObservacion">Agregar observación</button>



        </div>





    </div>



  </div>



</div>-->



    <!--=====================================

MODAL EDITAR ORDEN

======================================-->

    <div id="modalEditarOrden" class="modal fade" role="dialog">



      <div class="modal-dialog">



        <div class="modal-content">



          <!--=====================================

        CABEZA DEL MODAL

        ======================================-->

          <div class="modal-header">



            <button type="button" class="close" data-dissmiss="modal">&times;</button>



            <center>
              <h2><b>ORDEN:</b></h2>
              <h2 class="modal-title NumeroDeOrden"></h2>
            </center>



          </div>



          <!--=====================================

        CUERPO DEL MODAL

        ======================================-->

          <div class="modal-body">



            <div class="box-body">

              <!--=====================================

            ENTRADA PARA EL TÍTULO

            ======================================-->

              <div class="form-group">



                <div class="input-group">



                  <span class="input-group-addon"><i class="fas fa-product-hunt"></i></span>



                  <input type="text" class="form-control input-lg validarOrden tituloOrden" readonly>



                  <input type="hidden" class="idOrden">



                </div>



              </div>



              <!--=====================================

            ENTRADA PARA LA RUTA DE LA ORDEN

            ======================================-->

              <div class="form-group">



                <div class="input-group">



                  <span class="input-group-addon"><i class="fas fa-link"></i></span>



                  <input type="text" class="form-control input-lg rutaOrden" readonly>



                </div>



              </div>

              <!--=====================================

            ENTRADA PARA EL TECNICO

            ======================================-->

              <div class="form-group">



                <div class="input-group">



                  <span class="input-group-addon"><i class="fas fa-cogs"></i></span>



                  <select class="form-control input-lg seleccionarTecnico">



                    <option class="optionEditarTecnico"></option>



                    <?php



                    $item = null;

                    $valor = null;



                    $tecnico = ControladorTecnicos::ctrMostrarTecnicos($item, $valor);



                    foreach ($tecnico as $key => $value) {



                      echo '



                        <option value="' . $value["id"] . '">' . $value["nombre"] . '</option>';



                    }



                    ?>



                  </select>



                </div>



              </div>

              <!--=====================================

            ENTRADA PARA EL ASESOR

            ======================================-->

              <div class="form-group">



                <div class="input-group">



                  <span class="input-group-addon"><i class="fas fa-user"></i></span>



                  <select class="form-control input-lg seleccionarAsesor">



                    <option class="optionEditarAsesor"></option>



                    <?php



                    $item = null;

                    $valor = null;



                    $asesor = Controladorasesores::ctrMostrarAsesoresEleg($item, $valor);



                    foreach ($asesor as $key => $value) {



                      echo '



                        <option value="' . $value["id"] . '">' . $value["nombre"] . '</option>';



                    }



                    ?>



                  </select>



                </div>



              </div>

              <!--=====================================

            ENTRADA PARA EL CLIENTE

            ======================================-->

              <div class="form-group">



                <div class="input-group">



                  <span class="input-group-addon"><i class="fas fa-user"></i></span>



                  <select class="form-control input-lg seleccionarCliente" readonly>



                    <option class="optionEditarCliente"></option readonly>



                    <?php



                    $item = null;

                    $valor = null;



                    $usuario = ControladorClientes::ctrMostrarClientesOrdenes($item, $valor);



                    foreach ($usuario as $key => $value) {



                      echo '



                        <option value="' . $value["id"] . '">' . $value["nombre"] . '</option>';



                    }



                    ?>



                  </select>



                </div>



              </div>



              <?php



              if ($_SESSION["perfil"] != "tecnico") {



                echo '<div class="form-group">



                    <div class="input-group">



                      <span class="input-group-addon"><i class="fas fa-envelope"></i></span>



                      <input type="email" class="form-control input-lg correoCliente" readonly>



                    </div>





              </div>

              <div class="form-group">



                    <div class="input-group">



                      <span class="input-group-addon"><i class="fas fa-headphones"></i></span>



                      <input type="tel" class="form-control input-lg numeroCliente" readonly>



                    </div>





              </div>

               <div class="form-group">



                    <div class="input-group">



                      <span class="input-group-addon"><i class="fas fa-headphones"></i></span>



                      <input type="tel" class="form-control input-lg numeroClienteDos" readonly>



                    </div>





              </div>';



              }



              ?>

              <!--=====================================

            ENTRADA PARA EL ESTADO

            ======================================-->

              <div class="form-group">



                <div class="input-group">



                  <span class="input-group-addon"><i class="fas fa-toggle-on"></i></span>



                  <select class="form-control input-lg seleccionarEstatus">



                    <option class="optionEditarEstatus"></option>



                    <?php



                    if ($_SESSION["perfil"] == "tecnico") {



                      echo '<option class="pen" value="En revisión (REV)" style="display:none">En revisión (REV)</option>



                                  <option class="sup" value="Supervisión (SUP)" style="display:none">Supervisión (SUP)</option>



                                  <option class="ter" value="Terminada (ter)" style="display:none">Terminada (ter)</option>
                                  <option class="garantia" value="En revisión probable garantía "> En revisión probable garantía </option>';

                    }



                    if ($_SESSION["perfil"] == "editor") {



                      echo '<option class="pen" value="En revisión (REV)" style="display:none">En revisión (REV)</option>

                                 <option class="aut" value="Pendiente de autorización (AUT)" style="display:none">Pendiente de autorización (AUT)</option>

                                  <option class="ok" value="Aceptado (ok)" style="display:none">Aceptado (ok)</option>

                                  <option class="ter" value="Terminada (ter)" style="display:none">Terminada (ter)</option>

                                  <option class="ent" value="Entregado (Ent)" style="display:none">Entregado (Ent)</option>
                                  <option class="garantia" value="En revisión probable garantía "> En revisión probable garantía </option>';

                    }



                    if ($_SESSION["perfil"] == "vendedor") {

                      echo '<option value="En revisión (REV)">En revisión (REV)</option>

                                  <option value="Aceptado (ok)">Aceptado (ok)</option>

                                  <option value="Terminada (ter)">Terminada (ter)</option>

                                  <option value="Entregado (Ent)">Entregado (Ent)</option>
                                  <option class="garantia" value="En revisión probable garantía "> En revisión probable garantía </option>';

                    }

                    if ($_SESSION["perfil"] == "administrador") {



                      echo '<option class="pen" value="En revisión (REV)">En revisión (REV)</option>

                                  <option class="sup" value="Supervisión (SUP)">Supervisión (SUP)</option>

                                  <option class="aut" value="Pendiente de autorización (AUT)">Pendiente de autorización (AUT)</option>

                                  <option class="ok" value="Aceptado (ok)">Aceptado (ok)</option>

                                  <option class="ter" value="Terminada (ter)">Terminada (ter)</option>

                                  <option class="can" value="Cancelada (can)">Cancelada (can)</option>

                                  <option class="ent" value="Entregado (Ent)">Entregado (Ent)</option>

                                  <option class="garantia" value="En revisión probable garantía "> En revisión probable garantía </option>
                                  <option class="garantia"  value="Garantía aceptada (GA)"> Garantía aceptada (GA)</option>';

                    }



                    ?>



                  </select>



                </div>



              </div>

              <!--=====================================

            AGREGAR MULTIMEDIA

            ======================================-->

              <div class="form-group agregarMultimedia">



                <div class="row previsualizarImgFisico"></div>



                <div class="multimediaFisica needsclick dz-clickable">



                  <div class="dz-message needsclick">



                    Arrastrar o dar click para subir imagenes.



                  </div>



                </div>



              </div>
              <!--=====================================

            CATEGORIA DE EQUIPO

            
            
            <div class="form-group">

              

              <div class="input-group disabled">

              

                <span class="input-group-addon"><i class="fab fa-buromobelexperte"></i></span> 



                <select class="form-control" id="exampleFormControlSelect1">
                  <option>ALL IN ONE</option>
                  <option>PC ESCRITORIO</option>
                  <option>LAPTOP</option>
                  <option>TABLET</option>
                  <option>CELULAR</option>
                  <option>IMPRESORA</option>
                </select>



            </div>
            ======================================-->


              <!--=====================================

            MODELO

            ======================================-->
              <div class="form-group">



                <div class="input-group disabled">



                  <span class="input-group-addon"><i class="fas fa-kaaba"></i></span>



                  <input class="form-control form-control-lg" type="text" placeholder="Modelo del equipo">



                </div>
                <!--=====================================

            NUMERO DE SERIE

            ======================================-->
                <div class="form-group">



                  <div class="input-group disabled">



                    <span class="input-group-addon"><i class="fas fa-barcode"></i></span>



                    <input class="form-control form-control-lg" type="text" placeholder="Numero Serial">



                  </div>



                  <!--=====================================

            DETALLES INTERNOS

            ======================================-->

                  <div class="form-group">



                    <div class="input-group">



                      <span class="input-group-addon"><i class="fas fa-edit"></i></span>



                      <textarea type="text" rows="3" class="form-control input-lg descripcionOrden"
                        placeholder="Ingresar detalles internos"></textarea>



                    </div>





                    <!--=====================================

            AGREGAR FOTO DE PORTADA

            ======================================-->

                    <div class="form-group row">

                      <div class="col col-lg-6">

                        <div class="panel">
                          <h3>SUBIR FOTO PORTADA</h3>
                        </div>



                        <input type="file" class="fotoPortada form-control-file">

                        <input type="hidden" class="antiguaFotoPortada">



                        <p class="help-block">Tamaño recomendado 1280px * 720px <br> Peso máximo de la foto 2MB</p>

                      </div>

                      <div class="col col-lg-6">



                        <img loading="lazy" src="vistas/img/default/default.png"
                          class="img-thumbnail previsualizarPortada" style="width=200px;">

                      </div>



                    </div>



                    <!--=====================================

            AGREGAR FOTO DE MULTIMEDIA

            ======================================-->



                    <div class="form-group row">

                      <div class="col col-lg-6">

                        <div class="panel">
                          <h3>SUBIR FOTO PRINCIPAL DEL PRODUCTO</h3>
                        </div>



                        <input type="file" class="fotoPrincipal form-control-file">



                        <p class="help-block">Tamaño recomendado 400px * 450px <br> Peso máximo de la foto 2MB</p>

                      </div>

                      <div class="col col-lg-6">



                        <img loading="lazy" src="vistas/img/default/default.png"
                          class="img-thumbnail previsualizarPrincipal" style="width=200px;">

                      </div>



                    </div>

                    <!-- ═══ PARTIDAS (visible en ticket del cliente) ═══ -->
                    <div style="margin:16px 0 8px">
                      <span style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#475569">
                        <i class="fa-solid fa-receipt" style="margin-right:4px"></i>Partidas
                      </span>
                      <span style="font-size:10px;color:#94a3b8;font-weight:400"> (visible en el ticket del cliente)</span>
                    </div>

                    <?php
                    $partidaLabels = array(
                      1 => 'Diagnóstico / Servicio principal',
                      2 => 'Refacción o pieza',
                      3 => 'Servicio adicional',
                      4 => 'Refacción o pieza adicional',
                      5 => 'Servicio complementario',
                      6 => 'Componente extra',
                      7 => 'Trabajo adicional',
                      8 => 'Material o insumo',
                      9 => 'Concepto adicional',
                      10 => 'Concepto adicional'
                    );
                    for ($p = 1; $p <= 10; $p++):
                      $ro = ($p === 1) ? ' readonly' : '';
                    ?>
                    <div class="form-group row egs-partida-row" style="margin-bottom:8px;padding:10px 12px;background:#fafbfc;border-radius:8px;border:1px solid #f1f5f9">
                      <div class="col-xs-7 col-md-8" style="padding-right:6px">
                        <label style="font-size:10px;font-weight:600;color:#64748b;margin-bottom:3px;display:block">
                          <i class="fa-solid fa-file-lines" style="margin-right:3px"></i>Partida <?php echo $p; ?>
                          <span style="color:#94a3b8;font-weight:400">(visible en ticket)</span>
                        </label>
                        <textarea maxlength="320" rows="2" class="form-control partida<?php echo $p; ?> partidaUno" placeholder="<?php echo $partidaLabels[$p]; ?>" style="text-transform:uppercase;font-size:13px;resize:vertical"<?php echo $ro; ?>></textarea>
                      </div>
                      <div class="col-xs-5 col-md-4" style="padding-left:6px">
                        <label style="font-size:10px;font-weight:600;color:#64748b;margin-bottom:3px;display:block">
                          <i class="fa-solid fa-dollar-sign" style="margin-right:3px"></i>Precio
                        </label>
                        <div class="input-group">
                          <span class="input-group-addon" style="background:#6366f1;color:#fff;border-color:#6366f1;font-weight:700">$</span>
                          <input class="form-control precio<?php echo $p; ?> precioOrdenEditar" type="number" value="0" min="0" step="any" placeholder="0.00" style="font-weight:700"<?php echo $ro; ?>>
                        </div>
                      </div>
                    </div>
                    <?php endfor; ?>

                    <!-- TOTAL -->
                    <div class="row" style="margin-top:12px">
                      <div class="col-xs-6 col-xs-offset-6 col-md-4 col-md-offset-8">
                        <div style="background:#f8fafc;border-radius:10px;padding:12px 14px;border:1px solid #e2e8f0">
                          <label style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#475569;margin-bottom:4px;display:block">
                            <i class="fa-solid fa-calculator" style="margin-right:4px"></i>Total
                          </label>
                          <div class="input-group">
                            <span class="input-group-addon" style="background:#6366f1;color:#fff;border-color:#6366f1;font-weight:700">$</span>
                            <input type="number" class="form-control input-lg totalOrdenEditar" min="0" value="0" step="any" readonly style="font-size:18px;font-weight:800;color:#1e293b">
                          </div>
                        </div>
                      </div>
                    </div>



                    <!--=====================================

                SELECCIONAR PEDIDO

                ======================================-->

                    <div class="form-group">



                      <select class="form-control input-lg selActivarPedido">



                        <option value="">No tiene pedido</option>



                        <option value="pedido">Activar Pedido</option>



                      </select>



                    </div>

                    <!--=====================================

              DATOS DEL PEDIDO

              ======================================-->

                    <div class="datosPedido" style="display:none">



                      <select class="form-control input-lg seleccionarPedido">



                        <option class="optionEditarPedidos"></option>



                        <?php



                        $item = null;

                        $valor = null;



                        $pedido = ControladorPedidos::ctrMostrarorpedidosParaValidar($item, $valor);



                        foreach ($pedido as $key => $value) {



                          echo '



                          <option value="' . $value["id"] . '">' . $value["id"] . '</option>';



                        }



                        ?>



                      </select>





                    </div>



                  </div>



                </div>





                <!--=====================================

        PIE DEL MODAL

        ======================================-->



                <div class="modal-footer">



                  <div class="preload"></div>



                  <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>



                  <button type="button" class="btn btn-primary guardarCambiosOrden">Guardar cambios</button>



                </div>



              </div>



            </div>



          </div>

        </div>

        <?php



        $eliminarOrden = new controladorOrdenes();

        $eliminarOrden->ctrEliminarOrden();

        ?>