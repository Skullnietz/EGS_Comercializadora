<?php
require_once "../../config/env.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION["perfil"]) && $_SESSION["perfil"] === "vendedor") {
    http_response_code(403);
    exit("No autorizado");
}

require_once "../../controladores/ventas.controlador.php";
require_once "../../modelos/ventas.modelo.php";
require_once __DIR__ . "/excel_export_helper.php";

$empresa = isset($_GET["empresa"]) ? trim((string)$_GET["empresa"]) : "";
if ($empresa === "") {
    http_response_code(400);
    echo "Parametro 'empresa' requerido";
    exit;
}

$fechaInicial = isset($_GET["fechaInicial"]) ? trim((string)$_GET["fechaInicial"]) : "";
$fechaFinal = isset($_GET["fechaFinal"]) ? trim((string)$_GET["fechaFinal"]) : "";

$tabla = "compras";
$ventas = ModeloVentas::mdlMostrarVentasParaEmpresas($tabla, "id_empresa", $empresa);

if ($fechaInicial !== "" || $fechaFinal !== "") {
    if ($fechaInicial === "") {
        $fechaInicial = $fechaFinal;
    }
    if ($fechaFinal === "") {
        $fechaFinal = $fechaInicial;
    }

    $inicioTs = strtotime($fechaInicial . " 00:00:00");
    $finTs = strtotime($fechaFinal . " 23:59:59");

    if ($inicioTs !== false && $finTs !== false && is_array($ventas)) {
        $ventas = array_values(array_filter($ventas, function ($venta) use ($inicioTs, $finTs) {
            if (!isset($venta["fecha"])) {
                return false;
            }

            $ventaTs = strtotime((string)$venta["fecha"]);
            if ($ventaTs === false) {
                return false;
            }

            return $ventaTs >= $inicioTs && $ventaTs <= $finTs;
        }));
    }
}

if (!is_array($ventas)) {
    $ventas = array();
}

usort($ventas, function ($a, $b) {
    return strtotime((string)($b["fecha"] ?? "")) <=> strtotime((string)($a["fecha"] ?? ""));
});

$rangoTexto = ($fechaInicial !== "" && $fechaFinal !== "")
    ? (($fechaInicial === $fechaFinal) ? $fechaInicial : $fechaInicial . " a " . $fechaFinal)
    : "Todas las fechas";

$headers = array(
    "Orden",
    "Empresa",
    "Cliente",
    "Productos",
    "Cantidad",
    "Total",
    "Asesor",
    "Metodo",
    "Fecha"
);

$rows = array();
$sumaTotal = 0.0;
$sumaCantidad = 0.0;

foreach ($ventas as $venta) {
    $empresaInfo = ControladorVentas::ctrMostrarEmpresasParaTiketimp("id", $venta["empresa"] ?? "");
    $nombreEmpresa = is_array($empresaInfo) ? ($empresaInfo["empresa"] ?? "") : "";

    $productos = array();
    $camposProductos = array(
        "productoUno", "productoDos", "productoTres", "productoCuatro", "productoCinco",
        "productoSeis", "productoSiete", "productoOcho", "productoNueve", "productoDiez"
    );

    foreach ($camposProductos as $campo) {
        $producto = trim((string)($venta[$campo] ?? ""));
        if ($producto !== "") {
            $productos[] = $producto;
        }
    }

    $cantidad = floatval($venta["cantidadProductos"] ?? 0);
    $total = floatval($venta["pago"] ?? 0);

    $sumaCantidad += $cantidad;
    $sumaTotal += $total;

    $rows[] = array(
        $venta["id"] ?? "",
        $nombreEmpresa,
        $venta["nombreCliente"] ?? "",
        implode(" | ", $productos),
        $cantidad,
        $total,
        $venta["asesor"] ?? "",
        $venta["metodo"] ?? "",
        $venta["fecha"] ?? ""
    );
}

ExcelExportHelper::downloadXlsx($_GET["reporte"] ?? "ventasR", $headers, $rows, array(
    "sheetName" => "VentasRapidas",
    "title" => "Reporte de Ventas Rapidas",
    "subtitle" => "Rango: " . $rangoTexto . " | Generado: " . date("Y-m-d H:i"),
    "currencyColumns" => array(5),
    "dateColumns" => array(8),
    "footerRows" => array(
        array("values" => array(0 => "Registros", 1 => count($rows), 3 => "Totales", 4 => $sumaCantidad, 5 => $sumaTotal))
    )
));
