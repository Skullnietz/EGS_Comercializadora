<?php

require_once "../controladores/administradores.controlador.php";
require_once "../modelos/administradores.modelo.php";
require_once "../modelos/tecnicos.modelo.php";
require_once "../modelos/modelo.asesores.php";

if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

class AjaxAdministradores{

	private function puedeGestionar($perfil){

		if (!isset($_SESSION["validarSesionBackend"]) || $_SESSION["validarSesionBackend"] !== "ok") {
			return false;
		}

		if (!isset($_SESSION["perfil"]) || !in_array($_SESSION["perfil"], array("administrador", "Super-Administrador"), true)) {
			return false;
		}

		if ($_SESSION["perfil"] === "Super-Administrador") {
			return true;
		}

		return isset($perfil["id_empresa"], $_SESSION["empresa"])
			&& intval($perfil["id_empresa"]) === intval($_SESSION["empresa"]);
	}

	/*=============================================
	ACTIVAR PERFIL
	=============================================*/	

	public $activarPerfil;
	public $activarId;

	public function ajaxActivarPerfil(){

		$tabla = "administradores";

		$item1 = "estado";
		$valor1 = $this->activarPerfil;

		$item2 = "id";
		$valor2 = $this->activarId;

		$profile = ModeloAdministradores::mdlMostrarAdministradores($tabla, "id", $this->activarId);

		if (!$profile || !$this->puedeGestionar($profile)) {
			http_response_code(403);
			echo "error";
			return;
		}

		$respuesta = ModeloAdministradores::mdlActualizarPerfil($tabla, $item1, $valor1, $item2, $valor2);

		if($respuesta == "ok" && $profile){
			$estadoStr = ($valor1 == 1 || $valor1 == "1") ? "Activo" : "Inactivo";
			$email = $profile["email"];
			
			if($profile["perfil"] == "tecnico"){
				$tec = ModeloTecnicos::mdlMostrarTecnicos("tecnicos", "correo", $email);
				if($tec){
					ModeloTecnicos::mdlActualizarTecnico("tecnicos", "estado", $estadoStr, "id", $tec["id"]);
				}
			} elseif($profile["perfil"] == "vendedor"){
				$ase = ModeloAsesores::mdlMostrarAsesoresEleg("asesores", "correo", $email);
				if($ase){
					$datosAse = array(
						"id" => $ase["id"],
						"nombre" => $ase["nombre"],
						"correo" => $ase["correo"],
						"numerodeCelular" => $ase["numerodeCelular"],
						"numeroTelefono" => $ase["numeroTelefono"],
						"porcentajeComision" => $ase["porcentajeComision"],
						"estado" => $estadoStr,
						"id_empresa" => $profile["id_empresa"]
					);
					ModeloAsesores::mdlEditarAsesor("asesores", $datosAse);
				}
			}
		}

		echo $respuesta;

	}

	/*=============================================
	EDITAR PERFIL
	=============================================*/	

	public $idPerfil;

	public function ajaxEditarPerfil(){

		$item = "id";
		$valor = $this->idPerfil;

		$respuesta = ControladorAdministradores::ctrMostrarAdministradores($item, $valor);

		if (!$respuesta || !$this->puedeGestionar($respuesta)) {
			http_response_code(403);
			header("Content-Type: application/json; charset=utf-8");
			echo json_encode(array("error" => "No tienes permisos para editar este perfil."));
			return;
		}

		header("Content-Type: application/json; charset=utf-8");
		echo json_encode($respuesta);

	}



}

/*=============================================
ACTIVAR PERFIL
=============================================*/	

if(isset($_POST["activarPerfil"])){

	$activarPerfil = new AjaxAdministradores();
	$activarPerfil -> activarPerfil = $_POST["activarPerfil"];
	$activarPerfil -> activarId = $_POST["activarId"];
	$activarPerfil -> ajaxActivarPerfil();

}

/*=============================================
EDITAR PERFIL
=============================================*/
if(isset($_POST["idPerfil"])){

	$editar = new AjaxAdministradores();
	$editar -> idPerfil = $_POST["idPerfil"];
	$editar -> ajaxEditarPerfil();

}
