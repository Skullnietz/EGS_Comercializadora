<?php

/**
 * Protección común para endpoints JSON de solo lectura del panel.
 * Detiene la petición antes de cargar modelos o abrir conexiones si la sesión
 * del backend no es válida y libera el bloqueo de sesión durante las consultas.
 */
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

if (!isset($_SESSION["validarSesionBackend"]) || $_SESSION["validarSesionBackend"] !== "ok") {
	http_response_code(401);
	header("Content-Type: application/json; charset=utf-8");
	echo json_encode(array(
		"data" => array(),
		"aaData" => array(),
		"error" => "Sesión no válida"
	));
	exit;
}

// Los endpoints protegidos solo leen la sesión; no necesitan mantenerla bloqueada.
session_write_close();
