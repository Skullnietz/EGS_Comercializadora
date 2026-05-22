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
AGREGAR VENTA RAPIDA
=============================================*/
     public function ctrCrearventa(){

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
							   "cantidadProductos" => $_POST["cantidadProductos"],
							   "asesor" => $_POST["asesor"],
 							   "pago" => $_POST["pago"],							   
							   );


				$respuesta = ModeloVentas::mdlIngresarVenta($tabla, $datos);
				// mdlIngresarVenta ahora devuelve el ID insertado (> 0) o 0 en error
				if ($respuesta > 0) {
					
						echo '<script>

					swal({

						type: "success",
						title: "¡La venta se ha realizado correctamente!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
						
							window.location = "ventasR";

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
						
							window.location = "ventasR";

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

								window.location = "ventasR";

								}
							})

				</script>';

			}
		}
	}
}