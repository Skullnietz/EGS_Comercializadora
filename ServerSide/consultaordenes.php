<?php
include_once '../ServerSide/serversideConexion.php';
$objeto = new ConexionO();
$conexiono = $objeto->Conectar();

$dbSistema = getenv('DB_SISTEMA_NAME') ?: 'egsequip_dbsistema';

$consulta = "SELECT o.*, c.nombre AS cliente_nombre
             FROM ordenes o
             LEFT JOIN {$dbSistema}.clientesTienda c ON c.id = o.id_usuario
             ORDER BY o.id DESC";
$resultado = $conexiono->prepare($consulta);
$resultado->execute();
$data=$resultado->fetchAll(PDO::FETCH_ASSOC);

print json_encode($data, JSON_UNESCAPED_UNICODE);
$conexiono=null;

?>