<?php

class ControladorTecnicos
{
	/*=================================
	MOSTRAR TECNICOS
	=================================*/
	static public function ctrMostrarTecnicos($item, $valor)
	{
		$tabla = "tecnicos";

		return ModeloTecnicos::mdlMostrarTecnicos($tabla, $item, $valor);
	}

	/*=================================
	MOSTRAR TECNICOS PARA EMPRESAS
	=================================*/
	static public function ctrMostrarTecnicosDeEmpresas($item, $valor, $soloActivos = true)
	{
		$tabla = "tecnicos";

		return ModeloTecnicos::mdlMostrarTecnicosDeEmpresa($tabla, $item, $valor, $soloActivos);
	}

	/*=================================
	CREAR TECNICO
	=================================*/
	public function ctrCrearTecnico()
	{
		if (!isset($_POST["NombreDelTecnico"])) {
			return;
		}

		$nombre = trim($_POST["NombreDelTecnico"]);
		$correo = trim(isset($_POST["Emailtecnico"]) ? $_POST["Emailtecnico"] : "");
		$telefono = trim(isset($_POST["numeroTelTecnico"]) ? $_POST["numeroTelTecnico"] : "");
		$telefonoDos = trim(isset($_POST["numeroTelDosTecnico"]) ? $_POST["numeroTelDosTecnico"] : "");
		$horaDeComida = trim(isset($_POST["HoraDeComida"]) ? $_POST["HoraDeComida"] : "");
		$departamento = trim(isset($_POST["areratecnico"]) ? $_POST["areratecnico"] : "");
		$estado = isset($_POST["estadoTecnico"]) ? $_POST["estadoTecnico"] : "Activo";
		$idEmpresa = self::resolverEmpresaPermitida(isset($_POST["empresa"]) ? $_POST["empresa"] : 0);

		if (
			!self::validarNombre($nombre) ||
			!self::validarCorreo($correo) ||
			!self::validarTelefono($telefono, true) ||
			!self::validarTelefono($telefonoDos, false) ||
			!self::validarHorario($horaDeComida) ||
			!self::validarDepartamento($departamento) ||
			!self::validarEstado($estado) ||
			$idEmpresa <= 0
		) {
			self::mostrarAlerta(
				"error",
				"No fue posible guardar el técnico. Revisa los campos obligatorios y la empresa asignada."
			);
			return;
		}

		$datos = array(
			"nombre" => $nombre,
			"correo" => $correo,
			"telefono" => $telefono,
			"telefonoDos" => $telefonoDos,
			"HoraDeComida" => $horaDeComida,
			"areratecnico" => $departamento,
			"empresa" => $idEmpresa,
			"estado" => $estado
		);

		$respuesta = ModeloTecnicos::mdlCrearTecnico("tecnicos", $datos);

		if ($respuesta === "ok") {
			self::mostrarAlerta("success", "El técnico se guardó correctamente.");
		} else {
			self::mostrarAlerta("error", "No fue posible guardar el técnico. Inténtalo nuevamente.");
		}
	}

	/*=================================
	EDITAR TECNICO
	=================================*/
	public function ctrEditarTecnico()
	{
		if (!isset($_POST["idTecnico"])) {
			return;
		}

		$idTecnico = intval($_POST["idTecnico"]);
		$tecnicoActual = $idTecnico > 0
			? ModeloTecnicos::mdlMostrarTecnicos("tecnicos", "id", $idTecnico)
			: false;

		if (!$tecnicoActual || !self::puedeAdministrarTecnico($tecnicoActual)) {
			self::mostrarAlerta("error", "No tienes permiso para editar este técnico.");
			return;
		}

		$nombre = trim(isset($_POST["editarNombreTecnico"]) ? $_POST["editarNombreTecnico"] : "");
		$correo = trim(isset($_POST["editarEmailTecnico"]) ? $_POST["editarEmailTecnico"] : "");
		$telefono = trim(isset($_POST["editarNumeroUnoTecnico"]) ? $_POST["editarNumeroUnoTecnico"] : "");
		$telefonoDos = trim(isset($_POST["editarTelefonoDosTecnico"]) ? $_POST["editarTelefonoDosTecnico"] : "");
		$horaDeComida = trim(isset($_POST["HoraDeComidaEditada"]) ? $_POST["HoraDeComidaEditada"] : "");
		$departamento = trim(isset($_POST["editarAreaTecnico"]) ? $_POST["editarAreaTecnico"] : "");
		$estado = isset($_POST["estado"]) ? $_POST["estado"] : "";
		$idEmpresa = self::resolverEmpresaPermitida(
			isset($_POST["editarEmpresaTecnico"]) ? $_POST["editarEmpresaTecnico"] : 0
		);

		if (
			!self::validarNombre($nombre) ||
			!self::validarCorreo($correo) ||
			!self::validarTelefono($telefono, true) ||
			!self::validarTelefono($telefonoDos, false) ||
			!self::validarHorario($horaDeComida) ||
			!self::validarDepartamento($departamento) ||
			!self::validarEstado($estado) ||
			$idEmpresa <= 0
		) {
			self::mostrarAlerta(
				"error",
				"No fue posible guardar los cambios. Revisa los campos obligatorios y la empresa asignada."
			);
			return;
		}

		$datos = array(
			"id" => $idTecnico,
			"nombre" => $nombre,
			"correo" => $correo,
			"telefono" => $telefono,
			"telefonoDos" => $telefonoDos,
			"HoraDeComidaEditada" => $horaDeComida,
			"departamento" => $departamento,
			"id_empresa" => $idEmpresa,
			"estado" => $estado
		);

		$respuesta = ModeloTecnicos::mdlEditarTecnico("tecnicos", $datos);

		if ($respuesta === "ok") {
			self::mostrarAlerta("success", "Los datos del técnico se actualizaron correctamente.");
		} else {
			self::mostrarAlerta("error", "No fue posible actualizar el técnico. Inténtalo nuevamente.");
		}
	}

