<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . "/../config/env.php";
require_once __DIR__ . "/../config/comisiones.helper.php";
require_once __DIR__ . "/../modelos/ordenes.modelo.php";

function _comAjaxResponder($ok, $mensaje, $extra = array(), $http = 200) {
	http_response_code($http);
	echo json_encode(
		array_merge(array("ok" => $ok, "mensaje" => $mensaje), $extra),
		JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
	);
	exit;
}

if (!isset($_SESSION["perfil"])) {
	_comAjaxResponder(false, "Tu sesión terminó. Vuelve a iniciar sesión.", array(), 401);
}

$perfilesPermitidos = array("administrador", "secretaria", "Super-Administrador");
if (!in_array($_SESSION["perfil"], $perfilesPermitidos, true)) {
	_comAjaxResponder(false, "No tienes permiso para resolver comisiones.", array(), 403);
}

if (!isset($_POST["accion"]) || $_POST["accion"] !== "guardarAsignacionPartidas") {
	_comAjaxResponder(false, "Acción no reconocida.", array(), 400);
}

$idOrden = isset($_POST["idOrden"]) ? intval($_POST["idOrden"]) : 0;
if ($idOrden <= 0) {
	_comAjaxResponder(false, "La orden no es válida.", array(), 422);
}

try {
	$orden = ModeloOrdenes::mdlMostrarOrdenComisionPorId("ordenes", $idOrden);
} catch (Throwable $e) {
	_comAjaxResponder(false, "No fue posible consultar la orden.", array(), 500);
}

if (!is_array($orden)) {
	_comAjaxResponder(false, "La orden ya no existe.", array(), 404);
}

if (
	$_SESSION["perfil"] !== "Super-Administrador" &&
	intval(isset($orden["id_empresa"]) ? $orden["id_empresa"] : 0) !== intval(isset($_SESSION["empresa"]) ? $_SESSION["empresa"] : 0)
) {
	_comAjaxResponder(false, "La orden pertenece a otra empresa.", array(), 403);
}

if (!_comEsDoble($orden)) {
	_comAjaxResponder(false, "La orden ya no tiene dos técnicos distintos.", array(), 409);
}

if ((string) (isset($orden["estado"]) ? $orden["estado"] : "") !== "Entregado (Ent)") {
	_comAjaxResponder(false, "Solo se pueden resolver comisiones de órdenes entregadas.", array(), 409);
}

$recibidas = json_decode(isset($_POST["asignaciones"]) ? $_POST["asignaciones"] : "", true);
if (!is_array($recibidas)) {
	_comAjaxResponder(false, "La asignación enviada no es válida.", array(), 422);
}

$tecnicoUno = intval($orden["id_tecnico"]);
$tecnicoDos = intval($orden["id_tecnicoDos"]);
$permitidos = array($tecnicoUno, $tecnicoDos);
$asignaciones = array();
$partidasPositivas = 0;

foreach (_comPartidasOrden($orden) as $partida) {
	$monto = floatval($partida["monto"]);
	if ($monto <= 0) continue;

	$partidasPositivas++;
	$idTecnico = isset($recibidas[$partida["key"]]) ? intval($recibidas[$partida["key"]]) : 0;
	if (!in_array($idTecnico, $permitidos, true)) {
		_comAjaxResponder(
			false,
			"Falta asignar la partida: ".$partida["descripcion"].".",
			array("partida" => $partida["key"]),
			422
		);
	}

	$asignaciones[$partida["key"]] = $idTecnico;
}

if ($partidasPositivas === 0) {
	_comAjaxResponder(false, "La orden no tiene partidas con monto mayor a cero.", array(), 422);
}

$registro = array(
	"version" => 1,
	"huella" => _comHuellaAsignacion($orden),
	"asignaciones" => $asignaciones,
	"resuelto_en" => date("c"),
	"resuelto_por" => array(
		"id" => intval(isset($_SESSION["id"]) ? $_SESSION["id"] : 0),
		"nombre" => (string) (isset($_SESSION["nombre"]) ? $_SESSION["nombre"] : ""),
		"perfil" => (string) $_SESSION["perfil"]
	)
);

$json = json_encode($registro, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
if ($json === false) {
	_comAjaxResponder(false, "No fue posible preparar la asignación.", array(), 500);
}

try {
	$resultado = ModeloOrdenes::mdlGuardarAsignacionComision("ordenes", $idOrden, $json);
} catch (Throwable $e) {
	$resultado = "error";
}

if ($resultado === "schema_error") {
	_comAjaxResponder(
		false,
		"No se pudo preparar la columna de asignaciones. Ejecuta la migración 20260729_asignacion_partidas_comisiones.sql.",
		array(),
		500
	);
}

if ($resultado !== "ok") {
	_comAjaxResponder(false, "No fue posible guardar la asignación.", array(), 500);
}

_comAjaxResponder(
	true,
	"Las partidas de la orden #".$idOrden." quedaron asignadas correctamente.",
	array("idOrden" => $idOrden)
);
