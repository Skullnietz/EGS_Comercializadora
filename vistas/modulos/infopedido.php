<?php
if($_SESSION["perfil"] != "administrador" AND $_SESSION["perfil"]!= "vendedor" AND $_SESSION["perfil"]!= "tecnico"){

  echo '<script>

  window.location = "inicio";

  </script>';

  return;

}

?>
<div class="content-wrapper">
	
	<section class="content-header">

		<?php 

			$item = "id";
			$valor = $_GET["idPedido"];

			$pedidos = ControladorPedidos::ctrMostrarorpedidosParaValidar($item,$valor);

			foreach ($pedidos as $key => $valuePedidos) {
				
				
			}

		?>
		
		<h2><center>Pedido Numero: <?php echo $_GET["idPedido"] ?></center></h2>

		<button class='btn btn-info' data-toggle='modal' data-target='#modalAsignarPedido'>Asignar Pedido A Orden <i class='fas fa-sort'></i></button>



		<ol class="breadcrumb">
		
			<li><a href="#"><i class="fas fa-dashboard"></i></a></li>

			<li class="active">Pedido </li>

		</ol>

	</section>



	<div class="content">
		
		<div class="row">
			
			<div class="col-lg col-xs-5">

				<div class="box box-success">
					

					<div class="box-header whith-border"></div>

					<div class="box-body">
						
						<div class="box">
							
							<!--====================================

					        ENTRADA DATOS DEL CLIENTE

					        ======================================-->

					          <?php

            

							    //TRAER CLIENTE (USUARIO)

							    $item = "id";

							    $valor = $_GET["cliente"];



							    $usuario = ControladorClientes::ctrMostrarClientesOrdenes($item,$valor);




							    ?>

					        <div class="form-group">
					        	
					        	<div class="input-group">
					        		
					        		<span class="input-group-addon"><i class="fas fa-user"></i></span>

					        		<input type="text" class="form-control" value="<?php echo $usuario["nombre"] ?>" readonly>

					        	</div>

					        </div>

					        <div class="form-group">
					        	
					        	<div class="input-group">
					        		
					        		<span class="input-group-addon"><i class="fas fa-envelope"></i></span>

					        		<input type="text" class="form-control" value="<?php echo $usuario["correo"] ?>" readonly>

					        	</div>

					        </div>

					        <div class="form-group">
					        	
					        	<div class="input-group">
					        		
					        		<span class="input-group-addon"><i class="fa fa-phone"></i></span>

					        		<input type="number" class="form-control" value="<?php echo $usuario["telefono"] ?>" readonly>

					        	</div>

					        </div>

					        <div class="form-group">
					        	
					        	<div class="input-group">
					        		
					        		<span class="input-group-addon"><i class="fas fa-phone"></i></span>

					        		<input type="number"class="form-control" value="<?php echo $usuario["telefonoDos"] ?>" readonly>

					        	</div>

					        </div>

						</div>

					</div>	

				</div>

			</div>

			<!--=====================================
					INFORMACION DE ESTADO DEL PEDIDO
					======================================-->
					 <div class="col-lg-7">
					      		
					      <div class="box box-warning">
					      			
					      	<div class="box-header whith-border"></div>

					      		<div class="box-body">
					      				
					      			<table class="table table-borderd table-striped dt-responsive">
					      					
					      				<thead>
					      						
					      					<tr>
					      							
					      						<th>Estado</th>

					      					</tr>

					      				</thead>

					      				<tbody>

					      				    <td>

					                            <div class="form-group">
					                            	
					                            	<div class="input-group">

					                            		<span class="input-group-addon"><i class="fas fa-toggle-on"></i></span>

					                            		<form role="form" method="post">
					                            			<?php

					                            			if ($_SESSION["perfil"] == "administrador" || $_SESSION["perfil"] == "editor" || $_SESSION["perfil"] == "vendedor") {
					                            				
					                            				echo'<select class="form-control input-lg" name="EstadoPedidoDinamico">
                                    														
												                                    						
												                    <option>
												                      
												                       '.$valuePedidos["estado"].'

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

												                      <option value="cancelado">

												                        cancelado

												                      </option>
	

                                    					</select>';

					                            			}else{


																echo "<input type='text' class='form-control input-lg' value=".$valuePedidos["estado"]." readonly>";
					                            			}

					                            			?>
					                            		 
					                            	</div>

					                            </div>     
					                             <!---=========================
													ENTRADA PARA DATOS DEL ASESOR
					                             	===========================-->

					                             	<div class="form-group">
					                             		
					                             		<div class="input-group">
					                             			
					                             			<span class="input-group-addon"><i class="fas fa-headphones"></i></span>
					                             			<?php 

					                             				$item = "id";

				      											$valor = $_GET["asesor"];

                      											$asesor = Controladorasesores::ctrMostrarAsesoresEleg($item,$valor);

                      											echo '<input type="text" class="form-control input-lg" value="'.$asesor["nombre"].'"  readonly>';
                      										?>
					                             		
					                             		</div>

					                             	</div>

					                             	

					                             <!---=========================
													ENTRADA PARA NUMERO DE ORDEN
					                             	===========================-->

					                             	<div class="form-group">
					                             		
					                             		<div class="input-group">

					                             			<span class="input-group-addon"><i class="fas fa-sort"></i></span>
					                             			
					                             			<input type="text" class="form-control input-lg" value="Orden número: <?php echo $valuePedidos["id_orden"] ?>" readonly>

					                             		</div>

					                             	</div>

					                             	<div class="form-group">
					                             		
					                             		<div class="input-group">
					                             			
					                             			<span class="input-group-addon"><i class="fas fa-cogs"></i>
					                             			</span>

					                             			<?php
					                             			$itemOrdenes = "id";
					                             			$valorOrdenes = $valuePedidos["id_orden"];
					                             			$ordenes = controladorOrdenes::ctrMostrarordenesParaValidar($itemOrdenes,$valorOrdenes);

					                             			foreach ($ordenes as $key => $valueOrdenes) {
					                             				
					                             			}

					                             				$itemTec = "id";

				      											$valorTec = $valueOrdenes["id_tecnico"];

					                             				$respuesta = ControladorTecnicos::ctrMostrarTecnicos($itemTec, $valorTec);					                  
					                             				echo'<input type="text" class="form-control input-lg" value="Tecnico: '.$respuesta["nombre"].'" readonly>';
					                             			?>

					                             		</div>

					                             	</div>

					                        </td>

					      						
					      				</tbody>

					      			</table>


					      		</div>

					      	</div>
					</div>

