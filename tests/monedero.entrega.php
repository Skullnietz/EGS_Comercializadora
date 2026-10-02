<?php
// Modelos y controlador reales. Sin acceso al servidor ni modificaciones a clientes reales.
date_default_timezone_set('America/Mexico_City');
require_once __DIR__ . '/../modelos/monedero.persistencia.php';
require_once __DIR__ . '/../modelos/ordenes.modelo.php';
require_once __DIR__ . '/../controladores/ordenes.controlador.php';
class ControladorNotificaciones { public static function ctrCrearTablaEstado() { throw new RuntimeException('Notificaciones externas deshabilitadas en pruebas'); } }
$db = new PDO('sqlite::memory:', null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$pool = new ReflectionProperty('Database', 'instances');
$pool->setAccessible(true);
$pool->setValue(null, ['ecommerce' => $db, 'wordpress' => $db]);
$db->exec('CREATE TABLE dinero_electronico (id INTEGER PRIMARY KEY AUTOINCREMENT, id_cliente INTEGER UNIQUE, saldo NUMERIC, token TEXT UNIQUE)');
$db->exec('CREATE TABLE dinero_electronico_movimientos (id INTEGER PRIMARY KEY AUTOINCREMENT, id_cliente INTEGER, id_orden INTEGER, tipo TEXT, monto NUMERIC, saldo_anterior NUMERIC, saldo_nuevo NUMERIC, descripcion TEXT, referencia_tipo TEXT, referencia_id INTEGER, id_empresa INTEGER, id_usuario_aplico INTEGER, origen_total_bruto NUMERIC, origen_total_neto NUMERIC, monto_aplicado NUMERIC, fecha TEXT DEFAULT CURRENT_TIMESTAMP, UNIQUE(referencia_tipo, referencia_id, tipo))');
$db->exec('CREATE TABLE compras (id INTEGER PRIMARY KEY, id_cliente INTEGER, pago NUMERIC, fecha TEXT)');
$cols = ['id INTEGER PRIMARY KEY', 'id_usuario INTEGER', 'id_empresa INTEGER', 'id_pedido INTEGER DEFAULT 0', 'estado TEXT', 'total NUMERIC', 'fecha_Salida TEXT', 'total_bruto_monedero NUMERIC', 'monto_monedero_aplicado NUMERIC DEFAULT 0', 'total_pagado_cliente NUMERIC', 'fecha_canje_monedero TEXT', 'id_tecnico INTEGER', 'id_tecnicoDos INTEGER', 'id_Asesor INTEGER', 'partidas TEXT', 'inversiones TEXT', 'totalInversion NUMERIC', 'titulo TEXT', 'descripcion TEXT', 'observaciones TEXT', 'ruta TEXT', 'multimedia TEXT', 'portada TEXT'];
$nombres = ['Uno','Dos','Tres','Cuatro','Cinco','Seis','Siete','Ocho','Nueve','Diez'];
foreach ($nombres as $nombre) { $cols[] = 'partida' . $nombre . ' TEXT'; $cols[] = 'precio' . $nombre . ' NUMERIC'; }
$db->exec('CREATE TABLE ordenes (' . implode(',', $cols) . ')');
$_SESSION = ['id' => 7, 'empresa' => 1, 'perfil' => 'administrador', 'tokenMonederoOrden' => 'prueba'];
$_POST = ['tokenMonederoOrden' => 'prueba'];
$checks = 0;
function check($condicion, $mensaje) { global $checks; if (!$condicion) throw new RuntimeException($mensaje); $checks++; }
function falla($fn, $mensaje) { try { $fn(); } catch (Throwable $e) { check(true, $mensaje); return; } throw new RuntimeException($mensaje); }
function fila($id) { global $db; $s=$db->prepare('SELECT * FROM ordenes WHERE id = ?'); $s->execute([$id]); return $s->fetch(PDO::FETCH_ASSOC); }
function cantidadCanjes() { global $db; return (int)$db->query('SELECT COUNT(*) FROM dinero_electronico_movimientos')->fetchColumn(); }
function orden($id, $cliente=2, $empresa=1) { global $db; $db->prepare("INSERT INTO ordenes(id,id_usuario,id_empresa,estado,total,titulo) VALUES(?,?,?,'Terminada (ter)',200.75,'Equipo')")->execute([$id,$cliente,$empresa]); }
function datos($id, $estado='Entregado (Ent)', $total='200.75') {
    global $nombres;
    $d=['id'=>$id,'asesor'=>3,'tecnico'=>4,'tecnicodos'=>0,'estado'=>$estado,'costoTotalDeOrden'=>$total,'listatOrdenes'=>'[]','listatOrdenesNuevas'=>'[]','listarinversiones'=>'[]','totalInversiones'=>'0'];
    foreach ($nombres as $nombre) { $d['partida'.$nombre]=''; $d['precio'.$nombre]='0'; }
    $d['precioUno']=$total; $d['partidaUno']='Servicio'; return $d;
}
function guardar($id, $monto='60.25', $estado='Entregado (Ent)', $total='200.75', $cliente=2) {
    $d=datos($id,$estado,$total);
    return MonederoPersistencia::guardarOrden($id,$estado,$total,$monto,function()use($d){ return ModeloOrdenes::mdlEditarOrdenDinamica('ordenes',$d); },$cliente);
}
// Saldo inicial de $100, generado por una orden real del periodo vigente.
$db->prepare("INSERT INTO ordenes(id,id_usuario,id_empresa,estado,total,fecha_Salida) VALUES(1,2,1,'Entregado (Ent)',10000,?)")->execute([date('Y-m-d')]);
orden(101);
$r=guardar(101);
$o=fila(101); $mov=$r['movimiento'];
check($o['estado']==='Entregado (Ent)' && !empty($o['fecha_Salida']), 'Entrega y fecha confirmadas');
check((float)$o['total']===200.75 && (float)$o['precioUno']===200.75, 'El modelo conserva centavos de total y partidas');
check((float)$o['total_bruto_monedero']===200.75 && (float)$o['monto_monedero_aplicado']===60.25 && (float)$o['total_pagado_cliente']===140.5, 'Desglose utilizado por el ticket');
check((float)$mov['monto']===-60.25 && (float)$mov['monto_aplicado']===60.25, 'Movimiento de consumo negativo y monto aplicado positivo');
check((int)$mov['id_cliente']===2 && (int)$mov['referencia_id']===101 && $mov['referencia_tipo']==='orden', 'Referencia al cliente y orden correctos');
check((int)$mov['id_usuario_aplico']===7 && (int)$mov['id_empresa']===1, 'Autorización atribuida a administrador y empresa');
check((float)$mov['saldo_anterior']===100.0 && (float)$mov['saldo_nuevo']===39.75, 'Saldo del movimiento antes y después de consumir');
check(abs(ModeloRecompensas::mdlCalcularSaldoDinamico(2,1,1)-41.16)<0.001, 'Saldo incorpora recompensa sobre el importe efectivamente pagado');
$hist=ModeloRecompensas::mdlObtenerOrdenesConRecompensa(2,1,1);
check((float)$hist[0]['total_orden']===140.5 && (float)$hist[0]['recompensa']===1.41, 'Historial público usa la misma base que el ticket');
$antes=fila(101);
check(guardar(101)['reintento']===true && cantidadCanjes()===1 && fila(101)===$antes, 'Reenvío de entrega confirma lo mismo sin descontar ni actualizar otra vez');
falla(function(){guardar(101,'60.24');},'Rechaza reintento con otro monto');
falla(function(){guardar(101,'0','Terminada (ter)');},'No reabre una entrega que conserva un consumo');
falla(function(){guardar(101,'0','Entregado (Ent)','201');},'No altera total después de consumir');
guardar(101,'0');
check(cantidadCanjes()===1 && (float)fila(101)['monto_monedero_aplicado']===60.25,'Edición posterior sin solicitud conserva el consumo');
orden(102);
$inicial=fila(102);
foreach (['-1','abc','1.001','1e2','NaN','',[], '100000000'] as $valor) falla(function()use($valor){guardar(102,$valor);},'Rechaza importe mal formado');
falla(function(){guardar(102,'201');},'No excede total bruto');
falla(function(){guardar(102,'50');},'Saldo insuficiente bloquea entrega');
falla(function(){guardar(102,'10','Terminada (ter)');},'No consume antes de entregar');
falla(function(){guardar(102,'0',' entregado (Ent)');},'Un estado adulterado no omite validación de entrega');
falla(function(){guardar(102,'10','Entregado (Ent)','200.75',3);},'No descuenta a otro cliente mediante campo oculto');
$_POST['tokenMonederoOrden']='alterado';
falla(function(){guardar(102,'10');},'Rechaza autorización sin token de sesión');
$_POST['tokenMonederoOrden']='prueba';
foreach (['vendedor','tecnico','secretaria'] as $perfil) {
    $_SESSION['perfil']=$perfil;
    falla(function(){guardar(102,'10');},'Solo administrador puede consumir');
    falla(function(){guardar(102,'0');},'Solo administrador puede entregar');
}
$_SESSION['perfil']='administrador'; $_SESSION['empresa']=2;
falla(function(){guardar(102,'10');},'No entrega órdenes de otra empresa');
$_SESSION['empresa']=1;
check(fila(102)===$inicial && cantidadCanjes()===1,'Todas las validaciones dejan la orden y el saldo intactos');
// Detecta escrituras incompletas incluso cuando execute() devuelve éxito.
foreach (['estado'=>"'Terminada (ter)'",'total'=>'1','monto_monedero_aplicado'=>'0','total_pagado_cliente'=>'1','fecha_Salida'=>"NULL"] as $campo=>$valor) {
    $db->exec("CREATE TRIGGER alterar AFTER UPDATE ON ordenes WHEN NEW.id=102 BEGIN UPDATE ordenes SET $campo=$valor WHERE id=NEW.id; END");
    falla(function(){guardar(102,'10');},'Detecta dato de entrega recortado');
    check(fila(102)===$inicial && cantidadCanjes()===1,'Revierte orden y movimiento juntos');
    $db->exec('DROP TRIGGER alterar');
}
$db->exec('CREATE TRIGGER alterar AFTER INSERT ON dinero_electronico_movimientos BEGIN UPDATE dinero_electronico_movimientos SET saldo_nuevo=999 WHERE id=NEW.id; END');
falla(function(){guardar(102,'10');},'Comprueba saldo escrito en movimiento');
check(fila(102)===$inicial && cantidadCanjes()===1,'Movimiento dañado revierte toda la entrega');
$db->exec('DROP TRIGGER alterar');
falla(function(){MonederoPersistencia::guardarOrden(102,'Entregado (Ent)','200.75','10',function(){return 'error';},2);},'Error del modelo bloquea canje');
check(fila(102)===$inicial && cantidadCanjes()===1,'Error del modelo no deja consumo huérfano');
$db->exec('ALTER TABLE dinero_electronico_movimientos RENAME COLUMN id_usuario_aplico TO usuario_temporal');
falla(function(){guardar(102,'10');},'Esquema sin migración no confirma entrega');
check(fila(102)===$inicial && cantidadCanjes()===1,'Migración incompleta no deja entrega parcial');
$db->exec('ALTER TABLE dinero_electronico_movimientos RENAME COLUMN usuario_temporal TO id_usuario_aplico');
falla(function(){ControladorRecompensas::ctrCanjearEnOrden(2,102,10);},'Entrada antigua no consume fuera de entrega');
orden(103,3);
guardar(103,'0','Entregado (Ent)','200.75',3);
check((float)fila(103)['total_pagado_cliente']===200.75 && cantidadCanjes()===1,'Entrega sin petición de monedero no consume');
falla(function(){guardar(103,'1','Entregado (Ent)','200.75',3);},'No agrega consumo después de entregar');
// Controlador que usa realmente infoOrden.php: mismo nombre de campo que el formulario.
orden(104);
$_POST=datos(104)+['idOrden'=>104,'asesorEditadoEnOrdenDianmica'=>3,'tecnicoEditadoEnOrdenDianmica'=>4,'idClienteOrden'=>2,'montoCanjeMonederoOrden'=>'10.05','tokenMonederoOrden'=>'prueba','observaciones'=>'Entrega revisada','listarObservaciones'=>'[]'];
function formularioCompleto() {
    ob_start(); $c=new controladorOrdenes(); $c->ctrEditarObservacionesYaExistentes(); $c->ctrEditarOrdenDinamica(); $c->ctrEditarInversiones(); return ob_get_clean();
}
$salida=formularioCompleto();
check(strpos($salida,'guardado correctamente')!==false && (float)fila(104)['monto_monedero_aplicado']===10.05,'Flujo de entrega activo lee y aplica el campo real del formulario');
check(cantidadCanjes()===2 && (float)fila(104)['total_pagado_cliente']===190.7,'Flujo activo persiste descuento que imprime el ticket');
check(fila(104)['descripcion']==='Entrega revisada' && substr_count($salida,'guardado correctamente')===1,'Formulario completo guarda observaciones e inversiones sin un segundo éxito');
orden(105);
$_POST['idOrden']=105; $_POST['montoCanjeMonederoOrden']='100';
$salida=formularioCompleto();
check(strpos($salida,'No se guardó la entrega')!==false && strpos($salida,'guardado correctamente')===false,'Controlador muestra error real sin mensaje de éxito');
check(fila(105)['estado']==='Terminada (ter)' && cantidadCanjes()===2,'Controlador no entrega ni descuenta si no hay saldo');
check(fila(105)['descripcion']===null && fila(105)['inversiones']===null,'Los otros controladores de la página no escriben tras fallar la entrega');
ModeloOrdenes::mdlEditarInversiones('ordenes',['id'=>105,'estado'=>'Entregado (Ent)','listarinversiones'=>'[]','totalInversiones'=>'0']);
check(fila(105)['estado']==='Terminada (ter)','Guardado independiente de inversiones no omite autorización de entrega');
$_POST['tokenMonederoOrden']='prueba';
$venta=ControladorRecompensas::ctrCanjearEnVenta(2,901,'1.25',1,7,'20.75','19.50');
check($venta['monto_canjeado']===1.25 && $venta['saldo_anterior']>0,'Ventas también guardan auditoría del consumo bajo el bloqueo común');
check(ControladorRecompensas::ctrCanjearEnVenta(2,901,'1.25',1,7,'20.75','19.50')===$venta && cantidadCanjes()===3,'Reintento de venta no gasta otra vez el saldo');
// El modelo del editor antiguo también participa en el guardado seguro y conserva centavos.
$db->prepare("INSERT INTO ordenes(id,id_usuario,id_empresa,estado,total,fecha_Salida) VALUES(10,4,1,'Entregado (Ent)',10000,?)")->execute([date('Y-m-d')]);
orden(106,4);
$legacy=['id'=>106,'idCliente'=>4,'idTecnico'=>4,'idAsesor'=>3,'estado'=>'Entregado (Ent)','rutaOrden'=>'equipo','descripcionOrden'=>'Servicio','titulo'=>'Equipo','multimedia'=>'[]','imgFotoPrincipal'=>'','seleccionarPedido'=>0,'totalOrdenEditar'=>'200.75'];
for($i=1;$i<=10;$i++){ $legacy['partida'.$i]='Servicio'; $legacy['precio'.$i]=$i===1?'200.75':'0'; }
MonederoPersistencia::guardarOrden(106,'Entregado (Ent)','200.75','25.25',function()use($legacy){ return ModeloOrdenes::mdlEditarOrden('ordenes',$legacy); },4);
check((float)fila(106)['precioUno']===200.75 && (float)fila(106)['total']===200.75,'Editor antiguo conserva precios y total decimal');
check((float)fila(106)['monto_monedero_aplicado']===25.25 && (float)fila(106)['total_pagado_cliente']===175.5 && cantidadCanjes()===4,'Editor antiguo confirma entrega, descuento y consumo juntos');
echo "OK: $checks comprobaciones de entrega y monedero\n";
