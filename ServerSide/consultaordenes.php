<?php
include_once '../ServerSide/serversideConexion.php';
require_once __DIR__ . '/../config/global.php';

$objeto = new ConexionO();
$conexiono = $objeto->Conectar();

$consulta = "SELECT * FROM ordenes ORDER BY id DESC";
$resultado = $conexiono->prepare($consulta);
$resultado->execute();
$data=$resultado->fetchAll(PDO::FETCH_ASSOC);

// Traer nombres de clientes desde la BD del sistema (mismo método que Conexion.php)
$clienteNames = [];
$mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
if (!$mysqli->connect_error) {
    $mysqli->set_charset("utf8");
    $res = $mysqli->query("SELECT id, nombre FROM clientesTienda");
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $clienteNames[intval($row["id"])] = $row["nombre"];
        }
        $res->free();
    }
    $mysqli->close();
}

foreach ($data as &$row) {
    $uid = intval($row["id_usuario"]);
    $row["cliente_nombre"] = isset($clienteNames[$uid]) ? $clienteNames[$uid] : "";
}
unset($row);

print json_encode($data, JSON_UNESCAPED_UNICODE);
$conexiono=null;

?>