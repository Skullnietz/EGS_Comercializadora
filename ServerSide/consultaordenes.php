<?php
include_once '../ServerSide/serversideConexion.php';
include_once '../config/Database.php';
include_once '../config/env.php';

$objeto = new ConexionO();
$conexiono = $objeto->Conectar();

$consulta = "SELECT * FROM ordenes ORDER BY id DESC";
$resultado = $conexiono->prepare($consulta);
$resultado->execute();
$data=$resultado->fetchAll(PDO::FETCH_ASSOC);

// Traer nombres de clientes desde la otra BD
$clienteNames = [];
try {
    $pdoSis = Database::conectar(Database::SISTEMA);
    $stmtCli = $pdoSis->prepare("SELECT id, nombre FROM clientesTienda");
    $stmtCli->execute();
    foreach ($stmtCli->fetchAll(PDO::FETCH_ASSOC) as $cli) {
        $clienteNames[intval($cli["id"])] = $cli["nombre"];
    }
} catch (Exception $e) {}

foreach ($data as &$row) {
    $uid = intval($row["id_usuario"]);
    $row["cliente_nombre"] = isset($clienteNames[$uid]) ? $clienteNames[$uid] : "";
}
unset($row);

print json_encode($data, JSON_UNESCAPED_UNICODE);
$conexiono=null;

?>