<?php

class controladorOrdenes
{

	/*=============================================
	NOTIFICAR CAMBIO DE ESTADO A SERVICIO NODE (OPCIONAL)
	=============================================*/
	static private function ctrDatosClientePorId($idCliente)
	{
		$idCliente = intval($idCliente);
		if ($idCliente <= 0) {
			return array('nombre' => '', 'whatsapp' => '');
		}

		$cli = null;
		try {
			$cli = ControladorClientes::ctrMostrarClientes('id', $idCliente);
		} catch (Exception $e) {
		}

		if (!is_array($cli)) {
			return array('nombre' => '', 'whatsapp' => '');
		}

		$nombre = isset($cli['nombre']) ? $cli['nombre'] : '';
		$waRaw = '';
		if (isset($cli['telefonoDos'])) {
			$waRaw = $cli['telefonoDos'];
		} elseif (isset($cli['telefonoDosCliente'])) {
			$waRaw = $cli['telefonoDosCliente'];
		} elseif (isset($cli['telefono'])) {
			$waRaw = $cli['telefono'];
		}

		$wa = preg_replace('/[^0-9]/', '', (string) $waRaw);
		return array('nombre' => $nombre, 'whatsapp' => $wa);
	}

	static private function ctrNotificarEstadoNode($contexto)
	{
		$endpoint = '';
		$token = '';
		$timeout = 5;
		$payload = $contexto;

		if (class_exists('ControladorWhatsapp')) {
			try {
				$dispatch = ControladorWhatsapp::ctrPrepararNotificacionEstado($contexto);
				if (is_array($dispatch)) {
					$endpoint = trim((string) ($dispatch['endpoint'] ?? ''));
					$token = trim((string) ($dispatch['token'] ?? ''));
					$timeout = intval($dispatch['timeout'] ?? 5);
					$payload = isset($dispatch['payload']) ? $dispatch['payload'] : $contexto;
				}
			} catch (Exception $e) {
			}
		}

		if ($endpoint === '') {
			$endpoint = trim((string) getenv('NODE_WHATSAPP_ENDPOINT'));
			$token = trim((string) getenv('NODE_WHATSAPP_TOKEN'));
			$timeout = intval(getenv('NODE_WHATSAPP_TIMEOUT') ?: 5);
		}

		if ($endpoint === '') {
			return false;
		}

		$headers = array(
			'Content-Type: application/json'
		);
		if ($token !== '') {
			$headers[] = 'Authorization: Bearer ' . $token;
		}

		$ch = curl_init($endpoint);
		curl_setopt_array($ch, array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST => true,
			CURLOPT_HTTPHEADER => $headers,
			CURLOPT_POSTFIELDS => json_encode($payload),
			CURLOPT_CONNECTTIMEOUT => $timeout,
			CURLOPT_TIMEOUT => $timeout,
		));

		curl_exec($ch);
		$httpCode = intval(curl_getinfo($ch, CURLINFO_HTTP_CODE));
		curl_close($ch);

