<?php
require_once "../../controladores/ventas.controlador.php";

require_once "../../modelos/ventas.modelo.php";

require_once "../../controladores/ordenes.controlador.php";

require_once "../../modelos/ordenes.modelo.php";

require_once "../../controladores/controlador.asesore.php";

require_once "../../modelos/modelo.asesores.php";

//$reporte = new ControladorVentas();
//$reporte -> ctrDescargarReporteVentas();
$valorEmpresa = $_GET["empresa"];
$reporte = ControladorVentas::ctrDescargarReporteVentas($valorEmpresa);

