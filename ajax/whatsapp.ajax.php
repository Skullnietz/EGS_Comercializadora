<?php

require_once "../controladores/whatsapp.controlador.php";
require_once "../modelos/whatsapp.modelo.php";

header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$perfil = isset($_SESSION['perfil']) ? $_SESSION['perfil'] : '';
$permitido = ($perfil === 'administrador' || $perfil === 'Super-Administrador');

if (!$permitido) {
    http_response_code(403);
    echo json_encode(array('ok' => false, 'error' => 'No autorizado'));
    exit;
}

$accion = isset($_POST['accion']) ? $_POST['accion'] : '';

if ($accion === 'obtenerConfig') {
    $cfg = ControladorWhatsapp::ctrObtenerConfig();
    echo json_encode(array('ok' => true, 'config' => $cfg));
    exit;
}

if ($accion === 'guardarConfig') {
    $raw = isset($_POST['config']) ? $_POST['config'] : '';
    $data = json_decode($raw, true);

    if (!is_array($data)) {
        http_response_code(400);
        echo json_encode(array('ok' => false, 'error' => 'Config invalida'));
        exit;
    }

    $r = ControladorWhatsapp::ctrGuardarConfig($data);
    if ($r === 'ok') {
        echo json_encode(array('ok' => true));
        exit;
    }

    http_response_code(500);
    echo json_encode(array('ok' => false, 'error' => 'No se pudo guardar'));
    exit;
}

http_response_code(400);
echo json_encode(array('ok' => false, 'error' => 'Accion invalida'));
