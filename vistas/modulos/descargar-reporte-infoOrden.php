<?php

require_once "../../controladores/ventas.controlador.php";

require_once "../../modelos/ventas.modelo.php";

require_once "../../controladores/ordenes.controlador.php";

require_once "../../modelos/ordenes.modelo.php";

require_once "../../controladores/controlador.asesore.php";

require_once "../../modelos/modelo.asesores.php";

require_once "../../controladores/clientes.controlador.php";

require_once "../../modelos/clientes.modelo.php";

require_once "../../controladores/tecnicos.controlador.php";
require_once "../../modelos/tecnicos.modelo.php";
require_once __DIR__ . "/excel_export_helper.php";

$valorEmpresa = $_GET["empresa"];

$ordenes = ModeloOrdenes::mdlMostrarordenesParaValidar("ordenes", "id_empresa", $valorEmpresa);
if (!is_array($ordenes)) {
	$ordenes = array();
}

$rangoTexto = (isset($_GET["fechaInicial"]) && isset($_GET["fechaFinal"]))
	? (($_GET["fechaInicial"] === $_GET["fechaFinal"])
		? $_GET["fechaInicial"]
		: $_GET["fechaInicial"] . " a " . $_GET["fechaFinal"])
	: "Todas las ordenes";

usort($ordenes, function ($a, $b) {
	return strtotime((string)($a["fecha"] ?? "")) <=> strtotime((string)($b["fecha"] ?? ""));
});

$headers = array("Orden", "Tecnico", "Estado", "Cliente", "Partidas Recepcion", "Partidas", "Costo", "Observaciones");
$rows = array();

foreach ($ordenes as $value) {
	$cliente = ControladorClientes::ctrMostrarClientes("id", $value["id_usuario"]);
	$tecnico = ControladorTecnicos::ctrMostrarTecnicos("id", $value["id_tecnico"]);

	$partidas = json_decode($value["partidas"], true);
	$descPartida = "";
	$valorPartida = "";
	if (is_array($partidas)) {
		foreach ($partidas as $item) {
			$descPartida = $item["descripcion"] ?? "";
			$valorPartida = $item["precioPartida"] ?? "";
		}
	}

	$observaciones = json_decode($value["observaciones"], true);
	$obsText = "";
	if (is_array($observaciones)) {
		$obsList = array();
		foreach ($observaciones as $obs) {
			$obsList[] = $obs["observacion"] ?? "";
		}
		$obsText = implode(" | ", $obsList);
	}

	$rows[] = array(
		$value["id"],
		$tecnico["nombre"] ?? "",
		$value["estado"] ?? "",
		$cliente["nombre"] ?? "",
		trim(($value["partidaUno"] ?? "") . " " . ($value["partidaDos"] ?? "")),
		$descPartida,
		$valorPartida,
		$obsText
	);
}

ExcelExportHelper::downloadXlsx($_GET["reporte"] ?? "info_ordenes", $headers, $rows, array(
	"sheetName" => "Info Orden",
	"title" => "Reporte Info Orden",
	"subtitle" => "Rango: " . $rangoTexto . " | Generado: " . date("Y-m-d H:i"),
	"footerRows" => array(
		array("values" => array(0 => "Registros", 1 => count($rows)))
	)
));