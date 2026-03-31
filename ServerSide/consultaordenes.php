<?php
include_once '../ServerSide/serversideConexion.php';
require_once __DIR__ . '/../config/global.php';

$objeto = new ConexionO();
$conexiono = $objeto->Conectar();

$consulta = "SELECT * FROM ordenes ORDER BY id DESC";
$resultado = $conexiono->prepare($consulta);
$resultado->execute();
$data=$resultado->fetchAll(PDO::FETCH_ASSOC);

// Traer nombres de clientes desde la BD de e-commerce (clientesTienda vive en egsequip_ecomerce)
require_once __DIR__ . '/../config/Database.php';
$clienteNames = [];
try {
    $pdoEcom = Database::conectar(Database::ECOMMERCE);
    $stmtCli = $pdoEcom->query("SELECT id, nombre FROM clientesTienda");
    while ($row = $stmtCli->fetch(PDO::FETCH_ASSOC)) {
        $clienteNames[intval($row["id"])] = $row["nombre"];
    }
} catch (Exception $e) {
    error_log("consultaordenes.php - Error al traer clientes: " . $e->getMessage());
}

foreach ($data as &$row) {
    $uid = intval($row["id_usuario"]);
    $row["cliente_nombre"] = isset($clienteNames[$uid]) ? $clienteNames[$uid] : "";
}
unset($row);

print json_encode($data, JSON_UNESCAPED_UNICODE);
$conexiono=null;

?>