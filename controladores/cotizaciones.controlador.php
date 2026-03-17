<?php


class CotizacionesControlador
{

	/*=============================================
	MOSTRAR EL TOTAL DE VENTAS
	=============================================*/
	/*=============================================
	CREAR COTIZACION
	=============================================*/
	static public function ctrCrearCotizacion()
	{

		if (isset($_POST["empresa"])) {

			// Verificar token para evitar reenvíos (refresh)
			if (session_status() == PHP_SESSION_NONE) {
				session_start();
			}
			if (!isset($_POST['form_token']) || !isset($_SESSION['form_token']) || $_POST['form_token'] !== $_SESSION['form_token']) {
				// Token inválido o ya usado. No procesar.
				// Opcional: Mostrar mensaje de error o simplemente no hacer nada (idempotencia)
				return;
			}
			// Consumir el token
			unset($_SESSION['form_token']);

			$tabla = "cotizaciones";

			$datos = array(
				"id_cliente" => $_POST["id_cliente"],
				"nombre_cliente" => $_POST["nombre_cliente"],
				"id_vendedor" => $_POST["id_vendedor"],
				"empresa" => $_POST["empresa"],
				"productos" => $_POST["listaProductos"],
				"impuesto" => $_POST["impuesto"],
				"neto" => $_POST["neto"],
				"total" => $_POST["total"],
				"asunto" => $_POST["asunto"],
				"vigencia" => $_POST["vigencia"],
				"observaciones" => $_POST["observaciones"],
				"descuento_porcentaje" => $_POST["descuento_porcentaje"],
				"codigo_qr" => $_POST["codigo_qr"]
			);

			$respuesta = CotizacionModelo::mdlIngresarCotizacion($tabla, $datos);

			if ($respuesta == "ok") {

				echo '<script>

					Swal.fire({
						  icon: "success",
						  title: "La cotización ha sido guardada correctamente",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result){
									if (result.value) {

									window.location = "cotizaciones";

									}
								})

					</script>';

			} else {

				echo '<script>

					Swal.fire({
						  icon: "error",
						  title: "¡La cotización no se pudo guardar!",
						  text: "Ocurrió un error en la base de datos o faltan datos obligatorios.",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result){
							if (result.value) {
								window.location = "cotizaciones";
							}
						})

				</script>';

			}

		}

	}



	/*=============================================
	MOSTRAR COTIZACIONES (Placeholder)
	=============================================*/
	static public function ctrcotizacionescontrolador()
	{
		$tabla = "clientesTienda";
		$respuesta = CotizacionModelo::mdlcotizacion($tabla);
		return $respuesta;
	}
	
	/*=============================================
    MOSTRAR COTIZACIONES
    =============================================*/
	
	static public function ctrMostrarCotizaciones($item, $valor)
    {

        $tabla = "cotizaciones";

        $respuesta = CotizacionModelo::mdlMostrarCotizaciones($tabla, $item, $valor);

        return $respuesta;

    }



}