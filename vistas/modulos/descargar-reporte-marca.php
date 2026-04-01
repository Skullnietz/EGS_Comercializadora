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

if (isset($_GET["fechaInicial"]) && isset($_GET["fechaFinal"])) {
	$ordenes = ModeloOrdenes::mdlRangoFechasOrdenesPorEmpresa("ordenes", $_GET["fechaInicial"], $_GET["fechaFinal"], "id_empresa", $valorEmpresa);
} else {
	$ordenes = ModeloOrdenes::mdlMostrarordenesParaValidar("ordenes", "id_empresa", $valorEmpresa);
}

if (!is_array($ordenes)) {
	$ordenes = array();
}

$rangoTexto = (isset($_GET["fechaInicial"]) && isset($_GET["fechaFinal"]))
	? (($_GET["fechaInicial"] === $_GET["fechaFinal"])
		? $_GET["fechaInicial"]
		: $_GET["fechaInicial"] . " a " . $_GET["fechaFinal"])
	: "Todas las ordenes";

usort($ordenes, function ($a, $b) {
	return intval($b["id"] ?? 0) <=> intval($a["id"] ?? 0);
});

$headers = array("Orden", "Tecnico", "Marca", "Modelo", "Serie", "Estado", "Cliente");
$rows = array();
foreach ($ordenes as $value) {
	$cliente = ControladorClientes::ctrMostrarClientes("id", $value["id_usuario"]);
	$tecnico = ControladorTecnicos::ctrMostrarTecnicos("id", $value["id_tecnico"]);

	$rows[] = array(
		$value["id"],
		$tecnico["nombre"] ?? "",
		$value["marcaDelEquipo"] ?? "",
		$value["modeloDelEquipo"] ?? "",
		$value["numeroDeSerieDelEquipo"] ?? "",
		$value["estado"] ?? "",
		$cliente["nombre"] ?? ""
	);
}

ExcelExportHelper::downloadXlsx($_GET["reporte"] ?? "ordenes_marca", $headers, $rows, array(
	"sheetName" => "Por Marca",
	"title" => "Reporte por Marca",
	"subtitle" => "Rango: " . $rangoTexto . " | Generado: " . date("Y-m-d H:i"),
	"footerRows" => array(
		array("values" => array(0 => "Registros", 1 => count($rows)))
	)
));