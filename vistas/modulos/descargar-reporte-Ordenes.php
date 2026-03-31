<?php
require_once "../../config/env.php";

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
$tabla = "ordenes";

if (isset($_GET["fechaInicial"]) && isset($_GET["fechaFinal"])) {
	$ordenes = ModeloOrdenes::mdlRangoFechasOrdenesPorEmpresa($tabla, $_GET["fechaInicial"], $_GET["fechaFinal"], "id_empresa", $valorEmpresa);
} else {
	$ordenes = ModeloOrdenes::mdlMostrarordenesParaValidar($tabla, "id_empresa", $valorEmpresa);
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
	return strtotime((string)($b["fecha"] ?? "")) <=> strtotime((string)($a["fecha"] ?? ""));
});

$headers = array(
	"No. Orden", "Empresa", "Asesor", "Tecnico", "Cliente", "Estado",
	"Marca", "Modelo", "No. Serie", "Total", "Inversion", "Utilidad",
	"Fecha Ingreso", "Fecha Registro"
);

$rows = array();
$sumaTotal = 0.0;
$sumaInversion = 0.0;
$sumaUtilidad = 0.0;
foreach ($ordenes as $value) {
	$empresa = ControladorVentas::ctrMostrarEmpresasParaTiketimp("id", $value["id_empresa"]);
	$asesor = Controladorasesores::ctrMostrarAsesoresEleg("id", $value["id_Asesor"]);
	$cliente = ControladorClientes::ctrMostrarClientes("id", $value["id_usuario"]);
	$tecnico = ControladorTecnicos::ctrMostrarTecnicos("id", $value["id_tecnico"]);

	$total = floatval($value["total"]);
	$inversion = floatval($value["totalInversion"] ?? 0);
	$utilidad = $total - $inversion;
	$sumaTotal += $total;
	$sumaInversion += $inversion;
	$sumaUtilidad += $utilidad;

	$rows[] = array(
		$value["id"],
		$empresa["empresa"] ?? "",
		$asesor["nombre"] ?? "",
		$tecnico["nombre"] ?? "",
		$cliente["nombre"] ?? "",
		$value["estado"] ?? "",
		$value["marcaDelEquipo"] ?? "",
		$value["modeloDelEquipo"] ?? "",
		$value["numeroDeSerieDelEquipo"] ?? "",
		$total,
		$inversion,
		$utilidad,
		$value["fecha_ingreso"] ?? "",
		$value["fecha"] ?? ""
	);
}

ExcelExportHelper::downloadXlsx($_GET["reporte"] ?? "ordenes", $headers, $rows, array(
	"sheetName" => "Ordenes",
	"title" => "Reporte General de Ordenes",
	"subtitle" => "Rango: " . $rangoTexto . " | Generado: " . date("Y-m-d H:i"),
	"currencyColumns" => array(9, 10, 11),
	"dateColumns" => array(12, 13),
	"footerRows" => array(
		array("values" => array(0 => "Registros", 1 => count($rows), 8 => "Totales", 9 => $sumaTotal, 10 => $sumaInversion, 11 => $sumaUtilidad))
	)
));