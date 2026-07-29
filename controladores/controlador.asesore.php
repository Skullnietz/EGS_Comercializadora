<?php

class Controladorasesores{

	private static function esSuperAdministrador(){
		return isset($_SESSION["perfil"]) && $_SESSION["perfil"] === "Super-Administrador";
	}

	private static function resolverEmpresa($empresaSolicitada){
		if (self::esSuperAdministrador()) {
			return intval($empresaSolicitada);
		}

		return isset($_SESSION["empresa"]) ? intval($_SESSION["empresa"]) : 0;
	}

	private static function puedeGestionar($asesor){
		if (!$asesor || !isset($_SESSION["perfil"])) {
			return false;
		}

		if (self::esSuperAdministrador()) {
			return true;
		}

		return $_SESSION["perfil"] === "administrador"
			&& isset($asesor["id_empresa"], $_SESSION["empresa"])
			&& intval($asesor["id_empresa"]) === intval($_SESSION["empresa"]);
	}

	private static function nombreValido($nombre){
		return is_string($nombre)
			&& trim($nombre) !== ""
			&& preg_match("/^[\p{L}\p{M}0-9 .'-]+$/u", trim($nombre));
	}

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
			}).then(function(){ window.location = "index.php?ruta=asesores"; });
		</script>';
	}

	/* Mostrar asesores */
	public function ctrMostrarAsesores(){
		return ModeloAsesores::mdlMostrarAsesores("asesores");
	}

	/* Mostrar uno o todos */
	static public function ctrMostrarAsesoresEleg($item, $valor){
		return ModeloAsesores::mdlMostrarAsesoresEleg("asesores", $item, $valor);
	}

	/* Mostrar asesores de una empresa */
	static public function ctrMostrarAsesoresEmpresas($item, $valor, $soloActivos = true){
		return ModeloAsesores::mdlMostrarAsesoresEmpresas("asesores", $item, $valor, $soloActivos);
	}

	/* Crear asesor */
	public function ctrCrearPerfil(){
		if (!isset($_POST["nuevoNombreAsesor"])) {
			return;
		}

		$nombre = trim($_POST["nuevoNombreAsesor"]);
		$correo = trim($_POST["nuevoEmailAsesor"]);
		$telefonoUno = isset($_POST["nuevoNumeroUno"]) ? trim($_POST["nuevoNumeroUno"]) : "";
		$telefonoDos = isset($_POST["nuevoNumeroDos"]) ? trim($_POST["nuevoNumeroDos"]) : "";
		$empresa = self::resolverEmpresa(isset($_POST["empresa"]) ? $_POST["empresa"] : 0);
		$estado = isset($_POST["nuevoEstadoAsesor"]) && $_POST["nuevoEstadoAsesor"] === "Inactivo"
			? "Inactivo"
			: "Activo";
		$comision = isset($_POST["nuevoPorcentajeComision"]) ? intval($_POST["nuevoPorcentajeComision"]) : 0;

		if (!self::nombreValido($nombre)
			|| !filter_var($correo, FILTER_VALIDATE_EMAIL)
			|| !self::telefonoValido($telefonoUno)
			|| !self::telefonoValido($telefonoDos, false)
			|| $empresa <= 0
			|| $comision < 0
			|| $comision > 100) {
			self::mostrarAlerta("error", "Revisa los datos del asesor antes de guardar.");
			return;
		}

		$datos = array(
			"nombre" => $nombre,
			"correo" => $correo,
			"numerodeCelular" => $telefonoUno,
			"numeroTelefono" => $telefonoDos,
			"empresa" => $empresa,
			"porcentajeComision" => $comision,
			"estado" => $estado
		);

		$respuesta = ModeloAsesores::mdlIngresarAsesores("asesores", $datos);

		if ($respuesta === "ok") {
			self::mostrarAlerta("success", "El asesor se guardó correctamente.");
		} else {
			self::mostrarAlerta("error", "No fue posible guardar el asesor.");
		}
	}

	/* Editar asesor */
	static public function ctrEditarAsesor(){
		if (!isset($_POST["idAsesor"])) {
			return;
		}

		$asesorActual = ModeloAsesores::mdlMostrarAsesoresEleg("asesores", "id", intval($_POST["idAsesor"]));

		if (!self::puedeGestionar($asesorActual)) {
			self::mostrarAlerta("error", "No tienes permisos para editar este asesor.");
			return;
		}

		$nombre = trim($_POST["editarNombreAsesor"]);
		$correo = trim($_POST["editarEmailAsesor"]);
		$telefonoUno = isset($_POST["editarNumeroUno"]) ? trim($_POST["editarNumeroUno"]) : "";
		$telefonoDos = isset($_POST["editarTelefonoDos"]) ? trim($_POST["editarTelefonoDos"]) : "";
		$empresa = self::resolverEmpresa(isset($_POST["editarEmpresaAsesor"]) ? $_POST["editarEmpresaAsesor"] : 0);
		$estado = isset($_POST["estado"]) && $_POST["estado"] === "Inactivo" ? "Inactivo" : "Activo";
		$comision = isset($_POST["editarPorcentajeComision"]) ? intval($_POST["editarPorcentajeComision"]) : 0;

		if (!self::nombreValido($nombre)
			|| !filter_var($correo, FILTER_VALIDATE_EMAIL)
			|| !self::telefonoValido($telefonoUno)
			|| !self::telefonoValido($telefonoDos, false)
			|| $empresa <= 0
			|| $comision < 0
			|| $comision > 100) {
			self::mostrarAlerta("error", "Revisa los datos del asesor antes de guardar.");
			return;
		}

		$datos = array(
			"id" => intval($_POST["idAsesor"]),
			"nombre" => $nombre,
			"correo" => $correo,
			"numerodeCelular" => $telefonoUno,
			"numeroTelefono" => $telefonoDos,
			"porcentajeComision" => $comision,
			"estado" => $estado,
			"id_empresa" => $empresa
		);

		$respuesta = ModeloAsesores::mdlEditarAsesor("asesores", $datos);

		if ($respuesta === "ok") {
			self::mostrarAlerta("success", "El asesor se actualizó correctamente.");
		} else {
			self::mostrarAlerta("error", "No fue posible actualizar el asesor.");
		}
	}

	/* Eliminar asesor */
	static public function ctrEliminarAsesor(){
		if (!isset($_GET["idAsesor"])) {
			return;
		}

		$idAsesor = intval($_GET["idAsesor"]);
		$asesor = ModeloAsesores::mdlMostrarAsesoresEleg("asesores", "id", $idAsesor);

		if (!self::puedeGestionar($asesor)) {
			self::mostrarAlerta("error", "No tienes permisos para eliminar este asesor.");
			return;
		}

		$respuesta = ModeloAsesores::mdlEliminarAsesor("asesores", $idAsesor);

		if ($respuesta === "ok") {
			self::mostrarAlerta("success", "El asesor se eliminó correctamente.");
		} else {
			self::mostrarAlerta("error", "No fue posible eliminar el asesor.");
		}
	}

	/* Mostrar asesores y técnicos */
	public function ctrlMostrarTodosLosEmpleado(){
		return ModeloAsesores::mdlMostrarTodosLosEmpleado("asesores", "tecnicos");
	}
}
