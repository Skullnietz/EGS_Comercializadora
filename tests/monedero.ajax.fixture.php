<?php
$entrada = json_decode(file_get_contents('php://stdin'), true);
session_save_path(getenv('EGS_MONEDERO_TEST_DIR'));
session_id('monederoregresion');
session_start();
$_SESSION = ($entrada['perfil'] ?? '') === 'sinSesion' ? [] : ['id'=>7,'empresa'=>1,'perfil'=>$entrada['perfil'] ?? 'administrador'];
$_POST = $entrada['post'];
register_shutdown_function(function(){ fwrite(STDERR, 'EGS_HTTP:' . (http_response_code() ?: 200)); });
chdir(__DIR__ . '/../ajax');
require 'recompensas.ajax.php';
