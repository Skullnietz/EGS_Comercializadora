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

$ordenes = array_values(array_filter($ordenes, function ($orden) {
	return isset($orden["estado"]) && $orden["estado"] === "Producto para venta";
}));

$rangoTexto = (isset($_GET["fechaInicial"]) && isset($_GET["fechaFinal"]))
	? (($_GET["fechaInicial"] === $_GET["fechaFinal"])
		? $_GET["fechaInicial"]
		: $_GET["fechaInicial"] . " a " . $_GET["fechaFinal"])
	: "Todas las ordenes";

usort($ordenes, function ($a, $b) {
	return intval($b["id"] ?? 0) <=> intval($a["id"] ?? 0);
});

$headers = array("Orden", "Empresa", "Asesor", "Tecnico", "Cliente", "Estado", "Total", "Fecha");
$rows = array();
$sumaTotal = 0.0;

foreach ($ordenes as $value) {
	$empresa = ControladorVentas::ctrMostrarEmpresasParaTiketimp("id", $value["id_empresa"]);
	$asesor = Controladorasesores::ctrMostrarAsesoresEleg("id", $value["id_Asesor"]);
	$cliente = ControladorClientes::ctrMostrarClientes("id", $value["id_usuario"]);
	$tecnico = ControladorTecnicos::ctrMostrarTecnicos("id", $value["id_tecnico"]);

	$rows[] = array(
		$value["id"],
		$empresa["empresa"] ?? "",
		$asesor["nombre"] ?? "",
		$tecnico["nombre"] ?? "",
		$cliente["nombre"] ?? "",
		$value["estado"] ?? "",
		floatval($value["total"]),
		$value["fecha"] ?? ""
	);
	$sumaTotal += floatval($value["total"]);
}

ExcelExportHelper::downloadXlsx($_GET["reporte"] ?? "ordenes_venta", $headers, $rows, array(
	"sheetName" => "Para Venta",
	"title" => "Reporte de Ordenes para Venta",
	"subtitle" => "Rango: " . $rangoTexto . " | Generado: " . date("Y-m-d H:i"),
	"currencyColumns" => array(6),
	"dateColumns" => array(7),
	"footerRows" => array(
		array("values" => array(0 => "Registros", 1 => count($rows), 5 => "Total", 6 => $sumaTotal))
	)
));