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

$estado = $_GET["estado"];
$item = "id_empresa";
$valor = $_GET["empresa"];
$tecnico = "id_tecnico"; 
$valorTecnico = $_GET["tecnico"];

$ordenes = ModeloOrdenes::mdlMostrarOrdenesPorEstadoEmpresayTecnico("ordenes", $estado, $item, $valor, $tecnico, $valorTecnico);
if (!is_array($ordenes)) {
	$ordenes = array();
}

$rangoTexto = isset($_GET["estado"]) ? $_GET["estado"] : "Estado";

usort($ordenes, function ($a, $b) {
	return strtotime((string)($a["fecha"] ?? "")) <=> strtotime((string)($b["fecha"] ?? ""));
});

$headers = array("Orden", "Empresa", "Asesor", "Tecnico", "Cliente", "Estado", "Monto", "Fecha");
$rows = array();
$sumaTotal = 0.0;

foreach ($ordenes as $value) {
	$empresa = ControladorVentas::ctrMostrarEmpresasParaTiketimp("id", $value["id_empresa"]);
	$asesor = Controladorasesores::ctrMostrarAsesoresEleg("id", $value["id_Asesor"]);
	$cliente = ControladorClientes::ctrMostrarClientes("id", $value["id_usuario"]);
	$tec = ControladorTecnicos::ctrMostrarTecnicos("id", $value["id_tecnico"]);

	$rows[] = array(
		$value["id"],
		$empresa["empresa"] ?? "",
		$asesor["nombre"] ?? "",
		$tec["nombre"] ?? "",
		$cliente["nombre"] ?? "",
		$value["estado"] ?? "",
		floatval($value["total"]),
		$value["fecha"] ?? ""
	);
	$sumaTotal += floatval($value["total"]);
}

ExcelExportHelper::downloadXlsx($_GET["reporte"] ?? "ordenes_estado", $headers, $rows, array(
	"sheetName" => "Por Estado",
	"title" => "Reporte por Estado",
	"subtitle" => "Filtro: " . $rangoTexto . " | Generado: " . date("Y-m-d H:i"),
	"currencyColumns" => array(6),
	"dateColumns" => array(7),
	"footerRows" => array(
		array("values" => array(0 => "Registros", 1 => count($rows), 5 => "Total", 6 => $sumaTotal))
	)
));