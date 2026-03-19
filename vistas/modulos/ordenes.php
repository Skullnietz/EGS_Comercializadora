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
.badge-pendiente      { color: #92400e; background: #fef3c7; border-color: #fde68a; }
.badge-proceso        { color: #1e40af; background: #dbeafe; border-color: #bfdbfe; }
.badge-completada     { color: #065f46; background: #d1fae5; border-color: #a7f3d0; }
.badge-cancelada      { color: #991b1b; background: #fee2e2; border-color: #fecaca; }
.badge-otro           { color: #374151; background: #f3f4f6; border-color: #e5e7eb; }
.badge-aceptado       { color: #065f46; background: #d1fae5; border-color: #a7f3d0; }
.badge-pendiente-aut  { color: #92400e; background: #fff7ed; border-color: #fed7aa; }
.badge-producto-venta { color: #0e7490; background: #cffafe; border-color: #a5f3fc; }
.badge-terminado      { color: #0e7490; background: #ecfeff; border-color: #a5f3fc; }
.badge-revision       { color: #c2410c; background: #fff7ed; border-color: #fdba74; }
.badge-supervision    { color: #7e22ce; background: #faf5ff; border-color: #d8b4fe; }
.badge-sin-reparacion { color: #991b1b; background: #fef2f2; border-color: #fecaca; }
.badge-garantia       { color: #b91c1c; background: #fef2f2; border-color: #fca5a5; }
.badge-revision-garantia { color: #92400e; background: #fffbeb; border-color: #fde68a; }
.badge-entregado      { color: #065f46; background: #ecfdf5; border-color: #6ee7b7; }

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
    // Garantías primero (antes de revisión)
    if (strpos($e, 'garant') !== false) {
        return (strpos($e, 'revision') !== false || strpos($e, 'revisión') !== false || strpos($e, 'probable') !== false)
            ? 'badge-revision-garantia' : 'badge-garantia';
    }
    if (strpos($e, 'entregado') !== false || strpos($e, '(ent)') !== false) return 'badge-entregado';
    if (strpos($e, 'terminad') !== false || strpos($e, '(ter)') !== false) return 'badge-terminado';
    if (strpos($e, 'aceptado') !== false || strpos($e, '(ok)') !== false) return 'badge-aceptado';
    if (strpos($e, 'supervi') !== false || strpos($e, '(sup)') !== false) return 'badge-supervision';
    if (strpos($e, 'revisi') !== false || strpos($e, '(rev)') !== false) return 'badge-revision';
    if (strpos($e, 'autoriz') !== false || strpos($e, '(aut') !== false) return 'badge-pendiente-aut';
    if (strpos($e, 'cancel') !== false || strpos($e, 'rechaz') !== false) return 'badge-cancelada';
    if (strpos($e, 'producto para') !== false) return 'badge-producto-venta';
    if (strpos($e, 'sin reparac') !== false) return 'badge-sin-reparacion';
    if (strpos($e, 'pendiente') !== false || strpos($e, 'por asignar') !== false) return 'badge-pendiente';
    if (strpos($e, 'proceso') !== false || strpos($e, 'atendiendo') !== false) return 'badge-proceso';
    if (strpos($e, 'completad') !== false || strpos($e, 'finalizad') !== false || strpos($e, 'cerrad') !== false) return 'badge-completada';
    return 'badge-otro';
}
?>

<script>
  $(document).ready(function () {
    // Ocultar imagenes de la orden
    $(".ocultarimagen").hide();
    //No se admitira numeros ni signos en el titulo
    const $input1 = document.querySelector('.nce');
    const patron = /[a-zA-Z]+/;
    $input1.addEventListener("keydown", event => {
      console.log(event.key);
      if (patron.test(event.key)) {
        $("#nombre").css({ "border": "1px solid #0C0" });
      }
      else {
        if (event.keyCode == 8) { console.log("backspace"); }
        else { event.preventDefault(); }
      }
    });
    //Todo en MAYUS
    $("#btncompletarorden").hide();
    $("#spanboton").html("Complete su ficha tecnica");
    $("#marca").keyup(function () {
      $('#marca').val($(this).val().toUpperCase());
    });
    $("#modelo").keyup(function () {
      $('#modelo').val($(this).val().toUpperCase());
    });
    $("#numeroserial").keyup(function () {
      $('#numeroserial').val($(this).val().toUpperCase());
    });
    $("#textareaDetallesInternos").keyup(function () {
      $('#textareaDetallesInternos').val($(this).val().toUpperCase());
    });
    //No se mostrara el boton de completar si los tecnicos no llenan la ficha tecnica
    $("#marca,#modelo,#numeroserial").keyup(function () {
      if ($("#marca").val().length >= 2) {
        $("#spanmarca").html("");
        $("#spanboton").html("");
        if ($("#modelo").val().length >= 4) {
          $("#spanmodelo").html("");
          $("#spanboton").html("");
          if ($("#numeroserial").val().length == 6) {
            $("#spannumeroserie").html("");
            $("#spanboton").html("");
            $("#btncompletarorden").show();
          } else {
            $("#btncompletarorden").hide();
            $("#spannumeroserie").html("Debe contener los ultimos <b>6</b> digitos");
            $("#spanboton").html("Complete el campo de numero de serie");
          }
        } else {
          $("#btncompletarorden").hide();
          $("#spanboton").html("Complete el campo de modelo");
          $("#spanmodelo").html("Debe contener al menos <b>4</b> digitos");
        }
      } else {
        $("#btncompletarorden").hide();
        $("#spanboton").html("Complete el campo de marca");
        $("#spanmarca").html("Debe contener al menos <b>2</b> digitos");
      }
    });
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

                      


                      <td><strong><h2> ' . $valueOrdenes["id"] . '</h2></strong></td>

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

                      


                      <td><strong><h2>' . $valueOrdenes["id"] . '</h2></strong></td>

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

                      


                      <td><strong><h4>ORDEN: ' . $valueOrdenes["id"] . '</h4></strong></td>

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

                      


                      <td><strong><h4>ORDEN: ' . $valueOrdenes["id"] . '</h4></strong></td>

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

                      


                      <td><strong><h4>ORDEN: ' . $valueOrdeneDelTecnico["id"] . '</h4></strong></td>

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

                      


                      <td><strong><h4>ORDEN: ' . $valueOrdeneDelTecnico["id"] . '</h4></strong></td>

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

<div id="modalAgregarOrden" class="modal fade" role="dialog">



  <div class="modal-dialog modal-lg">



    <div class="modal-content">



      <!-- <form role="form" method="post" enctype="multipart/form-data"> -->



      <!--=====================================

        CABEZA DEL MODAL

        ======================================-->

      <div class="modal-header">



        <button type="button" class="close" data-dismiss="modal">&times;</button>



        <h4 class="modal-title">Agregar Orden</h4>



      </div>



      <!--=====================================

        CUERPO DEL MODAL

        ======================================-->



      <div class="modal-body">



        <div class="box-body">



          <!-- ENTRADA PARA EL NOMBRE DE LA EMPRESA QUE VENDE-->



          <div class="form-group">



            <div class="input-group">





              <?php



              echo '<input  type="hidden" value="' . $_SESSION["empresa"] . '" class="empresa">';



              ?>





            </div>



          </div>

          <!--=====================================

            ENTRADA PARA EL TÍTULO

            ======================================-->



          <div class="form-group">



            <div class="input-group">



              <span class="input-group-addon"><i class='fas fa-edit'></i></span>



              <input type="text" class="form-control form-control-sm  validarOrden tituloOrden nce"
                placeholder="Ingresar título orden" required>



            </div>



          </div>



          <!--=====================================

            ENTRADA PARA LA RUTA DEL PRODUCTO

            ======================================-->



          <div class="form-group">



            <div class="input-group">



              <span class="input-group-addon"><i class="fas fa-link"></i></span>



              <input type="text" class="form-control form-control-sm rutaOrden" placeholder="Ruta url de la orden"
                readonly>



            </div>



          </div>







          <!--=====================================

            ENTRADA PARA AGREGAR MULTIMEDIA

            ======================================-->



          <div class="form-group agregarMultimedia">







            <!--=====================================

                ENTRADA PARA EL TECNICO

                ======================================-->

            <div class="form-group">



              <div class="input-group">



                <span class="input-group-addon"><i class="fas fa-cogs"></i></span>



                <select class="form-control form-control-sm tecnico" required>



                  <option value="">



                    Seleccionar técnico



                  </option>



                  <?php



                  //$tecnico = ControladorAdministradores::ctrMostrarTecnicosActivos();
                  




                  $item = "id_empresa";



                  $valor = $_SESSION["empresa"];







                  $tecnico = ControladorTecnicos::ctrMostrarTecnicosDeEmpresas($item, $valor);





                  foreach ($tecnico as $key => $valueTecnicoActivo) {



                    echo '<option value="' . $valueTecnicoActivo["id"] . '" class="text-uppercase">' . $valueTecnicoActivo["nombre"] . '</option>';

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



                <select class="form-control form-control-sm asesor" required>



                  <option>



                    Seleccionar asesor



                  </option>



                  <?php





                  //$asesorActivo = ControladorAdministradores::ctrMostrarAdministradoresActvisoEnVentas();
                  


                  $itemUno = "id_empresa";



                  $valorDos = $_SESSION["empresa"];



                  $asesorParaSelect = Controladorasesores::ctrMostrarAsesoresEmpresas($itemUno, $valorDos);



                  foreach ($asesorParaSelect as $key => $valueAsesoresActivos) {



                    echo '<option value="' . $valueAsesoresActivos["id"] . '" idAsesor=' . $valueAsesoresActivos["id"] . ' class="seleccionarElAsesor text-uppercase">' . $valueAsesoresActivos["nombre"] . '</option>';



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



                <span class="input-group-addon"><i class="fas fa-user"></i></i></span>



                <select class="form-control  cliente" style="width: 100%; height: 100%" required>



                  <option>



                    Seleccionar cliente



                  </option>



                  <?php



                  $item = "id_empresa";

                  $valor = $_SESSION["empresa"];



                  $usuario = ControladorClientes::ctrMostrarClientesTabla($item, $valor);



                  foreach ($usuario as $key => $value) {



                    echo '<option value="' . $value["id"] . '">' . $value["nombre"] . '</option>';



                  }



                  ?>



                </select>



              </div>



            </div>

            <!--=====================================

                ENTRADA PARA EL STATUS

                ======================================-->

            <div class="form-group">



              <div class="input-group">



                <span class="input-group-addon"><i class="fas fa-toggle-on"></i></span>



                <select class="form-control form-control-sm status" required>







                  <?php



                  if ($_SESSION["perfil"] == "tecnico") {



                    echo '<option value="En revisión (REV)">En revisión (REV)</option>
                                  <option value="Supervisión (SUP)">Supervisión (SUP)</option>
                                  <option class="garantia" value="En revisión probable garantía "> En revisión probable garantía </option>';

                  }



                  if ($_SESSION["perfil"] == "editor") {



                    echo '<option value="En revisión (REV)">En revisión (REV)</option>

                                  <option value="Aceptado (ok)">Aceptado (ok)</option>
                                  

                                  <option value="Terminada (ter)">Terminada (ter)</option>
                                  <option class="garantia" value="En revisión probable garantía "> En revisión probable garantía </option>';

                  }



                  if ($_SESSION["perfil"] == "vendedor") {

                    echo '<option value="En revisión (REV)">En revisión (REV)</option>
                              <option value="Supervisión (SUP)">Supervisión (SUP)</option>
                              <option value="Pendiente de autorización (AUT)">Pendiente de autorización (AUT)</option>
                                    <option value="Terminada (ter)">Terminada (ter)</option>
                                    <option class="garantia" value="En revisión probable garantía "> En revisión probable garantía </option>';

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

                                  <option class="garantia" value="En revisión probable garantía "> En revisión probable garantía </option>
                                  <option class="garantia" value="Garantía aceptada (GA)">Garantía aceptada (GA)</option>';

                  }



                  ?>























                </select>



              </div>



            </div>

            <!--=====================================

              SUBIR MULTIMEDIA DE PRODUCTO FÍSICO

              ======================================-->



            <div class="multimediaOrden needsclick dz-clickable">



              <div class="dz-message needsclick cuadro multimedia">



                <center>Arrastrar o dar click para subir imagenes</center><br>

                <center><span class="glyphicon glyphicon-open" aria-hidden="true" style="font-size:60px;"></span>
                </center>



              </div>



            </div>



          </div>

          <!--=====================================

            MARCA

            ======================================-->
          <div class="form-group">



            <div class="input-group">



              <span class="input-group-addon"><i class="far fa-copyright"></i></span>



              <input id="marca" class="form-control form-control-lg marcaDelEquipo" type="text"
                placeholder="Marca del equipo" name="marcaDelEquipo"><span id="spanmarca" style="color:red;"></span>



            </div>

            <!--=====================================

            MODELO

            ======================================-->
            <div class="form-group">



              <div class="input-group">



                <span class="input-group-addon"><i class="fas fa-kaaba"></i></span>



                <input id="modelo" class="form-control form-control-lg modeloDelEquipo" type="text"
                  placeholder="Modelo del equipo" name="modeloDelEquipo"><span id="spanmodelo"
                  style="color:red;"></span>



              </div>

              <!--=====================================

            NUMERO DE SERIE

            ======================================-->
              <div class="form-group">



                <div class="input-group">



                  <span class="input-group-addon"><i class="fas fa-barcode"></i></span>


                  <input id="numeroserial" class="form-control form-control-lg numeroDeSerieDelEquipo" type="text"
                    placeholder="Numero Serial" name="numeroDeSerieDelEquipo"><span id="spannumeroserie"
                    style="color:red;"></span>



                </div>







                <!--=====================================

            AGREGAR DESCRIPCIÓN

            ======================================-->



                <div class="form-group">



                  <div class="input-group">



                    <span class="input-group-addon"><i class='fas fa-edit'></i></span>



                    <textarea type="text" rows="3" class="form-control form-control-sm descripcionOrden"
                      id="textareaDetallesInternos" placeholder="Ingresar detalles internos"></textarea>



                  </div>



                </div>



                <div class="form-group row">







                  <!--=====================================

            ENTRADA PARA AGREGAR NUEVA PARTIDA

            ======================================-->



                  <div class="form-group row ocultarimagen">


                    <div class="col col-lg-6">

                      <div class="panel">
                        <h3>SUBIR FOTO PORTADA</h3>
                      </div>



                      <input type="file" class="fotoPortada form-control-file">

                      <input type="hidden" class="antiguaFotoPortada">



                      <p class="help-block">Tamaño recomendado 1280px * 720px <br> Peso máximo de la foto 2MB</p>

                    </div>

                    <div class="col col-lg-6">



                      <img loading="lazy" src="vistas/img/cabeceras/default/default.jpg"
                        class="img-thumbnail previsualizarPortada" style="width=200px;">

                    </div>



                  </div>



                  <!--=====================================

            AGREGAR FOTO DE MULTIMEDIA

            ======================================-->



                  <div class="form-group row ocultarimagen">

                    <div class="col col-lg-6">

                      <div class="panel">
                        <h3>SUBIR FOTO PRINCIPAL DEL PRODUCTO</h3>
                      </div>



                      <input type="file" class="fotoPrincipal form-control-file">



                      <p class="help-block">Tamaño recomendado 400px * 450px <br> Peso máximo de la foto 2MB</p>

                    </div>

                    <div class="col col-lg-6">



                      <img loading="lazy" src="vistas/img/productos/default/default.jpg"
                        class="img-thumbnail previsualizarPrincipal" style="width=200px;">

                    </div>



                  </div>





                  <!--=====================================

            PARTIDA UNO

            ======================================

            <div class="form-group row">

              <div class="col-xs-6">

                <div class="input-group"> 

                  <span class="input-group-addon"><i class="fa fa-edit"></i></span>

                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida1 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 



                </div> 

              </div>

            <div>

            <div class="col-xs-6"><div class="input-group">



              <input class="form-control input-lg precio1 preciodeOrdenUno" type="number" value="0"  min="0" step="any" placeholder="Precio">



              <span class="input-group-addon"><i class="fa fa-dollar"></i></span>



            </div>

          </div>

        </div>

        </div>-->

                  <!--=====================================

            PARTIDA DOS

            ======================================

            <div class="form-group row">

              <div class="col-xs-6">

                <div class="input-group"> 

                  <span class="input-group-addon"><i class="fa fa-edit"></i></span>

                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida2 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 



                </div> 

              </div>

            <div>

            <div class="col-xs-6"><div class="input-group">



              <input class="form-control input-lg precio2 preciodeOrdenUno" type="number" value="0"  min="0" step="any" placeholder="Precio">



              <span class="input-group-addon"><i class="fa fa-dollar"></i></span>



            </div>

          </div>

        </div>

        </div>-->

                  <!--=====================================

            PARTIDA TRES

            ======================================



          <div class="form-group row">

              <div class="col-xs-6">

                <div class="input-group"> 

                  <span class="input-group-addon"><i class="fa fa-edit"></i></span>

                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida3 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 



                </div> 

              </div>

            <div>

            <div class="col-xs-6"><div class="input-group">



              <input class="form-control input-lg precio3 preciodeOrdenUno" type="number" value="0"  min="0" step="any" placeholder="Precio">



              <span class="input-group-addon"><i class="fa fa-dollar"></i></span>



            </div>

          </div>

        </div>

        </div>-->

                  <!--=====================================

            PARTIDA CUATRO

            ======================================



          <div class="form-group row">

              <div class="col-xs-6">

                <div class="input-group"> 

                  <span class="input-group-addon"><i class="fa fa-edit"></i></span>

                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida4 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 



                </div> 

              </div>

            <div>

            <div class="col-xs-6"><div class="input-group">



              <input class="form-control input-lg precio4 preciodeOrdenUno" type="number" value="0"  min="0" step="any" placeholder="Precio">



              <span class="input-group-addon"><i class="fa fa-dollar"></i></span>



            </div>

          </div>

        </div>

        </div>-->

                  <!--=====================================

            PARTIDA CINCO

            ======================================



                      <div class="form-group row">

              <div class="col-xs-6">

                <div class="input-group"> 

                  <span class="input-group-addon"><i class="fa fa-edit"></i></span>

                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida5 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 



                </div> 

              </div>

            <div>

            <div class="col-xs-6"><div class="input-group">



              <input class="form-control input-lg precio5 preciodeOrdenUno" type="number" value="0"  min="0" step="any" placeholder="Precio">



              <span class="input-group-addon"><i class="fa fa-dollar"></i></span>



            </div>

          </div>

        </div>

        </div>-->

                  <!--=====================================

            PARTIDA SEIS

            ======================================

                      <div class="form-group row">

              <div class="col-xs-6">

                <div class="input-group"> 

                  <span class="input-group-addon"><i class="fa fa-edit"></i></span>

                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida6 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 



                </div> 

              </div>

            <div>

            <div class="col-xs-6"><div class="input-group">



              <input class="form-control input-lg precio6 preciodeOrdenUno" type="number" value="0"  min="0" step="any" placeholder="Precio">



              <span class="input-group-addon"><i class="fa fa-dollar"></i></span>



            </div>

          </div>

        </div>

        </div>-->

                  <!--=====================================

            PARTIDA SIETE

            ======================================

                      <div class="form-group row">

              <div class="col-xs-6">

                <div class="input-group"> 

                  <span class="input-group-addon"><i class="fa fa-edit"></i></span>

                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida7 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 



                </div> 

              </div>

            <div>

            <div class="col-xs-6"><div class="input-group">



              <input class="form-control input-lg precio7 preciodeOrdenUno" type="number" value="0"  min="0" step="any" placeholder="Precio">



              <span class="input-group-addon"><i class="fa fa-dollar"></i></span>



            </div>

          </div>

        </div>

        </div>-->

                  <!--=====================================

            PARTIDA OCHO

            ======================================

                                  <div class="form-group row">

              <div class="col-xs-6">

                <div class="input-group"> 

                  <span class="input-group-addon"><i class="fa fa-edit"></i></span>

                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida8 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 



                </div> 

              </div>

            <div>

            <div class="col-xs-6"><div class="input-group">



              <input class="form-control input-lg precio8 preciodeOrdenUno" type="number" value="0"  min="0" step="any" placeholder="Precio">



              <span class="input-group-addon"><i class="fa fa-dollar"></i></span>



            </div>

          </div>

        </div>

        </div>-->

                  <!--=====================================

            PARTIDA NUEVE

            ======================================

            <div class="form-group row">

              <div class="col-xs-6">

                <div class="input-group"> 

                  <span class="input-group-addon"><i class="fa fa-edit"></i></span>

                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida9 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 



                </div> 

              </div>

            <div>

            <div class="col-xs-6"><div class="input-group">



              <input class="form-control input-lg precio9 preciodeOrdenUno" type="number" value="0"  min="0" step="any" placeholder="Precio">



              <span class="input-group-addon"><i class="fa fa-dollar"></i></span>



            </div>

          </div>

        </div>

        </div>-->

                  <!--=====================================

            PARTIDA DIEZ

            ======================================

            <div class="form-group row">

              <div class="col-xs-6">

                <div class="input-group"> 

                  <span class="input-group-addon"><i class="fa fa-edit"></i></span>

                    <textarea type="text" maxlength="320" rows="3" class="form-control input-lg partida10 partidaUno" placeholder="Ingresar detalles para cliente (Primera partida)"></textarea> 



                </div> 

              </div>

            <div>

            <div class="col-xs-6"><div class="input-group">



              <input class="form-control input-lg precio10 preciodeOrdenUno" type="number" value="0"  min="0" step="any" placeholder="Precio">



              <span class="input-group-addon"><i class="fa fa-dollar"></i></span>



            </div>

          </div>

        </div>-->

                </div>





              </div>



              <div class="form-group">


                <!--=====================================
            BOTON AGREGAR PARTIDAS
            ======================================-->
                <div class="panel">
                  <h3>AGREGAR PARTIDAS</h3>
                </div>

                <a href="#" onclick="AgregarCampos();">

                  <div id="campos">

                    <input type="button" class="btn btn-primary " value="Agregar Partida" />

                </a>
                <!--<input type="button" class="btn btn-success" id="agregarCaracteristicas" value="Agregar Caracteristicas"/></br></br>-->


              </div>



              <!--=====================================

                PRODUCTO CALCULAR TOTALES

                ======================================-->

              <div class="form-group row">



                <!--=====================================

                CAMBIO A REGRESAR

                ======================================-->



                <div class="col-xs-6">







                </div>



                <div class="col-xs-6">

                  <span>
                    <h5>
                      <center>TOTAL</center>
                    </h5>
                  </span>

                  <div class="input-group">



                    <span class="input-group-addon"><i class="fas fa-hand-holding-usd"></i></span>



                    <input type="number" class="form-control input-lg totalOrden" min="0" value="0" step="any" readonly>



                  </div>



                </div>



              </div>

            </div>



          </div>





          <!--=====================================

        PIE DEL MODAL

        ======================================-->



          <div class="modal-footer">



            <div class="preload"></div>



            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>



            <button type="button" id="btncompletarorden" class="btn btn-primary guardarOrden">Guardar Orden</button>


            <span style="color:red; float:right;" id="spanboton"></span>

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



                        <img loading="lazy" src="vistas/img/cabeceras/default/default.jpg"
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



                        <img loading="lazy" src="vistas/img/productos/default/default.jpg"
                          class="img-thumbnail previsualizarPrincipal" style="width=200px;">

                      </div>



                    </div>

                    <!--=====================================

            PARTIDA UNO

            ======================================-->

                    <div class="form-group row">



                      <div class="col-xs-">



                        <div class="input-group">



                          <span class="input-group-addon"><i class="fas fa-edit"></i></span>



                          <textarea type="text" maxlength="320" rows="3"
                            class="form-control input-lg partida1 partidaUno"
                            placeholder="Ingresar detalles para cliente (Primera partida)" readonly></textarea>



                        </div>



                      </div>



                      <div>



                        <div class="col-xs-6">



                          <div class="input-group">



                            <input class="form-control input-lg precio1 precioOrdenEditar" type="number" value="0"
                              min="0" step="any" placeholder="Precio" readonly>



                            <span class="input-group-addon"><i class="fas fa-dollar-sign"></i></span>



                          </div>



                        </div>



                      </div>



                    </div>



                    <!--=====================================

            PARTIDA DOS

            ======================================-->

                    <div class="form-group row">



                      <div class="col-xs-6">



                        <div class="input-group">



                          <span class="input-group-addon"><i class="fas fa-edit"></i></span>



                          <textarea type="text" maxlength="320" rows="3"
                            class="form-control input-lg partida2 partidaUno"
                            placeholder="Ingresar detalles para cliente (Primera partida)"></textarea>



                        </div>



                      </div>



                      <div>



                        <div class="col-xs-6">



                          <div class="input-group">



                            <input class="form-control input-lg precio2 precioOrdenEditar" type="number" value="0"
                              min="0" step="any" placeholder="Precio">



                            <span class="input-group-addon"><i class="fas fa-dollar-sign"></i></span>



                          </div>



                        </div>



                      </div>



                    </div>

                    <!--=====================================

            PARTIDA TRES

            ======================================-->

                    <div class="form-group row">



                      <div class="col-xs-6">



                        <div class="input-group">



                          <span class="input-group-addon"><i class="fas fa-edit"></i></span>



                          <textarea type="text" maxlength="320" rows="3"
                            class="form-control input-lg partida3 partidaUno"
                            placeholder="Ingresar detalles para cliente (Primera partida)"></textarea>



                        </div>



                      </div>



                      <div>



                        <div class="col-xs-6">



                          <div class="input-group">



                            <input class="form-control input-lg precio3 precioOrdenEditar" type="number" value="0"
                              min="0" step="any" placeholder="Precio">



                            <span class="input-group-addon"><i class="fas fa-dollar-sign"></i></span>



                          </div>



                        </div>



                      </div>



                    </div>

                    <!--=====================================

            PARTIDA CUATRO

            ======================================-->

                    <div class="form-group row">



                      <div class="col-xs-6">



                        <div class="input-group">



                          <span class="input-group-addon"><i class="fas fa-edit"></i></span>



                          <textarea type="text" maxlength="320" rows="3"
                            class="form-control input-lg partida4 partidaUno"
                            placeholder="Ingresar detalles para cliente (Primera partida)"></textarea>



                        </div>



                      </div>



                      <div>



                        <div class="col-xs-6">



                          <div class="input-group">



                            <input class="form-control input-lg precio4 precioOrdenEditar" type="number" value="0"
                              min="0" step="any" placeholder="Precio">



                            <span class="input-group-addon"><i class="fas fa-dollar-sign"></i></span>



                          </div>



                        </div>



                      </div>



                    </div>

                    <!--=====================================

            PARTIDA CINCO

            ======================================-->

                    <div class="form-group row">



                      <div class="col-xs-6">



                        <div class="input-group">



                          <span class="input-group-addon"><i class="fas fa-edit"></i></span>



                          <textarea type="text" maxlength="320" rows="3"
                            class="form-control input-lg partida5 partidaUno"
                            placeholder="Ingresar detalles para cliente (Primera partida)"></textarea>



                        </div>



                      </div>



                      <div>



                        <div class="col-xs-6">



                          <div class="input-group">



                            <input class="form-control input-lg precio5 precioOrdenEditar" type="number" value="0"
                              min="0" step="any" placeholder="Precio">



                            <span class="input-group-addon"><i class="fas fa-dollar-sign"></i></span>



                          </div>



                        </div>



                      </div>



                    </div>



                    <!--=====================================

            PARTIDA SEIS

            ======================================-->

                    <div class="form-group row">



                      <div class="col-xs-6">



                        <div class="input-group">



                          <span class="input-group-addon"><i class="fas fa-edit"></i></span>



                          <textarea type="text" maxlength="320" rows="3"
                            class="form-control input-lg partida6 partidaUno"
                            placeholder="Ingresar detalles para cliente (Primera partida)"></textarea>



                        </div>



                      </div>



                      <div>



                        <div class="col-xs-6">



                          <div class="input-group">



                            <input class="form-control input-lg precio6 precioOrdenEditar" type="number" value="0"
                              min="0" step="any" placeholder="Precio">



                            <span class="input-group-addon"><i class="fas fa-dollar-sign"></i></span>



                          </div>



                        </div>



                      </div>



                    </div>



                    <!--=====================================

            PARTIDA SIETE

            ======================================-->

                    <div class="form-group row">



                      <div class="col-xs-6">



                        <div class="input-group">



                          <span class="input-group-addon"><i class="fas fa-edit"></i></span>



                          <textarea type="text" maxlength="320" rows="3"
                            class="form-control input-lg partida7 partidaUno"
                            placeholder="Ingresar detalles para cliente (Primera partida)"></textarea>



                        </div>



                      </div>



                      <div>



                        <div class="col-xs-6">



                          <div class="input-group">



                            <input class="form-control input-lg precio7 precioOrdenEditar" type="number" value="0"
                              min="0" step="any" placeholder="Precio">



                            <span class="input-group-addon"><i class="fas fa-dollar-sign"></i></span>



                          </div>



                        </div>



                      </div>



                    </div>

                    <!--=====================================

            PARTIDA OCHO

            ======================================-->

                    <div class="form-group row">



                      <div class="col-xs-6">



                        <div class="input-group">



                          <span class="input-group-addon"><i class="fas fa-edit"></i></span>



                          <textarea type="text" maxlength="320" rows="3"
                            class="form-control input-lg partida8 partidaUno"
                            placeholder="Ingresar detalles para cliente (Primera partida)"></textarea>



                        </div>



                      </div>



                      <div>



                        <div class="col-xs-6">



                          <div class="input-group">



                            <input class="form-control input-lg precio8 precioOrdenEditar" type="number" value="0"
                              min="0" step="any" placeholder="Precio">



                            <span class="input-group-addon"><i class="fas fa-dollar-sign"></i></span>



                          </div>



                        </div>



                      </div>



                    </div>

                    <!--=====================================

            PARTIDA NUEVE

            ======================================-->

                    <div class="form-group row">



                      <div class="col-xs-6">



                        <div class="input-group">



                          <span class="input-group-addon"><i class="fas fa-edit"></i></span>



                          <textarea type="text" maxlength="320" rows="3"
                            class="form-control input-lg partida9 partidaUno"
                            placeholder="Ingresar detalles para cliente (Primera partida)"></textarea>



                        </div>



                      </div>



                      <div>



                        <div class="col-xs-6">



                          <div class="input-group">



                            <input class="form-control input-lg precio9 precioOrdenEditar" type="number" value="0"
                              min="0" step="any" placeholder="Precio">



                            <span class="input-group-addon"><i class="fas fa-dollar-sign"></i></span>



                          </div>



                        </div>



                      </div>



                    </div>



                    <!--=====================================

            PARTIDA DIEZ

            ======================================-->

                    <div class="form-group row">



                      <div class="col-xs-6">



                        <div class="input-group">



                          <span class="input-group-addon"><i class="fas fa-edit"></i></span>



                          <textarea type="text" maxlength="320" rows="3"
                            class="form-control input-lg partida10 partidaUno"
                            placeholder="Ingresar detalles para cliente (Primera partida)"></textarea>



                        </div>



                      </div>



                      <div>



                        <div class="col-xs-6">



                          <div class="input-group">



                            <input class="form-control input-lg precio10 precioOrdenEditar" type="number" value="0"
                              min="0" step="any" placeholder="Precio">



                            <span class="input-group-addon"><i class="fas fa-dollar-sign"></i></span>



                          </div>



                        </div>



                      </div>



                    </div>

                    <!--=====================================

            TOTAL ORDEN

            ======================================-->

                    <div class="form-group row">

                      <!--=====================================

            CAMBIO A REGRESAR

            ======================================-->

                      <div class="col-xs-6">





                      </div>



                      <div class="col-xs-6">

                        <span>
                          <h5>
                            <center>TOTAL</center>
                          </h5>
                        </span>

                        <div class="input-group">



                          <span class="input-group-addon"><i class="fas fa-dollar-sign"></i></span>



                          <input type="number" class="form-control input-lg totalOrdenEditar" min="0" value="0"
                            step="any" readonly>



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