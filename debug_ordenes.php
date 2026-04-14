<?php
require 'config/env.php';
require 'modelos/conexionWordpress.php';
$pdo = ConexionWP::conectarWP();
$stmt = $pdo->query('SELECT id, estado FROM ordenes ORDER BY id DESC LIMIT 50');
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

print_r($orders);

$stmt2 = $pdo->prepare("SELECT COUNT(*) FROM ordenes WHERE estado LIKE :estado");
$stmt2->execute([':estado' => '%REV%']);
echo "\nConteo REV: " . $stmt2->fetchColumn();

$stmt3 = $pdo->prepare("SELECT COUNT(*) FROM ordenes WHERE estado LIKE :estado");
$stmt3->execute([':estado' => '%Entregado%']);
echo "\nConteo Entregado: " . $stmt3->fetchColumn();