	/*=============================================
	ELIMINAR TECNICO
	=============================================*/
	static public function ctrEliminarTecnico()
	{
		if (!isset($_GET["idtecnico"])) {
			return;
		}

		$idTecnico = intval($_GET["idtecnico"]);
		$tecnicoActual = $idTecnico > 0
			? ModeloTecnicos::mdlMostrarTecnicos("tecnicos", "id", $idTecnico)
			: false;

		if (!$tecnicoActual || !self::puedeAdministrarTecnico($tecnicoActual)) {
			self::mostrarAlerta("error", "No tienes permiso para eliminar este técnico.");
			return;
		}

		$respuesta = ModeloTecnicos::mdlEliminarTecnico("tecnicos", $idTecnico);

		if ($respuesta === "ok") {
			self::mostrarAlerta("success", "El técnico se eliminó correctamente.");
		} else {
			self::mostrarAlerta("error", "No fue posible eliminar el técnico.");
		}
	}

	private static function resolverEmpresaPermitida($empresaSolicitada)
	{
		if (self::esSuperAdministrador()) {
			return intval($empresaSolicitada);
		}

		return isset($_SESSION["empresa"]) ? intval($_SESSION["empresa"]) : 0;
	}

	private static function puedeAdministrarTecnico($tecnico)
	{
		if (self::esSuperAdministrador()) {
			return true;
		}

		$empresaSesion = isset($_SESSION["empresa"]) ? intval($_SESSION["empresa"]) : 0;
		$empresaTecnico = isset($tecnico["id_empresa"]) ? intval($tecnico["id_empresa"]) : 0;

		return $empresaSesion > 0 && $empresaSesion === $empresaTecnico;
	}

	private static function esSuperAdministrador()
	{
		return isset($_SESSION["perfil"]) && $_SESSION["perfil"] === "Super-Administrador";
	}

	private static function validarNombre($nombre)
	{
		return $nombre !== "" &&
			strlen($nombre) <= 120 &&
			preg_match("/^[\p{L}\p{N}][\p{L}\p{N}\s.'-]*$/u", $nombre);
	}

	private static function validarCorreo($correo)
	{
		return strlen($correo) <= 150 && filter_var($correo, FILTER_VALIDATE_EMAIL) !== false;
	}

	private static function validarTelefono($telefono, $obligatorio)
	{
		if ($telefono === "") {
			return !$obligatorio;
		}

		return strlen($telefono) <= 25;
	}

	private static function validarHorario($horario)
	{
		return strlen($horario) <= 50;
	}

	private static function validarDepartamento($departamento)
	{
		return $departamento !== "" &&
			strlen($departamento) <= 80 &&
			preg_match("/^[\p{L}\p{N}\s.'()\/-]+$/u", $departamento);
	}

	private static function validarEstado($estado)
	{
		return in_array($estado, array("Activo", "Inactivo"), true);
	}

	private static function mostrarAlerta($tipo, $mensaje)
	{
		$configuracion = array(
			"type" => $tipo,
			"title" => $mensaje,
			"showConfirmButton" => true,
			"confirmButtonText" => "Cerrar"
		);

		echo '<script>
			swal(' . json_encode($configuracion, JSON_UNESCAPED_UNICODE) . ').then(function(result) {
				if (result.value) {
					window.location = "index.php?ruta=tecnicos";
				}
			});
		</script>';
	}
}
