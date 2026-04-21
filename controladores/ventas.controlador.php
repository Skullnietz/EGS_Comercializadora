<?php



class ControladorVentas{



	/*=============================================

	MOSTRAR TOTAL VENTAS

	=============================================*/



	static public function ctrMostrarTotalVentas(){



		$tabla = "compras";



		$respuesta = ModeloVentas::mdlMostrarTotalVentas($tabla);



		return $respuesta;



	}
	
		/*=============================================

	MOSTRAR TOTAL VENTAS MES

	=============================================*/



	static public function ctrMostrarTotalVentasMes(){



		$tabla = "compras";



		$respuesta = ModeloVentas::mdlMostrarTotalVentasMes($tabla);



		return $respuesta;



	}



	/*=============================================

	MOSTRAR VENTAS

	=============================================*/



	static public function ctrMostrarVentas(){



		$tabla = "compras";



		$respuesta = ModeloVentas::mdlMostrarVentas($tabla);



		return $respuesta;



	}



	/*=============================================

	MOSTRAR VENTAS R

	=============================================*/



	static public function ctrMostrarVentasR(){



		$tabla = "compras";



		$respuesta = ModeloVentas::mdlMostrarVentasR($tabla);



		return $respuesta;



	}





	/*=============================================

	MOSTRAR VENTAS PARA TIKET

	=============================================*/



	static public function ctrMostrarVentasParaTiket($item, $valor){



		$tabla = "compras";



		$respuesta = ModeloVentas::mdlMostrarVentasParaTiket($tabla, $item, $valor);



		return $respuesta;

	

	}
	/*=============================================

	MOSTRAR VENTAS PARA Empresa

	=============================================*/



	static public function ctrMostrarVentasParaEmpresa($item, $valor){



		$tabla = "compras";



		$respuesta = ModeloVentas::mdlMostrarVentasParaEmpresas($tabla, $item, $valor);



		return $respuesta;

	

	}


	/*=============================================

	MOSTRAR VENTAS PARA TIKET

	=============================================*/



	static public function ctrMostrarVentasParaTiketimp($item, $valor){



		$tabla = "compras";



		$respuesta = ModeloVentas::mdlMostrarVentasParaTiketimp($tabla, $item, $valor);



		return $respuesta;

	

	}



	/*=============================================
	MOSTRAR EMPRESAS PARA TIKET
	=============================================*/
	static public function ctrMostrarEmpresasParaTiketimp($item, $valor){

		$tabla = "empresa";

		$respuesta = ModeloVentas::mdlMostrarEmpresasParaTiketimp($tabla, $item, $valor);

		return $respuesta;

	
	}

	/*=============================================
	MOSTRAR ventas por empresa y asesor
	=============================================*/
	static public function ctrlMostrarventaPorAsesoryEmpresa($itemVentas, $valorVentas, $itemVentasDos, $valorventasDos){

		$tabla = "empresa";

		$respuesta = ModeloVentas::mdlMostrarventaPorAsesoryEmpresa($tabla, $itemVentas, $valorVentas, $itemVentasDos, $valorventasDos);

		return $respuesta;

	
	}

	/*=============================================
	AGREGAR VENTA RAPIDA
	=============================================*/

