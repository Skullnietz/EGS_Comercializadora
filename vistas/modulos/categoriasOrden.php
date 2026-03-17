<div class="content-wrapper">

	<section class="content-header">
      
    <h1>
      Gestor categorías de la orden
    </h1>
 
    <ol class="breadcrumb">

      <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>

      <li class="active">Gestor categorías de la orden</li>
      
    </ol>

  </section>

  <section class="content">
  	
  	<div class="box">

  		<div class="box-header with-border">
         
          <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarCategoriaOrden">

            Agregar categoría de orden

          </button>

      	</div>

      	<div class="box-body">
         
        <table class="table table-bordered table-striped dt-responsive tablaCategoriasOrden" width="100%">

          <thead>
            
            <tr>
              
              <th style="width:10px">#</th>
              <th>Area</th>
              <th>Producto</th>
              <th>Marca</th>
              <th>Modelo</th>
              <th>Descripción</th>
              <th>Fecha</th>

            </tr>

          </thead>

          <tbody>

          	<?php

          		$item = "id_empresa";
          		$valor = $_SESSION["empresa"];

          		$respuesta = ControladorCategoriasdeOrden::ctrlMostrarCategoriasdeorden($item,$valor);

          		foreach ($respuesta as $key => $value){


                echo ' <tr>
                          <td>'.($key+1).'</td>
                          <td>'.$value["id_area"].'</td>
                          <td>'.$value["Producto"].'</td>
                          <td>'.$value["Marca"].'</td>
                          <td>'.$value["Modelo"].'</td>
                          <td>'.$value["Descripcion"].'</td>
                          <td>'.$value["Fecha"].'</td>

                      </tr>';            
             }

          	?>

          	<tr>
          		
          		<td></td>

          	</tr>

              
          </tbody>


        </table> 

      </div>

  	</div>

  </section>


</div>



<!--=====================================
MODAL AGREGAR PERFIL
======================================-->

<div id="modalAgregarCategoriaOrden" class="modal fade" role="dialog">
  
  	<div class="modal-dialog">

	    <div class="modal-content">

	      <form role="form" method="post" enctype="multipart/form-data">

		        <!--=====================================
		        CABEZA DEL MODAL
		        ======================================-->

		        <div class="modal-header" style="background:#138a1e; color:white">

		          <button type="button" class="close" data-dismiss="modal">&times;</button>

		          <h4 class="modal-title">Agregar Técnico</h4>

		        </div>

		        <!--=====================================
		        CUERPO DEL MODAL
		        ======================================-->

		        <div class="modal-body">

		          <div class="box-body">

			            <!-- ENTRADA PARA EL NOMBRE -->
			            
			            <div class="form-group">
			              
			              <div class="input-group">
			              
			                <select class="form-control input-lg tecnico" name="areratecnico">
                    
			                    <option>Seleccionar área</option>

			                    <option value="electronica">Electrónica</option>
			                    <option value="impresoras">Impresoras</option>
			                    <option value="sistemas">Sistemas</option>

		                  	</select>

			              </div>

			            </div>


				        <!--=====================================
				        PIE DEL MODAL
				        ======================================-->

				        <div class="modal-footer">

				          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

				          <button type="submit" class="btn btn-primary">Guardar Categoria Orden</button>

				        </div>

				        <?php

				         //$crearPerfilTecnico = new ControladorTecnicos();
				         //$crearPerfilTecnico -> ctrCrearTecnico();

				        ?>

			      	</div>

			    </div>

		    </form>

	    </div>

  	</div>

</div>