</br></br></br></br></br></br>
				
 		<!--=====================================
      	INFORMACION DE PRODUCTOS
      	======================================-->
      	<div class="col-lg-7">
      		
      		<div class="box box-warning">
      			
      			<div class="box-header whith-border"></div>

      			<div class="box-body">
      				
      				<table class="table table-borderd table-striped dt-responsive">
      					
      					<thead>
      						
      						<tr>
      							
      							<th>Productos</th>

      							<th>Cantidad</th>

      							<th>Sub total</th>

      							<th>Total</th>

      						</tr>

      					</thead>

      					<tbody>

      						<?php

      						 

      							if ($valuePedidos["productoUno"] != "undefined" and $valuePedidos["productoUno"] != null) {
      								
      									
      								echo'<tr>

      									<td>'.$valuePedidos["productoUno"].'</td>
      									<td>'.$valuePedidos["cantidaProductoUno"].'</td>
      									<td><input tye="number" value="'.$valuePedidos["precioProductoUno"].'"></td>
      								</tr>';

      							}

      							

      							if ($valuePedidos["ProductoDos"] != "undefined" and $valuePedidos["ProductoDos"] != null) {
      								
      									
      								echo'<tr>

      									<td>'.$valuePedidos["ProductoDos"].'</td>
      									<td>'.$valuePedidos["cantidadProductoDos"].'</td>
      									<td>'.$valuePedidos["precioProductoDos"].'</td>
      								</tr>';

      							}


      							if($valuePedidos["ProductoTres"] != "undefined" and $valuePedidos["ProductoTres"] != null) {
      								
      								echo'<tr>

      									<td>'.$valuePedidos["ProductoTres"].'</td>
      									<td>'.$valuePedidos["cantidadProductoTres"].'</td>
      									<td>'.$valuePedidos["precioProductoTres"].'</td>
      								</tr>';

      							}

      							if($valuePedidos["ProductoCuatro"] != "undefined" and $valuePedidos["ProductoCuatro"] != null) {
      								
      								echo'<tr>

      									<td>'.$valuePedidos["ProductoCuatro"].'</td>
      									<td>'.$valuePedidos["cantidadProductoCuatro"].'</td>
      									<td>'.$valuePedidos["precioProductoCuatro"].'</td>
      								</tr>';

      							}

      							if($valuePedidos["ProductoCinco"] != "undefined" and $valuePedidos["ProductoCinco"] != null) {
      								 
      								echo'<tr>

      									<td>'.$valuePedidos["ProductoCinco"].'</td>
      									<td>'.$valuePedidos["cantidadProductoCinco"].'</td>
      									<td>'.$valuePedidos["precioProductoCinco"].'</td>
      								</tr>';																						

      							}

      							$productos = json_decode($valuePedidos["productos"], true);	

      							foreach ($productos as $key => $valueProductos) {
      									
      								if ($_SESSION["perfil"] == "administrador") {
      									echo'<tr>

      										<td><input type="text" value="'.$valueProductos["Descripcion"].'" class="descripcioParaListar"></td>
      										<td><input type="number" value="'.$valueProductos["cantidad"].'" class="cantidadProductoParaListar"></td>
      										<td><input type="number" value="'.$valueProductos["precio"].'" class="precioProductoParaListar"></td>

      									</tr>';		
      								}else{

      									echo'<tr>

      										<td><input type="text" value="'.$valueProductos["Descripcion"].'" class="descripcioParaListar" readonly></td>
      										<td><input type="number" value="'.$valueProductos["cantidad"].'" class="cantidadProductoParaListar" readonly></td>
      										<td><input type="number" value="'.$valueProductos["precio"].'" class="precioProductoParaListar" readonly></td>

      									</tr>';
      								}

      											
      							}


      						?>
      						<input type="hidden" id="ListarPreciosActualizados" name="ListarPreciosActualizados">
      						<tr>
      							<td></td>
      							<td></td>
      							<td></td>
      							<td>

      									
      								<div class="input-group">
      										
      									<span class="input-group-addon"><i class="fas fa-dollar"></i></span>
      									<input type="nmber" class="form-control totalPagarPedidoDinamico" name="totalPagarPedidoDinamico"value="<?php echo $valuePedidos["total"] ?>">

      								</div>

      								
      							</td>	
      							
      						</tr>

      						
      					</tbody>

      				</table>
      				<br>

      				<?php

      					if ($_SESSION["perfil"] == "administrador" || $_SESSION["perfil"] == "vendedor") {
      						
      						echo'<button type="button" class="btn btn-primary AgregarCampoDeObservacionPedidos">Agregar Nueva Observación </button>';
      					}

      				?>
      				<br><br>
      				<div class="col-lg-12">

      					<div class="box box-danger cajaObervacionesPedidos">
      						
      						<div class="box-header with-border">
      					
      							<div class="box-body">
		      						
		      						<div class="box">
		      						<?php

									

										echo'<input type="hidden" class="usuarioQueCaptura" value="'.$_SESSION["nombre"].'" name="usuarioQueCaptura">';

									?>
									<textarea type="hide"  class="form-control input-lg" id="fechaVista" style="display:none;"></textarea>

										<input type="hidden" id="listarObservacionesPedidos" name="listarObservacionesPedidos">

		      							
		      							<div class="agregarcampoobervacionesPedidos">
		      								
		      							</div>


		      						</div>

		      					</div>

      						</div>


      					</div>

      					<?php

											$observaciones = json_decode($valuePedidos["observaciones"], true);

											if (is_array($observaciones) || is_object($observaciones)) {
												
												foreach ($observaciones as $key => $valueObservaciones){
													
													if ($_SESSION["perfil"] == "administrador" || $_SESSION["perfil"] == "vendedor"   || $_SESSION["perfil"] == "tecnico") {
														
														

														echo'<div class="form-group">

															<span class="input-group-addon">'.$valueObservaciones["creador"].' '.$valueObservaciones["fecha"].'</span>

															<div class="input-group">

																<span class="input-group-addon"><i class="fas fa-pencil"></i></span>

																<textarea type="text"  class="form-control input-lg nuevaObservacion"  style="text-alinging:right; font-weight: bold;"readonly>'.$valueObservaciones["observacion"].'</textarea>


															</div>

														</div>';	
													}
												}
											}
		      							?>


      				
      					
      				</div>

      			</div>

      		</div>	

      	</div>

      		
      	<!--=====================================
        ENTRADA PARA AGREGAR PAGOS
        ======================================-->
       	<?php

       	if ($_SESSION["perfil"] == "administrador" || $_SESSION["perfil"] == "editor" || $_SESSION["perfil"] == "vendedor"){
      			
      			echo' <div class="col-lg-5 pagosPedido">

        	<style>
        		
        		.pagosPedido{

        			padding-top: 0;
        		}

        	</style>
      		
      		<div class="box box-warning">
      			
      			<div class="box-header whith-border"></div>

      			<div class="box-body">
	        		
	        		<table class="table table-borderd table-striped dt-responsive">
	        			
	        			<thead>
	        				
	        				<tr>
	        					
	        					<th>Abono</th>
	        					<th>Fecha</th>

	        				</tr>

	        			</thead>

	        			<tbody>
	        				
	        				<tr>

	        					<td>'; 		

	        					if ($valuePedidos["pagoPedido"] != null and $valuePedidos["pagoPedido"] != "" and $valuePedidos["pagoPedido"] != 0) {


	        						echo '<div class="input-group">
									        	<span class="input-group-addon"><i class="fas fa-money"></i></span>
									        	<input type="number" class="form-control" value="'.$valuePedidos["pagoPedido"].'" readonly>
										      	</div>
										      </br></br>';
								}

								echo'<div class="agregarCamposPago">';

	        								$pagos = json_decode($valuePedidos["pagos"], true);	

	        								if ($pagos != null and $pagos != "") {

	        									
	        									foreach ($pagos as $key => $valuePagos) {
	        										 
	        										 echo'<div class="input-group">
									        				<span class="input-group-addon"><i class="fas fa-money"></i></span>
									        				<input type="number" class="form-control pagoAbonado" value="'.$valuePagos["pago"].'" readonly>
										      				</div>
										      			</br></br>';
	        									}
	        									
	        									
	        								}

	        								echo'	
	        						</div>

	        					</td>

	        					<td>';

	        					if ($valuePedidos["pagoPedido"] != null and $valuePedidos["pagoPedido"] != "" and $valuePedidos["pagoPedido"] != 0) {
	        								
	        								echo '<br><br><br><br>';
	        							}

	        							 echo'<div class="nuevoCampoPagoPedido">';

	        							 if ($pagos != null and $pagos != "") {

	        									foreach ($pagos as $key => $valuePagos) {
	        										 
	        										 echo'<div class="input-group">
									        				<span class="input-group-addon"><i class="fas fa-table"></i></span>
									        				<input type="date" class="form-control fechaAbono" value="'.$valuePagos["fecha"].'" readonly>
										      				</div>
										      			</br></br>';
	        									}
	        									
	        									
	        								}


	        								echo'</div>

	        						<input type="hidden" class="PagosListados" name="PagosListados">
	        						<input type="hidden" value="'.$_GET["idPedido"].'"  name="idPedido">	        							

	        					</td>

	        					<button type="button" class="btn btn-primary agregarCamposPagoPedido">Agregar Nuevo Pago</button>

	        			
	        					
	        				</tr>

	        			</tbody>

	        		</table>

	        		<div class="form-group">
	        			<span><b>Total</b></span>
	        			<div class="input-group">
	        				
	        				<span class="input-group-addon"><i class="fas fa-dollar"></i></span>
	        				<input type="number" class="form-control totalPagosPeiddoDinamico" readonly>

	        			</div>

	        		</div>

	        			<div class="form-group">
	        			<span><b>Adeudo</b></span>
	        			<div class="input-group">
	        				
	        				<span class="input-group-addon"><i class="fas fa-dollar"></i></span>
	        				<input type="number" class="form-control adeudoPedidoDinamico" name="adeudoPedidoDinamico" readonly>

	        			</div>

	        		</div>

	        		<div class="box-footer">

           				<button type="submit" class="btn btn-primary pull-right">Guardar Pedido</button>

          			</div>';

          				$editarOrdenDinamica = new ControladorPedidos();
          				$editarOrdenDinamica -> ctrEditarOrdenDinamica();
          		
					

	        	echo'</form>';

       	}

       	?>
	        						



      			</div>




      		</div>

      		<!--=================

      			COLOCAR  ESTADO DE PEDIDO
      			===========--->

        </div>

		</div>

	</div>


