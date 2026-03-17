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

// //$reporte = new controladorOrdenes();
// //$reporte -> ctrDescargarReporteOrdenesOk();
$valorEmpresa = $_GET["empresa"];
// $reporte = controladorOrdenes::ctrDescargarReporteOrdenesOk($valorEmpresa);

require_once "reporte_helper.php";
ReporteHelper::generarReporteExcel("Aceptado (ok)", $valorEmpresa, "OrdenesOK");