     static public function ctrCrearventa(){



		if(isset($_POST["productoUno"])){



				$tabla = "compras";



				$datos = array("empresa" => $_POST["empresa"],

							   "productoUno" => $_POST["productoUno"],

							   "precioUno" => $_POST["precioUno"],

							   "cantidadUno" => $_POST["cantidadUno"],

							   "productoDos" => $_POST["productoDos"],

							   "precioDos" => $_POST["precioDos"],

							   "cantidadDos" => $_POST["cantidadDos"],

							   "productoTres" => $_POST["productoTres"],

							   "precioTres" => $_POST["precioTres"],

							   "cantidadTres" => $_POST["cantidadTres"],
							   
							   "productoCuatro" => $_POST["productoCuatro"],

							   "precioCuatro" => $_POST["precioCuatro"],

							   "cantidadCuatro" => $_POST["cantidadCuatro"],
							   
							   "productoCinco" => $_POST["productoCinco"],

							   "precioCinco" => $_POST["precioCinco"],

							   "cantidadCinco" => $_POST["cantidadCinco"],
							   
							   "productoSeis" => $_POST["productoSeis"],

							   "precioSeis" => $_POST["precioSeis"],

							   "cantidadSeis" => $_POST["cantidadSeis"],
							   
							   "productoSiete" => $_POST["productoSiete"],

							   "precioSiete" => $_POST["precioSiete"],

							   "cantidadSiete" => $_POST["cantidadSiete"],
							   
							   "productoOcho" => $_POST["productoOcho"],

							   "precioOcho" => $_POST["precioOcho"],

							   "cantidadOcho" => $_POST["cantidadOcho"],
							   
							   "productoNueve" => $_POST["productoNueve"],

							   "precioNueve" => $_POST["precioNueve"],

							   "cantidadNueve" => $_POST["cantidadNueve"],
							   
							   "productoDiez" => $_POST["productoDiez"],

							   "precioDiez" => $_POST["precioDiez"],

							   "cantidadDiez" => $_POST["cantidadDiez"],

							   "cantidadProductos" => $_POST["cantidadProductos"],

							   "asesor" => $_POST["asesor"],

 							   "pago" => $_POST["pago"],

 							   "metodo" => $_POST["metodo"],

 							   "nombreCliente" => $_POST["nombreCliente"],	

 							   "correo" => $_POST["correo"],			

 							   "empresa" => $_POST["empresa"],

 							   "id_cliente" => isset($_POST["id_cliente"]) ? intval($_POST["id_cliente"]) : 0

							   );



				$respuesta = ModeloVentas::mdlIngresarVenta($tabla, $datos);

				// ── Recompensas: canjear dinero electrónico si se solicitó ──
				$_egs_idClienteVenta = isset($_POST["id_cliente"]) ? intval($_POST["id_cliente"]) : 0;
				$_egs_montoCanjeVenta = isset($_POST["montoCanjeElectronicoVenta"]) ? floatval($_POST["montoCanjeElectronicoVenta"]) : 0;

				if ($respuesta == "ok" && $_egs_montoCanjeVenta > 0 && $_egs_idClienteVenta > 0) {
					try {
						require_once "recompensas.controlador.php";
						require_once __DIR__ . "/../modelos/recompensas.modelo.php";
						$ultimaVenta = ModeloVentas::mdlObtenerUltimaVenta(intval($_POST["empresa"]));
						if ($ultimaVenta > 0) {
							// Verificar que no exista un canje previo para esta venta
							$canjeExistente = ModeloRecompensas::mdlObtenerCanjeVenta($ultimaVenta);
							if (!$canjeExistente) {
								ControladorRecompensas::ctrCanjearRecompensaVenta($_egs_idClienteVenta, $ultimaVenta, $_egs_montoCanjeVenta);
							}
						}
					} catch (Exception $e) {
						// No bloquear la venta si falla el canje
					}
				}

				if ($respuesta == "ok") {

					

						echo '<script>



					swal({



						type: "success",

						title: "¡La venta se ha realizado correctamente!",

						showConfirmButton: true,

						confirmButtonText: "Cerrar"



					}).then(function(result){



						if(result.value){

						

							window.location = "index.php?ruta=ventasR";



						}



					});

				



					</script>';

				}else{



						echo '<script>



					swal({



						type: "error",

						title: "¡Los campos no pueden ir vacíos o llevar caracteres especiales!",

						showConfirmButton: true,

						confirmButtonText: "Cerrar"



					}).then(function(result){



						if(result.value){

						

							window.location = "index.php?ruta=ventasR";



						}



					});

				



				</script>';



				}



		}



	}



	public function ctrEliminarVenta(){

		

		if (isset($_GET["idventa"])) {

			

			$tabla ="compras";

			$datos = $_GET["idventa"];



			$respuesta = ModeloVentas::mdlEliminarVenta($tabla, $datos);



			if ($respuesta == "ok") {

				

				echo'<script>



				swal({

					  type: "success",

					  title: "La venta ha sido borrada correctamente",

					  showConfirmButton: true,

					  confirmButtonText: "Cerrar",

					  closeOnConfirm: false

					  }).then(function(result) {

								if (result.value) {



								window.location = "index.php?ruta=ventasR";



								}

							})



				</script>';



			}

		}

	}



	/*=============================================

	RANGO FECHAS

	=============================================*/	
	static public function ctrRangoFechasVentas($fechaInicial, $fechaFinal, $itemVentas, $valorVentas){

		$tabla = "compras";
		
		$respuesta = ModeloVentas::mdlRangoFechasVentas($tabla, $fechaInicial, $fechaFinal, $itemVentas, $valorVentas);



		return $respuesta;

		

	}

