<?php
include_once '../ServerSide/serversideConexion.php';
require_once __DIR__ . '/../config/global.php';

$objeto = new ConexionO();
$conexiono = $objeto->Conectar();

$consulta = "SELECT * FROM ordenes ORDER BY id DESC";
$resultado = $conexiono->prepare($consulta);
$resultado->execute();
$data=$resultado->fetchAll(PDO::FETCH_ASSOC);

// Traer nombres de clientes, técnicos y asesores desde la BD de e-commerce (egsequip_ecomerce)
require_once __DIR__ . '/../config/Database.php';
$clienteNames = [];
$tecnicoNames = [];
$asesorNames  = [];
try {
    $pdoEcom = Database::conectar(Database::ECOMMERCE);

    $stmtCli = $pdoEcom->query("SELECT id, nombre FROM clientesTienda");
    while ($row = $stmtCli->fetch(PDO::FETCH_ASSOC)) {
        $clienteNames[intval($row["id"])] = $row["nombre"];
    }

    $stmtTec = $pdoEcom->query("SELECT id, nombre FROM tecnicos");
    while ($row = $stmtTec->fetch(PDO::FETCH_ASSOC)) {
        $tecnicoNames[intval($row["id"])] = $row["nombre"];
    }

    $stmtAse = $pdoEcom->query("SELECT id, nombre FROM asesores");
    while ($row = $stmtAse->fetch(PDO::FETCH_ASSOC)) {
        $asesorNames[intval($row["id"])] = $row["nombre"];
    }
} catch (Exception $e) {
    error_log("consultaordenes.php - Error al traer catálogos: " . $e->getMessage());
}

foreach ($data as &$row) {
    $uid = intval($row["id_usuario"]);
    $tid = intval($row["id_tecnico"]);
    $aid = intval($row["id_Asesor"]);
    $row["cliente_nombre"] = isset($clienteNames[$uid]) ? $clienteNames[$uid] : "";
    $row["tecnico_nombre"] = isset($tecnicoNames[$tid]) ? $tecnicoNames[$tid] : "";
    $row["asesor_nombre"]  = isset($asesorNames[$aid])  ? $asesorNames[$aid]  : "";
}
unset($row);

print json_encode($data, JSON_UNESCAPED_UNICODE);
$conexiono=null;

?>