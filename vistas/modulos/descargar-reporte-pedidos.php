<?php
require_once "../../controladores/pedidos.controlador.php";

require_once "../../modelos/pedidos.modelo.php";

require_once "../../controladores/ventas.controlador.php";

require_once "../../modelos/ventas.modelo.php";

require_once "../../controladores/ordenes.controlador.php";

require_once "../../modelos/ordenes.modelo.php";

require_once "../../controladores/controlador.asesore.php";

require_once "../../modelos/modelo.asesores.php";

require_once "../../controladores/clientes.controlador.php";

require_once "../../modelos/clientes.modelo.php";

//$reporte = new ControladorPedidos();
//$reporte -> ctrDescargarReportePedidos();
$valorEmpres = $_GET["empresa"];
$reporte = ControladorPedidos::ctrDescargarReportePedidos($valorEmpres);

