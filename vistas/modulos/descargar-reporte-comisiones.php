<?php
require_once "../../controladores/comisiones.controlador.php";
require_once "../../modelos/reportes.modelo.php";

$reporte = new ControladorComisiones();
$reporte -> ctrlMostrarComicion();


