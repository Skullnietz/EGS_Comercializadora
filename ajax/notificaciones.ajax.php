<?php

require_once "../modelos/notificaciones.modelo.php";
require_once "../modelos/conexionWordpress.php";

Class AjaxNotificaciones{

	/*=============================================
	ACTUALIZAR NOTIFICACIONES (legacy)
	=============================================*/

	public $item;

	public function ajaxActualizarNotificaciones(){

		$item = $this->item;
		$valor = 0;

		$respuesta = ModeloNotificaciones::mdlActualizarNotificaciones("notificaciones", $item, $valor);

		echo $respuesta;

	}

}

if(isset($_POST["item"])){

	$actualizarNotificaciones = new AjaxNotificaciones();
	$actualizarNotificaciones -> item = $_POST["item"];
	$actualizarNotificaciones -> ajaxActualizarNotificaciones();

}

/*=============================================
MARCAR NOTIFICACIONES DE ESTADO COMO LEÍDAS
=============================================*/

if(isset($_POST["marcarLeidasEstado"])){

	session_start();

	$perfil    = isset($_SESSION["perfil"]) ? $_SESSION["perfil"] : "";
	$idEmpresa = isset($_SESSION["empresa"]) ? intval($_SESSION["empresa"]) : 0;
	$idRol     = null;

	if ($perfil === "vendedor") {
		require_once "../controladores/asesores.controlador.php";
		require_once "../modelos/asesores.modelo.php";
		$asesor = Controladorasesores::ctrMostrarAsesoresEleg("correo", $_SESSION["email"]);
		if (is_array($asesor) && isset($asesor["id"])) {
			$idRol = intval($asesor["id"]);
		}
	} elseif ($perfil === "tecnico") {
		require_once "../controladores/tecnicos.controlador.php";
		require_once "../modelos/tecnicos.modelo.php";
		$tecnico = ControladorTecnicos::ctrMostrarTecnicos("correo", $_SESSION["email"]);
		if (is_array($tecnico) && isset($tecnico["id"])) {
			$idRol = intval($tecnico["id"]);
		}
	}

	$respuesta = ModeloNotificaciones::mdlMarcarLeidasEstado($perfil, $idEmpresa, $idRol);

	echo $respuesta;

}
