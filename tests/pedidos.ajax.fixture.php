<?php
// Solo CLI con base temporal explícita; jamás usa las credenciales de la aplicación.
if (PHP_SAPI !== 'cli' || !getenv('EGS_PEDIDOS_TEST_DB')) exit(1);
require_once __DIR__ . '/../modelos/pedidos.persistencia.php';
$input = json_decode(file_get_contents('php://stdin'), true);
$db = new PDO('sqlite:' . getenv('EGS_PEDIDOS_TEST_DB'), null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$db->exec('CREATE TABLE IF NOT EXISTS pedidos (id INTEGER PRIMARY KEY AUTOINCREMENT, id_empresa INTEGER, id_cliente INTEGER, id_Asesor INTEGER, productos TEXT, pagos TEXT, observaciones TEXT, estado TEXT, total NUMERIC, adeudo NUMERIC, id_orden INTEGER, fechaDePedido TEXT, fechaEntrega TEXT)');
$db->exec('CREATE TABLE IF NOT EXISTS pedidos_guardados (clave TEXT PRIMARY KEY, huella TEXT, resultado TEXT)');
$db->exec('CREATE TABLE IF NOT EXISTS ordenes (id INTEGER PRIMARY KEY, id_empresa INTEGER, id_pedido INTEGER DEFAULT 0)');
$pool = new ReflectionProperty('Database', 'instances');
$pool->setAccessible(true);
$pool->setValue(null, ['ecommerce' => $db, 'wordpress' => $db]);
if (isset($input['snapshot'])) {
    echo json_encode($db->query('SELECT * FROM pedidos ORDER BY id')->fetchAll(PDO::FETCH_ASSOC));
    exit;
}
session_save_path(dirname(getenv('EGS_PEDIDOS_TEST_DB')));
session_id('pedidosregresion');
session_start();
$_SESSION = ($input['perfil'] ?? 'administrador') === 'sinSesion' ? [] : ['id' => 7, 'nombre' => 'Carlos', 'empresa' => 1, 'perfil' => $input['perfil'] ?? 'administrador'];
session_write_close();
$_POST = $input['post'];
register_shutdown_function(function () { fwrite(STDERR, '\nEGS_HTTP:' . (http_response_code() ?: 200)); });
chdir(__DIR__ . '/../ajax');
require 'pedidos.ajax.php';
