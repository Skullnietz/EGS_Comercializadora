<?php

class controladorOrdenes
{



	static public function ctrMostrarOrdenes($campo, $empresa)
	{



		$tabla = "ordenes";



		$respuesta = ModeloOrdenes::mdlMostrarOrdenes($tabla, $campo, $empresa);



		return $respuesta;

	}
	static public function ctrMostrarComisionesPorPersonaPrimera($session_id)
	{



		$tabla = "ordenes";



		$respuesta = ModeloOrdenes::mdlMostrarComisionesPorPersonaPrimera($tabla, $session_id);



		return $respuesta;

	}
	static public function ctrMostrarComisionesPorPersonaSegunda($session_id2)
	{



		$tabla = "ordenes";



		$respuesta = ModeloOrdenes::mdlMostrarComisionesPorPersonaSegunda($tabla, $session_id2);



		return $respuesta;

	}
	static public function ctrMostrarOrdenesMaterial()
	{



		$tabla = "ordenes";



		$respuesta = ModeloOrdenes::mdlMostrarOrdenesMaterial($tabla);



		return $respuesta;

	}
	static public function ctrMostrarHistorial($tabla, $valor)
	{



		$respuesta = ModeloOrdenes::mdlMostrarHistorial($tabla, $valor);



		return $respuesta;

	}
	static public function ctrUltimaEntrega()
	{



		$tabla = "ordenes";



		$respuesta = ModeloOrdenes::mdlUltimaEntrega($tabla);



		return $respuesta;

	}

	static public function ctrMostrarOrdenesNew($campo, $empresa)
	{



		$tabla = "ordenes";



		$respuesta = ModeloOrdenes::mdlMostrarOrdenesNew($tabla, $campo, $empresa);



		return $respuesta;

	}

	/*=============================================

	MOSTRAR ORDENES POR EMPRESA Y PERFIL ASESOR

	=============================================*/

	static public function ctrlMostrarordenesEmpresayPerfil($itemOrdenes, $valorOrdenes, $iteDosOrdenes, $valorDosOrdenes)
	{



		$tabla = "ordenes";



		$respuesta = ModeloOrdenes::mdlMostrarordenesEmpresayPerfil($tabla, $itemOrdenes, $valorOrdenes, $iteDosOrdenes, $valorDosOrdenes);



		return $respuesta;

	}

	/*=============================================

	MOSTRAR ORDENES PARA SUMAR

	=============================================*/

	static public function ctrMostrarOrdenesSuma()
	{



		$tabla = "ordenes";



		$respuesta = ModeloOrdenes::mdlMostrarOrdenesSuma($tabla);



		return $respuesta;

	}
	/*=============================================

MOSTRAR ORDENES PARA SUMAR DEL ASESOR

=============================================*/

	static public function ctrMostrarOrdenesSumaAsesor($idAsesor)
	{



		$tabla = "ordenes";



		$respuesta = ModeloOrdenes::mdlMostrarOrdenesSumaAsesor($tabla, $idAsesor);



		return $respuesta;

	}

	/*=============================================

	MOSTRAR ORDENES

	=============================================*/



	static public function ctrMostrarordenesParaValidar($item, $valor)
	{



		$tabla = "ordenes";



		$respuesta = ModeloOrdenes::mdlMostrarordenesParaValidar($tabla, $item, $valor);



		return $respuesta;



	}





	/*=============================================

	MOSTRAR ORDENES ORDENADAS

	=============================================*/



	static public function ctrMostrarOrdenesOrdenadas($ordenar, $item, $valor, $base, $tope, $modo)
	{



		$tabla = "ordenes";



		$respuesta = ModeloOrdenes::mdlMostrarordenesOrdenadas($tabla, $ordenar, $item, $valor, $base, $tope, $modo);



		return $respuesta;



	}



	static public function ctrSubirMultimediaOrden($datos, $ruta)
	{





		if (isset($datos["tmp_name"]) && !empty($datos["tmp_name"])) {





			/*=============================================

			DEFINIMOS LAS MEDIDAS

			=============================================*/

			list($ancho, $alto) = getimagesize($datos["tmp_name"]);



			$nuevoAncho = 1000;

			$nuevoAlto = 1000;



			/*=============================================

			CREAMOS EL DIRECTORIO DONDE VAMOS A GUARDAR LA FOTO DE LA MULTIMEDIA

			=============================================*/

			$directorio = "../vistas/img/multimedia/" . $ruta;





			if (!file_exists($directorio)) {



				mkdir($directorio, 0755);



			}



			/*=============================================

			DE ACUERDO AL TIPO DE IMAGEN APLICAMOS LAS FUNCIONES POR DEFECTO DE PHP

			=============================================*/



			if ($datos["type"] == "image/jpeg") {



				/*=============================================

				GUARDAMOS LA IMAGEN EN EL DIRECTORIO

				=============================================*/



				$rutaMultimedia = $directorio . "/" . $datos["name"];



				$origen = imagecreatefromjpeg($datos["tmp_name"]);



				$destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);



				imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);



				imagejpeg($destino, $rutaMultimedia);



			}



			if ($datos["type"] == "image/png") {



				/*=============================================

				GUARDAMOS LA IMAGEN EN EL DIRECTORIO

				=============================================*/



				$rutaMultimedia = $directorio . "/" . $datos["name"];



				$origen = imagecreatefrompng($datos["tmp_name"]);



				$destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);



				imagealphablending($destino, FALSE);



				imagesavealpha($destino, TRUE);



				//imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);



