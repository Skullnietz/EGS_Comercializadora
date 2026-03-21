<?php
class controladorObservaciones{
	
	/*=============================================
	MOSTRAR OBSERVACIONES
	=============================================*/

	static public function ctrMostrarobservaciones($itemobs){

		$tabla = "observacionesOrdenes";

		$respuesta = ModeloObservaciones::mdlMostrarobservaciones($tabla, $itemobs);

		return $respuesta;
		
	
	}
	/*=============================================
	MOSTRAR OBSERVACIONES INFO DE USUARIO
	=============================================*/

	static public function ctrMostrarInfoUser($idadmin){

		$tabla = "administradores";

		$respuesta = ModeloObservaciones::mdlMostrarInfoUser($tabla, $idadmin);

		return $respuesta;
		
	
	}
	/*=============================================
	ÚLTIMAS OBSERVACIONES GLOBALES
	=============================================*/

	static public function ctrUltimasObservaciones($limite = 12){

		$tabla = "observacionesOrdenes";

		return ModeloObservaciones::mdlUltimasObservaciones($tabla, $limite);

	}

	/*=============================================
	OBSERVACIONES DE HOY
	=============================================*/

	static public function ctrObservacionesHoy(){

		$tabla = "observacionesOrdenes";

		return ModeloObservaciones::mdlObservacionesHoy($tabla);

	}

	/*=============================================
	INSERTAR OBSERVACIONES (con token anti-duplicado)
	=============================================*/
	static public function ctrlCrearObservacion(){

		if (isset($_POST["observacion"])){

			// ── Token anti-duplicado: evita que refrescar la página reenvíe el form ──
			if (!isset($_SESSION)) session_start();
			$token = isset($_POST["_obs_token"]) ? $_POST["_obs_token"] : "";
			if (!empty($token) && isset($_SESSION["_obs_token_used"]) && $_SESSION["_obs_token_used"] === $token) {
				// Token ya fue usado, no procesar de nuevo — redirigir limpio
				echo '<script>window.history.back();</script>';
				return;
			}

			$tabla = "observacionesOrdenes";

			$datos = array("id_creador" => $_POST["id_creador"],
						   "id_orden" => $_POST["id_orden"],
						   "observacion" => $_POST["observacion"]
						    );

			$respuesta = ModeloObservaciones::mdlCrearObservacion($tabla, $datos);

				if ($respuesta == "ok") {

					// Marcar token como usado
					if (!empty($token)) {
						$_SESSION["_obs_token_used"] = $token;
					}

					echo '<script>

					swal({

						type: "success",
						title: "!La observacion guardado correctamente!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){

						    window.history.back();


						}

					});


					</script>';
				}

			}

	}

	/*=============================================
	OBSERVACIONES RECIENTES PARA NOTIFICACIÓN
	=============================================*/
	static public function ctrObservacionesRecientesNotif($idUsuario, $limite = 15){

		$tabla = "observacionesOrdenes";

		return ModeloObservaciones::mdlObservacionesRecientesNotif($tabla, $idUsuario, $limite);

	}
		/*=============================================

	ELIMINAR Observacion

	=============================================*/



	static public function ctrEliminarObservacion(){



		if(isset($_GET["idobs"])){
		    
		    $tabla = "observacionesOrdenes";
			$datos = $_GET["idobs"];



			$respuesta = ModeloObservaciones::mdlEliminarObservacion($tabla,$datos);



			if($respuesta == "ok"){



				echo'<script>



				swal({

					  type: "success",

					  title: "La orden ha sido borrado correctamente",

					  showConfirmButton: true,

					  confirmButtonText: "Cerrar"

					  }).then(function(result){

								if (result.value) {



								window.history.back();



								}

							})



				</script>';



			}		







		}



	}
}