		return ($httpCode >= 200 && $httpCode < 300);
	}

	static private function ctrNormalizarSlug($texto, $fallback = 'imagen')
	{
		$texto = strtolower(trim((string) $texto));
		$texto = str_replace(
			array('á', 'é', 'í', 'ó', 'ú', 'ñ', 'ü'),
			array('a', 'e', 'i', 'o', 'u', 'n', 'u'),
			$texto
		);
		$texto = preg_replace('/[^a-z0-9_-]+/', '-', $texto);
		$texto = preg_replace('/-+/', '-', $texto);
		$texto = trim($texto, '-_');

		if ($texto === '') {
			$texto = $fallback;
		}

		return $texto;
	}

	static private function ctrValidarArchivoImagen($archivo, $maxBytes = 10485760)
	{
		if (
			!is_array($archivo) ||
			!isset($archivo["tmp_name"]) ||
			empty($archivo["tmp_name"]) ||
			!file_exists($archivo["tmp_name"])
		) {
			return array("ok" => false, "mensaje" => "No se recibió ninguna imagen válida.");
		}

		if (isset($archivo["error"]) && intval($archivo["error"]) !== UPLOAD_ERR_OK) {
			return array("ok" => false, "mensaje" => "La imagen no se pudo subir correctamente.");
		}

		$tamano = isset($archivo["size"]) ? intval($archivo["size"]) : 0;
		if ($tamano <= 0) {
			return array("ok" => false, "mensaje" => "La imagen enviada está vacía.");
		}

		if ($tamano > $maxBytes) {
			return array(
				"ok" => false,
				"mensaje" => "La imagen supera el límite permitido de " . round($maxBytes / 1048576, 1) . " MB."
			);
		}

		$info = @getimagesize($archivo["tmp_name"]);
		if ($info === false || empty($info["mime"])) {
			return array("ok" => false, "mensaje" => "El archivo no es una imagen compatible.");
		}

		$mimesPermitidos = array(
			'image/jpeg',
			'image/png',
			'image/webp',
			'image/gif'
		);

		if (!in_array($info["mime"], $mimesPermitidos, true)) {
			return array("ok" => false, "mensaje" => "Formato no permitido. Usa JPG, PNG, WEBP o GIF.");
		}

		return array(
			"ok" => true,
			"mime" => $info["mime"],
			"ancho" => intval($info[0]),
			"alto" => intval($info[1]),
			"tamano" => $tamano
		);
	}

	static private function ctrImagenTieneTransparencia($imagen, $ancho, $alto, $mime)
	{
		if ($mime === 'image/jpeg') {
			return false;
		}

		if (function_exists('imagecolortransparent') && imagecolortransparent($imagen) >= 0) {
			return true;
		}

		if (!in_array($mime, array('image/png', 'image/webp', 'image/gif'), true)) {
			return false;
		}

		$pasoX = max(1, intval($ancho / 12));
		$pasoY = max(1, intval($alto / 12));

		for ($x = 0; $x < $ancho; $x += $pasoX) {
			for ($y = 0; $y < $alto; $y += $pasoY) {
				$rgba = imagecolorat($imagen, $x, $y);
				$alpha = ($rgba & 0x7F000000) >> 24;
				if ($alpha > 0) {
					return true;
				}
			}
		}

		return false;
	}

	static private function ctrCrearLienzo($ancho, $alto, $transparente)
	{
		$lienzo = imagecreatetruecolor($ancho, $alto);

		if ($transparente) {
			imagealphablending($lienzo, false);
			imagesavealpha($lienzo, true);
			$fondo = imagecolorallocatealpha($lienzo, 0, 0, 0, 127);
		} else {
			$fondo = imagecolorallocate($lienzo, 255, 255, 255);
		}

		imagefilledrectangle($lienzo, 0, 0, $ancho, $alto, $fondo);

		return $lienzo;
	}

	static private function ctrGuardarImagenProcesada($imagen, $rutaDestino, $mimeSalida)
	{
		if ($mimeSalida === 'image/png') {
			imagealphablending($imagen, false);
			imagesavealpha($imagen, true);
			return imagepng($imagen, $rutaDestino, 8);
		}

		imageinterlace($imagen, true);
		return imagejpeg($imagen, $rutaDestino, 82);
	}

	static private function ctrPuedeEliminarArchivo($ruta)
	{
		$ruta = trim((string) $ruta);
		if ($ruta === '' || $ruta === 'vistas/img/default/default.png') {
			return false;
		}

		$rutaFisica = $ruta;
		if (strpos($rutaFisica, '../') !== 0) {
			$rutaFisica = '../' . ltrim($rutaFisica, '/');
		}

		return file_exists($rutaFisica);
	}

	static private function ctrOptimizarImagenSubida($archivo, $config = array())
	{
		$validacion = self::ctrValidarArchivoImagen(
			$archivo,
			isset($config["max_bytes"]) ? intval($config["max_bytes"]) : 10485760
		);

		if (!$validacion["ok"]) {
			return $validacion;
		}

		$contenido = @file_get_contents($archivo["tmp_name"]);
		if ($contenido === false) {
			return array("ok" => false, "mensaje" => "No fue posible leer la imagen recibida.");
		}

		$origen = @imagecreatefromstring($contenido);
		if ($origen === false) {
			return array("ok" => false, "mensaje" => "No fue posible procesar el formato de imagen.");
		}

		$anchoOrigen = $validacion["ancho"];
		$altoOrigen = $validacion["alto"];
		$transparencia = self::ctrImagenTieneTransparencia($origen, $anchoOrigen, $altoOrigen, $validacion["mime"]);
		$mimeSalida = $transparencia ? 'image/png' : 'image/jpeg';
		$extensionSalida = $mimeSalida === 'image/png' ? 'png' : 'jpg';

		$directorio = isset($config["directorio"]) ? rtrim($config["directorio"], '/') : '../vistas/img/multimedia';
		if (!file_exists($directorio) && !mkdir($directorio, 0755, true)) {
			imagedestroy($origen);
			return array("ok" => false, "mensaje" => "No se pudo preparar el directorio de imágenes.");
		}

		$baseNombre = self::ctrNormalizarSlug(
			isset($config["nombre"]) ? $config["nombre"] : pathinfo($archivo["name"], PATHINFO_FILENAME),
			'orden-' . time()
		);

		$rutaDestino = $directorio . '/' . $baseNombre . '.' . $extensionSalida;
		$modo = isset($config["modo"]) ? $config["modo"] : 'contain';
		$objetivoAncho = isset($config["ancho"]) ? max(1, intval($config["ancho"])) : $anchoOrigen;
		$objetivoAlto = isset($config["alto"]) ? max(1, intval($config["alto"])) : $altoOrigen;

		if ($modo === 'canvas') {
			$destino = self::ctrCrearLienzo($objetivoAncho, $objetivoAlto, $transparencia);
			$escala = min($objetivoAncho / $anchoOrigen, $objetivoAlto / $altoOrigen, 1);
			$nuevoAncho = max(1, intval(round($anchoOrigen * $escala)));
			$nuevoAlto = max(1, intval(round($altoOrigen * $escala)));
			$destinoX = intval(($objetivoAncho - $nuevoAncho) / 2);
			$destinoY = intval(($objetivoAlto - $nuevoAlto) / 2);

			imagecopyresampled($destino, $origen, $destinoX, $destinoY, 0, 0, $nuevoAncho, $nuevoAlto, $anchoOrigen, $altoOrigen);
		} else {
			$escala = min($objetivoAncho / $anchoOrigen, $objetivoAlto / $altoOrigen, 1);
			$nuevoAncho = max(1, intval(round($anchoOrigen * $escala)));
			$nuevoAlto = max(1, intval(round($altoOrigen * $escala)));
			$destino = self::ctrCrearLienzo($nuevoAncho, $nuevoAlto, $transparencia);

			imagecopyresampled($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $anchoOrigen, $altoOrigen);
		}

		$guardada = self::ctrGuardarImagenProcesada($destino, $rutaDestino, $mimeSalida);

		imagedestroy($origen);
		imagedestroy($destino);

		if (!$guardada) {
			return array("ok" => false, "mensaje" => "No se pudo guardar la imagen optimizada.");
		}

		return array("ok" => true, "ruta" => $rutaDestino);
	}



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

			$rutaSegura = self::ctrNormalizarSlug($ruta, 'orden-' . time());
			$nombreBase = self::ctrNormalizarSlug(pathinfo($datos["name"], PATHINFO_FILENAME), 'multimedia-' . time());
			$nombreArchivo = $nombreBase . '-' . substr(md5(uniqid('', true)), 0, 8);

			$imagenProcesada = self::ctrOptimizarImagenSubida($datos, array(
				"directorio" => "../vistas/img/multimedia/" . $rutaSegura,
				"nombre" => $nombreArchivo,
				"ancho" => 1600,
				"alto" => 1600,
				"modo" => "contain",
				"max_bytes" => 10485760
			));

			if (!$imagenProcesada["ok"]) {
				return "error::" . $imagenProcesada["mensaje"];
			}

			return $imagenProcesada["ruta"];



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

					$portadaProcesada = self::ctrOptimizarImagenSubida($datos["fotoPortada"], array(
						"directorio" => "../vistas/img/cabeceras",
						"nombre" => $datos["rutaOrden"],
						"ancho" => 1280,
						"alto" => 720,
						"modo" => "canvas",
						"max_bytes" => 10485760
					));

					if (!$portadaProcesada["ok"]) {
						return $portadaProcesada["mensaje"];
					}

					$rutaPortada = $portadaProcesada["ruta"];
				}



				/*=============================================

				VALIDAR IMAGEN PRINCIPAL

				=============================================*/



				$rutaFotoPrincipal = "../vistas/img/default/default.png";



				if (isset($datos["fotoPrincipal"]["tmp_name"]) && !empty($datos["fotoPrincipal"]["tmp_name"])) {

					$principalProcesada = self::ctrOptimizarImagenSubida($datos["fotoPrincipal"], array(
						"directorio" => "../vistas/img/productos",
						"nombre" => $datos["rutaOrden"],
						"ancho" => 400,
						"alto" => 450,
						"modo" => "canvas",
						"max_bytes" => 10485760
					));

					if (!$principalProcesada["ok"]) {
						return $principalProcesada["mensaje"];
					}

					$rutaFotoPrincipal = $principalProcesada["ruta"];
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



						if (self::ctrPuedeEliminarArchivo($value)) {
							unlink("../" . $value);
						}



					}







				}



				/*=============================================

				VALIDAR IMAGEN PORTADA

				=============================================*/



				$rutaPortada = "../" . $datos["antiguaFotoPortada"];



				if (isset($datos["fotoPortada"]["tmp_name"]) && !empty($datos["fotoPortada"]["tmp_name"])) {

					$portadaProcesada = self::ctrOptimizarImagenSubida($datos["fotoPortada"], array(
						"directorio" => "../vistas/img/cabeceras",
						"nombre" => $datos["rutaOrden"],
						"ancho" => 1280,
						"alto" => 720,
						"modo" => "canvas",
						"max_bytes" => 10485760
					));

					if (!$portadaProcesada["ok"]) {
						return $portadaProcesada["mensaje"];
					}

					if (self::ctrPuedeEliminarArchivo($datos["antiguaFotoPortada"])) {
						unlink("../" . $datos["antiguaFotoPortada"]);
					}

					$rutaPortada = $portadaProcesada["ruta"];
				}



				/*=============================================

				VALIDAR IMAGEN PRINCIPAL

				=============================================*/



				$rutaFotoPrincipal = "../" . $datos["antiguaFotoPrincipal"];



				if (isset($datos["fotoPrincipal"]["tmp_name"]) && !empty($datos["fotoPrincipal"]["tmp_name"])) {

					$principalProcesada = self::ctrOptimizarImagenSubida($datos["fotoPrincipal"], array(
						"directorio" => "../vistas/img/productos",
						"nombre" => $datos["rutaOrden"],
						"ancho" => 400,
						"alto" => 450,
						"modo" => "canvas",
						"max_bytes" => 10485760
					));

					if (!$principalProcesada["ok"]) {
						return $principalProcesada["mensaje"];
					}

					if (self::ctrPuedeEliminarArchivo($datos["antiguaFotoPrincipal"])) {
						unlink("../" . $datos["antiguaFotoPrincipal"]);
					}

					$rutaFotoPrincipal = $principalProcesada["ruta"];
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

					// ── Recompensas: canjear monedero si se solicitó ──
					$_egs_montoCanjeOrden  = isset($_POST["montoCanjeMonederoOrden"]) ? floatval($_POST["montoCanjeMonederoOrden"]) : 0;
					$_egs_idClienteOrden   = isset($_POST["idClienteOrden"])          ? intval($_POST["idClienteOrden"])            : intval($datos["cliente"] ?? 0);
					$_egs_totalBrutoOrden  = isset($_POST["totalBrutoMonederoOrden"]) ? floatval($_POST["totalBrutoMonederoOrden"]) : 0;
					if ($_egs_totalBrutoOrden <= 0) {
						$_egs_totalBrutoOrden = isset($_POST["costoTotalDeOrden"]) ? floatval($_POST["costoTotalDeOrden"]) : 0;
					}

					if ($_egs_totalBrutoOrden > 0 && $_egs_montoCanjeOrden > $_egs_totalBrutoOrden) {
						$_egs_montoCanjeOrden = $_egs_totalBrutoOrden;
					}

					if ($_egs_montoCanjeOrden > 0 && $_egs_idClienteOrden > 0) {
						try {
							require_once __DIR__ . "/recompensas.controlador.php";
							require_once __DIR__ . "/../modelos/recompensas.modelo.php";

							$_egs_totalNetoOrden = max(0, $_egs_totalBrutoOrden - $_egs_montoCanjeOrden);
							$_egs_idEmpresa      = isset($_SESSION["empresa"]) ? intval($_SESSION["empresa"]) : null;
							$_egs_idUsuario      = isset($_SESSION["id"])      ? intval($_SESSION["id"])      : null;

							$_egs_canjeResult = ControladorRecompensas::ctrCanjearEnOrden(
								$_egs_idClienteOrden,
								intval($datos["idOrden"]),
								$_egs_montoCanjeOrden,
								$_egs_idEmpresa,
								$_egs_idUsuario,
								$_egs_totalBrutoOrden,
								$_egs_totalNetoOrden
							);

							if ($_egs_canjeResult !== false) {
								ModeloOrdenes::mdlGuardarCanjeMonederoOrden(
									intval($datos["idOrden"]),
									$_egs_totalBrutoOrden,
									$_egs_montoCanjeOrden,
									$_egs_totalNetoOrden
								);
							}
						} catch (Exception $e) {
							// No bloquear la entrega si falla el canje
						}
					}

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

						$_waCli = self::ctrDatosClientePorId(intval($datos["cliente"]));
						self::ctrNotificarEstadoNode(array(
							"id_orden" => intval($datos["idOrden"]),
							"estado_anterior" => $_egs_estadoAnterior,
							"estado_nuevo" => $datos["estado"],
							"id_empresa" => isset($_SESSION["empresa"]) ? intval($_SESSION["empresa"]) : 0,
							"empresa_nombre" => '',
							"id_cliente" => intval($datos["cliente"]),
							"cliente_nombre" => $_waCli['nombre'],
							"cliente_whatsapp" => $_waCli['whatsapp'],
							"id_asesor" => intval($datos["asesor"]),
							"id_tecnico" => intval($datos["tecnico"]),
							"id_usuario_accion" => isset($_SESSION["id"]) ? intval($_SESSION["id"]) : 0
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
		if (!isset($_GET["reporte"])) return;

		$tabla = "ordenes";

		if (isset($_GET["fechaInicial"]) && isset($_GET["fechaFinal"])) {
			$OrdenesFecha = ModeloOrdenes::mdlRangoFechasOrdenesPorEmpresa($tabla, $_GET["fechaInicial"], $_GET["fechaFinal"], "id_empresa", $valorEmpresa);
		} else {
			$OrdenesFecha = ModeloOrdenes::mdlMostrarordenesParaValidar($tabla, "id_empresa", $valorEmpresa);
		}

		/* ── Generar Excel (.xls) ── */
		$Name = $_GET["reporte"] . '.xls';

		header('Expires: 0');
		header('Cache-control: private');
		header("Content-Type: application/vnd.ms-excel; charset=utf-8");
		header("Cache-Control: cache, must-revalidate");
		header('Content-Description: File Transfer');
		header('Last-Modified: ' . date('D, d M Y H:i:s'));
		header("Pragma: public");
		header('Content-Disposition: attachment; filename="' . $Name . '"');

		// BOM UTF-8 para que Excel interprete acentos correctamente
		$output = fopen('php://output', 'w');
		fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

		// Encabezados
		fputcsv($output, [
			'No. Orden', 'Empresa', 'Asesor', 'Técnico', 'Cliente', 'Estado',
			'Marca', 'Modelo', 'No. Serie',
			'Total', 'Inversión', 'Utilidad',
			'Fecha Ingreso', 'Fecha Registro'
		]);

		$sumaTotal = 0;
		$sumaInversion = 0;

		foreach ($OrdenesFecha as $key => $value) {

			$NameEmpresa = ControladorVentas::ctrMostrarEmpresasParaTiketimp("id", $value["id_empresa"]);
			$NombreEmpresa = $NameEmpresa["empresa"] ?? "";

			$asesor = Controladorasesores::ctrMostrarAsesoresEleg("id", $value["id_Asesor"]);
			$NombreAsesor = $asesor["nombre"] ?? "";

			$usuario = ControladorClientes::ctrMostrarClientes("id", $value["id_usuario"]);
			$NombreUsuario = $usuario["nombre"] ?? "";

			$tecnico = ControladorTecnicos::ctrMostrarTecnicos("id", $value["id_tecnico"]);
			$NombreTecnico = $tecnico["nombre"] ?? "";

			$total     = floatval($value["total"]);
			$inversion = floatval($value["totalInversion"] ?? 0);
			$utilidad  = $total - $inversion;

			$sumaTotal     += $total;
			$sumaInversion += $inversion;

			fputcsv($output, [
				$value["id"],
				$NombreEmpresa,
				$NombreAsesor,
				$NombreTecnico,
				$NombreUsuario,
				$value["estado"],
				$value["marcaDelEquipo"] ?? "",
				$value["modeloDelEquipo"] ?? "",
				$value["numeroDeSerieDelEquipo"] ?? "",
				number_format($total, 2, '.', ''),
				number_format($inversion, 2, '.', ''),
				number_format($utilidad, 2, '.', ''),
				$value["fecha_ingreso"] ?? "",
				$value["fecha"] ?? ""
			]);
		}

		// Fila vacía + totales
		fputcsv($output, []);
		fputcsv($output, [
			'', '', '', '', '', '', '', '', 'TOTALES:',
			number_format($sumaTotal, 2, '.', ''),
			number_format($sumaInversion, 2, '.', ''),
			number_format($sumaTotal - $sumaInversion, 2, '.', ''),
			'', ''
		]);
		fputcsv($output, ['Total Órdenes:', count($OrdenesFecha)]);

		fclose($output);
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


			/* Excel output */
			$csvName = $_GET["reporte"] . '.xls';
			header('Expires: 0');
			header('Cache-control: private');
			header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
			header('Cache-Control: cache, must-revalidate');
			header('Content-Description: File Transfer');
			header('Last-Modified: ' . date('D, d M Y H:i:s'));
			header('Pragma: public');
			header('Content-Disposition: attachment; filename="' . $csvName . '"');
			header('Content-Transfer-Encoding: binary');

			$output = fopen('php://output', 'w');
			fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
			fputcsv($output, ['Orden', 'Empresa', 'Asesor', 'Técnico', 'Cliente', 'Estado', 'Total Cobrado', 'Ganancia Neta', 'Fecha Salida']);

			$suma = 0;
			foreach ($OrdenesFecha as $key => $value) {
				$NameEmpresa = ControladorVentas::ctrMostrarEmpresasParaTiketimp("id", $value["id_empresa"]);
				$asesor = Controladorasesores::ctrMostrarAsesoresEleg("id", $value["id_Asesor"]);
				$usuario = ControladorClientes::ctrMostrarClientes("id", $value["id_usuario"]);
				$tecnico = ControladorTecnicos::ctrMostrarTecnicos("id", $value["id_tecnico"]);
				$invercion = $value["totalInversion"]; $totalNetoOrden = $value["total"] - $invercion;

				fputcsv($output, [$value["id"], $NameEmpresa["empresa"], $asesor["nombre"], $tecnico["nombre"], $usuario["nombre"], $value["estado"], $value["total"], $totalNetoOrden, $value["fecha_Salida"]]);
				$suma += $totalNetoOrden;
			}

			// Total
			if (isset($_GET["fechaInicial"]) && isset($_GET["fechaFinal"])) {
				$tabla = "ordenes";
				$estado = "Entregado (Ent)";
				$total = ModeloOrdenes::mdlSumarTotalOrdenes($tabla, $_GET["fechaInicial"], $_GET["fechaFinal"], $valorEmpresa, $estado);
				foreach ($total as $valueTotal) {
					fputcsv($output, ["TOTAL", "", "", "", "", "", "", "", ""]);
					fputcsv($output, [$valueTotal["total"], "", "", "", "", "", "", "", ""]);
				}
			}
			fputcsv($output, ["TOTAL GANANCIA", "", "", "", "", "", "", $suma, ""]);

			fclose($output);

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


			/* Excel output */
			$csvName = $_GET["reporte"] . '.xls';
			header('Expires: 0');
			header('Cache-control: private');
			header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
			header('Cache-Control: cache, must-revalidate');
			header('Content-Description: File Transfer');
			header('Last-Modified: ' . date('D, d M Y H:i:s'));
			header('Pragma: public');
			header('Content-Disposition: attachment; filename="' . $csvName . '"');
			header('Content-Transfer-Encoding: binary');

			$output = fopen('php://output', 'w');
			fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
			fputcsv($output, ['Orden', 'Empresa', 'Asesor', 'Técnico', 'Cliente', 'Estado', 'Total', 'Fecha']);

			foreach ($OrdenesFecha as $key => $value) {
				$NameEmpresa = ControladorVentas::ctrMostrarEmpresasParaTiketimp("id", $value["id_empresa"]);
				$asesor = Controladorasesores::ctrMostrarAsesoresEleg("id", $value["id_Asesor"]);
				$usuario = ControladorClientes::ctrMostrarClientes("id", $value["id_usuario"]);
				$tecnico = ControladorTecnicos::ctrMostrarTecnicos("id", $value["id_tecnico"]);

				fputcsv($output, [$value["id"], $NameEmpresa["empresa"], $asesor["nombre"], $tecnico["nombre"], $usuario["nombre"], $value["estado"], $value["total"], $value["fecha"]]);
			}

			// Total
			if (isset($_GET["fechaInicial"]) && isset($_GET["fechaFinal"])) {
				$tabla = "ordenes";
				$estado = "Aceptado (ok)";
				$total = ModeloOrdenes::mdlSumarTotalOrdenes($tabla, $_GET["fechaInicial"], $_GET["fechaFinal"], $valorEmpresa, $estado);
				foreach ($total as $valueTotal) {
					fputcsv($output, ["TOTAL", "", "", "", "", "", "", ""]);
					fputcsv($output, [$valueTotal["total"], "", "", "", "", "", "", ""]);
				}
			}

			fclose($output);

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







			/* Excel output */
			$csvName = $_GET["reporte"] . '.xls';
			header('Expires: 0');
			header('Cache-control: private');
			header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
			header('Cache-Control: cache, must-revalidate');
			header('Content-Description: File Transfer');
			header('Last-Modified: ' . date('D, d M Y H:i:s'));
			header('Pragma: public');
			header('Content-Disposition: attachment; filename="' . $csvName . '"');
			header('Content-Transfer-Encoding: binary');

			$output = fopen('php://output', 'w');
			fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
			fputcsv($output, ['Orden', 'Empresa', 'Asesor', 'Técnico', 'Cliente', 'Estado', 'Total', 'Fecha']);

			foreach ($OrdenesFecha as $key => $value) {
				$NameEmpresa = ControladorVentas::ctrMostrarEmpresasParaTiketimp("id", $value["id_empresa"]);
				$asesor = Controladorasesores::ctrMostrarAsesoresEleg("id", $value["id_Asesor"]);
				$usuario = ControladorClientes::ctrMostrarClientes("id", $value["id_usuario"]);
				$tecnico = ControladorTecnicos::ctrMostrarTecnicos("id", $value["id_tecnico"]);

				fputcsv($output, [$value["id"], $NameEmpresa["empresa"], $asesor["nombre"], $tecnico["nombre"], $usuario["nombre"], $value["estado"], $value["total"], $value["fecha"]]);
			}

			// Total
			if (isset($_GET["fechaInicial"]) && isset($_GET["fechaFinal"])) {
				$tabla = "ordenes";
				$estado = "Terminada (ter)";
				$total = ModeloOrdenes::mdlSumarTotalOrdenes($tabla, $_GET["fechaInicial"], $_GET["fechaFinal"], $valorEmpresa, $estado);
				foreach ($total as $valueTotal) {
					fputcsv($output, ["TOTAL", "", "", "", "", "", "", ""]);
					fputcsv($output, [$valueTotal["total"], "", "", "", "", "", "", ""]);
				}
			}

			fclose($output);

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







			/* Excel output */
			$csvName = $_GET["reporte"] . '.xls';
			header('Expires: 0');
			header('Cache-control: private');
			header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
			header('Cache-Control: cache, must-revalidate');
			header('Content-Description: File Transfer');
			header('Last-Modified: ' . date('D, d M Y H:i:s'));
			header('Pragma: public');
			header('Content-Disposition: attachment; filename="' . $csvName . '"');
			header('Content-Transfer-Encoding: binary');

			$output = fopen('php://output', 'w');
			fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
			fputcsv($output, ['Orden', 'Empresa', 'Asesor', 'Técnico', 'Cliente', 'Estado', 'Total', 'Fecha']);

			foreach ($OrdenesFecha as $key => $value) {
				$NameEmpresa = ControladorVentas::ctrMostrarEmpresasParaTiketimp("id", $value["id_empresa"]);
				$asesor = Controladorasesores::ctrMostrarAsesoresEleg("id", $value["id_Asesor"]);
				$usuario = ControladorClientes::ctrMostrarClientes("id", $value["id_usuario"]);
				$tecnico = ControladorTecnicos::ctrMostrarTecnicos("id", $value["id_tecnico"]);

				fputcsv($output, [$value["id"], $NameEmpresa["empresa"], $asesor["nombre"], $tecnico["nombre"], $usuario["nombre"], $value["estado"], $value["total"], $value["fecha"]]);
			}

			// Total
			if (isset($_GET["fechaInicial"]) && isset($_GET["fechaFinal"])) {
				$tabla = "ordenes";
				$estado = "Pendiente de autorización (AUT";
				$total = ModeloOrdenes::mdlSumarTotalOrdenes($tabla, $_GET["fechaInicial"], $_GET["fechaFinal"], $valorEmpresa, $estado);
				foreach ($total as $valueTotal) {
					fputcsv($output, ["TOTAL", "", "", "", "", "", "", ""]);
					fputcsv($output, [$valueTotal["total"], "", "", "", "", "", "", ""]);
				}
			}

			fclose($output);

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







			/* Excel output */
			$csvName = $_GET["reporte"] . '.xls';
			header('Expires: 0');
			header('Cache-control: private');
			header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
			header('Cache-Control: cache, must-revalidate');
			header('Content-Description: File Transfer');
			header('Last-Modified: ' . date('D, d M Y H:i:s'));
			header('Pragma: public');
			header('Content-Disposition: attachment; filename="' . $csvName . '"');
			header('Content-Transfer-Encoding: binary');

			$output = fopen('php://output', 'w');
			fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
			fputcsv($output, ['Orden', 'Empresa', 'Asesor', 'Técnico', 'Cliente', 'Estado', 'Total', 'Fecha']);

			foreach ($OrdenesFecha as $key => $value) {
				$NameEmpresa = ControladorVentas::ctrMostrarEmpresasParaTiketimp("id", $value["id_empresa"]);
				$asesor = Controladorasesores::ctrMostrarAsesoresEleg("id", $value["id_Asesor"]);
				$usuario = ControladorClientes::ctrMostrarClientes("id", $value["id_usuario"]);
				$tecnico = ControladorTecnicos::ctrMostrarTecnicos("id", $value["id_tecnico"]);

				fputcsv($output, [$value["id"], $NameEmpresa["empresa"], $asesor["nombre"], $tecnico["nombre"], $usuario["nombre"], $value["estado"], $value["total"], $value["fecha"]]);
			}

			// Total
			if (isset($_GET["fechaInicial"]) && isset($_GET["fechaFinal"])) {
				$tabla = "ordenes";
				$estado = "Supervisión (SUP)";
				$total = ModeloOrdenes::mdlSumarTotalOrdenes($tabla, $_GET["fechaInicial"], $_GET["fechaFinal"], $valorEmpresa, $estado);
				foreach ($total as $valueTotal) {
					fputcsv($output, ["TOTAL", "", "", "", "", "", "", ""]);
					fputcsv($output, [$valueTotal["total"], "", "", "", "", "", "", ""]);
				}
			}

			fclose($output);

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







			/* CSV output */
			$csvName = $_GET["reporte"] . '.csv';
			header('Expires: 0');
			header('Cache-control: private');
			header('Content-Type: text/csv; charset=UTF-8');
			header('Cache-Control: cache, must-revalidate');
			header('Content-Description: File Transfer');
			header('Last-Modified: ' . date('D, d M Y H:i:s'));
			header('Pragma: public');
			header('Content-Disposition: attachment; filename="' . $csvName . '"');
			header('Content-Transfer-Encoding: binary');

			$output = fopen('php://output', 'w');
			fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
			fputcsv($output, ['Orden', 'Empresa', 'Asesor', 'Técnico', 'Cliente', 'Estado', 'Total', 'Fecha']);

			foreach ($OrdenesFecha as $key => $value) {
				$NameEmpresa = ControladorVentas::ctrMostrarEmpresasParaTiketimp("id", $value["id_empresa"]);
				$asesor = Controladorasesores::ctrMostrarAsesoresEleg("id", $value["id_Asesor"]);
				$usuario = ControladorClientes::ctrMostrarClientes("id", $value["id_usuario"]);
				$tecnico = ControladorTecnicos::ctrMostrarTecnicos("id", $value["id_tecnico"]);

				fputcsv($output, [$value["id"], $NameEmpresa["empresa"], $asesor["nombre"], $tecnico["nombre"], $usuario["nombre"], $value["estado"], $value["total"], $value["fecha"]]);
			}

			// Total
			if (isset($_GET["fechaInicial"]) && isset($_GET["fechaFinal"])) {
				$tabla = "ordenes";
				$estado = "En revisión (REV)";
				$total = ModeloOrdenes::mdlSumarTotalOrdenes($tabla, $_GET["fechaInicial"], $_GET["fechaFinal"], $valorEmpresa, $estado);
				foreach ($total as $valueTotal) {
					fputcsv($output, ["TOTAL", "", "", "", "", "", "", ""]);
					fputcsv($output, [$valueTotal["total"], "", "", "", "", "", "", ""]);
				}
			}

			fclose($output);

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



			/* Excel - REPORTE COMISIONES */
			$csvName = $_GET["reporte"] . ".xls";
			header("Expires: 0");
			header("Cache-control: private");
			header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
			header("Cache-Control: cache, must-revalidate");
			header("Content-Description: File Transfer");
			header("Last-Modified: " . date("D, d M Y H:i:s"));
			header("Pragma: public");
			header("Content-Disposition: attachment; filename=\"" . $csvName . "\"");
			header("Content-Transfer-Encoding: binary");

			$output = fopen("php://output", "w");
			fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
			fputcsv($output, ["Orden", "Técnico", "Estado", "Ingreso Bruto", "Utilidad Antes IVA", "Inversión", "Comisión Técnico", "Comisión Vendedor", "Fecha Salida"]);

			$sumaGanancia = 0;
			$sumaCobrado = 0;
			$sumaComisionesTec = 0;
			$sumaComisionesVend = 0;

			foreach ($OrdenesFecha as $key => $valueComisiones) {
				$asesor = Controladorasesores::ctrMostrarAsesoresEleg("id", $valueComisiones["id_Asesor"]);
				$departamentoAsesor = $asesor["departamento"];

				$tecnico = ControladorTecnicos::ctrMostrarTecnicos("id", $valueComisiones["id_tecnico"]);
				$NombreTecnico = $tecnico["nombre"];
				$departamento = $tecnico["departamento"];

				if ($valueComisiones["totalInversion"] == 0) {
					$invercion = 0;
					$totalNetoOrden = $valueComisiones["total"];
					$utilidadAntesIva = $valueComisiones["total"] / 1.16;
				} else {
					$invercion = $valueComisiones["totalInversion"];
					$totalNetoOrden = $valueComisiones["total"] - $invercion;
					$utilidadAntesIva = $valueComisiones["total"] / 1.16;
				}

				$comisionTec = 0;
				if ($departamento == "sistemas") {
					$comisionTec = $totalNetoOrden / 1.16 * 0.04;
				} elseif ($departamento == "electronica") {
					$comisionTec = $totalNetoOrden / 1.16 * 0.2;
				} elseif ($departamento == "impresoras") {
					$comisionTec = $totalNetoOrden / 1.16 * 0.2;
				}
				$sumaComisionesTec += $comisionTec;

				$comisionVededor = 0;
				if ($departamentoAsesor == "ventas") {
					$comisionVededor = $totalNetoOrden / 1.16 * 0.03;
					$sumaComisionesVend += $comisionVededor;
				}

				$sumaGanancia += $totalNetoOrden;
				$sumaCobrado += $valueComisiones["total"];

				fputcsv($output, [
					$valueComisiones["id"],
					$NombreTecnico,
					$valueComisiones["estado"],
					$valueComisiones["total"],
					number_format($utilidadAntesIva, 2),
					$invercion,
					number_format($comisionTec, 2),
					number_format($comisionVededor, 2),
					$valueComisiones["fecha_Salida"]
				]);
			}

			// Totals
			fputcsv($output, []);
			fputcsv($output, ["Total Cobrado", $sumaCobrado, "Total Ganancia", $sumaGanancia, "Total Comisión Técnico", number_format($sumaComisionesTec, 2), "Total Comisión Vendedor", number_format($sumaComisionesVend, 2), ""]);

			fclose($output);

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
			$_egs_dyn_idCliente = 0;
			try {
				$_egs_dyn_ordenAnt = ModeloOrdenes::mdlMostrarordenesParaValidar("ordenes", "id", $_POST["idOrden"]);
				if (is_array($_egs_dyn_ordenAnt) && !empty($_egs_dyn_ordenAnt)) {
					$_egs_dyn_first = reset($_egs_dyn_ordenAnt);
					if (is_array($_egs_dyn_first)) {
						$_egs_dyn_estadoAnt = isset($_egs_dyn_first["estado"]) ? $_egs_dyn_first["estado"] : '';
						$_egs_dyn_tecnicoAnt = isset($_egs_dyn_first["id_tecnico"]) ? intval($_egs_dyn_first["id_tecnico"]) : 0;
						$_egs_dyn_tituloOrden = isset($_egs_dyn_first["titulo"]) ? $_egs_dyn_first["titulo"] : '';
						$_egs_dyn_idCliente = isset($_egs_dyn_first["id_usuario"]) ? intval($_egs_dyn_first["id_usuario"]) : 0;
					}
				}
			} catch (Exception $e) {
			}

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

				// ── Recompensas: canjear dinero electrónico si se solicitó ──
				// (la acumulación es dinámica, no requiere registro)
				try {
					$_egs_idClienteRecomp = isset($_egs_dyn_idCliente) ? $_egs_dyn_idCliente : 0;
					$_egs_idOrdenRecomp = intval($_POST["idOrden"]);
					$_egs_montoCanje = isset($_POST["montoCanjeElectronico"]) ? floatval($_POST["montoCanjeElectronico"]) : 0;

					if ($_egs_montoCanje > 0 && $_egs_idClienteRecomp > 0) {
						ControladorRecompensas::ctrCanjearRecompensa(
							$_egs_idClienteRecomp,
							$_egs_idOrdenRecomp,
							$_egs_montoCanje
						);
					}
				} catch (Exception $e) { /* no romper flujo */ }


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

					$_waCliDyn = self::ctrDatosClientePorId($_egs_dyn_idCliente);
					self::ctrNotificarEstadoNode(array(
						"id_orden" => intval($_POST["idOrden"]),
						"estado_anterior" => $_egs_dyn_estadoAnt,
						"estado_nuevo" => $_POST["estado"],
						"id_empresa" => isset($_SESSION["empresa"]) ? intval($_SESSION["empresa"]) : 0,
						"empresa_nombre" => '',
						"id_cliente" => $_egs_dyn_idCliente,
						"cliente_nombre" => $_waCliDyn['nombre'],
						"cliente_whatsapp" => $_waCliDyn['whatsapp'],
						"id_asesor" => intval($_POST["asesorEditadoEnOrdenDianmica"]),
						"id_tecnico" => intval($_POST["tecnicoEditadoEnOrdenDianmica"]),
						"id_usuario_accion" => isset($_SESSION["id"]) ? intval($_SESSION["id"]) : 0
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







			/* Excel output */
			$csvName = $_GET["reporte"] . '.xls';
			header('Expires: 0');
			header('Cache-control: private');
			header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
			header('Cache-Control: cache, must-revalidate');
			header('Content-Description: File Transfer');
			header('Last-Modified: ' . date('D, d M Y H:i:s'));
			header('Pragma: public');
			header('Content-Disposition: attachment; filename="' . $csvName . '"');
			header('Content-Transfer-Encoding: binary');

			$output = fopen('php://output', 'w');
			fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
			fputcsv($output, ['Orden', 'Empresa', 'Asesor', 'Técnico', 'Cliente', 'Estado', 'Fecha Ingreso']);

			foreach ($OrdenesFecha as $key => $value) {
				$NameEmpresa = ControladorVentas::ctrMostrarEmpresasParaTiketimp("id", $value["id_empresa"]);
				$asesor = Controladorasesores::ctrMostrarAsesoresEleg("id", $value["id_Asesor"]);
				$usuario = ControladorClientes::ctrMostrarClientes("id", $value["id_usuario"]);
				$tecnico = ControladorTecnicos::ctrMostrarTecnicos("id", $value["id_tecnico"]);

				fputcsv($output, [$value["id"], $NameEmpresa["empresa"], $asesor["nombre"], $tecnico["nombre"], $usuario["nombre"], $value["estado"], $value["fecha_ingreso"]]);
			}


			fclose($output);

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







			/* Excel output */
			$csvName = $_GET["reporte"] . '.xls';
			header('Expires: 0');
			header('Cache-control: private');
			header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
			header('Cache-Control: cache, must-revalidate');
			header('Content-Description: File Transfer');
			header('Last-Modified: ' . date('D, d M Y H:i:s'));
			header('Pragma: public');
			header('Content-Disposition: attachment; filename="' . $csvName . '"');
			header('Content-Transfer-Encoding: binary');

			$output = fopen('php://output', 'w');
			fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
			fputcsv($output, ['Orden', 'Empresa', 'Asesor', 'Técnico', 'Cliente', 'Estado', 'Total', 'Fecha']);

			foreach ($OrdenesFecha as $key => $value) {
				$NameEmpresa = ControladorVentas::ctrMostrarEmpresasParaTiketimp("id", $value["id_empresa"]);
				$asesor = Controladorasesores::ctrMostrarAsesoresEleg("id", $value["id_Asesor"]);
				$usuario = ControladorClientes::ctrMostrarClientes("id", $value["id_usuario"]);
				$tecnico = ControladorTecnicos::ctrMostrarTecnicos("id", $value["id_tecnico"]);

				fputcsv($output, [$value["id"], $NameEmpresa["empresa"], $asesor["nombre"], $tecnico["nombre"], $usuario["nombre"], $value["estado"], $value["total"], $value["fecha"]]);
			}

			// Total
			if (isset($_GET["fechaInicial"]) && isset($_GET["fechaFinal"])) {
				$tabla = "ordenes";
				$estado = "Pendiente de autorización (AUT";
				$total = ModeloOrdenes::mdlSumarTotalOrdenes($tabla, $_GET["fechaInicial"], $_GET["fechaFinal"], $valorEmpresa, $estado);
				foreach ($total as $valueTotal) {
					fputcsv($output, ["TOTAL", "", "", "", "", "", "", ""]);
					fputcsv($output, [$valueTotal["total"], "", "", "", "", "", "", ""]);
				}
			}

			fclose($output);

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



			/* CSV output */
			$csvName = $_GET["reporte"] . '.csv';
			header('Expires: 0');
			header('Cache-control: private');
			header('Content-Type: text/csv; charset=UTF-8');
			header('Cache-Control: cache, must-revalidate');
			header('Content-Description: File Transfer');
			header('Last-Modified: ' . date('D, d M Y H:i:s'));
			header('Pragma: public');
			header('Content-Disposition: attachment; filename="' . $csvName . '"');
			header('Content-Transfer-Encoding: binary');

			$output = fopen('php://output', 'w');
			fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
			fputcsv($output, ['Orden', 'Empresa', 'Asesor', 'Técnico', 'Cliente', 'Estado', 'Monto', 'Fecha']);

			foreach ($OrdenesFecha as $key => $value) {
				$NameEmpresa = ControladorVentas::ctrMostrarEmpresasParaTiketimp("id", $value["id_empresa"]);
				$asesor = Controladorasesores::ctrMostrarAsesoresEleg("id", $value["id_Asesor"]);
				$usuario = ControladorClientes::ctrMostrarClientes("id", $value["id_usuario"]);
				$tecnico = ControladorTecnicos::ctrMostrarTecnicos("id", $value["id_tecnico"]);

				fputcsv($output, [$value["id"], $NameEmpresa["empresa"], $asesor["nombre"], $tecnico["nombre"], $usuario["nombre"], $value["estado"], $value["total"], $value["fecha"]]);
			}


			fclose($output);

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







			/* CSV output */
			$csvName = $_GET["reporte"] . '.csv';
			header('Expires: 0');
			header('Cache-control: private');
			header('Content-Type: text/csv; charset=UTF-8');
			header('Cache-Control: cache, must-revalidate');
			header('Content-Description: File Transfer');
			header('Last-Modified: ' . date('D, d M Y H:i:s'));
			header('Pragma: public');
			header('Content-Disposition: attachment; filename="' . $csvName . '"');
			header('Content-Transfer-Encoding: binary');

			$output = fopen('php://output', 'w');
			fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
			fputcsv($output, ['Orden', 'Técnico', 'Marca', 'Modelo', 'Serie', 'Estado', 'Cliente']);

			foreach ($OrdenesFecha as $key => $value) {
				$usuario = ControladorClientes::ctrMostrarClientes("id", $value["id_usuario"]);
				$tecnico = ControladorTecnicos::ctrMostrarTecnicos("id", $value["id_tecnico"]);

				fputcsv($output, [$value["id"], $tecnico["nombre"], $value["marcaDelEquipo"], $value["modeloDelEquipo"], $value["numeroDeSerieDelEquipo"], $value["estado"], $usuario["nombre"]]);
			}


			fclose($output);

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

			/* Excel - REPORTE INFO ORDEN */
			$csvName = $_GET["reporte"] . ".xls";
			header("Expires: 0");
			header("Cache-control: private");
			header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
			header("Cache-Control: cache, must-revalidate");
			header("Content-Description: File Transfer");
			header("Last-Modified: " . date("D, d M Y H:i:s"));
			header("Pragma: public");
			header("Content-Disposition: attachment; filename=\"" . $csvName . "\"");
			header("Content-Transfer-Encoding: binary");

			$output = fopen("php://output", "w");
			fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
			fputcsv($output, ["Orden", "Técnico", "Estado", "Cliente", "Partidas Recepción", "Partidas", "Costo", "Observaciones"]);

			foreach ($ordenesInfo as $key => $valuOrdenesInfo) {
				$usuario = ControladorClientes::ctrMostrarClientes("id", $valuOrdenesInfo["id_usuario"]);
				$tecnico = ControladorTecnicos::ctrMostrarTecnicos("id", $valuOrdenesInfo["id_tecnico"]);

				$partidas = json_decode($valuOrdenesInfo["partidas"], true);
				$descripcioPartida = "";
				$valorProducto = "";
				if (is_array($partidas)) {
					foreach ($partidas as $itemDetallesPartidas) {
						$descripcioPartida = $itemDetallesPartidas["descripcion"];
						$valorProducto = $itemDetallesPartidas["precioPartida"];
					}
				}

				$observaciones = json_decode($valuOrdenesInfo["observaciones"], true);
				$obsText = "";
				if (is_array($observaciones)) {
					$obsList = [];
					foreach ($observaciones as $itemobs) {
						$obsList[] = $itemobs["observacion"];
					}
					$obsText = implode(" | ", $obsList);
				}

				$partidasRecepcion = trim($valuOrdenesInfo["partidaUno"] . " " . $valuOrdenesInfo["partidaDos"]);

				fputcsv($output, [
					$valuOrdenesInfo["id"],
					$tecnico["nombre"],
					$valuOrdenesInfo["estado"],
					$usuario["nombre"],
					$partidasRecepcion,
					$descripcioPartida,
					$valorProducto,
					$obsText
				]);
			}

			fclose($output);

		}

	}

	/*=============================================
	DASHBOARD ASESOR — KPIs EN UNA CONSULTA
	=============================================*/

	static public function ctrDashboardKpisAsesor($idAsesor){

		return ModeloOrdenes::mdlDashboardKpisAsesor($idAsesor);

	}

	/*=============================================
	DASHBOARD ASESOR — ORDENES POR ESTADO
	=============================================*/

	static public function ctrOrdenesAsesorPorEstado($idEmpresa, $idAsesor, $estado){

		return ModeloOrdenes::mdlOrdenesAsesorPorEstado($idEmpresa, $idAsesor, $estado);

	}

	/*=============================================
	DASHBOARD ASESOR — ORDENES RECIENTES (últimos N meses)
	=============================================*/

	static public function ctrlMostrarordenesEmpresayPerfilRecientes($itemOrdenes, $valorOrdenes, $iteDosOrdenes, $valorDosOrdenes, $meses = 13){

		$tabla = "ordenes";

		$respuesta = ModeloOrdenes::mdlMostrarordenesEmpresayPerfilRecientes($tabla, $itemOrdenes, $valorOrdenes, $iteDosOrdenes, $valorDosOrdenes, $meses);

		return $respuesta;

	}


}
