<?php

class ControladorPedidos{
    /*=============================================
	MOSTRAR HISTORIAL PEDIDOS
	=============================================*/
    	static public function ctrMostrarHistorial( $tabla, $valoru){

		$tabla = "pedidos";

		

		$respuesta = ModeloPedidos::mdlMostrarHistorial($tabla, $valoru);



		return $respuesta;

	}
	
	/*=============================================
	MOSTRAR PEDIDOS
	=============================================*/

	static public function ctrMostrarPedidos(){

		$tabla = "pedidos";

		$respuesta = ModeloPedidos::mdlMostrarPedidos($tabla);

		return $respuesta;

	}
	/*=============================================
	MOSTRAR PEDIDO
	=============================================*/

	static public function ctrMostrarPedido($item,$valor){

		$tabla = "pedidos";

		$respuesta = ModeloPedidos::mdlMostrarPedido($tabla,$item,$valor);

		return $respuesta;

	}
	/*=============================================
	MOSTRAR PEDIDOS PARA EMPRESAS
	=============================================*/

	static public function ctrMostrarPedidoEmpresas($item,$valor){

		$tabla = "pedidos";

		$respuesta = ModeloPedidos::mdlMostrarPedidoEmpresas($tabla,$item,$valor);

		return $respuesta;

	}

	public function ctrCrearPedido($datos){
		
		if (isset($datos["empresaPedido"])){
			
			$datospedido = array("empresaPedido"=>$datos["empresaPedido"],
						   "AsesorPedido"=>$datos["AsesorPedido"],
						   "clientePeido"=>$datos["clientePeido"],
						   "Producto1"=>$datos["Producto1"],
						   "precioProducto1"=>$datos["precioProducto1"],
						   "cantidadProducto1"=>$datos["cantidadProducto1"],
						   "totalPedidoUno"=>$datos["totalPedidoUno"],
						   "Producto2"=>$datos["Producto2"],
						   "precioProducto2"=>$datos["precioProducto2"],
						   "cantidadProducto2"=>$datos["cantidadProducto2"],
						   "totalPedidoDos"=>$datos["totalPedidoDos"],
						   "Producto3"=>$datos["Producto3"],
						   "precioProducto3"=>$datos["precioProducto3"],
						   "cantidadProducto3"=>$datos["cantidadProducto3"],
						   "totalPedidoTres"=>$datos["totalPedidoTres"],
						   "Producto4"=>$datos["Producto4"],
						   "precioProducto4"=>$datos["precioProducto4"],
						   "cantidadProducto4"=>$datos["cantidadProducto4"],
						   "totalPedidoCuatro"=>$datos["totalPedidoCuatro"],
						   "Producto5"=>$datos["Producto5"],
						   "precioProducto5"=>$datos["precioProducto5"],
						   "cantidadProducto5"=>$datos["cantidadProducto5"],
						   "totalPedidoCinco"=>$datos["totalPedidoCinco"],
						   "metodo"=>$datos["metodo"],
						    "pagoClientePedido"=>$datos["pagoClientePedido"],
						    "pagoPedido"=>$datos["pagoPedido"],
						    "adeudo"=>$datos["adeudo"],
						    "fechaEntrega"=>$datos["fechaEntrega"],
						    "IngresarEstadoDelPedido"=>$datos["IngresarEstadoDelPedido"]
						    			);

			$respuesta = ModeloPedidos::mdlIngresarPedido("pedidos", $datospedido);

			return $respuesta;

		}else{


			echo'<script>

					swal({
						  type: "error",
						  title: "¡No se ha podido crear el pedido verifique sus datos!",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result){
							if (result.value) {

							window.location = "index.php?ruta=pedidos";

							}
						})

			  	</script>';
		}
	}

	/*=============================================
	MOSTRAR ORDENES
	=============================================*/

	static public function ctrMostrarorpedidosParaValidar($item, $valor){

		$tabla = "pedidos";

		$respuesta = ModeloPedidos::mdlMostrarpedidosParaValidar($tabla, $item, $valor);

		return $respuesta;
	
	}


	/*=============================================
	EDITAR PEDIDO
	=============================================*/

