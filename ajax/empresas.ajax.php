<?php

require_once "../controladores/empresas.controlador.php";
require_once "../modelos/empresas.modelo.php";

if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

class AjaxEmpresas{

	public $idEmpresa;

	public function ajaxEditarEmpresa(){
		header("Content-Type: application/json; charset=utf-8");

		if (!isset($_SESSION["validarSesionBackend"])
			|| $_SESSION["validarSesionBackend"] !== "ok"
			|| !isset($_SESSION["perfil"])
			|| !in_array($_SESSION["perfil"], array("administrador", "Super-Administrador"), true)) {
			http_response_code(403);
			echo json_encode(array("error" => "No tienes permisos para editar empresas."));
			return;
		}

		$respuesta = ControladorEmpresas::ctrMostrarEmpresas("id", intval($this->idEmpresa));

		if (!$respuesta) {
			http_response_code(404);
			echo json_encode(array("error" => "La empresa ya no está disponible."));
			return;
		}

		echo json_encode($respuesta);
	}
}

if (isset($_POST["idEmpresa"])) {
	$editar = new AjaxEmpresas();
	$editar->idEmpresa = $_POST["idEmpresa"];
	$editar->ajaxEditarEmpresa();
}
