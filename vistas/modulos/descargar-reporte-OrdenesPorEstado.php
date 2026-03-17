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

//$reporte = new controladorOrdenes();
//$reporte -> ctrDescargarReporteVOrdenes();
$estado = $_GET["estado"];
$item = "id_empresa";
$valor = $_GET["empresa"];
$tecnico = "id_tecnico"; 
$valorTecnico = $_GET["tecnico"];

$reporte = controladorOrdenes::ctrDescargarReporteOrdenesOkTecnico($estado, $item, $valor, $tecnico, $valorTecnico);