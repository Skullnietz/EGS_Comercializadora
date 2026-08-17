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

if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
	http_response_code(405);
	header('Allow: GET, POST, OPTIONS');
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

$ipCandidata = isset($_SERVER['HTTP_CF_CONNECTING_IP'])
	? trim($_SERVER['HTTP_CF_CONNECTING_IP'])
	: (isset($_SERVER['REMOTE_ADDR']) ? trim($_SERVER['REMOTE_ADDR']) : '0.0.0.0');
$ip = filter_var($ipCandidata, FILTER_VALIDATE_IP) ? $ipCandidata : '0.0.0.0';

$pais = isset($_REQUEST['pais']) ? substr(trim($_REQUEST['pais']), 0, 80) : 'Desconocido';
if ($pais === '') {
	$pais = 'Desconocido';
}

$fecha = date('Y-m-d H:i:s');

try {
	$stmt = $pdo->prepare(
		"SELECT id, visitas, fecha FROM visitasPersonas WHERE ip = :ip LIMIT 1"
	);
	$stmt->execute(array(':ip' => $ip));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);

	// Una ráfaga de recargas o bots desde la misma IP no debe escribir en MySQL sin límite.
	if ($row && !empty($row['fecha'])) {
		$ultimaVisita = strtotime($row['fecha']);
		if ($ultimaVisita !== false && $ultimaVisita >= time() - 60) {
			trackingResponder(true, 'deduplicated');
		}
	}

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

	$stmtP = $pdo->prepare(
		"INSERT INTO visitasPaises (pais, cantidad) VALUES (:pais, 1)
		 ON DUPLICATE KEY UPDATE cantidad = cantidad + 1"
	);
	$stmtP->execute(array(':pais' => $pais));

	try {
		$pdo->exec("UPDATE notificaciones SET nuevasVisitas = nuevasVisitas + 1 LIMIT 1");
	} catch (Exception $e) {}

	trackingResponder(true);
} catch (Exception $e) {
	trackingResponder(false, 'error');
}
