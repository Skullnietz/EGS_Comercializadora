<?php
// Sin conexión externa: modelos reales con el pool PDO inyectado y SQLite en memoria.
require_once __DIR__ . '/../modelos/pedidos.persistencia.php';
require_once __DIR__ . '/../modelos/pedidos.modelo.php';
$db = new PDO('sqlite::memory:', null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$pool = new ReflectionProperty('Database', 'instances');
$pool->setAccessible(true);
$pool->setValue(null, ['ecommerce' => $db, 'wordpress' => $db]);
$_SESSION = ['id' => 7, 'nombre' => 'Carlos', 'empresa' => 1, 'perfil' => 'administrador'];
$db->exec('CREATE TABLE pedidos (id INTEGER PRIMARY KEY AUTOINCREMENT, id_empresa INTEGER, id_cliente INTEGER, id_Asesor INTEGER, productos TEXT, pagos TEXT, observaciones TEXT, estado TEXT, total NUMERIC, adeudo NUMERIC, id_orden INTEGER, fechaDePedido TEXT, fechaEntrega TEXT, pagoPedido TEXT, abonoUno TEXT, fechaAbonoUno TEXT, abonoDos TEXT, fechaAbonoDos TEXT, abonoTres TEXT, fechaAbonoTres TEXT, abonoCuatro TEXT, fechaAbonoCuatro TEXT, abonoCinco TEXT, fechaAbonoCinco TEXT)');
$db->exec('CREATE TABLE pedidos_guardados (clave TEXT PRIMARY KEY, huella TEXT, resultado TEXT)');
$db->exec('CREATE TABLE ordenes (id INTEGER PRIMARY KEY, id_empresa INTEGER, id_pedido INTEGER DEFAULT 0)');
$db->exec('INSERT INTO ordenes (id,id_empresa) VALUES (101,1),(102,1),(103,2)');
$checks = 0;
function check($value, $message) { global $checks; if (!$value) throw new Exception($message); $checks++; }
function failure($fn, $message) { try { $fn(); } catch (Throwable $e) { check(true, $message); return $e; } throw new Exception($message); }
function row($id) { global $db; $s = $db->prepare('SELECT * FROM pedidos WHERE id = ?'); $s->execute([$id]); return $s->fetch(PDO::FETCH_ASSOC); }
function entry($extra = []) { return $extra + ['empresa' => 1, 'cliente' => 2, 'asesor' => 3, 'productos' => '[{"Descripcion":"Equipo 💻","cantidad":2,"precioUnitario":50.25}]', 'pago' => '[{"pago":20.50,"fecha":"2026-10-02"}]', 'estado' => 'Pedido Pendiente', 'id_orden' => 0]; }
function edits($extra = []) { return $extra + ['estado' => 'Pedido Adquirido', 'total' => '110.25', 'productos' => '[{"indice":0,"Descripcion":"Equipo actualizado 💻","cantidad":3,"precioUnitario":36.75}]', 'pagos' => '[{"pago":10.25,"fecha":"2026-10-02","ref":"abono00001"}]', 'observacion' => 'Revisado ✅']; }

$created = PedidosPersistencia::crearPedido(entry(), 'crear00001');
$id = $created['id'];
check((float)$created['total'] === 100.5 && (float)$created['adeudo'] === 80.0, 'Alta con centavos y adeudo calculado en servidor');
check(PedidosPersistencia::crearPedido(entry(), 'crear00001')['id'] === $id, 'Alta repetida retorna el mismo pedido');
check((int)$db->query('SELECT COUNT(*) FROM pedidos')->fetchColumn() === 1, 'No duplica el pedido si se pierde la respuesta');
$_SESSION['nombre'] = 'Carlos actualizado';
check(PedidosPersistencia::crearPedido(entry(), 'crear00001')['id'] === $id, 'Recibo identifica al usuario por ID aunque cambie su nombre');
$_SESSION['nombre'] = 'Carlos';
failure(function () { PedidosPersistencia::crearPedido(entry(['cliente' => 4]), 'crear00001'); }, 'No reutiliza el recibo con otra captura');
$version = PedidosPersistencia::version($created);
$saved = PedidosPersistencia::guardar($id, edits(), $version, 'guardar001');
check((float)$saved['adeudo'] === 79.5, 'Abonos preservados y adeudo actualizado');
check(count(json_decode($saved['observaciones'], true)) === 1, 'Observación guardada junto con los campos del pedido');
$db->exec("UPDATE pedidos SET estado = 'Producto en Almacen' WHERE id = $id");
$replay = PedidosPersistencia::guardar($id, edits(), $version, 'guardar001');
check($replay === $saved, 'Recupera el resultado aunque la versión inicial ya cambió');
check(row($id)['estado'] === 'Producto en Almacen' && count(json_decode(row($id)['pagos'], true)) === 2, 'Reintento no pisa cambios posteriores ni duplica abonos');
failure(function () use ($id, $version) { PedidosPersistencia::guardar($id, edits(), $version, 'guardar002'); }, 'Una edición nueva rechaza versión vieja');
failure(function () use ($id) { PedidosPersistencia::cambiarEstado($id . 'abc', 'Pedido Pendiente'); }, 'No recorta un ID inválido');
failure(function () use ($id) { PedidosPersistencia::guardar(999, edits(), PedidosPersistencia::version(row($id)), 'guardar003'); }, 'ID inexistente no devuelve éxito');
$obs = PedidosPersistencia::agregarObservacion($id, 'Observación 😃', 'observar01');
check(PedidosPersistencia::agregarObservacion($id, 'Observación 😃', 'observar01') === $obs, 'Observación repetida no se duplica');
failure(function () use ($id) { PedidosPersistencia::agregarObservacion($id, 'Otro texto', 'observar01'); }, 'No acepta otro texto con la misma referencia');
PedidosPersistencia::quitarObservacion($id, $obs, 'quitar0001');
PedidosPersistencia::quitarObservacion($id, $obs, 'quitar0001');
check(count(json_decode(row($id)['observaciones'], true)) === 1, 'Quitar una observación admite reintento');

// Simula MySQL sin modo estricto y triggers que cambian un campo después de escribirlo.
foreach (['estado' => "'cancelado'", 'total' => '1', 'adeudo' => '1', 'productos' => "'[]'", 'pagos' => "'[]'", 'observaciones' => "'[]'", 'fechaEntrega' => "'2000-01-01'"] as $field => $changed) {
    $before = row($id);
    $db->exec("CREATE TRIGGER alterar AFTER UPDATE ON pedidos BEGIN UPDATE pedidos SET $field = $changed WHERE id = NEW.id; END");
    failure(function () use ($id, $before) { PedidosPersistencia::guardar($id, edits(['estado' => 'Entregado/Pagado', 'pagos' => '[{"pago":7,"fecha":"2026-10-02","ref":"otroabono1"}]']), PedidosPersistencia::version($before), 'corrupt001'); }, 'Detecta modificación de ' . $field);
    check(row($id) === $before, 'Rollback completo al fallar ' . $field);
    $db->exec('DROP TRIGGER alterar');
}
foreach (['id_empresa' => '9', 'id_cliente' => '9', 'id_Asesor' => '9', 'id_orden' => '9', 'total' => '1', 'adeudo' => '1', 'estado' => "'cancelado'", 'productos' => "'[]'", 'pagos' => "'[]'", 'observaciones' => "'[1]'", 'fechaDePedido' => "'2000-01-01'", 'fechaEntrega' => "'2000-01-01'"] as $field => $changed) {
    $count = $db->query('SELECT COUNT(*) FROM pedidos')->fetchColumn();
    $db->exec("CREATE TRIGGER alterar AFTER INSERT ON pedidos BEGIN UPDATE pedidos SET $field = $changed WHERE id = NEW.id; END");
    failure(function () { PedidosPersistencia::crearPedido(entry(['estado' => 'Entregado/Pagado']), 'altacorrupt1'); }, 'Verifica alta: ' . $field);
    check($db->query('SELECT COUNT(*) FROM pedidos')->fetchColumn() === $count, 'Alta parcial revertida: ' . $field);
    $db->exec('DROP TRIGGER alterar');
}

PedidosPersistencia::asignar($id, 101);
check((int)row($id)['id_orden'] === 101 && (int)$db->query('SELECT id_pedido FROM ordenes WHERE id = 101')->fetchColumn() === (int)$id, 'Vínculo guardado en ambos extremos');
$before = row($id);
$db->exec('CREATE TRIGGER romper_orden AFTER UPDATE ON ordenes WHEN NEW.id = 102 BEGIN UPDATE ordenes SET id_pedido = 999 WHERE id = NEW.id; END');
failure(function () use ($id) { PedidosPersistencia::asignar($id, 102); }, 'Detecta fallo en el extremo de orden');
check(row($id) === $before && (int)$db->query('SELECT id_pedido FROM ordenes WHERE id = 101')->fetchColumn() === (int)$id, 'Rollback conserva vínculo anterior');
$db->exec('DROP TRIGGER romper_orden');
// Si no se confirma el recibo tampoco debe quedar un pedido cambiado.
$before = row($id);
$db->exec("CREATE TRIGGER romper_recibo AFTER UPDATE ON pedidos_guardados BEGIN UPDATE pedidos_guardados SET resultado = '{}' WHERE clave = NEW.clave; END");
failure(function () use ($id, $before) { PedidosPersistencia::guardar($id, edits(), PedidosPersistencia::version($before), 'recibofall1'); }, 'Detecta recibo incompleto');
check(row($id) === $before, 'Recibo y pedido se revierten juntos');
$db->exec('DROP TRIGGER romper_recibo');
$db->exec('ALTER TABLE pedidos_guardados RENAME TO recibos_temporal');
failure(function () use ($id, $before) { PedidosPersistencia::guardar($id, edits(), PedidosPersistencia::version($before), 'sinmigrac1'); }, 'Esquema sin migrar no escribe parcialmente');
check(row($id) === $before, 'Falta de migración conserva el pedido');
$db->exec('ALTER TABLE recibos_temporal RENAME TO pedidos_guardados');
failure(function () use ($id) { PedidosPersistencia::asignar($id, 103); }, 'Rechaza orden de otra empresa');
$count = $db->query('SELECT COUNT(*) FROM pedidos')->fetchColumn();
failure(function () { PedidosPersistencia::crearPedido(entry(['id_orden' => 101]), 'altavincul1'); }, 'No crea un pedido con orden ya ocupada');
check($db->query('SELECT COUNT(*) FROM pedidos')->fetchColumn() === $count, 'Alta y asignación se revierten juntas');
PedidosPersistencia::asignar($id, 102);
check((int)$db->query('SELECT id_pedido FROM ordenes WHERE id = 101')->fetchColumn() === 0, 'Reasignar limpia el vínculo anterior');
PedidosPersistencia::cambiarEstado($id, 'Entregado/Pagado');
check(row($id)['fechaEntrega'] !== null, 'Entrega registra fecha');
// Abonos históricos y JSON que no se editaron deben conservar su formato original.
$db->exec("UPDATE pedidos SET pagoPedido = '$10.00', abonoUno = '5.00', productos = '[{\"Descripcion\":\"Anterior\",\"cantidad\":\"1\",\"precio\":\"110.25\",\"extra\":\"conservar\"}]' WHERE id = $id");
$historical = row($id);
$savedHistorical = PedidosPersistencia::guardar($id, ['estado' => '', 'total' => '', 'productos' => '[]', 'pagos' => '[]'], PedidosPersistencia::version($historical), 'historico1');
check($savedHistorical['productos'] === $historical['productos'] && $savedHistorical['pagoPedido'] === '$10.00' && $savedHistorical['abonoUno'] === '5.00', 'No reescribe productos ni columnas de abonos históricos');
check((float)$savedHistorical['adeudo'] === 64.5, 'Adeudo incluye pago inicial y abono histórico');
failure(function () { PedidosPersistencia::crearPedido(entry(['productos' => '[]']), 'sinproducto'); }, 'Rechaza pedido sin productos');
failure(function () { PedidosPersistencia::crearPedido(entry(['productos' => '[{"Descripcion":"","cantidad":1,"precioUnitario":10}]']), 'sindescrip1'); }, 'No guarda un producto sin descripción');
failure(function () { PedidosPersistencia::crearPedido(entry(['pago' => '[{"pago":0,"fecha":"2026-10-02"}]']), 'pagovacio1'); }, 'No ignora una fecha capturada sin monto');
failure(function () { PedidosPersistencia::crearPedido(entry(['pago' => '[{"pago":1,"fecha":"2026-02-30"}]']), 'malafecha1'); }, 'Rechaza fecha inválida sin recortarla');
$_SESSION['empresa'] = 2;
failure(function () use ($id) { PedidosPersistencia::guardar($id, edits(), PedidosPersistencia::version(row($id)), 'sinpermiso'); }, 'No escribe otra empresa');
$_SESSION['empresa'] = 1;
$_SESSION['perfil'] = 'vendedor';
failure(function () use ($id) { PedidosPersistencia::guardar($id, edits(), PedidosPersistencia::version(row($id)), 'vendedor01'); }, 'Vendedor no modifica finanzas');
PedidosPersistencia::agregarObservacion($id, 'Observación de vendedor', 'vendedor02');
check(count(json_decode(row($id)['observaciones'], true)) === 2, 'Vendedor sí guarda observaciones');
$_SESSION['perfil'] = 'administrador';
failure(function () { ModeloPedidos::mdlEditarPedidoDinamico('pedidos', []); }, 'Escritura antigua no omite protecciones');
PedidosPersistencia::eliminar($id);
check(!row($id) && (int)$db->query('SELECT id_pedido FROM ordenes WHERE id = 102')->fetchColumn() === 0, 'Elimina y limpia el vínculo');
echo "OK: $checks comprobaciones de persistencia\n";
