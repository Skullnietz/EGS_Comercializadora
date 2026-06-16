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

				if ($respuesta !== "error") {

					// $respuesta es el id de la observación (int) o "ok" (duplicado sin id)
					$idObservacion = is_numeric($respuesta) ? intval($respuesta) : 0;

					// ── Guardar fotos adjuntas (si las hay) ──
					if ($idObservacion > 0) {
						self::ctrlGuardarFotosObservacion($idObservacion, intval($_POST["id_orden"]));
					}

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
	GUARDAR FOTOS ADJUNTAS A UNA OBSERVACIÓN
	Reutiliza el optimizador de imágenes de controladorOrdenes.
	Carpetas por año-mes para poder borrar por espacio con el tiempo.
	=============================================*/
	static private function ctrlGuardarFotosObservacion($idObservacion, $idOrden){

		if (empty($_FILES["fotosObs"]) || !isset($_FILES["fotosObs"]["tmp_name"])) {
			return;
		}

		if (!class_exists("controladorOrdenes")) {
			return; // optimizador no disponible
		}

		$tablaFotos = "observacionesFotos";
		$nombres    = $_FILES["fotosObs"]["name"];
		$maxFotos   = 8;

		// Normalizar a arreglo (input multiple => arreglos paralelos)
		if (!is_array($nombres)) {
			$nombres = array($nombres);
			$_FILES["fotosObs"]["tmp_name"] = array($_FILES["fotosObs"]["tmp_name"]);
			$_FILES["fotosObs"]["type"]     = array($_FILES["fotosObs"]["type"]);
			$_FILES["fotosObs"]["size"]     = array($_FILES["fotosObs"]["size"]);
			$_FILES["fotosObs"]["error"]    = array($_FILES["fotosObs"]["error"]);
		}

		$total = min(count($nombres), $maxFotos);

		// Ruta absoluta (independiente del CWD): este flujo corre desde index.php en la raíz,
		// así que "../vistas" no sirve. __DIR__ apunta a /controladores.
		$subRuta    = date("Y-m") . "/" . intval($idOrden);
		$directorio = __DIR__ . "/../vistas/img/observaciones/" . $subRuta;
		$rutaWebBase = "vistas/img/observaciones/" . $subRuta;

		for ($i = 0; $i < $total; $i++) {

			if (empty($_FILES["fotosObs"]["tmp_name"][$i])) continue;
			if (isset($_FILES["fotosObs"]["error"][$i]) && $_FILES["fotosObs"]["error"][$i] !== UPLOAD_ERR_OK) continue;

			$archivo = array(
				"name"     => $_FILES["fotosObs"]["name"][$i],
				"type"     => $_FILES["fotosObs"]["type"][$i],
				"tmp_name" => $_FILES["fotosObs"]["tmp_name"][$i],
				"error"    => $_FILES["fotosObs"]["error"][$i],
				"size"     => $_FILES["fotosObs"]["size"][$i]
			);

			$nombreBase = "obs-" . $idObservacion . "-" . ($i + 1) . "-" . substr(md5(uniqid('', true)), 0, 8);

			$procesada = controladorOrdenes::ctrOptimizarImagenSubida($archivo, array(
				"directorio" => $directorio,
				"nombre"     => $nombreBase,
				"ancho"      => 1600,
				"alto"       => 1600,
				"modo"       => "contain",
				"max_bytes"  => 10485760
			));

			if (!is_array($procesada) || empty($procesada["ok"])) {
				continue; // saltar la que falle
			}

			// Ruta web relativa (accesible y portable) a partir del archivo realmente guardado
			$rutaWeb = $rutaWebBase . "/" . basename($procesada["ruta"]);

			ModeloObservaciones::mdlCrearFotoObservacion($tablaFotos, array(
				"id_observacion" => $idObservacion,
				"id_orden"       => $idOrden,
				"ruta"           => $rutaWeb
			));
		}
	}

	/*=============================================
	MOSTRAR FOTOS DE LAS OBSERVACIONES DE UNA ORDEN
	=============================================*/
	static public function ctrMostrarFotosPorOrden($idOrden){

		$tabla = "observacionesFotos";

		return ModeloObservaciones::mdlMostrarFotosPorOrden($tabla, $idOrden);

	}

	/*=============================================
	OBSERVACIONES RECIENTES PARA NOTIFICACIÓN
	=============================================*/
	static public function ctrObservacionesRecientesNotif($idUsuario, $limite = 15, $ordenIds = null){

		$tabla = "observacionesOrdenes";

		return ModeloObservaciones::mdlObservacionesRecientesNotif($tabla, $idUsuario, $limite, $ordenIds);

	}
		/*=============================================

	ELIMINAR Observacion

	=============================================*/



	static public function ctrEliminarObservacion(){



		if(isset($_GET["idobs"])){

		    $tabla = "observacionesOrdenes";
			$datos = $_GET["idobs"];

			// ── Borrar primero las fotos físicas y sus registros (evita huérfanos) ──
			$rutasFotos = ModeloObservaciones::mdlEliminarFotosPorObservacion("observacionesFotos", $datos);
			if (is_array($rutasFotos)) {
				foreach ($rutasFotos as $rutaFoto) {
					// Ruta absoluta independiente del CWD (__DIR__ = /controladores)
					$rutaFisica = __DIR__ . "/../" . ltrim($rutaFoto, "/");
					if (!empty($rutaFoto) && file_exists($rutaFisica)) {
						@unlink($rutaFisica);
					}
				}
			}

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

	/*=============================================
	ÚLTIMAS OBSERVACIONES FILTRADAS POR ÓRDENES
	=============================================*/

	static public function ctrUltimasObservacionesPorOrdenes($idsOrdenes, $limite = 20){

		$tabla = "observacionesOrdenes";

		return ModeloObservaciones::mdlUltimasObservacionesPorOrdenes($tabla, $idsOrdenes, $limite);

	}
}