				imagepng($destino, $rutaMultimedia);



			}



			return $rutaMultimedia;



		}

	}



	/*=============================================

	CREAR ORDEN

	=============================================*/



	static public function ctrCrearOrden($datos)
	{



		if (isset($datos["tituloOrden"]) and isset($datos["empresa"]) and isset($datos["status"])) {





			if (preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $datos["tituloOrden"]) && preg_match('/^[,\\.\\a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["descripcionOrden"])) {



				/*=============================================

				VALIDAR IMAGEN PORTADA

				=============================================*/



				$rutaPortada = "../vistas/img/default/default.png";



				if (isset($datos["fotoPortada"]["tmp_name"]) && !empty($datos["fotoPortada"]["tmp_name"])) {



					/*=============================================

					DEFINIMOS LAS MEDIDAS

					=============================================*/



					list($ancho, $alto) = getimagesize($datos["fotoPortada"]["tmp_name"]);



					$nuevoAncho = 1280;

					$nuevoAlto = 720;





					/*=============================================

					DE ACUERDO AL TIPO DE IMAGEN APLICAMOS LAS FUNCIONES POR DEFECTO DE PHP

					=============================================*/



					if ($datos["fotoPortada"]["type"] == "image/jpeg") {



						/*=============================================

						GUARDAMOS LA IMAGEN EN EL DIRECTORIO

						=============================================*/



						$aleatorio = mt_rand(100, 999);



						$rutaPortada = "../vistas/img/cabeceras/" . $datos["rutaOrden"] . ".jpg";



						$origen = imagecreatefromjpeg($datos["fotoPortada"]["tmp_name"]);

						$destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);



						imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);



						imagejpeg($destino, $rutaPortada);



					}



					if ($datos["fotoPortada"]["type"] == "image/png") {



						/*=============================================

						GUARDAMOS LA IMAGEN EN EL DIRECTORIO

						=============================================*/



						$aleatorio = mt_rand(100, 999);



						$rutaPortada = "../vistas/img/cabeceras/" . $datos["rutaOrden"] . ".png";



						$origen = imagecreatefrompng($datos["fotoPortada"]["tmp_name"]);



						$destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);



						imagealphablending($destino, FALSE);



						imagesavealpha($destino, TRUE);



						imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);



						imagepng($destino, $rutaPortada);



					}



				}



				/*=============================================

				VALIDAR IMAGEN PRINCIPAL

				=============================================*/



				$rutaFotoPrincipal = "../vistas/img/default/default.png";



				if (isset($datos["fotoPrincipal"]["tmp_name"]) && !empty($datos["fotoPrincipal"]["tmp_name"])) {



					/*=============================================

					DEFINIMOS LAS MEDIDAS

					=============================================*/



					list($ancho, $alto) = getimagesize($datos["fotoPrincipal"]["tmp_name"]);



					$nuevoAncho = 400;

					$nuevoAlto = 450;



					/*=============================================

					DE ACUERDO AL TIPO DE IMAGEN APLICAMOS LAS FUNCIONES POR DEFECTO DE PHP

					=============================================*/



					if ($datos["fotoPrincipal"]["type"] == "image/jpeg") {



						/*=============================================

						GUARDAMOS LA IMAGEN EN EL DIRECTORIO

						=============================================*/



						$aleatorio = mt_rand(100, 999);



						$rutaFotoPrincipal = "../vistas/img/productos/" . $datos["rutaOrden"] . ".jpg";



						$origen = imagecreatefromjpeg($datos["fotoPrincipal"]["tmp_name"]);



						$destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);



						imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);



						imagejpeg($destino, $rutaFotoPrincipal);



					}



					if ($datos["fotoPrincipal"]["type"] == "image/png") {



						/*=============================================

						GUARDAMOS LA IMAGEN EN EL DIRECTORIO

						=============================================*/



						$aleatorio = mt_rand(100, 999);



						$rutaFotoPrincipal = "../vistas/img/productos/" . $datos["rutaOrden"] . ".png";



						$origen = imagecreatefrompng($datos["fotoPrincipal"]["tmp_name"]);



						$destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);



						imagealphablending($destino, FALSE);



						imagesavealpha($destino, TRUE);



						imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);



						imagepng($destino, $rutaFotoPrincipal);



					}



				}



				date_default_timezone_set("America/Mexico_City");



				$fechaDeIngreso = date('Y-m-d H:i:s');



				$datosOrden = array(

					"titulo" => $datos["tituloOrden"],

					"empresa" => $datos["empresa"],

					"multimedia" => $datos["multimedia"],

					"ruta" => $datos["rutaOrden"],

					//"creador"=>$_SESSION["id"],

					"tecnico" => $datos["tecnico"],

					"asesor" => $datos["asesor"],

					"cliente" => $datos["cliente"],

					"status" => $datos["status"],

					"descripcion" => $datos["descripcionOrden"],

					"partida1" => $datos["partida1"],

					"partida2" => $datos["partida2"],

					"partida3" => $datos["partida3"],

					"partida4" => $datos["partida4"],

					"partida5" => $datos["partida5"],

					"partida6" => $datos["partida6"],

					"partida7" => $datos["partida7"],

					"partida8" => $datos["partida8"],

					"partida9" => $datos["partida9"],

					"partida10" => $datos["partida10"],

					"precio1" => $datos["precio1"],

					"precio2" => $datos["precio2"],

					"precio3" => $datos["precio3"],

					"precio4" => $datos["precio4"],

					"precio5" => $datos["precio5"],

					"precio6" => $datos["precio6"],

					"precio7" => $datos["precio7"],

					"precio8" => $datos["precio8"],

					"precio9" => $datos["precio9"],

					"precio10" => $datos["precio10"],

					"totalOrden" => $datos["totalOrden"],

					"imgPortada" => substr($rutaPortada, 3),

					"imgFotoPrincipal" => substr($rutaFotoPrincipal, 3),

					"fecha_ingreso" => $fechaDeIngreso,

					"marcaDelEquipo" => $datos["marcaDelEquipo"],

					"modeloDelEquipo" => $datos["modeloDelEquipo"],

					"numeroDeSerieDelEquipo" => $datos["numeroDeSerieDelEquipo"]

				);



				$respuesta = ModeloOrdenes::mdlIngresarOrden("ordenes", $datosOrden);



				return $respuesta;





			} else {



				echo '<script>



					swal({

						  type: "error",

						  title: "¡El nombre del orden no puede ir vacía o llevar caracteres especiales!",

						  showConfirmButton: true,

						  confirmButtonText: "Cerrar"

						  }).then(function(result){

							if (result.value) {



							window.location = "index.php?ruta=ordenes";



							}

						})



			  	</script>';







			}



		}



	}



	/*=============================================

	EDITAR ORDENES

	=============================================*/



	static public function ctrEditarOrden($datos)
	{



		if (isset($datos["idOrden"])) {



			if (preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $datos["tituloOrden"]) && preg_match('/^[,\\.\\a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["descripcionOrden"])) {



				/*=============================================

				ELIMINAR LAS FOTOS DE MULTIMEDIA DE LA CARPETA

				=============================================*/



				$item = "id";

				$valor = $datos["idOrden"];



				$traerOrdenes = ModeloOrdenes::mdlMostrarordenesParaValidar("ordenes", $item, $valor);

				// Guardar estado y técnico anterior para detectar cambios
				$_egs_estadoAnterior = '';
				$_egs_tecnicoAnterior = 0;
				if (is_array($traerOrdenes) && !empty($traerOrdenes)) {
					$_egs_primerOrden = reset($traerOrdenes);
					if (is_array($_egs_primerOrden)) {
						$_egs_estadoAnterior = isset($_egs_primerOrden["estado"]) ? $_egs_primerOrden["estado"] : '';
						$_egs_tecnicoAnterior = isset($_egs_primerOrden["id_tecnico"]) ? intval($_egs_primerOrden["id_tecnico"]) : 0;
					}
				}

				foreach ($traerOrdenes as $key => $value) {



					$multimediaBD = json_decode($value["multimedia"], true);

					$multimediaEditar = json_decode($datos["multimedia"], true);



					$objectMultimediaBD = array();

					$objectMultimediaEditar = array();



					foreach ($multimediaBD as $key => $value) {



						array_push($objectMultimediaBD, $value["foto"]);



					}



					foreach ($multimediaEditar as $key => $value) {



						array_push($objectMultimediaEditar, $value["foto"]);



					}



					$borrarFoto = array_diff($objectMultimediaBD, $objectMultimediaEditar);



					foreach ($borrarFoto as $key => $value) {



						unlink("../" . $value);



					}







				}



				/*=============================================

				VALIDAR IMAGEN PORTADA

				=============================================*/



				$rutaPortada = "../" . $datos["antiguaFotoPortada"];



				if (isset($datos["fotoPortada"]["tmp_name"]) && !empty($datos["fotoPortada"]["tmp_name"])) {



					/*=============================================

					BORRAMOS ANTIGUA FOTO PORTADA

					=============================================*/



					unlink("../" . $datos["antiguaFotoPortada"]);



					/*=============================================

					DEFINIMOS LAS MEDIDAS

					=============================================*/



					list($ancho, $alto) = getimagesize($datos["fotoPortada"]["tmp_name"]);



					$nuevoAncho = 1280;

					$nuevoAlto = 720;





					/*=============================================

					DE ACUERDO AL TIPO DE IMAGEN APLICAMOS LAS FUNCIONES POR DEFECTO DE PHP

					=============================================*/



					if ($datos["fotoPortada"]["type"] == "image/jpeg") {



						/*=============================================

						GUARDAMOS LA IMAGEN EN EL DIRECTORIO

						=============================================*/



						$aleatorio = mt_rand(100, 999);



						$rutaPortada = "../vistas/img/cabeceras/" . $datos["rutaOrden"] . ".jpg";



						$origen = imagecreatefromjpeg($datos["fotoPortada"]["tmp_name"]);

						$destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);



						imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);



						imagejpeg($destino, $rutaPortada);



					}



					if ($datos["fotoPortada"]["type"] == "image/png") {



						/*=============================================

						GUARDAMOS LA IMAGEN EN EL DIRECTORIO

						=============================================*/



						$aleatorio = mt_rand(100, 999);



						$rutaPortada = "../vistas/img/cabeceras/" . $datos["rutaOrden"] . ".png";



						$origen = imagecreatefrompng($datos["fotoPortada"]["tmp_name"]);



						$destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);



						imagealphablending($destino, FALSE);



						imagesavealpha($destino, TRUE);



						imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);



						imagepng($destino, $rutaPortada);



					}



				}



				/*=============================================

				VALIDAR IMAGEN PRINCIPAL

				=============================================*/



				$rutaFotoPrincipal = "../" . $datos["antiguaFotoPrincipal"];



				if (isset($datos["fotoPrincipal"]["tmp_name"]) && !empty($datos["fotoPrincipal"]["tmp_name"])) {



					/*=============================================

					BORRAMOS ANTIGUA FOTO PRINCIPAL

					=============================================*/



					unlink("../" . $datos["antiguaFotoPrincipal"]);



					/*=============================================

					DEFINIMOS LAS MEDIDAS

					=============================================*/



					list($ancho, $alto) = getimagesize($datos["fotoPrincipal"]["tmp_name"]);



					$nuevoAncho = 400;

					$nuevoAlto = 450;





					/*=============================================

					DE ACUERDO AL TIPO DE IMAGEN APLICAMOS LAS FUNCIONES POR DEFECTO DE PHP

					=============================================*/



					if ($datos["fotoPrincipal"]["type"] == "image/jpeg") {



						/*=============================================

						GUARDAMOS LA IMAGEN EN EL DIRECTORIO

						=============================================*/



						$aleatorio = mt_rand(100, 999);



						$rutaFotoPrincipal = "../vistas/img/productos/" . $datos["rutaOrden"] . ".jpg";



						$origen = imagecreatefromjpeg($datos["fotoPrincipal"]["tmp_name"]);



						$destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);



						imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);



						imagejpeg($destino, $rutaFotoPrincipal);



					}



					if ($datos["fotoPrincipal"]["type"] == "image/png") {



						/*=============================================

						GUARDAMOS LA IMAGEN EN EL DIRECTORIO

						=============================================*/



						$aleatorio = mt_rand(100, 999);



						$rutaFotoPrincipal = "../vistas/img/productos/" . $datos["rutaOrden"] . ".png";



						$origen = imagecreatefrompng($datos["fotoPrincipal"]["tmp_name"]);



						$destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);



						imagealphablending($destino, FALSE);



						imagesavealpha($destino, TRUE);



						imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);



						imagepng($destino, $rutaFotoPrincipal);



					}



				}



				date_default_timezone_set("America/Mexico_City");



				$fechaActualParaEntrada = date('Y-m-d');





				$datosOrden = array(

					"id" => $datos["idOrden"],

					"titulo" => $datos["tituloOrden"],

					"idTecnico" => $datos["tecnico"],

					"idAsesor" => $datos["asesor"],

					"idCliente" => $datos["cliente"],

					"estado" => $datos["estado"],

					"descripcionOrden" => $datos["descripcionOrden"],

					"rutaOrden" => $datos["rutaOrden"],

					"multimedia" => $datos["multimedia"],

					"partida1" => $datos["partida1"],

					"partida2" => $datos["partida2"],

					"partida3" => $datos["partida3"],

					"partida4" => $datos["partida4"],

					"partida5" => $datos["partida5"],

					"partida6" => $datos["partida6"],

					"partida7" => $datos["partida7"],

					"partida8" => $datos["partida8"],

					"partida9" => $datos["partida9"],

					"partida10" => $datos["partida10"],

					"precio1" => $datos["precio1"],

					"precio2" => $datos["precio2"],

					"precio3" => $datos["precio3"],

					"precio4" => $datos["precio4"],

					"precio5" => $datos["precio5"],

					"precio6" => $datos["precio6"],

					"precio7" => $datos["precio7"],

					"precio8" => $datos["precio8"],

					"precio9" => $datos["precio9"],

					"precio10" => $datos["precio10"],

					"totalOrdenEditar" => $datos["totalOrdenEditar"],

					"imgPortada" => substr($rutaPortada, 3),

					"imgFotoPrincipal" => substr($rutaFotoPrincipal, 3),

					"seleccionarPedido" => $datos["seleccionarPedido"],

					"idPedido" => $datos["idPedido"],

					"EstadoDelPedido" => $datos["EstadoDelPedido"]



				);



				if ($datos["estado"] == "Entregado (Ent)") {



					date_default_timezone_set("America/Mexico_City");



					$fecha = date('Y-m-d');



					$fechaActual = $fecha;



					$datosOrdenSalida = array(
						"id" => $datos["idOrden"],

						"fecha_Salida" => $fechaActual

					);



					$respuesta = ModeloOrdenes::mdlEditarFechaSalida("ordenes", $datosOrdenSalida);

				}



				$actualizadPedido = ModeloOrdenes::mdlEditarPedidoEnOrden("pedidos", $datosOrden);





				$respuesta = ModeloOrdenes::mdlEditarOrden("ordenes", $datosOrden);

				// ── Notificación de cambio de estado ──
				if (
					$respuesta === "ok" && !empty($_egs_estadoAnterior)
					&& $_egs_estadoAnterior !== $datos["estado"]
				) {
					try {
						ControladorNotificaciones::ctrCrearTablaEstado();
						// Obtener id_tecnicoDos de la orden
						$_egs_ordTecDos = 0;
						try {
							$_egs_ordActual = ModeloOrdenes::mdlMostrarOrdenes("ordenes", "id", intval($datos["idOrden"]));
							if (is_array($_egs_ordActual) && isset($_egs_ordActual["id_tecnicoDos"])) {
								$_egs_ordTecDos = intval($_egs_ordActual["id_tecnicoDos"]);
							}
						} catch (Exception $ex) {}

						ControladorNotificaciones::ctrRegistrarCambioEstado(array(
							"id_orden" => intval($datos["idOrden"]),
							"estado_anterior" => $_egs_estadoAnterior,
							"estado_nuevo" => $datos["estado"],
							"id_usuario_accion" => isset($_SESSION["id"]) ? intval($_SESSION["id"]) : 0,
							"nombre_usuario" => isset($_SESSION["nombre"]) ? $_SESSION["nombre"] : "Sistema",
							"titulo_orden" => $datos["tituloOrden"],
							"id_empresa" => isset($_SESSION["empresa"]) ? intval($_SESSION["empresa"]) : 0,
							"id_asesor" => intval($datos["asesor"]),
							"id_tecnico" => intval($datos["tecnico"]),
							"id_tecnicoDos" => $_egs_ordTecDos,
						));
					} catch (Exception $e) { /* silenciar para no romper el flujo */
					}
				}

				// ── Notificación de traspaso de técnico ──
				if (
					$respuesta === "ok" && $_egs_tecnicoAnterior > 0
					&& $_egs_tecnicoAnterior !== intval($datos["tecnico"])
					&& intval($datos["tecnico"]) > 0
				) {
					try {
						ControladorNotificaciones::ctrCrearTablaEstado();
						// Obtener nombres de técnicos
						$_egs_tecAntNombre = "Técnico #" . $_egs_tecnicoAnterior;
						$_egs_tecNuevoNombre = "Técnico #" . $datos["tecnico"];
						try {
							$_egs_tAnt = ControladorTecnicos::ctrMostrarTecnicos("id", $_egs_tecnicoAnterior);
							if (is_array($_egs_tAnt) && isset($_egs_tAnt["nombre"]))
								$_egs_tecAntNombre = $_egs_tAnt["nombre"];
							$_egs_tNuevo = ControladorTecnicos::ctrMostrarTecnicos("id", intval($datos["tecnico"]));
							if (is_array($_egs_tNuevo) && isset($_egs_tNuevo["nombre"]))
								$_egs_tecNuevoNombre = $_egs_tNuevo["nombre"];
						} catch (Exception $ex) {
						}

						ControladorNotificaciones::ctrRegistrarCambioEstado(array(
							"id_orden" => intval($datos["idOrden"]),
							"estado_anterior" => $_egs_tecAntNombre,
							"estado_nuevo" => $_egs_tecNuevoNombre,
							"id_usuario_accion" => isset($_SESSION["id"]) ? intval($_SESSION["id"]) : 0,
							"nombre_usuario" => isset($_SESSION["nombre"]) ? $_SESSION["nombre"] : "Sistema",
							"titulo_orden" => $datos["tituloOrden"],
							"id_empresa" => isset($_SESSION["empresa"]) ? intval($_SESSION["empresa"]) : 0,
							"id_asesor" => intval($datos["asesor"]),
							"id_tecnico" => intval($datos["tecnico"]),
							"id_tecnicoDos" => isset($_egs_ordTecDos) ? $_egs_ordTecDos : 0,
							"tipo" => "traspaso",
						));
					} catch (Exception $e) {
					}
				}















				return $respuesta;





			} else {



				echo '<script>



					swal({

						  type: "error",

						  title: "¡El nombre de la orden no puede ir vacío o llevar caracteres especiales!",

						  showConfirmButton: true,

						  confirmButtonText: "Cerrar"

						  }).then(function(result){

							if (result.value) {



							window.location = "index.php?ruta=ordenes";



							}

						})



			  	</script>';



			}



		}



	}





	/*=============================================

	ELIMINAR ORDEN

	=============================================*/



	static public function ctrEliminarOrden()
	{



		if (isset($_GET["idOrden"])) {



			$datos = $_GET["idOrden"];





			/*=============================================

			ELIMINAR FOTO PRINCIPAL

			=============================================*/



			if ($_GET["imgPrincipal"] != "" && $_GET["imgPrincipal"] != "vistas/img/default/default.png") {



				unlink($_GET["imgPrincipal"]);



			}



			if ($_GET["imgPortada"] != "" && $_GET["imgPortada"] != "vistas/img/default/default.png") {



				unlink($_GET["imgPortada"]);



			}





			$respuesta = ModeloOrdenes::mdlEliminarOrden("ordenes", $datos);



			if ($respuesta == "ok") {



				echo '<script>



				swal({

					  type: "success",

					  title: "La orden ha sido borrado correctamente",

					  showConfirmButton: true,

					  confirmButtonText: "Cerrar"

					  }).then(function(result){

								if (result.value) {



								window.location = "index.php?ruta=ordenes";



								}

							})



				</script>';



			}







		}



	}







	/*=============================================

	AGREGAR OBSERVACION

	=============================================*/

	public function ctrAgregarObservacion($datos)
	{



		if (isset($datos["observacion1"])) {



			$tabla = "observacionesOrdenes";



			$datos = array(
				"creador" => $datos["creador"],

				"idOrdenObservacion" => $datos["idOrdenObservacion"],

				"observacion1" => $datos["observacion1"],

				"observacion2" => $datos["observacion2"],

				"observacion3" => $datos["observacion3"],

				"observacion4" => $datos["observacion4"],

				"observacion5" => $datos["observacion5"],

				"observacion6" => $datos["observacion6"],

				"observacion7" => $datos["observacion7"],

				"observacion8" => $datos["observacion8"],

				"observacion9" => $datos["observacion9"],

				"observacion10" => $datos["observacion10"]

			);









			$respuesta = ModeloOrdenes::mdlIngresarObservacion($tabla, $datos);



			return $respuesta;

		} else {





			echo '<script>



					swal({



						type: "error",

						title: "¡La observacion no puede ir vacío o llevar caracteres speciales!",

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





	/*=============================================

	RANGO FECHAS

	=============================================*/



	static public function ctrRangoFechasOrdenes($fechaInicial, $fechaFinal, $itemUno, $valorUno)
	{



		$tabla = "ordenes";



		$respuesta = ModeloOrdenes::mdlRangoFechasOrdenes($tabla, $fechaInicial, $fechaFinal, $itemUno, $valorUno);



		return $respuesta;



	}



	/*=============================================

	RANGO FECHAS PARA SUPER ADMINISTRADOR

	=============================================*/



	static public function ctrRangoFechasOrdenesSuperAdmin($fechaInicial, $fechaFinal)
	{



		$tabla = "ordenes";



		$respuesta = ModeloOrdenes::mdlRangoFechasOrdenesParSuperAdmin($tabla, $fechaInicial, $fechaFinal);



		return $respuesta;



	}

	/*=============================================

	RANGO FECHAS PARA ENTREGADOS COMISIONES

	=============================================*/



	static public function ctrRangoFechasOrdenesComisiones($fechaInicial, $fechaFinal)
	{



		$tabla = "ordenes";



		$respuesta = ModeloOrdenes::mdlRangoFechasOrdenesENT($tabla, $fechaInicial, $fechaFinal);



		return $respuesta;



	}





	/*=============================================

	DESCARGAR REPORTE GENERAL ORDENES EXCEL 

	=============================================*/



	public function ctrDescargarReporteVOrdenes($valorEmpresa)
	{



		if (isset($_GET["reporte"])) {



			$tabla = "ordenes";



			if (isset($_GET["fechaInicial"]) && isset($_GET["fechaFinal"])) {



				$itemUno = "id_empresa";

				$valorUno = $valorEmpresa;

				$OrdenesFecha = ModeloOrdenes::mdlRangoFechasOrdenesPorEmpresa($tabla, $_GET["fechaInicial"], $_GET["fechaFinal"], $itemUno, $valorUno);





			} else {



				$item = "id_empresa";

				$valor = $valorEmpresa;



				$OrdenesFecha = ModeloOrdenes::mdlMostrarordenesParaValidar($tabla, $item, $valor);

			}







			/*=============================================

			CREAMOS EL ARCHIVO DE EXCEL

			=============================================*/



			$Name = $_GET["reporte"] . '.xls';



			header('Expires: 0');

			header('Cache-control: private');

			header("Content-type: application/vnd.ms-excel"); // Archivo de Excel

			header("Cache-Control: cache, must-revalidate");

			header('Content-Description: File Transfer');

			header('Last-Modified: ' . date('D, d M Y H:i:s'));

			header("Pragma: public");

			header('Content-Disposition:; filename="' . $Name . '"');

			header("Content-Transfer-Encoding: binary");



			echo utf8_decode("<table border='0'> 



					<tr> 

						<td style='font-weight:bold; border:1px solid #eee;'>Orden</td>

						<td style='font-weight:bold; border:1px solid #eee;'>Empresa</td>

						<td style='font-weight:bold; border:1px solid #eee;'>Asesor</td>

						<td style='font-weight:bold; border:1px solid #eee;'>Tecnico</td>

						<td style='font-weight:bold; border:1px solid #eee;'>Cliente</td>

						<td style='font-weight:bold; border:1px solid #eee;'>Estado</td>

						<td style='font-weight:bold; border:1px solid #eee;'>total</td>

						<td style='font-weight:bold; border:1px solid #eee;'>fecha</td>

					</tr>");





			foreach ($OrdenesFecha as $key => $value) {



				$item = "id";

				$valor = $value["id_empresa"];



				$NameEmpresa = ControladorVentas::ctrMostrarEmpresasParaTiketimp($item, $valor);



				$NombreEmpresa = $NameEmpresa["empresa"];



				//TRAER ASESOR



				$item = "id";

				$valor = $value["id_Asesor"];



				$asesor = Controladorasesores::ctrMostrarAsesoresEleg($item, $valor);



				$NombreAsesor = $asesor["nombre"];



				//TRAER CLIENTE (USUARIO)



				$item = "id";

				$valor = $value["id_usuario"];



				$usuario = ControladorClientes::ctrMostrarClientes($item, $valor);



				$NombreUsuario = $usuario["nombre"];



				//TRAER TECNICO

				$item = "id";

				$valor = $value["id_tecnico"];



				$tecnico = ControladorTecnicos::ctrMostrarTecnicos($item, $valor);



				$NombreTecnico = $tecnico["nombre"];



				//$ElTotal = number_format($total["total"],2);





				/*=============================================

				TRAER EMAIL DATOS DE COMPRA

				=============================================*/



				echo utf8_decode("<tr>

									 <td style='border:1px solid #eee;'>" . $value["id"] . "</td>

									 <td style='border:1px solid #eee;'>" . $NombreEmpresa . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $NombreAsesor . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $NombreTecnico . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $NombreUsuario . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $value["estado"] . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . money_format("%i", $value["total"]) . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $value["fecha"] . "</td>

			 					  	 </tr>");



			}

			/*=============================================

		   TRAER TOTAL

		   =============================================*/

			$tabla = "ordenes";

			$total = ModeloOrdenes::mdlSumarTotalOrdenesGeneral($tabla, $_GET["fechaInicial"], $_GET["fechaFinal"], $valorEmpresa);

			foreach ($total as $key => $valueTotal) {

				echo utf8_decode("<tr><td style='font-weight:bold; border:1px solid #eee;'>TOTAL</td></tr>");



				echo utf8_decode("<tr><td style='border:1px solid #eee;'>$" . $valueTotal["total"] . "</td>

			 					  	 	</tr>");

			}







			echo utf8_decode("</table>



					");

		}



	}



	/*=============================================

	DESCARGAR REPORTE ORDENES ENTREGADAS EXCEL 

	=============================================*/



	public function ctrDescargarReporteOrdenesENT($valorEmpresa)
	{



		if (isset($_GET["reporte"])) {

			$tabla = "ordenes";

			$itemEmpresa = "id_empresa";

			if (isset($_GET["fechaInicial"]) && isset($_GET["fechaFinal"])) {



				$OrdenesFecha = ModeloOrdenes::mdlRangoFechasOrdenesENT($tabla, $_GET["fechaInicial"], $_GET["fechaFinal"], $itemEmpresa, $valorEmpresa);


			} else {

				$estado = "Entregado (Ent)";

				$OrdenesFecha = ModeloOrdenes::mdlMostrarOrdenesPorEstado($tabla, $estado, $itemEmpresa, $valorEmpresa);

			}


			/*=============================================
			CREAMOS EL ARCHIVO DE EXCEL
			=============================================*/

			$Name = $_GET["reporte"] . '.xls';

			header('Expires: 0');

			header('Cache-control: private');

			header("Content-type: application/vnd.ms-excel"); // Archivo de Excel

			header("Cache-Control: cache, must-revalidate");

			header('Content-Description: File Transfer');

			header('Last-Modified: ' . date('D, d M Y H:i:s'));

			header("Pragma: public");

			header('Content-Disposition:; filename="' . $Name . '"');

			header("Content-Transfer-Encoding: binary");



			echo utf8_decode("<table border='0'> 



					<tr> 
						<td style='font-weight:bold; border:1px solid #eee;'>Orden</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Empresa</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Asesor</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Tecnico</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Cliente</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Estado</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Total Cobrado</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Ganancia neta</td>
						<td style='font-weight:bold; border:1px solid #eee;'>fecha salida</td>

					</tr>");


			foreach ($OrdenesFecha as $key => $value) {



				$item = "id";

				$valor = $value["id_empresa"];
				$NameEmpresa = ControladorVentas::ctrMostrarEmpresasParaTiketimp($item, $valor);

				$NombreEmpresa = $NameEmpresa["empresa"];

				//TRAER ASESOR
				$item = "id";
				$valor = $value["id_Asesor"];
				$asesor = Controladorasesores::ctrMostrarAsesoresEleg($item, $valor);

				$NombreAsesor = $asesor["nombre"];
				$departamentoAsesor = $asesor["departamento"];

				//TRAER CLIENTE (USUARIO)
				$item = "id";
				$valor = $value["id_usuario"];

				$usuario = ControladorClientes::ctrMostrarClientes($item, $valor);

				$NombreUsuario = $usuario["nombre"];
				//TRAER TECNICO
				$item = "id";
				$valor = $value["id_tecnico"];
				$tecnico = ControladorTecnicos::ctrMostrarTecnicos($item, $valor);

				$NombreTecnico = $tecnico["nombre"];
				$departamento = $tecnico["departamento"];

				//$ElTotal = number_format($total["total"],2);
				//CALCULO DEL TOTAL DESCONTANDO INVERCION

				$invercion = $value["totalInversion"];
				$totalNetoOrden = $value["total"] - $invercion;

				if ($departamento == "sistemas") {

					$comision = $totalNetoOrden / 1.16 * 0.04;

				} elseif ($departamento == "electronica") {

					$comision = $totalNetoOrden / 1.16 * 0.2;

				} elseif ($departamento == "impresoras") {


					$comision = $totalNetoOrden / 1.16 * 0.2;

				}

				if ($departamentoAsesor = "ventas") {

					$comision = $totalNetoOrden / 1.16 * 0.03;
				}

				/*=============================================
			  TRAER EMAIL DATOS DE COMPRA
			  =============================================*/
				echo utf8_decode("</td>
									 <td style='border:1px solid #eee;'>" . $value["id"] . "</td>
									 <td style='border:1px solid #eee;'>" . $NombreEmpresa . "</td>
			 					  	 <td style='border:1px solid #eee;'>" . $NombreAsesor . "</td>
			 					  	 <td style='border:1px solid #eee;'>" . $NombreTecnico . "</td>
			 					  	 <td style='border:1px solid #eee;'>" . $NombreUsuario . "</td>
			 					  	 <td style='border:1px solid #eee;'>" . $value["estado"] . "</td>
			 					  	 <td style='border:1px solid #eee;'>" . $value["total"] . "</td>
			 					  	 <td style='border:1px solid #eee;'>" . $totalNetoOrden . "</td>
			 					  	 <td style='border:1px solid #eee;'>" . $value["fecha_Salida"] . "</td>
			 					  	 </tr>");



				$suma += $totalNetoOrden;


			}

			echo utf8_decode("<tr><td style='font-weight:bold; border:1px solid #eee;'>TOTAL GANANCIA</td></tr>");

			echo utf8_decode("<tr><td style='border:1px solid #eee;'>$" . $suma . "</td>

						</tr>");

			/*=============================
			AQUI VA EL TOTAL
			==============================*/
			$tabla = "ordenes";

			$estado = "Entregado (Ent)";

			$total = ModeloOrdenes::mdlSumarTotalOrdenes($tabla, $_GET["fechaInicial"], $_GET["fechaFinal"], $valorEmpresa, $estado);

			foreach ($total as $key => $valueTotal) {

				echo utf8_decode("<tr><td style='font-weight:bold; border:1px solid #eee;'>TOTAL  COBRADO</td></tr>");

				echo utf8_decode("<tr><td style='border:1px solid #eee;'>$" . $valueTotal["total"] . "</td>

						</tr>");

			}





		}



	}





	/*=============================================

	DESCARGAR REPORTE ORDENES Aceptadas Ok

	=============================================*/



	public function ctrDescargarReporteOrdenesOk($valorEmpresa)
	{


		if (isset($_GET["reporte"])) {

			$tabla = "ordenes";
			$itemEmpresa = "id_empresa";


			if (isset($_GET["fechaInicial"]) && isset($_GET["fechaFinal"])) {


				$OrdenesFecha = ModeloOrdenes::mdlRangoFechasOrdenesOk($tabla, $_GET["fechaInicial"], $_GET["fechaFinal"], $itemEmpresa, $valorEmpresa);


			} else {


				$estado = "Aceptado (ok)";

				$OrdenesFecha = ModeloOrdenes::mdlMostrarOrdenesPorEstado($tabla, $estado, $itemEmpresa, $valorEmpresa);

			}


			/*=============================================
			CREAMOS EL ARCHIVO DE EXCEL
			=============================================*/

			$Name = $_GET["reporte"] . '.xls';
			header('Expires: 0');
			header('Cache-control: private');
			header("Content-type: application/xls"); // Archivo de Excel
			header("Cache-Control: cache, must-revalidate");
			header('Content-Description: File Transfer');
			header('Last-Modified: ' . date('D, d M Y H:i:s'));
			header("Pragma: public");
			header('Content-Disposition:; filename="' . $Name . '"');
			header("Content-Transfer-Encoding: binary");

			echo utf8_decode("<table border='0'> 

					<tr> 

						<td style='font-weight:bold; border:1px solid #eee;'>Orden</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Empresa</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Asesor</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Tecnico</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Cliente</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Estado</td>
						<td style='font-weight:bold; border:1px solid #eee;'>fecha</td>

					</tr>");


			foreach ($OrdenesFecha as $key => $value) {


				$item = "id";
				$valor = $value["id_empresa"];

				$NameEmpresa = ControladorVentas::ctrMostrarEmpresasParaTiketimp($item, $valor);
				$NombreEmpresa = $NameEmpresa["empresa"];

				//TRAER ASESOR
				$item = "id";
				$valor = $value["id_Asesor"];



				$asesor = Controladorasesores::ctrMostrarAsesoresEleg($item, $valor);



				$NombreAsesor = $asesor["nombre"];

				//TRAER CLIENTE (USUARIO)

				$item = "id";
				$valor = $value["id_usuario"];

				$usuario = ControladorClientes::ctrMostrarClientes($item, $valor);

				$NombreUsuario = $usuario["nombre"];

				//$ElTotal = number_format($total["total"],2);

				//TRAER TECNICO

				$item = "id";

				$valor = $value["id_tecnico"];

				$tecnico = ControladorTecnicos::ctrMostrarTecnicos($item, $valor);

				$NombreTecnico = $tecnico["nombre"];

				/*=============================================
				TRAER EMAIL DATOS DE COMPRA
				=============================================*/

				echo utf8_decode("<tr>

									 <td style='border:1px solid #eee;'>" . $value["id"] . "</td>

									 <td style='border:1px solid #eee;'>" . $NombreEmpresa . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $NombreAsesor . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $NombreTecnico . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $NombreUsuario . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $value["estado"] . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $value["total"] . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $value["fecha"] . "</td>

			 					  	 </tr>");

			}

			/*=============================================
			TRAER TOTAL
			=============================================*/

			$tabla = "ordenes";

			$estado = "Aceptado (ok)";

			$total = ModeloOrdenes::mdlSumarTotalOrdenes($tabla, $_GET["fechaInicial"], $_GET["fechaFinal"], $valorEmpresa, $estado);

			foreach ($total as $key => $valueTotal) {

				echo utf8_decode("<tr><td style='font-weight:bold; border:1px solid #eee;'>TOTAL</td></tr>");

				echo utf8_decode("<tr><td style='border:1px solid #eee;'>$" . $valueTotal["total"] . "</td>

						</tr>");

			}

			echo utf8_decode("</table>

					");

		}



	}





	/*=============================================

	DESCARGAR REPORTE ORDENES TERMINADAS TER

	=============================================*/



	public function ctrDescargarReporteOrdenesTer($valorEmpresa)
	{



		if (isset($_GET["reporte"])) {



			$tabla = "ordenes";

			$itemEmpresa = "id_empresa";



			if (isset($_GET["fechaInicial"]) && isset($_GET["fechaFinal"])) {



				$OrdenesFecha = ModeloOrdenes::mdlRangoFechasOrdenesTer($tabla, $_GET["fechaInicial"], $_GET["fechaFinal"], $itemEmpresa, $valorEmpresa);





			} else {





				$estado = "Terminada (ter)";



				$OrdenesFecha = ModeloOrdenes::mdlMostrarOrdenesPorEstado($tabla, $estado, $itemEmpresa, $valorEmpresa);

			}







			/*=============================================

			CREAMOS EL ARCHIVO DE EXCEL

			=============================================*/



			$Name = $_GET["reporte"] . '.xls';



			header('Expires: 0');

			header('Cache-control: private');

			header("Content-type: application/vnd.ms-excel"); // Archivo de Excel

			header("Cache-Control: cache, must-revalidate");

			header('Content-Description: File Transfer');

			header('Last-Modified: ' . date('D, d M Y H:i:s'));

			header("Pragma: public");

			header('Content-Disposition:; filename="' . $Name . '"');

			header("Content-Transfer-Encoding: binary");



			echo utf8_decode("<table border='0'> 



					<tr> 

						<td style='font-weight:bold; border:1px solid #eee;'>Orden</td>

						<td style='font-weight:bold; border:1px solid #eee;'>Empresa</td>

						<td style='font-weight:bold; border:1px solid #eee;'>Asesor</td>

						<td style='font-weight:bold; border:1px solid #eee;'>Tecnico</td>

						<td style='font-weight:bold; border:1px solid #eee;'>Cliente</td>

						<td style='font-weight:bold; border:1px solid #eee;'>Estado</td>

						<td style='font-weight:bold; border:1px solid #eee;'>total</td>

						<td style='font-weight:bold; border:1px solid #eee;'>fecha</td>

					</tr>");





			foreach ($OrdenesFecha as $key => $value) {



				$item = "id";

				$valor = $value["id_empresa"];



				$NameEmpresa = ControladorVentas::ctrMostrarEmpresasParaTiketimp($item, $valor);



				$NombreEmpresa = $NameEmpresa["empresa"];



				//TRAER ASESOR



				$item = "id";

				$valor = $value["id_Asesor"];



				$asesor = Controladorasesores::ctrMostrarAsesoresEleg($item, $valor);



				$NombreAsesor = $asesor["nombre"];



				//TRAER CLIENTE (USUARIO)



				$item = "id";

				$valor = $value["id_usuario"];



				$usuario = ControladorClientes::ctrMostrarClientes($item, $valor);



				$NombreUsuario = $usuario["nombre"];



				//$ElTotal = number_format($total["total"],2);



				//TRAER TECNICO

				$item = "id";

				$valor = $value["id_tecnico"];



				$tecnico = ControladorTecnicos::ctrMostrarTecnicos($item, $valor);



				$NombreTecnico = $tecnico["nombre"];



				/*=============================================

				TRAER EMAIL DATOS DE COMPRA

				=============================================*/



				echo utf8_decode("</td>

									 <td style='border:1px solid #eee;'>" . $value["id"] . "</td>

									 <td style='border:1px solid #eee;'>" . $NombreEmpresa . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $NombreAsesor . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $NombreTecnico . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $NombreUsuario . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $value["estado"] . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $value["total"] . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $value["fecha"] . "</td>

			 					  	 </tr>");



			}

			/*=============================================

			TRAER TOTAL

			=============================================*/

			$tabla = "ordenes";

			$estado = "Terminada (ter)";

			$total = ModeloOrdenes::mdlSumarTotalOrdenes($tabla, $_GET["fechaInicial"], $_GET["fechaFinal"], $valorEmpresa, $estado);

			foreach ($total as $key => $valueTotal) {

				echo utf8_decode("<tr><td style='font-weight:bold; border:1px solid #eee;'>TOTAL</td></tr>");



				echo utf8_decode("<tr><td style='border:1px solid #eee;'>$" . $valueTotal["total"] . "</td>

			 					  	 	</tr>");

			}







			echo utf8_decode("</table>



					");

		}



	}

	/*=============================================

		DESCARGAR REPORTE ORDENES AUTORIZADAS AUT

		=============================================*/



	public function ctrDescargarReporteOrdenesAut($valorEmpresa)
	{



		if (isset($_GET["reporte"])) {



			$tabla = "ordenes";

			$itemEmpresa = "id_empresa";



			if (isset($_GET["fechaInicial"]) && isset($_GET["fechaFinal"])) {



				$OrdenesFecha = ModeloOrdenes::mdlRangoFechasOrdenesAut($tabla, $_GET["fechaInicial"], $_GET["fechaFinal"], $itemEmpresa, $valorEmpresa);





			} else {



				$estado = "Pendiente de autorización (AUT";

				$item = "id_empresa";

				$valor = $valorEmpresa;

				$OrdenesFecha = ModeloOrdenes::mdlMostrarOrdenesPorEstado($tabla, $estado, $item, $valor);

			}







			/*=============================================

			CREAMOS EL ARCHIVO DE EXCEL

			=============================================*/



			$Name = $_GET["reporte"] . '.xls';



			header('Expires: 0');

			header('Cache-control: private');

			header("Content-type: application/vnd.ms-excel"); // Archivo de Excel

			header("Cache-Control: cache, must-revalidate");

			header('Content-Description: File Transfer');

			header('Last-Modified: ' . date('D, d M Y H:i:s'));

			header("Pragma: public");

			header('Content-Disposition:; filename="' . $Name . '"');

			header("Content-Transfer-Encoding: binary");



			echo utf8_decode("<table border='0'> 



					<tr> 

						<td style='font-weight:bold; border:1px solid #eee;'>Orden</td>

						<td style='font-weight:bold; border:1px solid #eee;'>Empresa</td>

						<td style='font-weight:bold; border:1px solid #eee;'>Asesor</td>

						<td style='font-weight:bold; border:1px solid #eee;'>Tecnico</td>

						<td style='font-weight:bold; border:1px solid #eee;'>Cliente</td>

						<td style='font-weight:bold; border:1px solid #eee;'>Estado</td>

						<td style='font-weight:bold; border:1px solid #eee;'>total</td>

						<td style='font-weight:bold; border:1px solid #eee;'>fecha</td>

					</tr>");





			foreach ($OrdenesFecha as $key => $value) {



				$item = "id";

				$valor = $value["id_empresa"];



				$NameEmpresa = ControladorVentas::ctrMostrarEmpresasParaTiketimp($item, $valor);



				$NombreEmpresa = $NameEmpresa["empresa"];



				//TRAER ASESOR



				$item = "id";

				$valor = $value["id_Asesor"];



				$asesor = Controladorasesores::ctrMostrarAsesoresEleg($item, $valor);



				$NombreAsesor = $asesor["nombre"];



				//TRAER CLIENTE (USUARIO)



				$item = "id";

				$valor = $value["id_usuario"];



				$usuario = ControladorClientes::ctrMostrarClientes($item, $valor);



				$NombreUsuario = $usuario["nombre"];



				//$ElTotal = number_format($total["total"],2);

				//TRAER TECNICO

				$item = "id";

				$valor = $value["id_tecnico"];



				$tecnico = ControladorTecnicos::ctrMostrarTecnicos($item, $valor);



				$NombreTecnico = $tecnico["nombre"];



				/*=============================================

				TRAER EMAIL DATOS DE COMPRA

				=============================================*/



				echo utf8_decode("</td>

									 <td style='border:1px solid #eee;'>" . $value["id"] . "</td>

									 <td style='border:1px solid #eee;'>" . $NombreEmpresa . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $NombreAsesor . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $NombreTecnico . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $NombreUsuario . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $value["estado"] . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $value["total"] . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $value["fecha"] . "</td>

			 					  	 </tr>");



			}

			/*=============================================

			TRAER TOTAL

			=============================================*/

			$tabla = "ordenes";

			$estado = "Pendiente de autorización (AUT";

			$total = ModeloOrdenes::mdlSumarTotalOrdenes($tabla, $_GET["fechaInicial"], $_GET["fechaFinal"], $valorEmpresa, $estado);

			foreach ($total as $key => $valueTotal) {

				echo utf8_decode("<tr><td style='font-weight:bold; border:1px solid #eee;'>TOTAL</td></tr>");



				echo utf8_decode("<tr><td style='border:1px solid #eee;'>$" . $valueTotal["total"] . "</td>

			 					  	 	</tr>");

			}







			echo utf8_decode("</table>



					");

		}



	}





	/*=============================================

	DESCARGAR REPORTE ORDENES SUPERVISION SUP

	=============================================*/



	public function ctrDescargarReporteOrdenesSup($valorEmpresa)
	{



		if (isset($_GET["reporte"])) {



			$tabla = "ordenes";

			$itemEmpresa = "id_empresa";



			if (isset($_GET["fechaInicial"]) && isset($_GET["fechaFinal"])) {



				$OrdenesFecha = ModeloOrdenes::mdlRangoFechasOrdenesSup($tabla, $_GET["fechaInicial"], $_GET["fechaFinal"], $itemEmpresa, $valorEmpresa);





			} else {



				$estado = "Supervisión (SUP)";

				$item = "id_empresa";

				$valor = $valorEmpresa;



				$OrdenesFecha = ModeloOrdenes::mdlMostrarOrdenesPorEstado($tabla, $estado, $item, $valor);

			}







			/*=============================================

			CREAMOS EL ARCHIVO DE EXCEL

			=============================================*/



			$Name = $_GET["reporte"] . '.xls';



			header('Cache-control: private');

			header("Content-type: application/vnd.ms-excel"); // Archivo de Excel

			header("Cache-Control: cache, must-revalidate");

			header('Content-Description: File Transfer');

			header('Last-Modified: ' . date('D, d M Y H:i:s'));

			header("Pragma: public");

			header('Content-Disposition:; filename="' . $Name . '"');

			header("Content-Transfer-Encoding: binary");



			echo utf8_decode("<table border='0'> 



					<tr> 

						<td style='font-weight:bold; border:1px solid #eee;'>Orden</td>

						<td style='font-weight:bold; border:1px solid #eee;'>Empresa</td>

						<td style='font-weight:bold; border:1px solid #eee;'>Asesor</td>

						<td style='font-weight:bold; border:1px solid #eee;'>Tecnico</td>

						<td style='font-weight:bold; border:1px solid #eee;'>Cliente</td>

						<td style='font-weight:bold; border:1px solid #eee;'>Estado</td>

						<td style='font-weight:bold; border:1px solid #eee;'>total</td>

						<td style='font-weight:bold; border:1px solid #eee;'>fecha</td>

					</tr>");





			foreach ($OrdenesFecha as $key => $value) {



				$item = "id";

				$valor = $value["id_empresa"];



				$NameEmpresa = ControladorVentas::ctrMostrarEmpresasParaTiketimp($item, $valor);



				$NombreEmpresa = $NameEmpresa["empresa"];



				//TRAER ASESOR



				$item = "id";

				$valor = $value["id_Asesor"];



				$asesor = Controladorasesores::ctrMostrarAsesoresEleg($item, $valor);



				$NombreAsesor = $asesor["nombre"];



				//TRAER CLIENTE (USUARIO)



				$item = "id";

				$valor = $value["id_usuario"];



				$usuario = ControladorClientes::ctrMostrarClientes($item, $valor);



				$NombreUsuario = $usuario["nombre"];



				//$ElTotal = number_format($total["total"],2);

				//TRAER TECNICO

				$item = "id";

				$valor = $value["id_tecnico"];



				$tecnico = ControladorTecnicos::ctrMostrarTecnicos($item, $valor);



				$NombreTecnico = $tecnico["nombre"];



				/*=============================================

				TRAER EMAIL DATOS DE COMPRA

				=============================================*/



				echo utf8_decode("</td>

					                

									 <td style='border:1px solid #eee;'>" . $value["id"] . "</td>

									 <td style='border:1px solid #eee;'>" . $NombreEmpresa . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $NombreAsesor . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $NombreTecnico . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $NombreUsuario . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $value["estado"] . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $value["total"] . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $value["fecha"] . "</td>

			 					  	 </tr>");



			}

			/*=============================================

		   TRAER TOTAL

		   =============================================*/

			$tabla = "ordenes";

			$estado = "Supervisión (SUP)";

			$total = ModeloOrdenes::mdlSumarTotalOrdenes($tabla, $_GET["fechaInicial"], $_GET["fechaFinal"], $valorEmpresa, $estado);

			foreach ($total as $key => $valueTotal) {

				echo utf8_decode("<tr><td style='font-weight:bold; border:1px solid #eee;'>TOTAL</td></tr>");



				echo utf8_decode("<tr><td style='border:1px solid #eee;'>$" . $valueTotal["total"] . "</td>

			 					  	 	</tr>");

			}







			echo utf8_decode("</table>



					");

		}



	}







	/*=============================================

	DESCARGAR REPORTE ORDENES PENDIENTE DE REVISION EXCEL 

	=============================================*/



	public function ctrReporteOrdenesPenR($valorEmpresa)
	{



		if (isset($_GET["reporte"])) {



			$tabla = "ordenes";

			$itemEmpresa = "id_empresa";



			if (isset($_GET["fechaInicial"]) && isset($_GET["fechaFinal"])) {



				$OrdenesFecha = ModeloOrdenes::mdlRangoFechasOrdenesPenR($tabla, $_GET["fechaInicial"], $_GET["fechaFinal"], $itemEmpresa, $valorEmpresa);





			} else {







				$estado = "En revisión (REV)";

				$item = "id_empresa";

				$valor = $valorEmpresa;

				$OrdenesFecha = ModeloOrdenes::mdlMostrarOrdenesPorEstado($tabla, $estado, $item, $valor);

			}







			/*=============================================

			CREAMOS EL ARCHIVO DE EXCEL

			=============================================*/



			$Name = $_GET["reporte"] . '.xls';



			header('Expires: 0');

			header('Cache-control: private');

			header("Content-type: application/vnd.ms-excel"); // Archivo de Excel

			header("Cache-Control: cache, must-revalidate");

			header('Content-Description: File Transfer');

			header('Last-Modified: ' . date('D, d M Y H:i:s'));

			header("Pragma: public");

			header('Content-Disposition:; filename="' . $Name . '"');

			header("Content-Transfer-Encoding: binary");



			echo utf8_decode("<table border='0'> 



					<tr> 

						<td style='font-weight:bold; border:1px solid #eee;'>Orden</td>

						<td style='font-weight:bold; border:1px solid #eee;'>Empresa</td>

						<td style='font-weight:bold; border:1px solid #eee;'>Asesor</td>

						<td style='font-weight:bold; border:1px solid #eee;'>Tecnico</td>

						<td style='font-weight:bold; border:1px solid #eee;'>Cliente</td>

						<td style='font-weight:bold; border:1px solid #eee;'>Estado</td>

						<td style='font-weight:bold; border:1px solid #eee;'>total</td>

						<td style='font-weight:bold; border:1px solid #eee;'>fecha</td>

					</tr>");





			foreach ($OrdenesFecha as $key => $value) {



				$item = "id";

				$valor = $value["id_empresa"];



				$NameEmpresa = ControladorVentas::ctrMostrarEmpresasParaTiketimp($item, $valor);



				$NombreEmpresa = $NameEmpresa["empresa"];



				//TRAER ASESOR



				$item = "id";

				$valor = $value["id_Asesor"];



				$asesor = Controladorasesores::ctrMostrarAsesoresEleg($item, $valor);



				$NombreAsesor = $asesor["nombre"];



				//TRAER CLIENTE (USUARIO)



				$item = "id";

				$valor = $value["id_usuario"];



				$usuario = ControladorClientes::ctrMostrarClientes($item, $valor);



				$NombreUsuario = $usuario["nombre"];



				//$ElTotal = number_format($total["total"],2);



				//TRAER TECNICO

				$item = "id";

				$valor = $value["id_tecnico"];



				$tecnico = ControladorTecnicos::ctrMostrarTecnicos($item, $valor);



				$NombreTecnico = $tecnico["nombre"];

				/*=============================================

				TRAER EMAIL DATOS DE COMPRA

				=============================================*/



				echo utf8_decode("</td>

									 <td style='border:1px solid #eee;'>" . $value["id"] . "</td>

									 <td style='border:1px solid #eee;'>" . $NombreEmpresa . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $NombreAsesor . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $NombreTecnico . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $NombreUsuario . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $value["estado"] . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $value["total"] . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $value["fecha"] . "</td>

			 					  	 </tr>");



			}

			/*=============================================

		   TRAER TOTAL

		   =============================================*/

			$tabla = "ordenes";

			$estado = "En revisión (REV)";

			$total = ModeloOrdenes::mdlSumarTotalOrdenes($tabla, $_GET["fechaInicial"], $_GET["fechaFinal"], $valorEmpresa, $estado);

			foreach ($total as $key => $valueTotal) {

				echo utf8_decode("<tr><td style='font-weight:bold; border:1px solid #eee;'>TOTAL</td></tr>");



				echo utf8_decode("<tr><td style='border:1px solid #eee;'>$" . $valueTotal["total"] . "</td>

			 					  	 	</tr>");

			}







			echo utf8_decode("</table>



					");

		}



	}





	/*=============================================

	EDITAR OBSERVACIONES YA EXISTENTES

	=============================================*/

	function ctrEditarObservacionesYaExistentes()
	{



		if (isset($_POST["observaciones"])) {



			$tabla = "ordenes";



			$datosObservacion = array(
				"id" => $_POST["idOrden"],

				"observaciones" => $_POST["observaciones"],

				"listarObservaciones" => $_POST["listarObservaciones"]
			);



			$respuesta = ModeloOrdenes::mdlEditarObservacionesYaExistentes($tabla, $datosObservacion);



			/*====================================

					AGREGAR NOTIFICACION PUSH PARA OBSERVACIONES

					======================================*/



			if ($_SESSION["perfil"] == "administrador") {



				$observaciones = json_decode($_POST["listarObservaciones"], true);



				foreach ($observaciones as $key => $valueObservacionesPush) {







				}

				echo '<!-- notifications-push -->	





									<script>



										Push.create("Nueva observación",{



											body:"' . $_POST["idOrden"] . ' observación: ' . $valueObservacionesPush["observacion"] . '",

											icon:"' . $_SESSION["foto"] . '",

											timeout:10000,

											onClick: function(){

												window.location="index.php?ruta=inicio";

												this.close();



											}



											});

									</script>';

			}





			if ($respuesta == "ok") {



				echo '<script>







					swal({



						type: "success",

						title: "¡La observacion se ha guardado correctamente!",

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

	REALIZAR BUSQUEDA DE ORDENES

	=============================================*/

	static public function ctrBuscadorDeOrdenes($consulta)
	{



		$tabla = "ordenes";



		$respuesta = ModeloOrdenes::mdlBuscarOrdenes($tabla, $consulta);



		return $respuesta;



	}

	/*=============================================

	REALIZAR BUSQUEDA DE ORDENES

	=============================================*/

	static public function ctrBuscadorDeOrdenesNew($consulta)
	{



		$tabla = "ordenes";



		$respuesta = ModeloOrdenes::mdlBuscarOrdenesNew($tabla, $consulta);



		return $respuesta;



	}





	/*=============================================

	AGREGAR INVSERSIONES

	=============================================*/

	public function ctrEditarInversiones()
	{





		if (isset($_POST["listarinversiones"])) {



			$tabla = "ordenes";



			$datosInversion = array(

				"id" => $_POST["idOrden"],

				"listarinversiones" => $_POST["listarinversiones"],

				"totalInversiones" => isset($_POST["totalInversiones"]) ? $_POST["totalInversiones"] : 0,

				"estado" => $_POST["estado"]

			);



			$respuesta = ModeloOrdenes::mdlEditarInversiones($tabla, $datosInversion);









			if ($respuesta == "ok") {





				echo '<script>







					swal({



						type: "success",

						title: "¡La orden se ha guardado correctamente!",

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

	CREAR PEDIDO EN ORDEN

	=============================================*/

	public function ctrAgregarPedidoEnOrden()
	{



		if (isset($_POST["ProductosPedidoListados"])) {



			$datosPedidoEnOrden = array(
				"empresa" => $_POST["empresaPedioDinamico"],

				"asesor" => $_POST["asesorPedidoDinamico"],

				"cliente" => $_POST["clientePedidoDinamico"],

				"productos" => $_POST["ProductosPedidoListados"],

				"total" => $_POST["TotalPedidoEnOrden"],

				"estado" => $_POST["EstadoPedidoDinamico"],

				"pago" => $_POST["PrimerPagolistado"],

				"adeudo" => $_POST["PrimerAdeudo"],

				"id_orden" => $_POST["seleccionarOrdenPedidoDinamico"]

			);



			$respuesta = ModeloOrdenes::mdlIngresarPedidoDinamico("pedidos", $datosPedidoEnOrden);



			if ($respuesta == "ok") {



				echo '<script>



					swal({



						type: "success",

						title: "¡El Pedido ha sido guardado correctamente!",

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

	MOSTRAR ORDENES DEL ASESOR

	=============================================*/

	static public function ctrMostrarOrdenesDelAsesor($id_Asesor)
	{



		$tabla = "ordenes";



		$respuesta = ModeloOrdenes::mdlMostrarOrdenesConAtraso($id_Asesor, $tabla);



		return $respuesta;

	}



	/*=============================================

	MOSTRAR ORDENES DEL TECNICO

	=============================================*/

	static public function ctrMostrarOrdenesDelTecncio($id_tecnico)
	{



		$tabla = "ordenes";



		$respuesta = ModeloOrdenes::mdlMostrarOrdenesDelTecnico($id_tecnico, $tabla);



		return $respuesta;

	}



	/*=============================================

	LISTAR ORDENES

	=============================================*/

	static public function ctrListarOrdenes()
	{



		$tabla = "ordenes";



		$respuesta = ModeloOrdenes::mdlListarOrdenes($tabla);



		return $respuesta;



	}


	/*=============================================

LISTAR ORDENES ASESOR MES

=============================================*/

	static public function ctrListarOrdenesAsesor($idAsesor)
	{



		$tabla = "ordenes";



		$respuesta = ModeloOrdenes::mdlListarOrdenesAsesor($tabla, $idAsesor);



		return $respuesta;



	}
	/*=============================================

LISTAR ORDENES MES ENTRADAS

=============================================*/

	static public function ctrMostrarOrdenesEntrada()
	{



		$tabla = "ordenes";



		$respuesta = ModeloOrdenes::mdlMostrarOrdenesEntrada($tabla);



		return $respuesta;



	}
	/*=============================================

LISTAR ORDENES ASESOR MES ENTRADAS

=============================================*/

	static public function ctrMostrarOrdenesEntradaAsesor($idAsesor)
	{



		$tabla = "ordenes";



		$respuesta = ModeloOrdenes::mdlMostrarOrdenesEntradaAsesor($tabla, $idAsesor);



		return $respuesta;



	}



	/*=============================================

	ORDENES CON BASE Y TOPE

	=============================================*/

	static public function ctrlTraerOrdenesConTope($base, $tope)
	{



		$tabla = "ordenes";



		$respuesta = ModeloOrdenes::mdlMostrarOrdenesConTope($tabla, $base, $tope);



		return $respuesta;

	}

	/*=============================

	DESCARGAR REPORTE COMISIONES

	=========================*/

	public function ctrlDescargarComisiones()
	{





		if (isset($_GET["reporte"])) {



			$tabla = "ordenes";





			if (isset($_GET["fechaInicial"]) && isset($_GET["fechaFinal"])) {



				$OrdenesFecha = ModeloOrdenes::mdlRangoFechasOrdenesENT($tabla, $_GET["fechaInicial"], $_GET["fechaFinal"]);





			} else {



				$estado = "Entregado (Ent)";



				$OrdenesFecha = ModeloOrdenes::mdlMostrarOrdenesPorEstado($tabla, $estado);

			}



			/*=============================================

				CREAMOS EL ARCHIVO DE EXCEL

			=============================================*/



			$Name = $_GET["reporte"] . '.xls';



			header('Expires: 0');

			header('Cache-control: private');

			header("Content-type: application/vnd.ms-excel"); // Archivo de Excel

			header("Cache-Control: cache, must-revalidate");

			header('Content-Description: File Transfer');

			header('Last-Modified: ' . date('D, d M Y H:i:s'));

			header("Pragma: public");

			header('Content-Disposition:; filename="' . $Name . '"');

			header("Content-Transfer-Encoding: binary");



			echo utf8_decode("<table border='0'> 



				<tr> 

					

					<td style='font-weight:bold; border:1px solid #eee;'>Orden</td>

						<td style='font-weight:bold; border:1px solid #eee;'>Tecnico</td>

						<td style='font-weight:bold; border:1px solid #eee;'>Estado</td>

						<td style='font-weight:bold; border:1px solid #eee;'>Ingreso Bruto</td>

						<td style='font-weight:bold; border:1px solid #eee;'>Utilidad Antes IVA</td>

						<td style='font-weight:bold; border:1px solid #eee;'>Inversión</td>

						<td style='font-weight:bold; border:1px solid #eee;'>comisión Tecnico</td>

						<td style='font-weight:bold; border:1px solid #eee;'>comisión Vendedor</td>

						<td style='font-weight:bold; border:1px solid #eee;'>fecha salida</td>

			</tr>");





			foreach ($OrdenesFecha as $key => $valueComisiones) {



				//TRAER ASESOR



				$item = "id";

				$valor = $valueComisiones["id_Asesor"];



				$asesor = Controladorasesores::ctrMostrarAsesoresEleg($item, $valor);



				$NombreAsesor = $asesor["nombre"];

				$departamentoAsesor = $asesor["departamento"];



				//TRAER TECNICO

				$item = "id";

				$valor = $valueComisiones["id_tecnico"];



				$tecnico = ControladorTecnicos::ctrMostrarTecnicos($item, $valor);



				$NombreTecnico = $tecnico["nombre"];

				$departamento = $tecnico["departamento"];



				//CALCULO DEL TOTAL DESCONTANDO INVERCION

				if ($valueComisiones["totalInversion"] == 0) {





					$invercion = 0;

					$totalNetoOrden = $valueComisiones["total"];

					$utilidadAntesIva = $valueComisiones["total"] / 1.16;



				} else {



					$invercion = $valueComisiones["totalInversion"];

					$totalNetoOrden = $valueComisiones["total"] - $invercion;

					$utilidadAntesIva = $valueComisiones["total"] / 1.16;



				}

				//var_dump($valueComisiones["id"],$valueComisiones["total"],$invercion);



				if ($departamento == "sistemas") {



					$comisionTec = $totalNetoOrden / 1.16 * 0.04;



				} elseif ($departamento == "electronica") {



					$comisionTec = $totalNetoOrden / 1.16 * 0.2;



				} elseif ($departamento == "impresoras") {



					$comisionTec = $totalNetoOrden / 1.16 * 0.2;

					$sumaComisionesTec += $comisionTec;

				}



				if ($departamentoAsesor = "ventas") {



					$comisionVededor = $totalNetoOrden / 1.16 * 0.03;

					$sumaComisionesVend += $comisionVededor;

				}



				/*=============================================

			  TRAER EMAIL DATOS DE COMPRA

			  =============================================*/





				$sumaGanancia += $totalNetoOrden;

				$sumaCobrado += $valueComisiones["total"];

				echo utf8_decode("</td>

						

						<td style='border:1px solid #eee;'>" . $valueComisiones["id"] . "</td>

									 <td style='border:1px solid #eee;'>" . $NombreTecnico . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $valueComisiones["estado"] . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $valueComisiones["total"] . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $utilidadAntesIva . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $invercion . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $comisionTec . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $comisionVededor . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $valueComisiones["fecha_Salida"] . "</td>

				 	</tr>");

			}



			echo utf8_decode("</br></br><td style='font-weight:bold; border:1px solid #eee;'>total cobrado</td>

					<td style='border:1px solid #eee;'>" . $sumaCobrado . "</td>

					<td style='font-weight:bold; border:1px solid #eee;'>total ganancia</td>

					<td style='border:1px solid #eee;'>" . $sumaGanancia . "</td>

					<td style='font-weight:bold; border:1px solid #eee;'>total comision Técnico</td>

					<td style='border:1px solid #eee;'>" . $sumaComisionesTec . "</td>

					<td style='font-weight:bold; border:1px solid #eee;'>total comision Vendedor</td>

					<td style='border:1px solid #eee;'>" . $sumaComisionesVend . "</td>");

			echo utf8_decode("</table>



						");

		}

	}



	public function ctrlAgregarTipoDeOrden()
	{



		if (isset($_POST["Tipo-repearacion"])) {



			$tabla = "ordenes";



			$idOrden = $_POST["idOrden"];



			$tipo_de_reparacion = $_POST["Tipo-repearacion"];



			$respuesta = ModeloOrdenes::mdlIngresartipoDeRepacion($tabla, $idOrden, $tipo_de_reparacion);



			if ($respuesta == "ok") {



				echo '<script>



					swal({



						type: "success",

						title: "¡El tipo de reparación fue agregado correctamente!",

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



	public function ctrlAgregarRecargaDeCartucho()
	{



		if (isset($_POST["nuevaRecarga"])) {



			$tabla = "ordenes";



			$idOrden = $_POST["idOrden"];



			$recarga = $_POST["nuevaRecarga"];



			$precioRecarga = $_POST["precioRecarga"];



			$respuesta = ModeloOrdenes::mdlIngresarRecarga($tabla, $idOrden, $recarga, $precioRecarga);



			if ($respuesta == "ok") {



				echo '<script>



					swal({



						type: "success",

						title: "¡La recarga fue agregado correctamente!",

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



	public function ctrlEnviarCorreoCliente()
	{



		if (isset($_GET["correo"])) {





			//TRAER ORDEN PARA MOSTRAR EN CORREO

			$tabla = "ordenes";

			$item = "id";

			$valor = $_GET["idOrden"];

			$respuestaOrdenes = ModeloOrdenes::mdlMostrarordenesParaValidar($tabla, $item, $valor);

			foreach ($respuestaOrdenes as $key => $valueOrdenesCorreo) {



				$partidas = json_decode($valueOrdenesCorreo["partidas"], true);



				foreach ($partidas as $key => $valuePartidas) {



					$descripcionpartidas = $valuePartidas["descripcion"];

				}

			}





			//var_dump($respuesta);



			//VOLCAR NOMBRE DEL CLIENTE

			$itemCliente = "id";

			$valorCliente = $_GET["cliente"];



			$usuario = ControladorClientes::ctrMostrarClientes($itemCliente, $valorCliente);





			//VOLCAR NOMBRE DEL ASESOR

			$itemAsesor = "id";

			$valorAsesor = $_GET["asesor"];



			$asesor = Controladorasesores::ctrMostrarAsesoresEleg($itemAsesor, $valorAsesor);



			//VOLCAR NOMBRE DEL TECNICO

			$item = "id";

			$valor = $_GET["tecnico"];



			$tecnico = ControladorTecnicos::ctrMostrarTecnicos($item, $valor);





			date_default_timezone_set("America/Mexico_City");



			$mail = new PHPMailer;



			$mail->CharSet = 'UTF-8';



			$mail->SMTPSecure = 'tls';



			$mail->isMail();



			$mail->setFrom('noreplay.ordenes@backend.comercializadoraegs.com');



			$mail->addReplyTo('noreplay.ordenes@backend.comercializadoraegs.com');



			$mail->Subject = "Actualización de tu orden de servicio EGS";



			$mail->addAddress($usuario["correo"]);



			$mail->msgHTML('



				<img style="width: 10%; height: 10%; border-color: green; border-radius: 90%;" src="https://comercializadoraegs.com/wp-content/uploads/2021/04/logo-1.jpg">



				<h1 style="font-weight: bold;">Gracias por su preferencia su ordene se encuetra ' . $valueOrdenesCorreo["estado"] . '</h1>



				<p>&nbsp;</p>



				<h3 style="line-height: 150%; text-align: justify; font-family: "Montserrat", sans-serif;">

				

					Gracias por elegir a Comercializadora EGS para realizar tus compras, órdenes y  ervicios.Comercializadora EGS te ofrece todo lo que necesitas en Equipo de cómputo, Materiales de Construcción, Abarrotes, Cremería, Chiles y Semillas.

					Recuerda que ahora puedes diseñas tu sitio web con nosotros conoce nuestros servicios en 

					https://comercializadoraegs.com/servicios	



				</h3>



				<h2><b>Verifique los datos de su orden</b></h2>

  				<hr style=" height: 3px; background-color:#256F46;">

  				<h4>Información de la orden</h4>

  				<hr style=" height: 3px; background-color:#256F46;">



  				<div  style=" display:flex; flex-direction: row;">



  					<div style="text-align:right; font-size:15px; font-weight: bold;">



  						<ul style="list-style:none;">

     									

     						<li>Número de orden:</li>

     						<li>Estado de orden:</li>

     						<li>Cliente:</li>

     						<li>Asesor:</li>

     						<li>Técnico:</li>

     						<li>Fecha de ingreso:</li>



   						</ul>



  					</div>



  					<div style="text-align:left; font-weight: bold;">



  						<ul style="list-style:none; font-size:15px;">

      								

      						<li>' . $_GET["idOrden"] . '</li>

      						<li>' . $valueOrdenesCorreo["estado"] . '</li>

      						<li>' . $usuario["nombre"] . '</li>

      						<li>' . $asesor["nombre"] . '</li>

      						<li>' . $tecnico["nombre"] . '</li>

      						<li>' . $valueOrdenesCorreo["fecha_ingreso"] . '</li>      						 



    					</ul>



  					</div>



  				</div>



  				<hr style=" height: 3px; background-color:#256F46;">

  					Descripcion de su orden

  				<hr style=" height: 3px; background-color:#256F46;">



  				<div  style=" display:flex; flex-direction: row; ">



  					<div style="text-align:right; font-size:15px; font-weight: bold;">



  						<ul style="list-style:none;">



     						<li>Observaciones:</li><br/><br/><br/><br/>

     						<li>Total:</li>



   						</ul>



  					</div>



  					<div style="text-align:left; font-weight: bold;">



  						<ul style="list-style:none; font-size:15px;">



      						<li>' . $valueOrdenesCorreo["partidaUno"] . '</li>

      						<li>' . $valueOrdenesCorreo["partidaDos"] . '</li>

							<li>' . $descripcionpartidas . '</li>

							<li>MXN $' . $valueOrdenesCorreo["total"] . '</li> 



    					</ul>



  					</div>



  				</div>



  				<p>&nbsp;</p>



			');

			if (!$mail->Send()) {



				echo '<script>



					alert("' . $mail->ErrorInfo . '");

						 

					window.location = "index.php?ruta=ordenes";



			  	</script>';



			} else {



				echo '<script>



					alert("Correo enviado correctamente");



					window.location = "index.php?ruta=ordenes";

				

				</script>';

			}

		}

	}







	public function ctrlAgregarNuevaPartidaTecnicoDos()
	{



		if (isset($_POST["partidasTecnicoDos"])) {



			$tabla = "ordenes";



			$idOrden = $_POST["idOrden"];



			$partidasTecnicoDos = $_POST["partidasTecnicoDos"];



			$TotalPartidasTecnicoDos = $_POST["TotalPartidasTecnicoDos"];



			$respuesta = ModeloOrdenes::mdlIngresarPartidasTecnico($tabla, $idOrden, $partidasTecnicoDos, $TotalPartidasTecnicoDos);



			if ($respuesta == "ok") {



				echo '<script>



					swal({



						type: "success",

						title: "¡Partida de Tecnico agregada correctamente!",

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





	public function ctrlAgregarTecnicoDos()
	{



		if (isset($_POST["NuevoTecnicoEnOrden"])) {



			$tabla = "ordenes";



			$idOrden = $_POST["idOrden"];



			$idTecnicoDos = $_POST["NuevoTecnicoEnOrden"];



			$respuesta = ModeloOrdenes::mdlIngresarTecnicoDos($tabla, $idOrden, $idTecnicoDos);

			// Se cambio el titulo del success

			//title: "¡Tecnico agregado correctamente!",



			if ($respuesta == "ok") {



				echo '<script>



					swal({



						type: "success",

						title: "¡La orden se ha guardado correctamente!",

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

	AGREGAR ORDENES CON PARTIDAS LISTADAS

	=============================================*/

	public function ctrEditarOrdenDinamica()
	{



		if (isset($_POST["listatOrdenes"])) {

			$tabla = "ordenes";

			// ── Guardar estado y técnico anterior para detectar cambios ──
			$_egs_dyn_estadoAnt = '';
			$_egs_dyn_tecnicoAnt = 0;
			$_egs_dyn_tituloOrden = '';
			try {
				$_egs_dyn_ordenAnt = ModeloOrdenes::mdlMostrarordenesParaValidar("ordenes", "id", $_POST["idOrden"]);
				if (is_array($_egs_dyn_ordenAnt) && !empty($_egs_dyn_ordenAnt)) {
					$_egs_dyn_first = reset($_egs_dyn_ordenAnt);
					if (is_array($_egs_dyn_first)) {
						$_egs_dyn_estadoAnt = isset($_egs_dyn_first["estado"]) ? $_egs_dyn_first["estado"] : '';
						$_egs_dyn_tecnicoAnt = isset($_egs_dyn_first["id_tecnico"]) ? intval($_egs_dyn_first["id_tecnico"]) : 0;
						$_egs_dyn_tituloOrden = isset($_egs_dyn_first["titulo"]) ? $_egs_dyn_first["titulo"] : '';
					}
				}
			} catch (Exception $e) {
			}

			// DEBUG: Log temporal para verificar los valores que llegan
			file_put_contents("debug_infoOrden.txt", date("Y-m-d H:i:s") . " | " .
				"perfil=" . (isset($_SESSION["perfil"]) ? $_SESSION["perfil"] : "?") .
				" | tecnicodos_POST=" . (isset($_POST["tecnicodosEditadoEnOrdenDianmica"]) ? $_POST["tecnicodosEditadoEnOrdenDianmica"] : "NO_EXISTE") .
				" | inversiones_POST=" . (isset($_POST["listarinversiones"]) ? mb_substr($_POST["listarinversiones"], 0, 80) : "NO_EXISTE") .
				" | totalInv_POST=" . (isset($_POST["totalInversiones"]) ? $_POST["totalInversiones"] : "NO_EXISTE") .
				" | estado=" . $_POST["estado"] .
				"\n", FILE_APPEND);

			$datosOrdenDinamica = array(

				"id" => $_POST["idOrden"],

				"asesor" => $_POST["asesorEditadoEnOrdenDianmica"],

				"tecnico" => $_POST["tecnicoEditadoEnOrdenDianmica"],

				"tecnicodos" => isset($_POST["tecnicodosEditadoEnOrdenDianmica"]) ? intval($_POST["tecnicodosEditadoEnOrdenDianmica"]) : 0,

				"estado" => $_POST["estado"],

				"partidaUno" => $_POST["partidaUno"],

				"precioUno" => $_POST["precioUno"],

				"partidaDos" => $_POST["partidaDos"],

				"precioDos" => $_POST["precioDos"],

				"partidaTres" => $_POST["partidaTres"],

				"precioTres" => $_POST["precioTres"],

				"partidaCuatro" => $_POST["partidaCuatro"],

				"precioCuatro" => $_POST["precioCuatro"],

				"partidaCinco" => $_POST["partidaCinco"],

				"precioCinco" => $_POST["precioCinco"],

				"partidaSeis" => $_POST["partidaSeis"],

				"precioSeis" => $_POST["precioSeis"],

				"partidaSiete" => $_POST["partidaSiete"],

				"precioSiete" => $_POST["precioSiete"],

				"partidaOcho" => $_POST["partidaOcho"],

				"precioOcho" => $_POST["precioOcho"],

				"partidaNueve" => $_POST["partidaNueve"],

				"precioNueve" => $_POST["precioNueve"],

				"partidaDiez" => $_POST["partidaDiez"],

				"precioDiez" => $_POST["precioDiez"],

				"listatOrdenes" => $_POST["listatOrdenes"],

				"costoTotalDeOrden" => $_POST["costoTotalDeOrden"],

				"listatOrdenesNuevas" => $_POST["listatOrdenesNuevas"],

				"listarinversiones" => isset($_POST["listarinversiones"]) ? $_POST["listarinversiones"] : '',

				"totalInversiones" => isset($_POST["totalInversiones"]) ? $_POST["totalInversiones"] : 0,

				//	   "marcaDelEquipo"=>$_POST["marcaDelEquipo"],

				//	   "modeloDelEquipo"=>$_POST["modeloDelEquipo"],

				//	   "numeroDeSerieDelEquipo"=>$_POST["numeroDeSerieDelEquipo"]



			);









			if ($datosOrdenDinamica["estado"] == "Entregado (Ent)") {



				date_default_timezone_set("America/Mexico_City");



				$fecha = date('Y-m-d H:i:s');



				$fechaActual = $fecha;



				$tabla = "ordenes";



				$datosOrdenDinamicaFecha = array(

					"id" => $_POST["idOrden"],

					"asesor" => $_POST["asesorEditadoEnOrdenDianmica"],

					"tecnico" => $_POST["tecnicoEditadoEnOrdenDianmica"],

					"tecnicodos" => isset($_POST["tecnicodosEditadoEnOrdenDianmica"]) ? intval($_POST["tecnicodosEditadoEnOrdenDianmica"]) : 0,

					"estado" => "Entregado (Ent)",

					"partidaUno" => $_POST["partidaUno"],

					"precioUno" => $_POST["precioUno"],

					"partidaDos" => $_POST["partidaDos"],

					"precioDos" => $_POST["precioDos"],

					"partidaTres" => $_POST["partidaTres"],

					"precioTres" => $_POST["precioTres"],

					"partidaCuatro" => $_POST["partidaCuatro"],

					"precioCuatro" => $_POST["precioCuatro"],

					"partidaCinco" => $_POST["partidaCinco"],

					"precioCinco" => $_POST["precioCinco"],

					"partidaSeis" => $_POST["partidaSeis"],

					"precioSeis" => $_POST["precioSeis"],

					"partidaSiete" => $_POST["partidaSiete"],

					"precioSiete" => $_POST["precioSiete"],

					"partidaOcho" => $_POST["partidaOcho"],

					"precioOcho" => $_POST["precioOcho"],

					"partidaNueve" => $_POST["partidaNueve"],

					"precioNueve" => $_POST["precioNueve"],

					"partidaDiez" => $_POST["partidaDiez"],

					"precioDiez" => $_POST["precioDiez"],

					"listatOrdenes" => $_POST["listatOrdenes"],

					"costoTotalDeOrden" => $_POST["costoTotalDeOrden"],

					"listatOrdenesNuevas" => $_POST["listatOrdenesNuevas"],

					"listarinversiones" => isset($_POST["listarinversiones"]) ? $_POST["listarinversiones"] : '',

					"totalInversiones" => isset($_POST["totalInversiones"]) ? $_POST["totalInversiones"] : 0,

					"fecha_Salida" => $fechaActual,

					"marcaDelEquipo" => isset($_POST["marcaDelEquipo"]) ? $_POST["marcaDelEquipo"] : '',

					"modeloDelEquipo" => isset($_POST["modeloDelEquipo"]) ? $_POST["modeloDelEquipo"] : '',

					"numeroDeSerieDelEquipo" => isset($_POST["numeroDeSerieDelEquipo"]) ? $_POST["numeroDeSerieDelEquipo"] : ''

				);





				$respuestaUno = ModeloOrdenes::mdlEditarFechaSalida($tabla, $datosOrdenDinamicaFecha);



				echo '<!-- notifications-push -->	





									<script>



										Push.create("ENTREGADA",{



											body:"ORDEN: ' . $_POST["idOrden"] . '",

											icon:"' . $_SESSION["foto"] . '",

											timeout:10000,

											onClick: function(){

												window.location="index.php?ruta=inicio";

												this.close();

											}



											});

									</script>';





			}



			$respuesta = ModeloOrdenes::mdlEditarOrdenDinamica($tabla, $datosOrdenDinamica);

			// ── Notificaciones de cambio de estado (dinámica) ──
			if (
				$respuesta == "ok" && !empty($_egs_dyn_estadoAnt)
				&& $_egs_dyn_estadoAnt !== $_POST["estado"]
			) {
				try {
					ControladorNotificaciones::ctrCrearTablaEstado();
					ControladorNotificaciones::ctrRegistrarCambioEstado(array(
						"id_orden" => intval($_POST["idOrden"]),
						"estado_anterior" => $_egs_dyn_estadoAnt,
						"estado_nuevo" => $_POST["estado"],
						"id_usuario_accion" => isset($_SESSION["id"]) ? intval($_SESSION["id"]) : 0,
						"nombre_usuario" => isset($_SESSION["nombre"]) ? $_SESSION["nombre"] : "Sistema",
						"titulo_orden" => $_egs_dyn_tituloOrden,
						"id_empresa" => isset($_SESSION["empresa"]) ? intval($_SESSION["empresa"]) : 0,
						"id_asesor" => intval($_POST["asesorEditadoEnOrdenDianmica"]),
						"id_tecnico" => intval($_POST["tecnicoEditadoEnOrdenDianmica"]),
						"id_tecnicoDos" => isset($_POST["tecnicodosEditadoEnOrdenDianmica"]) ? intval($_POST["tecnicodosEditadoEnOrdenDianmica"]) : 0,
					));
				} catch (Exception $e) {
				}
			}

			// ── Notificación de traspaso de técnico (dinámica) ──
			if (
				$respuesta == "ok" && $_egs_dyn_tecnicoAnt > 0
				&& $_egs_dyn_tecnicoAnt !== intval($_POST["tecnicoEditadoEnOrdenDianmica"])
				&& intval($_POST["tecnicoEditadoEnOrdenDianmica"]) > 0
			) {
				try {
					ControladorNotificaciones::ctrCrearTablaEstado();
					$_egs_dTecAntNom = "Técnico #" . $_egs_dyn_tecnicoAnt;
					$_egs_dTecNuevoNom = "Técnico #" . $_POST["tecnicoEditadoEnOrdenDianmica"];
					try {
						$_egs_dTA = ControladorTecnicos::ctrMostrarTecnicos("id", $_egs_dyn_tecnicoAnt);
						if (is_array($_egs_dTA) && isset($_egs_dTA["nombre"]))
							$_egs_dTecAntNom = $_egs_dTA["nombre"];
						$_egs_dTN = ControladorTecnicos::ctrMostrarTecnicos("id", intval($_POST["tecnicoEditadoEnOrdenDianmica"]));
						if (is_array($_egs_dTN) && isset($_egs_dTN["nombre"]))
							$_egs_dTecNuevoNom = $_egs_dTN["nombre"];
					} catch (Exception $ex) {
					}

					ControladorNotificaciones::ctrRegistrarCambioEstado(array(
						"id_orden" => intval($_POST["idOrden"]),
						"estado_anterior" => $_egs_dTecAntNom,
						"estado_nuevo" => $_egs_dTecNuevoNom,
						"id_usuario_accion" => isset($_SESSION["id"]) ? intval($_SESSION["id"]) : 0,
						"nombre_usuario" => isset($_SESSION["nombre"]) ? $_SESSION["nombre"] : "Sistema",
						"titulo_orden" => $_egs_dyn_tituloOrden,
						"id_empresa" => isset($_SESSION["empresa"]) ? intval($_SESSION["empresa"]) : 0,
						"id_asesor" => intval($_POST["asesorEditadoEnOrdenDianmica"]),
						"id_tecnico" => intval($_POST["tecnicoEditadoEnOrdenDianmica"]),
						"id_tecnicoDos" => isset($_POST["tecnicodosEditadoEnOrdenDianmica"]) ? intval($_POST["tecnicodosEditadoEnOrdenDianmica"]) : 0,
						"tipo" => "traspaso",
					));
				} catch (Exception $e) {
				}
			}

			if ($respuesta == "ok") {





				echo '<script>







					swal({



						type: "success",

						title: "¡La orden se ha guardado correctamente!",

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

	DESCARGAR REPORTE DE INGRESOS ORDENES EXCEL 

	=============================================*/



	public function ctrDescargarReporteOrdenesIngresos(

		$valorEmpresa
	) {



		if (isset($_GET["reporte"])) {



			$tabla = "ordenes";



			if (isset($_GET["fechaInicial"]) && isset($_GET["fechaFinal"])) {



				$itemUno = "id_empresa";



				$OrdenesFecha = ModeloOrdenes::mdlRangoFechasOrdenesingresadas($tabla, $_GET["fechaInicial"], $_GET["fechaFinal"], $itemUno, $valorEmpresa);





			} else {



				$item = "id_empresa";

				$valor = $valorEmpresa;



				$OrdenesFecha = ModeloOrdenes::mdlMostrarordenesParaValidar($tabla, $item, $valor);

			}







			/*=============================================

			REPORTE DE INGRESOS DE ORDENES

			=============================================*/



			$Name = $_GET["reporte"] . '.xls';



			header('Expires: 0');

			header('Cache-control: private');

			header("Content-type: application/vnd.ms-excel"); // Archivo de Excel

			header("Cache-Control: cache, must-revalidate");

			header('Content-Description: File Transfer');

			header('Last-Modified: ' . date('D, d M Y H:i:s'));

			header("Pragma: public");

			header('Content-Disposition:; filename="' . $Name . '"');

			header("Content-Transfer-Encoding: binary");



			echo utf8_decode("<table border='0'> 



					<tr> 

						<td style='font-weight:bold; border:1px solid #eee;'>Orden</td>

						<td style='font-weight:bold; border:1px solid #eee;'>Empresa</td>

						<td style='font-weight:bold; border:1px solid #eee;'>Asesor</td>

						<td style='font-weight:bold; border:1px solid #eee;'>Tecnico</td>

						<td style='font-weight:bold; border:1px solid #eee;'>Cliente</td>

						<td style='font-weight:bold; border:1px solid #eee;'>Estado</td>

						<td style='font-weight:bold; border:1px solid #eee;'>Fecha Ingreso</td>

					</tr>");





			foreach ($OrdenesFecha as $key => $value) {



				$item = "id";

				$valor = $value["id_empresa"];



				$NameEmpresa = ControladorVentas::ctrMostrarEmpresasParaTiketimp($item, $valor);



				$NombreEmpresa = $NameEmpresa["empresa"];



				//TRAER ASESOR



				$item = "id";

				$valor = $value["id_Asesor"];



				$asesor = Controladorasesores::ctrMostrarAsesoresEleg($item, $valor);



				$NombreAsesor = $asesor["nombre"];



				//TRAER CLIENTE (USUARIO)



				$item = "id";

				$valor = $value["id_usuario"];



				$usuario = ControladorClientes::ctrMostrarClientes($item, $valor);



				$NombreUsuario = $usuario["nombre"];



				//TRAER TECNICO

				$item = "id";

				$valor = $value["id_tecnico"];



				$tecnico = ControladorTecnicos::ctrMostrarTecnicos($item, $valor);



				$NombreTecnico = $tecnico["nombre"];



				//$ElTotal = number_format($total["total"],2);





				/*=============================================

				TRAER EMAIL DATOS DE COMPRA

				=============================================*/



				echo utf8_decode("<tr>

									 <td style='border:1px solid #eee;'>" . $value["id"] . "</td>

									 <td style='border:1px solid #eee;'>" . $NombreEmpresa . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $NombreAsesor . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $NombreTecnico . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $NombreUsuario . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $value["estado"] . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $value["fecha_ingreso"] . "</td>

			 					  	 </tr>");



			}



			echo utf8_decode("</table>");

		}



	}



	/*=============================================

	DESCARGAR REPORTE ORDENES PARA VENTA

	=============================================*/



	public function ctrReporteOrdenesParaVenta($valorEmpresa)
	{



		if (isset($_GET["reporte"])) {



			$tabla = "ordenes";

			$itemEmpresa = "id_empresa";



			if (isset($_GET["fechaInicial"]) && isset($_GET["fechaFinal"])) {



				$OrdenesFecha = ModeloOrdenes::mdlRangoFechasOrdenesAut($tabla, $_GET["fechaInicial"], $_GET["fechaFinal"], $itemEmpresa, $valorEmpresa);





			} else {



				$estado = "Producto para venta";

				$item = "id_empresa";

				$valor = $valorEmpresa;

				$OrdenesFecha = ModeloOrdenes::mdlMostrarOrdenesPorEstado($tabla, $estado, $item, $valor);

			}







			/*=============================================

			CREAMOS EL ARCHIVO DE EXCEL

			=============================================*/



			$Name = $_GET["reporte"] . '.xls';



			header('Expires: 0');

			header('Cache-control: private');

			header("Content-type: application/vnd.ms-excel"); // Archivo de Excel

			header("Cache-Control: cache, must-revalidate");

			header('Content-Description: File Transfer');

			header('Last-Modified: ' . date('D, d M Y H:i:s'));

			header("Pragma: public");

			header('Content-Disposition:; filename="' . $Name . '"');

			header("Content-Transfer-Encoding: binary");



			echo utf8_decode("<table border='0'> 



					<tr> 

						<td style='font-weight:bold; border:1px solid #eee;'>Orden</td>

						<td style='font-weight:bold; border:1px solid #eee;'>Empresa</td>

						<td style='font-weight:bold; border:1px solid #eee;'>Asesor</td>

						<td style='font-weight:bold; border:1px solid #eee;'>Tecnico</td>

						<td style='font-weight:bold; border:1px solid #eee;'>Cliente</td>

						<td style='font-weight:bold; border:1px solid #eee;'>Estado</td>

						<td style='font-weight:bold; border:1px solid #eee;'>total</td>

						<td style='font-weight:bold; border:1px solid #eee;'>fecha</td>

					</tr>");





			foreach ($OrdenesFecha as $key => $value) {



				$item = "id";

				$valor = $value["id_empresa"];



				$NameEmpresa = ControladorVentas::ctrMostrarEmpresasParaTiketimp($item, $valor);



				$NombreEmpresa = $NameEmpresa["empresa"];



				//TRAER ASESOR



				$item = "id";

				$valor = $value["id_Asesor"];



				$asesor = Controladorasesores::ctrMostrarAsesoresEleg($item, $valor);



				$NombreAsesor = $asesor["nombre"];



				//TRAER CLIENTE (USUARIO)



				$item = "id";

				$valor = $value["id_usuario"];



				$usuario = ControladorClientes::ctrMostrarClientes($item, $valor);



				$NombreUsuario = $usuario["nombre"];



				//$ElTotal = number_format($total["total"],2);

				//TRAER TECNICO

				$item = "id";

				$valor = $value["id_tecnico"];



				$tecnico = ControladorTecnicos::ctrMostrarTecnicos($item, $valor);



				$NombreTecnico = $tecnico["nombre"];



				/*=============================================

				TRAER EMAIL DATOS DE COMPRA

				=============================================*/



				echo utf8_decode("</td>

									 <td style='border:1px solid #eee;'>" . $value["id"] . "</td>

									 <td style='border:1px solid #eee;'>" . $NombreEmpresa . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $NombreAsesor . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $NombreTecnico . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $NombreUsuario . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $value["estado"] . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $value["total"] . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $value["fecha"] . "</td>

			 					  	 </tr>");



			}

			/*=============================================

			TRAER TOTAL

			=============================================*/

			$tabla = "ordenes";

			$estado = "Pendiente de autorización (AUT";

			$total = ModeloOrdenes::mdlSumarTotalOrdenes($tabla, $_GET["fechaInicial"], $_GET["fechaFinal"], $valorEmpresa, $estado);

			foreach ($total as $key => $valueTotal) {

				echo utf8_decode("<tr><td style='font-weight:bold; border:1px solid #eee;'>TOTAL</td></tr>");



				echo utf8_decode("<tr><td style='border:1px solid #eee;'>$" . $valueTotal["total"] . "</td>

			 					  	 	</tr>");

			}







			echo utf8_decode("</table>



					");

		}



	}





	public function ctrlMostrarOrdenesPorEstadoEmpresayTecnico($estado, $item, $valor, $tecnico, $valorTecnico)
	{



		$tabla = "ordenes";



		$respuesta = ModeloOrdenes::mdlMostrarOrdenesPorEstadoEmpresayTecnico($tabla, $estado, $item, $valor, $tecnico, $valorTecnico);



		return $respuesta;

	}



	/*=============================================

	DESCARGAR REPORTE ORDENES Aceptadas Ok

	=============================================*/



	public function ctrDescargarReporteOrdenesOkTecnico($estado, $item, $valor, $tecnico, $valorTecnico)
	{



		if (isset($_GET["reporte"])) {



			$tabla = "ordenes";

			$OrdenesFecha = ModeloOrdenes::mdlMostrarOrdenesPorEstadoEmpresayTecnico($tabla, $estado, $item, $valor, $tecnico, $valorTecnico);



			/*=============================================

			CREAMOS EL ARCHIVO DE EXCEL

			=============================================*/



			$Name = $_GET["reporte"] . '.xls';



			header('Expires: 0');

			header('Cache-control: private');

			header("Content-type: application/xls"); // Archivo de Excel

			header("Cache-Control: cache, must-revalidate");

			header('Content-Description: File Transfer');

			header('Last-Modified: ' . date('D, d M Y H:i:s'));

			header("Pragma: public");

			header('Content-Disposition:; filename="' . $Name . '"');

			header("Content-Transfer-Encoding: binary");



			echo utf8_decode("<table border='0'> 



					<tr> 

						<td style='font-weight:bold; border:1px solid #eee;'>Orden</td>

						<td style='font-weight:bold; border:1px solid #eee;'>Empresa</td>

						<td style='font-weight:bold; border:1px solid #eee;'>Asesor</td>

						<td style='font-weight:bold; border:1px solid #eee;'>Tecnico</td>

						<td style='font-weight:bold; border:1px solid #eee;'>Cliente</td>

						<td style='font-weight:bold; border:1px solid #eee;'>Estado</td>

						<td style='font-weight:bold; border:1px solid #eee;'>Monto</td>

						<td style='font-weight:bold; border:1px solid #eee;'>fecha</td>

					</tr>");





			foreach ($OrdenesFecha as $key => $value) {



				$item = "id";

				$valor = $value["id_empresa"];



				$NameEmpresa = ControladorVentas::ctrMostrarEmpresasParaTiketimp($item, $valor);



				$NombreEmpresa = $NameEmpresa["empresa"];



				//TRAER ASESOR



				$item = "id";

				$valor = $value["id_Asesor"];



				$asesor = Controladorasesores::ctrMostrarAsesoresEleg($item, $valor);



				$NombreAsesor = $asesor["nombre"];



				//TRAER CLIENTE (USUARIO)



				$item = "id";

				$valor = $value["id_usuario"];



				$usuario = ControladorClientes::ctrMostrarClientes($item, $valor);



				$NombreUsuario = $usuario["nombre"];



				//$ElTotal = number_format($total["total"],2);



				//TRAER TECNICO

				$item = "id";

				$valor = $value["id_tecnico"];



				$tecnico = ControladorTecnicos::ctrMostrarTecnicos($item, $valor);



				$NombreTecnico = $tecnico["nombre"];





				/*=============================================

				TRAER EMAIL DATOS DE COMPRA

				=============================================*/



				echo utf8_decode("<tr>

									 <td style='border:1px solid #eee;'>" . $value["id"] . "</td>

									 <td style='border:1px solid #eee;'>" . $NombreEmpresa . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $NombreAsesor . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $NombreTecnico . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $NombreUsuario . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $value["estado"] . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $value["total"] . "</td>

			 					  	 <td style='border:1px solid #eee;'>" . $value["fecha"] . "</td>

			 					  	 </tr>");

			}







			echo utf8_decode("</table>



					");

		}



	}

	/*=============================================

AGREGAR ORDENES CON PARTIDAS LISTADAS

=============================================*/

	public function ctrlEditarMarca()
	{





		if (isset($_POST["marcaDelEquipo"])) {



			$tabla = "ordenes";



			$datos = array(

				"id" => $_POST["idOrden"],

				"marcaDelEquipo" => $_POST["marcaDelEquipo"],

				"modeloDelEquipo" => $_POST["modeloDelEquipo"],

				"numeroDeSerieDelEquipo" => $_POST["numeroDeSerieDelEquipo"]

			);



			$respuesta = ModeloOrdenes::mdlEditarMarca($tabla, $datos);





		}

	}



	/*=============================================

	DESCARGAR REPORTE ORDENES ENTREGADAS EXCEL 

	=============================================*/



	public function ctrDescargarReporteOrdenesMarca($valorEmpresa)
	{



		if (isset($_GET["reporte"])) {



			$tabla = "ordenes";

			$itemEmpresa = "id_empresa";

			if (isset($_GET["fechaInicial"]) && isset($_GET["fechaFinal"])) {



				$OrdenesFecha = ModeloOrdenes::mdlRangoFechasOrdenesPorEmpresa($tabla, $_GET["fechaInicial"], $_GET["fechaFinal"], $itemEmpresa, $valorEmpresa);





			} else {



				$tabla = "ordenes";

				$item = "id_empresa";

				$valor = $valorEmpresa;



				$OrdenesFecha = ModeloOrdenes::mdlMostrarordenesParaValidar($tabla, $item, $valor);

			}







			/*=============================================
			CREAMOS EL ARCHIVO DE EXCEL
			=============================================*/
			$Name = $_GET["reporte"] . '.xls';
			header('Expires: 0');
			header('Cache-control: private');
			header("Content-type: application/vnd.ms-excel"); // Archivo de Excel
			header("Cache-Control: cache, must-revalidate");
			header('Content-Description: File Transfer');
			header('Last-Modified: ' . date('D, d M Y H:i:s'));
			header("Pragma: public");
			header('Content-Disposition:; filename="' . $Name . '"');
			header("Content-Transfer-Encoding: binary");

			echo utf8_decode("<table border='0'> 

					<tr> 
						<td style='font-weight:bold; border:1px solid #eee;'>Orden</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Tecnico</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Marca</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Modelo</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Serie</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Estado</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Cliente</td>
					</tr>");

			foreach ($OrdenesFecha as $key => $value) {

				$item = "id";
				$valor = $value["id_empresa"];
				$NameEmpresa = ControladorVentas::ctrMostrarEmpresasParaTiketimp($item, $valor);

				$NombreEmpresa = $NameEmpresa["empresa"];
				//TRAER ASESOR                   

				$item = "id";
				$valor = $value["id_Asesor"];
				$asesor = Controladorasesores::ctrMostrarAsesoresEleg($item, $valor);

				$NombreAsesor = $asesor["nombre"];
				$departamentoAsesor = $asesor["departamento"];

				//TRAER CLIENTE (USUARIO)
				$item = "id";
				$valor = $value["id_usuario"];
				$usuario = ControladorClientes::ctrMostrarClientes($item, $valor);
				$NombreUsuario = $usuario["nombre"];

				//TRAER TECNICO
				$item = "id";
				$valor = $value["id_tecnico"];
				$tecnico = ControladorTecnicos::ctrMostrarTecnicos($item, $valor);

				$NombreTecnico = $tecnico["nombre"];
				$departamento = $tecnico["departamento"];

				//$ElTotal = number_format($total["total"],2);

				/*=============================================
				TRAER EMAIL DATOS DE COMPRA
				=============================================*/

				echo utf8_decode("</td>
									 <td style='border:1px solid #eee;'>" . $value["id"] . "</td>
									 <td style='border:1px solid #eee;'>" . $NombreTecnico . "</td>
			 					  	 <td style='border:1px solid #eee;'>" . $value["marcaDelEquipo"] . "</td>
			 					  	 <td style='border:1px solid #eee;'>" . $value["modeloDelEquipo"] . "</td>
			 					  	 <td style='border:1px solid #eee;'>" . $value["numeroDeSerieDelEquipo"] . "</td>
			 					  	 <td style='border:1px solid #eee;'>" . $value["estado"] . "</td>
			 					  	 <td style='border:1px solid #eee;'>" . $NombreUsuario . "</td>
			 					  	 </tr>");


			}



		}



	}


	/*===========================
	DESCARGAR REPORTE INFO ORDEN
	============================*/
	public function ctrDescargarReporteInfoOrden($valorEmpresa)
	{


		if (isset($_GET["reporte"])) {


			$tabla = "ordenes";
			$item = "id_empresa";
			$valor = $valorEmpresa;

			$ordenesInfo = ModeloOrdenes::mdlMostrarordenesParaValidar($tabla, $item, $valor);

			/*=============================================
			CREAMOS EL ARCHIVO DE EXCEL
			=============================================*/
			$Name = $_GET["reporte"] . '.xls';
			header('Expires: 0');
			header('Cache-control: private');
			header("Content-type: application/vnd.ms-excel"); // Archivo de Excel
			header("Cache-Control: cache, must-revalidate");
			header('Content-Description: File Transfer');
			header('Last-Modified: ' . date('D, d M Y H:i:s'));
			header("Pragma: public");
			header('Content-Disposition:; filename="' . $Name . '"');
			header("Content-Transfer-Encoding: binary");

			echo utf8_decode("<table border='0'> 

					<tr> 
						<td style='font-weight:bold; border:1px solid #eee;'>Orden</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Técnico</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Estado</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Cliente</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Partidas recepción</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Partidas</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Costo</td>
						
					");

			foreach ($ordenesInfo as $key => $valuOrdenesInfo) {

				$item = "id";
				$valor = $valuOrdenesInfo["id_empresa"];
				$NameEmpresa = ControladorVentas::ctrMostrarEmpresasParaTiketimp($item, $valor);

				$NombreEmpresa = $NameEmpresa["empresa"];
				//TRAER ASESOR                   

				$item = "id";
				$valor = $valuOrdenesInfo["id_Asesor"];
				$asesor = Controladorasesores::ctrMostrarAsesoresEleg($item, $valor);

				$NombreAsesor = $asesor["nombre"];
				$departamentoAsesor = $asesor["departamento"];

				//TRAER CLIENTE (USUARIO)
				$item = "id";
				$valor = $valuOrdenesInfo["id_usuario"];
				$usuario = ControladorClientes::ctrMostrarClientes($item, $valor);
				$NombreUsuario = $usuario["nombre"];

				//TRAER TECNICO
				$item = "id";
				$valor = $valuOrdenesInfo["id_tecnico"];
				$tecnico = ControladorTecnicos::ctrMostrarTecnicos($item, $valor);

				$NombreTecnico = $tecnico["nombre"];
				$departamento = $tecnico["departamento"];

				//$ElTotal = number_format($total["total"],2);

				/*=============================================
				TRAER EMAIL DATOS DE COMPRA
				=============================================*/
				$partidas = json_decode($valuOrdenesInfo["partidas"], true);

				foreach ($partidas as $key => $itemDetallesPartidas) {

					$descripcioPartida = $itemDetallesPartidas["descripcion"];
					$valorProducto = $itemDetallesPartidas["precioPartida"];

				}

				$observaciones = json_decode($valuOrdenesInfo["observaciones"], true);


				foreach ($observaciones as $key => $itemobservaciones) {


					echo utf8_decode("<td style='border:1px solid #eee;'>Observaciones</td>");



				}
				echo utf8_decode("</tr>");
				echo utf8_decode("</td>
									 <td style='border:1px solid #eee;'>" . $valuOrdenesInfo["id"] . "</td>
									 <td style='border:1px solid #eee;'>" . $NombreTecnico . "</td>
			 					  	 <td style='border:1px solid #eee;'>" . $valuOrdenesInfo["estado"] . "</td>
			 					  	 <td style='border:1px solid #eee;'>" . $NombreUsuario . "</td>
			 					  	 <td style='border:1px solid #eee;'>" . $valuOrdenesInfo["partidaUno"] . "</br>
			 					  	 " . $valuOrdenesInfo["partidaDos"] . "
			 					  	 " . $valuOrdenesInfo["partidaDos"] . "
			 					  	 </td>
			 					  	 <td style='border:1px solid #eee;'>" . $descripcioPartida . "</td>
			 					  	 <td style='border:1px solid #eee;'>" . $valorProducto . "</td>
			 					  	");

				foreach ($observaciones as $key => $itemobservaciones) {

					$obs = $itemobservaciones["observacion"];
					echo utf8_decode("<td style='border:1px solid #eee;'>" . $obs . "</td>");



				}
				echo utf8_decode("</tr>");




			}

		}

	}


}