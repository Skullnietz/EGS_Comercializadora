<?php
include_once '../ServerSide/serversideConexion.php';
$objeto = new ConexionO();
$conexiono = $objeto->Conectar();

$consulta = "SELECT * FROM ordenes ORDER BY id DESC";
$resultado = $conexiono->prepare($consulta);
$resultado->execute();
$data=$resultado->fetchAll(PDO::FETCH_ASSOC);

print json_encode($data, JSON_UNESCAPED_UNICODE);
$conexiono=null;

?>