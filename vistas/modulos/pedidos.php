<?php

if($_SESSION["perfil"] != "administrador" AND $_SESSION["perfil"]!= "vendedor" AND $_SESSION["perfil"]!= "tecnico" AND $_SESSION["perfil"]!= "Super-Administrador"){

  echo '<script>

  window.location = "inicio";

  </script>';

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

  .tablaPedidos thead th {
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

  .tablaPedidos.dataTable thead .sorting,
  .tablaPedidos.dataTable thead .sorting_asc,
  .tablaPedidos.dataTable thead .sorting_desc {
    background-image: none !important;
    padding-right: 8px !important;
  }

  .tablaPedidos.dataTable tbody tr td {
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

  .tablaPedidos_wrapper .dataTables_length,
  .tablaPedidos_wrapper .dataTables_filter {
    margin-bottom: 12px;
  }

  .tablaPedidos_wrapper .dataTables_length label,
  .tablaPedidos_wrapper .dataTables_filter label {
    color: #475569;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .2px;
  }

  .tablaPedidos_wrapper .dataTables_length select {
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

  .tablaPedidos_wrapper .dataTables_filter input {
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

  .tablaPedidos_wrapper .dataTables_length select:focus,
  .tablaPedidos_wrapper .dataTables_filter input:focus {
    outline: none;
    border-color: #a5b4fc !important;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, .12);
  }

  .tablaPedidos_wrapper .dataTables_paginate ul.pagination > li.paginate_button > a {
    border-radius: 8px !important;
    border: 1px solid #dbe3ef !important;
    background: #ffffff !important;
    color: #334155 !important;
    margin-left: 6px;
    padding: 6px 12px !important;
    font-weight: 600;
    transition: all .15s ease;
  }

  .tablaPedidos_wrapper .dataTables_paginate ul.pagination > li.paginate_button > a:hover {
    background: #eef2ff !important;
    border-color: #a5b4fc !important;
    color: #3730a3 !important;
  }

  .tablaPedidos_wrapper .dataTables_paginate ul.pagination > li.paginate_button.active > a {
    background: #1a3152 !important;
    border-color: #1a3152 !important;
    color: #ffffff !important;
  }

  .tablaPedidos_wrapper .dataTables_paginate ul.pagination > li.paginate_button.disabled > a {
    background: #f8fafc !important;
    border-color: #e2e8f0 !important;
    color: #94a3b8 !important;
    cursor: not-allowed;
  }

  .tablaPedidos_wrapper .dataTables_paginate ul.pagination > li.paginate_button {
    background: transparent !important;
    border: 0 !important;
    box-shadow: none !important;
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

    .tablaPedidos_wrapper .dataTables_length,
    .tablaPedidos_wrapper .dataTables_filter {
      float: none !important;
      text-align: left !important;
      width: 100%;
    }

    .tablaPedidos_wrapper .dataTables_filter input {
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
    padding: 20px !important;
  }

  .modal-header .close {
    color: #fff !important;
    opacity: 0.8;
    text-shadow: none;
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

  .modal-body {
    padding: 24px;
    background: #f8fafc;
  }

  .modal-footer {
    background: #fff;
    border-top: 1px solid #e2e8f0;
    padding: 16px;
  }

  .modal-body .form-group {
    margin-bottom: 16px;
  }

  .modal-body .input-group {
    border-radius: 8px;
    overflow: hidden;
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
      <div class="ped-section-icon"><i class="fa-solid fa-shopping-cart"></i></div>
      <div>
        <h3>Administrador de Pedidos</h3>
        <p>Organiza, monitorea y gestiona todos tus pedidos en una única vista mejorada.</p>
      </div>
    </div>

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
              <table class="table stripe ordenes order-table display compact cell-border hover row-border tablaPedidos" width="100%">
              
                <thead>
                  
                  <tr>
                    
                    <th style="width:40px">#</th>
                    <th>Empresa</th>
                    <th>No. Pedido</th>
                    <th>Cliente</th>
                    <th>Estado</th>
                    <th>Total del pedido</th>
                    <th>Método de pago</th>
                    <th>Registro</th>
                    <th>Modificación</th>
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

  </div>

</div>
<!--=====================================
MODAL EDITAR PEDIDO
======================================-->
<div id="modalEditarPedido" class="modal fade" role="dialog">
  
    <div class="modal-dialog modal-lg">
      
      <div class="modal-content">
        
        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->
        <div class="modal-header" style="background:#138a1e; color:white">
          
          <button type="button" class="close" data-dismiss>&times;</button>

          <center><h2><b>PEDIDO:</b></h2><h2 class="modal-title NumeroDePedido"></h2></center>

        </div>
        <!--=====================================
        CUERPO DEL MODAL
        ======================================--> 
        <input type="hidden" class="idPedido">



        <!--=====================================
        ENTRADA PARA EL ASESOR
        ======================================-->
        <div class="form-group">
          
          <div class="input-group">
            
            <span class="input-group-addon"><i class="fas fa-user"></i></span>

            <input type="tex" class="form-control input-lg asesorDePedido" readonly>

          </div>

        </div>
         <!--=====================================
        INFORMACIÓN DEL CLIENTE
        ======================================-->
        <div class="form-group">
          
          <div class="input-group">
            
            <span class="input-group-addon"><i class="fas fa-user"></i></span>

            <input type="tex" class="form-control input-lg clienteNombre" readonly>
            <input type="tex" class="form-control input-lg clienteNumero" readonly>
            <input type="tex" class="form-control input-lg clienteOrden" readonly>

          </div>

        </div>

        <!--=====================================
        ENTRADA PARA INFORMACION DEL PEDIDO UNO
        ======================================-->
        <div class="form-group row productoUnoEdicionMostrar" style="display: none;">
          
          <div class="col-xs-6">
            <span><h5><center>Producto</center></h5></span>
            <div class="input-group">
              
              <span class="input-group-addon"><i class="fas fa-product-hunt"></i></span>
              <input type="text" class="form-control input-lg edicionProductoUnoPedido">

            </div>

          </div>
          <div class="col-xs-6">
            <span><h5><center>Precio</center></h5></span>
            <div class="input-group">

              <input type="number" class="form-control input-lg precioProductoPedidoEdicion" readonly>


              <span class="input-group-addon"><i class="fas fa-dollar"></i></span>
              
            </div>

          </div>

        </div>  


        <!--=====================================
        ENTRADA PARA CANTIDAD DE CANTIDA DE PRODUCTOS PEDIDOS UNO
        ======================================-->
        <div class="form-group cantidadProductosUnoPedidoEditados" style="display: none;">
          <span><h5><center>Cantidad</center></h5></span>
          <div class="input-group">
            
            <span class="input-group-addon"><i class="fas fa-arrow-circle-up"></i></span>
            <input type="number" class="form-control input-lg cantidadDeProductoPedidoEditado" readonly>  

          </div>

        </div>
        <!--=====================================
        ENTRADA PARA INFORMACION DEL PEDIDO DOS 
        ======================================-->
        <div class="form-group row productoDosEdicionMostrar" style="display: none;">
          
          <div class="col-xs-6">
            <span><h5><center>Producto</center></h5></span>
            <div class="input-group">
              
              <span class="input-group-addon"><i class="fas fa-product-hunt"></i></span>
              <input type="text" class="form-control input-lg edicionProductoUnoPedidoDos">

            </div>

          </div>
          <div class="col-xs-6">
            <span><h5><center>Precio</center></h5></span>
            <div class="input-group">

              <input type="number" class="form-control input-lg precioProductoPedidoEdicionDos" readonly>


              <span class="input-group-addon"><i class="fas fa-dollar"></i></span>
              
            </div>

          </div>

        </div>  

        <!--=====================================
        ENTRADA PARA CANTIDAD DE CANTIDA DE PRODUCTOS PEDIDOS DOS
        ======================================-->
        <div class="form-group cantidadProductosDosPedidoEditados" style="display: none;">
          <span><h5><center>Cantidad</center></h5></span>
          <div class="input-group">
            
            <span class="input-group-addon"><i class="fas fa-arrow-circle-up"></i></span>
            <input type="number" class="form-control input-lg cantidadDeProductoPedidoEditadoDos" readonly>  

          </div>

        </div>
        <!--=====================================
        ENTRADA PARA INFORMACION DEL PEDIDO TRES
        ======================================-->
        <div class="form-group row productoTresEdicionMostrar" style="display: none;">
          
          <div class="col-xs-6">
            <span><h5><center>Producto</center></h5></span>
            <div class="input-group">
              
              <span class="input-group-addon"><i class="fas fa-product-hunt"></i></span>
              <input type="text" class="form-control input-lg edicionProductoUnoPedidoTres">

            </div>

          </div>
          <div class="col-xs-6">
            <span><h5><center>Precio</center></h5></span>
            <div class="input-group">

              <input type="number" class="form-control input-lg precioProductoPedidoEdicionTres" readonly>


              <span class="input-group-addon"><i class="fas fa-dollar"></i></span>
              
            </div>

          </div>

        </div>  

        <!--=====================================
        ENTRADA PARA CANTIDAD DE CANTIDA DE PRODUCTOS PEDIDOS TRES
        ======================================-->
        <div class="form-group cantidadProductosTresPedidoEditados" style="display: none;">
          <span><h5><center>Cantidad</center></h5></span>
          <div class="input-group">
            
            <span class="input-group-addon"><i class="fas fa-arrow-circle-up"></i></span>
            <input type="number" class="form-control input-lg cantidadDeProductoPedidoEditadoTres" readonly>  

          </div>

        </div>
        <!--=====================================
        ENTRADA PARA INFORMACION DEL PEDIDO CUATRO
        ======================================-->
        <div class="form-group row productoCuatroEdicionMostrar" style="display: none;">
          
          <div class="col-xs-6">
            <span><h5><center>Producto</center></h5></span>
            <div class="input-group">
              
              <span class="input-group-addon"><i class="fas fa-product-hunt"></i></span>
              <input type="text" class="form-control input-lg edicionProductoUnoPedidoCuatro">

            </div>

          </div>
          <div class="col-xs-6">
            <span><h5><center>Precio</center></h5></span>
            <div class="input-group">

              <input type="number" class="form-control input-lg precioProductoPedidoEdicionCuatro" readonly>


              <span class="input-group-addon"><i class="fas fa-dollar"></i></span>
              
            </div>

          </div>

        </div>  

        <!--=====================================
        ENTRADA PARA CANTIDAD DE CANTIDA DE PRODUCTOS PEDIDOS CUATRO
        ======================================-->
        <div class="form-group cantidadProductosCuatroPedidoEditados" style="display: none;">
          <span><h5><center>Cantidad</center></h5></span>
          <div class="input-group">
            
            <span class="input-group-addon"><i class="fas fa-arrow-circle-up"></i></span>
            <input type="number" class="form-control input-lg cantidadDeProductoPedidoEditadoCuatro" readonly>  

          </div>

        </div>
        <!--=====================================
        ENTRADA PARA INFORMACION DEL PEDIDO CINCO
        ======================================-->
        <div class="form-group row productoCincoEdicionMostrar" style="display: none;">
          
          <div class="col-xs-6">
            <span><h5><center>Producto</center></h5></span>
            <div class="input-group">
              
              <span class="input-group-addon"><i class="fas fa-product-hunt"></i></span>
              <input type="text" class="form-control input-lg edicionProductoUnoPedidoCinco">

            </div>

          </div>
          <div class="col-xs-6">
            <span><h5><center>Precio</center></h5></span>
            <div class="input-group">

              <input type="number" class="form-control input-lg precioProductoPedidoEdicionCinco" readonly>


              <span class="input-group-addon"><i class="fas fa-dollar"></i></span>
              
            </div>

          </div>

        </div>  

        <!--=====================================
        ENTRADA PARA CANTIDAD DE CANTIDAD DE PRODUCTOS PEDIDOS CINCO
        ======================================-->
        <div class="form-group cantidadProductosCincoPedidoEditados" style="display: none;">
          <span><h5><center>Cantidad</center></h5></span>
          <div class="input-group">
            
            <span class="input-group-addon"><i class="fas fa-arrow-circle-up"></i></span>
            <input type="number" class="form-control input-lg cantidadDeProductoPedidoEditadoCinco" readonly>  

          </div>

        </div>
        <!--=====================================
        ENTRADA PARA PAGOS
        ======================================-->
        <div class="form-group row">
          
          <div class="col-xs-6">
            
            <span><h5><center>Pago del cliente</center></h5></span>

            <div class="input-group"> 
              
              <span class="input-group-addon"><i class="fas fa-hand-holding-usd"></i></span>
              <input type="number" class="form-control input-lg pagoClientePedido" value="0" min="0" step="any" readonly>

            </div>

          </div>
          <div class="col-xs-6">
            
            <span><h5><center>TOTAL</center></h5></span>

            <div class="input-group">
              
                <span class="input-group-addon"><i class="fas fa-money"></i></span>
                <input type="number" class="form-control input-lg pagoPedidoEdidato" readonly>

            </div>

          </div>

        </div>



        <!--=====================================
        Adeudo
        ======================================-->
        <div class="form-group row">
          
          <div class="col-xs-6">
            
            <span><h5><center>Adeudo</center></h5></span>

            <div class="input-group">
              
              <span class="input-group-addon"><i class="fas fa-exchange"></i></span>
              
              <input type="number" class="form-control input-lg adeudoPedidoEditado" min="0" value="0" step="any" readonly>  

            </div>  

          </div>


        </div>
            <!--=====================================
            ENTRADA PARA LOS ESTATUS
            ======================================-->  

              <div class="form-group">
                
                <div class="input-group">

                  <span class="input-group-addon"><i class="fas fa-toggle-on"></i></i></span>
                  
                  <select class="form-control input-lg EstadoDelPedido">

                    <option class="optionEstadoPedido">
                      

                    </option>
                    
                    <option value="Pedido Pendiente">

                      Pedido Pendiente

                    </option>

                      <option value="Pedido Adquirido">

                        Pedido Adquirido        

                      </option> 

                      <option value="Producto en Almacen">

                        Producto en Almacén        

                      </option> 
  
                      <option value="Entregado al asesor">

                        Entregado al Asesor

                      </option>

                      <option value="Entregado/Pagado">

                        Entregado/Pagado

                      </option>

                      <option value="Entregado/Credito">

                        Entregado/Crédito

                      </option>


                  </select>

                </div>

              </div>

            <!--=====================================
            BOTON PARA PODER AGREGAR ABONOS DE MANERA AUTOMATICA
            ======================================-->  
            
             <div class="panel">AGREGAR NUEVO ABONO</div>

                 <a href="#" onclick="AgregarCampoDeaAbonoEditado();">
                  
                  <div id="camposAbono">
                
                    <input type="button" class="btn btn-primary " value="Agregar Abono"/></br></br>

                </a>

          </div>
        <!--=====================================
        CAMPOS DE ABONOS
        ======================================-->
        <div class="form-group row">
          
          <div class="col-xs-6">
            
            <span><h5><center>Abono</center></h5></span>

            <div class="input-group">
              
              <span class="input-group-addon"><i class="fas fa-money"></i></span>
              <input class="form-control input-lg abono1Lectura" type="text" readonly>

            </div>

          </div>
          <div class="col-xs-6">
            
            <span><h5><center>Fecha</center></h5></span>

            <div class="input-group">

              <span class="input-group-addon"><i class="fas fa-clock"></i></span>
              <input type="date" class="form-control input-lg fechaAbono1Lectura" min="0" value="0" step="any" readonly>

            </div>

          </div>

        </div>

        <!--=====================================
        Fecha de entrega
        ======================================-->
        <div class="form-group">

          <span><h5><center>Fecha de Entrega</center></h5></span>
          
          <div class="input-group">
            
            <span class="input-group-addon"><i class="fas fa-dollar"></i></span>

            <input type="date" class="form-control input-lg fechaEntregaPedidoEditado" readonly>

          </div>

        </div>

        <!--=====================================
        PIE DEL MODAL
        ======================================-->
        <div class="modal-footer">
          
          <button type="button" class="btn btn-light" data-dismiss="modal" style="border: 1px solid #dbe3ef; color: #334155; font-weight: 600;">Cancelar</button>
          <button type="submit" class="btn btn-primary botonGuardarPedido" style="background: #6366f1; border: none; color: white; font-weight: 600; padding: 8px 20px; border-radius: 8px;">Guardar Pedido</button>

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

  <form role="form" method="post" class="formularioPedidosDinamicos">
  
    <div class="modal-dialog modal-lg">
      
      <div class="modal-content">
        
        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->
        <div class="modal-header" style="background:#138a1e; color:white">
          
          <button type="button" class="close" data-dismiss="modal">&times;</button>

          <h4>Agregar Pedido</h4>

        </div>

        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->
        <div class="modal-body">
          
          <div class="box-body">

          <!--=====================================
           ENTRADA PARA LA EMPRESA
          ======================================-->
          <div class="form-group">
            
            <div class="input-group">
              
                             
                  <?php
                
                    echo'<input  type="hidden" value="'.$_SESSION["empresa"].'" name="empresaPedioDinamico">';

                  ?>

                
            </div>

          </div>
          <!--=====================================
           ENTRADA EL ASESOR
          ======================================-->
          <div class="form-group">
            
            <div class="input-group">
              
              <span class="input-group-addon"><i class="fas fa-user"></i></span>

              <select class="form-control input-lg asesorPedidoDinamico" name="asesorPedidoDinamico">

                <option>
                  Seleccionar Asesor
                </option>

                <?php

                      $item = "id_empresa";
                      $valor = $_SESSION["empresa"];


                          $asesor = Controladorasesores::ctrMostrarAsesoresEmpresas($item,$valor);
                              foreach ($asesor as $key => $value) {
                                
                                echo '<option value="'.$value["id"].'">'.$value["nombre"].'</option>';

                              }                         

                        ?>
                 </select>
                      
            </div>

          </div>
          <!--=====================================
           ENTRADA PARA EL CLIENTE
          ======================================-->
          <div class="fomr-group">
            
            <div class="input-group">
              
              <span class="input-group-addon"><i class="fas fa-user"></i></span>

              <select class="form-control input-lg clientePedidoDinamico" name="clientePedidoDinamico">
                
                <option>
                  Seleccionar Cliente
                </option>
                <?php

                  $item = "id_empresa";
                  $valor = $_SESSION["empresa"];

                  $usuario = ControladorClientes::ctrMostrarClientesTabla($item,$valor);


                            foreach ($usuario as $key => $value) {
                              
                                echo '<option value="'.$value["id"].'">'.$value["nombre"].'</option>';

                              }

                          ?>
                

              </select>

            </div>

          </div>
          </br>
          <!--=====================================
           ENTRADA PARA LOS PRODUCTOS
          ======================================-->
          <div class="AgregarProductos">
            
             <input type="button" class="btn btn-primary " value="Agregar producto"></br></br>

          </div>

             <div class="NuevoProductoPedido">
             
             </div>

             <div class="form-group row">
               
              <div class="col-md-3 col-xs-12">

                

                <div class="input-group">
                  
                  <span class="input-group-addon"><i class="fas fa-hand-holding-usd"></i> | Pago</span>
                  <input type="number" class="form-control input-lg PagoClientePedidoDinamico">

                </div>
                 
               </div>

               <div class="col-md-3 col-xs-12">

                  

                <div class="input-group">
                  
                  <span class="input-group-addon"><i class="fas fa-hand-holding-usd"></i> | Restar</span>
                  <input type="number" class="form-control input-lg cambioClientePedidoDinamico" readonly>

                </div>
                 
               </div>

               <div class="col-md-6 col-xs-12">

                

                <div class="input-group">
                  
                  <span class="input-group-addon"><i class="fas fa-table"></i> | Fecha de pago</span>
                  <input type="date" class="form-control input-lg fechaPagoVentaModal" style="width:60%;">

                  <input type="hidden" class="PrimerPagolistado" name="PrimerPagolistado">
                  <input type="hidden" class="PrimerAdeudo" name="PrimerAdeudo">

                </div>
                 
               </div>               

             </div>
              


              <div class="form-group">
                
                <div class="input-group">
                  
                  <span class="input-group-addon"><i class="fas fa-hand-holding-usd"></i></span>
                  <input type="number" class="form-control input-lg TotalPedidoEnOrden monto totales" name="TotalPedidoEnOrden" readonly>

                </div>

              </div>
              <!--=====================================
              ENTRADA PAR EL ESTADO
              ======================================-->
              <div class=form-group>
                
                <div class="input-group">
                  
                  <span class="input-group-addon"><i class="fas fa-toggle-on"></i></span>

                  <select class="form-control input-lg estadoPedidoDinamico" name="EstadoPedidoDinamico">
                    
                    <option class="Pedido Pendiente">
                    Pedido Pendiente  
                    </option>

                    <option value="Pedido Adquirido">
                      Pedido Adquirido 
                    </option>
                    
                    <option class="Entregado al asesor">
                      Entregado al Asesor
                    </option>

                    <option value="Entregado/Pagado">
                      Entregado/Pagado
                    </option>

                    <option value="Entregado/Credito">
                      Entregado/Crédito
                    </option>

                  </select>

                </div>

              </div>

              <!--ASIGNAR ORDEN -->
              <div class="form-group">

                <div class="input-group">
                  
                  <select class="form-control input-lg seleccionarOrdenPedidoDinamico" name="seleccionarOrdenPedidoDinamico">
                  
                    <option>ASIGNAR ORDEN</option>

                    <?php

                       $item = "id_empresa";
                        $valor = $_SESSION["empresa"];

                        $pedido = controladorOrdenes::ctrMostrarOrdenes($item,$valor);

                        foreach ($pedido as $key => $valueOrdenes) {
                          
                          echo '

                          <option value="'.$valueOrdenes["id"].'">'.$valueOrdenes["id"].'</option>';

                        }

                    ?>

                  </select>

                </div> 
                
                <input type="hidden" id="ProductosPedidoListados" name="ProductosPedidoListados">

              </div>


          </div>
          <!--=====================================
           PIE DEL MODAL
          ======================================-->

        <div class="modal-footer">

          <button type="button" class="btn btn-light" data-dismiss="modal" style="border: 1px solid #dbe3ef; color: #334155; font-weight: 600;">Cancelar</button>

          <button type="submit" class="btn btn-primary guardarPedidoEditado" style="background: #6366f1; border: none; color: white; font-weight: 600; padding: 8px 20px; border-radius: 8px;">Guardar Cambios</button>

        </div>


        </div>

      </div>

    </div>

    <?php

         $crearPedido = new controladorOrdenes();
         $crearPedido -> ctrAgregarPedidoEnOrden();

        ?>

  </form>

</div>

<script>
    /*=============================================
CARGAR LA TABLA DINÁMICA DE PEDIDOS
=============================================*/
$.ajax({
  url:"ajax/tablapedidos.ajax.php?perfil="+$("#tipoDePerfil").val()+"&empresa="+$("#id_empresa").val(),
 	success:function(respuesta){
		console.log("Tabla de pedidos cargada");
 	}
 })

$(".tablaPedidos").DataTable({
  "ajax": "ajax/tablapedidos.ajax.php?perfil="+$("#tipoDePerfil").val()+"&empresa="+$("#id_empresa").val(),
	 "deferRender": true,
	 "retrieve": true,
	 "processing": true,
	 "pageLength": 25,
	 "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
	 "autoWidth": false,
	 "order": [[0, 'desc']],
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
campo = '<div class="form-group row"><div class="col-xs-6"><div class="input-group"><span class="input-group-addon"><i class="fa fa-product-hunt"></i></span><input class="form-control input-lg Producto'+nextinput+'" type="text" placeholder="Nombre Del Producto"></div></div><div class="col-xs-6"><div class="input-group"><input class="form-control input-lg precioProducto precioProducto'+nextinputPrecio+'" type="number" value="0"  placeholder="Precio" id="precioUno"><span class="input-group-addon"><i class="fa fa-dollar"></i></span></div></div></div><div class="form-group"><div class="input-group"><span class="input-group-addon"><i class="fa fa-arrow-circle-up"></i></span><input type="number" class="form-control input-lg cantidadProducto'+nextCantidad+'" placeholder="cantidad"></div></div>';
$("#camposProductos").append(campo);
}

/*=============================================
REALIZAR OPERACIONES PRODUCTO UNO
=============================================*/
$(document).on("change", function() {


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
AGREGAR CAMPOS PAGO PEDIDO DINAMICO
=============================================*/
$('.agregarCamposPagoPedido').click(function() {

  $(".agregarCamposPago").append(

      '<div class="input-group">'+
        '<span class="input-group-addon"><i class="fa fa-money"></i></span>'+
        '<input type="number" class="form-control input-sm pagoAbonado">'+
      '</div>'+
      '</br></br>'
  
  )
  listarPrimerPago()
  listarObservacionesPedidos()
  listarNuevosPreciosDePedido()
  

});

$('.agregarCamposPagoPedido').click(function() {

  $(".nuevoCampoPagoPedido").append(

    '<div class="input-group">'+
      '<span class="input-group-addon"><i class="fa fa-table"></i></span>'+
      '<input type="date" class="form-control input-sm fechaAbono">'+
    '</div>'+
    '</br></br>'                   
  )
  listarProductosPedidoDinamico()
  listarNuevosPreciosDePedido()
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
SELECTOR DE CLIENTE
=============================================*/
$(document).ready(function(){
      
  $('.seleccionarOrdenPedidoDinamico').select2();
  $('.clientePedidoDinamico').select2();

  
        
});

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



var valor_sesion = $('.usuarioQueCaptura').val();



$("#fechaVista").attr("fecha", fecha);


$('.AgregarCampoDeObservacionPedidos').click(function() {


  $( ".cajaObervacionesPedidos" ).show();

  $(".agregarcampoobervacionesPedidos").append(



    '<div class="form-group">'+



      '<div class="input-group">'+



        '<span class="input-group-addon"><button type="button" class="btn btn-danger quitarObservacion" btn-lg><i class="fa fa-times"></i></button></span>'+

          

          '<textarea type="text"  class="form-control input-lg nuevaObservacion" fecha="'+fecha+'" style="text-alinging:right; font-weight: bold;"></textarea>'+

          

          '<input type="hidden" class="usuarioQueCaptura" value="'+valor_sesion+'" name="usuarioQueCaptura">'+

                

      '</div>'+



      '</div>')

  listarObservacionesPedidos()
  listarNuevosPreciosDePedido()
  


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

  var creador = $(".usuarioQueCaptura");

  for (var i =0; i < descripcion.length; i++){

    listarnuvasObservacionesPedidos.push({"observacion" : $(descripcion[i]).val(), 

                     "creador" : $(creador[i]).val(),

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

</script>