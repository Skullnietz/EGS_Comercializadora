<?php

if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

if (!isset($_SESSION["validarSesionBackend"]) || $_SESSION["validarSesionBackend"] !== "ok") {
	http_response_code(401);
	header("Content-Type: application/json; charset=utf-8");
	echo json_encode(array("ok" => false, "error" => "Sesión no válida"));
	exit;
}

// El endpoint no modifica la sesión; liberar el bloqueo para no serializar el resto de AJAX.
session_write_close();

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

	require_once "../modelos/conexion.php";
	require_once "../modelos/observacionOrdenes.modelo.php";
	require_once "../modelos/comentarioClienteOrden.modelo.php";
	require_once "../modelos/reporteEquipo.modelo.php";

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
	// Si es técnico, filtrar solo observaciones de sus órdenes
	$idUsuario = isset($_SESSION["id"]) ? intval($_SESSION["id"]) : 0;
	$_poll_tecOrdenIds = null;
	if ($perfil === "tecnico" && $idRol !== null) {
		try {
			require_once "../controladores/ordenes.controlador.php";
			require_once "../modelos/ordenes.modelo.php";
			$_poll_tecOrdenes = controladorOrdenes::ctrMostrarOrdenesDelTecncio($idRol);
			if (is_array($_poll_tecOrdenes)) {
				$_poll_tecOrdenIds = array();
				foreach ($_poll_tecOrdenes as $_ptOrd) {
					$_poll_tecOrdenIds[] = intval($_ptOrd["id"]);
				}
			}
		} catch (Exception $e) {}
	}
	$obsNotifs = ModeloObservaciones::mdlObservacionesRecientesNotif("observacionesOrdenes", $idUsuario, 5, $_poll_tecOrdenIds);
	if (!is_array($obsNotifs)) $obsNotifs = array();
	$totalObs = count($obsNotifs);

	// Contar comentarios del cliente y reportes del equipo de hoy.
	// Para tecnicos se reutiliza el filtro de sus ordenes asignadas.
	$clienteNotifs = ModeloComentarioCliente::mdlComentariosRecientesNotif(
		"comentariosClienteOrden", 5, $_poll_tecOrdenIds
	);
	if (!is_array($clienteNotifs)) $clienteNotifs = array();
	$totalCliente = count($clienteNotifs);

	$reporteNotifs = ModeloReporteEquipo::mdlReportesRecientesNotif(
		"reporteEstadoEquipo", $idUsuario, 5, $_poll_tecOrdenIds
	);
	if (!is_array($reporteNotifs)) $reporteNotifs = array();
	$totalReporte = count($reporteNotifs);

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

	$ultimoCliente = null;
	if (!empty($clienteNotifs)) {
		$c = $clienteNotifs[0];
		$ultimoCliente = array(
			"idOrden" => $c["id_orden"],
			"texto"   => mb_strlen($c["comentario"]) > 80 ? mb_substr($c["comentario"], 0, 80) . "..." : $c["comentario"],
			"fecha"   => $c["fecha"],
			"id"      => $c["id"]
		);
	}

	$ultimoReporte = null;
	if (!empty($reporteNotifs)) {
		$rep = $reporteNotifs[0];
		$ultimoReporte = array(
			"idOrden" => $rep["id_orden"],
			"texto"   => mb_strlen($rep["descripcion"]) > 80 ? mb_substr($rep["descripcion"], 0, 80) . "..." : $rep["descripcion"],
			"creador" => isset($rep["creador_nombre"]) ? $rep["creador_nombre"] : "Usuario",
			"fecha"   => $rep["fecha"],
			"id"      => $rep["id"]
		);
	}

	header("Content-Type: application/json");
	echo json_encode(array(
		"totalEstado" => $totalEstado,
		"totalObs"    => $totalObs,
		"totalCliente" => $totalCliente,
		"totalReporte" => $totalReporte,
		"total"       => $totalEstado + $totalObs + $totalCliente + $totalReporte,
		"ultimaNotif" => $ultimaNotif,
		"ultimaObs"   => $ultimaObs,
		"ultimoCliente" => $ultimoCliente,
		"ultimoReporte" => $ultimoReporte
	));
	exit;

}