	static public function ctrEditarPedido($datos){

		if(isset($datos["idPedido"])){


				$item = "id";
				$valor = $datos["idPedido"];

				 
				$datosPedioEditado = array(
						 		   "id"=>$datos["idPedido"],
						 		   "edicionProductoUnoPedido"=>$datos["edicionProductoUnoPedido"],
								   "abono1"=>$datos["abono1"],
								   "fechaAbono1"=>$datos["fechaAbono1"],
								   "edicionProductoUnoPedidoDos"=>$datos["edicionProductoUnoPedidoDos"],
								   "abono2"=>$datos["abono2"],
								   "fechaAbono2"=>$datos["fechaAbono2"],
								   "abono3"=>$datos["abono3"],
								   "fechaAbono3"=>$datos["fechaAbono3"],
								   "abono4"=>$datos["abono4"],
								   "fechaAbono4"=>$datos["fechaAbono4"],
								   "abono5"=>$datos["abono5"],
								   "fechaAbono5"=>$datos["fechaAbono5"],
								   "adeudoPedidoEditado"=>$datos["adeudoPedidoEditado"],
								   "EstadoDelPedido" =>$datos["EstadoDelPedido"]
							);

				
				$respuesta = ModeloPedidos::mdlEditarPedido("pedidos", $datosPedioEditado);

				// Sistema de recompensas deshabilitado en pedidos

				return $respuesta;

			
		}else{

				echo'<script>

					swal({
						  type: "error",
						  title: "¡El abono no se ha podido Guardar correctamente!",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result){
							if (result.value) {

							window.location = "index.php?ruta=pedidos";

							}
						})

			  	</script>';

		}
		
	}


	
	/*=============================================
	ELIMINAR PEDIDO
	=============================================*/

	static public function ctrEliminarPedido(){

		if(isset($_GET["idpedido"])){

			$datos = $_GET["idpedido"];


			$respuesta = ModeloPedidos::mdlEliminarPedido("pedidos", $datos);

			if($respuesta == "ok"){

				echo'<script>

				swal({
					  type: "success",
					  title: "El pedido ha sido borrado correctamente",
					  showConfirmButton: true,
					  confirmButtonText: "Cerrar"
					  }).then(function(result){
								if (result.value) {

								window.location = "index.php?ruta=pedidos";

								}
							})

				</script>';

			}		



		}

	}
	/*=============================================
	DESCARGAR REPORTE GENERAL PEDIDOS EXCEL 
	=============================================*/

