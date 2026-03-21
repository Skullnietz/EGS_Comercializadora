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
		require_once "../controladores/controlador.asesore.php";
		require_once "../modelos/modelo.asesores.php";
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

/*=============================================
POLLING: CONTAR NOTIFICACIONES NUEVAS (liviano)
- Retorna JSON con conteo y últimas notificaciones
- Se llama cada 30-60 seg desde el frontend
=============================================*/

if(isset($_POST["pollNotificaciones"])){

	session_start();

	require_once "../modelos/conexion.php";
	require_once "../modelos/observacionOrdenes.modelo.php";

	$perfil    = isset($_SESSION["perfil"]) ? $_SESSION["perfil"] : "";
	$idEmpresa = isset($_SESSION["empresa"]) ? intval($_SESSION["empresa"]) : 0;
	$idRol     = null;

	if ($perfil === "vendedor") {
		require_once "../controladores/controlador.asesore.php";
		require_once "../modelos/modelo.asesores.php";
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

	// Asegurar que la tabla existe
	ModeloNotificaciones::mdlCrearTablaEstado();

	// Contar estado/traspaso no leídos
	$estadoNotifs = ModeloNotificaciones::mdlNotifEstadoNoLeidas($perfil, $idEmpresa, $idRol, 5);
	if (!is_array($estadoNotifs)) $estadoNotifs = array();
	$totalEstado = count($estadoNotifs);

	// Contar observaciones de hoy (no del usuario actual)
	$idUsuario = isset($_SESSION["id"]) ? intval($_SESSION["id"]) : 0;
	$obsNotifs = ModeloObservaciones::mdlObservacionesRecientesNotif("observacionesOrdenes", $idUsuario, 5);
	if (!is_array($obsNotifs)) $obsNotifs = array();
	$totalObs = count($obsNotifs);

	// Preparar datos de la última notificación (para toast en tiempo real)
	$ultimaNotif = null;
	if (!empty($estadoNotifs)) {
		$n = $estadoNotifs[0];
		$ultimaNotif = array(
			"tipo"    => isset($n["tipo"]) ? $n["tipo"] : "estado",
			"idOrden" => $n["id_orden"],
			"anterior" => $n["estado_anterior"],
			"nuevo"   => $n["estado_nuevo"],
			"usuario" => $n["nombre_usuario"],
			"fecha"   => $n["fecha"],
			"id"      => $n["id"]
		);
	}

	$ultimaObs = null;
	if (!empty($obsNotifs)) {
		$o = $obsNotifs[0];
		$ultimaObs = array(
			"idOrden" => $o["id_orden"],
			"texto"   => mb_strlen($o["observacion"]) > 80 ? mb_substr($o["observacion"], 0, 80) . "…" : $o["observacion"],
			"creador" => isset($o["creador_nombre"]) ? $o["creador_nombre"] : "Usuario",
			"fecha"   => $o["fecha"],
			"id"      => $o["id"]
		);
	}

	header("Content-Type: application/json");
	echo json_encode(array(
		"totalEstado" => $totalEstado,
		"totalObs"    => $totalObs,
		"total"       => $totalEstado + $totalObs,
		"ultimaNotif" => $ultimaNotif,
		"ultimaObs"   => $ultimaObs
	));
	exit;

}
