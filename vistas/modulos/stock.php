<?php

//if($_SESSION["perfil"] != "administrador"){

  //echo '<script>

  //window.location = "inicio";

  //</script>';

 // return;

//}

?>
<!--=====================================
MODAL AGREGAR STOCK PRODUCTOS AGOTANDOSE
======================================-->
<div class="content-wrapper">
	
	<section class="content-header">
		
		<h1>
			Gestor de stock Producto agotandose
		</h1>

		<ol class="breadcrumb">

	      <li><a href="inicio"><i class="fas fa-dashboard"></i>Inicio</a></li>

	      <li class="active">Gestor de stock Producto agotandose</li>
      
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
				
				<a href="vistas/modulos/reporte.ventasR.php?reporte=stockAgotarse">

	          		<button class="btn btn-success" style="margin-top:5px">Descargar reporte en Excel</button>

	        	</a>

	        	<a href="crearTicket"><button class='btn btn-warning'><i class="fas fa-ticket"></i></button></a>

	        </div>


	        <br>
	        
	        <table class="table table-bordered table-striped dt-responsive tablaStockApuntoAgotarse" width="100%">
	        
	          <thead>
	            
	            <tr>
	              
	              <th style="width:10px">#</th>
                  <th>Nombre</th>
                  <th>Descripcion</th>
                  <th>Cantidad</th>
                  <th>Acciones</th>
	            </tr>

	          </thead>


	        </table>


	      </div>

	    </div>

	  </section>

</div>

<!--=====================================
TABLA PRODUCTOS CON ALERTA DE AGOTAMIENTO
======================================-->
<div id="ModalAgregarStockAgotandose" class="modal fade" role="dialog">
	
	<div class="modal-dialog">
		
		<div class="modal-content">

			<form role="form" method="POST" enctype="multipart/form-data">
			
		        <!--=====================================
		        CABEZA DEL MODAL
		        ======================================-->
		        <div class="modal-header" style="background: #138a1e; color:white;">
		          
		          <button type="button" class="close" data-dissmiss="modal">&times;</button>

		          <h4 class="modal-title">Agregar Stock</h4>

		        </div>

		        <!--=====================================
	       		CUERPO DEL MODAL
	        	======================================-->

	        	<div class="modal-body">
        		
        			<div class="box-body">


        			
	        			<!--=====================================
		            	ENTRADA PARA NUMERO DE STOCK
		            	======================================-->

						<div class="form-group">
								
							<div class="input-group">
								
								<span class="input-group-addon"><i class="fas fa-cubes"></i></span>

								<input type="number"class="form-control input-lg VerStock" name="StockEditado" required>

								<input type="hidden" class="idProducto" name="idProducto">

							</div>

						</div>
						
						<!--=====================================
		            	PIE DEL MODAL
		            	======================================-->
						
				        <div class="modal-footer">

				          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

				          <button type="submit" class="btn btn-primary">Modificar Stock</button>

				        </div>


					</div>

        		</div>

        		<?php


        			$objeto = new ControladorProductos();

        			$objeto -> ctrEditarStock();

        		?>

        	</form>

		</div>

	</div>

</div>

<!--=====================================
TABLA PRODUCTOS CON ALERTA DE AGOTAMIENTO
======================================-->

<div class="content-wrapper">
	
	<section class="content-header">
		
		<h1>
			Gestor de stock con alerta de agotamiento
		</h1>

		<ol class="breadcrumb">

	      <li><a href="inicio"><i class="fas fa-dashboard"></i>Inicio</a></li>

	      <li class="active">Gestor de stock Gestor de stock con alerta de agotamiento</li>
      
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

	        	<a href="vistas/modulos/reporte.ventasR.php?reporte=stockAlerta">';

	          		<button class="btn btn-success" style="margin-top:5px">Descargar reporte en Excel</button>

	        	</a>

	        </div>


	        <br>
	        
	        <table class="table table-bordered table-striped dt-responsive tablaStockAlerta" width="100%">
	        
	          <thead>
	            
	            <tr>
	              
	              <th style="width:10px">#</th>
                  <th>Nombre</th>
                  <th>Descripcion</th>
                  <th>Cantidad</th>
                  <th>Acciones</th>
	            </tr>

	          </thead>


	        </table>


	      </div>

	    </div>

	  </section>

</div>

<!--=====================================
TABLA PRODUCTOS CON ALERTA DE AGOTAMIENTO
======================================-->
<div id="ModalAgregarNuevoTicket" class="modal fade" role="dialog">
	
	<div class="modal-dialog">
		
		<div class="modal-content">

			<form role="form" method="POST" enctype="multipart/form-data">
			
		        <!--=====================================
		        CABEZA DEL MODAL
		        ======================================-->
		        <div class="modal-header" style="background: #138a1e; color:white;">
		          
		          <button type="button" class="close" data-dissmiss="modal">&times;</button>

		          <h4 class="modal-title">Agregar Stock Con ticket</h4>

		        </div>

		        <!--=====================================
	       		CUERPO DEL MODAL
	        	======================================-->

	        	<div class="modal-body">
        		
        			<div class="box-body">


        			
	        			<!--=====================================
		            	ENTRADA PARA NUMERO DE STOCK
		            	======================================-->
		            	<button type="button" class="btn btn-primary AgregarCamposProductosTicket">Agregar Producto</button>
		            	</br>
		            

						<div class="form-group row nuevoProductoTicket">
						

						</div></br>
						
						<!--=====================================
		            	PIE DEL MODAL
		            	======================================-->
						
				        <div class="modal-footer">

				          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

				          <button type="submit" class="btn btn-primary">Agregar Ticket</button>

				        </div>


					</div>

        		</div>

        		<?php


        				//$objeto = new ControladorProductos();

        				//$objeto -> ctrEditarStock();

        		?>

        	</form>

		</div>

	</div>

</div>