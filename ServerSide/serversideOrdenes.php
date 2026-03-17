<?php
require 'serverside.php';


require_once "../controladores/ordenes.controlador.php";
require_once "../modelos/ordenes.modelo.php";


$table_data->get('ordenes','id',array('id', 'estado', 'fecha'));

?>