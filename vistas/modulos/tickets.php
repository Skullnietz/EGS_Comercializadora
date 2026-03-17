<?php

if($_SESSION["perfil"] != "administrador"){

  echo '<script>

  window.location = "inicio";

  </script>';

  return;

}

?>

<div class="content-wrapper">
	
	<section class="content-header">
		
		<h1>
			Gestor Órdenes De Servicio
		</h1>
		
		<ol class="breadcrumb">

	      <li><a href="inicio"><i class="fas fa-dashboard"></i>Inicio</a></li>

	      <li class="active">Gestor de Tickets/facturas</li>
      
    	</ol>

	</section>

	<section class="content">
		
		<div class="box">
			
			<div class="box-body">

				<div class="box-tools">

					<button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarOrden">
	          
	          			Agregar ticket/factura

	        		</button>

				</div>
				
			</div>



			<table  class="table table-bordered table-striped dt-responsive order-table" width="100%">
				

				<thead>
	            
	            <tr>
	              
	              <th style="width:10px">#</th>
                  <th>Empresa</th>
	              <th>Código</th>
	              <th>Total</th>
	              <th>Fecha</th>
	              <th>Acciones</th>
	                              
	            </tr>

	          </thead>


	          <tbody>
	          	
  				<?php
  				$item=null;
    			$valor=null; 
              
    			$tickets = ControladorTickets::ctrMostrarTickets($item, $valor);


    			foreach ($tickets as $key => $value) {
    				
    				//TRAER EMPRESA

                  $item = "id";
                  $valor = $value["id_empresa"];

                  $respuesta = ControladorVentas::ctrMostrarEmpresasParaTiketimp($item,$valor);

                  $NombreEmpresa = $respuesta["empresa"];

                  $acciones = "<div class='btn-group'><button class='btn btn-warning btnVerInfoTikcet' idTicket='".$value["id"]."'><i class='fas fa-pencil'></i></button><button class='btn btn-danger btnEliminarTicket' idTicket='".$value["id"]."'><i class='fas fa-times'></i></button></div>";

                  echo '<tr>
                      
                      <td>'.($key+1).'</td>
                      <td>'.$NombreEmpresa.'</td>
                      <td>'.$value["codigo"].'</td>
                      <td><strong>$'.$value["total"].'</strong></td>
                      <td>'.$value["fecha"].'</td>
                      <td>'.$acciones.'</td>
                     ';          


    			}


  				?>

	          </tbody> 

			</table>

		</div>

	</section>

</div>