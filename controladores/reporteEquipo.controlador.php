<?php
class controladorReporteEquipo
{

	/* Perfiles autorizados para crear/eliminar (igual que observaciones) */
	static private $perfilesPermitidos = array("administrador", "vendedor", "tecnico", "secretaria");

	/*=============================================
	MOSTRAR REPORTES DE UNA ORDEN
	=============================================*/
	static public function ctrMostrarReportes($idOrden)
	{
		$tabla = "reporteEstadoEquipo";
		return ModeloReporteEquipo::mdlMostrarReportes($tabla, $idOrden);
	}

	/*=============================================
	MOSTRAR FOTOS DE LOS REPORTES DE UNA ORDEN
	=============================================*/
	static public function ctrMostrarFotosPorOrden($idOrden)
	{
		$tabla = "reporteEstadoEquipoFotos";
		return ModeloReporteEquipo::mdlMostrarFotosPorOrden($tabla, $idOrden);
	}

	/*=============================================
	REPORTES RECIENTES PARA NOTIFICACION
	=============================================*/
	static public function ctrReportesRecientesNotif($idUsuario, $limite = 15, $ordenIds = null)
	{
		return ModeloReporteEquipo::mdlReportesRecientesNotif(
			"reporteEstadoEquipo",
			$idUsuario,
			$limite,
			$ordenIds
		);
	}

	/*=============================================
	CREAR REPORTE (con token anti-duplicado)
	=============================================*/
	static public function ctrlCrearReporte()
	{
		if (isset($_POST["descripcionReporte"])) {

			if (!isset($_SESSION)) session_start();

			// Permiso
			if (!isset($_SESSION["perfil"]) || !in_array($_SESSION["perfil"], self::$perfilesPermitidos, true)) {
				return;
			}

			// Token anti-duplicado: evita reenvío por refresco
			$token = isset($_POST["_rep_token"]) ? $_POST["_rep_token"] : "";
			if (!empty($token) && isset($_SESSION["_rep_token_used"]) && $_SESSION["_rep_token_used"] === $token) {
				echo '<script>window.history.back();</script>';
				return;
			}

			$tabla = "reporteEstadoEquipo";
			$datos = array(
				"id_creador"  => $_POST["id_creador"],
				"id_orden"    => $_POST["id_orden"],
				"descripcion" => $_POST["descripcionReporte"]
			);

			$respuesta = ModeloReporteEquipo::mdlCrearReporte($tabla, $datos);

			if ($respuesta !== "error") {

				$idReporte = is_numeric($respuesta) ? intval($respuesta) : 0;

				if ($idReporte > 0) {
					self::ctrlGuardarFotosReporte($idReporte, intval($_POST["id_orden"]));
				}

				if (!empty($token)) {
					$_SESSION["_rep_token_used"] = $token;
				}

				echo '<script>
				swal({
					type: "success",
					title: "¡Reporte guardado correctamente!",
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
	GUARDAR FOTOS ADJUNTAS A UN REPORTE
	Reutiliza el optimizador de imágenes de controladorOrdenes.
	Carpetas por año-mes para poder borrar por espacio con el tiempo.
	=============================================*/
	static private function ctrlGuardarFotosReporte($idReporte, $idOrden)
	{
		if (empty($_FILES["fotosReporte"]) || !isset($_FILES["fotosReporte"]["tmp_name"])) {
			return;
		}

		if (!class_exists("controladorOrdenes")) {
			return; // optimizador no disponible
		}

		$tablaFotos = "reporteEstadoEquipoFotos";
		$nombres    = $_FILES["fotosReporte"]["name"];
		$maxFotos   = 8;

		// Normalizar a arreglo (input multiple => arreglos paralelos)
		if (!is_array($nombres)) {
			$nombres = array($nombres);
			$_FILES["fotosReporte"]["tmp_name"] = array($_FILES["fotosReporte"]["tmp_name"]);
			$_FILES["fotosReporte"]["type"]     = array($_FILES["fotosReporte"]["type"]);
			$_FILES["fotosReporte"]["size"]     = array($_FILES["fotosReporte"]["size"]);
			$_FILES["fotosReporte"]["error"]    = array($_FILES["fotosReporte"]["error"]);
		}

		$total = min(count($nombres), $maxFotos);

		// Ruta absoluta (independiente del CWD): este flujo corre desde index.php en la raíz.
		$subRuta     = date("Y-m") . "/" . intval($idOrden);
		$directorio  = __DIR__ . "/../vistas/img/reporte-equipo/" . $subRuta;
		$rutaWebBase = "vistas/img/reporte-equipo/" . $subRuta;

		for ($i = 0; $i < $total; $i++) {

			if (empty($_FILES["fotosReporte"]["tmp_name"][$i])) continue;
			if (isset($_FILES["fotosReporte"]["error"][$i]) && $_FILES["fotosReporte"]["error"][$i] !== UPLOAD_ERR_OK) continue;

			$archivo = array(
				"name"     => $_FILES["fotosReporte"]["name"][$i],
				"type"     => $_FILES["fotosReporte"]["type"][$i],
				"tmp_name" => $_FILES["fotosReporte"]["tmp_name"][$i],
				"error"    => $_FILES["fotosReporte"]["error"][$i],
				"size"     => $_FILES["fotosReporte"]["size"][$i]
			);

			$nombreBase = "rep-" . $idReporte . "-" . ($i + 1) . "-" . substr(md5(uniqid('', true)), 0, 8);

			$procesada = controladorOrdenes::ctrOptimizarImagenSubida($archivo, array(
				"directorio" => $directorio,
				"nombre"     => $nombreBase,
				"ancho"      => 1600,
				"alto"       => 1600,
				"modo"       => "contain",
				"max_bytes"  => 10485760
			));

			if (!is_array($procesada) || empty($procesada["ok"])) {
				continue;
			}

			$rutaWeb = $rutaWebBase . "/" . basename($procesada["ruta"]);

			ModeloReporteEquipo::mdlCrearFotoReporte($tablaFotos, array(
				"id_reporte" => $idReporte,
				"id_orden"   => $idOrden,
				"ruta"       => $rutaWeb
			));
		}
	}

	/*=============================================
	ELIMINAR REPORTE (y sus fotos físicas + registros)
	=============================================*/
	static public function ctrEliminarReporte()
	{
		if (isset($_GET["idReporteEquipo"])) {

			if (!isset($_SESSION)) session_start();
			if (!isset($_SESSION["perfil"]) || !in_array($_SESSION["perfil"], self::$perfilesPermitidos, true)) {
				return;
			}

			$idReporte = intval($_GET["idReporteEquipo"]);

			// Borrar primero las fotos físicas y sus registros
			$rutasFotos = ModeloReporteEquipo::mdlEliminarFotosPorReporte("reporteEstadoEquipoFotos", $idReporte);
			if (is_array($rutasFotos)) {
				foreach ($rutasFotos as $rutaFoto) {
					$rutaFisica = __DIR__ . "/../" . ltrim($rutaFoto, "/");
					if (!empty($rutaFoto) && file_exists($rutaFisica)) {
						@unlink($rutaFisica);
					}
				}
			}

			$respuesta = ModeloReporteEquipo::mdlEliminarReporte("reporteEstadoEquipo", $idReporte);

			if ($respuesta == "ok") {
				echo '<script>
				swal({
					type: "success",
					title: "El reporte ha sido borrado correctamente",
					showConfirmButton: true,
					confirmButtonText: "Cerrar"
				}).then(function(result){
					if (result.value) {
						window.history.back();
					}
				});
				</script>';
			}
		}
	}
}