	/*=============================================
	DESCARGAR EXCEL
	=============================================*/
	public function ctrDescargarReporteVentas($valorEmpresa){

		if (isset($_GET["reporte"])) {

			$tabla = "compras";
			$itemEmpresa = "id_empresa";

			if (isset($_GET["fechaInicial"]) && isset($_GET["fechaFinal"])) {


				$ventasFecha =ModeloVentas::mdlRangoFechasVentas($tabla, $_GET["fechaInicial"], $_GET["fechaFinal"], $itemEmpresa, $valorEmpresa);

				$tablaDos = "ordenes";

				$OrdenesPorFecha = ModeloOrdenes::mdlRangoFechasOrdenesENT($tablaDos, $_GET["fechaInicial"], $_GET["fechaFinal"], $itemEmpresa, $valorEmpresa);

			}else{


				$ventasFecha = ModeloVentas::mdlMostrarVentasParaTiket($tabla, $itemEmpresa, $valorEmpresa);

				$estado = "Entregado (Ent)";

				$OrdenesPorFecha =  ModeloOrdenes::mdlMostrarOrdenesPorEstado($tablaDos,$estado, $itemEmpresa, $valorEmpresa);	

			}







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

						<td style='font-weight:bold; border:1px solid #eee;'>ORDEN</td>

						<td style='font-weight:bold; border:1px solid #eee;'>EMPRESA</td>

						<td style='font-weight:bold; border:1px solid #eee;'>ASESOR</td>

						<td style='font-weight:bold; border:1px solid #eee;'>PRODUCTOS</td>

						<td style='font-weight:bold; border:1px solid #eee;'>TOTAL</td>

						<td style='font-weight:bold; border:1px solid #eee;'>FECHA</td>	

					</tr>");





			foreach ($ventasFecha as $key => $value) {

				

				/*=============================================

				TRAER EMPRESA

				=============================================*/

				$item = "id";

				$valor = $value["empresa"];

				$tabla = "empresa";

				$empresa = ModeloVentas::mdlMostrarEmpresasParaTiketimp($tabla, $item, $valor);

			    $NombreEmpresa = $empresa["empresa"];





				//$ElTotal = number_format($total["total"],2);


				


				/*=============================================
				TRAER EMAIL DATOS DE COMPRA
				=============================================*/



					echo utf8_decode("</td>

									 <td style='border:1px solid #eee;'>".$value["id"]."</td>

									 <td style='border:1px solid #eee;'>".$NombreEmpresa."</td>

									 <td style='border:1px solid #eee;'>".$value["asesor"]."</td>

			 					  	 <td style='border:1px solid #eee;'>".$value["productoUno"]." ".$value["productoDos"]." ".$value["productoTres"]."</td>

			 					  	 <td style='border:1px solid #eee;'>".$value["pago"]."</td>

			 					  	 <td style='border:1px solid #eee;'>".$value["fecha"]."</td>

			 					  	 </tr>"); 		



			}
			/*=============================================
				TRAER INFORMACION DE ORDENES
				=============================================*/
				foreach ($OrdenesPorFecha as $key => $valueOrdenesFecha) {
					
					/*=======================
					TARER INFORMACION DE EMPRESA DE ORDEN
					========================*/
					$item = "id";

					$valor = $valueOrdenesFecha["id_empresa"];

					$tablaEmpresa = "empresa";

					$empresaOrden = ModeloVentas::mdlMostrarEmpresasParaTiketimp($tablaEmpresa, $item, $valor);


		            $itemAseor = "id";
		            $valorAsesor = $valueOrdenesFecha["id_Asesor"];

		            $asesorDeOrden = Controladorasesores::ctrMostrarAsesoresEleg($itemAseor,$valorAsesor);
						
		            $TotalDeOrdenes =  $valueOrdenesFecha["total"];
		            $suma += $TotalDeOrdenes;

					echo utf8_decode("</td>

									 <td style='border:1px solid #eee;'>".$valueOrdenesFecha["id"]."</td>

									<td style='border:1px solid #eee;'>".$empresaOrden["empresa"]."</td>

									<td style='border:1px solid #eee;'>".$asesorDeOrden["nombre"]."</td>
									
									<td style='border:1px solid #eee;'>".$valueOrdenesFecha["partidaUno"]."</td>
										

									<td style='border:1px solid #eee;'>".$valueOrdenesFecha["total"]."</td>

									<td style='border:1px solid #eee;'>".$valueOrdenesFecha["fecha_Salida"]."</td>

			 					  	 </tr>"); 		

				}

			    /*=============================================

				TRAER TOTAL

				=============================================*/

				$tabla= "compras";

				$total = ModeloVentas::mdlSumarTotalVentas($tabla, $_GET["fechaInicial"], $_GET["fechaFinal"]);

				foreach ($total as $key => $valueTotal) {

						echo utf8_decode("<tr><td style='font-weight:bold; border:1px solid #eee;'>TOTAL</td></tr>"); 

						
						$corte = $valueTotal["suma"] + $suma;

						echo utf8_decode("</td><td style='border:1px solid #eee;'>$".$ElTotal = $corte."</td>

			 					  	 	</td>"); 

				}







				echo utf8_decode("</table>



					");

		}



	}





	/*=============================================

	CREAR VENTA DINAMICA

	=============================================*/

	static public function ctrCrearVentaDinamica(){



		if(isset($_POST["listaProductos"])){

			

			/*=============================================

			ACTULIZAR LAS COMPRAS DEL CLIENTE Y REDUCIR EL STOCK Y AUMENTAR LA VENTA DE LOS PRODUCTOS

			=============================================*/

			$listaProductos = json_decode($_POST["listaProductos"], true);



			//var_dump($listaProductos);



			$totalProductosComprados = array();



			foreach ($listaProductos as $key => $value) {



			}



				//var_dump($value["cantidad"]);

				array_push($totalProductosComprados, $value["cantidad"]);



				$tablaProductos = "productos";



				$item = "id";

				$valor = $value["id"];



				$traerProductos = ModeloProductos::mdlMostrarProductos($tablaProductos,$item,$valor);



				

				foreach ($traerProductos as $key => $valueVentas) {

					

					 //var_dump($valueVentas["ventas"]);

					 $item1a = "ventas";

					 $valor1a = $value["cantidad"] + $valueVentas["ventas"];



					 //var_dump("actualizar",$valor1a);

					 $nuevasVentas = ModeloProductos::mdlActualizarProductoVentasDinamicas($tablaProductos, $item1a, $valor1a, $valor);



					 $item1b = "disponibilidad";

					 $valor1b = $value["stock"];

					 //var_dump($valor1b);

					$nuevoStock = ModeloProductos::mdlActualizarProductoVentasDinamicas($tablaProductos, $item1b, $valor1b, $valor);

				}



				



				//TRAER A LOS CLIENTES

				$tablaClientes = "clientesTienda";

				$item="id";

				$valor=$_POST["seleccionarCliente"];



				$traerCliente = ModeloClientes::mdlMostrarClientes($tablaClientes,$item,$valor);



				//var_dump($traerCliente);





				//ACTUALIZAR LAS COMPRAS DE LOS CLIENTES

				$itemCliente = "compras";

				$valorCliente = array_sum($totalProductosComprados) + $traerCliente["compras"];



				$actualizacComprasCliente = ModeloClientes::mdlActualizarCantidadDeComprasCliente($tablaClientes, $itemCliente, $valorCliente, $valor);



				/*=============================================

				GUARDAR LA VENTA

				=============================================*/

				$tablaVentas = "compras";



				$datos = array("id_usuario" =>$_POST["seleccionarCliente"],

							   "id_Asesor" =>$_POST["asesor"],

							   "productos" =>$_POST["listaProductos"],

							   "inversion" =>$_POST["nversion"],

							   "impuesto" =>$_POST["nuevoImpuestoVenta"],

							   "neto"=>$_POST["precioNeto"],

							   "pago" =>$_POST["totalVenta"],

							   "id_empresa" =>$_POST["empresa"],

							   "metodo" =>$_POST["listaMetodoPago"]);



				$respuesta = ModeloVentas::mdlIngresarVentaDinamica($tablaVentas,$datos);

				// ── Recompensas: canjear dinero electrónico si aplica ──
				$_egs_idClienteVentaDin = isset($_POST["id_cliente"]) ? intval($_POST["id_cliente"]) : intval($_POST["seleccionarCliente"]);
				$_egs_montoCanjeVentaDin = isset($_POST["montoCanjeElectronicoVenta"]) ? floatval($_POST["montoCanjeElectronicoVenta"]) : 0;

				if ($respuesta == "ok" && $_egs_montoCanjeVentaDin > 0 && $_egs_idClienteVentaDin > 0) {
					try {
						require_once "recompensas.controlador.php";
						require_once __DIR__ . "/../modelos/recompensas.modelo.php";
						$ultimaVenta = ModeloVentas::mdlObtenerUltimaVenta(intval($_POST["empresa"]));
						if ($ultimaVenta > 0) {
							$canjeExistente = ModeloRecompensas::mdlObtenerCanjeVenta($ultimaVenta);
							if (!$canjeExistente) {
								ControladorRecompensas::ctrCanjearRecompensaVenta($_egs_idClienteVentaDin, $ultimaVenta, $_egs_montoCanjeVentaDin);
							}
						}
					} catch (Exception $e) {
						// No bloquear la venta si falla el canje
					}
				}

				if ($respuesta == "ok") {

					

						echo '<script>



					swal({



						type: "success",

						title: "¡La venta se ha realizado correctamente!",

						showConfirmButton: true,

						confirmButtonText: "Cerrar"



					}).then(function(result){



						if(result.value){

						

							window.location = "index.php?ruta=ventasD";



						}



					});

				



					</script>';

				}else{



						echo '<script>



					swal({



						type: "error",

						title: "¡Los campos no pueden ir vacíos o llevar caracteres especiales!",

						showConfirmButton: true,

						confirmButtonText: "Cerrar"



					}).then(function(result){



						if(result.value){

						

							window.location = "index.php?ruta=ventasD";



						}



					});

				



				</script>';



				}

			

		}



	}



}