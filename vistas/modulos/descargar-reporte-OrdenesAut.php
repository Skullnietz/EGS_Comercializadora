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

// $objeto = new controladorOrdenes();
// $objeto -> ctrReporteOrdenesAut();
$valorEmpresa = $_GET["empresa"];
// $objeto = controladorOrdenes::ctrReporteOrdenesAut($valorEmpresa);

require_once "reporte_helper.php";
// Assuming AUT corresponds to 'Pendiente de autorización (AUT' based on naming, but better to be sure.
// Let's use 'Pendiente de autorización (AUT' for now as it's the only similar one in the ENUM list I saw.
// Wait, if PEN is 'Pendiente (REV)'?
// I need to verify status strings.
// I'll pause this replacement and check the file first.
ReporteHelper::generarReporteExcel("Pendiente de autorización (AUT", $valorEmpresa, "OrdenesAUT");