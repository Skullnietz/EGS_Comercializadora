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

				$_egs_montoCanjeVenta = isset($_POST["montoCanjeElectronicoVenta"]) ? floatval($_POST["montoCanjeElectronicoVenta"]) : 0;
				$_egs_pagoOriginal    = floatval($_POST["pago"]);

				if ($_egs_montoCanjeVenta > 0) {
					$datos["total_antes_monedero"]    = $_egs_pagoOriginal;
					$datos["monto_monedero_aplicado"] = $_egs_montoCanjeVenta;
					$datos["pago"]                    = max(0, $_egs_pagoOriginal - $_egs_montoCanjeVenta);
				}

				$idVentaInsertada = ModeloVentas::mdlIngresarVenta($tabla, $datos);

				// ── Recompensas: canjear con el ID real de la venta ──
				$_egs_idClienteVenta = isset($_POST["id_cliente"]) ? intval($_POST["id_cliente"]) : 0;

				if ($idVentaInsertada > 0 && $_egs_montoCanjeVenta > 0 && $_egs_idClienteVenta > 0) {
					try {
						require_once "recompensas.controlador.php";
						require_once __DIR__ . "/../modelos/recompensas.modelo.php";
						ControladorRecompensas::ctrCanjearEnVenta(
							$_egs_idClienteVenta,
							$idVentaInsertada,
							$_egs_montoCanjeVenta,
							intval($_POST["empresa"]),
							null,
							$_egs_pagoOriginal,
							floatval($datos["pago"])
						);
					} catch (Exception $e) {
						// No bloquear la venta si falla el canje
					}
				}

				$respuesta = $idVentaInsertada > 0 ? "ok" : "error";

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

		if(!isset($_POST["listaProductos"])){
			return;
		}

		$listaProductos = json_decode($_POST["listaProductos"], true);
		$idCliente = isset($_POST["id_cliente"]) ? intval($_POST["id_cliente"]) : intval($_POST["seleccionarCliente"]);
		$idEmpresa = isset($_POST["empresa"]) ? intval($_POST["empresa"]) : 0;
		$idAsesor = isset($_POST["asesor"]) ? intval($_POST["asesor"]) : 0;

		if(!is_array($listaProductos) || count($listaProductos) === 0){
			self::ctrRespuestaVentaDinamica("error", "El carrito está vacío. Agrega al menos un producto.");
			return;
		}

		if($idCliente <= 0){
			self::ctrRespuestaVentaDinamica("error", "Debes seleccionar un cliente para registrar la venta.");
			return;
		}

		if($idAsesor <= 0){
			self::ctrRespuestaVentaDinamica("error", "Debes seleccionar un asesor para registrar la venta.");
			return;
		}

		$tablaProductos = "productos";
		$totalProductosComprados = array();
		$productosActualizados = array();

		foreach ($listaProductos as $value) {
			$idProducto = isset($value["id"]) ? intval($value["id"]) : 0;
			$cantidad = isset($value["cantidad"]) ? intval($value["cantidad"]) : 0;

			if($idProducto <= 0 || $cantidad <= 0){
				self::ctrRespuestaVentaDinamica("error", "Hay productos con cantidad inválida en el carrito.");
				return;
			}

			$traerProductos = ModeloProductos::mdlMostrarProductos($tablaProductos, "id", $idProducto);
			if(empty($traerProductos)){
				self::ctrRespuestaVentaDinamica("error", "Uno de los productos ya no existe en el catálogo.");
				return;
			}

			$productoDb = $traerProductos[0];
			$stockActual = intval($productoDb["disponibilidad"]);

			if($stockActual < $cantidad){
				$titulo = isset($productoDb["titulo"]) ? $productoDb["titulo"] : "Producto";
				self::ctrRespuestaVentaDinamica("error", "Stock insuficiente para \"".$titulo."\". Disponible: ".$stockActual.".");
				return;
			}

			array_push($totalProductosComprados, $cantidad);

			$nuevoStock = max(0, $stockActual - $cantidad);
			$nuevasVentas = intval($productoDb["ventas"]) + $cantidad;

			ModeloProductos::mdlActualizarProductoVentasDinamicas($tablaProductos, "ventas", $nuevasVentas, $idProducto);
			ModeloProductos::mdlActualizarProductoVentasDinamicas($tablaProductos, "disponibilidad", $nuevoStock, $idProducto);

			$productosActualizados[] = array(
				"id" => $idProducto,
				"titulo" => isset($value["titulo"]) ? $value["titulo"] : $productoDb["titulo"],
				"codigo" => isset($value["codigo"]) ? $value["codigo"] : $productoDb["codigo"],
				"cantidad" => $cantidad,
				"precio" => isset($value["precio"]) ? $value["precio"] : $productoDb["precio"],
				"stock" => $nuevoStock,
				"medida" => isset($value["medida"]) ? $value["medida"] : $productoDb["medida"],
				"total" => isset($value["total"]) ? $value["total"] : (floatval($value["precio"]) * $cantidad)
			);
		}

		$tablaClientes = "clientesTienda";
		$traerCliente = ModeloClientes::mdlMostrarClientes($tablaClientes, "id", $idCliente);
		if(empty($traerCliente)){
			self::ctrRespuestaVentaDinamica("error", "El cliente seleccionado no existe.");
			return;
		}

		$valorCliente = array_sum($totalProductosComprados) + intval($traerCliente["compras"]);
		ModeloClientes::mdlActualizarCantidadDeComprasCliente($tablaClientes, "compras", $valorCliente, $idCliente);

		$tablaVentas = "compras";
		$listaProductosJson = json_encode($productosActualizados);

		$datos = array(
			"id_usuario" => $idCliente,
			"id_Asesor" => $idAsesor,
			"productos" => $listaProductosJson,
			"inversion" => isset($_POST["nversion"]) ? $_POST["nversion"] : 0,
			"impuesto" => isset($_POST["nuevoImpuestoVenta"]) ? $_POST["nuevoImpuestoVenta"] : 0,
			"neto" => isset($_POST["precioNeto"]) ? $_POST["precioNeto"] : 0,
			"pago" => $_POST["totalVenta"],
			"id_empresa" => $idEmpresa,
			"metodo" => $_POST["listaMetodoPago"]
		);

		$_egs_montoCanjeVentaDin = isset($_POST["montoCanjeElectronicoVenta"]) ? floatval($_POST["montoCanjeElectronicoVenta"]) : 0;
		$_egs_pagoOriginalDin = floatval($_POST["totalVenta"]);

		if ($_egs_montoCanjeVentaDin > $_egs_pagoOriginalDin) {
			$_egs_montoCanjeVentaDin = $_egs_pagoOriginalDin;
		}

		if ($_egs_montoCanjeVentaDin > 0) {
			$datos["total_antes_monedero"] = $_egs_pagoOriginalDin;
			$datos["monto_monedero_aplicado"] = $_egs_montoCanjeVentaDin;
			$datos["pago"] = max(0, $_egs_pagoOriginalDin - $_egs_montoCanjeVentaDin);
		}

		$idVentaDinamicaInsertada = ModeloVentas::mdlIngresarVentaDinamica($tablaVentas, $datos);

		if ($idVentaDinamicaInsertada > 0 && $_egs_montoCanjeVentaDin > 0 && $idCliente > 0) {
			try {
				require_once "recompensas.controlador.php";
				require_once __DIR__ . "/../modelos/recompensas.modelo.php";
				ControladorRecompensas::ctrCanjearEnVenta(
					$idCliente,
					$idVentaDinamicaInsertada,
					$_egs_montoCanjeVentaDin,
					$idEmpresa,
					null,
					$_egs_pagoOriginalDin,
					floatval($datos["pago"])
				);
			} catch (Exception $e) {
				// No bloquear la venta si falla el canje
			}
		}

		if ($idVentaDinamicaInsertada > 0) {
			self::ctrRespuestaVentaDinamica("ok", "¡La venta se ha realizado correctamente!", $idVentaDinamicaInsertada, $idEmpresa, $idAsesor);
			return;
		}

		self::ctrRespuestaVentaDinamica("error", "No se pudo guardar la venta. Verifica los datos e intenta de nuevo.");
	}

	private static function ctrRespuestaVentaDinamica($tipo, $mensaje, $idVenta = 0, $idEmpresa = 0, $idAsesor = 0){
		$tipoJs = $tipo === "ok" ? "success" : "error";
		$idVentaJs = intval($idVenta);
		$idEmpresaJs = intval($idEmpresa);
		$idAsesorJs = intval($idAsesor);
		$mensajeJs = addslashes($mensaje);

		echo '<script>
		if (typeof window.egsPosVentaCompletada === "function") {
			window.egsPosVentaCompletada("'.$tipoJs.'", "'.$mensajeJs.'", '.$idVentaJs.', '.$idEmpresaJs.', '.$idAsesorJs.');
		} else {
			swal({
				type: "'.$tipoJs.'",
				title: "'.$mensajeJs.'",
				showConfirmButton: true,
				confirmButtonText: "Cerrar"
			}).then(function(result){
				if (result.value && "'.$tipoJs.'" === "success") {
					window.location = "index.php?ruta=ventasD";
				}
			});
		}
		</script>';
	}



}