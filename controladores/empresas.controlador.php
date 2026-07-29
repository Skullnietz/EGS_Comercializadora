<?php

class ControladorEmpresas{

	private static function telefonoValido($telefono, $obligatorio = true){
		$telefono = trim((string)$telefono);

		if ($telefono === "") {
			return !$obligatorio;
		}

		return preg_match('/^[0-9+() .-]{7,25}$/', $telefono);
	}

	private static function mostrarAlerta($tipo, $titulo){
		echo '<script>
			swal({
				type: ' . json_encode($tipo) . ',
				title: ' . json_encode($titulo) . ',
				showConfirmButton: true,
				confirmButtonText: "Cerrar"
			}).then(function(){ window.location = "index.php?ruta=empresas"; });
		</script>';
	}

	/* Mostrar empresas */
	public static function ctrMostrarEmpresas($item, $valor){
		return ModeloEmpresas::mdlMostrarEmpresas("empresa", $item, $valor);
	}

	/* Mostrar datos de empresa para editar */
	public static function ctrMostrarEmpresasParaEditar($item, $valor){
		return ModeloEmpresas::mdlMostrarEmpresasParaEditar("empresa", $item, $valor);
	}

	/* Mostrar datos de empresa para reportes */
	public static function ctrMostrarEmpresasParaReportes($item, $valor){
		return ModeloEmpresas::mdlMostrarEmpresasParaReportes("empresa", $item, $valor);
	}

	/* Crear empresa */
	public function ctrCrearEmpresa(){
		if (!isset($_POST["empresa"])) {
			return;
		}

		$nombre = trim($_POST["empresa"]);
		$correo = trim($_POST["correo"]);
		$telefonoUno = trim($_POST["telefonoDeEmpresa"]);
		$telefonoDos = isset($_POST["telefonoDosDeEmpresa"]) ? trim($_POST["telefonoDosDeEmpresa"]) : "";
		$direccion = trim($_POST["direccion"]);
		$horario = trim($_POST["Horario"]);

		if ($nombre === ""
			|| !filter_var($correo, FILTER_VALIDATE_EMAIL)
			|| !self::telefonoValido($telefonoUno)
			|| !self::telefonoValido($telefonoDos, false)
			|| $direccion === ""
			|| $horario === "") {
			self::mostrarAlerta("error", "Revisa los datos de la empresa antes de guardar.");
			return;
		}

		$datos = array(
			"empresa" => $nombre,
			"correo" => $correo,
			"telefonoDeEmpresa" => $telefonoUno,
			"telefonoDosDeEmpresa" => $telefonoDos,
			"direccion" => $direccion,
			"Horario" => $horario,
			"Facebook" => isset($_POST["Facebook"]) ? trim($_POST["Facebook"]) : "",
			"Sitio" => isset($_POST["Sitio"]) ? trim($_POST["Sitio"]) : ""
		);

		$respuesta = ModeloEmpresas::mdlcrearEmpresas("empresa", $datos);

		if ($respuesta === "ok") {
			self::mostrarAlerta("success", "La empresa se guardó correctamente.");
		} else {
			self::mostrarAlerta("error", "No fue posible guardar la empresa.");
		}
	}

	/* Editar empresa */
	public static function ctrEditarEmpresa(){
		if (!isset($_POST["idEmpresa"])) {
			return;
		}

		$nombre = trim($_POST["editarNombreEmpresa"]);
		$correo = trim($_POST["editarCorreoEmpresa"]);
		$telefonoUno = trim($_POST["editarNumeroUnoDeEmpresa"]);
		$telefonoDos = isset($_POST["telefonoDosDeEmpresaEditado"]) ? trim($_POST["telefonoDosDeEmpresaEditado"]) : "";
		$direccion = trim($_POST["EditarDireccion"]);
		$horario = trim($_POST["HoraEditada"]);

		if ($nombre === ""
			|| !filter_var($correo, FILTER_VALIDATE_EMAIL)
			|| !self::telefonoValido($telefonoUno)
			|| !self::telefonoValido($telefonoDos, false)
			|| $direccion === ""
			|| $horario === "") {
			self::mostrarAlerta("error", "Revisa los datos de la empresa antes de guardar.");
			return;
		}

		$datos = array(
			"id" => intval($_POST["idEmpresa"]),
			"empresa" => $nombre,
			"correo" => $correo,
			"editarNumeroUnoDeEmpresa" => $telefonoUno,
			"telefonoDosDeEmpresaEditado" => $telefonoDos,
			"direccion" => $direccion,
			"HoraEditada" => $horario,
			"FacebookEditado" => isset($_POST["FacebookEditado"]) ? trim($_POST["FacebookEditado"]) : "",
			"SitioEditado" => isset($_POST["SitioEditado"]) ? trim($_POST["SitioEditado"]) : ""
		);

		$respuesta = ModeloEmpresas::mdlEditarEmpresa("empresa", $datos);

		if ($respuesta === "ok") {
			self::mostrarAlerta("success", "La empresa se actualizó correctamente.");
		} else {
			self::mostrarAlerta("error", "No fue posible actualizar la empresa.");
		}
	}

	/* Eliminar empresa */
	public static function ctrEliminarEmpresa(){
		if (!isset($_GET["idEmpresa"])) {
			return;
		}

		$respuesta = ModeloEmpresas::mdlEliminarEmpresa("empresa", intval($_GET["idEmpresa"]));

		if ($respuesta === "ok") {
			self::mostrarAlerta("success", "La empresa se eliminó correctamente.");
		} else {
			self::mostrarAlerta("error", "No fue posible eliminar la empresa; verifica que no tenga registros asignados.");
		}
	}
}
