<?php
/**
 * Endpoint público para registrar visitas en visitasPersonas / visitasPaises.
 * Incluir en comercializadoraegs.com vía pixel o fetch (ver docs/ANALITICA-WEB.md).
 */
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: https://comercializadoraegs.com');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
	http_response_code(204);
	exit;
}

require_once __DIR__ . '/../../config/env.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../modelos/conexion.php';
require_once __DIR__ . '/../../modelos/visitas.modelo.php';

function trackingResponder($ok, $msg = '')
{
	echo json_encode(array('ok' => $ok, 'msg' => $msg));
	exit;
}

try {
	$pdo = Database::conectar(Database::ECOMMERCE);
} catch (Exception $e) {
	trackingResponder(false, 'db');
}

$inst = ModeloVisitas::mdlCrearTablasVisitas();
if (empty($inst['ok'])) {
	trackingResponder(false, 'tables');
}

$ip = isset($_SERVER['HTTP_X_FORWARDED_FOR'])
	? trim(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0])
	: (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0');

$pais = isset($_REQUEST['pais']) ? substr(trim($_REQUEST['pais']), 0, 80) : 'Desconocido';
if ($pais === '') {
	$pais = 'Desconocido';
}

$fecha = date('Y-m-d H:i:s');

try {
	$stmt = $pdo->prepare(
		"SELECT id, visitas FROM visitasPersonas WHERE ip = :ip LIMIT 1"
	);
	$stmt->execute(array(':ip' => $ip));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);

	if ($row) {
		$upd = $pdo->prepare(
			"UPDATE visitasPersonas SET visitas = visitas + 1, fecha = :fecha, pais = :pais WHERE id = :id"
		);
		$upd->execute(array(':fecha' => $fecha, ':pais' => $pais, ':id' => $row['id']));
	} else {
		$ins = $pdo->prepare(
			"INSERT INTO visitasPersonas (ip, pais, visitas, fecha) VALUES (:ip, :pais, 1, :fecha)"
		);
		$ins->execute(array(':ip' => $ip, ':pais' => $pais, ':fecha' => $fecha));
	}

	$stmtP = $pdo->prepare("SELECT id, cantidad FROM visitasPaises WHERE pais = :pais LIMIT 1");
	$stmtP->execute(array(':pais' => $pais));
	$rowP = $stmtP->fetch(PDO::FETCH_ASSOC);

	if ($rowP) {
		$updP = $pdo->prepare("UPDATE visitasPaises SET cantidad = cantidad + 1 WHERE id = :id");
		$updP->execute(array(':id' => $rowP['id']));
	} else {
		$insP = $pdo->prepare("INSERT INTO visitasPaises (pais, cantidad) VALUES (:pais, 1)");
		$insP->execute(array(':pais' => $pais));
	}

	try {
		$pdo->exec("UPDATE notificaciones SET nuevasVisitas = nuevasVisitas + 1 LIMIT 1");
	} catch (Exception $e) {}

	trackingResponder(true);
} catch (Exception $e) {
	trackingResponder(false, 'error');
}
