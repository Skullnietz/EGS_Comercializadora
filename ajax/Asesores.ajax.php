<?php

require_once "../controladores/controlador.asesore.php";
require_once "../modelos/modelo.asesores.php";

if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

class AjaxAsesores{

	public $idAsesor;

	private function puedeConsultar($asesor){
		if (!isset($_SESSION["validarSesionBackend"]) || $_SESSION["validarSesionBackend"] !== "ok") {
			return false;
		}

		if (!isset($_SESSION["perfil"]) || !in_array($_SESSION["perfil"], array("administrador", "Super-Administrador"), true)) {
			return false;
		}

		if ($_SESSION["perfil"] === "Super-Administrador") {
			return true;
		}

		return isset($asesor["id_empresa"], $_SESSION["empresa"])
			&& intval($asesor["id_empresa"]) === intval($_SESSION["empresa"]);
	}

	public function ajaxEditarAsesor(){
		$respuesta = Controladorasesores::ctrMostrarAsesoresEleg("id", intval($this->idAsesor));
		header("Content-Type: application/json; charset=utf-8");

		if (!$respuesta || !$this->puedeConsultar($respuesta)) {
			http_response_code(403);
			echo json_encode(array("error" => "No tienes permisos para editar este asesor."));
			return;
		}

		echo json_encode($respuesta);
	}
}

if (isset($_POST["idAsesor"])) {
	$editar = new AjaxAsesores();
	$editar->idAsesor = $_POST["idAsesor"];
	$editar->ajaxEditarAsesor();
}