	public function ctrDescargarReportePedidos($valorEmpres){
	
		if (isset($_GET["reporte"])) {

			$tabla = "pedidos";
			
				$item = "id_empresa";
				$valor = $valorEmpres;
				$pedidos = ModeloPedidos::mdlMostrarpedidosParaValidar($tabla, $item, $valor);


			/*=============================================
			CREAMOS EL ARCHIVO DE EXCEL
			=============================================*/

			$Name = $_GET["reporte"].'.xls';

			header('Expires: 0');
			header('Cache-control: private');
			header("Content-type: application/vnd.ms-excel"); // Archivo de Excel
			header("Cache-Control: cache, must-revalidate"); 
			header('Content-Description: File Transfer');
			header('Last-Modified: '.date('D, d M Y H:i:s'));
			header("Pragma: public"); 
			header('Content-Disposition:; filename="'.$Name.'"');
			header("Content-Transfer-Encoding: binary");

			echo utf8_decode("<table border='0'> 

					<tr> 
						<td style='font-weight:bold; border:1px solid #eee;'>Pedido</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Empresa</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Asesor</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Cliente</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Estado</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Productos</td>
						<td style='font-weight:bold; border:1px solid #eee;'>total</td>
						<td style='font-weight:bold; border:1px solid #eee;'>fecha</td>
					</tr>");


			foreach ($pedidos as $key => $value) {
				
				 $item = "id";
              	$valor = $value["id_empresa"];

             	 $NameEmpresa = ControladorVentas::ctrMostrarEmpresasParaTiketimp($item,$valor);

              	$NombreEmpresa = $NameEmpresa["empresa"];
				
				//TRAER ASESOR
                    
	              $item = "id";
	              $valor = $value["id_Asesor"];

	              $asesor = Controladorasesores::ctrMostrarAsesoresEleg($item,$valor);
					
				  $NombreAsesor = $asesor["nombre"];

             	//TRAER CLIENTE (USUARIO)

                $item = "id";
                $valor = $value["id_cliente"];

                $usuario = ControladorClientes::ctrMostrarClientes($item,$valor);

                $NombreUsuario = $usuario["nombre"];

				//$ElTotal = number_format($total["total"],2);

                if (isset($value["ProductoCinco"])) {

                	$value["ProductoCinco"] = $value["ProductoCinco"] ;
                	
                }else{
                	$value["ProductoCinco"] = " ";
                }




				/*=============================================
				TRAER EMAIL DATOS DE COMPRA
				=============================================*/

					echo utf8_decode("</td>
									 <td style='border:1px solid #eee;'>".$value["id"]."</td>
									 <td style='border:1px solid #eee;'>".$NombreEmpresa."</td>
			 					  	 <td style='border:1px solid #eee;'>".$NombreAsesor."</td>
			 					  	 <td style='border:1px solid #eee;'>".$NombreUsuario."</td>
			 					  	 <td style='border:1px solid #eee;'>".$value["estado"]."</td>
			 					  	 <td style='border:1px solid #eee;'>

			 					  	 	<ol>

			 					  	 		<li>".$value["productoUno"]."</li>
			 					  	 		<li>".$value["ProductoDos"]."</li>
			 					  	 		<li>".$value["ProductoTres"]."</li>
			 					  	 		<li>".$value["ProductoCuatro"]."</li>
			 					  	 		<li>".$value["ProductoCinco"]."</li>
			 					  	 	</ol>	
			 					  	
			 					  	</td>
			 					  	 <td style='border:1px solid #eee;'>$ ".$value["total"]."</td>
			 					  	 <td style='border:1px solid #eee;'>".$value["fechaDePedido"]."</td>
			 					  	 </tr>"); 		

			}
			    /*=============================================
				TRAER TOTAL
				=============================================*/
				$tabla= "ordenes";
				$total = ModeloVentas::mdlSumarTotalOrdenes($tabla, $_GET["fechaInicial"], $_GET["fechaFinal"]);
				foreach ($total as $key => $valueTotal) {
						echo utf8_decode("<tr><td style='font-weight:bold; border:1px solid #eee;'>TOTAL DE VENTAS</td></tr>"); 

						echo utf8_decode("</td><td style='border:1px solid #eee;'>$".$ElTotal = number_format($valueTotal["total"],2)."</td>
			 					  	 	</td>"); 
				}



				echo utf8_decode("</table>

					");
		}

	}



	/*=============================================
	DESCARGAR REPORTE GENERAL PEDIDOS PENDIENTES EXCEL  
	=============================================*/

	public function ctrDescargarReportePedidosPendientes($valorEmpresa){
	
		if (isset($_GET["reporte"])) {

			$tabla = "pedidos";

			$item = "estado";
			$valor = "Pedido Pendiente";
			$itemDos = "id_empresa";
			$valorDos = $valorEmpresa;
			
				$pedidos = ModeloPedidos::mdlMostrarpedidosParaEmpresaCOnestado($tabla, $item, $valor, $itemDos, $valorDos);


			/*=============================================
			CREAMOS EL ARCHIVO DE EXCEL
			=============================================*/

			$Name = $_GET["reporte"].'.xls';

			header('Expires: 0');
			header('Cache-control: private');
			header("Content-type: application/vnd.ms-excel"); // Archivo de Excel
			header("Cache-Control: cache, must-revalidate"); 
			header('Content-Description: File Transfer');
			header('Last-Modified: '.date('D, d M Y H:i:s'));
			header("Pragma: public"); 
			header('Content-Disposition:; filename="'.$Name.'"');
			header("Content-Transfer-Encoding: binary");

			echo utf8_decode("<table border='0'> 

					<tr> 
						<td style='font-weight:bold; border:1px solid #eee;'>Pedido</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Empresa</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Asesor</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Cliente</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Estado</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Productos</td>
						<td style='font-weight:bold; border:1px solid #eee;'>total</td>
						<td style='font-weight:bold; border:1px solid #eee;'>fecha</td>
					</tr>");


			foreach ($pedidos as $key => $value) {
				
				 $item = "id";
              	$valor = $value["id_empresa"];

             	 $NameEmpresa = ControladorVentas::ctrMostrarEmpresasParaTiketimp($item,$valor);

              	$NombreEmpresa = $NameEmpresa["empresa"];
				
				//TRAER ASESOR
                    
	              $item = "id";
	              $valor = $value["id_Asesor"];

	              $asesor = Controladorasesores::ctrMostrarAsesoresEleg($item,$valor);
					
				  $NombreAsesor = $asesor["nombre"];

             	//TRAER CLIENTE (USUARIO)

                $item = "id";
                $valor = $value["id_cliente"];

                $usuario = ControladorClientes::ctrMostrarClientes($item,$valor);

                $NombreUsuario = $usuario["nombre"];

				//$ElTotal = number_format($total["total"],2);

                if (isset($value["ProductoCinco"])) {

                	$value["ProductoCinco"] = $value["ProductoCinco"] ;
                	
                }else{
                	$value["ProductoCinco"] = " ";
                }




				/*=============================================
				TRAER EMAIL DATOS DE COMPRA
				=============================================*/

					echo utf8_decode("</td>
									 <td style='border:1px solid #eee;'>".$value["id"]."</td>
									 <td style='border:1px solid #eee;'>".$NombreEmpresa."</td>
			 					  	 <td style='border:1px solid #eee;'>".$NombreAsesor."</td>
			 					  	 <td style='border:1px solid #eee;'>".$NombreUsuario."</td>
			 					  	 <td style='border:1px solid #eee;'>".$value["estado"]."</td>
			 					  	 <td style='border:1px solid #eee;'>

			 					  	 	<ol>

			 					  	 		<li>".$value["productoUno"]."</li>
			 					  	 		<li>".$value["ProductoDos"]."</li>
			 					  	 		<li>".$value["ProductoTres"]."</li>
			 					  	 		<li>".$value["ProductoCuatro"]."</li>
			 					  	 		<li>".$value["ProductoCinco"]."</li>
			 					  	 	</ol>	
			 					  	
			 					  	</td>
			 					  	 <td style='border:1px solid #eee;'>$ ".$value["total"]."</td>
			 					  	 <td style='border:1px solid #eee;'>".$value["fechaDePedido"]."</td>
			 					  	 </tr>"); 		

			}
			   

				echo utf8_decode("</table>

					");
		}

	}

	//MIKE 1

	/*=============================================
	DESCARGAR REPORTE GENERAL PEDIDOS ADQUIRIDOS EXCEL  
	=============================================*/

	public function ctrDescargarReportePedidosAdquiridos($valorEmpresa){
	
		if (isset($_GET["reporte"])) {

			$tabla = "pedidos";

			$item = "estado";
			//Mike checar el Nombre
			$valor = "Pedido Adquirido";
			
			$itemDos ="id_empresa";
			$valorDos = $valorEmpresa;

				$pedidos = ModeloPedidos::mdlMostrarpedidosParaEmpresaCOnestado($tabla, $item, $valor, $itemDos, $valorDos);


			/*=============================================
			CREAMOS EL ARCHIVO DE EXCEL
			=============================================*/

			$Name = $_GET["reporte"].'.xls';

			header('Expires: 0');
			header('Cache-control: private');
			header("Content-type: application/vnd.ms-excel"); // Archivo de Excel
			header("Cache-Control: cache, must-revalidate"); 
			header('Content-Description: File Transfer');
			header('Last-Modified: '.date('D, d M Y H:i:s'));
			header("Pragma: public"); 
			header('Content-Disposition:; filename="'.$Name.'"');
			header("Content-Transfer-Encoding: binary");

			echo utf8_decode("<table border='0'> 

					<tr> 
						<td style='font-weight:bold; border:1px solid #eee;'>Pedido</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Empresa</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Asesor</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Cliente</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Estado</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Productos</td>
						<td style='font-weight:bold; border:1px solid #eee;'>total</td>
						<td style='font-weight:bold; border:1px solid #eee;'>fecha</td>
					</tr>");


			foreach ($pedidos as $key => $value) {
				
				 $item = "id";
              	$valor = $value["id_empresa"];

             	 $NameEmpresa = ControladorVentas::ctrMostrarEmpresasParaTiketimp($item,$valor);

              	$NombreEmpresa = $NameEmpresa["empresa"];
				
				//TRAER ASESOR
                    
	              $item = "id";
	              $valor = $value["id_Asesor"];

	              $asesor = Controladorasesores::ctrMostrarAsesoresEleg($item,$valor);
					
				  $NombreAsesor = $asesor["nombre"];

             	//TRAER CLIENTE (USUARIO)

                $item = "id";
                $valor = $value["id_cliente"];

                $usuario = ControladorClientes::ctrMostrarClientes($item,$valor);

                $NombreUsuario = $usuario["nombre"];

				//$ElTotal = number_format($total["total"],2);

                if (isset($value["ProductoCinco"])) {

                	$value["ProductoCinco"] = $value["ProductoCinco"] ;
                	
                }else{
                	$value["ProductoCinco"] = " ";
                }




				/*=============================================
				TRAER EMAIL DATOS DE COMPRA
				=============================================*/

					echo utf8_decode("</td>
									 <td style='border:1px solid #eee;'>".$value["id"]."</td>
									 <td style='border:1px solid #eee;'>".$NombreEmpresa."</td>
			 					  	 <td style='border:1px solid #eee;'>".$NombreAsesor."</td>
			 					  	 <td style='border:1px solid #eee;'>".$NombreUsuario."</td>
			 					  	 <td style='border:1px solid #eee;'>".$value["estado"]."</td>
			 					  	 <td style='border:1px solid #eee;'>

			 					  	 	<ol>

			 					  	 		<li>".$value["productoUno"]."</li>
			 					  	 		<li>".$value["ProductoDos"]."</li>
			 					  	 		<li>".$value["ProductoTres"]."</li>
			 					  	 		<li>".$value["ProductoCuatro"]."</li>
			 					  	 		<li>".$value["ProductoCinco"]."</li>
			 					  	 	</ol>	
			 					  	
			 					  	</td>
			 					  	 <td style='border:1px solid #eee;'>$ ".$value["total"]."</td>
			 					  	 <td style='border:1px solid #eee;'>".$value["fechaDePedido"]."</td>
			 					  	 </tr>"); 		

			}
			   

				echo utf8_decode("</table>

					");
		}

	}

//MIKE 2

	/*=============================================
	DESCARGAR REPORTE GENERAL PEDIDOS ASESOR EXCEL  
	=============================================*/

	public function ctrDescargarReportePedidosAsesor($valorEmpresa){
	
		if (isset($_GET["reporte"])) {

			$tabla = "pedidos";

			$item = "estado";
			//Mike checar el Nombre
			$valor = "Entregado al asesor";
			$itemDos = "id_empresa";
			$valorDos = $valorEmpresa;
			
			$pedidos = ModeloPedidos::mdlMostrarpedidosParaEmpresaCOnestado($tabla, $item, $valor, $itemDos, $valorDos);


			/*=============================================
			CREAMOS EL ARCHIVO DE EXCEL
			=============================================*/

			$Name = $_GET["reporte"].'.xls';

			header('Expires: 0');
			header('Cache-control: private');
			header("Content-type: application/vnd.ms-excel"); // Archivo de Excel
			header("Cache-Control: cache, must-revalidate"); 
			header('Content-Description: File Transfer');
			header('Last-Modified: '.date('D, d M Y H:i:s'));
			header("Pragma: public"); 
			header('Content-Disposition:; filename="'.$Name.'"');
			header("Content-Transfer-Encoding: binary");

			echo utf8_decode("<table border='0'> 

					<tr> 
						<td style='font-weight:bold; border:1px solid #eee;'>Pedido</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Empresa</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Asesor</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Cliente</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Estado</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Productos</td>
						<td style='font-weight:bold; border:1px solid #eee;'>total</td>
						<td style='font-weight:bold; border:1px solid #eee;'>fecha</td>
					</tr>");


			foreach ($pedidos as $key => $value) {
				
				 $item = "id";
              	$valor = $value["id_empresa"];

             	 $NameEmpresa = ControladorVentas::ctrMostrarEmpresasParaTiketimp($item,$valor);

              	$NombreEmpresa = $NameEmpresa["empresa"];
				
				//TRAER ASESOR
                    
	              $item = "id";
	              $valor = $value["id_Asesor"];

	              $asesor = Controladorasesores::ctrMostrarAsesoresEleg($item,$valor);
					
				  $NombreAsesor = $asesor["nombre"];

             	//TRAER CLIENTE (USUARIO)

                $item = "id";
                $valor = $value["id_cliente"];

                $usuario = ControladorClientes::ctrMostrarClientes($item,$valor);

                $NombreUsuario = $usuario["nombre"];

				//$ElTotal = number_format($total["total"],2);

                if (isset($value["ProductoCinco"])) {

                	$value["ProductoCinco"] = $value["ProductoCinco"] ;
                	
                }else{
                	$value["ProductoCinco"] = " ";
                }




				/*=============================================
				TRAER EMAIL DATOS DE COMPRA
				=============================================*/

					echo utf8_decode("</td>
									 <td style='border:1px solid #eee;'>".$value["id"]."</td>
									 <td style='border:1px solid #eee;'>".$NombreEmpresa."</td>
			 					  	 <td style='border:1px solid #eee;'>".$NombreAsesor."</td>
			 					  	 <td style='border:1px solid #eee;'>".$NombreUsuario."</td>
			 					  	 <td style='border:1px solid #eee;'>".$value["estado"]."</td>
			 					  	 <td style='border:1px solid #eee;'>

			 					  	 	<ol>

			 					  	 		<li>".$value["productoUno"]."</li>
			 					  	 		<li>".$value["ProductoDos"]."</li>
			 					  	 		<li>".$value["ProductoTres"]."</li>
			 					  	 		<li>".$value["ProductoCuatro"]."</li>
			 					  	 		<li>".$value["ProductoCinco"]."</li>
			 					  	 	</ol>	
			 					  	
			 					  	</td>
			 					  	 <td style='border:1px solid #eee;'>$ ".$value["total"]."</td>
			 					  	 <td style='border:1px solid #eee;'>".$value["fechaDePedido"]."</td>
			 					  	 </tr>"); 		

			}
			   

				echo utf8_decode("</table>

					");
		}

	}


//MIKE 3

	/*=============================================
	DESCARGAR REPORTE GENERAL PEDIDOS PAGADOS EXCEL  
	=============================================*/

	public function ctrDescargarReportePedidosPagados($valorEmpresa){
	
		if (isset($_GET["reporte"])) {

			$tabla = "pedidos";

			$item = "estado";
			//Mike Checar el Nombre
			$valor = "Entregado/Pagado";
			$itemDos = "id_empresa";
			$valorDos = $valorEmpresa;
				$pedidos = ModeloPedidos::mdlMostrarpedidosParaEmpresaCOnestado($tabla, $item, $valor, $itemDos, $valorDos);


			/*=============================================
			CREAMOS EL ARCHIVO DE EXCEL
			=============================================*/

			$Name = $_GET["reporte"].'.xls';

			header('Expires: 0');
			header('Cache-control: private');
			header("Content-type: application/vnd.ms-excel"); // Archivo de Excel
			header("Cache-Control: cache, must-revalidate"); 
			header('Content-Description: File Transfer');
			header('Last-Modified: '.date('D, d M Y H:i:s'));
			header("Pragma: public"); 
			header('Content-Disposition:; filename="'.$Name.'"');
			header("Content-Transfer-Encoding: binary");

			echo utf8_decode("<table border='0'> 

					<tr> 
						<td style='font-weight:bold; border:1px solid #eee;'>Pedido</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Empresa</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Asesor</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Cliente</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Estado</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Productos</td>
						<td style='font-weight:bold; border:1px solid #eee;'>total</td>
						<td style='font-weight:bold; border:1px solid #eee;'>fecha</td>
					</tr>");


			foreach ($pedidos as $key => $value) {
				
				 $item = "id";
              	$valor = $value["id_empresa"];

             	 $NameEmpresa = ControladorVentas::ctrMostrarEmpresasParaTiketimp($item,$valor);

              	$NombreEmpresa = $NameEmpresa["empresa"];
				
				//TRAER ASESOR
                    
	              $item = "id";
	              $valor = $value["id_Asesor"];

	              $asesor = Controladorasesores::ctrMostrarAsesoresEleg($item,$valor);
					
				  $NombreAsesor = $asesor["nombre"];

             	//TRAER CLIENTE (USUARIO)

                $item = "id";
                $valor = $value["id_cliente"];

                $usuario = ControladorClientes::ctrMostrarClientes($item,$valor);

                $NombreUsuario = $usuario["nombre"];

				//$ElTotal = number_format($total["total"],2);

                if (isset($value["ProductoCinco"])) {

                	$value["ProductoCinco"] = $value["ProductoCinco"] ;
                	
                }else{
                	$value["ProductoCinco"] = " ";
                }




				/*=============================================
				TRAER EMAIL DATOS DE COMPRA
				=============================================*/

					echo utf8_decode("</td>
									 <td style='border:1px solid #eee;'>".$value["id"]."</td>
									 <td style='border:1px solid #eee;'>".$NombreEmpresa."</td>
			 					  	 <td style='border:1px solid #eee;'>".$NombreAsesor."</td>
			 					  	 <td style='border:1px solid #eee;'>".$NombreUsuario."</td>
			 					  	 <td style='border:1px solid #eee;'>".$value["estado"]."</td>
			 					  	 <td style='border:1px solid #eee;'>

			 					  	 	<ol>

			 					  	 		<li>".$value["productoUno"]."</li>
			 					  	 		<li>".$value["ProductoDos"]."</li>
			 					  	 		<li>".$value["ProductoTres"]."</li>
			 					  	 		<li>".$value["ProductoCuatro"]."</li>
			 					  	 		<li>".$value["ProductoCinco"]."</li>
			 					  	 	</ol>	
			 					  	
			 					  	</td>
			 					  	 <td style='border:1px solid #eee;'>$ ".$value["total"]."</td>
			 					  	 <td style='border:1px solid #eee;'>".$value["fechaDePedido"]."</td>
			 					  	 </tr>"); 		

			}
			   

				echo utf8_decode("</table>

					");
		}

	}



//MIKE 4

	/*=============================================
	DESCARGAR REPORTE GENERAL PEDIDOS CREDITO EXCEL  
	=============================================*/

	public function ctrDescargarReportePedidosCredito($valorEmpresa){
	
		if (isset($_GET["reporte"])) {

			$tabla = "pedidos";

			$item = "estado";
			//Mike Checar el Nombre
			$valor = "Entregado/Crédito";
			$itemDos = "id_empresa";
			$valorDos = $valorEmpresa;
			
				$pedidos = ModeloPedidos::mdlMostrarpedidosParaEmpresaCOnestado($tabla, $item, $valor, $itemDos, $valorDos);


			/*=============================================
			CREAMOS EL ARCHIVO DE EXCEL
			=============================================*/

			$Name = $_GET["reporte"].'.xls';

			header('Expires: 0');
			header('Cache-control: private');
			header("Content-type: application/vnd.ms-excel"); // Archivo de Excel
			header("Cache-Control: cache, must-revalidate"); 
			header('Content-Description: File Transfer');
			header('Last-Modified: '.date('D, d M Y H:i:s'));
			header("Pragma: public"); 
			header('Content-Disposition:; filename="'.$Name.'"');
			header("Content-Transfer-Encoding: binary");

			echo utf8_decode("<table border='0'> 

					<tr> 
						<td style='font-weight:bold; border:1px solid #eee;'>Pedido</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Empresa</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Asesor</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Cliente</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Estado</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Productos</td>
						<td style='font-weight:bold; border:1px solid #eee;'>total</td>
						<td style='font-weight:bold; border:1px solid #eee;'>fecha</td>
					</tr>");


			foreach ($pedidos as $key => $value) {
				
				 $item = "id";
              	$valor = $value["id_empresa"];

             	 $NameEmpresa = ControladorVentas::ctrMostrarEmpresasParaTiketimp($item,$valor);

              	$NombreEmpresa = $NameEmpresa["empresa"];
				
				//TRAER ASESOR
                    
	              $item = "id";
	              $valor = $value["id_Asesor"];

	              $asesor = Controladorasesores::ctrMostrarAsesoresEleg($item,$valor);
					
				  $NombreAsesor = $asesor["nombre"];

             	//TRAER CLIENTE (USUARIO)

                $item = "id";
                $valor = $value["id_cliente"];

                $usuario = ControladorClientes::ctrMostrarClientes($item,$valor);

                $NombreUsuario = $usuario["nombre"];

				//$ElTotal = number_format($total["total"],2);

                if (isset($value["ProductoCinco"])) {

                	$value["ProductoCinco"] = $value["ProductoCinco"] ;
                	
                }else{
                	$value["ProductoCinco"] = " ";
                }




				/*=============================================
				TRAER EMAIL DATOS DE COMPRA
				=============================================*/

					echo utf8_decode("</td>
									 <td style='border:1px solid #eee;'>".$value["id"]."</td>
									 <td style='border:1px solid #eee;'>".$NombreEmpresa."</td>
			 					  	 <td style='border:1px solid #eee;'>".$NombreAsesor."</td>
			 					  	 <td style='border:1px solid #eee;'>".$NombreUsuario."</td>
			 					  	 <td style='border:1px solid #eee;'>".$value["estado"]."</td>
			 					  	 <td style='border:1px solid #eee;'>

			 					  	 	<ol>

			 					  	 		<li>".$value["productoUno"]."</li>
			 					  	 		<li>".$value["ProductoDos"]."</li>
			 					  	 		<li>".$value["ProductoTres"]."</li>
			 					  	 		<li>".$value["ProductoCuatro"]."</li>
			 					  	 		<li>".$value["ProductoCinco"]."</li>
			 					  	 	</ol>	
			 					  	
			 					  	</td>
			 					  	 <td style='border:1px solid #eee;'>$ ".$value["total"]."</td>
			 					  	 <td style='border:1px solid #eee;'>".$value["fechaDePedido"]."</td>
			 					  	 </tr>"); 		

			}
			   

				echo utf8_decode("</table>

					");
		}

	}


	/*=============================================
	AGREGAR ORDENES CON PAGOS LISTADOS
	=============================================*/

	public function ctrEditarOrdenDinamica(){
		
		if (isset($_POST["EstadoPedidoDinamico"])) {
			
			$tabla = "pedidos";

			$datosPedido = array("id" => $_POST["idPedido"],
								 "estado" => $_POST["EstadoPedidoDinamico"],
								 "pago" => $_POST["PagosListados"],
								 "adeudo" => $_POST["adeudoPedidoDinamico"],
								 "observaciones" => $_POST["listarObservacionesPedidos"],
								 "productos"=>$_POST["ListarPreciosActualizados"],
								 "total" => $_POST["totalPagarPedidoDinamico"]
								);

			$respuesta = ModeloPedidos::mdlEditarPedidoDinamico($tabla, $datosPedido);

			if ($respuesta == "ok") {


					echo '<script>



					swal({

						type: "success",
						title: "¡El pedido se ha guardado correctamente!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
							
							window.location = "index.php?ruta=pedidos";

					

						}

					});
				

					</script>';
				}
		}
	}



	/*=============================================
	ASIGNAR PEDIDO A ORDEN
	=============================================*/

	public function ctrAsignarPedidoEnOrden(){
		
		if (isset($_POST["AsignarPedidoDinamico"])) {
			
			$tabla = "ordenes";

			$datosPedidoAsignado = array("id" => $_POST["AsignarOrdenDinamico"],
								         "id_pedido" => $_POST["AsignarPedidoDinamico"]
								);

			$respuesta = ModeloPedidos::mdlAsignarPedidoDinamico($tabla, $datosPedidoAsignado);

			if ($respuesta == "ok") {


					echo '<script>



					swal({

						type: "success",
						title: "¡El pedido se ha guardado correctamente!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
							
							window.location = "index.php?ruta=ordenes";

					

						}

					});
				

					</script>';
				}
		}
	}
	
	/*=============================================
	AGREGAR NUEVO ESTADO A PEDIDO
	=============================================*/
	static public function ctrEditarPedidoEnEstado(){
		
		if (isset($_POST["EdicionUnicaDeEstadoDePedidoEnOrden"])) {
			
			$tabla = "pedidos";

			$datosEstadoPeido = array( "id"=> $_POST["idPeido"],
									   "estado" => $_POST["EdicionUnicaDeEstadoDePedidoEnOrden"]);


			$respuesta = ModeloPedidos::mdlAsignarNuevoEstadoPedido($tabla, $datosEstadoPeido);

			if ($respuesta == "ok") {


					echo '<script>



					swal({

						type: "success",
						title: "¡El pedido se ha guardado correctamente!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
							
							window.location = "index.php?ruta=ordenes";

					

						}

					});
				

					</script>';
				}

		}
	}

	/*=============================================
	MOSTRAR TOTAL PEDIDOS
	=============================================*/

	static public function ctrMostrarTotalPedidos($orden){

		$tabla = "pedidos";

		$respuesta = ModeloPedidos::mdlMostrarTotalPedido($tabla, $orden);

		return $respuesta;

	}
	
		/*=============================================
	MOSTRAR TOTAL PEDIDOS MES
	=============================================*/

	static public function ctrMostrarTotalPedidosMes($orden){

		$tabla = "pedidos";

		$respuesta = ModeloPedidos::mdlMostrarTotalPedidoMes($tabla, $orden);

		return $respuesta;

	}


	public function ctrDescargarReportePedidosSinEnlace($valorEmpresa){
	
		if (isset($_GET["reporte"])) {

			$tabla = "pedidos";
			
			$item ="id_empresa";
			$valor = $valorEmpresa;

			$itemDos = "id_orden";
			$valorDos = 0;

			$pedidos = ModeloPedidos::mdlMostrarpedidossinEnlace($tabla, $item, $valor, $itemDos, $valorDos);

			/*=============================================
			CREAMOS EL ARCHIVO DE EXCEL
			=============================================*/

			$Name = $_GET["reporte"].'.xls';

			header('Expires: 0');
			header('Cache-control: private');
			header("Content-type: application/vnd.ms-excel"); // Archivo de Excel
			header("Cache-Control: cache, must-revalidate"); 
			header('Content-Description: File Transfer');
			header('Last-Modified: '.date('D, d M Y H:i:s'));
			header("Pragma: public"); 
			header('Content-Disposition:; filename="'.$Name.'"');
			header("Content-Transfer-Encoding: binary");

			echo utf8_decode("<table border='0'> 

					<tr> 
						<td style='font-weight:bold; border:1px solid #eee;'>Pedido</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Orden</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Asesor</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Estado</td>
						<td style='font-weight:bold; border:1px solid #eee;'>fecha</td>
					</tr>");


			foreach ($pedidos as $key => $value) {
				
				 $item = "id";
              	$valor = $value["id_empresa"];

             	 $NameEmpresa = ControladorVentas::ctrMostrarEmpresasParaTiketimp($item,$valor);

              	$NombreEmpresa = $NameEmpresa["empresa"];
				
				//TRAER ASESOR
                    
	              $item = "id";
	              $valor = $value["id_Asesor"];

	              $asesor = Controladorasesores::ctrMostrarAsesoresEleg($item,$valor);
					
				  $NombreAsesor = $asesor["nombre"];

             	//TRAER CLIENTE (USUARIO)

                $item = "id";
                $valor = $value["id_cliente"];

                $usuario = ControladorClientes::ctrMostrarClientes($item,$valor);

                $NombreUsuario = $usuario["nombre"];

				//$ElTotal = number_format($total["total"],2);

                if (isset($value["ProductoCinco"])) {

                	$value["ProductoCinco"] = $value["ProductoCinco"] ;
                	
                }else{
                	$value["ProductoCinco"] = " ";
                }




				/*=============================================
				TRAER EMAIL DATOS DE COMPRA
				=============================================*/

					echo utf8_decode("</td>
									 <td style='border:1px solid #eee;'>".$value["id"]."</td>
									 <td style='border:1px solid #eee;'>".$value["id_orden"]."</td>
			 					  	 <td style='border:1px solid #eee;'>".$NombreAsesor."</td>
			 					  	 <td style='border:1px solid #eee;'>".$value["estado"]."</td>
			 					  	 <td style='border:1px solid #eee;'>".$value["fechaDePedido"]."</td>
			 					  	 </tr>"); 		

			}
			   

				echo utf8_decode("</table>

					");
		}

	}
}