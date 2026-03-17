<?php

require_once "../../controladores/clientes.controlador.php";
require_once "../../modelos/clientes.modelo.php";

require_once "../../controladores/controlador.asesore.php";

require_once "../../modelos/modelo.asesores.php";

require_once "../../controladores/ordenes.controlador.php";

require_once "../../modelos/ordenes.modelo.php";
$valorEmpresa = $_GET["empresa"];
//$reporte = new ControladorClientes();
//$reporte -> ctrDescargarReporteClientesTienda($valorEmpresa);
$resporte = ControladorClientes::ctrDescargarReporteClientesTienda($valorEmpresa);

//$reporteDos = new ControladorClientes();
//$reporteDos -> ctrDescargarReporteClientesTiendaENT();

$reporteDos = ControladorClientes::ctrDescargarReporteClientesTiendaENT($valorEmpresa);