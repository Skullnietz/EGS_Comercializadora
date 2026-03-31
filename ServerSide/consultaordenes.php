<?php
include_once '../ServerSide/serversideConexion.php';

$objeto = new ConexionO();
$conexiono = $objeto->Conectar();

$consulta = "SELECT * FROM ordenes ORDER BY id DESC";
$resultado = $conexiono->prepare($consulta);
$resultado->execute();
$data=$resultado->fetchAll(PDO::FETCH_ASSOC);

// Traer nombres de clientes desde la otra BD
$clienteNames = [];
try {
    $host   = getenv('DB_SISTEMA_HOST') ?: 'localhost';
    $dbname = getenv('DB_SISTEMA_NAME') ?: 'egsequip_dbsistema';
    $user   = getenv('DB_SISTEMA_USER') ?: 'egsequip_sistema';
    $pass   = getenv('DB_SISTEMA_PASS') ?: '{#k%ER.PJD0?';
    $pdoSis = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8']);
    $stmtCli = $pdoSis->prepare("SELECT id, nombre FROM clientesTienda");
    $stmtCli->execute();
    foreach ($stmtCli->fetchAll(PDO::FETCH_ASSOC) as $cli) {
        $clienteNames[intval($cli["id"])] = $cli["nombre"];
    }
    $pdoSis = null;
} catch (Exception $e) {}

foreach ($data as &$row) {
    $uid = intval($row["id_usuario"]);
    $row["cliente_nombre"] = isset($clienteNames[$uid]) ? $clienteNames[$uid] : "";
}
unset($row);

print json_encode($data, JSON_UNESCAPED_UNICODE);
$conexiono=null;

?>