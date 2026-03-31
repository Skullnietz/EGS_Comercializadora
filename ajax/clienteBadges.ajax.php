<?php
require_once "../controladores/clientes.controlador.php";
require_once "../modelos/clientes.modelo.php";
require_once "../config/clienteBadges.helper.php";

header('Content-Type: application/json; charset=utf-8');

$bh = ClienteBadgesHelper::getInstance();
echo json_encode($bh->toArray(), JSON_UNESCAPED_UNICODE);
