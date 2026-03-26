<?php

require_once "../../controladores/ordenes.controlador.php";
require_once "../../modelos/ordenes.modelo.php";
require_once "../../controladores/controlador.asesore.php";
require_once "../../modelos/modelo.asesores.php";
require_once "../../controladores/clientes.controlador.php";
require_once "../../modelos/clientes.modelo.php";
require_once "../../controladores/tecnicos.controlador.php";
require_once "../../modelos/tecnicos.modelo.php";

$valorEmpresa = $_GET["empresa"];

require_once "reporte_helper.php";
ReporteHelper::generarReporteCRMSeguimiento($valorEmpresa, "CRM-Seguimiento-Tecnicos");
