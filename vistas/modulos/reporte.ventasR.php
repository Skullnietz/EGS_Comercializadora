<?php

if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

if (isset($_SESSION["perfil"]) && $_SESSION["perfil"] === "vendedor") {
	http_response_code(403);
	exit("No autorizado");
}

require_once "../../controladores/reportes.controlador.php";
require_once "../../modelos/reportes.modelo.php";

require_once "../../controladores/productos.controlador.php";
require_once "../../modelos/productos.modelo.php";

require_once "../../controladores/usuarios.controlador.php";
require_once "../../modelos/usuarios.modelo.php";

require_once "../../controladores/empresas.controlador.php";
require_once "../../modelos/empresas.modelo.php";

require_once "../../controladores/ventas.controlador.php";

require_once "../../modelos/ventas.modelo.php";

//$reporte = new ControladorReportes();
//$reporte -> ctrDescargarReporteR();

$valorReporteVentasR = $_GET["empresa"];

$reporte = ControladorReportes::ctrDescargarReporteR($valorReporteVentasR);