</div>


<!--=====================================
MODAL AGREGAR PEDIDO
======================================-->
<div id="modalAsignarPedido" class="modal fade" role="dialog"> 

  <form role="form" method="post" class="formularioPedidosDinamicos">
  
    <div class="modal-dialog">
      
      <div class="modal-content">
        
        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->
        <div class="modal-header" style="background:#138a1e; color:white">
          
          <button type="button" class="close" data-dismiss="modal">&times;</button>

          <h4>Asignar Pedido</h4>

        </div>

        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->
        <div class="modal-body">
          
          <div class="box-body">

              <!--ASIGNAR ORDEN -->

              <div class="form-group">

                <div class="input-group">
                  
                  <select class="form-control input-lg" name="AsignarPedidoDinamico">
                  
                    <?php

                          echo '

                          <option value="'.$_GET["idPedido"].'">'.$_GET["idPedido"].'</option>';

                      

                    ?>

                  </select>

                </div> 

              </div>
              <!--=====================================
              ENTRADA PARA NUMERO DE ORDEN
              ======================================-->
              <div class="form-group">
                
                <div class="input-group">
                  
                  <select class="form-control input-lg select2" name="AsignarOrdenDinamico">
                    
                    <option>Asignar Orden</option>

                    <?php
                
                        $orden = controladorOrdenes::ctrMostrarOrdenesSuma();

                        foreach ($orden as $key => $valueOrden) {
                          
                          echo '

                          <option value="'.$valueOrden["id"].'">'.$valueOrden["id"].'</option>';

                        }

                    ?>


                  </select>

                </div>

              </div>


                
              </div>


          </div>
          <!--=====================================
           PIE DEL MODAL
          ======================================-->

        <div class="modal-footer">

          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

          <button type="submit" class="btn btn-primary">Guardar Pedido</button>

        </div>


        </div>

      </div>
    
  </div>
    </div>

    <?php

         $crearPedido = new ControladorPedidos();
         $crearPedido -> ctrAsignarPedidoEnOrden();

        ?>

  </form>

</div>

<script>
    /*=============================================
CARGAR LA TABLA DINÁMICA DE VENTAS
=============================================*/
//var Perfil = $("#Perfil").val();
$.ajax({
	//url:"ajax/tablapedidos.ajax.php?perfil="+$("#Perfil").val(),
  url:"ajax/tablapedidos.ajax.php?perfil="+$("#tipoDePerfil").val()+"&empresa="+$("#id_empresa").val(),
 	success:function(respuesta){
		
 		//console.log("respuesta", respuesta);

 	}
 })

$(".tablaPedidos").DataTable({
  "ajax": "ajax/tablapedidos.ajax.php?perfil="+$("#tipoDePerfil").val()+"&empresa="+$("#id_empresa").val(),
	 "deferRender": true,
	 "retrieve": true,
	 "processing": true,
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