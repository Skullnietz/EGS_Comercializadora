<?php
require_once "../config/env.php";
require_once "../modelos/conexionWordpress.php";

header('Content-Type: application/json');

try {
    $pdoWP = ConexionWP::conectarWP();
    $stmt = $pdoWP->prepare("SELECT id, estado FROM ordenes ORDER BY id DESC LIMIT 50");
    $stmt->execute();
    $ordenes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt2 = $pdoWP->prepare("SELECT COUNT(*) as rev FROM ordenes WHERE estado LIKE :estado");
    $stmt2->execute([':estado' => '%REV%']);
    $rev = $stmt2->fetch(PDO::FETCH_ASSOC);

    echo json_encode(["status" => "ok", "ordenes" => $ordenes, "rev_count" => $rev]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
